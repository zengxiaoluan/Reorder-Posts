<?php

/*
 * Plugin Name: Reorder Posts
 * Plugin URI:  https://wordpress.org/plugins/Reorder-Posts/
 * Description: Allows you to reorder the posts.
 * Version:     0.0.1
 * Author:      Zeng xiao luan
 * Author URI:  https://zengxiaoluan.com
 * Text Domain: reorder
 * License:     GPL-2.0+
 */

function order_posts_by_title( $query ) { 

  if ( $query->is_home() && $query->is_main_query() ) { 

    $query->set( 'orderby', 'post_modified' );

    $query->set( 'order', 'DESC' );

  } 

} 

add_action( 'pre_get_posts', 'order_posts_by_title' );