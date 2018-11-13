<?php

namespace App;

use function Roots\asset;
use function Roots\view;

/**
 * Theme assets
 */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script('sage/vendor', asset('scripts/vendor.js'), ['jquery'], null, true);
    wp_enqueue_script('sage/main', asset('scripts/main.js'), ['sage/vendor', 'jquery'], null, true);

    wp_add_inline_script('sage/vendor', asset('scripts/manifest.js')->contents(), 'before');

    if (is_single() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }

    $styles = ['styles/main.css'];

    foreach ($styles as $stylesheet) {
        if (($asset = asset($stylesheet)->exists())) {
            wp_enqueue_style('sage/'.basename($stylesheet, '.css'), asset($stylesheet)->uri(), false, null);
        }
    }
}, 100);

/**
 * Theme setup
 */
add_action('after_setup_theme', function () {
    /**
     * Enable features from Soil when plugin is activated
     * @link https://roots.io/plugins/soil/
     */
    add_theme_support('soil-clean-up');
    add_theme_support('soil-jquery-cdn');
    add_theme_support('soil-nav-walker');
    add_theme_support('soil-nice-search');
    add_theme_support('soil-relative-urls');

    /**
     * Enable plugins to manage the document title
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#title-tag
     */
    add_theme_support('title-tag');

    /**
     * Register navigation menus
     * @link https://developer.wordpress.org/reference/functions/register_nav_menus/
     */
    register_nav_menus([
        'primary_navigation' => __('Primary Navigation', 'sage')
    ]);

    /**
     * Enable post thumbnails
     * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
     */
    add_theme_support('post-thumbnails');

    /**
     * Enable HTML5 markup support
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#html5
     */
    add_theme_support('html5', ['caption', 'comment-form', 'comment-list', 'gallery', 'search-form']);

    /**
     * Enable selective refresh for widgets in customizer
     * @link https://developer.wordpress.org/themes/advanced-topics/customizer-api/#theme-support-in-sidebars
     */
    add_theme_support('customize-selective-refresh-widgets');

    /**
     * Use main stylesheet for visual editor
     * @see resources/assets/styles/layouts/_tinymce.scss
     */
    add_editor_style(asset('styles/admin.css')->uri());

    /**  
     * Remove WP emojis
     */
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    add_filter( 'emoji_svg_url', '__return_false' );
}, 20);

/**
 * Preloads any scripts that we have asked to preload
 */
add_action('wp_head', function() {
    foreach (wp_scripts()->registered as $handle => $script) :
        if(strpos($handle, 'artemesia/preload') === 0) :
            printf('<link rel="preload" href="%s" as="script">', apply_filters('script_loader_src', $script->src));
        endif;
    endforeach;
});

/**
 * Preloads any styles that we have asked to preload
 */
add_filter('style_loader_tag', function($html, $handle, $href, $media) {
    if(strpos($handle, 'artemesia/preload') === 0) :
        $GLOBALS['is_preloading_styles'] = 'yes';
        return sprintf('<link rel="preload" href="%1$s" as="style" type="text/css" onload="this.rel=\'stylesheet\'" media="%2$s">%3$s<noscript><link rel="stylesheet" href="%1$s"></noscript>', apply_filters('style_loader_src', $href, $handle), $media, PHP_EOL);
    endif;
    return $html;
}, 10, 4);

add_action('wp_head', function() {
    if($GLOBALS['is_preloading_styles'] === 'yes') :
        $loadCSS = '!function(a){"use strict";var b=function(b,c,d){function j(a){if(e.body)return a();setTimeout(function(){j(a)})}function l(){f.addEventListener&&f.removeEventListener("load",l),f.media=d||"all"}var g,e=a.document,f=e.createElement("link");if(c)g=c;else{var h=(e.body||e.getElementsByTagName("head")[0]).childNodes;g=h[h.length-1]}var i=e.styleSheets;f.rel="stylesheet",f.href=b,f.media="only x",j(function(){g.parentNode.insertBefore(f,c?g:g.nextSibling)});var k=function(a){for(var b=f.href,c=i.length;c--;)if(i[c].href===b)return a();setTimeout(function(){k(a)})};return f.addEventListener&&f.addEventListener("load",l),f.onloadcssdefined=k,k(l),f};"undefined"!=typeof exports?exports.loadCSS=b:a.loadCSS=b}("undefined"!=typeof global?global:this);';
        $preloadPolyfill = '!function(a){if(a.loadCSS){var b=loadCSS.relpreload={};if(b.support=function(){try{return a.document.createElement("link").relList.supports("preload")}catch(a){return!1}},b.poly=function(){for(var b=a.document.getElementsByTagName("link"),c=0;c<b.length;c++){var d=b[c];"preload"===d.rel&&"style"===d.getAttribute("as")&&(a.loadCSS(d.href,d,d.getAttribute("media")),d.rel=null)}},!b.support()){b.poly();var c=a.setInterval(b.poly,300);a.addEventListener&&a.addEventListener("load",function(){b.poly(),a.clearInterval(c)}),a.attachEvent&&a.attachEvent("onload",function(){a.clearInterval(c)})}}}(this);';
        printf('<!-- Start loadCSS scripts -->%2$s<script type="text/javascript" charset="utf-8">%1$s%2$s%3$s</script>%2$s<!-- End loadCSS scripts -->', $loadCSS, PHP_EOL, $preloadPolyfill);
    endif;
}, 99);

/**
 * Register sidebars
 */
add_action('widgets_init', function () {
    $config = [
        'before_widget' => '<section class="widget %1$s %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ];
    register_sidebar([
        'name' => __('Primary', 'sage'),
        'id' => 'sidebar-primary'
    ] + $config);
    register_sidebar([
        'name' => __('Footer', 'sage'),
        'id' => 'sidebar-footer'
    ] + $config);
});
