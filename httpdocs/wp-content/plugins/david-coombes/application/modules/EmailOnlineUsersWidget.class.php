<?php
/**
 * Contains the class for the Widgets module.
 * 
 * @package david-coombes
 */

/**
 * This class has all the method definition and class for the widget module.
 *
 * @package david-coombes
 */
class EmailOnlineUsersWidget extends WP_Widget {

	/** @var string Holds the html from the view file for parsing */
	private $html;
	/** @var array An associative array of code=>value pairs for the view file*/
	private $shortcodes;
	
	/**
	 * constructor
	 */
	function __construct() {
		
		//default params
		$this->shortcodes = array();
		
		$widget_ops = array('classname' => 'EmailOnlineUsersWidget', 'description' => 'Send email to online users');
		$this->WP_Widget('EmailOnlineUsersWidget', 'Email Online Users', $widget_ops);
	}

	/**
	 * Wordpress widget method.
	 *
	 * @param array $args
	 * @param array $instance 
	 */
	public function widget($args, $instance) {
		
		extract($args, EXTR_SKIP);
		
		//if user not logged in then return;
		if(!is_user_logged_in()) return false;

		//view file shortcodes
		$this->shortcodes['users select'] = $this->get_online_users();
		
		//echo widget html
		echo $before_widget;
		$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);

		if (!empty($title))
			echo $before_title . $title . $after_title;;

		$this->get_page();

		echo $after_widget;
		return true;
	}

	/**
	 * Prints the view html.
	 * 
	 * Loads the html then sets shortcodes
	 * 
	 * @return void
	 */
	public function get_page() {

		$this->html = file_get_contents( EMAIL_ONLINE_DIR . "/public_html/EmailOnlineUsersWidget.php");
		$this->set_shortcodes();

		$this->load_scripts();
		$this->load_styles();

		print $this->html;
	}

	/**
	 * Build list of online users.
	 * 
	 * Returns the &lt;option> list of users currently online.
	 *
	 * @global EmailOnlineUsers $emailonline
	 * @return string 
	 */
	private function get_online_users(){
		
		global $emailonline;
		
		$html = "<option class=\"required\">Please select user</option>\n";
		$res = $emailonline->get_online_users();
		
		if(!count($res)) $html = "<option>no users online<option>\n";
		else
			foreach($res as $user)
				$html .= "<option value=\"{$user->user_email}\">{$user->user_nicename}</option>\n";
				
		return $html;
	}
	
	/**
	 * Loads javascript files
	 * 
	 * @return void 
	 */
	private function load_scripts() {
		;
	}

	/**
	 * Loads css files
	 * 
	 * @return void 
	 */
	private function load_styles() {
		
		wp_register_style("emailonline-widget", EMAIL_ONLINE_URL . "/public_html/css/EmailOnlineUsersWidget.css");
		wp_enqueue_style("emailonline-widget");
	}

	/**
	 * Sets values for the shortcodes in the view file.
	 * 
	 * Replaces the codes with values in @see FSNetworkRegister::$html . To add
	 * shortcodes to the view file use the syntax:
	 * <code> <!--[--identifying string--]--> </code>. In the construct of this
	 * class add the value to the array @see FSNetworkRegister::$shortcodes.
	 * eg: $this->shortcodes['identifying string'] = $this->method_returns_html()
	 * 
	 * @return void
	 */
	private function set_shortcodes() {
		foreach ($this->shortcodes as $code => $val)
			$this->html = str_replace("<!--[--{$code}--]-->", $val, $this->html);
	}

}

?>
