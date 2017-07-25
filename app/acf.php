<?php
// fix acf json saving
// https://discourse.roots.io/t/locating-acf-json-in-sage-9-wont-work-in-theme-root/7776

// Set save point
add_filter('acf/settings/save_json', function ($path) {

  $path = get_stylesheet_directory() . '/acf-json';

  return $path;
});


// Set load point(s) - ACF loads all.json files from multiple load points
add_filter('acf/settings/load_json', function ($paths) {

  // remove original path (optional)
  unset($paths[0]);

  // append path
  $paths[] = get_stylesheet_directory() . '/acf-json';

  return $paths;
});

