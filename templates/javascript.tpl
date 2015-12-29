		<!-- jQuery 2.1.4 -->
		<script type="text/javascript" src="//code.jquery.com/jquery-2.1.4.js"></script>
		
		<script type="text/javascript" src="{$url}/bootstrap/js/bootstrap.js"></script>
		
		<script type="text/javascript" src="{$url}/js.cookie.js"></script>
		<script type="text/javascript">
			$(document).ready(function(){
				
				$("#saveConfigs").on("click", function(){
					Cookies.set('refresh_rate', $("#refresh_rate").val());
					$('#configModal').modal('hide');
				});
				
				$("#configModal").on('show.bs.modal', function(){
					$("#refresh_rate").val(Cookies.get('refresh_rate'));
				});
				
				$("#showJobInfo").on("click", function(){
					$("#infoJobs").toggle();
				});

			});
						
		</script>