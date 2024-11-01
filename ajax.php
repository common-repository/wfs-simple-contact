<?php
/**
 * Description: contains all server ajax functions
 * Post status: 
 * - pending: not read
 * - publish: read
*/

if(!function_exists('add_wfs_contact'))
{
	function add_wfs_contact()
	{	 		
		// contact data
		$contact_subject = $_REQUEST["contact_subject"];
		$contact_name = $_REQUEST["contact_name"];
		$contact_email = $_REQUEST["contact_email"];
		$contact_message = $_REQUEST["contact_message"];
		
		// expired time
		$contact_secure_key = $_REQUEST["contact_secure_key"]; 		
		$date = new DateTime();
		$key = $date->format('H')*3600 + $date->format('i')*60 + $date->format('s');  
		$delta = $key - $contact_secure_key;
		
		if($contact_subject == '' || $contact_name == '' 
			|| $contact_email == '' || $contact_message == '')
		{
			echo 'false';
		}
		elseif($delta > 0 & $delta < 600)
		{ 		
			// Insert the post into the database 
			$post = array( 
				'post_title'    	=> $contact_subject,
				'post_excerpt'		=> $contact_name.'{wfs}'.$contact_email.'{wfs} ',
				'post_name'			=> 'contact-'.$contact_subject,
				'post_content'  	=> $contact_message,
				'post_status'   	=> 'pending',
				'post_type' 		=> 'wfs_contact'		  
			);
			wp_insert_post( $post );  
						
			// send email
			$emailList = get_option('email_list'); 
			$emailArr = explode(';', $emailList);
			$messageContent = 
					'Name: '.$contact_name."\n".
					'Email: '.$contact_email."\n".
					'Message: '. $contact_message;
			
			foreach($emailArr as $emailTo)
			{
				wp_mail($emailTo, '[WFS - New Message] '. $contact_subject, $messageContent);
			}
			
			echo 'true';
		}
		else
		{
			echo 'time_expired';
		}
		
		die();
	}
}

if(!function_exists('delete_wfs_contact'))
{
	function delete_wfs_contact()
	{			
		if(current_user_can('edit_posts') === false) 
		{
			echo 'access_denined';
		}
		else
		{
			$contactID = $_REQUEST["contact_id"] == '' ? 0 : intval($_REQUEST["contact_id"]); 

			if($contactID == 0){
				echo 'false';
			}else{
				wp_delete_post($contactID, true);		
				echo 'true';
			}
		}
		
		die();
	} 
}


if(!function_exists('update_wfs_contact_note'))
{
	function update_wfs_contact_note()
	{			
		if(current_user_can('edit_posts') === false) 
		{
			echo 'access_denined';
		}
		else
		{
			$contactID = $_REQUEST["contact_id"] == '' ? 0 : intval($_REQUEST["contact_id"]); 
			$contactNote = $_REQUEST["contact_note"] ; 

			if($contactID == 0){
				echo 'false';
			}else{
				$post = get_post($contactID);
				$arr = explode("{wfs}", $post->post_excerpt);
				wp_update_post(array(
					'ID'			=> $contactID,
					'post_excerpt'	=> $arr[0].'{wfs}'.$arr[1].'{wfs}'.$contactNote
				));				
				echo 'true';
			}
		}
		
		die();
	} 
}

?>