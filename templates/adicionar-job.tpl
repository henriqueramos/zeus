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
        <h1>Adicionar Job</h1>
        <p class="lead">Editor b&aacute;sico para adi&ccedil;&atilde;o de jobs. Para maiores informa&ccedil;&otilde;es acesse o menu <a href="{$url}/ajuda">"Ajuda"</a>.</p>
      </div>
     
		<form method="post" action="{$url}/adicionar-job">
			<div class="form-group">
				<label for="job_name">Nome</label>
				<input type="text" id="job_name" name="job_name" class="form-control input-lg" placeholder="" value="Teste">
			</div>
			<div class="form-group">
				<label for="job_comment">Coment&aacute;rios</label>
				<textarea class="form-control" rows="5" name="job_comment" id="job_comment">Teste</textarea>
			</div>
			<div class="form-group">
				<label for="job_cron">Agendador (CRON)</label>
				<textarea class="form-control" rows="5" name="job_cron" id="job_cron">* */5 * * *</textarea>
					<br />
				<div id="cron_parser"></div>
			</div>
			<div class="form-group">
				<label for="job_type">Tipo de Arquivo</label>
				<select id="job_type" name="job_type" class="form-control input-lg">
					<option value="internal" selected="selected">Interno (Path)</option>
					<option value="external">Externo (URL)</option>
				</select>
			</div>
			<div class="form-group">
				<label for="job_path">Caminho do Arquivo (Interno ou Externo)</label>
				<input type="text" id="job_path" name="job_path" class="form-control input-lg" placeholder="" value="/walmart/www/html/cron/checarVPN.php">
				<br />
				<div class="alert alert-warning">Arquivos internos s&oacute; podem ser do tipo PHP.</div>
			</div>
			<div class="form-group">
				<label for="job_status">Estado do Job</label>
				<select id="job_status" name="job_status" class="form-control input-lg">
					<option value="active" selected="selected">Habilitado</option>
					<option value="disable">Desabilitado</option>
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
				<div id="alert_group_1" class="alert_group panel panel-default">
					<div class="panel-body">
						<p class="pull-right"><span class="removeAlert btn btn-danger">Remover Alerta</span></p>
						<div class="form-group">
							<label for="alert_1_status">Estado do Alerta</label>
							<select id="alert_1_status" name="alerts[1][status]" class="form-control input-lg">
								<option value="active" selected="selected">Habilitado</option>
								<option value="disable">Desabilitado</option>
							</select>
						</div>
						<div class="form-inline toggleGroup">
							<div class="form-group">
								<label for="alert_1_when">Quando</label>
								<select id="alert_1_when" name="alerts[1][when]" class="form-control input-lg">
									<option value="http_code" selected="selected">HTTP Code</option>
									<option value="total_time">Total Time</option>
									<option value="redirect_count">Redirect Count</option>
									<option value="content_type">Content Type</option>
									<option value="content">Content</option>
								</select>
							</div>
							<div class="form-group">
								<label for="alert_1_comparison">For</label>
								<select id="alert_1_comparison" name="alerts[1][comparison]" class="form-control input-lg">
									<option value="equal">Igual (==)</option>
									<option value="not_equal" selected="selected">Diferente (!=)</option>
									<option value="less_than">Menor (&lt;)</option>
									<option value="greater_than">Maior (&gt;)</option>
									<option value="less_than_or_equal_to">Menor ou igual (&lt;=)</option>
									<option value="greater_than_or_equal_to">Maior ou igual (&gt;=)</option>
								</select>
							</div>
							<div class="form-group">
								<label for="alert_return">Que</label>
								<textarea class="form-control input-lg" name="alerts[1][return]" id="alert_1_return">200</textarea>
							</div>
							<div class="form-group">
								<label for="alert_1_type">Ent&atilde;o alerte via</label>
								<select id="alert_1_type" name="alerts[1][type]" class="form-control input-lg changeAlertType">
									<option value="popup" selected="selected">Popup</option>
									<option value="email">Email</option>
									<option value="sound">Som</option>
									<option value="blink">Blink</option>
								</select>
							</div>
						</div>
						<div class="alertTypes">
							<div class="form-group alert_message_popup_email">
								<label for="alert_1_message">A seguinte mensagem</label>
								<textarea class="form-control input-lg" name="alerts[1][message]" id="alert_1_message">Warning! VPN com problemas</textarea>
							</div>
							<div class="form-group alert_via_email">
								<label for="alert_1_email">Email</label>
								<textarea class="form-control input-lg" name="alerts[1][email]" id="alert_1_email"></textarea>
								<br />
								<div class="alert alert-info">Separe os emails por v&iacute;rgula.</div>
							</div>
							<div class="form-group alert_via_sound">
								<label for="alert_1_sound">Qual o som?</label>
								<select id="alert_1_type" name="alerts[1][sound]" class="form-control input-lg">
									<option value="Ding">Ding</option>
									<option value="Boing">Boing</option>
									<option value="Drop">Drop</option>
									<option value="Ta-da">Ta-da</option>
									<option value="Plink">Plink</option>
									<option value="Wow">Wow</option>
									<option value="Here you go">Here you go</option>
									<option value="Hi">Hi</option>
									<option value="Yoink">Yoink</option>
									<option value="Knock Brush">Knock Brush</option>
									<option value="Woah!">Woah!</option>
									<option value="none" selected="selected">Nenhum</option>
								</select>
								<br />
							</div>
							<div class="form-group alert_via_blink">
								<div class="alert alert-info">O tile do job ficar&aacute; piscando na home.</div>
							</div>
						</div>
						<h4>Bloquear outros Jobs?</h4>
						<div class="alert alert-warning">Se um alerta for emitido, esse job ir&aacute; travar a execução de outros jobs até ser resolvido.</div>
						<div class="form-group">
							<label for="alert_1_block_other_jobs">Estado do Bloqueio</label>
							<select id="alert_1_block_other_jobs" name="alerts[1][block_other_jobs]" class="form-control input-lg block_other_jobs">
								<option value="active">Habilitado</option>
								<option value="disable" selected="selected">Desabilitado</option>
							</select>
						</div>
						<div class="form-group block_jobs_group">
							<label for="alert_1_block_except">Bloqueie exceto esses jobs (opcional)</label>
							<textarea class="form-control" rows="5" name="alerts[1][block_except]" id="alert_1_block_except"></textarea>
								<br />
							<div class="alert alert-warning">Separe os IDs por v&iacute;rgula.</div>
						</div>
					</div>
				</div>
			</div>
			<button type="submit" class="btn btn-default">Cadastrar!</button>
		</form>

    </div> <!-- /container -->

	{include file="configModal.tpl"}
	
  </body>
</html>
