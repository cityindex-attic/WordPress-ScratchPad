<?php

//Create settings page

function pib_create_settings_page() {
	global $pib_options;
    $custom_css = trim( $pib_options['custom_css'] );
	?>
    
    <div class="wrap">        
        <a href="http://pinterestplugin.com/" target="_blank"><div id="pinterest-button-icon-32" class="icon32"
            style="background: url(<?php echo plugins_url( '/img/pinterest-button-icon-med.png', dirname( __FILE__ ) ); ?>) no-repeat;"></div></a>
        <h2><?php _e( 'Pinterest "Pin It" Button Settings', 'pib' ); ?></h2>
        
        <div id="poststuff" class="metabox-holder has-right-sidebar">

			<!-- Fixed right sidebar like WP post edit screen -->
			<div id="side-info-column" class="inner-sidebar">
				<div id="side-sortables" class="meta-box-sortables ui-sortable">
					<div class="pib-admin-banner">
						<a href="http://pinterestplugin.com/ad-tpp-from-pib" target="_blank">
							<img src="http://cdn.pinterestplugin.com/img/top-pinned-posts-ad-01.jpg" alt="Top Pinned Posts Pinterest Plugin for WordPress"></img>
						</a>
					</div>
                    
					<div class="postbox">
						<div class="handlediv pib-handlediv" title="Click to toggle"><br /></div>
						<h3 class="hndle pib-hndle"><?php _e( 'Spread the Word', 'pib' ); ?></h3>
						
						<div class="inside">
                            <p><?php _e( 'Like this plugin? A share would be awesome!', 'pib' ); ?></p>
							
							<table id="share_plugin_buttons">
								<tr>
									<td><?php echo pib_share_twitter(); ?></td>
									<td><?php echo pib_share_pinterest(); ?></td>
									<td><?php echo pib_share_facebook(); ?></td>
								</tr>
							</table>
                            
                            <p>
                                &raquo; <a href="http://wordpress.org/extend/plugins/pinterest-pin-it-button/" target="_blank" class="external">
									<?php _e( 'Rate it on WordPress', 'pib' ); ?></a>
                            </p>
						</div>
					</div>

					<div class="postbox">
						<div class="handlediv pib-handlediv" title="Click to toggle"><br /></div>
						<h3 class="hndle pib-hndle"><?php _e( 'Plugin Support', 'tpp' ); ?></h3>
						
						<div class="inside">
							<p>
								&raquo; <a href="http://pinterestplugin.com/support-pin-it-button" target="_blank" class="external">
								<?php _e( 'Support & Knowledge Base', 'pib' ); ?></a>
							</p>
							<p>
								<?php _e( 'Email support provided to licensed users only.', 'pib' ); ?>
							</p>
							<p>
								&raquo; <strong><a href="http://pinterestplugin.com/buy-support-pin-it-button" target="_blank" class="external">
								<?php _e( 'See Support Pricing', 'pib' ); ?></a></strong>
							</p>							
						</div>
					</div>
					
					<div class="postbox">
						<div class="handlediv pib-handlediv" title="Click to toggle"><br /></div>
						<h3 class="hndle pib-hndle"><?php _e( 'More Pinterest Plugins', 'pib' ); ?></h3>
						
						<div class="inside">
							<ul>
								<li>&raquo; <a href="http://pinterestplugin.com/top-pinned posts" target="_blank" class="external">Top Pinned Posts</a></li>
								<li>&raquo; <a href="http://pinterestplugin.com/follow-button" target="_blank" class="external">"Follow" Button</a></li>
								<li>&raquo; <a href="http://pinterestplugin.com/pinterest-block" target="_blank" class="external">Pinterest Block</a></li>
							</ul>
						</div>
					</div>
					
					<div class="postbox">
						<div class="handlediv pib-handlediv" title="Click to toggle"><br /></div>
						<h3 class="hndle pib-hndle"><?php _e( 'Pinterest Plugin News', 'pib' ); ?></h3>
						
						<div class="inside">
							<? echo pib_rss_news(); ?>
						</div>
					</div>

					<div class="postbox">
						<div class="handlediv pib-handlediv" title="Click to toggle"><br /></div>
						<h3 class="hndle pib-hndle"><?php _e( 'Subscribe by Email', 'pib' ); ?></h3>
						
						<div class="inside">
							<p><?php _e( 'Want to know when new Pinterest plugins and features are released?', 'pib' ); ?></p>
							&raquo; <strong><a href="http://pinterestplugin.com/newsletter-from-plugin" target="_blank" class="external">
								<?php _e( 'Get Updates', 'pib' ); ?></a></strong>
						</div>
					</div>
				</div>
            </div>
			
			<div id="post-body">
				<div id="post-body-content">
					<div class="meta-box-sortables ui-sortable">
						<?php settings_errors(); //Display status messages after action ("settings saved", errors) ?>
					
						<form method="post" action="options.php">
							<?php settings_fields( 'pib-settings-group' ); ?>							
							
							<div id="button-type" class="postbox pib-postbox">
								<div class="handlediv pib-handlediv" title="Click to toggle"><br /></div>								
								<h3 class="hndle pib-hndle"><?php _e( 'Button Style', 'pib' ); ?></h3>

								<table class="form-table inside">
									<tr valign="top">
										<td>
											<?php _e( 'What style of button would you like to use?', 'pib' ); ?>
										</td>
									</tr>
									<tr valign="top">
										<td>
											<input type="radio" id="user_selects_image" value="user_selects_image" name="pib_options[button_style]"
												<?php checked( ( $pib_options['button_style'] == 'user_selects_image' ) || empty( $pib_options['button_style'] ) ); ?> />
											<label for="display_pinit_button"><strong>User selects image</strong> from popup</label>
										</td>
									</tr>
									<tr valign="top">
										<td>
											<input type="radio" id="image_selected" value="image_selected" name="pib_options[button_style]" 
												<?php checked( $pib_options['button_style'], 'image_selected' ); ?> />
											<label for="display_pinit_count_button">Image is <strong>pre-selected</strong></label>
										</td>
									</tr>
									<tr valign="top">
										<td class="pib-pad-cell-top">
											<label for="pib_pin_count" class="pib-plain-label"><?php _e( 'Pin Count:' ); ?></label>
											<select id="count_layout" name="pib_options[count_layout]">
												<option value="none" <?php selected( ( $pib_options['count_layout'] == 'none' ) || empty( $pib_options['count_layout'] ) ); ?>><?php _e( 'No Count' ); ?></option>
												<option value="horizontal" <?php selected( $pib_options['count_layout'], 'horizontal' ); ?>><?php _e( 'Horizontal' ); ?></option>
												<option value="vertical" <?php selected( $pib_options['count_layout'], 'vertical' ); ?>><?php _e('Vertical'); ?></option>
											</select>
										</td>
									</tr>
									<tr valign="top">
										<td>
											<input id="always_show_count" name="pib_options[always_show_count]" type="checkbox" <?php if ( $pib_options['always_show_count'] ) echo 'checked="checked"'; ?> />
											<label for="always_show_count">Always show pin count (currently works on vertical pin count only)</label>
										</td>
									</tr>								
									<tr valign="top">
										<td class="pib-pad-cell-top">
											To specify the website address (URL) to pin, image to pin and/or pin description (other than the defaults), go to the edit screen for those posts and pages
											and scroll to the bottom.
										</td>
									</tr>
									<tr valign="top">
										<td>
											The button style setting applies to <strong>ALL</strong> "Pin It" buttons displayed on the website (widgets and shortcodes included).
										</td>
									</tr>                                
								</table>
								
							</div>
							
							<div id="pib-options" class="postbox pib-postbox">
								<div class="handlediv pib-handlediv" title="Click to toggle"><br /></div>						
								<h3 class="hndle pib-hndle"><?php _e( 'Button Visibility', 'pib' ); ?></h3>
								
								<table class="form-table inside">
									<tr valign="top">
										<td>
											<?php _e( 'What types of pages should the button appear on?', 'pib' ); ?>
										</td>
									</tr>
									<tr valign="top">
										<td>
											<input id="display_home_page" name="pib_options[display_home_page]" type="checkbox" value="1"
												<?php checked( (bool)$pib_options['display_home_page'] ); ?> />
											<label for="display_home_page">Blog Home Page (or Latest Posts Page)</label>
										</td>
									</tr>
									<tr valign="top">
										<td>
											<input id="display_front_page" name="pib_options[display_front_page]" type="checkbox" value="1"
												<?php checked( (bool)$pib_options['display_front_page'] ); ?> />
											<label for="display_front_page">Front Page (different from Home Page only if set in Settings > Reading)</label>
										</td>
									</tr>					
									<tr valign="top">
										<td>
											<input id="display_posts" name="pib_options[display_posts]" type="checkbox" value="1"
												<?php checked( (bool)$pib_options['display_posts'] ); ?> />
											<label for="display_posts">Individual Posts</label>
										</td>
									</tr>
									<tr valign="top">
										<td>
											<input id="display_pages" name="pib_options[display_pages]" type="checkbox" value="1"
												<?php checked( (bool)$pib_options['display_pages'] ); ?> />
											<label for="display_pages">WordPress Static "Pages"</label>
										</td>
									</tr>
									<tr valign="top">
										<td>
											<input id="display_archives" name="pib_options[display_archives]" type="checkbox" value="1"
												<?php checked( (bool)$pib_options['display_archives'] ); ?> />
											<label for="display_archives">Archives (includes Category, Tag, Author and date-based pages)</label>
										</td>
									</tr>
									<tr valign="top">
										<td class="pib-pad-cell-top">
											To hide the "Pin It" button for a specific post, page or category, go to the edit screen for that post, page or category,
											scroll down to the bottom, and uncheck the "Show" checkbox.
										</td>
									</tr>
									<tr valign="top">
										<td>
											Go to Appearance &rarr; <a href="<?php echo admin_url( 'widgets.php' ); ?>">Widgets</a> to add a "Pin It" button to your sidebar.
										</td>
									</tr>
								</table>
							</div>
								
							<div id="button-show" class="postbox pib-postbox">
								<div class="handlediv pib-handlediv" title="Click to toggle"><br /></div>								
								<h3 class="hndle pib-hndle"><?php _e( 'Button Placement', 'pib' ); ?></h3>
								
								<table class="form-table inside">
									<tr valign="top">
										<td>
											<?php _e( 'Where on each page should the button appear?', 'pib' ); ?>
										</td>
									</tr>
									<tr valign="top">
										<td>
											<input id="display_above_content" name="pib_options[display_above_content]" type="checkbox" value="1"
												<?php checked( (bool)$pib_options['display_above_content'] ); ?> />
											<label for="display_above_content">Above Content</label>
										</td>
									</tr>
									<tr valign="top">
										<td>
											<input id="display_below_content" name="pib_options[display_below_content]" type="checkbox" value="1"
												<?php checked( (bool)$pib_options['display_below_content'] ); ?> />
											<label for="display_below_content">Below Content</label>
										</td>
									</tr>
									<tr valign="top">
										<td>
											<input id="display_on_post_excerpts" name="pib_options[display_on_post_excerpts]" type="checkbox" value="1"
												<?php checked( (bool)$pib_options['display_on_post_excerpts'] ); ?> />
											<label for="display_on_post_excerpts">On Post Excerpts</label>
										</td>
									</tr>
									<tr valign="top">
										<td>
											Currently, only the button style <strong>"image is pre-selected"</strong> will use the individual post URL when a visitor
											pins on a post excerpt.
										</td>
									</tr>								
								</table>
							</div>
							
							<div class="submit-settings">
								<input name="submit" type="submit" value="Save Settings" class="button-primary" />
							</div>
								
							<div id="style-options" class="postbox pib-postbox">                         
								<div class="handlediv pib-handlediv" title="Click to toggle"><br /></div>								
								<h3 class="hndle pib-hndle"><?php _e( 'Advanced Styling & CSS', 'pib' ); ?></h3>
								
								<table class="form-table inside">
									<tr valign="top">
										<td>
											<label for="custom_css">Custom CSS</label>
										</td>
									</tr>
									<tr valign="top">
										<td>
											<textarea id="pib-custom-css" name="pib_options[custom_css]" rows="6"><?php echo $custom_css; ?></textarea>
									   </td>
									</tr>
									<tr valign="top">
										<td>
											<input id="remove_div" name="pib_options[remove_div]" type="checkbox" value="1"
												<?php checked( (bool)$pib_options['remove_div'] ); ?> />
											<label for="remove_div">Remove div tag surrounding regular button: <code><?php echo htmlentities('<div class="pin-it-btn-wrapper"></div>'); ?></code></label>
										</td>
									</tr>
									
									<tr valign="top">
										<td>
											<a href="http://pinterestplugin.com/pin-it-button-custom-css/" target="_blank" class="external">
												See custom CSS examples</a> aligning the button with other social sharing plugins.
										</td>
									</tr>
								</table>
							</div>
							
							<div class="postbox pib-postbox">
								<div class="handlediv pib-handlediv" title="Click to toggle"><br /></div>
								<h3 class="hndle pib-hndle"><?php _e( 'Shortcode Instructions', 'pib' ); ?></h3>
								
								<div class="inside">
									<p>
										Use the shortcode <code>[pinit]</code> to display the button within your content.
									</p>
									<p>
										Use the function <code><?php echo htmlentities('<?php echo do_shortcode(\'[pinit]\'); ?>'); ?></code>
										to display within template or theme files.
									</p>
									<p><strong>Shortcode parameters</strong></p>
									<p>
										- count: none (default), horizontal, vertical<br/>
										- url: URL of the web page to be pinned (defaults to current post/page URL, but <em>must specify</em> if on home or index page)<br/>
										- image_url: URL of the image to be pinned (defaults to first image in post)<br/>
										- description: description of the pin (defaults to post title)<br/>
										- float: none (default), left, right<br/>
										- remove_div: false (default), true -- if true removes surrounding div tag 
											(<code><?php echo htmlentities('<div class="pin-it-btn-wrapper-shortcode"></div>'); ?></code>), which also removes float setting<br/>
										- always_show_count: false (default), true -- if true will show count even if zero
									</p>
									<p><strong>Examples</strong></p>
									<p>
										<code>[pinit count="horizontal"]</code><br/>
										<code>[pinit count="vertical" url="http://www.mysite.com" image_url="http://www.mysite.com/myimage.jpg" 
											description="My favorite image!" float="right"]</code><br/>
									</p>
								</div>
							</div>
							
							<div class="submit-settings">
								<input name="submit" type="submit" value="Save Settings" class="button-primary" />
							</div>
							
						</form>
					</div>
				</div>
			</div>            
        </div>
    </div>
    
    <?php
}
