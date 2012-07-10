<?php
/**
 * Contains the EmailOnlineUsers class.
 * 
 * @package david-coombes
 */

/**
 * This is the main class/controller for the plugin.
 * 
 * All global method definitions and calls are found here.
 *
 * @package david-coombes
 */
class EmailOnlineUsers {

	/** @var string Holds the html from the view file for parsing */
	private $html;
	/** @var array Associative array of shortcode=>value pairs for use with the
	 view file */
	private $shortcodes;

	/**
	 * constructor
	 */
	public function __construct() {
		
		//default params
		$this->shortcodes = array();
	}

	/**
	 * Build the admin menu.
	 * 
	 * The root menu and menu's for modules are created here.
	 */
	public function admin_menu(){
		
		global $emailonline_dashboard;
		global $pfwpnm_slugs;
		global $pfwpnm_name;
		
		$this->menu = "emailonline-users";
		add_menu_page( "Email Online Users", "Email Online Users", "administrator", "emailonline-users", array(&$this, 'get_page'));
		add_submenu_page($this->menu, "Email Online Users - Message", "Message", 'read', "emailonline-users-dashboard", array($emailonline_dashboard, 'get_page'));
	}
	
	/**
	 * Ajax Method updates the users last online.
	 * 
	 * Will update the users last online in the table emailonline_user.
	 */
	public function emailonline_users_log(){
		
		global $emailOnline_tables;
		global $wpdb;
		$time = time();
		
		$wpdb->query($wpdb->prepare("
			INSERT INTO `{$emailOnline_tables->user}` (user_id,last_online)
			VALUES(%d,'{$time}')
			ON DUPLICATE KEY UPDATE `last_online`='{$time}'
			", array($_POST['user_id'])));
		
		die();
	}
	
	/**
	 * Prints the view html.
	 * 
	 * Loads the html then sets shortcodes
	 * @return void
	 */
	public function get_page() {

		$this->html = file_get_contents(EMAIL_ONLINE_DIR . "/public_html/EmailOnlineUsers.php");
		$this->set_shortcodes();
		
		$this->load_styles();

		print $this->html;
	}

	/**
	 * Get list of online users.
	 * 
	 * Will return the an array of user object with the email, id and
	 * user_nicename.
	 *
	 * @global object $emailOnline_tables
	 * @global wpdb $wpdb
	 * @return array 
	 */
	public function get_online_users(){
		
		//vars
		global $emailOnline_tables;
		global $wpdb;
		$time = time();
		$online_time = $time-20;
		
		//query db
		$res = $wpdb->get_results("
			SELECT users.ID, users.user_nicename, user_email 
			FROM `{$emailOnline_tables->user}` as emailonline,`{$wpdb->users}` as users
			WHERE emailonline.last_online >= {$online_time}
			AND users.ID = emailonline.user_id
			");
		
		return $res;
	}
	
	/**
	 * Add javascript to the &lt;head>.
	 * 
	 * If the user is logged in then javascript is added to the head to post the
	 * user id when the page first loads and every 30secs after to the method
	 * @see $this->emailonline_users_log().
	 *
	 * @global WP_User $current_user 
	 */
	public function head_javascript(){
		
		//vars
		global $current_user;
		$ajaxurl = admin_url('admin-ajax.php');
		$wpnonce = wp_create_nonce("emailonline_users_log");
		$current_user = wp_get_current_user();
		
		//if user is logged in then print global js vars
		if(is_user_logged_in() ){
			?>
			<script type="text/javascript">
				var ajaxurl = '<?=$ajaxurl?>';
				var emailonline_user_id = '<?=$current_user->ID?>';
				var emailonline_user_wpnonce = '<?=$wpnonce?>';
				
				function emailonline_user_log(){
					jQuery.post(
						ajaxurl,
						{
							'_wpnonce' : emailonline_user_wpnonce,
							'action' : 'emailonline_users_log',
							'user_id' : emailonline_user_id
						},
						function(j){
							//console.log(j);
						},
						'json'
					)
				}
				
				jQuery(document).ready(function(){
					emailonline_user_log();
					setTimeout('emailonline_user_log()', 10000);
				});
			</script>
			<?php
		}
		
		//if user is not logged print not logged in var
		else{
			?>
			<script type="text/javascript">
				var emailonline_user_not_loggedin = true;
			</script>
			<?php
		}
	}
	
	/**
	 * Loads javascript files
	 * 
	 * @return void 
	 */
	public function load_scripts() {
		wp_register_script('jquery-validate', EMAIL_ONLINE_URL . "/application/includes/jquery.validate.min.js", array('jquery'));
		wp_register_script('jquery-block-ui', EMAIL_ONLINE_URL . "/application/includes/jquery.blockUI.js", array('jquery'));
		wp_register_script("emailonline-user", EMAIL_ONLINE_URL . "/public_html/js/EmailOnlineUsers.js", array(
			'jquery',
			'jquery-validate',
			'jquery-block-ui'
		));
		
		wp_enqueue_script("emailonline-user");
	}

	/**
	 * Sends an email.
	 * 
	 * Takes an associative array for params ar['email'],ar['subject'],
	 * ar['message'].
	 *
	 * @param array $details
	 * @return boolean 
	 */
	public function send_message(array $details){
		
		if(!mail($details['email'], $details['subject'], $details['message'])) return false;
		return true;
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
	 * Replaces the codes with values in @see EmailOnlineUsers::$html . To add
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
