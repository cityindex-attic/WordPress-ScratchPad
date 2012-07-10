<?php
/**
 * Contains the Dashboard class.
 * 
 * @package david-coombes
 */

/**
 * Method definition and calls for the plugin dashboard page 'messages' goes
 * here.
 *
 * @package david-coombes
 */
class EmailOnlineUsersDashboard {

	/** @var string Holds the html from the view file for parsing */
	private $html;
	/** @var array An array of messages to print to the view file */
	private $messages;
	/** @var array Associative array of shortcode=>value pairs for use with the
	 view file */
	private $shortcodes;

	/**
	 * constructor
	 */
	public function __construct() {
		
		global $emailonline_action;		//is set from plugin index.php file
		
		//default params
		$this->messages = array();
		$this->shortcodes = array();
		
		//check for actions
		if(!empty($emailonline_action))
			if(method_exists($this, $emailonline_action))
				add_action('admin_init', array(&$this, $emailonline_action));
					
		//call methods only when on this page in the dashboard
		if(@$_GET['page']=='emailonline-users-dashboard')
			add_action('admin_init', array(&$this, 'init'));
		
		
	}

	/**
	 * Prints the view html.
	 * 
	 * Loads the html then sets shortcodes ( @see EmailOnlineUsersDashboard::set_shortcodes() )
	 * then loads scripts (@see EmailOnlineUsersDashboard::load_scripts() ) and styles
	 * (@see EmailOnlineUsersDashboard::load_styles() ) then prints html
	 * @return void
	 */
	public function get_page() {

		$this->html = file_get_contents(EMAIL_ONLINE_DIR . "/public_html/EmailOnlineUsersDashboard.php");
		$this->set_shortcodes();

		$this->load_scripts();
		$this->load_styles();

		print $this->html;
	}

	/**
	 * Admin init callback.
	 * 
	 * Called in __construct, only called if on this admin dashboard page.
	 */
	public function init(){
		
		$this->shortcodes['users online select'] = $this->get_users_select();
		$this->shortcodes['nonce send message'] = wp_create_nonce("send message");
		$this->shortcodes['get messages'] = $this->get_messages();
	}
	
	/**
	 * Sends emails to users.
	 * 
	 * Handles sending of emails from the form in this view file.
	 *
	 * @global EmailOnlineUsers $emailonline
	 * @return boolean 
	 */
	public function send_message(){
		
		if(!wp_verify_nonce($_REQUEST['_wpnonce'], 'send message')) return false;
		
		global $emailonline;
		if($emailonline->send_message($_POST)) $this->messages[] = "Message Sent";
		else $this->messages[] = "Error sending message";
		
		return true;
	}
	
	/**
	 * Builds up list of messages.
	 * 
	 * Builds up an &lt;ul> list of messages for display on the view file. Uses
	 * $this->messages array to build list.
	 *
	 * @return mixed Returns html on success or false on fail. 
	 */
	private function get_messages(){
		
		if(!count($this->messages)) return false;
		
		$html = "<div id=\"message-1\" class=\"updated\"><ul>\n";
		foreach($this->messages as $msg)
			$html .= "<li>{$msg}</li>\n";
			
		return $html."</ul>\n</div>\n";
	}
	
	/**
	 * Get &lt;option> list of current users online.
	 * 
	 * Checks the current timestamp against the table emailonline_users for a
	 * list of users online within the past 30 secs.
	 *
	 * @global EmailOnlineUsers $emailonline
	 * @return string 
	 */
	private function get_users_select(){
		
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
		;
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
