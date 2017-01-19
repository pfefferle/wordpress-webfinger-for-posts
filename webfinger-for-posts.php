<?php
/*
Plugin Name: WebFinger for Posts
Plugin URI: https://github.com/pfefferle/wordpress-webfinger-for-posts
Description: WebFinger for Posts
Version: 1.0.0
Author: pfefferle
Author URI: http://notizblog.org/
*/

// initialize plugin
add_action( 'init', array( 'WebfingerForPostsPlugin', 'init' ) );

/**
 * webfinger-for-posts
 *
 * @author Matthias Pfefferle
 */
class WebfingerForPostsPlugin {

	/**
	 * Initialize the plugin, registering WordPress hooks.
	 */
	public static function init() {
		add_filter( 'webfinger_data', array( 'WebfingerForPostsPlugin', 'generate_post_data' ), 10, 3 );
	}

	/**
	 * generates the webfinger base array
	 *
	 * @param array $webfinger the webfinger data-array
	 * @param string $resource the resource param
	 * @return array the enriched webfinger data-array
	 */
	public static function generate_post_data( $webfinger, $resource ) {
		// find matching post
		$post_id = url_to_postid( $resource );

		// check if there is a matching post-id
		if ( ! $post_id ) {
			return $webfinger;
		}

		// get post by id
		$post = get_post( $post_id );

		// check if there is a matching post
		if ( ! $post ) {
			return $webfinger;
		}

		$author = get_user_by( 'id', $post->post_author );

		// default webfinger array for posts
		$webfinger = array(
			'subject' => get_permalink( $post->ID ),
			'aliases' => apply_filters( 'webfinger_post_resource', array( home_url( '?p=' . $post->ID ), get_permalink( $post->ID ) ), $post ),
			'links' => array(
				array( 'rel' => 'shortlink', 'type' => 'text/html', 'href' => wp_get_shortlink( $post ) ),
				array( 'rel' => 'canonical', 'type' => 'text/html', 'href' => get_permalink( $post->ID ) ),
				array( 'rel' => 'author',    'type' => 'text/html', 'href' => get_author_posts_url( $author->ID, $author->nicename ) ),
			),
		);

		return apply_filters( 'webfinger_post_data', $webfinger, $resource, $post );
	}
}
