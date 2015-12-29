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
	
	{include file="javascript.tpl"}
	
	
  </head>

  <body>
	{include file="navbar.tpl"}
    <div class="container">
      <div class="page-header">
        <h1>Interaction #{$info.run.run_id} - Job {$info.job_name} (#{$info.job_id})</h1>
      </div>
		<div class="form-horizontal">
			<div class="form-group">
				<label class="col-sm-4 control-label">Start at:</label>
				<div class="col-sm-8">
					<p class="form-control-static">{$info.run.run_date|date_format:'%d/%m/%Y %H:%M:%S'}</p>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Delay:</label>
				<div class="col-sm-8">
					<p class="form-control-static">{$info.run.run_start|time_ago:$info.run.run_end|display_time_array}</p>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Have problems?</label>
				<div class="col-sm-8">
					<p class="form-control-static">{$info.run.run_problems|yes_no|capitalize}</p>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Block other jobs?</label>
				<div class="col-sm-8">
					<p class="form-control-static">{$info.run.block_status|yes_no|capitalize}</p>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Block date</label>
				<div class="col-sm-8">
					<p class="form-control-static">{$info.run.block_date|date_format:'%d/%m/%Y %H:%M:%S'|default:None}</p>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">HTTP Code:</label>
				<div class="col-sm-8">
					<p class="form-control-static">{$info.run.run_http_code|default:None}</p>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Total Time (in miliseconds):</label>
				<div class="col-sm-8">
					<p class="form-control-static">{$info.run.run_total_time|default:None}</p>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Redirect Count:</label>
				<div class="col-sm-8">
					<p class="form-control-static">{$info.run.run_redirect_count|default:None}</p>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Response Content Type:</label>
				<div class="col-sm-8">
					<p class="form-control-static">{$info.run.run_content_type|default:None}</p>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Response Content:</label>
				<div class="col-sm-8">
					<textarea class="form-control input-lg" readonly>{$info.run.run_response|htmlspecialchars|default:None}</textarea>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Comments:</label>
				<div class="col-sm-8">
					<textarea class="form-control input-lg" readonly>{$info.run.run_comments|htmlspecialchars|default:None}</textarea>
				</div>
			</div>
			
			<div class="form-group">
				<label class="col-sm-4 control-label">Other Infos:</label>
				<div class="col-sm-8">
					<textarea class="form-control input-lg" readonly>{$info.run.others|htmlspecialchars|default:None}</textarea>
				</div>
			</div>
		</div>
    </div> <!-- /container -->

  </body>
</html>
