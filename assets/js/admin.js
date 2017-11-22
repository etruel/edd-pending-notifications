jQuery(document).ready(function($){
	//var ajaxurl = edd_pending_object.ajax_url;
	// creating the ajax function
	function edd_pending_ajax_post(element){
		var data = {
			payment_id : $("#sendData").attr("payment_id"),
			action : "edd_pending_notification_email_payment",
			_ajax_nonce : edd_pending_object.nonce

		}
		$.post(ajaxurl,data,function( result ) {
				element.parent().find('span.msj_span').text(result);
			});
	}
	// open the download button
	$("#sendData").click(function(){
		$(this).parent().find('span.msj_span').show('fast');
		$(this).parent().find('span.msj_span').text(edd_pending_object.text_send_email+'....');
		edd_pending_ajax_post($(this));
		return false;
	});
});