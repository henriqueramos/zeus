	<nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
			<a class="navbar-brand" href="{$url}/inicio">Zeus</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li{if $actualPage == "inicio" && isset($actualPage)} class="active"{/if}><a href="{$url}/inicio">In&iacute;cio</a></li>
            <li{if $actualPage == "adicionar-job"} class="active"{/if}><a href="{$url}/adicionar-job">Adicionar Job</a></li>
			<li{if $actualPage == "editar-job" || $actualPage == "listar-job"} class="active"{/if}><a href="{$url}/listar-jobs">Listar Jobs</a></li>
          </ul>
			<ul class="nav navbar-nav navbar-right">
				<li><a href="{$url}/ajuda">Ajuda</a></li>
				<li><a href="#configModal" id="configButton" data-toggle="modal" data-target="#configModal"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Configs</a></li>
			</ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>