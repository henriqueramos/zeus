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
        <h1>Deletar Job</h1>
      </div>
		{if isset($info.job_id)}
		<form method="post" action="{$url}/remover-job/{$info.job_id}">
			<h2>Job {$info.job_name}</h2>
			<div class="form-group">
				<p class="form-control-static"><h3>Voc&ecirc; tem certeza que deseja deletar esse job e todas suas intera&ccedil;&otilde;es?</h3></p>
			</div>
			<div class="checkbox">
				<label>
					<input type="checkbox" id="accepted" name="accepted"> Sim, eu desejo deletar esse job.
				</label>
			</div>
			<button type="submit" class="btn btn-default">Remover</button>
		</form>
		{/if}

    </div> <!-- /container -->

  </body>
</html>
