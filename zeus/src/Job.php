<?php

namespace Ramos\Zeus;

use Ramos\Zeus\Run;
use Ramos\Zeus\Alert;
use Ramos\Zeus\Groups;

/**
 * @author Henrique Ramos <hramos@live.de>
 * @version 0.2.0
 */
class Job{
	
	/**
	 * Função createUpdateJob
	 *
	 * Creates a new job in the database, if the parameter \$jobID is filled, the Job's content will be updated.
	 *
	 * @author Henrique Ramos <hramos@live.de>
	 * @version 0.2.0
	 * @since 0.1.0
	 *
	 * @param array[] $params F3 parameters array.
	 *
	 * @throws Exception If _POST parameters job_name, job_cron, job_type, job_path aren't filled or are invalid
	 * @throws Exception If can't add the Job in the database
	 *
	 * @return array[]
	 */
	public function createUpdateJob($params){
		
		global $connectDB, $logger;
		
		try{
			
			$jobID = (!empty($params['jobID']) AND is_numeric($params['jobID']))?$params['jobID']:NULL;
			$job_author = 1; //Melhorar isso
			
			if(empty($_POST['job_name'])){
				throw new \Exception(__("You need a valid name for this job."));
			}
			
			if(empty($_POST['job_cron'])){
				throw new \Exception(__("We need the Cron filled."));
			}
			
			if(!\Cron\CronExpression::isValidExpression($_POST['job_cron'])){
				throw new \Exception(__("We need a valid Cron syntax."));
			}
			
			if(empty($_POST['job_type']) OR !in_array($_POST['job_type'], array('internal', 'external'))){
				throw new \Exception(__("We need the job type filled.  Choose between \"internal\" and \"external\"."));
			}
			
			if(empty($_POST['job_path'])){
				throw new \Exception(__("We need the job path filled."));
			}
			
			if(empty($_POST['job_status']) OR !in_array($_POST['job_status'], array('active', 'disable'))){
				throw new \Exception(__("We need the job type filled.  Choose between \"active\" and \"disable\"."));
			}
			
			$alertArray = $_POST['alerts'];
			
			if(!empty($alertArray) AND !is_array($alertArray)){
				throw new \Exception(__("Fill the \"alerts\" parameter correctly."));
			}
			
			$job_comment = (!empty($_POST['job_comment']))?$_POST['job_comment']:NULL;
			$job_status = ($_POST['job_status']=='active')?1:0;
			$job_cron = $_POST['job_cron'];
			$job_group = 1; //Groups::checkIfGroupExists($_POST['job_group'])?$_POST['job_group']:NULL;
			
			$timestamp = time();
			
			$connectDB->beginTransaction();
			
			$insertJobDatabase = $connectDB->prepare("INSERT INTO `jobs`(`job_id`, `job_name`, `job_date`, `job_cron`, `job_path`, `job_type`, `job_author`, `job_status`, `job_comment`, `job_group`, `is_running`) VALUES (:job_id,:job_name, FROM_UNIXTIME(:job_date),:job_cron,:job_path,:job_type,:job_author,:job_status,:job_comment,:job_group,0) ON DUPLICATE KEY UPDATE `job_name`=:job_name, `job_cron`=:job_cron, `job_path`=:job_path, `job_type`=:job_type, `job_status`=:job_status, `job_comment`=:job_comment, `job_group`=:job_group");
			
			$insertJobDatabase->bindValue(":job_id", $jobID, \PDO::PARAM_STR);
			$insertJobDatabase->bindValue(":job_name", $_POST['job_name'], \PDO::PARAM_STR);
			$insertJobDatabase->bindValue(":job_date", $timestamp, \PDO::PARAM_INT);
			$insertJobDatabase->bindValue(":job_cron", $job_cron, \PDO::PARAM_STR);
			$insertJobDatabase->bindValue(":job_path", $_POST['job_path'], \PDO::PARAM_STR);
			$insertJobDatabase->bindValue(":job_type", $_POST['job_type'], \PDO::PARAM_STR);
			$insertJobDatabase->bindValue(":job_author", $job_author, \PDO::PARAM_INT);
			$insertJobDatabase->bindValue(":job_status", $job_status, \PDO::PARAM_STR);
			$insertJobDatabase->bindValue(":job_comment", $job_comment, \PDO::PARAM_STR);
			$insertJobDatabase->bindValue(":job_group", $job_group, \PDO::PARAM_STR);

			if(!$insertJobDatabase->execute()){
				$logger->addError(sprintf(__("MySQL Error %s"), var_Export($insertJobDatabase->errorInfo(), true)));
				throw new \Exception(sprintf(__("Problems with database insertion. Try again later. MySQL Error %s"), $insertJobDatabase->errorCode()));
			}
			
			$JobIDInfo = $connectDB->lastInsertId();
			
			foreach($alertArray AS $alertIndividual){
				$alert = new Alert();
				$alert->addUpdateAlert($JobIDInfo, $alertIndividual);
			}
			
			$logger->addError(sprintf(__("About Job: {id: \"%s\" name: \"%s\", timestamp: \"%s\", path: \"%s\"}"), $JobIDInfo, $_POST['job_name'],$timestamp, $_POST['job_path']));
			
			//Inserir no crontab
			switch($_POST['job_type']){
				case 'external':
					$logger->addInfo(sprintf(__("External Job %s"), ExternalCrawlerPath));
					$pathToCallingJob = ExternalCrawlerPath;
				break;
				case 'internal': default:
					$logger->addInfo(sprintf(__("Internal Job %s"), InternalCrawlerPath));
					$pathToCallingJob = InternalCrawlerPath;
				break;
			}
			
			$logger->addInfo(sprintf(__("Job Info %s"), var_export($JobInfo, true)));
			$logger->addInfo(sprintf(__("CRON Expression %s"), $job_cron));
			$logger->addInfo(sprintf(__("CRON Log %s"), Zeus_Monitor_Log_Path));
			$logger->addInfo(sprintf(__("Path to call job %s"), $pathToCallingJob));
			
			$cronParser = \Cron\CronExpression::factory($job_cron);			
			
			$cronMinute = (!empty($cronParser->getExpression(1)))?$cronParser->getExpression(1):"*";
			$cronHour = (!empty($cronParser->getExpression(2)))?$cronParser->getExpression(2):"*";
			$cronDayMonth = (!empty($cronParser->getExpression(3)))?$cronParser->getExpression(3):"*";
			$cronMonth = (!empty($cronParser->getExpression(4)))?$cronParser->getExpression(4):"*";
			$cronDayWeek = (!empty($cronParser->getExpression(5)))?$cronParser->getExpression(5):"*";

			$addToCron = new \MyBuilder\Cronos\Formatter\Cron;

			$addToCron
					->comment(sprintf("Job %s", addslashes(htmlspecialchars($_POST['job_name']))))
					->job("php {$pathToCallingJob} {$JobIDInfo}")
							->setMinute($cronMinute)
							->setHour($cronHour)
							->setDayOfMonth($cronDayMonth)
							->setMonth($cronMonth)
							->setDayOfWeek($cronDayWeek)
							->setStandardOutFile('log')
							->appendStandardErrorToFile(Zeus_Monitor_Log_Path)
					->end();
			
			$connectDB->commit();
			
			if(is_null($jobID)){
				return json_encode(array('success'=>sprintf(__("Job inserted with success.<br />Please, insert this code inside your cron:<br /><code>%s</code>"), nl2br($addToCron->format()))));
			}
			
			return json_encode(array('success'=>sprintf(__("Job updated with success.<br />Please, insert this code inside your cron:<br /><code>%s</code>"), nl2br($addToCron->format()))));
			
		}catch(Exception $e){
			$connectDB->rollBack();
			throw $e;
		}
		
	}
	
	/**
	 * Função atualizarAtributoJob / updateJobAttr
	 *
	 * Atualiza um determinado atributo do job
	 *
	 * @author Henrique Ramos <hramos@live.de>
	 * @version 0.2.0
	 * @since 0.2.0
	 *
	 * @param string $jobID ID do job
	 * @param string $attr Atributo
	 * @param string $value Valor
	 *
	 * @throws Exception Senão encontrar nenhum resultado no banco de dados
	 *
	 * @return true|false
	 */
	public function updateJobAttr($jobID=false, $attr=NULL, $value=NULL){
		global $connectDB;

		if(empty($jobID) OR !is_numeric($jobID)){
			throw new \Exception(__("Fill the \$jobID parameter correctly."));
		}
		
		$fieldsName = array('job_name', 'job_date', 'job_cron', 'job_path', 'job_type', 'job_author', 'job_status', 'job_comment', 'is_running', 'last_run', 'job_group');
		
		if(is_null($attr)){
			throw new \Exception(__("Fill the \$attr parameter correctly."));
		}
		
		if(!in_array($attr, $fieldsName)){
			throw new \Exception(__("Fill the \$attr parameter with a valid value."));
		}
		
		if(is_null($value)){
			throw new \Exception(__("Fill the \$value parameter correctly."));
		}
		
		$jobsSQL = $connectDB->prepare("UPDATE `jobs` SET {$attr}=:value WHERE `job_id`=:job_id");
		$jobsSQL->bindValue(":job_id", (int)$jobID, \PDO::PARAM_INT);
		$jobsSQL->bindValue(":value", $value, \PDO::PARAM_STR);
		
		if(!$jobsSQL->execute()){
			$logger->addError(sprintf("#############################\r\nError MySQL updateJobAttr %s\r\n#############################\r\nError#############################\r\n%s\r\n#############################\r\n\r\n", $jobID,var_export($jobsSQL->errorInfo(), true)));
			throw new \Exception(__("Problems occurred when we tried to update this job."));
		}
		
		return true;
		
	}
	
	/**
	 * Função checkIfJobExists
	 *
	 * Verifica se um job existe com base em um ID.
	 *
	 * @author Henrique Ramos <hramos@live.de>
	 * @version 0.1.0
	 * @since 0.1.0
	 *
	 * @param int $jobID ID do job.
	 *
	 * @throws Exception Senão for passado nenhum \$jobID.
	 *
	 * @return false|true
	 */
	public static function checkIfJobExists($jobID=null){
		global $connectDB;
		
		if(empty($jobID) OR !is_numeric($jobID)){
			throw new \Exception(__("Did you forget the jobID parameter? We really need this guy."));
		}
		
		$checkJob = $connectDB->prepare("SELECT COUNT(`j`.`job_id`) FROM `jobs` AS `j` WHERE `j`.`job_id`=:jobID");
		$checkJob->bindValue(":jobID", $jobID, \PDO::PARAM_INT);
		$checkJob->execute();
		return $checkJob->fetchColumn()?true:false;
			
	}
	
	/**
	 * Função visualizarJobs / showJobs
	 *
	 * Visualiza todos os jobs do banco de dados, se for passado um parâmetro jobID, retornará as informações desse job
	 *
	 * @author Henrique Ramos <hramos@live.de>
	 * @version 0.1.0
	 * @since 0.1.0
	 *
	 * @param array[] $params Array com parâmetros passados através do F3.
	 *
	 * @throws Exception Senão encontrar nenhum resultado no banco de dados
	 *
	 * @return array[]
	 */
	public function showJobs($params=NULL){
		
		global $connectDB, $logger;
				
		try{
			
			if(empty($params)){
				throw new \Exception(__("Fill the \$params parameter."));
			}
			
			if((empty($params['jobID']) OR !is_numeric($params['jobID']))){
				$jobsSQL = $connectDB->query("SELECT `j`.`job_id`, `j`.`job_name`, `j`.`job_date`, `j`.`job_cron`, `j`.`job_path`, `j`.`job_type`, `j`.`job_author`, `j`.`job_status`, `j`.`job_comment` FROM `jobs` AS `j`", \PDO::FETCH_ASSOC);
				if($jobsSQL->rowCount() == 0){
					if($jobsSQL->errorCode() != 00000){
						$logger->addError(sprintf(__("MySQL Error when we tried to retrieve jobs\r\n#############################\r\nError#############################\r\n%s\r\n#############################\r\n\r\n"), var_export($jobsSQL->errorInfo(), true)));
					}
					throw new \Exception(__("Not found any job matching this parameters."));
				}
				return (array('jobs'=>$jobsSQL->fetchAll()));
			}
			
			$jobID = $params['jobID'];
			
			$jobsSQL = $connectDB->prepare("SELECT `j`.`job_id`, `j`.`job_name`, `j`.`job_date`, `j`.`job_cron`, `j`.`job_path`, `j`.`job_type`, `j`.`job_author`, `j`.`job_status`, `j`.`job_comment` FROM `jobs` AS `j` WHERE `j`.`job_id`=:jobID");
			$jobsSQL->bindValue(":jobID", $jobID, \PDO::PARAM_INT);
			$jobsSQL->execute();
			
			if($jobsSQL->errorCode() != 00000){
				$logger->addError(sprintf(__("MySQL Error when we tried to retrieve job %s\r\n#############################\r\nError#############################\r\n%s\r\n#############################\r\n\r\n"), $jobID, var_export($jobsSQL->errorInfo(), true)));
				throw new \Exception(__("Not found any job matching this parameters."));
			}
			
			
			$finalArray = array('job'=>$jobsSQL->fetch(\PDO::FETCH_ASSOC));
			
			$alert = new \Ramos\Zeus\Alert();
			$finalArray = array_merge($finalArray['job'], array('alerts'=>$alert->viewAlertsByJob($jobID)));
			
			return $finalArray;
		
		}catch(Exception $e){
			throw $e;
		}
	}
	
	/**
	 * Função visualizarInteracao / showRuns
	 *
	 * Visualiza todos as interações do job no banco de dados, se for passado um parâmetro runID, retornará as informações dessa interacao
	 *
	 * @author Henrique Ramos <hramos@live.de>
	 * @version 0.1.0
	 * @since 0.1.0
	 *
	 * @param array[] $params Array com parâmetros passados através do F3.
	 *
	 * @throws Exception Senão encontrar nenhum resultado no banco de dados
	 *
	 * @return array[]
	 */
	public function showRuns($params=NULL){
		
		global $connectDB, $logger;
				
		try{
			
			if(empty($params)){
				throw new \Exception(__("Fill the \$params parameter."));
			}
			
			if(empty($params['jobID'])){
				throw new \Exception(__("Fill the \$jobID parameter."));
			}
			
			if(!empty($params['runID']) AND !is_numeric($params['runID'])){
				throw new \Exception(__("Fill the \$runID parameter."));
			}
			
			$jobID = $params['jobID'];
			
			if(!empty($jobID) AND is_numeric($params['jobID']) AND empty($params['runID'])){
				
				$jobsSQL = $connectDB->prepare("SELECT `r`.`run_id`, `r`.`job_id`, `r`.`run_no`, `r`.`run_date`, `r`.`run_start`, `r`.`run_end`, `r`.`run_http_code`, `r`.`run_total_time`, `r`.`run_redirect_count`, `r`.`run_content_type`, `r`.`run_others`, `r`.`run_response`, `r`.`run_comments`, `r`.`run_problems`, `b`.`block_status`, `b`.`block_date` FROM `runs` AS `r` LEFT JOIN `blocks` AS `b` ON `b`.`job_id`=`r`.`job_id` WHERE `r`.`job_id`=:job_id GROUP BY `r`.`run_id` ORDER BY `r`.`run_date` DESC");
				$jobsSQL->bindValue(":job_id", $jobID, \PDO::PARAM_INT);
				$jobsSQL->execute();
				
				if($jobsSQL->errorCode() != 00000){
					throw new \Exception(__("Not found any job matching this parameters."));
				}
				return array_merge(self::showJobs(array('jobID'=>$jobID)), array('runs'=>$jobsSQL->fetchAll(\PDO::FETCH_ASSOC)));
				
			}
			
			$runID = $params['runID'];
			
			$jobsSQL = $connectDB->prepare("SELECT `r`.`run_id`, `r`.`job_id`, `r`.`run_no`, `r`.`run_date`, `r`.`run_start`, `r`.`run_end`, `r`.`run_http_code`, `r`.`run_total_time`, `r`.`run_redirect_count`, `r`.`run_content_type`, `r`.`run_others`, `r`.`run_response`, `r`.`run_comments`, `r`.`run_problems`, `b`.`block_status`, `b`.`block_date` FROM `runs` AS `r` LEFT JOIN `blocks` AS `b` ON `b`.`block_id`=(SELECT `b1`.`block_id` FROM `blocks` AS `b1` WHERE `b1`.`job_id`=`r`.`job_id` ORDER BY `b1`.`block_date` DESC LIMIT 1) WHERE `r`.`job_id`=:job_id AND `r`.`run_no`=:run_no");
			$jobsSQL->bindValue(":job_id", $jobID, \PDO::PARAM_INT);
			$jobsSQL->bindValue(":run_no", $runID, \PDO::PARAM_INT);
			$jobsSQL->execute();
			
			if($jobsSQL->errorCode() != 00000){
				$logger->addInfo(sprintf("\r\n############################################\r\nProblems when we tried to select an interaction of the Job (%d)\r\n%s\r\n############################################", $jobID, var_export($jobsSQL->errorInfo(), true)));
				throw new \Exception(__("Not found any interaction matching this parameters."));
			}
			
			return array_merge(self::showJobs(array('jobID'=>$jobID)), array('run'=>$jobsSQL->fetch(\PDO::FETCH_ASSOC)));
		
		}catch(Exception $e){
			throw $e;
		}
	}
	
	/**
	 * Função removeJobController
	 *
	 * Remove um determinado Job do banco de dados 
	 *
	 * @author Henrique Ramos <hramos@live.de>
	 * @version 0.1.0
	 * @since 0.2.0
	 *
	 * @param array[] $params Array com parâmetros passados através do F3.
	 *
	 * @throws Exception Senão encontrar nenhum resultado no banco de dados
	 *
	 * @return array[]
	 */
	public function removeJobController($params){
		
		try{
			
			if(empty($params)){
				throw new \Exception(__("Fill the \$params parameter."));
			}
			
			if(empty($params['jobID']) OR !is_numeric($params['jobID'])){
				throw new \Exception(__("Fill the \$jobID parameter."));
			}
			
			if(!self::checkIfJobExists($params['jobID'])){
				throw new \Exception(__("Select a valid job!"));
			}
			
			$jobID = $params['jobID'];
			
			if(!empty($_POST['accepted']) AND $_POST['accepted']=="on"){
				
				$run = new \Ramos\Zeus\Run();
				
				$run->deleteAllRunnings($jobID);
				$this->deleteJob($jobID);
				
				throw new \Exception(__("The job and all of their interactions has been removed."));
			}
			
			return self::showJobs($params);
			
		}catch(Exception $e){
			throw $e;
		}
		
	}
	
	/**
	 * Função deleteJob
	 *
	 * Remove um determinado Job do banco de dados 
	 *
	 * @author Henrique Ramos <hramos@live.de>
	 * @version 0.1.0
	 * @since 0.2.0
	 *
	 * @param string $jobID ID do Job.
	 *
	 * @throws Exception Senão encontrar nenhum resultado no banco de dados
	 *
	 * @return true|false
	 */
	public function deleteJob($jobID=false){
		
		global $connectDB;
				
		try{
			
			if(empty($jobID) OR !is_numeric($jobID)){
				throw new \Exception(__("Fill the \$jobID parameter."));
			}
			
			$jobsSQL = $connectDB->prepare("DELETE FROM `jobs` WHERE `job_id`=:job_id");
			$jobsSQL->bindValue(":job_id", (int)$jobID, \PDO::PARAM_INT);
			
			$jobsSQL->execute();
			
			if($jobsSQL->errorCode() != 00000){
				throw new \Exception(__("Problems when we tried to remove this job."));
			}
			
			return true;
		
		}catch(Exception $e){
			throw $e;
		}
	}
	
	/**
	 * Função checkIfJobIsBlocked
	 *
	 * Checa se um job foi bloqueado por outro job
	 *
	 * @param string $jobID ID do Job.
	 *
	 * @throws Exception Senão conseguir checar no banco de dados
	 *
	 * @return true|false
	 */
	 
	public function checkIfJobIsBlocked($jobID=false){
		
		global $connectDB, $logger;
		
		try{
			
			if(empty($jobID) OR !is_numeric($jobID)){
				throw new \Exception(__("Fill the \$jobID parameter."));
			}
			$checkBlock = $connectDB->prepare("SELECT `b`.*, `a`.`block_except` FROM `blocks` AS `b` INNER JOIN `alerts` AS `a` ON `b`.`job_id`=`a`.`job_id` WHERE `b`.`block_status`=TRUE AND `a`.`job_id`!=:jobID AND :jobID NOT IN (`a`.`block_except`)");
			$checkBlock->bindValue(":jobID", $jobID, \PDO::PARAM_INT);
			$checkBlock->execute();
			if($checkBlock->errorCode() != 00000){
				$logger->addError(sprintf(__("MySQL Error when we tried to check if job has a block\r\n#############################\r\nError#############################\r\n%s\r\n#############################\r\n\r\n"), var_export($checkBlock->errorInfo(), true)));
			}
			return $checkBlock->rowCount()>=1?true:false;
			
		}catch(Exception $e){
			throw $e;
		}
	
	}

	
}

?>