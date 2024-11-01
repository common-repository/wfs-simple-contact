jQuery(document).ready(function($)
{   
	// adding contact event
	$('#wfsContactSubmit').unbind('click');
	$('#wfsContactSubmit').bind('click', function()
	{
		wfsFrontendAjax.addContact($(this).attr('form_id'));	
	});	
	
	// ajax frontend object
	var wfsFrontendAjax = 
	{
		translatedArr : { 
			alert_required_fields: $('#alert_required_fields').text() == '' ? 'Please enter the required fields.' :  $('#alert_required_fields').text(),
			alert_email_format: $('#alert_email_format').text() == '' ? 'The email format is not correct.' : $('#alert_email_format').text(),
			alert_sent_successfully: $('#alert_sent_successfully').text() == '' ? 'Contact message is sent successfully.' : $('#alert_sent_successfully').text(),
			alert_expired_time: $('#alert_expired_time').text() == '' ? 'Expired time.' : $('#alert_expired_time').text(),
			alert_problem: $('#alert_problem').text() == '' ? 'There has a problem while sending message.' : $('#alert_problem').text(),
			alert_sending: $('#alert_sending').text() == '' ? 'Sending...' : $('#alert_sending').text()
		},
		
		addContact: function(fromTagID)
		{
			var fromTag = $('#'+fromTagID); 

			var contactName = $(fromTag).find('#wfs_contact_name').val().trim();
			var contactEmail = $(fromTag).find('#wfs_contact_email').val().trim();
			var contactSubject = $(fromTag).find('#wfs_contact_subject').val().trim();
			var contactMessage = $(fromTag).find('#wfs_contact_message').val().trim();
			var contactSecureKey = $(fromTag).find('#wfs_contact_secure_key').text().trim();

			if(contactName == '' || contactEmail == '' || contactSubject == '' || contactMessage == '')
			{
				$(fromTag).find('#wfs_contact_notification').html(wfsFrontendAjax.translatedArr.alert_required_fields );
			}
			else if(!wfsFrontendAjax.checkEmail(contactEmail))
			{
				$(fromTag).find('#wfs_contact_notification').html(wfsFrontendAjax.translatedArr.alert_email_format);
			}
			else
			{
				$(fromTag).find('#wfs_contact_notification').html(wfsFrontendAjax.translatedArr.alert_sending);
				$('#wfsContactSubmit').attr('disabled', 'disabled');
				//
				$.ajax({
					type: 'POST',
					dataType: 'json',
					url: myAjax.ajaxUrl,
					data: {
						action: 'add_wfs_contact', 
						contact_name: contactName,
						contact_email: contactEmail, 	
						contact_subject: contactSubject, 	
						contact_message: contactMessage, 	
						contact_secure_key: contactSecureKey
					},
					success: function(data)
					{ 
						$('#wfsContactSubmit').removeAttr('disabled');
						
						if(data == true){
							$(fromTag).find('#wfs_contact_notification').html(wfsFrontendAjax.translatedArr.alert_sent_successfully);
							//
							$(fromTag).find('#wfs_contact_name').val('');
							$(fromTag).find('#wfs_contact_email').val('');
						}
						else if(data == 'time_expired'){
							$(fromTag).find('#wfs_contact_notification').html(wfsFrontendAjax.translatedArr.alert_expired_time);
						}else{
							$(fromTag).find('#wfs_contact_notification').html(wfsFrontendAjax.translatedArr.alert_problem); 												
						}
					},
					error: function(errorThrown){
						$(fromTag).find('#wfs_contact_notification').html(errorThrown.responseText);
						$('#wfsContactSubmit').removeAttr('disabled');
					}
				}); 
			}		
		}, 
		
		checkEmail: function(email) 
		{ 
			var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/; 
			return filter.test(email) ? true : false;
		}
	};

});

