<?php

require_once("config.php");

use Guzzle\Http\Client;
use Guzzle\Http\EntityBody;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;

/** Instancia a classe de Runs **/
$runClass = new \Ramos\Zeus\Run();
/** Instancia a classe de Blocks **/
$blockClass = new \Ramos\Zeus\Block();
/** Instancia a classe de Alerts **/
$alertsClass = new \Ramos\Zeus\Alert();
/** Instancia a classe de Jobs **/
$jobClass = new \Ramos\Zeus\Job();

try{
	
	$logAdapter = new Guzzle\Log\MonologLogAdapter($logger);
	$logPlugin = new Guzzle\Plugin\Log\LogPlugin($logAdapter, Guzzle\Log\MessageFormatter::DEBUG_FORMAT);
	
	$startingScript = time();
		
	$jobID = (PHP_SAPI === 'cli')?$argv[1]:$_GET['job_id'];
	
	if(empty($jobID) OR !is_numeric($jobID)){
		$logger->addError(__("Please, fill job ID parameter with a numeric string"));
		throw new \Exception(__("Please, fill job ID parameter with a numeric string"));
	}
	
	$job = $connectDB->prepare("SELECT `j`.* FROM `jobs` AS `j` WHERE `j`.`job_id`=:jobID AND `j`.`job_type`='external' AND `j`.`is_running`=0");
	$job->bindValue(":jobID", $jobID, PDO::PARAM_INT);
	
	$job->execute();

	if($job->rowCount()==0){
		define('CURRENT_JOB', $jobID);
		$logger->addWarning(sprintf(__("Job %d cannot be called at this time."), $jobID));
		throw new \Exception(sprintf(__("Job %d cannot be called at this time."), $jobID));
	}
	
	$JobProperties = $job->fetch(PDO::FETCH_ASSOC);
	
	if($JobProperties['job_status']==false){
		define('CURRENT_JOB', $jobID);
		$logger->addError(sprintf(__("Job %s (%d) has been disabled!"), $jobName, $jobID));
		throw new \Exception(sprintf(__("Job %s (%d) has been disabled!"), $jobName, $jobID));
	}
	
	$jobName = $JobProperties['job_name'];
	$jobID = (int)$JobProperties['job_id'];
	define('CURRENT_JOB', $jobID);
	$jobPath = $JobProperties['job_path'];
	
	$viewAlertsByJob = $alertsClass->viewAlertsByJob($jobID);
	
	$logger->addInfo(sprintf("\r\n############################################\r\nRunning job ID %d\r\n############################################", $jobID));
	
	echo sprintf("############################################<br />Running job ID %d<br />############################################", $jobID);
	
	//Atualizar o is_running e o last_run
	$jobClass->updateJobAttr($jobID, 'is_running', true);
	$jobClass->updateJobAttr($jobID, 'last_run', date("Y-m-d H:i:s", time()));
	
	if($jobClass->checkIfJobIsBlocked($jobID)){
		$logger->addInfo(sprintf(__("Job %s (%d) blocked by another job!"), $jobName, $jobID));
		throw new \Exception(sprintf(__("Job %s (%d) blocked by another job!"), $jobName, $jobID));
	}
	
	$clientExternal = new Client($jobPath, array('curl.options'=>array('CURLOPT_RETURNTRANSFER'=>TRUE)));

	#$clientExternal->addSubscriber($logPlugin);
	
	/** @var $request Request */
	$requestExternal = $clientExternal->get();

	/** @var $response Response */
	$responseExternal = $requestExternal->send();

	/** @var $body EntityBody */
	$bodyExternal = $responseExternal->getBody(true);
	
	/** @var $responseInfo O retorno HTTP da requisição **/
	$responseExternalInfo = $responseExternal->getInfo();
	
	if($responseExternalInfo['http_code'] != 200){
		
		$message = sprintf(__("Job %s (%d) has a invalid url!"), $jobName, $jobID);
		
		$populateArray = array('startingScript'=> $startingScript, 'endingScript' => time(), 'http_code' => $responseExternalInfo['http_code'], 'total_time' => $responseExternalInfo['total_time'], 'redirect_count' => $responseExternalInfo['redirect_count'], 'content_type' => $responseExternalInfo['content_type'], 'others' => NULL, 'responseContent' => 'NULL', 'comments' => $message, 'run_problems' => true);
		
		$logger->addError($message);
		
		foreach($viewAlertsByJob AS $singleAlert){
			
			if($singleAlert['block_other_jobs']==true){				
				$blockClass->addBlock($jobID, $singleAlert['alert_id']);
			}
			
		}

		$runClass->addRunning($jobID, $populateArray);
		
		throw new \Exception($message);
	}
	
	$JobResponse = json_decode($bodyExternal, true);

	$logger->addInfo(sprintf(__("Job %s (%d) response: %s"), $jobName, $jobID, var_export($JobResponse, true)));
	
	foreach($viewAlertsByJob AS $singleAlert){
		
		$queryWhen = transformAlertWhen($singleAlert['alert_when']);
		$queryComparison = transformAlertComparison($singleAlert['alert_comparison']);
		$queryReturn = $singleAlert['alert_return'];
		
		if(!array_key_exists($queryWhen, $JobResponse)){
			$logger->addWarning(sprintf(__("Key \"%s\" not found in response JSON Object of Job %s (%d). Try again later."), $queryWhen, $jobName, $jobID));
			throw new \Exception(sprintf(__("Key \"%s\" not found in response JSON Object of Job %s (%d). Try again later."), $queryWhen, $jobName, $jobID));
		}
		
	}
	
	$arrayRequerida = array('http_code','total_time','redirect_count','content_type','content');

	if(count(array_intersect_key(array_flip($arrayRequerida), $JobResponse)) < count($arrayRequerida)) {
		$logger->addWarning(sprintf(__("The response JSON Object is less than the expected in the Job %s (%d)."), $jobName, $jobID));
	}	

	$endingScript = time();
	
	$http_code = (!empty($JobResponse['http_code']))?$JobResponse['http_code']:0;
	$total_time = (!empty($JobResponse['total_time']))?$JobResponse['total_time']:0;
	$redirect_count = (!empty($JobResponse['redirect_count']))?$JobResponse['redirect_count']:0;
	$content_type = (!empty($JobResponse['content_type']))?$JobResponse['content_type']:0;
	$others = (!empty($JobResponse['others']))?$JobResponse['others']:NULL;
	$responseContent = (!empty($JobResponse['content']))?$JobResponse['content']:0;
	$comments = (!empty($JobResponse['comments']))?$JobResponse['comments']:NULL;
	
	$comparisonFailed = false;
	$run_problems = false;
	
	$logger->addWarning(array('when'=>$queryWhen,'target'=>$queryReturn,'comparison'=>$queryComparison));
	
	#echo "<br />";print_r(array('key_from_response'=>$JobResponse[$queryWhen],'when'=>$queryWhen,'target'=>$queryReturn,'comparison'=>$queryComparison));echo "<br />";var_export(applyComparison($JobResponse[$queryWhen],$queryReturn,$queryComparison), true);echo "<br />";
	
	if(applyComparison($JobResponse[$queryWhen],$queryReturn,$queryComparison) === false){
		
		foreach($viewAlertsByJob AS $singleAlert){
			
			if($singleAlert['alert_type']=="email"){
				
				$alert_message = json_decode($singleAlert['alert_message'], true);
			
				switch (json_last_error()) {
					case JSON_ERROR_NONE:
						$emailsArray = explode(",", $alert_message['emails']);
						foreach($emailsArray AS $emailString){
							$sendEmail = sendEmail($emailString, sprintf("Job \"%s\" (ID: %d) with problems | Zeus Monitor", $jobName, $jobID), $alert_message['message']);
						}
					break;
					default:
						$logger->addWarning(sprintf(__("Unknown error when we tried to decode the JSON Alert Message from Job %s (%d)."), $jobName, $jobID));
					break;
				}
				
			}
			
			if($singleAlert['block_other_jobs']==true){				
				$blockClass->addBlock($jobID, $singleAlert['alert_id']);
			}
			
		}
		
		$logger->addError(sprintf(__("Job %s (%d) has suffered problems in latest run! See logs for more info."), $jobName, $jobID));
		$comments = sprintf(__("Job %s (%d) has suffered problems in latest run."), $jobName, $jobID);
		$comparisonFailed = true;
		$run_problems = true;
	}
	
	//Inserir um último Running para esse script
	$populateArray = array('startingScript'=> $startingScript, 'endingScript' => $endingScript, 'http_code' => $http_code, 'total_time' => $total_time, 'redirect_count' => $redirect_count, 'content_type' => $content_type, 'others' => $others, 'responseContent' => $responseContent, 'comments' => $comments, 'run_problems' => $run_problems);
	
	$runClass->addRunning($jobID, $populateArray);
	
	//POG
	if($comparisonFailed){
		throw new \Exception($comments);
	}
	
	//Atualizar blocks para esse Job
	$blockClass->updateBlock($jobID);
	//Atualizar o is_running e o last_run
	$jobClass->updateJobAttr($jobID, 'is_running', false);
	$jobClass->updateJobAttr($jobID, 'last_run', date("Y-m-d H:i:s", time()));
	
	$logger->addInfo(sprintf("\r\n############################################\r\nSuccessful job running - Job %s (%d) \r\n############################################", $jobName, $jobID));
	echo sprintf("<br />############################################<br />Successful job running - Job %s (%d) <br />############################################", $jobName, $jobID);
	
}catch(Exception $e){
	//Atualizar o is_running e o last_run
	$jobClass->updateJobAttr((int)CURRENT_JOB, 'is_running', false);
	$jobClass->updateJobAttr((int)CURRENT_JOB, 'last_run', date("Y-m-d H:i:s", time()));
	$logger->addError(json_encode(array('line'=>$e->getLine(), 'file'=>$e->getFile(), 'message'=>$e->getMessage())));
	echo $e->getMessage();
}
	
?>