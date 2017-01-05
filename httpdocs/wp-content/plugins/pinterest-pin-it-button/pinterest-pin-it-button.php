<?php
/*
  Plugin Name: Pinterest "Pin It" Button
  Plugin URI: http://pinterestplugin.com
  Description: Add a Pinterest "Pin It" Button to your posts and pages allowing your readers easily pin your images. Includes shortcode and widget.
  Author: Phil Derksen
  Author URI: http://pinterestplugin.com
  Version: 1.3.1
  License: GPLv2
  Copyright 2012 Phil Derksen (phil@pinterestplugin.com)
*/

/***************************
* Global Constants
***************************/

define( 'PIB_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

$pib_options = get_option( 'pib_options' );

/***************************
* Includes
***************************/

require_once( 'inc/admin-settings.php' );
require_once( 'inc/widget.php' );
require_once( 'inc/shortcode.php' );
require_once( 'inc/post-meta-page.php' );
require_once( 'inc/category-meta-page.php' );

//Plugin install/activation

function pib_install() {
    global $pib_options;
    
	//Deactivate plugin if WP version too low
    if ( version_compare( get_bloginfo( 'version' ), '3.0', '<' ) ) {
        deactivate_plugins( basename( __FILE__ ) );
    }
    
	//Setup default options for values that don't exist and need to be set to 1/true/value (not 0/false/blank)
    //Done this way to preseve options saved in previous versions
    if ( !isset( $pib_options['display_home_page'] ) ) { $pib_options['display_home_page'] = 1; }
    if ( !isset( $pib_options['display_posts'] ) ) { $pib_options['display_posts'] = 1; }
    if ( !isset( $pib_options['display_pages'] ) ) { $pib_options['display_pages'] = 1; }
    if ( !isset( $pib_options['display_below_content'] ) ) { $pib_options['display_below_content'] = 1; }
    if ( !isset( $pib_options['button_style'] ) ) { $pib_options['button_style'] = 'user_selects_image'; }
    if ( !isset( $pib_options['count_layout'] ) ) { $pib_options['count_layout'] = 'none'; }
    
	//Save default option values
	update_option( 'pib_options', $pib_options );
}

register_activation_hook( __FILE__, 'pib_install' );

//Debugging

function pib_debug_print( $value ) {
    print_r( '<br/><br/>' );
	print_r( $value );
}

//Register settings

function pib_register_settings() {
	register_setting( 'pib-settings-group', 'pib_options' );
}

// Tie into WordPress Hooks and any functions that should run on load

add_action( 'admin_init', 'pib_sharing_add_meta_box' );
add_action( 'admin_init', 'pib_register_settings' );

//Add settings page to admin menu
//Use $page variable to load CSS/JS ONLY for this plugin's admin page

function pib_create_menu() {
	$capability = 'manage_options';
	
	//add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
    $page = add_menu_page( 'Pin It Button Settings', 'Pin It Button', $capability, __FILE__, 'pib_create_settings_page', 
        plugins_url( '/img/pinterest-button-icon-small.png', __FILE__ ) );
    
	add_action( 'admin_print_styles-' . $page, 'pib_add_admin_css_js' );
}

add_action( 'admin_menu', 'pib_create_menu' );

//Add Admin CSS/JS

function pib_add_admin_css_js() {
	wp_enqueue_script( 'jquery' );
	
	wp_enqueue_style( 'pinterest-pin-it-button', plugins_url( '/css/pinterest-pin-it-button-admin.css' , __FILE__ ) );
    wp_enqueue_script( 'pinterest-pin-it-button', plugins_url( '/js/pinterest-pin-it-button-admin.js', __FILE__ ), array( 'jquery' ) );
}

//Add first-install pointer CSS/JS & functionality

function pib_add_admin_css_js_pointer() {
	wp_enqueue_style( 'wp-pointer' );
    wp_enqueue_script( 'wp-pointer' );
	
    add_action( 'admin_print_footer_scripts', 'pib_admin_print_footer_scripts' );
}

add_action( 'admin_enqueue_scripts', 'pib_add_admin_css_js_pointer' );

//Add pointer popup message when plugin first installed

function pib_admin_print_footer_scripts() {
    //Check option to hide pointer after initial display
    if ( !get_option( 'pib_hide_pointer' ) ) {
        $pointer_content = '<h3>Ready to be Pinned?</h3>';
        $pointer_content .= '<p>Congratulations. You have just installed the Pinterest "Pin It" Button Plugin. ' .
            'Now just configure your settings and start getting Pinned!</p>';
         
        $url = admin_url( 'admin.php?page=' . PIB_PLUGIN_BASENAME );
        
        ?>

        <script type="text/javascript">
            //<![CDATA[
            jQuery(document).ready( function($) {
                $("#menu-plugins").pointer({
                    content: '<?php echo $pointer_content; ?>',
                    buttons: function( event, t ) {
                        button = $('<a id="pointer-close" class="button-secondary">Close</a>');
                        button.bind("click.pointer", function() {
                            t.element.pointer("close");
                        });
                        return button;
                    },
                    position: "left",
                    close: function() { }
            
                }).pointer("open");
              
                $("#pointer-close").after('<a id="pointer-primary" class="button-primary" style="margin-right: 5px;" href="<?php echo $url; ?>">' + 
                    'Pin It Button Settings');
            });
            //]]>
        </script>

        <?php
        
        //Update option so this pointer is never seen again
        update_option( 'pib_hide_pointer', 1 );
	}
}

//Render rss items from pinterestplugin.com
//http://codex.wordpress.org/Function_Reference/fetch_feed

function pib_rss_news() {
	// Get RSS Feed(s)
	include_once(ABSPATH . WPINC . '/feed.php');

	// Get a SimplePie feed object from the specified feed source.
	$rss = fetch_feed('http://pinterestplugin.com/feed/');
	
	if (!is_wp_error( $rss ) ) {
		// Checks that the object is created correctly 
		// Figure out how many total items there are, but limit it to 5. 
		$maxitems = $rss->get_item_quantity(3); 

		// Build an array of all the items, starting with element 0 (first element).
		$rss_items = $rss->get_items(0, $maxitems); 
	}
	
	?>

	<ul>
		<?php if ($maxitems == 0): ?>
			<li><?php _e( 'No items.', 'pib' ); ?></li>
		<?php else: ?>
			<?php
			// Loop through each feed item and display each item as a hyperlink.
			foreach ( $rss_items as $item ): ?>
				<li>
					&raquo; <a href="<?php echo esc_url( $item->get_permalink() ); ?>" target="_blank" class="external">
						<?php echo esc_html( $item->get_title() ); ?></a>
				</li>
			<?php endforeach; ?>
		<?php endif; ?>
	</ul>
	
	<?php
}

//Render Facebook Share button
//http://developers.facebook.com/docs/share/

function pib_share_facebook() {
	?>	
	<a name="fb_share" type="button" share_url="http://pinterestplugin.com/" alt="Share on Facebook"></a> 
	<script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript"></script>	
	<?php
}

//Render Twitter button
//https://twitter.com/about/resources/buttons

function pib_share_twitter() {
	?>
    <a href="https://twitter.com/share" class="twitter-share-button" data-url="http://pinterestplugin.com" data-text="I'm using the Pinterest &quot;Pin It&quot; Button Plugin for WordPress. It rocks!" data-count="none">Tweet</a>
    <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>	
	<?php
}

//Render Pin It button
//Render in iFrame otherwise it messes up the WP admin left menu

function pib_share_pinterest() {
	?>
	<a href="http://pinterest.com/pin/create/button/?url=http%3A%2F%2Fpinterestplugin.com%2F&media=http%3A%2F%2Fpinterestplugin.com%2Fimg%2Fpinterest-pin-it-button-plugin-for-wordpress.png&description=Add%20a%20Simple%20and%20Flexible%20%22Pin%20It%22%20Button%20to%20Your%20WordPress%20Site%20--%20http%3A%2F%2Fpinterestplugin.com%2F" class="pin-it-button" count-layout="horizontal"><img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" /></a>
	<script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script>
	<?php
}

//Add Settings link to the left of Deactivate on plugins list page

function pib_plugin_settings_link( $links ) {
	$url = admin_url( 'admin.php?page=' . PIB_PLUGIN_BASENAME );
	$settings_link = '<a href="' . $url . '">Settings</a>';
	array_unshift( $links, $settings_link );
	return $links;
}
 
add_filter('plugin_action_links_' . PIB_PLUGIN_BASENAME, 'pib_plugin_settings_link' );

//Add Public CSS/JS (to Header)

function pib_add_public_css_js() {
	wp_enqueue_style( 'pinterest_pin_it_button', plugins_url( '/css/pinterest-pin-it-button.css' , __FILE__ ) );
}

add_action( 'wp_enqueue_scripts', 'pib_add_public_css_js' );

//Add Public JS (to Footer)

function pib_add_public_js_footer() {
    global $pib_options;
	
	// If option #1 selected (user selects image)
	if ( ( $pib_options['button_style'] == 'user_selects_image' ) || empty( $pib_options['button_style'] ) ) {
        //Save iFrame URL to JS variable
        echo '<script type="text/javascript">' .
            'var iFrameBtnUrl = "' . plugins_url( '/inc/pin-it-button-user-selects-image-iframe.html', __FILE__ ) . '"; ' .
            '</script>' . "\n";
        
        echo '<script type="text/javascript" src="' . plugins_url( '/js/pin-it-button-user-selects-image.js', __FILE__ ) . '"></script>' . "\n";
        echo '<script type="text/javascript" src="' . plugins_url( '/js/pin-it-button-user-selects-image-assets.js', __FILE__ ) . '"></script>' . "\n";
    }
	// If option #2 selected (image pre-selected)
	elseif ( $pib_options['button_style'] == 'image_selected' ) {
        echo '<script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script>' . "\n";
	}
}

add_action( 'wp_footer', 'pib_add_public_js_footer' );

//Add Custom CSS

function pib_add_custom_css() {
    global $pib_options;
    
    $custom_css = trim( $pib_options['custom_css'] );
    
	if ( $custom_css != '' ) {
        echo "\n" . '<style type="text/css">' . "\n" . $custom_css . "\n" . '</style>' . "\n";
	}
}

add_action( 'wp_head', 'pib_add_custom_css' );

//Function for rendering "Pin It" button base html

function pib_button_base( $postUrl, $imageUrl, $description, $countLayout ) {
    global $pib_options;

    //Change css class for "user selects image" button style to avoid conflicts
    $buttonClass = 'pin-it-button';
    
	if ( $pib_options['button_style'] == 'user_selects_image' ) {
        $buttonClass = 'pin-it-button2';
    }
    
    //HTML from Pinterest Goodies 3/19/2012
    //<a href="http://pinterest.com/pin/create/button/?url=PAGE&media=IMG&description=DESC" class="pin-it-button" count-layout="horizontal">
    //<img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" /></a>
    
    $btn = '<a href="http://pinterest.com/pin/create/button/?url=' . urlencode( $postUrl ) . 
        '&media=' . urlencode( $imageUrl ) . '&description='. urlencode( $description ) . '" ' .
        'count-layout="' . $countLayout . '" class="' . $buttonClass . '">' . 
        '<img border="0" style="border:0;" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" /></a>';
    
    return $btn;
}

//Button html to render

function pib_button_html( $postID ) {	
    global $pib_options;
	global $post;

	$pib_url_of_webpage = get_post_meta( $postID, 'pib_url_of_webpage', true );
	$pib_url_of_img = get_post_meta( $postID, 'pib_url_of_img', true );
	$pib_description = get_post_meta( $postID, 'pib_description', true );
	
	$countLayout = $pib_options['count_layout'];
	$permalink = get_permalink( $postID );
	$title = get_the_title( $postID );
	$first_img = '';
	 
	//Get url of img and compare width and height
	$output = preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches );
	$first_img = $matches [1] [0];
    
    //Determine if button displayed on current page from single post settings
	if ( get_post_meta( $postID, 'pib_sharing_disabled', 1 ) ) {			
		return "";
	}
	else {	
		//if url_of_webpage, url_of_img or description are not set through pinit admin setting page then set default to post/page URL for the attribute url
        $pib_url_of_webpage = ( empty( $pib_url_of_webpage ) ? $permalink : $pib_url_of_webpage );
        $pib_url_of_img = ( empty( $pib_url_of_img ) ? $first_img : $pib_url_of_img );
        $pib_description = ( empty( $pib_description ) ? $title : $pib_description );
        
        $baseBtn = pib_button_base( $pib_url_of_webpage, $pib_url_of_img, $pib_description, $countLayout );
        
		if ( $pib_options['remove_div'] ) {
            return $baseBtn;
        }
		else {
            //Surround with div tag
            return '<div class="pin-it-btn-wrapper">' . $baseBtn . '</div>';
		}
    }
}

//Render button on pages with regular content

function pib_render_btn( $content ) {
    global $pib_options;
 	global $post;
    
	$postID = $post->ID;

    //Determine if button displayed on current page from main admin settings
    if (
        ( is_home() && ( $pib_options['display_home_page'] ) ) ||
        ( is_front_page() && ( $pib_options['display_front_page'] ) ) ||
		( is_single() && ( $pib_options['display_posts'] ) ) ||
        ( is_page() && ( $pib_options['display_pages'] ) && !is_front_page() ) ||
        
        //archive pages besides categories (tag, author, date, search)
        //http://codex.wordpress.org/Conditional_Tags
        ( is_archive() && ( $pib_options['display_archives'] ) && 
            ( is_tag() || is_author() || is_date() || is_search() ) 
        )
       ) {
        if ( $pib_options['display_above_content'] ) {
            $content = pib_button_html( $postID ) . $content;
        }
        if ( $pib_options['display_below_content'] ) {
            $content .= pib_button_html( $postID );
        }
    }	
	 	
	//Determine if displayed on Category on the base of category edit Screen Option
	if ( is_archive() && ( $pib_options['display_archives'] ) ) {
		$tag_extra_fields = get_option(PIB_CATEGORY_FIELDS);
		$category_ids = get_all_category_ids();
		foreach($category_ids as $term_id) {
					 
			if($tag_extra_fields[$term_id]['checkbox'] != true) {
						
				if(is_category($term_id)) {	
					if ( $pib_options['display_above_content'] ) {
						$content = pib_button_html($postID) . $content;
					}
							
					if ( $pib_options['display_below_content'] ) {
						$content .= pib_button_html($postID);
					}
				}
			}				
		}
	}
		
	return $content;
}

add_filter( 'the_content', 'pib_render_btn' );

//Render button on pages with excerpts if option checked

function pib_render_btn_excerpt( $content ) {
    global $pib_options;
    global $post;
	$postID = $post->ID;
	
    if ( $pib_options['display_on_post_excerpts'] ) {
        if (
            ( is_home() && ( $pib_options['display_home_page'] ) ) ||
            ( is_front_page() && ( $pib_options['display_front_page'] ) ) 
           
           ) {
            if ( $pib_options['display_above_content'] ) {
                $content = pib_button_html($postID) . $content;
            }
            if ( $pib_options['display_below_content'] ) {
                $content .= pib_button_html($postID);
            }
        }   
	
		//Determine if displayed on Category on the base of category edit Screen Option
		if( is_archive() && ( $pib_options['display_archives'] ) ) {
				
            $tag_extra_fields = get_option(PIB_CATEGORY_FIELDS);
            $category_ids = get_all_category_ids();
            foreach($category_ids as $term_id) {
                     
                if($tag_extra_fields[$term_id]['checkbox'] != true) {
                            
                    
                    if(is_category($term_id)) {	
                        if ( $pib_options['display_above_content'] ) {
                            $content = pib_button_html($postID) . $content;
                        }
                                
                        if ( $pib_options['display_below_content'] ) {
                            $content .= pib_button_html($postID);
                        }
                    }
                }
            }
		}
	}
	return $content;
}

add_filter( 'the_excerpt', 'pib_render_btn_excerpt' );
