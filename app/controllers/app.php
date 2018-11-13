<?php
namespace App;

add_filter('sage/template/app/data', function ($data) {
    $data['title'] = tite();
    return $data;
});
