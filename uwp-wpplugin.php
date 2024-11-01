<?php
/**
 * Plugin Name: 	  UnitedWed ProConnect
 * Version:           1.0.0
 * Description: 	  This plugin allows you to manage your portfolio and banner photo from your Wordpress site at the same time as you're freshening up your own site.
 * Author:            United Wedding Professionals Inc
 * Author URI:        https://unitedwed.co
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

add_filter( 'attachment_fields_to_edit', 'uwp_add_controls', 10, 2 );
add_filter( 'attachment_fields_to_save', 'uwp_save_metadata', 10, 2 );

/**
 * Add additional fields to media attachment post
 *
 * @param  array  $form_fields Form field array for the attachment.
 * @param  object $post        Attachment record in the database.
 * @return array              Updated form fields.
 */
function uwp_add_controls( $form_fields, $post ) {
	$post_id = $post->ID;
	$in_portfolio = get_post_meta( $post_id, 'uwp_portfolio', true );
	// $is_banner = get_post_meta( $post_id, 'uwp_banner', true );

	$form_fields['uwp_portfolio']['label'] = esc_html__( 'Add to UnitedWed', 'mytheme' );
	$form_fields['uwp_portfolio']['input'] = 'html';
	$form_fields['uwp_portfolio']['html'] = '<input type="checkbox" value="1" name="attachments[' . $post_id . '][uwp_portfolio]" id="attachments[' . $post_id . '][uwp_portfolio]" ' . checked( $in_portfolio, '1', false ) . ' />';
/*
	$form_fields['uwp_banner']['label'] = esc_html__( 'Profile Banner', 'mytheme' );
	$form_fields['uwp_banner']['input'] = 'html';
	$form_fields['uwp_banner']['html'] = '<input type="checkbox" value="1" name="attachments[' . $post_id . '][uwp_banner]" id="attachments[' . $post_id . '][uwp_banner]" ' . checked( $is_banner, '1', false ) . ' />';
*/
	return $form_fields;
}


/**
 * Save form fields for media attachments
 *
 * @param  array $post       WP post array.
 * @param  array $attachment Part of the form $_POST ($_POST[attachments][postID]).
 * @return array             Updated post.
 */
function uwp_save_metadata( $post, $attachment ) {
	update_post_meta( $post['ID'], 'uwp_portfolio', $attachment['uwp_portfolio'] );
	// update_post_meta( $post['ID'], 'uwp_banner', $attachment['uwp_banner'] );
	
	return $post;
}

add_action( 'rest_api_init', function() {
    register_rest_route( 'uwp/v1', 'portfolio', [
		'methods' => 'GET',
		'callback'=> 'uwp_get_portfolio',
	]);
});

function uwp_get_portfolio() {
	// Get the banner
/*
	$query_images_args = array(
		'post_type'      => 'attachment',
		'post_mime_type' => 'image',
		'post_status'    => 'inherit',
		'posts_per_page' => - 1,
		'meta_query' 	 => array(
	         array(
    	        'key' => 'uwp_banner',
        	    'value' => true,
            	'compare' => '=='
	         )
    	)
	);

	$query_images = new WP_Query( $query_images_args );

	$banner_images = array();
	foreach ( $query_images->posts as $image ) {
    	$banner_images[] = wp_get_attachment_url( $image->ID );
	}
*/	
	// Get the portfolio
	$query_images_args = array(
		'post_type'      => 'attachment',
		'post_mime_type' => 'image',
		'post_status'    => 'inherit',
		'posts_per_page' => - 1,
		'meta_query' 	 => array(
	         array(
    	        'key' => 'uwp_portfolio',
        	    'value' => true,
            	'compare' => '=='
	         )
    	)
	);

	$query_images = new WP_Query( $query_images_args );

	$portfolio = array();
	foreach ( $query_images->posts as $image ) {
		$p = new stdClass();
		$p->id = $image->ID;
		$p->url = wp_get_attachment_url( $image->ID );
    	$portfolio[] = $p;
	}
	
	// $json->banner = $banner_images;
	$json->portfolio = $portfolio;
	
	return $json;
}
?>