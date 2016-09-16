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
 * bulk custom post types
 *
 * Each content type can have a few optional overides,
 * otherwise the default value is used.
 *
 * 'single'         - single name for display titles
 * 'plural'         - plural name for display titles
 * 'position'       - change the weight of the nav icon
 * 'icon'           - https://developer.wordpress.org/resource/dashicons
 * 'supports'       - array() of available fields
 * 'exclude_search' - true/false should be excluded from search results
 * 'hierarchical'   - true/false whether the post type is hierarchical
 */

function custom_post_types() {
  $cpt_list = array(
    'custom_post_type' => array (
      'single'         => 'Custom Type',
      'plural'         => 'Custom Types',
      'position'       => 3,
      'icon'           => 'dashicons-unlock',
      'supports'       => array('title','editor','page-attributes')
    ),
    'default_cpt' => array (
      'single'         => 'Default Type',
      'plural'         => 'Default Types'
    )
  );

  foreach( $cpt_list as $cpt => $data) :

    # Null coalescing operators
    $single         = $data['single']         ?? 'Post Type';
    $plural         = $data['plural']         ?? 'Post Types';
    $icon           = $data['icon']           ?? 'dashicons-book';
    $position       = $data['position']       ?? 20;
    $supports       = $data['supports']       ?? array('title','editor','thumbnail');
    $exclude_search = $data['exclude_search'] ?? false;
    $hierarchical   = $data['hierarchical']   ?? false;

    $labels = array(
      'name'               => _x($plural, 'post type general name'),
      'singular_name'      => _x($single, 'post type singular name'),
      'add_new'            => _x('Add New', $single),
      'add_new_item'       => __('Add New '. $single),
      'edit_item'          => __('Edit '.$single),
      'new_item'           => __('New '.$single),
      'view_item'          => __('View '.$single),
      'search_items'       => __('Search '.$plural),
      'not_found'          => __('No '.$plural.' found'),
      'not_found_in_trash' => __('No '.$plural.' found in Trash'),
      'parent_item_colon'  => ''
    );

    $args = array(
      'labels'              => $labels,
      'public'              => true,
      'publicly_queryable'  => true,
      'exclude_from_search' => $exclude_search,
      'show_ui'             => true,
      'query_var'           => true,
      'rewrite'             => true,
      'capability_type'     => 'post',
      'hierarchical'        => false,
      'has_archive'         => true,
      'menu_icon'           => $icon,
      'menu_position'       => $position,
      'supports'            => $supports
    );

    register_post_type($cpt, $args);

  endforeach;
}

add_action( 'init', __NAMESPACE__ . '\\custom_post_types');
