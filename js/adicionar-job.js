jQuery(document).ready(function(){
	jQuery("#job_cron").on("change", function(){
		try{
			var schedule = later.parse.cron(jQuery("#job_cron").val()+" *", true);
			var scheduleCron = later.schedule(schedule);
			var NextInteractions = scheduleCron.next(2);
			if( NextInteractions.length > 0){
				jQuery("#cron_parser").html("<div class=\"alert alert-success\" role=\"alert\">O script rodar&aacute; &agrave;s <ul><li>"+NextInteractions[0].toLocaleString()+"</li><li>"+NextInteractions[1].toLocaleString()+"</li></ul></div>");
			}else{
				jQuery("#cron_parser").html("<div class=\"alert alert-danger\" role=\"alert\">Problemas ao tentar selecionar esse cron.</div>");
			}
		}catch(Exception){
			console.log(Exception);
			jQuery("#cron_parser").html("<div class=\"alert alert-danger\" role=\"alert\">Problemas ao tentar selecionar esse cron. Analise o log do Javascript.</div>");
		}
	});
	jQuery("#job_cron").trigger("change");

	jQuery("#alert_1_status").on("change", function(){
		var AlertStatus = jQuery(this).val();
		jQuery(this).parents().find(".toggleGroup").toggle();
		console.log("toggle Alert 1");
	});
	//jQuery("#alert_1_status").trigger("change");
	jQuery(document).on("change", ".block_other_jobs", function(){
		var BlockStatus = jQuery(this).val();
		jQuery(this).parents().find(".block_jobs_group").toggle();
	});

	jQuery(document).on("change", ".changeAlertType", function(){
		var BlockAlertType = jQuery(this).val();
		console.log("Alert type "+BlockAlertType);
		switch(BlockAlertType){
			case 'email':
				jQuery(this).parentsUntil(".alert_group").find(".alertTypes").find(".alert_message_popup_email,.alert_via_email").each(function(){jQuery(this).show()});
				jQuery(this).parentsUntil(".alert_group").find(".alertTypes").find(".alert_via_sound, .alert_via_blink").each(function(){jQuery(this).hide()});
			break;
			case 'sound':
				jQuery(this).parentsUntil(".alert_group").find(".alertTypes").find(".alert_via_sound").show();
				jQuery(this).parentsUntil(".alert_group").find(".alertTypes").find(".alert_via_email, .alert_message_popup_email, .alert_via_blink").each(function(){jQuery(this).hide()});
			break;
			case 'blink':
				jQuery(this).parentsUntil(".alert_group").find(".alertTypes").find(".alert_via_blink").show();
				jQuery(this).parentsUntil(".alert_group").find(".alertTypes").find(".alert_via_email, .alert_via_sound, .alert_message_popup_email").each(function(){jQuery(this).hide()});
			break;
			default: case 'popup':
				jQuery(this).parentsUntil(".alert_group").find(".alertTypes").find(".alert_message_popup_email").show();
				jQuery(this).parentsUntil(".alert_group").find(".alertTypes").find(".alert_via_email, .alert_via_sound, .alert_via_blink").each(function(){jQuery(this).hide()});
			break;
		}
		
	});
	jQuery(".changeAlertType").trigger("change");
	
	jQuery("#addAlert").on("click", function(){
		console.log("Trying to add an alert");
		var idAlert = jQuery(".alert_group").length+1;
		var html = jQuery.templates.addAlertTemplate.render({"amountAlerts":idAlert});
		jQuery(".alert_stage").append(html);
		jQuery("#alert_group_"+idAlert).find(".changeAlertType").trigger("change");
		
	});
	
	jQuery(document).on("click", ".removeAlert", function(){
		jQuery(this).closest(".alert_group").remove();
	});
	
	jQuery(document).on("click", ".removeAlertWithID", function(){
		var alertID = jQuery(this).data('alert_id');
		if(alertID.length == 0){
			return false;
		}
		$.ajax({
			type: "post",
			url: "../remover-alerta/"+alertID,
			error: function(returnVal) {
				popupModalMonitor('Erro!', returnVal.error, 'danger');
				console.log(returnVal);
			},
			success: function (returnVal) {
				console.log(returnVal.success);
				jQuery(this).closest(".alert_group").remove();  
			}
		});
	});
		
});