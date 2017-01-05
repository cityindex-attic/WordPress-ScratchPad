<?php

/*
Plugin Name:  Wordpress Post Sync
Plugin URI:   http://nouri/
Description:  Performs post synchronization with PhpBB. Need some configuration parameter
			  in wp-config.php (All are required).
	            define('PHPBB_DB', ''); // PhpBB Databse Name
     			define('PHPBB_DB_USER', ''); // User Name
				define('PHPBB_DB_USER_PASSWORD', ''); // Password
				define('PHPBB_DB_HOST', 'localhost'); // Host Name
				define('PHPBB_SUBMITTER_USER_ID', '76'); // Post to be associated with this user. This id can be found from PhpBB database.
				define('PHPBB_SUBMITTER_USER_NAME', 'BluWave Airlines Newsdesk'); // User name of that user.
				define('PHPBB_FORUM_ID', '2'); // Forum id in which the post would be submitted.
			  If you face any problem, just de-activate the plugin and contact me at ahasan20@gmail.com.
Version:      0.0.1
Author:       Shaikh A. Haque
Author URI:   http://nouri/
*/

function published_post_sync($post_ID)  {
   $post = get_post($post_ID);
	
   $topic_poster = PHPBB_SUBMITTER_USER_ID;
   $poster_name = PHPBB_SUBMITTER_USER_NAME;
   $forum_id = PHPBB_FORUM_ID;
   
   global $wpdb;
   $phpbb_post_id = $wpdb->get_var("SELECT phpbb_post_id from post_map where post_id = $post_ID");

   $username = PHPBB_DB_USER;
   $password = PHPBB_DB_USER_PASSWORD;
   $database = PHPBB_DB;
   
   mysql_connect(PHPBB_DB_HOST, $username, $password);
   @mysql_select_db($database); // or die( "Unable to select database");
   
   include ABSPATH . "/wp-content/plugins/post-sync/parser.php";
   $parser = new parser();
   $bbtext = "";
   try {
   		$bbtext = $parser->html_to_bb($post->post_content);
   } catch (Exception $ex) {
   		$bbtext = $post->post_content;
   }
   
   mysql_query( "SET CHARACTER SET utf8");
   
   if ($phpbb_post_id) {
   		$query = "select * from phpbb_posts where topic_id = $phpbb_post_id";
   		$result = mysql_query($query);
		$num = mysql_numrows($result);
		if ($num > 0) {
			$post_title = $post->post_title; 
			$post_desc = $bbtext; //$post->post_content;  
			$message_md5 = md5($post_desc);
			$query = $wpdb->prepare("update phpbb_posts set post_subject = %s, post_text = %s, post_checksum = '$message_md5' where topic_id = $phpbb_post_id", $post_title, $post_desc);
			mysql_query($query);
			$query = $wpdb->prepare("update phpbb_topics set topic_title = %s, topic_last_post_subject = %s where topic_id = $phpbb_post_id", $post_title, $post_title);
			mysql_query($query);
			return;
		} else {
			$query = "delete from post_map where phpbb_post_id = $phpbb_post_id";
   			mysql_query($query);			
		}
	}
   
	$current_time = time();
	$ip = $_SERVER['REMOTE_ADDR'];
	
	$post_title = $post->post_title; 
	$post_desc = $bbtext; //$post->post_content;  
	$message_md5 = md5($post_desc);
	
	$query = $wpdb->prepare("INSERT INTO phpbb_topics  
	(topic_poster, topic_time, topic_last_view_time, forum_id, icon_id, topic_approved, topic_title, 
	topic_first_poster_name, topic_first_poster_colour, topic_type, topic_time_limit, topic_attachment) 
	VALUES ($topic_poster, $current_time, $current_time, $forum_id, 0, 1, %s, %s, 'AA0000', 0, 0, 0)", $post_title, $poster_name);
	mysql_query($query);
	
	$topic_id = mysql_insert_id();
	
	$query = "INSERT INTO post_map (post_id, phpbb_post_id) values ($post_ID, $topic_id)";
	$wpdb->query($query);
	
	$query = $wpdb->prepare("INSERT INTO phpbb_posts  (forum_id, poster_id, icon_id, poster_ip, post_time, post_approved, enable_bbcode, enable_smilies, 
	enable_magic_url, enable_sig, post_username, post_subject, post_text, post_checksum, post_attachment, bbcode_bitfield, 
	bbcode_uid, post_postcount, post_edit_locked, topic_id) 
	VALUES ($forum_id, $topic_poster, 0, '$ip', $current_time, 1, 1, 1, 1, 1, '', %s, %s, 
	'$message_md5', 0, 'GA==', '5m0wto18', 1, 0, $topic_id)", $post_title, $post_desc);
	mysql_query($query);
	
	$post_id = mysql_insert_id();
	
	$query = $wpdb->prepare("UPDATE phpbb_topics
	SET topic_first_post_id = $post_id, topic_last_post_id = $post_id, topic_last_post_time = $current_time, topic_last_poster_id = $topic_poster, 
	topic_last_poster_name = %s, topic_last_poster_colour = 'AA0000', topic_last_post_subject = %s
	WHERE topic_id = $topic_id", $poster_name, $post_title);
	mysql_query($query);

	$query = "UPDATE phpbb_config SET config_value = config_value + 1 WHERE config_name = 'num_topics'";
	mysql_query($query);

	$query = "UPDATE phpbb_config SET config_value = config_value + 1 WHERE config_name = 'num_posts'";
	mysql_query($query);
	
	$query = "UPDATE phpbb_users SET user_lastpost_time = $current_time, user_posts = user_posts + 1 WHERE user_id = $topic_poster";
	mysql_query($query);
	
	$query = $wpdb->prepare("UPDATE phpbb_forums SET forum_posts = forum_posts + 1, forum_topics_real = forum_topics_real + 1, 
	forum_topics = forum_topics + 1, forum_last_post_id = $post_id, forum_last_post_subject = %s, 
	forum_last_post_time = $current_time, forum_last_poster_id = $topic_poster, forum_last_poster_name = %s, 
	forum_last_poster_colour = 'AA0000' WHERE forum_id = $forum_id", $post_title, $poster_name);
	mysql_query($query);
}

add_action('publish_post', 'published_post_sync');

?>
