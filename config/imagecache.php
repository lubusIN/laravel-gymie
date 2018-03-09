<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Name of route
    |--------------------------------------------------------------------------
    |
    | Enter the routes name to enable dynamic imagecache manipulation.
    | This handle will define the first part of the URI:
    |
    | {route}/{template}/{filename}
    |
    | Examples: "images", "img/cache"
    |
    */

    'route' => 'images',

    /*
    |--------------------------------------------------------------------------
    | Storage paths
    |--------------------------------------------------------------------------
    |
    | The following paths will be searched for the image filename, submited
    | by URI.
    |
    | Define as many directories as you like.
    |
    */

    'paths' => [
        public_path('assets/img/profile'),
        public_path('assets/img/proof'),
        public_path('assets/img/staff'),
        public_path('assets/img/gym'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Manipulation templates
    |--------------------------------------------------------------------------
    |
    | Here you may specify your own manipulation filter templates.
    | The keys of this array will define which templates
    | are available in the URI:
    |
    | {route}/{template}/{filename}
    |
    | The values of this array will define which filter class
    | will be applied, by its fully qualified name.
    |
    */

    'templates' => [
        'small'   => 'Intervention\Image\Templates\Small',
        'medium'  => 'Intervention\Image\Templates\Medium',
        'large'   => 'Intervention\Image\Templates\Large',
        '100x100' => 'App\Lubus\ImageFilters\Image100x100',
        '400x400' => 'App\Lubus\ImageFilters\Image400x400',
        '50x50'   => 'App\Lubus\ImageFilters\Image50x50',
        '64x64'   => 'App\Lubus\ImageFilters\Image64x64',
        '70x70'   => 'App\Lubus\ImageFilters\Image70x70',
        'Invoice' => 'App\Lubus\ImageFilters\Invoice',
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Cache Lifetime
    |--------------------------------------------------------------------------
    |
    | Lifetime in minutes of the images handled by the imagecache route.
    |
    */

    'lifetime' => 0,

];
