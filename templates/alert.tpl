
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
    <div class="container starter-template">
		{if isset($alert)}
			<div class="alert {if $alert.type=="warning"}alert-warning{elseif $alert.type=="error"}alert-error{elseif $alert.type=="info"}alert-info{elseif $alert.type=="success"}alert-success{/if}" role="alert">{$alert.message}</div>
		{else}
			<div class="alert alert-warning" role="alert">Ocorreu um problema! <strong>Contate o administrador.</strong></div>
		{/if}

    </div> <!-- /container -->
	
	
		{include file="configModal.tpl"}
		
		{include file="alertModal.tpl"}
	
  </body>
</html>
