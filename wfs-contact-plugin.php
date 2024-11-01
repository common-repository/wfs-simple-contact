<?php

/**
* Plugin Name: WFS Simple Contact
* Plugin URI: http://webfaceScript.com
* Description: The simple contact form and admin control
* Version: 0.7
* Author: WebfaceScript
* Author URI: http://webfaceScript.com
*/

/*
* License: Copyright 2013 WebfaceScript.com
*/

include( dirname( __FILE__ ) . '/lib/configurations.php');
require_once( dirname( __FILE__ ) . '/lib/functions.php' );
wfsLoadTranslatingLangText_SimpleContact(get_option('default_language')) ;

require_once( dirname( __FILE__ ) . '/contacts.php' ); 
require_once( dirname( __FILE__ ) . '/contact.php' );
require_once( dirname( __FILE__ ) . '/ajax.php' );

/*
* Menu
*/
add_action( 'admin_menu', 'wfs_contact_register_main_menu' );
if(!function_exists('wfs_register_main_menu'))
{
	function wfs_contact_register_main_menu()
	{
		// add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
		add_menu_page( 
			'WFS Contact List', 
			'WFS Contact', 
			'edit_posts', 
			'wfs_contacts.php', // this key is used in other pages
			'load_wfs_contacts', 
			plugins_url('assets/images/menu_icon.png', __FILE__ ), 
			9003
		);  
		
		// hidden submenu
		// add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
		add_submenu_page(
			'wfs_wow_slider_hidden_menu',
			'Display WFS Contact', 
			'Display', 
			'edit_posts', 
			'view_wfs_contact.php', 
			'view_wfs_contact'
		); 
	}	
}

/*
 * Add setting options
 */
add_action( 'admin_init', 'register_wfs_contact_setting' );
if(!function_exists('register_wfs_contact_setting'))
{
	function register_wfs_contact_setting() { 
		register_setting( 'wfs_contact_settings_group', 'email_list' );
		register_setting( 'wfs_contact_settings_group', 'default_language' ); 
	}
}

/*
* page, post displays the contact form
*/
add_shortcode( 'wfs_contact', 'wfs_contact_shortcode_func' );
function wfs_contact_shortcode_func() 
{ 
	display_wfs_contact();
}

/*
* widget displays the contact form
*/
require_once( dirname( __FILE__ ) . '/wfs-contact-widget.php' );
add_action( 'widgets_init', 'register_wfs_contact_widget' );
function register_wfs_contact_widget() {
    register_widget( 'wfs_contact_widget' );
}

/*
* adding AJAX 
*/
add_action( 'init', 'wfs_contact_script_enqueuer' );
function wfs_contact_script_enqueuer() {
   wp_register_script( "wfs_ajax_frontend", WP_PLUGIN_URL.'/wfs-simple-contact/js/wfs_ajax_frontend.js', array('jquery') );
   wp_localize_script( 'wfs_ajax_frontend', 'myAjax', array( 'ajaxUrl' => admin_url( 'admin-ajax.php' )));        
   
   wp_register_script( "wfs_ajax_backend", WP_PLUGIN_URL.'/wfs-simple-contact/js/wfs_ajax_backend.js', array('jquery') );
   wp_localize_script( 'wfs_ajax_backend', 'myAjax', array( 'ajaxUrl' => admin_url( 'admin-ajax.php' )));        

}

// for logged users
add_action("wp_ajax_add_wfs_contact", "add_wfs_contact");
add_action("wp_ajax_delete_wfs_contact", "delete_wfs_contact");
add_action('wp_ajax_update_wfs_contact_note', 'update_wfs_contact_note'); 

// for un-logged users
add_action('wp_ajax_nopriv_add_wfs_contact', 'add_wfs_contact'); 

?>