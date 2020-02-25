<?php

/*
 * Plugin Name: Reorder Posts
 * Plugin URI:  https://wordpress.org/plugins/slide/
 * Description: Allows you to create presentations with the block editor.
 * Version:     0.0.39
 * Author:      Ella van Durpe
 * Author URI:  https://ellavandurpe.com
 * Text Domain: slide
 * License:     GPL-2.0+
 */

function order_posts_by_title( $query ) { 

  if ( $query->is_home() && $query->is_main_query() ) { 

    $query->set( 'orderby', 'post_modified' );

    $query->set( 'order', 'DESC' );

  } 

} 

add_action( 'pre_get_posts', 'order_posts_by_title' );