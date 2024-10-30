jQuery(document).ready(function($) {
		jQuery(".mailchimp_subscribe").click(function(){
			var error = false;
			var error_message = '';
			
			jQuery(this).parent().parent().find("#mailchimp_error").html("").hide();
			var mailchimp_email = jQuery(this).parent().parent().find("input[name='mailchimp_email']").val();
			var mailchimp_file = jQuery(this).parent().parent().find("input[name='mailchimp_file']").val();
			
			if (mailchimp_email == '') {
				error = true;
				error_message += 'Your Email is required.\n';
			} else if (!/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/.test(mailchimp_email)) {
				error = true;
				error_message += 'Your Email is incorrect.\n';
			}
			
			if(error){
			
				jQuery(this).parent().parent().find("#mailchimp_error").html(error_message).show();
				jQuery(this).parent().parent().find("input[name='mailchimp_email']").val("");
				
			}else{
			
				var mailchimp_data = {
										action: "mailchimp_subscribe",
										mailchimp_email: mailchimp_email,
										nonce: maildepot.mailchimp_nnc
									};
				jQuery.post(maildepot.ajaxurl, mailchimp_data, function(request) {						
							//alert(request.toSource());							
							
							if(request.success == "1"){
								jQuery(this).parent().parent().find("input[name='mailchimp_email']").val("");
								tb_remove();
								window.location.href = '?action=force_download&file='+mailchimp_file+'&d_nonce='+maildepot.download_ref_nnc;
								
							}else if(request.error == "1"){
								
								//alert(request.error_message);
								jQuery(".mailchimp_error").html(request.error_message).show();
							}
							
							
						}, "json");	//, "json"
			
			}
		});
});	