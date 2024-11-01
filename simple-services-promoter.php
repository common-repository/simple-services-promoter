<?php
/*
 Plugin Name: Simple Services Promoter
 Description: Easily showcase your services, products, or anything else anywhere on your site.
 Version:     1.2
 Author:      Corporate Zen
 Author URI:  http://www.corporatezen.com/
 License:     GPL2
 License URI: https://www.gnu.org/licenses/gpl-2.0.html
 
 Simple Services Promoter is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 2 of the License, or
 any later version.
 
 Simple Services Promoter is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 GNU General Public License for more details.
 
 You should have received a copy of the GNU General Public License
 along with Simple Services Promoter. If not, see https://www.gnu.org/licenses/gpl-2.0.html
 */

defined( 'ABSPATH' ) or die( 'Direct access to this code is not allowed.' );

// Register Custom Post Type
function simple_services_promoter_setup_cpt() {
	
	$labels = array(
			'name'                  => 'Services',
			'singular_name'         => 'Service', 
			'menu_name'             => 'Services', 
			'name_admin_bar'        => 'Services',
			'archives'              => 'Service Archives',
			'attributes'            => 'Service Attributes', 
			'parent_item_colon'     => 'Parent Service:', 
			'all_items'             => 'All Services', 
			'add_new_item'          => 'Add New Service', 
			'add_new'               => 'Add New', 
			'new_item'              => 'New Service', 
			'edit_item'             => 'Edit Service', 
			'update_item'           => 'Update Service', 
			'view_item'             => 'View Service', 
			'view_items'            => 'View Services', 
			'search_items'          => 'Search Services', 
			'not_found'             => 'Not found', 
			'not_found_in_trash'    => 'Not found in Trash', 
			'featured_image'        => 'Featured Image', 
			'set_featured_image'    => 'Set featured image', 
			'remove_featured_image' => 'Remove featured image', 
			'use_featured_image'    => 'Use as featured image', 
			'insert_into_item'      => 'Insert into service', 
			'uploaded_to_this_item' => 'Uploaded to this service', 
			'items_list'            => 'Service list', 
			'items_list_navigation' => 'Services list navigation', 
			'filter_items_list'     => 'Filter service list'
	);
	$args = array(
			'label'                 => 'Service',
			'description'           => 'A service or product your website or buisness provides',
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor', 'author', 'thumbnail', 'revisions', ),
			'taxonomies'            => array( 'category', 'post_tag' ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 30,
			'menu_icon'             => 'dashicons-index-card',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'capability_type'       => 'post',
	);
	
	register_post_type( 'ssp_service', $args );
	
}
add_action( 'init', 'simple_services_promoter_setup_cpt', 0 );

// Change Excerpt Length
function ssp_excerpt($limit) {
	$excerpt = explode(' ', get_the_excerpt(), $limit);

	if (count($excerpt)>=$limit) {
		array_pop($excerpt);
		$excerpt = implode(" ",$excerpt).'...';
	} else {
		$excerpt = implode(" ",$excerpt);
	}
	$excerpt = preg_replace('`[[^]]*]`', '', $excerpt);
	return $excerpt;
}

function ssp_content($limit) {
	
	$content = explode(' ', get_the_content(), $limit);
	
	if (count($content)>=$limit) {
		array_pop($content);
		$content = implode(" ", $content).'...';
	} else {
		$content = implode(" ", $content);
	}
	
	$content = preg_replace('/[.+]/', '', $content);
	$content = apply_filters('the_content', $content);
	$content = str_replace(']]>', ']]&gt;', $content);
	
	return $content;
}

// Setup Shortcode Handlers
add_shortcode('ssp', 'ssp_shortcode_handler');
function ssp_shortcode_handler( $atts ) {
	global $post;
	
	$atts = shortcode_atts(
			array(
					'posts_per_page' => '-1',
					'post_type'      => 'ssp_service',
					//'service_id'     => array(),
					'order'          => 'ASC',
					'order_by'       => 'title'
			),
			$atts,
			'ssp'
			);
	
	$posts = new WP_Query($atts);
	$out = '';
	
	if ( $posts->have_posts() )
		while ( $posts->have_posts() ):
			$posts->the_post();
				
				$feat_img_id  = get_post_thumbnail_id();
				$feat_img_url = wp_get_attachment_image_src($feat_img_id, 'full');
				$img_div_html = '';
				
				if ( isset ( $feat_img_url[0] ) && !empty ( $feat_img_url[0] ) ) {
					$img_div_html = '<div style="margin-bottom: 20px; width: 100%; height: 300px; background-size:cover; background-image: url(' . $feat_img_url[0] . ');"></div>';
				}
				
				$related = ( !empty ( $atts['service_id'] ) && isset ( $atts['service_id'] ) && is_array($atts['service_id'] ) ? $atts['service_id'] : get_post_meta( $post->ID , 'meta_related', true) );
				//$related = get_post_meta( $post->ID , 'meta_related', true);
				$related_html = '';
				if ( count ( $related ) > 0 && is_array( $related ) ) {
					$related_html .= '<h3>Related</h3><ul>';
					
					foreach ( $related as $rel) {
						$related_html .= '<li><a href="' . get_the_permalink() . '">' . get_the_title($rel) . '</a></li>';
					}
					
					$related_html .= '</ul>';
				}
				
				//the_tags();
				
				$out .= '
<div class="service-wrap">
	<h1><a href="' . get_the_permalink() . '" >' . get_the_title() . '</a></h1>
	' . $img_div_html . '
	<span style="float: right; max-width: 50%;">' . $related_html . '</span>
	' . wpautop ( get_the_content() ) . '
</div>
<div style="clear:both;"></div>';
		endwhile;
	else
		return; // no posts found
				
		wp_reset_query();
		return html_entity_decode( trim($out) );
}

add_shortcode('ssp_grid', 'ssp_shortcode_grid_handler');
function ssp_shortcode_grid_handler( $atts ) {
	global $post;
	
	$atts = shortcode_atts(
		array (
				'posts_per_page' => '-1',
				'post_type'      => 'ssp_service',
				//'service_id'     => array(),
				//'cols'           => '3',
				'order'          => 'ASC',
				'order_by'       => 'title'
		), 
		$atts,
		'ssp_grid'
	);
	
	$posts = new WP_Query($atts);
	$out = '';
	$current_post = 0;
	
	if ( $posts->have_posts() )
		while ( $posts->have_posts() ):
			$posts->the_post();

				$count = $posts->post_count;
				$current_post++;
				
				if ( $atts['posts_per_page'] == 1 || $count == 1 ) {
					$col = 12;
					$content =  wpautop ( get_the_content() );
				} else if ( $atts['posts_per_page'] == 2 || $count == 2 ) {
					$col = 6;
					$content = ssp_excerpt(40);
				} else {
					$col = 4;
					$content = ssp_excerpt(25);
				}
				
				$out .= '
<div style="margin-bottom: 20px; border: 1px solid grey; min-height: 280px;" class="ssp_service col-lg-' . $col . ' col-md-' . $col . ' col-sm-12">
	<h2><a href="' . get_the_permalink() . '" >' . get_the_title() . '</a></h2>
	<p>' . $content . '</p>
	<a href="' . get_the_permalink() . '" >Learn More</a>
</div>';	
				
				if ( $current_post % 3 == 0 ) {
					$out .= '<div style="clear:both"></div>';
				}
			
		endwhile;
	else
		return; // no posts found
			
			wp_reset_query();
			return html_entity_decode( trim($out) );
}

// Enqueue grid.css
function theme_styles() {
	wp_enqueue_style( 'grid_style', plugin_dir_url( __FILE__ ) . 'css/grid.css' );
}

add_action( 'wp_enqueue_scripts', 'theme_styles');

// Dropdown Metabox Setup
add_action( 'admin_init', 'simple_services_promoter_metabox_setup');
function simple_services_promoter_metabox_setup() {
	add_action( 'add_meta_boxes', 'simple_services_promoter_add_metabox');
	add_action( 'save_post', 'simple_services_promoter_save_custom_meta');
} 

function simple_services_promoter_add_metabox() {
	add_meta_box( 'ssp-services-related', 'Related', 'simple_services_promoter_fill_metabox', 'ssp_service', 'normal', 'high' );
}

// Save Metadata
function simple_services_promoter_save_custom_meta( $post_id ) {
	
	/* Verify the nonce before proceeding. */
	if ( !isset( $_POST['simple_services_promoter_plugin_nonce'] ) || !wp_verify_nonce( $_POST['simple_services_promoter_plugin_nonce'], basename( __FILE__ ) ) ) {
		return $post_id;
	}
		
	/* Check if the current user has permission to edit the post. */
	if ( !current_user_can( 'edit_post', $post_id ) ) {
		return $post_id;
	}
	
	$sanitizedValues = array_filter ( $_POST['meta_related'], 'ctype_digit' );
	
	/*
	// Get the posted data and sanitize it for use as an HTML class. 
	$new_meta_value = ( isset( $_POST['meta_related'] ) ? $sanitizedValues : '' );
	
	// Get the meta key. 
	$meta_key = 'meta_related';
	
	// Get the meta value of the custom field key. 
	$meta_value = get_post_meta( $post_id, $meta_key, true );
	
	// If a new meta value was added and there was no previous value, add it. 
	if ( $new_meta_value && empty($meta_value) ) {
		add_post_meta( $post_id, $meta_key, $new_meta_value, true );
	}
	
	// If the new meta value does not match the old value, update it. 
	elseif ( $new_meta_value && $new_meta_value != $meta_value ) {
		update_post_meta( $post_id, $meta_key, $new_meta_value );
	}
	
	// If there is no new meta value but an old value exists, delete it. 
	elseif ( empty($new_meta_value) && $meta_value ) {
		delete_post_meta( $post_id, $meta_key, $meta_value );
	}
	*/
	
	update_post_meta($post_id, 'meta_related',  $sanitizedValues);
	
}

// File Metabox
function simple_services_promoter_fill_metabox( $post ) {

	$related = get_post_meta( $post->ID , 'meta_related', true); 
	
	wp_nonce_field( basename( __FILE__ ), 'simple_services_promoter_plugin_nonce' ); ?>
	<p><em>Hold the "Ctrl" key and you can select as many related pages, posts, and/or services as you want.</em></p>
    <p>
        <label for="meta_related_pages">Pages: </label>
        <select multiple name='meta_related[]' id='meta_related_pages'>
            <?php 
            $pages = get_posts( array('post_type' => 'page') );
            
            foreach ($pages as $page): ?>
            	<option <?php echo ( is_array($related) && in_array ( $page->ID, $related ) ? 'selected' : '' ); ?> value="<?php echo esc_attr ( $page->ID ); ?>"><?php echo esc_html ( $page->post_title ); ?></option>
            <?php endforeach; ?>
        </select>
        <br><br>
        <label for="meta_related_posts">Posts: </label>
        <select multiple name='meta_related[]' id='meta_related_posts'>
            <?php 
            $posts = get_posts( array('post_type' => 'post') );
            
            foreach ($posts as $the_post): ?>
            	<option <?php echo ( is_array($related) && in_array ( $the_post->ID, $related ) ? 'selected' : '' ); ?> value="<?php echo esc_attr ( $the_post->ID ); ?>"><?php echo esc_html ( $the_post->post_title ); ?></option>
            <?php endforeach; ?>
        </select>
        <br><br>
        <label for="meta_related_services">Services: </label>
        <select multiple name='meta_related[]' id='meta_related_services'>
            <?php 
            $services = get_posts( array('post_type' => 'ssp_service') );
            
            foreach ($services as $service): ?>
            	<option <?php echo ( is_array($related) && in_array ( $service->ID, $related ) ? 'selected' : '' ); ?> value="<?php echo esc_attr ( $service->ID ); ?>"><?php echo esc_html ( $service->post_title ); ?></option>
            <?php endforeach; ?>
        </select>
        
    </p>
    <?php   
}

/////////////////////////////// SIGN UP ////////////////////////////
add_action('wp_dashboard_setup', 'simple_services_promoter_custom_dashboard_widgets');
function simple_services_promoter_custom_dashboard_widgets() {
	global $wp_meta_boxes;
	wp_add_dashboard_widget('corporatezen_newsletter', 'CZ Newsletter', 'simple_services_promoter_mailchimp_signup_widget');
}

function simple_services_promoter_mailchimp_signup_widget() {
	$user    = wp_get_current_user();
	$email   = (string) $user->user_email;
	$fname   = (string) $user->user_firstname;
	$lname   = (string) $user->user_lastname;
	?>
	
<!-- Begin MailChimp Signup Form -->
<div id="mc_embed_signup">
	<form action="//corporatezen.us13.list-manage.com/subscribe/post?u=e9426a399ea81798a865c10a7&amp;id=9c1dcdaf0e" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
	    <div id="mc_embed_signup_scroll">
	    
			<h2>Don't miss important updates!</h2>
			<p>Don't worry, we hate spam too. We send a max of 2 emails a month, and we will never share your email for any reason. Sign up to ensure you don't miss any important updates or information about this plugin or theme. </p>
		
			<div class="mc-field-group">
				<!--<label for="mce-EMAIL">Email Address  <span class="asterisk">*</span></label>-->
				<input type="email" value="<?php echo $email; ?>" name="EMAIL" class="fat_wide required email" id="mce-EMAIL" style="width: 75%;">
				<input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button button-primary">
			</div>
		
			<div class="mc-field-group">
				<input type="hidden" value="<?php echo $fname; ?>" name="FNAME" class="" id="mce-FNAME">
			</div>
			<div class="mc-field-group">
				<input type="hidden" value="<?php echo $lname; ?>" name="LNAME" class="" id="mce-LNAME">
			</div>
			
		
			<div id="mce-responses" class="clear">
				<div class="response" id="mce-error-response" style="display:none;color: red;font-weight: 500;margin-top: 20px; margin-bottom: 20px;"></div>
				<div class="response" id="mce-success-response" style="display:none;color: green;font-weight: 500;margin-top: 20px; margin-bottom: 20px;"></div>
			</div>    
			
			<!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
		    <div style="position: absolute; left: -5000px;" aria-hidden="true">
		    	<input type="text" name="b_e9426a399ea81798a865c10a7_9c1dcdaf0e" tabindex="-1" value="">
		    </div>
	
	    </div>
	</form>
</div>

<script type='text/javascript' src='//s3.amazonaws.com/downloads.mailchimp.com/js/mc-validate.js'></script>
<script type='text/javascript'>(function($) {window.fnames = new Array(); window.ftypes = new Array();fnames[0]='EMAIL';ftypes[0]='email';fnames[1]='FNAME';ftypes[1]='text';fnames[2]='LNAME';ftypes[2]='text';}(jQuery));var $mcj = jQuery.noConflict(true);</script>
<!--End mc_embed_signup-->
	
	<?php
}
?>