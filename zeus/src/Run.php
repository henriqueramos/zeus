<?php

namespace Ramos\Zeus;

use Ramos\Zeus\Job;

/**
 * @author Henrique Ramos <hramos@live.de>
 * @version 0.1.0
 * @since 0.2.0
 */
class Run{
	
	/**
	 * Função latestJobRun
	 *
	 * Visualiza a última interação do Job
	 *
	 * @author Henrique Ramos <hramos@live.de>
	 * @version 0.2.0
	 * @since 0.2.0
	 *
	 * @param string $jobID ID do Job.
	 *
	 * @throws Exception Senão encontrar nenhum resultado no banco de dados
	 *
	 * @return array[]
	 */
	public function latestJobRun($jobID=NULL){
	
		global $connectDB;
				
		try{
			
			if(empty($jobID) OR !is_numeric($jobID)){
				throw new \Exception(__("Fill the \$jobID parameter."));
			}
			
			$jobsSQL = $connectDB->prepare("SELECT `r`.`run_id`, `r`.`job_id`, `r`.`run_no`, `r`.`run_date`, `r`.`run_start`, `r`.`run_end`, `r`.`run_http_code`, `r`.`run_total_time`, `r`.`run_redirect_count`, `r`.`run_content_type`, `r`.`run_others`, `r`.`run_response`, `r`.`run_comments`, `r`.`run_problems`, `b`.`block_status`, `b`.`block_date` FROM `runs` AS `r` LEFT JOIN `blocks` AS `b` ON `b`.`block_id`=(SELECT `b1`.`block_id` FROM `blocks` AS `b1` WHERE `b1`.`job_id`=`r`.`job_id` ORDER BY `b1`.`block_date` DESC LIMIT 1) WHERE `r`.`job_id`=:job_id ORDER BY `r`.`run_date` DESC LIMIT 1");
			$jobsSQL->bindValue(":job_id", (int)$jobID, \PDO::PARAM_INT);
			$jobsSQL->execute();
			
			if($jobsSQL->rowCount()){
				return $jobsSQL->fetch(\PDO::FETCH_ASSOC);
			}

			return false;
		
		}catch(\Exception $e){
			throw $e;
		}
	}
	
	/**
	 * Função deleteAllRuns
	 *
	 * Remove todas as interações de Job do banco de dados 
	 *
	 * @author Henrique Ramos <hramos@live.de>
	 * @version 0.2.0
	 * @since 0.2.0
	 *
	 * @param string $jobID ID do Job.
	 *
	 * @throws Exception Senão encontrar nenhum resultado no banco de dados
	 *
	 * @return true|false
	 */
	public function deleteAllRuns($jobID=false){
		
		global $connectDB;
				
		try{
			
			if(empty($jobID) OR !is_numeric($jobID)){
				throw new \Exception(__("Fill the \$jobID parameter."));
			}
			
			$jobsSQL = $connectDB->prepare("DELETE FROM `runs` WHERE `job_id`=:job_id");
			$jobsSQL->bindValue(":job_id", (int)$jobID, \PDO::PARAM_INT);
			
			$jobsSQL->execute();
			
			if($jobsSQL->errorCode() != 00000){
				throw new \Exception(__("Problems when we tried to remove all interactions of this job."));
			}
			
			return true;
		
		}catch(\Exception $e){
			throw $e;
		}
		
	}

	
	/**
	 * Função addRunning
	 *
	 * Adiciona uma interação no job
	 *
	 * @param string $jobID ID do Job.
	 * @param array[] $params Parâmetros para popular o running
	 *
	 * @throws Exception Senão conseguir inserir a interação
	 *
	 * @return true|false
	 */
	public function addRunning($jobID=false, $params=false){
		
		global $connectDB, $logger;

		if(empty($jobID) OR !is_numeric($jobID)){
			throw new \Exception(__("Fill the \$jobID parameter."));
		}
		
		if(empty($params) OR !is_array($params)){
			throw new \Exception(__("Fill the \$params parameter with a valid array."));
		}
		
		extract($params, EXTR_SKIP);
	
		$insertRunning = $connectDB->prepare("INSERT INTO `runs`(`run_id`, `job_id`, `run_no`, `run_date`, `run_start`, `run_end`, `run_http_code`, `run_total_time`, `run_redirect_count`, `run_content_type`, `run_others`, `run_response`, `run_comments`, `run_problems`) VALUES (NULL,:job_id,:run_no, NOW(),FROM_UNIXTIME(:job_start),FROM_UNIXTIME(:job_end),:run_http_code,:run_total_time,:run_redirect_count,:run_content_type,:run_others,:run_response,:run_comments, :run_problems)");
		$insertRunning->bindValue(":job_id", $jobID, \PDO::PARAM_INT);
		$insertRunning->bindValue(":run_no", $this->calcRunNo($jobID, 1), \PDO::PARAM_INT);
		$insertRunning->bindValue(":job_start", $startingScript, \PDO::PARAM_STR);
		$insertRunning->bindValue(":job_end", $endingScript, \PDO::PARAM_STR);
		$insertRunning->bindValue(":run_http_code", $http_code, \PDO::PARAM_STR);
		$insertRunning->bindValue(":run_total_time", $total_time, \PDO::PARAM_STR);
		$insertRunning->bindValue(":run_redirect_count", $redirect_count, \PDO::PARAM_STR);
		$insertRunning->bindValue(":run_content_type", $content_type, \PDO::PARAM_STR);
		$insertRunning->bindValue(":run_others", $others, \PDO::PARAM_STR);
		$insertRunning->bindValue(":run_response", $responseContent, \PDO::PARAM_STR);
		$insertRunning->bindValue(":run_comments", $comments, \PDO::PARAM_STR);
		$insertRunning->bindValue(":run_problems", $run_problems, \PDO::PARAM_STR);
		$insertRunning->execute();
		
		if($insertRunning->errorCode() != 00000){
			$logger->addInfo(sprintf("\r\n############################################\r\nProblems when we tried to add an interaction to Job (%d)\r\n%s\r\n############################################", $jobID, var_export($insertRunning->errorInfo(), true)));
			throw new \Exception(__("Problems when we tried to add an interaction to this job."));
		}
		
		return true;
		
	}
	
	/**
	 * Função calcRunNo
	 *
	 * Calcula o número de interações de um determinado Job, permitindo somar valores a quantidade de interações.
	 *
	 * @param string $jobID ID do Job.
	 * @param array[] $params Parâmetros para popular o running
	 *
	 * @throws Exception Senão conseguir inserir a interação
	 *
	 * @return true|false
	 */
	public function calcRunNo($jobID=false, $addRun=false){
		
		global $connectDB, $logger;

		if(empty($jobID) OR !is_numeric($jobID)){
			throw new \Exception(__("Fill the \$jobID parameter."));
		}
		
		if(!empty($addRun) AND !is_numeric($addRun)){
			throw new \Exception(__("Fill the \$addRun parameter with a valid number."));
		}
		
		$calcRunNo = $connectDB->prepare("SELECT count(`r`.`run_id`) AS `run_no` FROM `runs` AS `r` WHERE `r`.`job_id`=:job_id");
		$calcRunNo->bindValue(":job_id", $jobID, \PDO::PARAM_INT);
		$calcRunNo->execute();
		
		if($calcRunNo->errorCode() != 00000){
			$logger->addInfo(sprintf("\r\n############################################\r\nProblems when we tried to calculate the runs no of the Job (%d)\r\n%s\r\n############################################", $jobID, var_export($calcRunNo->errorInfo(), true)));
			throw new \Exception(sprintf(__("Problems when we tried to calculate the runs no of the Job (%d)"), $jobID));
		}
		
		return (!empty($addRun))?($calcRunNo->fetchColumn()+$addRun):$calcRunNo->fetchColumn();
		
	}
	
	/**
	 * Função deleteAllRunnings
	 *
	 * Remove todas as interações de Job do banco de dados 
	 *
	 * @param string $jobID ID do Job.
	 *
	 * @throws Exception Senão encontrar nenhum resultado no banco de dados
	 *
	 * @return true|false
	 */
	public function deleteAllRunnings($jobID=false){
		
		global $connectDB, $logger;
				
		try{
			
			if(empty($jobID) OR !is_numeric($jobID)){
				throw new Exception(__("Fill the \$jobID parameter."));
			}
			
			$jobsSQL = $connectDB->prepare("DELETE FROM `runs` WHERE `job_id`=:job_id");
			$jobsSQL->bindValue(":job_id", (int)$jobID, \PDO::PARAM_INT);
			
			$jobsSQL->execute();
			
			if($jobsSQL->errorCode() != 00000){
				$logger->addInfo(sprintf("\r\n############################################\r\nProblems when we tried to calculate the runs no of the Job (%d)\r\n%s\r\n############################################", $jobID, var_export($jobsSQL->errorInfo(), true)));
				throw new \Exception(sprintf(__("Problems when we tried to remove all interactions of this job (%d)."), $jobID));
			}
			
			return true;
		
		}catch(\Exception $e){
			throw $e;
		}
		
	}
	
}

?>