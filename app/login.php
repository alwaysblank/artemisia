<?php

namespace App;

/**
 * Custom Login Page
 */

// calling your own login css so you can style it
add_action('login_enqueue_scripts', function () {
    wp_enqueue_style('login_css', get_template_directory_uri() . '/resources/views/login/login.css', false);
}, 10);

// changing the logo link from wordpress.org to your site
add_filter('login_headerurl', function () {
    return home_url();
});

// changing the alt text on the logo to show your site name
add_filter('login_headertitle', function () {
    return get_option('blogname');
});
