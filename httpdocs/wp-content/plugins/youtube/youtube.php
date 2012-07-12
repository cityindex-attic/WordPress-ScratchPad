<?php
/*
Plugin Name: Youtube integration
Plugin URI: 
Description: Plugin for youtube movies
Author: Piotr Karbownik
Version: 3.3.4
Author URI: 
*/

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');

// Load the widget on widgets_init

add_action('init', 'youtube_register');
add_action("admin_init", "admin_init");
add_action('save_post', 'save_details');
add_action("manage_posts_custom_column", "youtube_columns_values");
add_filter("manage_edit-youtube_columns", "youtube_columns");
add_action('wp_footer', 'youtubeForm');
add_action('wp_ajax_nopriv_addMovie', 'addMovie');
//YOUTUBE plugin functions******************************************************
function addMovie(){
    $post = array(
        'post_title' => $_POST["title"],
        'post_status' => 'draft', 
        'post_type' => 'youtube', 
        'post_author' => $_POST["user"]
        );
    $post_id = wp_insert_post($post);
    add_post_meta($post_id, 'description',$_POST["description"], true);
    add_post_meta($post_id, 'url', $_POST["url"], true);
    add_post_meta($post_id, 'accepted', $_POST["url"], true);
    if($post_id!=NULL && $post_id>0){
        echo "Thank you for posting your YouTube Video. Please wait for the video to be reviewed - Thanks";
    }
    else{
        echo "Error. Could not insert data. Please try again later.";
    }
    die();
}
function youtubeForm(){
    global $current_user;
    get_currentuserinfo();
    ?>
    <a href="#TB_inline?height=360&width=420&inlineId=youtubeWrapper&modal=false" id="youtube-popup-anchor" class="thickbox" style="display: none;" >Show</a>
    <div id="youtubeWrapper" style="display: none;">
    <div id="youtubeForm" style="background: #FFFFFF;">
        <h2>Upload movie from YouTube</h2>
        <?php if($_COOKIE["shopL"]==1){?>
        <form action="" method="post">
            
            <label for="youtubeTitle">Title <span class="req">*</span></label>
            <input id="youtubeTitle" type="text" value="" name="title"/>
           
            <label for="youtubeUrl">Url <span class="req">*</span></label>
            <input id="youtubeUrl" type="text" value="" name="url"/>
            
            <label for="youtubeDesc">Description</label>
            <textarea id="youtubeDesc" name="description"/></textarea>
            <input type="hidden" value="<?php echo $current_user->ID; ?>" name="user_id" id="user_id" />
            
            <input style="outline: none; padding-left: 30px;background: url(<?php echo home_url();?>/wp-admin/images/wpspin_light.gif) no-repeat -100px 8px #999999; " type="submit" id="uploadMovie" value="Add movie" name=""/>
        </form>
        
        <div id="result"></div>
        <?php }else{ ?> <div id="resultLogin" style="color:#FF0000; font-weight: bold;">Please login to upload videos</div>
       
        <div class="dark float-left" style="margin-right:5px;margin-top:10px;">
        <fb:login-button class=" fb_login_not_logged_in FB_login_button" onlogin="FBConnect.redirect_home()" background="dark" size="large">
        <a id="RES_ID_fb_login" class="fbconnect_login_button">
        <img id="RES_ID_fb_login_image" src="http://static.ak.fbcdn.net/rsrc.php/v2/yg/r/di8GQ4yWYmF.gif" alt="Connect">
        </a>
        </fb:login-button>
        </div>
     
        <div class="float-left" style="margin-right:5px;margin-top:10px;">
        <a href="<?php echo home_url();?>/?stc_oauth_start=1&stcaction=login&loc=<?php echo home_url();?>%2Fwp-login.php%3Fredirect_to%3D<?php echo home_url();?>%252F%26reauth%3D1&type=authenticate">
            <img border="0" src="<?php echo home_url();?>/wp-content/plugins/simple-twitter-connect/images/Sign-in-with-Twitter-lighter.png">
        </a>
        </div>
        <div class="float-left" style="margin-right:5px;margin-top:10px;">
        <a class="link-middle" style="height: 15px;font-size:11px;padding: 5px;" href="<?php echo home_url();?>/shop/customer/account/login/">
           Login from shop
        </a>
        </div>
        <div class="clear-both"></div>
        <?php }?>
    </div>
    </div>
    <?php
}
//YOUTUBE post type*************************************************************
function youtube_register() {
 
	$labels = array(
		'name' => _x('Youtube', 'post type general name'),
		'singular_name' => _x('Youtube movie', 'post type singular name'),
		'add_new' => _x('Add New', 'portfolio item'),
		'add_new_item' => __('Add New Youtube video'),
		'edit_item' => __('Edit Youtube Item'),
		'new_item' => __('New Youtube Item'),
		'view_item' => __('View Youtube Item'),
		'search_items' => __('Search Youtube items'),
		'not_found' =>  __('Nothing found'),
		'not_found_in_trash' => __('Nothing found in Trash'),
		'parent_item_colon' => ''
	);
 
	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'query_var' => true,
		'menu_icon' =>  plugin_dir_url().'/youtube/youtube16.gif',
		'rewrite' => true,
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array('title','editor','thumbnail')
	  ); 
 
	register_post_type( 'youtube' , $args );
}


 
function admin_init(){
  add_meta_box("description","Description", "movieDescription", "youtube", "normal", "low");
  add_meta_box("url", "Url", "movieUrl", "youtube", "normal", "low");
  add_meta_box("accepted","Accepted","movieAccepted", "youtube", "normal", "low");
}

function movieDescription(){
  global $post;
  $custom = get_post_custom($post->ID);
  $description = $custom["description"][0];
  ?>
  <textarea cols="50" rows="5" name="description"><?php echo $description; ?></textarea>
  <?php
}
function movieUrl(){
  global $post;
  $custom = get_post_custom($post->ID);
  $url = $custom["url"][0];
  ?>
  <label>Url(example of valid url: http://www.youtube.com/watch?v=cRRcr26lX30):</label>
  <input name="url" value="<?php echo $url; ?>" />
  <?php 
  $movie = explode('?v=',$url);
   $movie = explode('&',$movie[1]);
   $movie = $movie[0];
    if($url!=""){
        ?>
  <div>
      <iframe width="500" height="300" src="http://www.youtube.com/embed/<?php echo $movie; ?>" frameborder="0" allowfullscreen></iframe>
  </div>
    <?php
    }
}
function movieAccepted(){
  global $post;
  $custom = get_post_custom($post->ID);
  $accepted = $custom["accepted"][0];
  ?>
  <label>Accepted:</label>
  <select name="accepted">
      <option value="1"<?php if($accepted==1){echo "selected='selected'";}?>>Yes</option>
      <option value="0" <?php if($accepted!=1){echo "selected='selected'";}?>>No</option>
  </select>
  <?php
}
function save_details(){
  global $post;
  update_post_meta($post->ID, "accepted", $_POST["accepted"]);
  update_post_meta($post->ID, "url", $_POST["url"]);
  update_post_meta($post->ID, "description", $_POST["description"]);
}


 
function youtube_columns($columns) //this function display the columns headings
{
	$columns = array(
                "cb" => "<input type=\"checkbox\" />",
		"title" => "Title",
		"url" => "Url",
                "user" => "User",
                "accepted" => "Accepted"
	);
	return $columns;
}
 
function youtube_columns_values($column)
{
	global $post;
        $author_id=$post->post_author;
        $custom = get_post_custom();
	if ("url" == $column){
            echo "<a href='".$custom["url"][0]."'>".$custom["url"][0]."</a>";
        }
        elseif("user" == $column){
            echo the_author_meta( 'user_nicename' , $author_id );
        }
	elseif ("accepted" == $column) {
            if($custom["accepted"][0]==1){
                echo "<span style='color: #00FF00; font-weight: bold;'>Yes</span>";
            }
            else{
                echo "<span style='color: #FF0000; font-weight: bold;'>No</span>";
            }
        }
}