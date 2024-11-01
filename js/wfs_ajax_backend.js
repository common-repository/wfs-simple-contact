jQuery(document).ready(function($)
{  
	// deleting contact event in view single contact
	if($('#wfsDeleteContact'))
	{
		$('#wfsDeleteContact').unbind('click');
		$('#wfsDeleteContact').bind('click', function()
		{
			if (confirm('Do you want to delete this item?', 'Delete'))  
			{		
				wfsBackendAjax.deleteByID($(this).attr('contact_id'), $(this).attr('return_link'));
			}
		});	
	} 
	
	// deleting contact event in view list of contacts
	if($('.wfsDeleteContact'))
	{
		$('.wfsDeleteContact').unbind('click');
		$('.wfsDeleteContact').bind('click', function()
		{
			if (confirm('Do you want to delete this item?', 'Delete'))  
			{		
				// page index and option for search new message => return link
				optPageIndex = $('#optPageIndex option:selected');
				chkNewMessage = $('#chkNewMessage');
				
				wfsBackendAjax.deleteByID($(this).attr('contact_id'), 
					$(this).attr('return_link')+'&page_index='+$(optPageIndex).val()+'&new_message='+$(chkNewMessage).is(':checked'));
			}
		});
	}
	
	// update contact note event in view single contact
	if($('#wfsUpdateNote'))
	{
		$('#wfsUpdateNote').unbind('click');
		$('#wfsUpdateNote').bind('click', function()
		{ 	
			txtContactNote = $('#wfsContactNote'); 
			wfsBackendAjax.updateContactNote($(this).attr('contact_id'), $(txtContactNote).val()); 
		});	
	} 
	
	// ajax object
	var wfsBackendAjax = 
	{
		deleteByID: function(contactID, returnLink)
		{
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: myAjax.ajaxUrl,
				data: {
					action: 'delete_wfs_contact', 
					contact_id: contactID			
				},			
				success: function(data)
				{ 
					if(data == true)
						if(returnLink != '') window.location = returnLink;
					else if(data == 'access_denined')
						alert('Access denined.')
					else
						alert('There has a problem when deleting!');
				},
				error: function(errorThrown){
					alert(errorThrown.responseText);
				}
			});
		},
		
		updateContactNote: function(contactID, contactNote)
		{
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: myAjax.ajaxUrl,
				data: {
					action: 'update_wfs_contact_note', 
					contact_id: contactID,
					contact_note: contactNote
				},			
				success: function(data)
				{ 
					if(!data)  
						alert('There has a problem when updating note!');
				},
				error: function(errorThrown){
					alert(errorThrown.responseText);
				}
			});
		}
	};
});

