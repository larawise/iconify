<?php

return [
    'throw'                                     => (bool) env('ICONIFY_THROW', true),

    'icon_threshold'                            => (int) env('ICONIFY_ICON_THRESHOLD', 10),
    'package_threshold'                            => (int) env('ICONIFY_PACKAGE_THRESHOLD', 3),

    'fallback'                                  => [
        'status'    => (bool) env('ICONIFY_FALLBACK_STATUS', true),
        'package'   => env('ICONIFY_FALLBACK_PACKAGE', 'iconify'),
        'icon'      => env('ICONIFY_FALLBAC_ICONK', 'iconify'),
    ],

    /*
    |--------------------------------------------------------------------------
    | ℹ️ Iconify (Storage)
    |--------------------------------------------------------------------------
    |
    | Specify one or more directories where Iconify JSON icon sets are stored.
    | These paths will be scanned during the update process to locate and compile
    | icon definitions. You can override this to support custom storage locations
    | or multiple sources.
    |
    */
    'paths'                                     => [storage_path('app/collections/iconify')],

    /*
    |--------------------------------------------------------------------------
    | ℹ️ Iconify (Compiled)
    |--------------------------------------------------------------------------
    |
    | Define the path where compiled icon data should be cached. This file is
    | used to optimize performance by avoiding repeated parsing of icon sets.
    | You can safely clear this file if you want to regenerate the cache.
    |
    */
    'compiled'                                  => storage_path('framework/cache'),
];
