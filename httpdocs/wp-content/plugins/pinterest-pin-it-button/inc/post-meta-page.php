<?php

//Adds a meta box to the main column on the Post and Page edit screens 

function pib_sharing_add_meta_box() {
	add_meta_box( 'pib_sharing_meta','"Pin It" Button Settings', 'pib_sharing_meta_box_content', 'page', 'advanced', 'high' );
	add_meta_box( 'pib_sharing_meta','"Pin It" Button Settings', 'pib_sharing_meta_box_content', 'post', 'advanced', 'high' );
}

//Renders the post/page meta box checkbox html

function pib_sharing_meta_box_content( $post ) {
	global $pib_options;

	$pib_sharing_checked = get_post_meta( $post->ID, 'pib_sharing_disabled', 1 );
	$pib_url_of_webpage = get_post_meta( $post->ID, 'pib_url_of_webpage', true);
	$pib_url_of_img = get_post_meta( $post->ID, 'pib_url_of_img', true);
	$pib_description = get_post_meta( $post->ID, 'pib_description', true);
	
	$pib_button_style = ( $pib_options['button_style'] == 'user_selects_image' ) ? 'User selects image' : 'Image pre-selected';
	?>

    <p>
        <em>Button style is inherited from setting saved in <a href="<?php echo admin_url( 'admin.php?page=' . PIB_PLUGIN_BASENAME ); ?>">"Pin It" Button Settings</a>.
		Current style: <strong><?php echo $pib_button_style; ?></strong></em>
    </p>
	<p>
		<em>These 3 text fields will be used only if the button style is: <strong>Image pre-selected</strong></em>
	</p>
	<p>
		<table class="form-table inside">
			<tr valign="top">
				<td>
					<label for="pib_url_of_webpage">URL of the web page to be pinned (defaults to current post/page URL if left blank):</label><br/>
					<input type="text" id="pib_url_of_webpage" name="pib_url_of_webpage" value="<?php echo $pib_url_of_webpage; ?>" class="widefat"/>
				</td>
			</tr>
			<tr valign="top">
				<td>
					<label for="pib_url_of_img">URL of the image to be pinned (defaults to first image in post if left blank):</label><br/>
					<input type="text" id="pib_url_of_img" name="pib_url_of_img" value="<?php echo $pib_url_of_img; ?>" class="widefat"/>
				</td>
			</tr>
			<tr valign="top">
				<td>
					<label for="pib_description">Description of the pin (defaults to post title if left blank):</label><br/>
					<input type="text" id="pib_description" name="pib_description" value="<?php echo $pib_description; ?>" class="widefat"/>
				</td>
			</tr>
		</table>
	</p>	
	
	<p>
		<input name="pib_enable_post_sharing" id="pib_enable_post_sharing" type="checkbox" value="1"
            <?php checked( empty( $pib_sharing_checked ) || ($pib_sharing_checked === false) ); ?> />
		<label for="pib_enable_post_sharing">Show "Pin It" button on this post/page.</label>
		<p class="description">
			If checked displays the button for this post/page (if <strong>Individual Posts</strong> (for posts) or <strong>WordPress Static "Pages"</strong> 
			(for pages) is also checked in <a href="<?php echo admin_url( 'admin.php?page=' . PIB_PLUGIN_BASENAME ); ?>">"Pin It" Button Settings</a>).
            If unchecked the button will <strong>always</strong> be hidden for this post/page.
		</p>
		<input type="hidden" name="pib_sharing_status_hidden" value="1" />
	</p>
		
	<?php	
}


//Saves display option for individual post/page

function pib_sharing_meta_box_save( $post_id ) {
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
		return $post_id;

	// Record sharing disable
	if ( isset( $_POST['post_type'] ) && ( 'post' == $_POST['post_type'] || 'page' == $_POST['post_type'] ) ) {
		if ( current_user_can( 'edit_post', $post_id ) ) {
			if ( isset( $_POST['pib_sharing_status_hidden'] ) ) {
				if ( !isset( $_POST['pib_enable_post_sharing'] ) ) {
					update_post_meta( $post_id, 'pib_sharing_disabled', 1 );
				}
				else {
					delete_post_meta( $post_id, 'pib_sharing_disabled' );
				}
					
				if ( isset( $_POST['pib_url_of_webpage'] ) && isset( $_POST['pib_url_of_img'] ) && isset( $_POST['pib_description'] )) {
					update_post_meta( $post_id, 'pib_url_of_webpage', $_POST['pib_url_of_webpage'] );
					update_post_meta( $post_id, 'pib_url_of_img', $_POST['pib_url_of_img'] );
					update_post_meta( $post_id, 'pib_description', $_POST['pib_description'] );
				}					
				else {
					delete_post_meta( $post_id, 'pib_url_of_webpage' );
					delete_post_meta( $post_id, 'pib_url_of_img' );
					delete_post_meta( $post_id, 'pib_description' );
				}
			}
		}
	}

	return $post_id;
}

add_action( 'save_post', 'pib_sharing_meta_box_save' );
