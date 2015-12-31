<?php

namespace Roots\Sage\CPT;

/**
 * Flush rewrite rules
 */
function do_flush_rewrite_rules() {
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', __NAMESPACE__ . '\\do_flush_rewrite_rules' );

/**
 * The custom type
 */
function custom_post_example() {
  /* http://codex.wordpress.org/Function_Reference/register_post_type */
	register_post_type( 'custom_type',
		array( 'labels'         => array(
			'name'                => __( 'Custom Types', 'sage' ),
			'singular_name'       => __( 'Custom Post', 'sage' ),
			),
			'description'         => __( 'This is the example custom post type', 'sage' ),
			'public'              => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => false,
			'show_ui'             => true,
			'query_var'           => true,
			'menu_position'       => 8,
      /* https://developer.wordpress.org/resource/dashicons */
			'menu_icon'           => 'dashicons-book',
			'rewrite'	            => array(
        'slug'              => 'custom_type',
        'with_front'        => false
      ),
			'has_archive'         => 'custom_type',
			'capability_type'     => 'post',
			'hierarchical'        => false,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'page-attributes')
		)
	);
}
//add_action( 'init', __NAMESPACE__ . '\\custom_post_example');