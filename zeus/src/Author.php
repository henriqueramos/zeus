<?php

namespace Ramos\Zeus;

/**
 * @author Henrique Ramos <hramos@live.de>
 * @version 0.1.0
 */
class Author{
	
	/** @var string $authorID Author's ID **/
	private $authorID = null;
	
	/**
	 * Initialize the class
	 * @author Henrique Ramos <hramos@live.de>
	 * @version 0.1.0
	 * @since 0.1.0
	 */
	public function __construct($authorID=false){		
		if(!empty($authorID) AND is_numeric($authorID)){
			$this->authorID = $authorID;
		}
	}
	
	
}

?>