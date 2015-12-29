function popupModalMonitor(title,message, type){
	
	if(typeof title === 'undefined' || !title){
		console.log('Fill the title parameter in popupModalMonitor.');
		return false;
	}
	
	if(typeof message === 'undefined' || !message){
		console.log('Fill the message parameter in popupModalMonitor.');
		return false;
	}
	
	if(typeof type === 'undefined' || !type){
		console.log('Fill the type parameter in popupModalMonitor.');
		return false;
	}
	var headerType = "", glyphicon = "";
	switch(type){
		case 'danger':
			headerType = "modal-header-danger";
			glyphicon = "glyphicon-remove-sign";
		break;
		case 'success':
			headerType = "modal-header-success";
			glyphicon = "glyphicon-ok";
		break;
		case 'warning':
			headerType = "modal-header-warning";
			glyphicon = "glyphicon-alert";
		break;
		case 'info':
			headerType = "modal-header-info";
			glyphicon = "glyphicon-question-sign";
		break;
		default:
		break;
			headerType = "modal-header-primary";
			glyphicon = "glyphicon-thumbs-up";
	}
	
	console.log([title, message, type]);
	
	var alertModal = jQuery("#alertModal");
	
	
	if(!alertModal.length){
		console.log('Alert Modal not found. Aborting the operation.');
		return false;
	}
	
	var modalID = Math.floor(0 + (1+Math.random()-0)*Math.random());
	
	alertModal.clone()
		.prop("id", "modalAlert"+modalID)
		.appendTo("body");
	
	var newModal = jQuery("#modalAlert"+modalID);
	
	newModal
		.find(".modal-header")
		.addClass(headerType);
	newModal
		.find("span.glyphicon")
		.addClass(glyphicon);
	newModal	
		.find("span.inner-title")
		.html(title);
	newModal
		.find(".modal-body")
		.html(message);
	
	console.log("Showing up Modal!");
	newModal.on('show.bs.modal', function(e) {
	  centerModals(jQuery(this));
	});
	
	newModal.modal();
	
}

/* center modal */
function centerModals($element) {
  var $modals;
  if ($element.length) {
	$modals = $element;
  } else {
	$modals = $('.modal-vcenter:visible');
  }
  $modals.each( function(i) {
	var $clone = $(this).clone().css('display', 'block').appendTo('body');
	var top = Math.round(($clone.height() - $clone.find('.modal-content').height()) / 2);
	top = top > 0 ? top : 0;
	$clone.remove();
	$(this).find('.modal-content').css("margin-top", top);
  });
}