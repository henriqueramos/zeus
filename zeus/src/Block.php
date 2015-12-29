<?php

namespace Ramos\Zeus;

/**
 * @author Henrique Ramos <hramos@live.de>
 * @version 0.1.0
 * @since 0.2.0
 */
class Block{
	
	/**
	 * Função addBlock
	 *
	 * Adiciona um block no job
	 *
	 * @param string $jobID ID do Job.
	 * @param string $alertID ID do Alert.
	 *
	 * @throws Exception Senão conseguir inserir o block
	 *
	 * @return true
	 */
	public function addBlock($jobID=false, $alertID=false){
		
		global $connectDB;
		
		if(empty($jobID) OR !is_numeric($jobID)){
			throw new \Exception(__("Fill the \$jobID parameter."));
		}
		
		if(empty($alertID) OR !is_numeric($alertID)){
			throw new \Exception(__("Fill the \$alertID parameter."));
		}
		
		$insertBlock = $connectDB->prepare("INSERT INTO `blocks`(`block_id`, `job_id`, `alert_id`, `block_status`, `block_date`) VALUES (NULL,:job_id,:alert_id,1,NOW())");
		$insertBlock->bindValue(":job_id", (int)$jobID, \PDO::PARAM_INT);
		$insertBlock->bindValue(":alert_id", (int)$alertID, \PDO::PARAM_INT);
		
		$insertBlock->execute();
		
		if($insertBlock->errorCode()){
			throw new \Exception(__("Problems when we tried to add a block to this job."));
		}
		
		return true;
		
	}
	
	/**
	 * Função updateBlock
	 *
	 * Atualiza um block de job
	 *
	 * @param string $jobID ID do Job.
	 *
	 * @throws Exception Senão conseguir atualizar o block
	 *
	 * @return true
	 */
	public function updateBlock($jobID=false){
		
		global $connectDB;
		
		if(empty($jobID) OR !is_numeric($jobID)){
			throw new \Exception(__("Fill the \$jobID parameter."));
		}
		
		$updateBlocking = $connectDB->prepare("UPDATE `blocks` AS `b` SET `b`.`block_status`=0 WHERE `b`.`job_id`=:job_id AND `b`.`block_status`=1");
		$updateBlocking->bindValue(":job_id", $jobID, \PDO::PARAM_INT);
		
		$updateBlocking->execute();
		
		if($updateBlocking->errorCode() != 00000){
			throw new \Exception(__("Problems when we tried to add a block to this job."));
		}
		
		return true;
		
	}
}

?>