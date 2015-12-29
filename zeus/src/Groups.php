<?php

namespace Ramos\Zeus;

class Groups{
	
	/** @var string $groupID Group's ID **/
	private $groupID = false;
	
	public function __construct($groupID=false){
		if(!empty($groupID) AND is_numeric($groupID)){
			$this->groupID = $groupID;
		}
	}
	
	/**
	 * checkIfGroupExists
	 *
	 * @author Henrique Ramos <hramos@live.de>
	 * @version 0.2.0
	 * @since 0.2.0
	 *
	 * @param int $groupID Group's ID.
	 *
	 * @throws Exception If \$groupID isn't filled or are invalid.
	 *
	 * @return false|true
	 */
	public static function checkIfGroupExists($groupID=false){
		global $connectDB;
		
		if(empty($groupID) OR !is_numeric($groupID)){
			throw new \Exception(__("Fill \$groupID parameter correctly."));
		}
		
		$checkGroup = $connectDB->prepare("SELECT COUNT(`g`.`group_id`) FROM `groups` AS `g` WHERE `g`.`group_id`=:groupID");
		$checkGroup->bindValue(":groupID", $groupID, \PDO::PARAM_INT);
		$checkGroup->execute();
		return $checkGroup->fetchColumn()?true:false;
		
	}
	
	/**
	 * addGroup
	 *
	 * Add a group to database.
	 *
	 * @author Henrique Ramos <hramos@live.de>
	 * @version 0.2.0
	 * @since 0.2.0
	 *
	 * @param array[] $params Parameters array to populate the group.
	 *
	 * @throws Exception If \$params isn't filled or is invalid.
	 *
	 * @return true
	 */
	public function addGroup($params=false){
		
		global $connectDB, $logger;
		
		if(empty($params) OR !is_array($params)){
			throw new \Exception(__("Fill the \$params parameter correctly."));
		}
		
		self::validateGroupParams($params);
		
		$addGroup = $connectDB->prepare("INSERT INTO `groups`(`group_id`, `group_name`, `group_color`, `group_icon`, `group_date`) VALUES (NULL,:group_name,:group_color,:group_icon,NOW())");
		
		$addGroup->bindValue(":group_name",$params['name'],\PDO::PARAM_STR);
		$addGroup->bindValue(":group_color",$params['color'],\PDO::PARAM_STR);
		$addGroup->bindValue(":group_icon",(empty($params['icon'])?NULL:$params['icon']),\PDO::PARAM_STR);
		
		if(!$addGroup->execute()){
			$logger->addError(sprintf("#############################\r\nError MySQL addGroup\r\n#############################\r\nError#############################\r\n%s\r\n#############################\r\n\r\n",var_export($addGroup->errorInfo(), true)));
			throw new \Exception(__("Problems occurred when we tried to add a group. See the logs for more information."));
		}
		
		return true;
		
	}
	
	/**
	 * updateGroup
	 *
	 * Update a group.
	 *
	 * @author Henrique Ramos <hramos@live.de>
	 * @version 0.2.0
	 * @since 0.2.0
	 *
	 * @param string $groupID Group's ID
	 * @param array[] $params Parameters array to populate the group.
	 *
	 * @throws Exception If \$groupID and/or \$params aren't filled or are invalid.
	 * @throws Exception If the Group doesn't exists.
	 *
	 * @return true
	 **/
	public function updateGroup($groupID=false, $params=false){
		
		global $connectDB, $logger;
	
		if(empty($groupID) OR !is_numeric($groupID)){
			throw new \Exception(__("Fill the \$groupID parameter correctly."));
		}
		
		if(self::checkIfGroupExists($groupID) == false){
			throw new \Exception(__("This group doesn't exists."));
		}
		
		self::validateGroupParams($params);
				
		$addGroup = $connectDB->prepare("UPDATE `groups` SET `group_name`=:group_name,`group_color`=:group_color,`group_icon`=:group_icon WHERE `group_id`=:group_id");
		
		$addGroup->bindValue(":group_id",$groupID,\PDO::PARAM_INT);
		$addGroup->bindValue(":group_name",$params['name'],\PDO::PARAM_STR);
		$addGroup->bindValue(":group_color",$params['color'],\PDO::PARAM_STR);
		$addGroup->bindValue(":group_icon",(empty($params['icon'])?NULL:$params['icon']),\PDO::PARAM_STR);
		
		if(!$addGroup->execute()){
			$logger->addError(sprintf("#############################\r\nError MySQL addGroup\r\n#############################\r\nError#############################\r\n%s\r\n#############################\r\n\r\n",var_export($addGroup->errorInfo(), true)));
			throw new \Exception(__("Problems occurred when we tried to add a group. See the logs for more information."));
		}
		
		return true;
		
	}
	
	/**
	 * viewGroup
	 *
	 * View the content from a group.
	 *
	 * @author Henrique Ramos <hramos@live.de>
	 * @version 0.2.0
	 * @since 0.2.0
	 *
	 * @param string $groupID Group's ID
	 *
	 * @throws Exception If \$groupID isn't filled or is invalid.
	 * @throws Exception If the Group doesn't exists.
	 *
	 * @return array[] Return an array containing the values of a group.
	 **/
	public function viewGroup($groupID=false){
		global $connectDB, $logger;
	
		if(empty($groupID) OR !is_numeric($groupID)){
			throw new \Exception(__("Fill the \$groupID parameter correctly."));
		}
		
		if(self::checkIfGroupExists($groupID) == false){
			throw new \Exception(__("This group doesn't exists."));
		}
				
		$viewGroup = $connectDB->prepare("SELECT `group_id`, `group_name`, `group_color`, `group_icon`, `group_date` FROM `groups` WHERE `group_id`=:groupID");
		
		$viewGroup->bindValue(":groupID",$groupID,\PDO::PARAM_INT);
		
		if(!$viewGroup->execute()){
			$logger->addError(sprintf("#############################\r\nError MySQL viewGroup %s\r\n#############################\r\nError#############################\r\n%s\r\n#############################\r\n\r\n", $groupID,var_export($viewGroup->errorInfo(), true)));
			throw new \Exception(__("Problems occurred when we tried to retrieve the content of this group. See the logs for more information."));
		}
		
		return $viewGroup->fetch(\PDO::FETCH_ASSOC);
	}
	
	/**
	 * deleteGroup
	 *
	 * Delete a group.
	 *
	 * @author Henrique Ramos <hramos@live.de>
	 * @version 0.2.0
	 * @since 0.2.0
	 *
	 * @param string $groupID Group's ID
	 *
	 * @throws Exception If \$groupID isn't filled or is invalid.
	 * @throws Exception If the Group doesn't exists.
	 *
	 * @return true
	 */
	public function deleteGroup($groupID=false){
		
		global $connectDB, $logger;
		
		if(empty($groupID) OR !is_numeric($groupID)){
			throw new \Exception(__("Fill the \$groupID parameter correctly."));
		}
				
		if(self::checkIfGroupExists($groupID) == false){
			throw new \Exception(__("This group doesn't exists."));
		}
		
		$deleteGroup = $connectDB->prepare("DELETE FROM `groups` WHERE `group_id`=:group_id");
		
		$deleteGroup->bindValue(":group_id",$groupID,\PDO::PARAM_INT);
		
		if(!$deleteGroup->execute()){
			$logger->addError(sprintf("#############################\r\nError MySQL Delete Group %s\r\n#############################\r\nError#############################\r\n%s\r\n#############################\r\n\r\n", $groupID,var_export($deleteGroup->errorInfo(), true)));
			throw new \Exception(__("Problems occurred when we tried to remove this group. See the logs for more information."));
		}
		
		return true;
		
	}
	
	/**
	 * validateGroupParams
	 *
	 * Validates the params of a group.
	 *
	 * @author Henrique Ramos <hramos@live.de>
	 * @version 0.2.0
	 * @since 0.2.0
	 *
	 * @param array[] $params Group parameters in array format
	 *
	 * @throws Exception If \$params isn't filled or is invalid.
	 *
	 * @return true
	 */
	public static function validateGroupParams($params=null){
		
		if(empty($params) OR !is_array($params)){
			throw new \Exception(__("Fill the \$params parameter."));
		}
		
		$requiredParams = array('name','color');

		if(count(array_intersect_key(array_flip($requiredParams), $params)) < count($requiredParams)) {
			throw new \Exception(__("Fill the \$params parameter correctly. With all keys and values shown in the document."));
		}
				
		if(empty($params['name'])){
			throw new \Exception(__("Fill the \"name\" parameter correctly."));
		}

		if(empty($params['color']) OR !preg_match('/^(#)?([A-Fa-f0-9]{3,6})$/', $params['color'])){
			throw new \Exception(__("Fill the \"color\" parameter correctly."));
		}
		
		if(empty($params['icon']) OR !isset($params['icon'])){
			$params['icon'] = NULL;
		}
		
		return $params;
		
	}
}

?>