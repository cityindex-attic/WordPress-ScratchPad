<?php
/*
Plugin name: Tools Demo
Plugin URI: http://www.agileinfoways.com/
Description: A widget to display title,url on your site
Version: 1.0.0
Author: Romesh Gupta
Author URI: http://www.agileinfoways.com
*/
?>
<?php 
/*Apply Css*/

define( "TOOLS_DEMO_URL", WP_PLUGIN_URL . '/ToolsDemo/');
define( "TOOLS_DEMO_VERSION", "1.0.0");

add_action( 'admin_init', 'tools_demo_admin_init' );

function tools_demo_admin_init() {
wp_enqueue_style('tools-demo-styles', TOOLS_DEMO_URL.'tooldemo.css', false, TOOLS_DEMO_VERSION);

?>
 
<?php
}

/*Install Datebase */
	function tooldemo_install() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'tooldemo';
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			$sql =  "CREATE TABLE `$table_name` (
					`tool_id` int(11) unsigned NOT NULL auto_increment,
					`title` varchar(250) character set utf8 NOT NULL,
					`url` varchar(250) character set utf8 NOT NULL,
					PRIMARY KEY  (`tool_id`)
				)";
		   $results = $wpdb->query($sql);
		}
	}
register_activation_hook(__FILE__,'tooldemo_install');

/* Admin Page Redirect */
if(is_admin())
	include 'tooldemo_admin.php';
	
/*Widget Box display*/

class tooldemo extends WP_Widget {
	
	function tooldemo() {
		$widget_ops = array('classname' => 'tooldemo');
		$this->WP_Widget('tooldemo', 'Tool Demos', $widget_ops);
	}
	
	function widget($args, $instance) {
		extract($args);
		$title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title']);
		$snippet = $this->get_tooldemo();
		if($snippet!='') {
			echo $before_widget;
			if($title)
				echo $before_title.$title.$after_title;
				echo $after_widget;
		}
	}
	
	function get_tooldemo() {
		global $wpdb, $tooldemo_adminurl;
		$table_name = $wpdb->prefix.'tooldemo';
		$pageURL = $tooldemo_adminurl;
		$where = '';
		// now load the data to display
		 $rows = $wpdb->get_results("SELECT * FROM $table_name $where ORDER BY tool_id");
		foreach($rows as $row) {
		echo '<div style="font-weight:bold;float:left;margin-right:10px;">Title:</div>'.$row->title."<br/>";
		echo '<div style="font-weight:bold;float:left;margin-right:10px;">URL:</div>'.$row->url ."<hr/>";
		}
	}
}
	function tooldemo_init() {
  		register_widget('tooldemo');
	}
add_action('widgets_init', 'tooldemo_init');
	
// Shortcode implementation
function tooldemo_shortcode($attribs) {
	extract(shortcode_atts(array( ), $attribs));
	
	$tooldemo = new tooldemo;
	return $tooldemo->get_tooldemo();
}
add_shortcode('tooldemo', 'tooldemo_shortcode');  	

?>