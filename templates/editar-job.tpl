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
        <h1>Editar Job</h1>
        <p class="lead">Editor b&aacute;sico para adi&ccedil;&atilde;o de jobs. Para maiores informa&ccedil;&otilde;es acesse o menu <a href="{$url}/ajuda">"Ajuda"</a>.</p>
      </div>
     
		<form method="post" action="{$url}/editar-job/{$job.job_id}">
			<div class="form-group">
				<label for="job_name">Nome</label>
				<input type="text" id="job_name" name="job_name" class="form-control input-lg" placeholder="" value="{$job.job_name}">
			</div>
			<div class="form-group">
				<label for="job_comment">Coment&aacute;rios</label>
				<textarea class="form-control" rows="5" name="job_comment" id="job_comment">{$job.job_comment}</textarea>
			</div>
			<div class="form-group">
				<label for="job_cron">Agendador (CRON)</label>
				<textarea class="form-control" rows="5" name="job_cron" id="job_cron">{$job.job_cron}</textarea>
					<br />
				<div id="cron_parser"></div>
			</div>
			<div class="form-group">
				<label for="job_type">Tipo de Arquivo</label>
				<select id="job_type" name="job_type" class="form-control input-lg">
					<option value="internal"{if $job.job_type == "internal"} selected="selected"{/if}>Interno (Path)</option>
					<option value="external"{if $job.job_type == "external"} selected="selected"{/if}>Externo (URL)</option>
				</select>
			</div>
			<div class="form-group">
				<label for="job_path">Caminho do Arquivo (Interno ou Externo)</label>
				<input type="text" id="job_path" name="job_path" class="form-control input-lg" placeholder="" value="{$job.job_path}">
				<br />
				<div class="alert alert-warning">Arquivos internos s&oacute; podem ser do tipo PHP.</div>
			</div>
			<div class="form-group">
				<label for="job_status">Estado do Job</label>
				<select id="job_status" name="job_status" class="form-control input-lg">
					<option value="active"{if $job.job_status == true} selected="selected"{/if}>Habilitado</option>
					<option value="disable"{if $job.job_status == false} selected="selected"{/if}>Desabilitado</option>
				</select>
			</div>
			<hr />
			<h3>Alertas <small>Voc&ecirc; pode selecionar um determinado retorno do Job e verificar se ele difere de um valor pr&eacute;-determinado, disparando um alerta.</small></h3>
			<div class="clearfix">
				<p class="pull-right">
					<span id="addAlert" class="btn btn-primary">Adicionar Alerta</a></span>
				</p>
			</div>
			<div class="alert_stage">
				{include file="form-alert-populated.tpl"}
			</div>
			<button type="submit" class="btn btn-default">Editar!</button>
		</form>

    </div> <!-- /container -->
	
	{include file="alertModal.tpl"}
  </body>
</html>
