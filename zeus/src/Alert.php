<?php

namespace Ramos\Zeus;

use Ramos\Zeus\Job;

class Alert{
	
	/** @var string $alertID Alert's ID **/
	private $alertID = false;
	
	public function __construct($alertID=false){
		if(!empty($alertID) AND is_numeric($alertID)){
			$this->alertID = $alertID;
		}
	}
	
	/**
	 * checkIfAlertExists
	 *
	 * @author Henrique Ramos <hramos@live.de>
	 * @version 0.2.0
	 * @since 0.2.0
	 *
	 * @param int $alertID Alert's ID.
	 *
	 * @throws Exception If \$alertID isn't filled or are invalid.
	 *
	 * @return false|true
	 */
	public static function checkIfAlertExists($alertID=false){
		global $connectDB;
		
		if(empty($alertID) OR !is_numeric($alertID)){
			throw new \Exception(__("Fill \$alertID parameter correctly."));
		}
		
		$checkAlert = $connectDB->prepare("SELECT COUNT(`a`.`alert_id`) FROM `alerts` AS `a` WHERE `a`.`alert_id`=:alertID");
		$checkAlert->bindValue(":alertID", $alertID, \PDO::PARAM_INT);
		$checkAlert->execute();
		return $checkAlert->fetchColumn()?true:false;
		
	}
	
	/**
	 * addUpdateAlert
	 *
	 * Append/Update an alert to/from a job.
	 *
	 * @author Henrique Ramos <hramos@live.de>
	 * @version 0.2.0
	 * @since 0.2.0
	 *
	 * @param string $jobID Job's ID
	 * @param array[] $params Parameters array to populate the alert.
	 *
	 * @throws Exception If \$jobID and/or \$params aren't filled or are invalid.
	 * @throws Exception If the Job doesn't exists.
	 *
	 * @return true
	 */
	public function addUpdateAlert($jobID=false, $params=false){
		
		global $connectDB, $logger;
		
		if(empty($jobID) OR !is_numeric($jobID)){
			$logger->addError(sprintf(__("Parameter \$jobID equal %s"), $jobID));
			throw new \Exception(__("Fill the \$jobID parameter correctly."));
		}
		
		if(Job::checkIfJobExists($jobID) == false){
			throw new \Exception(__("This job doesn't exists."));
		}
		
		self::validateAlertParams($params);
		
		$alertID = (!empty($params['id']) AND is_numeric($params['id']))?$params['id']:NULL;
		
		$logger->addInfo(sprintf("#############################\r\nJob Info %s Alert ID from Params %s - Alert ID %s\r\n#############################\r\n", $jobID,$params['id'], $alertID));
		
		$status = ($params['status']=='active')?1:0;
		$block_other_jobs = ($params['block_other_jobs']=='active')?1:0;
		$block_except = (!empty($params['block_except']))?$params['block_except']:NULL;
		
		switch($params['type']){
			case 'email':
				$message = json_encode(array('message'=>$params['message'], 'emails'=>cleanUpEmails($params['email'])));
			break;
			case 'sound':
				$message = json_encode(array('sound'=>$params['sound']));
			break;
			case 'blink':
				$message = json_encode(array('blink'=>'active'));
			break;
			default: case 'popup':
				$message = json_encode(array('message'=>$params['message']));
			break;
		}
		
		$addAlert = $connectDB->prepare("INSERT INTO `alerts`(`alert_id`, `job_id`, `alert_status`, `alert_when`, `alert_comparison`, `alert_return`, `alert_type`, `alert_message`, `block_other_jobs`, `block_except`) VALUES (:alert_id, :job_id,:alert_status,:alert_when,:alert_comparison,:alert_return,:alert_type,:alert_message,:block_other_jobs,:block_except) ON DUPLICATE KEY UPDATE `alert_status`=:alert_status,`alert_when`=:alert_when,`alert_comparison`=:alert_comparison,`alert_return`=:alert_return,`alert_type`=:alert_type,`alert_message`=:alert_message,`block_other_jobs`=:block_other_jobs,`block_except`=:block_except");
		
		$addAlert->bindValue(":alert_id", $alertID, \PDO::PARAM_STR);
		$addAlert->bindValue(":job_id",$jobID,\PDO::PARAM_INT);
		$addAlert->bindValue(":alert_status",$status,\PDO::PARAM_STR);
		$addAlert->bindValue(":alert_when",$params['when'],\PDO::PARAM_STR);
		$addAlert->bindValue(":alert_comparison",$params['comparison'],\PDO::PARAM_STR);
		$addAlert->bindValue(":alert_return",$params['return'],\PDO::PARAM_STR);
		$addAlert->bindValue(":alert_type",$params['type'],\PDO::PARAM_STR);
		$addAlert->bindValue(":alert_message",$message,\PDO::PARAM_STR);
		$addAlert->bindValue(":block_other_jobs",block_other_jobs,\PDO::PARAM_STR);
		$addAlert->bindValue(":block_except",$block_except,\PDO::PARAM_STR);
		
		$addAlert->execute();
		
		if($addAlert->errorCode() != 00000){
			$logger->addError(sprintf("#############################\r\nError MySQL addAlert to Job %s\r\n#############################\r\nError#############################\r\n%s\r\n#############################\r\n\r\n", $jobID,var_export($addAlert->errorInfo(), true)));
			if(is_null($alertID)){
				throw new \Exception(__("Problems occurred when we tried to add an alert to this job. See the logs for more information."));
			}
			throw new \Exception(__("Problems occurred when we tried to update this alert. See the logs for more information."));
		}
		
		return true;
		
	}
	
	
	/**
	 * viewAlert
	 *
	 * View the content from an alert.
	 *
	 * @author Henrique Ramos <hramos@live.de>
	 * @version 0.2.0
	 * @since 0.2.0
	 *
	 * @param string $alertID Alert's ID
	 *
	 * @throws Exception If \$alertID isn't filled or is invalid.
	 * @throws Exception If the Alert doesn't exists.
	 *
	 * @return array[] Return an array containing the values of an alert.
	 **/
	public function viewAlert($alertID=false){
		global $connectDB, $logger;
	
		if(empty($alertID) OR !is_numeric($alertID)){
			throw new \Exception(__("Fill the \$alertID parameter correctly."));
		}
		
		if(self::checkIfAlertExists($alertID) == false){
			throw new \Exception(__("This alert doesn't exists."));
		}
				
		$viewAlert = $connectDB->prepare("SELECT `job_id`, `alert_status`, `alert_when`, `alert_comparison`, `alert_return`, `alert_type`, `alert_message`, `block_other_jobs`, `block_except` FROM `alerts` WHERE WHERE `alert_id`=:alertID");
		
		$viewAlert->bindValue(":alertID",$alertID,\PDO::PARAM_INT);
		
		$viewAlert->execute();
		
		if($viewAlert->errorCode() != 00000){
			$logger->addError(sprintf("#############################\r\nError MySQL viewAlert %s\r\n#############################\r\nError#############################\r\n%s\r\n#############################\r\n\r\n", $alertID,var_export($viewAlert->errorInfo(), true)));
			throw new \Exception(__("Problems occurred when we tried to retrieve the content of this alert. See the logs for more information."));
		}
		
		return $viewAlert->fetch(\PDO::FETCH_ASSOC);
	}
	
	/**
	 * viewAlertsByJob
	 *
	 * View the alerts from a job.
	 *
	 * @author Henrique Ramos <hramos@live.de>
	 * @version 0.2.0
	 * @since 0.2.0
	 *
	 * @param string $jobID Job's ID
	 *
	 * @throws Exception If \$jobID isn't filled or is invalid.
	 * @throws Exception If the Job doesn't exists.
	 *
	 * @return array[] Return an array containing all the alerts from a job.
	 **/
	public function viewAlertsByJob($jobID=false){
		global $connectDB, $logger;
	
		if(empty($jobID) OR !is_numeric($jobID)){
			throw new \Exception(__("Fill the \$jobID parameter correctly."));
		}
		
		if(Job::checkIfJobExists($jobID) == false){
			throw new \Exception(__("This job doesn't exists."));
		}
				
		$viewAlert = $connectDB->prepare("SELECT `alert_id`, `job_id`, `alert_status`, `alert_when`, `alert_comparison`, `alert_return`, `alert_type`, `alert_message`, `block_other_jobs`, `block_except` FROM `alerts` WHERE `job_id`=:jobID");
		
		$viewAlert->bindValue(":jobID",$jobID,\PDO::PARAM_INT);
		
		$viewAlert->execute();
		
		if($viewAlert->errorCode() != 00000){
			$logger->addError(sprintf("#############################\r\nError MySQL viewAlertsByJob %s\r\n#############################\r\nError#############################\r\n%s\r\n#############################\r\n\r\n", $alertID,var_export($viewAlert->errorInfo(), true)));
			throw new \Exception(__("Problems occurred when we tried to retrieve the alerts of this job. See the logs for more information."));
		}
		
		return $viewAlert->fetchAll(\PDO::FETCH_ASSOC);
	}
	
	/**
	 * deleteAlert
	 *
	 * Delete an alert.
	 *
	 * @author Henrique Ramos <hramos@live.de>
	 * @version 0.2.0
	 * @since 0.2.0
	 *
	 * @param string $alertID Alert's ID
	 *
	 * @throws Exception If \$alertID isn't filled or is invalid.
	 * @throws Exception If the Alert doesn't exists.
	 *
	 * @return true
	 */
	public function deleteAlert($alertID=false){
		
		global $connectDB, $logger;
		
		if(empty($alertID) OR !is_numeric($alertID)){
			throw new \Exception(__("Fill the \$alertID parameter correctly."));
		}
				
		if(self::checkIfAlertExists($alertID) == false){
			throw new \Exception(__("This alert doesn't exists."));
		}
		
		$deleteAlert = $connectDB->prepare("DELETE FROM `alerts` WHERE `alert_id`=:alert_id");
		
		$deleteAlert->bindValue(":alert_id",$alertID,\PDO::PARAM_INT);
		
		$deleteAlert->execute();
		
		if($deleteAlert->errorCode() != 00000){
			$logger->addError(sprintf("#############################\r\nError MySQL Delete Alert %s\r\n#############################\r\nError#############################\r\n%s\r\n#############################\r\n\r\n", $alertID,var_export($deleteAlert->errorInfo(), true)));
			throw new \Exception(__("Problems occurred when we tried to remove an alert of this job. See the logs for more information."));
		}
		
		return true;
		
	}
	
	/**
	 * validateAlertParams
	 *
	 * Validates the params of an alert.
	 *
	 * @author Henrique Ramos <hramos@live.de>
	 * @version 0.2.0
	 * @since 0.2.0
	 *
	 * @param array[] $params Alert parameters in array format
	 *
	 * @throws Exception If \$params isn't filled or is invalid.
	 *
	 * @return true
	 */
	public static function validateAlertParams($params=null){
		
		if(empty($params) OR !is_array($params)){
			throw new \Exception(__("Fill the \$params parameter."));
		}
		
		$requiredParams = array('status','when','comparison','return','type', 'message', 'block_other_jobs');

		if(count(array_intersect_key(array_flip($requiredParams), $params)) < count($requiredParams)) {
			throw new \Exception(__("Fill the \$params parameter correctly. With all keys and values shown in the document."));
		}
		
		if(empty($params['status']) OR !in_array($params['status'], array('active', 'disable'))){
			throw new \Exception(__("Fill the \"status\" parameter correctly."));
		}
		
		if(empty($params['when']) OR !in_array($params['when'], array('http_code','total_time','redirect_count','content_type','content','others'))){
			throw new \Exception(__("Fill the \"when\" parameter correctly."));
		}
		
		if(empty($params['comparison']) OR !in_array($params['comparison'], array('equal','not_equal','less_than','greater_than','less_than_or_equal_to','greater_than_or_equal_to'))){
			throw new \Exception(__("Fill the \"comparison\" parameter correctly. Choose between \"equal\", \"not_equal\", \"less_than\", \"greater_than\", \"less_than_or_equal_to\" or \"greater_than_or_equal_to\"."));
		}
		
		if(empty($params['type']) OR !in_array($params['type'], array('popup', 'email', 'sound', 'blink'))){
			throw new \Exception(__("Fill the \"type\" parameter correctly. Choose between \"popup\", \"email\", \"sound\" or \"blink\"."));
		}
		
		if((empty($params['block_other_jobs']) OR !in_array($params['block_other_jobs'], array('active', 'disable'))) OR (!empty($params['block_other_jobs']) AND !in_array($params['block_other_jobs'], array('active', 'disable')))){
			throw new \Exception(__("Fill the \"block_other_jobs\" parameter correctly. Choose between \"internal\" and \"external\"."));
		}
		
		if($params['status'] == 'active' AND empty($params['return'])){
			throw new \Exception(__("Fill the \"return\" parameter correctly."));
		}
		
		if($params['status'] == 'active' AND empty($params['message'])){
			throw new \Exception(__("Fill the \"message\" parameter correctly."));
		}
		
		if($params['status'] == 'active' AND $params['type'] == 'popup' AND empty($params['message'])){
			throw new \Exception(__("Fill the \"message\" parameter correctly for popup alert."));
		}
		
		if($params['status'] == 'active' AND $params['type'] == 'email' AND empty($params['message']) AND empty($params['email'])){
			throw new \Exception(__("Fill the \"message\" parameter correctly for email sending."));
		}
		
	}
	
}

?>