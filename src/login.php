<?php

namespace Roots\Sage\Login;

/**
 * Custom Login Page
 */

// calling your own login css so you can style it
function login_css() {
  wp_enqueue_style( 'login_css', get_template_directory_uri() . '/templates/login/login.css', false );
}

// changing the logo link from wordpress.org to your site
function login_url() {  return home_url(); }

// changing the alt text on the logo to show your site name
function login_title() { return get_option( 'blogname' ); }

// calling it only on the login page
add_action( 'login_enqueue_scripts', __NAMESPACE__ . '\\login_css', 10 );
add_filter( 'login_headerurl', __NAMESPACE__ . '\\login_url' );
add_filter( 'login_headertitle', __NAMESPACE__ . '\\login_title' );
