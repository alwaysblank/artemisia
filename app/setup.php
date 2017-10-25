<?php

namespace App;

use Roots\Sage\Container;
use Roots\Sage\Assets\JsonManifest;
use Roots\Sage\Template\Blade;
use Roots\Sage\Template\BladeProvider;

/**
 * Theme assets
 */

 /**
  * This replaces the functionality of Soil's jQuery
  * stuff, but does *not* provide a fallback if the
  * CDN fails. This is done because that fallback
  * causes jQuery to be loaded twice in some situations.
  *
  * @return void
  */
add_action('wp_enqueue_scripts', function () {
    $jquery_version = wp_scripts()->registered['jquery']->ver;

    wp_deregister_script('jquery');

    wp_register_script(
        'jquery',
        'https://code.jquery.com/jquery-' . $jquery_version . '.min.js',
        [],
        null,
        true
    );

    add_filter('wp_resource_hints', function ($urls, $relation_type) {
        if ($relation_type === 'dns-prefetch') {
            $urls[] = 'code.jquery.com';
        }
        return $urls;
    }, 10, 2);
}, 100);

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'artemesia/preload/sage/main.css',
        asset_path('styles/main.css'),
        false,
        null
    );

    /* Load this if CSS is not cached, because that means
       we're preloading CSS and we want to avoid style pop.
    */
    if (!isset($_COOKIE['CSS_CACHED'])) {
        wp_add_inline_style(
            'artemesia/preload/sage/main.css',
            file_get_contents(locate_asset('styles/critical.css'))
        );
    }

    wp_enqueue_script(
        'artemesia/preload/sage/main.js',
        asset_path('scripts/main.js'),
        ['jquery'],
        null,
        true
    );
}, 100);

/**
 * Theme setup
 */
add_action(
    'after_setup_theme',
    function () {
        /**
         * Enable features from Soil when plugin is activated
         * @link https://roots.io/plugins/soil/
         */
        add_theme_support('soil-clean-up');
        // add_theme_support('soil-jquery-cdn');
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
        add_editor_style(asset_path('styles/main.css'));

        /**  
         * Remove WP emojis
         */
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('wp_print_styles', 'print_emoji_styles');
        add_filter('emoji_svg_url', '__return_false');
    },
    20
);

/**
 * Set a cookie so we can guess whether we've loaded
 * css or not.
 */
add_action('init', function () {
    $css_id = hash('md4', asset_path('styles/main.css'));
    if (!isset($_COOKIE['CSS_CACHED'])) {
        // If the cookie isn't set, set it.
        setcookie('CSS_CACHED', $css_id, strtotime('+30 days'), '/');
    } elseif ($_COOKIE['CSS_CACHED'] != $css_id) {
        // If the cookie doesn't match our CSS, unset it.
        setcookie('CSS_CACHED', $_COOKIE['CSS_CACHED'], 1, '/');
        unset($_COOKIE['CSS_CACHED']);
    }
});

/**
 * Preloads any scripts that we have asked to
 * preload.
 */
add_action('wp_head', function () {
    foreach (wp_scripts()->registered as $handle => $script) {
        if (strpos($handle, 'artemesia/preload') === 0) {
            printf('<link rel="preload" href="%s" as="script">', apply_filters('script_loader_src', $script->src));
        }
    }
});

/**
 * Preloads any styles that we have asked to preload
 */
add_filter('style_loader_tag', function ($html, $handle, $href, $media) {
    if (strpos($handle, 'artemesia/preload') === 0 && !isset($_COOKIE['CSS_CACHED'])) {
        $GLOBALS['is_preloading_styles'] = 'yes';
        $url = apply_filters('style_loader_src', $href, $handle);
        /**
         * There isn't a good way to break up this string, so we're
         * going to ignore standards for a bit.
         */
        // @codingStandardsIgnoreStart
        return sprintf(
            '<link rel="preload" href="%1$s" as="style" type="text/css" onload="this.rel=\'stylesheet\'" media="%2$s">%3$s<noscript><link rel="stylesheet" href="%1$s"></noscript>',
            $url,
            $media,
            PHP_EOL
        );
        // @codingStandardsIgnoreEnd
    } else {
        $GLOBALS['is_preloading_styles'] = 'no';
    }
    return $html;
}, 10, 4);

add_action('wp_footer', function () {
    if ($GLOBALS['is_preloading_styles'] === 'yes') :
        /**
         * There isn't a good way to break up these strings, so we're
         * going to ignore standards for a bit.
         */
        // @codingStandardsIgnoreStart
        $loadCSS = '!function(a){"use strict";var b=function(b,c,d){function j(a){if(e.body)return a();setTimeout(function(){j(a)})}function l(){f.addEventListener&&f.removeEventListener("load",l),f.media=d||"all"}var g,e=a.document,f=e.createElement("link");if(c)g=c;else{var h=(e.body||e.getElementsByTagName("head")[0]).childNodes;g=h[h.length-1]}var i=e.styleSheets;f.rel="stylesheet",f.href=b,f.media="only x",j(function(){g.parentNode.insertBefore(f,c?g:g.nextSibling)});var k=function(a){for(var b=f.href,c=i.length;c--;)if(i[c].href===b)return a();setTimeout(function(){k(a)})};return f.addEventListener&&f.addEventListener("load",l),f.onloadcssdefined=k,k(l),f};"undefined"!=typeof exports?exports.loadCSS=b:a.loadCSS=b}("undefined"!=typeof global?global:this);';
        $preloadPolyfill = '!function(a){if(a.loadCSS){var b=loadCSS.relpreload={};if(b.support=function(){try{return a.document.createElement("link").relList.supports("preload")}catch(a){return!1}},b.poly=function(){for(var b=a.document.getElementsByTagName("link"),c=0;c<b.length;c++){var d=b[c];"preload"===d.rel&&"style"===d.getAttribute("as")&&(a.loadCSS(d.href,d,d.getAttribute("media")),d.rel=null)}},!b.support()){b.poly();var c=a.setInterval(b.poly,300);a.addEventListener&&a.addEventListener("load",function(){b.poly(),a.clearInterval(c)}),a.attachEvent&&a.attachEvent("onload",function(){a.clearInterval(c)})}}}(this);';
        printf(
            '<!-- Start loadCSS scripts -->%2$s<script type="text/javascript" charset="utf-8">%1$s%2$s%3$s</script>%2$s<!-- End loadCSS scripts -->',
            $loadCSS,
            PHP_EOL,
            $preloadPolyfill
        );
        // @codingStandardsIgnoreEnd
    endif;
}, 99);

/**
 * Register sidebars
 */
add_action('widgets_init', function () {
    $config = [
        'before_widget' => '<section class="widget %1$s %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>'
    ];
    register_sidebar([
        'name'          => __('Primary', 'sage'),
        'id'            => 'sidebar-primary'
    ] + $config);
    register_sidebar([
        'name'          => __('Footer', 'sage'),
        'id'            => 'sidebar-footer'
    ] + $config);
});

/**
 * Updates the `$post` variable on each iteration of the loop.
 * Note: updated value is only available for subsequently loaded views, such as partials
 */
add_action('the_post', function ($post) {
    sage('blade')->share('post', $post);
});

/**
 * Setup Sage options
 */
add_action('after_setup_theme', function () {
    /**
     * Add JsonManifest to Sage container
     */
    sage()->singleton('sage.assets', function () {
        return new JsonManifest(config('assets.manifest'), config('assets.uri'));
    });

    /**
     * Add Blade to Sage container
     */
    sage()->singleton('sage.blade', function (Container $app) {
        $cachePath = config('view.compiled');
        if (!file_exists($cachePath)) {
            wp_mkdir_p($cachePath);
        }

        (new BladeProvider($app))->register();
        return new Blade($app['view']);
    });

    /**
     * Create @asset() Blade directive
     */
    sage('blade')->compiler()->directive('asset', function ($asset) {
        return "<?= " . __NAMESPACE__ . "\\asset_path({$asset}); ?>";
    });

    /**
     * Create @inline() Blade directive
     */
    sage('blade')->compiler()->directive('inline', function ($asset) {
        $asset = trim($asset, " '\"");
        return "<?= file_get_contents(". __NAMESPACE__ . "\\locate_asset('" . $asset . "')); ?>";
    });

    /**
     * Create @icon Blade directive
     */
    sage('blade')->compiler()->directive('icon', function ($asset) {
        $asset = trim($asset, " '\"");
        $asset = str_replace("'", "\"", $asset);
        /**
         * There isn't a good way to break up this string, so we're
         * going to ignore standards for a bit.
         */
        // @codingStandardsIgnoreStart
        return '<?php $asset = "' . $asset . '"; $true_asset = ' . __NAMESPACE__ . '\\locate_asset($asset); ?><span class="a-icon a-icon--<?= basename($asset, \'.svg\') ?>"><?= file_get_contents($true_asset); ?></span>';
        // @codingStandardsIgnoreEnd
    });
});
