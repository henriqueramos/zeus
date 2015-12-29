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
				<h1>Job {$info.job_name} (#{$info.job_id})</h1>
				<p class="lead">
					<ul class="list-inline">
						<li><button type="button" id="showJobInfo" class="btn btn-primary">Informa&ccedil;&otilde;es sobre o job</button></li>
						<li><a href="{$url}/editar-job/{$info.job_id}" class="btn btn-success">Editar Job</a></li>
						<li><a href="{$url}/remover-job/{$info.job_id}" class="btn btn-danger">Remover Job</a></li>
					</ul>
				</p>
				<div id="infoJobs" class="well">
					<ul class="list-unstyled">
						<li>Creation Date: {$info.job_date|date_format:'%d/%m/%Y %H:%M:%S'|default:none|capitalize}</li>
						<li>CRON: {$info.job_cron}</li>
						<li>Job Type: {$info.job_type|default:"none"|capitalize}</li>
						<li>Job Status: {$info.job_status|active_disable|capitalize}</li>
						<li>Job Path: {$info.job_path|default:"none"}</li>
						{if count($info.alerts)>0}<li>Alerts <ul>{/if}
						{foreach $info.alerts AS $alert}
							<li>
								Alert {$alert.alert_type|capitalize}:
									<ul>
										<li>Do an alert when <strong>"{$alert.alert_when}"</strong> {$alert.alert_comparison|about_comparison} to <code>"{$alert.alert_return}"</code></li>
										<li>Block other jobs? {$alert.block_other_jobs|active_disable|capitalize}</li>
										{if $alert.block_other_jobs==1}
										<li>Except those IDs: {$alert.block_except}</li>
										{/if}
									</ul>
							</li>
						{/foreach}
						{if count($info.alerts)>0}</ul></li>{/if}
					</ul>
				</div>
			</div>
			 
			<div class="row stage">
				{if empty($info.runs)}
					<div class="well">Not found any job matching this parameters.</div>
				{/if}
				{foreach $info.runs AS $run}
				<div class="col-xs-6 col-md-3 {if $run.run_problems==true}redAlert{else}greenAlert{/if}">
					<h3 class="jobTitle"><a href="{$url}/listar-jobs/{$info.job_id}">{$info.job_name}</a>{if isset($run.run_no)}<span class="badge pull-right"><a href="{$url}/listar-jobs/{$info.job_id}/run/{$run.run_no}">#{$run.run_no}</a></span>{/if}</h3>
					<ul>
						<li>Job Type: {$info.job_type|default:"none"|capitalize}</li>
						<li>Job Status: {$info.job_status|active_disable|capitalize}</li>
						<li>Latest run at {$run.run_date|date_format:'%d/%m/%Y %H:%M:%S'|default:none|capitalize}</li>
					</ul>
				</div>
				{/foreach}
			</div>

		</div> <!-- /container -->

		{include file="configModal.tpl"}
		
	</body>
</html>