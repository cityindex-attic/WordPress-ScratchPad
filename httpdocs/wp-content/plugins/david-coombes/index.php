<?php
/**
 * @package david-coombes
 */
/*
Plugin Name: David Coombes
Plugin URI: http://david-coombes.com
Description: Email online users from widget on front end or admin page in backend
Version: 2.5.6
Author: David Coombes
Author URI: http://david-coombes.com
*/

//debug?
error_reporting(E_ALL);
ini_set("display_errors", "on");

//constants
define("EMAIL_ONLINE_DIR", WP_PLUGIN_DIR . "/" . basename(dirname(__FILE__)));
define("EMAIL_ONLINE_URL", WP_PLUGIN_URL . "/" . basename(dirname(__FILE__)));

//vars
$emailonline_action = @$_REQUEST['emailonline_action'];
/** @var object An object of table names */
$emailOnline_tables = get_option("emailOnline_tables");

//include files
require_once( EMAIL_ONLINE_DIR . "/application/includes/debug.func.php");
require_once( EMAIL_ONLINE_DIR . "/application/EmailOnlineUsers.class.php");
require_once( EMAIL_ONLINE_DIR . "/application/modules/EmailOnlineUsersDashboard.class.php");
require_once( EMAIL_ONLINE_DIR . "/application/modules/EmailOnlineUsersWidget.class.php");

//construct plugin objects
$emailonline = new EmailOnlineUsers();
$emailonline_dashboard = new EmailOnlineUsersDashboard();

//load global javascripts
$emailonline->load_scripts();


/**
 * Actions and Filters
 */
add_action('admin_menu', array($emailonline, 'admin_menu'));
add_action('admin_head', array($emailonline, 'head_javascript'));
add_action('widgets_init',create_function('', 'return register_widget("EmailOnlineUsersWidget");') );
add_action('wp_ajax_emailonline_users_log', array($emailonline, 'emailonline_users_log'));
add_action('wp_head', array($emailonline, 'head_javascript'));
register_activation_hook(__FILE__, 'emailOnline_INSTALL');

/**
 * The plugin install action.
 * 
 * Creates the tables and stores the table names as an object in wordpress
 * option 'emailOnline_tables'.
 *
 * @global object $emailOnline_tables 
 */
function emailOnline_INSTALL(){
	
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
	//vars
	global $emailOnline_tables;	
	$emailOnline_tables = (object) array(
		'user' => 'emailonline_user'
	);
	
	//set options
	delete_option("emailOnline_tables");
	add_option("emailOnline_tables", $emailOnline_tables);
	delete_option("emailOnline_version");
	add_option("emailOnline_version", "1.0");
	
	//build table
	$sql = "
		CREATE TABLE IF NOT EXISTS `{$emailOnline_tables->user}` (
			`user_id` int(11) NOT NULL,
			`last_online` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			UNIQUE KEY `user_id` (`user_id`)
		);
	";
	dbDelta($sql);
}
?>