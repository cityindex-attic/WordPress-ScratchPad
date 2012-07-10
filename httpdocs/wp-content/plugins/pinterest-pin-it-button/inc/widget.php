<?php
//Add Pinterest Pin It Button widget

class Pib_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'pib-clearfix', 'description' => __( 'Add a Pinterest "Pin It" button to your sidebar with this widget.') );
		$control_ops = array('width' => 400);  //doesn't use height
		parent::__construct('pib_button', __('Pinterest "Pin It" Button'), $widget_ops, $control_ops);
	}

	function widget( $args, $instance ) {
		extract($args);
		
		$title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);		
		$pib_url_of_webpage_widget = $instance['pib_url_of_webpage_widget'];
		$pib_url_of_img_widget = $instance['pib_url_of_img_widget'];		
		$pib_description_widget = $instance['pib_description_widget'];
		$count_layout = empty( $instance['count_layout'] ) ? 'none' : $instance['count_layout'];
		$float = empty( $instance['float'] ) ? 'none' : $instance['float'];
        $pib_remove_div = (bool)$instance['remove_div'];
        $pib_always_show_count = (bool)$instance['always_show_count'];
        
		$baseBtn = pib_button_base( $pib_url_of_webpage_widget, $pib_url_of_img_widget, $pib_description_widget, $count_layout, $pib_always_show_count );
		
		echo $before_widget;
        
		if ( $title ) {
			echo $before_title . $title . $after_title;
        }
		
		if ( $pib_remove_div ) {
			echo $baseBtn;
		}
		else {
			//Surround with div tag
			$float_class = '';
			
			if ( $float == 'left' ) {
				$float_class = 'pib-float-left';
			}
			elseif ( $float == 'right' ) {
				$float_class = 'pib-float-right';
			}
		
			echo '<div class="pin-it-btn-wrapper-widget ' . $float_class . '">' . $baseBtn . '</div>';
		}
		
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$new_instance = wp_parse_args( (array) $new_instance, array( 'count_layout' => 'none', 'title' => '', 
			'pib_count_button_radio' => 'user_selects_image', 'float' => 'none') );
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['pib_url_of_webpage_widget'] = strip_tags($new_instance['pib_url_of_webpage_widget']);
		$instance['pib_url_of_img_widget'] = strip_tags($new_instance['pib_url_of_img_widget']);
		$instance['pib_description_widget'] = strip_tags($new_instance['pib_description_widget']);		
		$instance['count_layout'] = $new_instance['count_layout'];
		$instance['float'] = $new_instance['float'];
        $instance['remove_div'] = ( $new_instance['remove_div'] ? 1 : 0 );
        $instance['always_show_count'] = ( $new_instance['always_show_count'] ? 1 : 0 );
        
		return $instance;
	}

	function form( $instance ) {
        global $pib_options;
        
		$instance = wp_parse_args( (array) $instance, array( 'count_layout' => 'none', 'title' => '', 
		'pib_count_button_radio' => 'user_selects_image', 'float' => 'none') );
		$title = strip_tags($instance['title']);
		$pib_url_of_webpage_widget = strip_tags($instance['pib_url_of_webpage_widget']);
		$pib_url_of_img_widget = strip_tags($instance['pib_url_of_img_widget']);
		$pib_description_widget = strip_tags($instance['pib_description_widget']);
		$pib_button_style_widget = ( $pib_options['button_style'] == 'user_selects_image' ) ? 'User selects image' : 'Image pre-selected';		
		?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title (optional):'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('count_layout'); ?>"><?php _e('Pin Count:'); ?></label> 
			<select name="<?php echo $this->get_field_name('count_layout'); ?>" id="<?php echo $this->get_field_id('count_layout'); ?>">
				<option value="none" <?php selected( $instance['count_layout'], 'none' ); ?>><?php _e('No Count'); ?></option>
				<option value="horizontal" <?php selected( $instance['count_layout'], 'horizontal' ); ?>><?php _e('Horizontal'); ?></option>
				<option value="vertical" <?php selected( $instance['count_layout'], 'vertical' ); ?>><?php _e('Vertical'); ?></option>
			</select>
		</p>
		<p>
			<input class="checkbox" <?php checked($instance['always_show_count'], true) ?> id="<?php echo $this->get_field_id('always_show_count'); ?>" name="<?php echo $this->get_field_name('always_show_count'); ?>" type="checkbox"/>
			<label for="<?php echo $this->get_field_id('always_show_count'); ?>">Always show pin count (even when zero)</label>
		</p>
		<div class="pib-widget-text-fields">
            <p>
                <em>Button style is inherited from setting saved in <a href="<?php echo admin_url( 'admin.php?page=' . PIB_PLUGIN_BASENAME ); ?>">"Pin It" Button Settings</a>.
				Current style: <strong><?php echo $pib_button_style_widget; ?></strong></em>
            </p>
			<p>
				<em>These 3 text fields will be used only if the button style is <strong>"Image pre-selected"</strong></em>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('pib_url_of_webpage_widget'); ?>"><?php _e('URL of the web page to be pinned (<strong>required</strong>):'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('pib_url_of_webpage_widget'); ?>" name="<?php echo $this->get_field_name('pib_url_of_webpage_widget'); ?>" type="text" value="<?php echo esc_attr($pib_url_of_webpage_widget); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('pib_url_of_img_widget'); ?>"><?php _e('URL of the image to be pinned (<strong>required</strong>):'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('pib_url_of_img_widget'); ?>" name="<?php echo $this->get_field_name('pib_url_of_img_widget'); ?>" type="text" value="<?php echo esc_attr($pib_url_of_img_widget); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('pib_description_widget'); ?>"><?php _e('Description of the pin (optional):'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('pib_description_widget'); ?>" name="<?php echo $this->get_field_name('pib_description_widget'); ?>" type="text" value="<?php echo esc_attr($pib_description_widget); ?>" />
			</p>
		</div>
		
		<p>
			<label for="<?php echo $this->get_field_id('float'); ?>"><?php _e('Align (float):'); ?></label> 
			<select name="<?php echo $this->get_field_name('float'); ?>" id="<?php echo $this->get_field_id('float'); ?>">
				<option value="none" <?php selected( $instance['float'], 'none' ); ?>><?php _e('none (default)'); ?></option>
				<option value="left" <?php selected( $instance['float'], 'left' ); ?>><?php _e('left'); ?></option>
				<option value="right" <?php selected( $instance['float'], 'right' ); ?>><?php _e('right'); ?></option>
			</select>
		</p>
		<p>
			<input class="checkbox" <?php checked($instance['remove_div'], true) ?> id="<?php echo $this->get_field_id('remove_div'); ?>" name="<?php echo $this->get_field_name('remove_div'); ?>" type="checkbox"/>
			<label for="<?php echo $this->get_field_id('remove_div'); ?>">Remove div tag surrounding this widget button (also removes <strong>float</strong> setting)</label>
		</p>
        <?php
	}
}


// Function that register Pin It Button widget. 

function pib_load_widgets() {
	register_widget( 'Pib_Widget' );
}

//Add function to the widgets_init hook. 
add_action( 'widgets_init', 'pib_load_widgets' );
