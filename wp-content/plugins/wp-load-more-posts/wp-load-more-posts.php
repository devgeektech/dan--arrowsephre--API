<?php

/*
Plugin Name: WP Load More Posts
Description: WP Load More Posts is a simple and light-weight plugin for easily adding a Load More Posts button to your WordPress blog posts.
Version: 1.0
Author: Sean Megan
Author URI: http://www.seanmegan.com
License: GPL2
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/


if ( file_exists( dirname( __FILE__ ) . '/library/cmb2/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/library/cmb2/init.php';
}

if ( file_exists( dirname( __FILE__ ) . '/includes/wp-lmp-settings.php' ) ) {
	require_once dirname( __FILE__ ) . '/includes/wp-lmp-settings.php';
}

function wp_load_more_posts_init() {
       global $wp_query;

       // Add code to index and archive pages.
       if( is_home() || is_archive() || is_tax() ) {	
	       // Queue JS and CSS
	       wp_enqueue_script(
		       'wp-load-more-posts-js',
		       plugin_dir_url( __FILE__ ) . 'library/js/load-more.js',
		       array('jquery'),
		       '1.0'
	       );
	       
	       // What page are we on?
	       $paged = ( get_query_var('paged') > 1 ) ? get_query_var('paged') : 1;
	       // How many pages of posts do we have to display?
	       $max = $wp_query->max_num_pages;
	       
	       // Here we set values from the plugin options page
	       $content_selector = wp_lmp_get_option( 'content_selector' );
	       $post_class_selector = wp_lmp_get_option( 'post_class_selector' );
	       $pager_selector = wp_lmp_get_option( 'pager_selector' );
	       $btn_class = wp_lmp_get_option( 'btn_class' );
	       $load_more_text = wp_lmp_get_option( 'load_more_text' );
	       $loading_text = wp_lmp_get_option( 'loading_text' );
	       $no_posts_text = wp_lmp_get_option( 'no_posts_text' );
	       
	       
	       
	       // Add some parameters for the JS.
	       wp_localize_script(
		       'wp-load-more-posts-js',
		       'wpLoadMorePosts',
		       array(
			       'startPage' => $paged,
			       'maxPages' => $max,
			       'nextLink' => next_posts($max, false),
			       'contentSelector' => sanitize_text_field($content_selector),
			       'postClassSelector' => sanitize_text_field($post_class_selector),
			       'pagerSelector' => sanitize_text_field($pager_selector),
			       'btnClass' => sanitize_html_class($btn_class),
			       'loadMoreText' => esc_html($load_more_text),
			       'loadingText' => esc_html($loading_text),
			       'noPostsText' => esc_html($no_posts_text)
		       )
	       );
       }
}

add_action('template_redirect', 'wp_load_more_posts_init');

/* Adds link to Settings page on the plugins page */

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'wp_lmp_add_action_links' );

function wp_lmp_add_action_links ( $links ) {
       
       $mylinks = array(
	      '<a href="' . admin_url( 'options-general.php?page=wp_lmp_options' ) . '">Settings</a>',
       );
       
       return array_merge( $links, $mylinks );
}

?>