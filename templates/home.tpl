<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		<meta name="description" content="">
		<meta name="author" content="">
		<link rel="icon" href="favicon.ico">
		<!-- Desabilitando o cache -->
		<meta http-equiv="cache-control" content="max-age=0" />
		<meta http-equiv="cache-control" content="no-cache" />
		<meta http-equiv="expires" content="0" />
		<meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
		<meta http-equiv="pragma" content="no-cache" />
		
		<title>{if isset($pageTitle)}{$pageTitle}{else}Zeus Monitor{/if}</title>

		<!-- Bootstrap core CSS -->
		<link href="{$url}/bootstrap/css/bootstrap.min.css" rel="stylesheet">

		<!-- Custom styles for this template -->
		<link href="{$url}/bootstrap/css/grid.css" rel="stylesheet">
		
		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
		{include file="javascriptHeader.tpl"}
	</head>

	<body>
		{include file="navbar.tpl"}
		<div class="container">

			<div class="page-header">
				<h1>Jobs - <span id="timer"></span></h1>
				<!--<button type="button" id="popAlert">Alerta!</button>-->
			</div>
			 
			<div class="row stage">
				{foreach $jobs AS $job}
				{if $job.lastRunning.run_problems==true}
					{foreach $job.alerts AS $alert}
						{if $alert.alert_status==true}
							{if $alert.alert_type=="popup"}
								{assign var="jsonMessage" value=$alert.alert_message|json_decode:true}
				<script type="text/javascript">
					jQuery(document).ready(function(){
						popupModalMonitor("Warning!", "{$jsonMessage.message}", "danger");
					});
				</script>
							{/if}
							{if $alert.alert_type=="blink"}
				<script type="text/javascript">
					jQuery(document).ready(function(){
						jQuery("#job_{$job.job_id}").addClass("glowingDanger");
					});
				</script>
							{/if}
						{/if}
					
					{/foreach}
				{/if}
				<div id="job_{$job.job_id}" class="col-xs-6 col-md-3 {if $job.lastRunning.run_problems==true}redAlert{else}greenAlert{/if}">
					<h3 class="jobTitle"><a href="{$url}/listar-jobs/{$job.job_id|default:0}">{$job.job_name}</a><span class="badge pull-right"><a href="{$url}/listar-jobs/{$job.job_id|default:0}/run/{$job.lastRunning.run_no|default:0}">#{$job.lastRunning.run_no|default:0}</a></span></h3>
					<ul>
						<li>Job Type: {$job.job_type|default:"none"|capitalize}</li>
						<li>Job Status: {$job.job_status|active_disable|capitalize}</li>
						<li>Latest run at {$job.lastRunning.run_date|date_format:'%d/%m/%Y %H:%M:%S'|default:none|capitalize}</li>
					</ul>
				</div>
				{/foreach}
			</div>

		</div> <!-- /container -->

		{include file="configModal.tpl"}
		
		{include file="alertModal.tpl"}
		
	</body>
</html>