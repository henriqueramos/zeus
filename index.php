<?php

/**
 * Controller do sistema
 * @author Henrique Ramos <hramos@live.de>
 * @since 0.1 
 * @version 0.1
 */

//Inclui o arquivo de configuração
require_once("config.php");

$f3->route(array('POST /remover-alerta/@alertID'), function($f3, $params){
	
	try{
		
		if(empty($params['alertID']) OR !is_numeric($params['alertID'])){
			throw new \Exception("Fill the \"alert id\" parameter correctly.");
		}
		
		$alertID = $params['alertID'];
		
		$alert = new \Ramos\Zeus\Alert();
		
		$alert->deleteAlert($alertID);

		echo json_encode(array('success'=>__('Successful alert deletion.')));
		
	}catch(\Exception $e){
		http_response_code(500);
		echo json_encode(array('error'=>$e->getMessage()));
	}
	
});

//Change
$f3->route(array('GET /remover-job/@jobID', 'POST /remover-job/@jobID'), function($f3, $params){
	
	global $Smarty;
	
	try{
		
		$jobs = new \Ramos\Zeus\Job();
	
		if(empty($params['jobID']) OR !is_numeric($params['jobID'])){
			throw new \Exception(__("Not found any job matching this parameters."));
		}
		
		$returnInfo = $jobs->removeJobController($params);
		
		$Smarty->assign('info', $returnInfo);
		
		$Smarty->assign('actualPage', "Remover Job | Zeus Monitor");
		$Smarty->assign('pageTitle', $title);
		$Smarty->display("remover-job.tpl");
		
	}catch(\Exception $e){
		$Smarty->assign('alert', array('message'=>$e->getMessage(), 'type'=>'error'));
		$Smarty->assign('pageTitle', 'Alerta | Zeus Monitor');
		$Smarty->display("alert.tpl");
	}
	
});

//Change
$f3->route(array('GET /listar-jobs/@jobID', 'GET /listar-jobs/@jobID', 'GET /listar-jobs/@jobID/run', 'GET /listar-jobs/@jobID/run/@runID'), function($f3, $params){
	global $Smarty;
	
	try{
		$jobs = new \Ramos\Zeus\Job();
		
		if(!empty($params['jobID']) AND !empty($params['runID'])){
			$returnInfo = $jobs->showRuns($params);
			$Smarty->assign('info', $returnInfo);
			$title = "Visualizar Interação | Zeus Monitor";
			$template = "visualizar-interacao.tpl";
		}
		
		if(!empty($params['jobID']) AND empty($params['runID'])){
			
			$returnInfo = $jobs->showRuns($params);
			
			$javascriptHeaderCodes = array();
			$javascriptHeaderCodes[] = "\$(\"#showJobInfo\").on(\"click\", function(){\$(\"#infoJobs\").toggle();});";
						
			#appendHeaderJS($adicionar_job);
			appendHeaderCode($javascriptHeaderCodes);
			
			$Smarty->assign('info', $returnInfo);
			
			$title = "Visualizar Job | Zeus Monitor";
			$template = "visualizar-job.tpl";
		
		}
		$Smarty->assign('actualPage', 'listar-jobs');
		$Smarty->assign('pageTitle', $title);
		$Smarty->display($template);
		
	}catch(\Exception $e){
		$Smarty->assign('alert', array('message'=>$e->getMessage(), 'type'=>'error'));
		$Smarty->assign('pageTitle', 'Alerta | Zeus Monitor');
		$Smarty->display("alert.tpl");
	}
	
});

//Change
$f3->route(array('GET /listar-jobs'), function($f3, $params){
	global $Smarty;
	
	try{
		
		$jobs = new \Ramos\Zeus\Job();
		
		$returnInfo = $jobs->showJobs($params);
		
		if(empty($returnInfo['jobs'])){
			throw new \Exception(__("Not found any job matching this parameters."));
		}
		
		
		$run = new \Ramos\Zeus\Run();
		
		foreach($returnInfo['jobs'] AS $key=>$job){
			$returnInfo['jobs'][$key]['lastRunning'] = $run->latestJobRun($job['job_id']);
		}
		
		$Smarty->assign('jobs', $returnInfo['jobs']);		
		$Smarty->assign('actualPage', 'Listar Jobs | Zeus Monitor');
		$Smarty->display("listar-jobs.tpl");
		
	}catch(\Exception $e){
		$Smarty->assign('alert', array('message'=>$e->getMessage(), 'type'=>'error'));
		$Smarty->assign('pageTitle', 'Alerta | Zeus Monitor');
		$Smarty->display("alert.tpl");
	}
	
});

$f3->route(array('GET /editar-job', 'GET /editar-job/@jobID'), function($f3, $params){
	global $Smarty, $urlDomain;
	
	try{
		$jobs = new \Ramos\Zeus\Job();
	
		$returnInfo = $jobs->showJobs($params);
		
		#print_r($returnInfo);
		
		$Smarty->assign('job', $returnInfo);
		
		$editar_job = array();
	
		$editar_job[] = array('url'=>"{$urlDomain}/js/later.js");
		$editar_job[] = array('url'=>"{$urlDomain}/js/jsviews.js");
		$editar_job[] = array('url'=>"{$urlDomain}/js/addAlert.js");
		$editar_job[] = array('url'=>"{$urlDomain}/js/adicionar-job.js");
		
		appendHeaderJS($editar_job);
		
		$Smarty->assign('actualPage', 'editar-job');
		$Smarty->assign('pageTitle', sprintf('Editar Job %s (ID: %d) | Zeus Monitor', $returnInfo['job']['job_name'], $returnInfo['job']['job_id']));
		$Smarty->display("editar-job.tpl");
		
	}catch(\Exception $e){
		$Smarty->assign('alert', array('message'=>$e->getMessage(), 'type'=>'error'));
		$Smarty->assign('pageTitle', 'Alerta | Zeus Monitor');
		$Smarty->display("alert.tpl");
	}
	
	/**/
	
});

$f3->route(array('POST /adicionar-job', 'POST /editar-job/@jobID'), function($f3, $params){
	global $Smarty;
	
	try{
		$job = new \Ramos\Zeus\Job();
	
		$returnInfo = json_decode($job->createUpdateJob($params), true);
		
		switch (json_last_error()) {
			case JSON_ERROR_NONE:
				$Smarty->assign('alert', array('message'=>$returnInfo['success'], 'type'=>'success'));
				$Smarty->assign('actualPage', 'editar-job');
				$Smarty->assign('pageTitle', 'Adição bem sucedida | Zeus Monitor');
				$Smarty->display("alert.tpl");
			break;
			default:
				throw new \Exception(__("Unknown error when we tried to decode the JSON response. Contact the administrator."));
			break;
		}
				
	}catch(\Exception $e){
		$Smarty->assign('alert', array('message'=>$e->getMessage(), 'type'=>'error'));
		$Smarty->assign('pageTitle', 'Alerta | Zeus Monitor');
		$Smarty->display("alert.tpl");
	}
	
});

$f3->route(array('GET /adicionar-job'), function($f3, $params){
	global $Smarty, $urlDomain;
	
	$adicionar_job = array();
	
	$adicionar_job[] = array('url'=>"{$urlDomain}/js/later.js");
	$adicionar_job[] = array('url'=>"{$urlDomain}/js/jsviews.js");
	$adicionar_job[] = array('url'=>"{$urlDomain}/js/addAlert.js");
	$adicionar_job[] = array('url'=>"{$urlDomain}/js/adicionar-job.js");
	
	appendHeaderJS($adicionar_job);
	#$Smarty->append('javascriptHeaderCodes', $javascriptHeaderCodes);
	
	$Smarty->assign('actualPage', 'adicionar-job');
	$Smarty->assign('pageTitle', 'Adicionar Job | Zeus Monitor');
	$Smarty->display("adicionar-job.tpl");
});

$f3->route(array('GET /job-mosaic'), function($f3, $params){
	
	try{
		
		$jobs = new \Ramos\Zeus\Job();
		
		$returnInfo = $jobs->visualizarJobs($params);
		
		if(empty($returnInfo['jobs'])){
			throw new \Exception(__("Not found any job matching this parameters."));
		}
		
		foreach($returnInfo['jobs'] AS $key=>$job){
			$returnInfo['jobs'][$key]['lastRunning'] = $run->latestJobRun($job['job_id']);
		}
		
		
		echo json_encode(array('success'=>$returnInfo));
		
	}catch(\Exception $e){
		header($e->getMessage(), true, 500);
		echo json_encode(array('error'=>$e->getMessage()));
	}
	
});

$f3->route(array('GET /ajuda'), function($f3, $params){
	global $Smarty;
	$Smarty->assign('alert', array('message'=>'WIP', 'type'=>'info'));
	$Smarty->assign('actualPage', 'ajuda');
	$Smarty->assign('pageTitle', 'Ajuda | Zeus Monitor');
	$Smarty->display("alert.tpl");
});

$f3->route(array('GET /', 'GET /inicio'), function($f3, $params){
	global $Smarty;
	
	try{
		
		$jobs = new \Ramos\Zeus\Job();
		
		$run = new \Ramos\Zeus\Run();
		
		$returnInfo = $jobs->showJobs($params);
		
		if(empty($returnInfo['jobs'])){
			throw new \Exception(__("Not found any job matching this parameters."));
		}
		
		$alert = new \Ramos\Zeus\Alert();
		
		foreach($returnInfo['jobs'] AS $key=>$job){
			$returnInfo['jobs'][$key]['lastRunning'] = $run->latestJobRun($job['job_id']);
			$returnInfo['jobs'][$key]['alerts'] = $alert->viewAlertsByJob($job['job_id']);
		}
		
		$javascriptHeaderCodes = array();
		$javascriptHeaderCodes[] = "\$(\"#showJobInfo\").on(\"click\", function(){\$(\"#infoJobs\").toggle();});";
		$javascriptHeaderCodes[] = "\$(\"#popAlert\").on(\"click\", function(){popupModalMonitor('Sucesso!', 'Essa &eacute; uma mensagem de sucesso', 'success');});";
		
		$javascriptHeaderCodes[] = "\$(window).on('resize', centerModals);";
		$javascriptHeaderCodes[] = "var refresh_rate = Cookies.get('refresh_rate');";
		$javascriptHeaderCodes[] = "if(!refresh_rate){Cookies.set('refresh_rate', 35);}";
		$javascriptHeaderCodes[] = "if(refresh_rate > 10){setInterval(\"reloadPage()\",refresh_rate*1000);}else{setInterval(\"reloadPage()\",35000);Cookies.set('refresh_rate', 35);}";
		$javascriptHeaderCodes[] = "count = refresh_rate; counter = setInterval(timer, 1000);";
		
		#appendHeaderJS($adicionar_job);
		appendHeaderCode($javascriptHeaderCodes);
		
		$Smarty->assign('jobs', $returnInfo['jobs']);		
		$Smarty->assign('actualPage', 'inicio');
		$Smarty->display("home.tpl");
				
	}catch(\Exception $e){
		$Smarty->assign('alert', array('message'=>$e->getMessage(), 'type'=>'error'));
		$Smarty->assign('pageTitle', 'Alerta | Zeus Monitor');
		$Smarty->display("alert.tpl");
	}
	
});

/*$f3->set('ONERROR',
    function($f3) {
		echo "Ocorreu um erro ao tentar renderizar essa p&aacute;gina. Entre em contato com os administradores";
    }
);*/

$f3->run();
