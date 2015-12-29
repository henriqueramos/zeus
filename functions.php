<?php

function __($text){
	return $text;
}

/** explode by comma **/
function cleanUp($string){
	$returnInfo = array();
	$idArray = explode(",", $string);
	foreach($idArray AS $id){
		$trimmedID = trim($id);
		if(is_numeric($trimmedID)){
			$returnInfo[] = $trimmedID;
		}
	}
	return implode(",", $returnInfo);
}

/** explode by comma **/
function cleanUpEmails($string){
	$returnInfo = array();
	$emailArray = explode(",", $string);
	foreach($emailArray AS $email){
		$trimmedEmail = trim($email);
		if(filter_var($trimmedEmail, FILTER_VALIDATE_EMAIL)){
			$returnInfo[] = $trimmedEmail;
		}
	}
	return implode(",", $returnInfo);
}

function transformAlertComparison($input="equal"){
	try{
		if(empty($input)){
			throw new Exception(__("Fill the \$input parameter."));
		}
		switch($input){
			case 'not_equal': return "!="; break;
			case 'less_than': return "<"; break;
			case 'greater_than': return ">"; break;
			case 'less_than_or_equal_to': return "<="; break;
			case 'greater_than_or_equal_to': return ">="; break;
			case 'equal': default: return "==";	break;
		}
	}catch(Exception $e){
		throw $e;
	}
}

function transformAlertWhen($input="http_code"){
	try{
		if(empty($input)){
			throw new Exception(__("Fill the \$input parameter."));
		}
		if(!in_array($input, array('http_code','total_time','redirect_count','content_type','content'))){
			throw new Exception(__("Fill the \$input parameter with a recognized value."));
		}
		return $input;
	}catch(Exception $e){
		throw $e;
	}
}


function applyComparison($when=NULL, $target=NULL, $comparison="equal"){
	try{
		if(is_null($when)){
			throw new Exception(__("Fill the \$when parameter."));
		}
		if(is_null($target)){
			throw new Exception(__("Fill the \$target parameter."));
		}
		if(is_null($comparison) AND !in_array($comparison, array('equal','not_equal','less_than','greater_than','less_than_or_equal_to','greater_than_or_equal_to'))){
			throw new Exception(__("Fill the \$comparison parameter. Choose between \"equal\", \"not_equal\", \"less_than\", \"greater_than\", \"less_than_or_equal_to\" or \"greater_than_or_equal_to\"."));
		}
		switch($comparison){
			case 'not_equal': return $when != $target; break;
			case 'less_than': return $when < $target; break;
			case 'greater_than': return $when > $target; break;
			case 'less_than_or_equal_to': return $when <= $target; break;
			case 'greater_than_or_equal_to': return $when >= $target; break;
			case 'equal': default: return $when == $target;	break;
		}
	}catch(Exception $e){
		throw $e;
	}
}

function sendEmail($to, $subject, $message){
		
	if(empty($to) OR !filter_var($to, FILTER_VALIDATE_EMAIL)){
		//throw new Exception(__("Fill the \$to parameter."));
		return false;
	}
	if(empty($subject)){
		//throw new Exception(__("Fill the \$subject parameter."));
		return false;
	}
	if(empty($message)){
		//throw new Exception(__("Fill the \$message parameter."));
		return false;
	}

	$headers = "MIME-Version: 1.1\r\n";
	$headers .= "Content-type: text/plain; charset=iso-8859-1\r\n";
	$headers .= "From: zeus-monitor@localhost\r\n";
	$headers .= "Return-Path: zeus-monitor@localhost\r\n";
	$send = mail($to, $subject, $message, $headers);
	
	return $send;
}

	/** gerarLinksPaginacao **/
	if(!function_exists('gerarLinksPaginacao')){
	
	/**
	 * Função gerarLinksPaginacao
	 *
	 * Gera uma array com chaves next e previous a partir de uma página visitada e o total de páginas
	 *
	 * @since 0.5
	 *
	 * @param int $atual A página atual
	 * @param int $total Total de páginas da paginação
	 *
	 *
	 * @return array[] Retorna uma array com os elementos next e previous
	 */
	function gerarLinksPaginacao($atual=null, $total=null){
		
		if((empty($atual) && !is_numeric($atual)) OR (empty($total) && !is_numeric($total)))
			return false;
				
		$linksAntQtd = ceil($atual - 1);
		$linksPosQtd = ceil($atual + 2);
		
		$arrayLinks = array();
		
		if(!$linksAntQtd OR $linksAntQtd >= 1){
			$arrayLinks['previous'] = $linksAntQtd+1;
		}
		
		if(!$linksPosQtd OR $linksPosQtd <= $total){
			$arrayLinks['next'] = $linksPosQtd;
		}
		
		return $arrayLinks;
	}
		
	}
	
	/** fileMimetype **/
	if(!function_exists('fileMimetype')){
	
	/**
	 * Função fileMimetype
	 *
	 * Retorna o mime type de um determinado arquivo
	 *
	 * @since 0.5
	 *
	 * @param string $file Full path para o arquivo
	 *
	 * @return string String contendo o mime type do arquivo segundo as especificações Media Types da IANA <http://cweiske.de/tagebuch/php-mimetype.htm>
	 */
	function fileMimetype($file=NULL){
		
		if(!function_exists('finfo_open')){
			throw new Exception(__("Finfo_open not found. We need this function to get the mimetype' file."));
		}
		
		if(empty($file)){
			throw new Exception(__("Fill \$file parameter."));
		}
		
		if(!file_exists($file)){
			throw new Exception(__("File doesn't exists."));
		}
		
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$fileMimetype = finfo_file($finfo, $file);
		finfo_close($finfo);
		
		return $fileMimetype;
	}
	
	}
	
	/** appendHeaderCode **/
	if(!function_exists('appendHeaderCode')){
	
	/**
	 * Função appendHeaderCode
	 *
	 * Adiciona códigos ao header
	 *
	 * @since 0.5
	 *
	 * @param array[] $codeArray Array contendo os códigos
	 *
	 */
	function appendHeaderCode($codeArray=false){
		
		global $Smarty;
		
		if(empty($codeArray) AND !is_array($codeArray)){
			return false;
		}
		
		foreach($codeArray AS $simpleCode){
			$Smarty->append('javascriptHeaderCodes', $simpleCode);
		}
	}
	
	}
	
	/** appendHeaderJS **/
	if(!function_exists('appendHeaderJS')){
	
	/**
	 * Função appendHeaderJS
	 *
	 * Adiciona javascripts ao header
	 *
	 * @since 0.5
	 *
	 * @param array[] $jsArray Array contendo os javascripts
	 *
	 */
	function appendHeaderJS($jsArray=false){
		
		global $Smarty;
		
		if(empty($jsArray) AND !is_array($jsArray)){
			return false;
		}
		
		foreach($jsArray AS $simpleJS){
			$Smarty->append('javascriptHeaderFiles', $simpleJS);
		}
		
	}
	
	}
	

?>