<?php
 /** 
* Class implements plugin's work "social essentials buttons" in particular its activation, deactivation and HTML code generation. Plugin "social essentials buttons" places buttons  at the top of each post/page.
*
* @author Best Web Soft <bestwebsoft.com>
* @version 1.0
*/

class social_essentials
{
	/**
   * Plugin title is displayed in admin section at the page of plugin options
   * 
   * @var string
   */
	protected $page_title;							
	/**
   * Menu title of plugin in admin section 
   * 
   * @var string
   */
	protected $menu_title;							
	/**
   * Access level for plugin 
   * 
   * @var int
   */
	protected $access_level;						
	
	/**
   * Where in admin panel a link to plugin is displayed
   * 
   * @var int
   */
	protected $add_page_to;						
	
	/**
	* Plugin's description which is displayed below the title in admin section at the page of plugin's options.
	* 
	* @var string
	*/
	protected $short_description;						
	
	/**
	* Site admin twitter name
	* 
	* @var string
	*/
	protected $twitter_username; 			
	
	/**
	* Facebook API key
	* 
	* @var string
	*/
	protected $feb_app_id = '';
  	
	/**
	* Enable or disable "Call to action text"
	* 
	* @var bool
	*/
    protected $call_to_action;
	
	/**
	* Style of call to action text bold
	* 
	* @var bool
	*/
    protected $call_to_action_text_style_bold;
	
	/**
	* Style of call to action text italic
	* 
	* @var bool
	*/
    protected $call_to_action_text_style_italic;
	
	/**
	* Style of call to action text underline
	* 
	* @var bool
	*/
    protected $call_to_action_text_style_underline;
	
	/**
	* Hex color ['#000000']
	* 
	* @var string
	*/
    protected $text_call_to_action_color;
	
	/**
	* Contains the name of html tag ['h1','h2', ...]
	* 
	* @var string
	*/
    protected $call_to_action_text_size;
	
	/**
	* Position of call to actiob text ['left', 'right']
	* 
	* @var string
	*/
    protected $call_to_action_position;
	
	/**
	* Call to action text
	* 
	* @var string
	*/
    protected $call_to_action_text;
	
	/**
	* Buttons icons aligment ['left', 'right', 'center', 'float left', 'float right']
	* 
	* @var string
	*/
    protected $icon_aligment;
	
	/**
	* Buttons icon size ['small', 'large']
	* 
	* @var string
	*/
    protected $icon_size;
	
	/**
	* Cutom css field
	* 
	* @var string
	*/
    protected $custom_css;
	
	/**
	* Button names separated by comma
	* 
	* @var string
	*/
    protected $buttons_order;	
	
	/**
	* ULR to arrow image
	* 
	* @var string
	*/
    protected $text_call_to_action_arrow;	
	
	/**
	* Contains section of page where should be buttons placed
	* 
	* @var array
	*/
	protected $display = array();
	
	/**
	* Content button names in items keys
	* 
	* @var array
	*/
    protected $show_buttons = array();
	
	/**
	* Count of RSS feed posts in sidebar
	* 
	* @var int
	*/
    protected $count_posts = 5;		
	
	/**
	* Method initialize object of a class with variables from wp-option table
	*    
	* @return void
	*/
	function social_essentials()
	{			
				
		if (get_option('se_show_twitter'))   	$this->show_buttons['twitter']   = get_option('se_show_twitter');
		if (get_option('se_show_fb_like'))   	$this->show_buttons['fb_like']   = get_option('se_show_fb_like');
		if (get_option('se_show_fb_share'))  	$this->show_buttons['fb_share']  = get_option('se_show_fb_share');
		if (get_option('se_show_google'))    	$this->show_buttons['google'] 	  = get_option('se_show_google');
		if (get_option('se_show_pinterest')) 	$this->show_buttons['pinterest'] = get_option('se_show_pinterest');
		if (get_option('se_show_stumbleupon')) 	$this->show_buttons['stumbleupon'] = get_option('se_show_stumbleupon');
		
		if (get_option('se_display_above_posts'))  $this->display['above_posts']   = get_option('se_display_above_posts');
		if (get_option('se_display_below_posts'))  $this->display['below_posts']   = get_option('se_display_below_posts');
		if (get_option('se_display_above_pages'))  $this->display['above_pages']   = get_option('se_display_above_pages');
		if (get_option('se_display_below_pages'))  $this->display['below_pages']   = get_option('se_display_below_pages');
		if (get_option('se_display_above_home'))   $this->display['above_home']   = get_option('se_display_above_home');
		if (get_option('se_display_below_home'))   $this->display['below_home']   = get_option('se_display_below_home');
		
		$this->icon_size 					= get_option('se_icon_size');
		$this->icon_aligment 				= get_option('se_icon_aligment');
		$this->call_to_action_text 			= get_option('se_call_to_action_text');
		$this->call_to_action_position 		= get_option('se_call_to_action_position');
		$this->call_to_action_text_size 	= get_option('se_call_to_action_text_size');
		$this->text_call_to_action_color 	= get_option('se_text_call_to_action_color');
		$this->call_to_action_text_style_bold 	= get_option('se_call_to_action_text_style_bold');
		$this->call_to_action_text_style_italic 	= get_option('se_call_to_action_text_style_italic');
		$this->call_to_action_text_style_underline 	= get_option('se_call_to_action_text_style_underline');
		$this->call_to_action 				= get_option('se_call_to_action');				
		$this->text_call_to_action_arrow	= get_option('se_text_call_to_action_arrow');
		
		$this->twitter_username 			= get_option('se_settings_twitter_username');
		$this->feb_app_id 					= get_option('se_settings_fb_app_id');
		$this->custom_css 					= get_option('se_custom_css');
		$this->buttons_order 				= get_option('se_buttons_order');
		
		add_action( 'wp_ajax_se_preview', array(&$this, 'preview_buttons' ));		
		add_action( 'wp_ajax_se_stats', array(&$this, 'get_stats_table' ));		
		add_action( 'wp_ajax_se_stats_top', array(&$this, 'get_stats_top_table' ));		
		add_action( 'wp_ajax_se_stats_last_top', array(&$this, 'get_stats_top_table' ));		
		add_action( 'wp_ajax_se_stats_last', array(&$this, 'get_stats_top_table' ));		
		add_filter( 'the_content', array(&$this, 'generate_buttons' ));						
		add_action( 'update_stats', array(&$this, 'update_stats'));
	}	
	
	/**
	* Method which defines what section in admin panel loads a link for plugin settings
	* 
	* @return void
	*/
	function add_admin_menu()
	{				
		add_menu_page($this->page_title, $this->menu_title, $this->access_level, basename(__FILE__), array(&$this, 'stats_page'));
		
		add_submenu_page(basename(__FILE__), "Setup - Social Essentials", "Setup", "activate_plugins", 'Setup', array(&$this, 'admin_page'));					
	}
		
	/**
	* This method check current plugin options and generates  html for share buttons
	* @param string[required] $content post content
	* @param string[optional] $preview url for share with social buttons
	* @param array[optional] $options plugin settings
	* @return string html for share buttons
	*/
	function generate_buttons($content, $preview = false, $options = array())
	{
		if (!$preview) // if we not in preview mode (admin section)
			global $post;

		if (!empty($options))	
		{
			foreach ($options as $option => $value)
			{
				$this->{$option} = $value; // initialize options	
			}
		}
			
		$html = '<div id="social-essentials" class="se_'.$this->icon_aligment.'">';
		
		$plink   = (!$preview) ? get_permalink($post->ID) : $preview;
		$eplink  = (!$preview) ? urlencode($plink) : urlencode($preview);
		$ptitle  = (!$preview) ? get_the_title($post->ID) : $this->page_title;
		$eptitle = (!$preview) ? str_replace(array(">","<"), "", $ptitle) : $this->page_title;
		
		if (!empty($this->show_buttons) && (!empty($this->display) || $preview)) // If display options are enabled or in preview mode then prepare html for social buttons
		{
			foreach (explode(',', $this->buttons_order) as $button) 
			{
				if (!empty($this->show_buttons[trim($button)]))
				{
					
					switch (trim($button))
					{												
						case 'twitter': // prepare twitter button html code
							
							$layout = $this->icon_size == 'small' ? 'horizontal' : 'vertical' ;
							
							$html .= '<div class="se_button" style="width:'.(($this->icon_size == 'large') ? '65' : '80').'px"><a href="https://twitter.com/share" class="twitter-share-button" data-url="'.$plink.'" data-text="'.$eptitle.'" data-via="'.$this->twitter_username.'" data-counturl="'.$plink.'" data-count="'.$layout.'">Tweet</a><script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script></div>';
							
							break;
						case 'fb_like': // prepare facebook like button html code
							
							$layout = $this->icon_size == 'small' ? 'button_count' : 'box_count' ;							
							
							$html .= '<div class="se_button"><iframe src="//www.facebook.com/plugins/like.php?href='.$plink.'&amp;send=false&amp;layout='.$layout.'&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font" scrolling="no" frameborder="0" allowTransparency="true" style="width:'.(($this->icon_size == 'small') ? '72' : '44').'px; height:'.(($this->icon_size == 'small') ? '40' : '65').'px;"></iframe></div>';
														
							break;
						case 'fb_share': // prepare facebook share button html code
							
							if ($this->icon_size == "large" )
							{
								$tp  = "box_count";
								
							} else $tp= "button_count";							
							
							$html .= '<div class="se_button" '.(($this->icon_size == 'small') ? 'style="position:relative; top:-4px;"' : '').'><a name="fb_share" type="'.$tp.'" expr:share_url="'.$plink.'" href="http://www.facebook.com/sharer.php">Share</a><script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript"></script></div>';							
																		
							break;
						case 'google': // prepare google +1 button
							
							if ($this->icon_size == "large" ) $tp="tall"; else $tp="small";																										
							$classSize = $this->icon_size == 'large' ? 'size-box' : 'size-small';
							
							$html .= '<div class="se_button" style="width:50px"><g:plusone size="'.$tp.'" href="'.$plink.'" count="true"></g:plusone></div>';

							$html .= '<script type="text/javascript">
								(function() {
									var po = document.createElement("script"); po.type = "text/javascript"; po.async = true;
									po.src = "https://apis.google.com/js/plusone.js";
									var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(po, s);
								})();
								</script>';
								
							break;
						case 'pinterest': // prepare pinterest button	

							$classSize = $this->icon_size == 'small' ? 'horizontal' : 'vertical' ;

							$html .= '<a href="http://pinterest.com/pin/create/button/?url='.$plink.'" class="pin-it-button" count-layout="'.$classSize.'">Pin It</a><script type="text/javascript" src="http://assets.pinterest.com/js/pinit.js"></script>';

							break;
						case 'stumbleupon': // prepare stumbleupon button
							
							$classSize = $this->icon_size == 'small' ? 1 : 5 ;
							$dim 	   = $this->icon_size == 'small' ? 'width:100px; height: 30px;' : 'width:70px; height: 60px;';
														
							$html .= '<div class="se_button"><su:badge layout="'.$classSize.'"></su:badge></div>
							<!-- Place this snippet wherever appropriate --> 
							 <script type="text/javascript"> 
							 (function() { 
								 var li = document.createElement(\'script\'); li.type = \'text/javascript\'; li.async = true; 
								 li.src = window.location.protocol + \'//platform.stumbleupon.com/1/widgets.js\'; 
								 var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(li, s); 
							 })(); 
							 </script>';

							break;
					}
				}
			}						
			
			if ($this->call_to_action)
			{											
				$classes = array();
				if (!empty($this->call_to_action_text_style_bold)) array_push($classes, 'se_bold');
				if (!empty($this->call_to_action_text_style_italic)) array_push($classes, 'se_italic');
				if (!empty($this->call_to_action_text_style_underline)) array_push($classes, 'se_underline');
				
				switch ($this->call_to_action_position)
				{					
					
					case 'left':
						$call_to_action_html = '<'.$this->call_to_action_text_size.' class="'.implode(' ', $classes).'" style="color:'.$this->text_call_to_action_color.'; float:left;">'.$this->call_to_action_text.'</'.$this->call_to_action_text_size.'>';
						$html = '<div id="call_to_action" style="float:left;">'.$call_to_action_html.((!empty($this->text_call_to_action_arrow)) ? '<img src="'.$this->text_call_to_action_arrow.'"/></div>' : '</div>').$html;
						break;
					case 'right':
						$call_to_action_html = '<'.$this->call_to_action_text_size.' class="'.implode(' ', $classes).'" style="color:'.$this->text_call_to_action_color.'; float:right;">'.$this->call_to_action_text.'</'.$this->call_to_action_text_size.'>';
						$html .= ((!empty($this->text_call_to_action_arrow)) ? '<div id="call_to_action"><img src="'.$this->text_call_to_action_arrow.'"/>' : '<div id="call_to_action">').$call_to_action_html.'</div>';
						break;
				}
			}
			
			$html .= '</div>';
			
			if ($this->icon_aligment == 'left' || $this->icon_aligment == 'right' || $this->icon_aligment == 'center') $html .= '<div class="clear"></div>';
			
			if (!$preview) // if in frontent screen
			{
				foreach ($this->display as $key => $display)
				{				
					switch ($key)
					{
						case 'above_posts':
							if ($post->post_type == 'post' && !is_home())  $content = $html.$content;
							break;
						case 'below_posts':
							if ($post->post_type == 'post' && !is_home()) $content .= $html;
							break;
						case 'above_page':
							if ($post->post_type == 'page' && !is_home()) $content = $html.$content;
							break;
						case 'below_page':
							if ($post->post_type == 'page' && !is_home()) $content .= $html;
							break;
						case 'above_home':
							if (is_home()) $content = $content.$html;
							break;
						case 'below_home':
							if (is_home()) $content .= $html;
							break;					
					}
				}
			}
			else $content .= $html;
			
			if ($this->custom_css)
			{				
				$content .= '<style type="text/css">'.$this->custom_css.'</style>';
			}
		}									
		
		return $content;
	}		
	
}
?>