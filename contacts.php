<?php
/*
 * list contacts in table
 */
if(!function_exists('load_wfs_contacts'))
{
	function load_wfs_contacts()
	{
		$pageIndex = $_REQUEST["page_index"];
		if($pageIndex == '')
			$pageIndex = '0';
		$pageIndex = intval($pageIndex);
		
		$newMessage = $_REQUEST["new_message"];
		//
		load_wfs_contacts_by_page($pageIndex, $newMessage);
	}
}
if (!function_exists( 'load_wfs_contacts_by_page' )) 
{
	function load_wfs_contacts_by_page($pageIndex, $newMessage) 
	{
		global $post;
		$postPerPage = 20;
		$postStatus = 'publish, pending';
		if($newMessage == 'true')
			$postStatus = 'pending';
			
		//		
		$posts = new WP_Query();
		$posts->query( array(
			'posts_per_page' => -1, 	
			'post_status' => 'pending',
			'post_type' => 'wfs_contact'
		) );
		$wfsNewContact = sizeof( $posts->posts );
		
		if($newMessage == 'true'){
			$wfsContactCount = $wfsNewContact;
		}
		else{
			$posts = new WP_Query();
			$posts->query( array(
				'posts_per_page' => -1, 	 
				'post_type' => 'wfs_contact'
			) ); 
			$wfsContactCount = sizeof( $posts->posts );
		}
		$wfsPageCount = ceil($wfsContactCount/$postPerPage);  
		if($wfsPageCount == 0)
			$wfsPageCount = 1;
		
		if($pageIndex >= $wfsPageCount)
			$pageIndex = $wfsPageCount-1;
		
		//
		$args = array(  
			'post_type' 		=> 'wfs_contact',	
			'post_status'      	=> $postStatus,
			'posts_per_page'   	=> $postPerPage,
			'offset'           	=> $pageIndex*$postPerPage,
			'orderby'			=> 'ID',
			'order'				=> 'DESC'
		);
		$wfsContacts = get_posts( $args );		
		?>

		<?php 		
		// load cripts
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'wfs_ajax_backend' );
		?>

		<div style="padding-right: 10px;">	
			<script> 				
				function searchContacts(pageIndex)
				{  
					chkNewMessage = document.getElementById('chkNewMessage');
					window.location='admin.php?page=wfs_contacts.php&page_index='+pageIndex+'&new_message='+chkNewMessage.checked; 
				}
			</script>
			
			<h2>WFS Simple Contact</h2>
			<h4>Minimum Version of Control Panel</h4> 
			
			<h3>[Help]</h3>	
			<ul>
				<li>Use this key to add contact form to page/post: <strong>[wfs_contact]</strong></li>
				<li>You can create your own translated file '.ini' in the folder 'languages' of plugin folder (exist vi.ini & en.ini)</li>
			</ul>		 
			
			<h3>[Settings]</h3>			
			<form method="post" action="options.php">
				<?php settings_fields( 'wfs_contact_settings_group' ); ?>
				<?php do_settings_sections( 'wfs_contact_settings_group' ); ?>
				<table class="form-table">
					<tr valign="top">
					<th scope="row">Default language</th>
					<td><input type="text" name="default_language" value="<?php echo get_option('default_language'); ?>" /> Example: en</td>
					</tr> 
					
					<tr valign="top">
					<th scope="row">Email list (receive email that alerts new message) </th>
					<td><input type="text" name="email_list" value="<?php echo get_option('email_list'); ?>" style="width: 300px;"/>Use ';' to separate each email</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
			
			<h3>[Messages]</h3>			
			<span><?php _e('Total unread messages: ');?></span><strong><?php echo $wfsNewContact;?> </strong>
			<span style="margin-left: 30px;">Page Index: </span>
			<select id="optPageIndex"> <?php
				for($i=0; $i<$wfsPageCount; $i++)
				{?>
					<option value="<?php echo $i;?>" <?php if($i==$pageIndex) echo 'selected';?> onclick="searchContacts(<?php echo $i;?>);"><?php echo $i+1;?></option><?php					
				}?>
			</select>
			<input id="chkNewMessage" type="checkbox" <?php if($newMessage == 'true') echo 'checked'; ?> onchange="searchContacts(0);" style="margin-left: 30px;"></input> 
			<label for="chkNewMessage"><?php _e('Search new message', 'wfs_search_new');?></label>
			<table cellspacing="0" class="wp-list-table widefat fixed posts">
				<thead>
					<tr>
						<th width="30px"><?php _e('No.');?></th>
						<th><?php _e('View status', 'wfs_view_status');?></th>
						<th><?php _e('Contact Title', 'wfs_contact_title');?></th> 
						<th><?php _e('Name', 'wfs_name');?></th>  
						<th><?php _e('Email address', 'wfs_email_address');?></th>    
						<th><?php _e('Admin Note');?></th>  
						<th><?php _e('Date', 'wfs_date');?></th>
					</tr>
				</thead> 
				<tbody id="the-list">					
				<?php 
				$contactIndex = $pageIndex*$postPerPage + 1;
				foreach ( $wfsContacts as $post ) : 
					setup_postdata( $post ); 
					$arr = explode("{wfs}", $post->post_excerpt);
					// old version only includes email and name, not 'note'
					if(count($arr) == 2)
						array_push($arr, ' ');
					?> 
					
					<tr valign="top" id="<?php echo 'post-'.$post->ID;?>"> 	
						<td>
							<?php echo $contactIndex++;	?>
						</td>
						<td> 
							<?php  
							if($post->post_status == 'publish')
								echo '<div style="color: #FFFFFF; background-color: #4D90FE; width: 50px;"> READ</div>';
							else
								echo '<div style="color: #FFFFFF; background-color: #FFA500; width: 80px;"> Un-READ</div>';	
							?>
						</td>   
						<td class="post-title page-title column-title">
							<strong>
								<a title="Edit �WFS Contact Script�" class="row-title"
									href="<?php echo 'admin.php?page=view_wfs_contact.php&contact_id='.$post->ID; ?>">
								<?php the_title(); ?></a>
							</strong>
							<div class="locked-info">
								<span class="locked-avatar"></span><span class="locked-text"></span>
							</div>
							<div class="row-actions"> 
								<span class="delete">
									<a href="javascript:void(0);" class="wfsDeleteContact submitdelete" contact_id="<?php echo $post->ID;?>" return_link="admin.php?page=wfs_contacts.php">
									Delete</a> 
								</span> 
							</div> 
						</td>          
						<td class=" column-date"> 
							<?php echo $arr[0]; ?>
						</td>          
						<td class="date column-date">  
							<a href="mailto:<?php echo $arr[1]; ?>"><?php echo $arr[1]; ?></a>
						</td>           
						<td class=" column-date"> 
							<?php echo $arr[2]; ?>
						</td>       
						<td class="date column-date"> 
							<?php echo $post->post_date; ?> 
						</td>
					</tr> 					
				<?php endforeach;
				wp_reset_postdata(); ?>				
				</tbody>
			</table>
		</div><?php
	}
}

?>