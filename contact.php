<?php
/*
 * Frontend: Display the contact form
 */
 
if ( ! function_exists( 'display_wfs_contact' ) ) 
{
	function display_wfs_contact() 	
	{ 	
		// load cripts
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'wfs_ajax_frontend' ); 
		
		// contact form id, that contain expired time information
		$date = new DateTime();
		$key = $date->format('H')*3600 + $date->format('i')*60 + $date->format('s'); 
		$contactFormID = "wfs_contact".$key;
		?>		
		<div style="display: none;">
			<label id="alert_required_fields"><?php echo wfsTranslate_SimpleContact('alert_required_fields');?></label>
			<label id="alert_email_format"><?php echo wfsTranslate_SimpleContact('alert_email_format');?></label>
			<label id="alert_sent_successfully"><?php echo wfsTranslate_SimpleContact('alert_sent_successfully');?></label>
			<label id="alert_expired_time"><?php echo wfsTranslate_SimpleContact('alert_expired_time');?></label>
			<label id="alert_problem"><?php echo wfsTranslate_SimpleContact('alert_problem');?></label>
			<label id="alert_sending"><?php echo wfsTranslate_SimpleContact('alert_sending');?></label>
		</div> 
		<div id="<?php echo $contactFormID; ?>" style="padding-left: 10px;">
			<span id="wfs_contact_secure_key" style="display:none;"><?php echo $key;?></span>
			<div style="text-align: left; padding-top: 5px;"> 
				<label><?php echo wfsTranslate_SimpleContact('name');?><span style="color: red;"> *</span></label> 
			</div>
			<div style="text-align: left;">
				<input type="text" style="text-align: left; margin: 0; width: 320px;" id="wfs_contact_name" value="" size="40" class="text">
			</div>
			<div style="text-align: left; padding-top: 5px;"> 
				<label><?php echo wfsTranslate_SimpleContact('email-address');?><span style="color: red;"> *</span></label> 
			</div>
			<div style="text-align: left;">
				<input type="text" style="text-align: left; margin: 0; width: 320px;" id="wfs_contact_email" value="" size="40" class="text">
			</div>
			<div style="text-align: left; padding-top: 5px;"> 
				<label><?php echo wfsTranslate_SimpleContact('subject');?><span style="color: red;"> *</span></label> 
			</div>
			<div style="text-align: left;">
				<input type="text" style="text-align: left; margin: 0; width: 320px;" id="wfs_contact_subject" value="" size="40" class="text">
			</div>
			<div style="text-align: left; padding-top: 5px;"> 
				<label><?php echo wfsTranslate_SimpleContact('message');?><span style="color: red;"> *</span></label> 
			</div>
			<div style="text-align: left;">
				<textarea id="wfs_contact_message" style=" width: 320px;" cols="30" rows="5"></textarea>
			</div> 
			<div style="text-align: left; padding-top: 8px;"> 
				<input id="wfsContactSubmit" type="button" value="<?php echo wfsTranslate_SimpleContact('submit');?>" form_id="<?php echo $contactFormID; ?>"/> 
			</div>
			<span id="wfs_contact_notification" style="color: red;" style="color: red;"></span>
		</div> <?php
	}
}

/*
 * Admin: View each contact
 */
if ( ! function_exists( 'view_wfs_contact' ) ) 
{
	function view_wfs_contact() 
	{
		$postID = $_REQUEST["contact_id"];
		if($postID == '')
			$postID = '0';
		$postID = intval($postID);
		
		view_wfs_contact_func($postID) ;
	}
} 
if ( ! function_exists( 'view_wfs_contact_func' ) ) 
{
	function view_wfs_contact_func($post_id) 
	{
		$post = get_post($post_id);
		$arr = explode("{wfs}", $post->post_excerpt);
		// old version only includes email and name, not 'note'
		if(count($arr) == 2)
			array_push($arr, ' ');
		?>

		<?php 		
		// load cripts
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'wfs_ajax_backend' );
		?> 	

		<div style="max-width: 800px;">
			<br/>
			<a href="admin.php?page=wfs_contacts.php" class="button">
				<< Return</a>
			<a href="javascript:void(0);" id="wfsDeleteContact" contact_id="<?php echo $post->ID;?>" return_link="admin.php?page=wfs_contacts.php" class="button">
				Delete</a> 
			<a href="javascript:void(0);" id="wfsUpdateNote" contact_id="<?php echo $post->ID;?>" return_link="admin.php?page=wfs_contacts.php" class="button">
				Update contact note</a> 
			<br/>
			<p> 
				Admin note: </br> 
				<input id="wfsContactNote" type="text" style="text-align: left; margin: 0; width: 320px;" value="<?php echo $arr[2];?>" size="40" class="text">
			</p>
			<p>The contact message detail:</p> 
			<table>
				<tr>
					<td style="width: 100px;"><strong>+ From: </strong></td>
					<td><?php echo $arr[0]; ?></td>
				</tr>
				<tr>
					<td style="width: 100px;"><strong>+ Email: </strong></td>
					<td><a href="mailto:<?php echo $arr[1]; ?>"><?php echo $arr[1]; ?></a></td>
				</tr>
				<tr>
					<td style="width: 100px;"><strong>+ Subject: </strong></td>
					<td><?php echo $post->post_title; ?></td>
				</tr>
				<tr>
					<td style="width: 100px;"><strong>+ Message: </strong></td>
					<td><div class="entry-content"><?php echo $post->post_content; ?></div></td>
				</tr>
			</table>
		</div>
		
		<?php		
		// update viewed message
		if($post->post_status == 'pending')
		{
			$date = $post->post_date;
			// change status to publish => read
			wp_update_post(array(
				'ID'           	=> $post->ID,
				'post_status' 	=> 'publish'
			));			
			// update old post date, because when publish, the post date is auto changed
			wp_update_post(array(
				'ID'           	=> $post->ID, 
				'post_date'		=> $date
			));
		}
	}
}
?>