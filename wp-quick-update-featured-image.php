<?php
/*
  Plugin Name: WP Quick Update Featured Image
  Plugin URI: https://wordpress.org/plugins/wp-quick-update-featured-image/
  Description: Update post featured image from listing in admin.
  Author: CMitexperts
  Version: 1.0
  Author URI: http://cmitexperts.com/
  Tags: wordpress, posts, featured image, update featured image, update featured image from listing page
  Text Domain: wp-quick-update-featured-image
 */

// ADD NEW COLUMN
function cmit_featured_image_columns_head($defaults) {
    $defaults['cmit_featured_image'] = 'Featured Image';
    return $defaults;
}

// GET FEATURED IMAGE
function cmit_get_featured_image($post_ID) {
    $post_thumbnail_id = get_post_thumbnail_id($post_ID);
    if ($post_thumbnail_id) {
        $post_thumbnail_img = wp_get_attachment_image_src($post_thumbnail_id, array(50,50));
        return $post_thumbnail_img[0];
    }
}

// SHOW THE FEATURED IMAGE
function cmit_featured_image_content($column_name, $post_ID) {
    if ($column_name == 'featured_image') {
        $post_featured_image = cmit_get_featured_image($post_ID);
        echo '<div class="featured-img-container" id="feat_container_'.$post_ID.'">';
	        if ($post_featured_image) {
            	echo '<img class="img-'.$post_ID.' image" src="' . $post_featured_image . '" />';
            	echo '<div class="contorls-featured-action">
            		<a href="javascript:void(0);" class="open-editor feat-actions" data-postID="'.$post_ID.'"><span class="dashicons dashicons-welcome-write-blog" title="Update Image"></span></a>
            		<a href="javascript:void(0);" class="removeImage feat-actions" data-postID="'.$post_ID.'"><span class="dashicons dashicons-no-alt" title="Remove Image"></span></a>
            	  </div>';
	        } else {
	        	echo '<img class="img-'.$post_ID.' image" src="'.plugins_url( '/images/no-image.png' , __FILE__ ).'" />';
        		echo '<div class="contorls-featured-action">
            			<a href="javascript:void(0);" class="open-editor feat-actions" data-postID="'.$post_ID.'"><span class="dashicons dashicons-plus" title="Add Image"></span></a>
            	  </div>';
	        }
        echo '</div>';
    }
}


function cmit_load_wp_media_files( $page ) {
  // change to the $page where you want to enqueue the script
  if( $page == 'edit.php' ) {
    // Enqueue WordPress media scripts
    wp_enqueue_media();
    // Enqueue custom script that will interact with wp.media
    wp_enqueue_script( 'cmit_custom_featured_image', plugins_url( '/js/featured_image.js' , __FILE__ ), array('jquery'), '0.1' );
    wp_register_style( 'cmit_custom_featured_image_css', plugins_url( '/css/featured_image.css' , __FILE__ ) , false, '1.0.0' );
    wp_enqueue_style( 'cmit_custom_featured_image_css' );
  }
}


add_action( 'wp_ajax_update_featured_img', 'cmit_update_featured_img' );
function cmit_update_featured_img() {
	$attach_id = $_POST['id'];
	if($attach_id)
	{
		$post_id = (int) $_POST['post_id'];
    if($post_id)
    {
		  // And finally assign featured image to post
	    set_post_thumbnail( $post_id, $attach_id );
	     wp_send_json(array('success'=>true,'post_id'=>$post_id,'html'=>'<a href="javascript:void(0);" class="open-editor feat-actions" data-postID="'.$post_id.'" title="Update Image"><span class="dashicons dashicons-welcome-write-blog"></span></a><a href="javascript:void(0);" class="removeImage feat-actions" data-postID="'.$post_id.'" title="Remove Image"><span class="dashicons dashicons-no-alt"></span></a>'));
     } else {
        wp_send_json(array('success'=>false,'post_id'=>$post_id,'html'=>''));
     }
	} else {
		wp_send_json(array('success'=>false,'post_id'=>$post_id,'html'=>''));
	}
	exit;
}

add_action( 'wp_ajax_remove_featured_img', 'cmit_remove_featured_img' );
function cmit_remove_featured_img() {
	// And finally remove featured image to post
	$post_id = (int) $_POST['post_id'];
  if($post_id)
  {
    $attachment_id = get_post_thumbnail_id( $post_id );
    delete_post_thumbnail($post_id);
    wp_send_json(array('success'=>true,'post_id'=>$post_id,'html'=>'<img class="img-'.$post_id.' image" src="'.plugins_url( '/images/no-image.png' , __FILE__ ).'" /><div class="contorls-featured-action"><a href="javascript:void(0);" class="open-editor feat-actions" data-postID="'.$post_id.'" title="Add Image"><span class="dashicons dashicons-plus"></span></a></div>'));
  }
	exit;
}

add_action( 'admin_enqueue_scripts', 'cmit_load_wp_media_files' );
add_filter('manage_posts_columns', 'cmit_featured_image_columns_head');
add_action('manage_posts_custom_column', 'cmit_featured_image_content', 10, 2);