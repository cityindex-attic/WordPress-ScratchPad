<?php

//Register shortcode: [pinit url="" image_url="" description="" float="none" remove_div="false" always_show_count="false"]

function pib_button_shortcode_html( $attr ) {
	global $pib_options;
	global $post;

    /*
        For URL, image URL and Description, use in order:
        1) attribute value
        2) custom fields for post
        3) inherit from post: permalink, first image, post title
    */

    $url = $attr['url'];
    
    if ( empty( $url ) ) {
        $url = get_post_meta( $post->ID, 'pib_url_of_webpage', true);
        if ( empty( $url ) ) {
            $url = get_permalink( $post->ID );
        }
    }
    $attr['url'] = $url;

    $image_url = $attr['image_url'];
    
    if ( empty( $image_url ) ) {
        $image_url = get_post_meta( $post->ID, 'pib_url_of_img', true);
        if ( empty( $image_url ) ) {
            //Get url of img and compare width and height
            $output = preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches );
            $first_img = $matches [1] [0];        
            $image_url = $first_img;
        }
    }
    $attr['image_url'] = $image_url;
    
    $description = $attr['description'];
    
    if ( empty( $description ) ) {
        $description = get_post_meta( $post->ID, 'pib_description', true);
        if ( empty( $description ) ) {
            $description = get_the_title( $post->ID );
        }
    }
    $attr['description'] = $description;    
    
	$attr['count'] = ( empty( $attr['count'] ) ? 'none' : $attr['count'] );
	$remove_div_bool = ( $attr['remove_div'] == 'true' );
	$always_show_count_bool = ( $attr['always_show_count'] == 'true' );

	$baseBtn = pib_button_base( $attr['url'], $attr['image_url'], $attr['description'], $attr['count'], $always_show_count_bool );

	if ( $remove_div_bool ) {
		return $baseBtn;
	}
	else {
		//Surround with div tag
		$float_class = '';
		
		if ( $attr['float'] == 'left' ) {
			$float_class = 'pib-float-left';
		}
		elseif ( $attr['float'] == 'right' ) {
			$float_class = 'pib-float-right';
		}
	
		return '<div class="pin-it-btn-wrapper-shortcode ' . $float_class . '">' . $baseBtn . '</div>';
	}
}

add_shortcode( 'pinit', 'pib_button_shortcode_html' );
