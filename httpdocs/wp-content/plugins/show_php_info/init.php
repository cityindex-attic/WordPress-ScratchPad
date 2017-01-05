<?php

/*
  Plugin Name: Show PhpInfo
  Plugin URI: http://github.com/mrdavidlaing/Wordpress-Interview
  Description: A simple plugin showing phpinfo in the WordPress admin area
  Author: David Laing
  Author URI: http://davidlaing.com/
  Version: 0.1
 */

$plugin_path = dirname( __FILE__ ) . '/';
if ( class_exists( 'SanityPluginFramework' ) != true ) require_once $plugin_path . 'framework/sanity.php';


class ShowPHPInfoPlugin extends SanityPluginFramework {

	var $version = '0.1';
	/*
   * 	Required __construct() function that initalizes the Sanity Framework
   */
	function __construct() {
		parent::__construct( __FILE__ );
	}


	function plugin_activate() {

	}

	function plugin_initialize() {
	    add_action( "admin_menu"  , array( &$this, 'register_custom_menu_page' ) );
	}
        

	function register_custom_menu_page() {
		add_menu_page( 'PHPInfo', 'PHPInfo', 'administrator', 'display_phpinfo_admin_page',
			array( &$this, 'display_phpinfo_admin_page' ), plugins_url( 'show_php_info/images/icon.jpeg' ), 3 );
	}

	function display_phpinfo_admin_page() {
      echo $this->render("phpinfo_admin_page");
	}

}

$ShowPHPInfoPlugin = new ShowPHPInfoPlugin();
register_activation_hook( __FILE__, array( &$ShowPHPInfoPlugin, 'plugin_activate' ) );
add_action( 'init', array( &$ShowPHPInfoPlugin, 'plugin_initialize' ), 1);
?>
