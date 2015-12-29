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
				<h1>Jobs</h1>
			</div>
			 
			<div class="row stage">
				{foreach $jobs AS $job}
				<div class="col-xs-6 col-md-3 {if $job.lastRunning.run_problems==true}redAlert{else}greenAlert{/if}">
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

		<div class="modal fade" id="configModal" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title">Configura&ccedil;&otilde;es</h4>
					</div>
					<div class="modal-body">
						<div class="form-horizontal">
							<div class="form-group form-group-sm">
								<label class="col-sm-4 control-label" for="refresh_rate">Refresh rate (in seconds)</label>
								<div class="col-sm-8">
									<input type="number" id="refresh_rate" name="refresh_rate" class="form-control input-sm" placeholder="" value="">
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button type="button" id="saveConfigs" class="btn btn-primary">Save changes</button>
					</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
		
	</body>
</html>