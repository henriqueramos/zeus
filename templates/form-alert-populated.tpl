				{foreach $job.alerts AS $alert}
				<div id="alert_group_{$alert@iteration}" class="alert_group panel panel-default">
					<input type="hidden" name="alerts[{$alert@iteration}][id]" value="{$alert.alert_id}" />
					<div class="panel-body">
						<p class="pull-right"><span class="removeAlertWithID btn btn-danger" data-alert_id="{$alert.alert_id}">Remover Alerta</span></p>
						<div class="form-group">
							<label for="alert_{$alert@iteration}_status">Estado do Alerta</label>
							<select id="alert_{$alert@iteration}_status" name="alerts[{$alert@iteration}][status]" class="form-control input-lg">
								<option value="active"{if $alert.alert_status==TRUE} selected="selected"{/if}>Habilitado</option>
								<option value="disable"{if $alert.alert_status==FALSE} selected="selected"{/if}>Desabilitado</option>
							</select>
						</div>
						<div class="form-inline toggleGroup">
							<div class="form-group">
								<label for="alert_{$alert@iteration}_when">Quando</label>
								<select id="alert_{$alert@iteration}_when" name="alerts[{$alert@iteration}][when]" class="form-control input-lg">
									<option value="http_code"{if $alert.alert_when=="http_code"} selected="selected"{/if}>HTTP Code</option>
									<option value="total_time"{if $alert.alert_when=="total_time"} selected="selected"{/if}>Total Time</option>
									<option value="redirect_count"{if $alert.alert_when=="redirect_count"} selected="selected"{/if}>Redirect Count</option>
									<option value="content_type"{if $alert.alert_when=="content_type"} selected="selected"{/if}>Content Type</option>
									<option value="content"{if $alert.alert_when=="content"} selected="selected"{/if}>Content</option>
								</select>
							</div>
							<div class="form-group">
								<label for="alert_{$alert@iteration}_comparison">For</label>
								<select id="alert_{$alert@iteration}_comparison" name="alerts[{$alert@iteration}][comparison]" class="form-control input-lg">
									<option value="equal"{if $alert.alert_comparison=="equal"} selected="selected"{/if}>Igual (==)</option>
									<option value="not_equal"{if $alert.alert_comparison=="not_equal"} selected="selected"{/if}>Diferente (!=)</option>
									<option value="less_than"{if $alert.alert_comparison=="less_than"} selected="selected"{/if}>Menor (&lt;)</option>
									<option value="greater_than"{if $alert.alert_comparison=="greater_than"} selected="selected"{/if}>Maior (&gt;)</option>
									<option value="less_than_or_equal_to"{if $alert.alert_comparison=="less_than_or_equal_to"} selected="selected"{/if}>Menor ou igual (&lt;=)</option>
									<option value="greater_than_or_equal_to"{if $alert.alert_comparison=="greater_than_or_equal_to"} selected="selected"{/if}>Maior ou igual (&gt;=)</option>
								</select>
							</div>
							<div class="form-group">
								<label for="alert_return">Que</label>
								<textarea class="form-control input-lg" name="alerts[{$alert@iteration}][return]" id="alert_{$alert@iteration}_return">{$alert.alert_return}</textarea>
							</div>
							<div class="form-group">
								<label for="alert_{$alert@iteration}_type">Ent&atilde;o alerte via</label>
								<select id="alert_{$alert@iteration}_type" name="alerts[{$alert@iteration}][type]" class="form-control input-lg changeAlertType">
									<option value="popup"{if $alert.alert_type=="popup"} selected="selected"{/if}>Popup</option>
									<option value="email"{if $alert.alert_type=="email"} selected="selected"{/if}>Email</option>
									<option value="sound"{if $alert.alert_type=="sound"} selected="selected"{/if}>Som</option>
									<option value="blink"{if $alert.alert_type=="blink"} selected="selected"{/if}>Blink</option>
								</select>
							</div>
						</div>
						{if $alert.alert_type=="popup" OR $alert.alert_type=="email"}
							{assign var=messageContent value=$alert.alert_message|json_decode:1}
						{/if}
						<div class="alertTypes">
							<div class="form-group alert_message_popup_email">
								<label for="alert_{$alert@iteration}_message">A seguinte mensagem</label>
								<textarea class="form-control input-lg" name="alerts[{$alert@iteration}][message]" id="alert_{$alert@iteration}_message">{$messageContent.message}</textarea>
							</div>
							<div class="form-group alert_via_email">
								<label for="alert_{$alert@iteration}_email">Email</label>
								<textarea class="form-control input-lg" name="alerts[{$alert@iteration}][email]" id="alert_{$alert@iteration}_email">{$messageContent.emails}</textarea>
								<br />
								<div class="alert alert-info">Separe os emails por v&iacute;rgula.</div>
							</div>
							<div class="form-group alert_via_sound">
								<label for="alert_{$alert@iteration}_sound">Qual o som?</label>
								<select id="alert_{$alert@iteration}_type" name="alerts[{$alert@iteration}][sound]" class="form-control input-lg">
									<option value="Ding"{if $messageContent.sound=="Ding"} selected="selected"{/if}>Ding</option>
									<option value="Boing"{if $messageContent.sound=="Boing"} selected="selected"{/if}>Boing</option>
									<option value="Drop"{if $messageContent.sound=="Drop"} selected="selected"{/if}>Drop</option>
									<option value="Ta-da"{if $messageContent.sound=="Ta-da"} selected="selected"{/if}>Ta-da</option>
									<option value="Plink"{if $messageContent.sound=="Plink"} selected="selected"{/if}>Plink</option>
									<option value="Wow"{if $messageContent.sound=="Wow"} selected="selected"{/if}>Wow</option>
									<option value="Here you go"{if $messageContent.sound=="Here you go"} selected="selected"{/if}>Here you go</option>
									<option value="Hi"{if $messageContent.sound=="Hi"} selected="selected"{/if}>Hi</option>
									<option value="Yoink"{if $messageContent.sound=="Yoink"} selected="selected"{/if}>Yoink</option>
									<option value="Knock Brush"{if $messageContent.sound=="Knock Brush"} selected="selected"{/if}>Knock Brush</option>
									<option value="Woah!"{if $messageContent.sound=="Woah!"} selected="selected"{/if}>Woah!</option>
									<option value="none"{if $messageContent.sound=="none" OR isset($messageContent.sound) OR empty($messageContent.sound)} selected="selected"{/if}>Nenhum</option>
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
							<label for="alert_{$alert@iteration}_block_other_jobs">Estado do Bloqueio</label>
							<select id="alert_{$alert@iteration}_block_other_jobs" name="alerts[{$alert@iteration}][block_other_jobs]" class="form-control input-lg block_other_jobs">
								<option value="active"{if $alert.block_other_jobs==TRUE} selected="selected"{/if}>Habilitado</option>
								<option value="disable"{if $alert.block_other_jobs==FALSE} selected="selected"{/if}>Desabilitado</option>
							</select>
						</div>
						{if $alert.block_other_jobs==TRUE}
						<script type="application/javascript">
							jQuery(document).ready(function(){
								jQuery(".block_except_{$alert@iteration}").show();
							});
						</script>
						{/if}
						<div class="form-group block_jobs_group block_except_{$alert@iteration}">
							<label for="alert_{$alert@iteration}_block_except">Bloqueie exceto esses jobs (opcional)</label>
							<textarea class="form-control" rows="5" name="alerts[{$alert@iteration}][block_except]" id="alert_{$alert@iteration}_block_except">{$alert.block_except|default:""}</textarea>
								<br />
							<div class="alert alert-warning">Separe os IDs por v&iacute;rgula.</div>
						</div>
					</div>
				</div>
				{/foreach}