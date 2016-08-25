<?php

/*
 * Copyright 2012-2015, Theia Post Slider, WeCodePixels, http://wecodepixels.com
 */

add_filter( 'query_vars', 'TpsAjax::query_vars' );
add_action( 'parse_request', 'TpsAjax::parse_request' );

class TpsAjax {
	public static function query_vars( $vars ) {
		$vars[] = 'theiaPostSlider';
		$vars[] = 'postId';
		$vars[] = 'slides';

		return $vars;
	}

	public static function parse_request( $wp ) {
		if ( ! array_key_exists( 'theiaPostSlider', $wp->query_vars ) ) {
			return;
		}

		switch ( $wp->query_vars['theiaPostSlider'] ) {
			case 'get-slides':
				self::get_slides( $wp );
				break;
		}
	}

	private static function get_slides( $wp ) {
		if (
			! array_key_exists( 'postId', $wp->query_vars ) ||
			! array_key_exists( 'slides', $wp->query_vars )
		) {
			return;
		}

		TpsMisc::$force_disable = true;

		// Get post.
		global $post;
		$post = get_post( $wp->query_vars['postId'] );
		if ( $post === null ) {
			exit();
		}
		setup_postdata( $post );
		query_posts( 'p=' . $wp->query_vars['postId'] );

		// Get and process each slide.
		$requestedSlides = $wp->query_vars['slides'];
		$slides          = array();
		foreach ( $requestedSlides as $i ) {
			$slides[ $i ] = TpsMisc::get_sub_page( $i + 1, null );
		}

		// Add previous and next slide permalinks.
		{
			$previous = min( $requestedSlides ) - 1;
			$url      = TpsMisc::get_post_page_url( $previous + 1 );
			if ( $url ) {
				$slides[ $previous ] = array(
					'permalink' => $url
				);
			}

			$next = max( $requestedSlides ) + 1;
			$url  = TpsMisc::get_post_page_url( $next + 1 );
			if ( $url ) {
				$slides[ $next ] = array(
					'permalink' => $url
				);
			}
		}

		$result = array(
			'postId' => $post->ID,
			'slides' => $slides
		);

		header( 'Content-Type: application/json; charset=utf-8' );
		echo json_encode( $result );

		exit();
	}
}
