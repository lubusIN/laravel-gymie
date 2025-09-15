<?php

return [
    'navigation' => [
        'token' => [
            'cluster' => null,
            'group' => 'User',
            'sort' => -1,
            'icon' => 'heroicon-o-key',
            'should_register_navigation' => false,
        ],
    ],
    'models' => [
        'token' => [
            'enable_policy' => false,
        ],
    ],
    'route' => [
        'panel_prefix' => true,
        'use_resource_middlewares' => false,
    ],
    'tenancy' => [
        'enabled' => false,
        'awareness' => false,
    ],
    'login-rules' => [
        'email' => 'required|email',
        'password' => 'required',
    ],
    'login-middleware' => [
        // Add any additional middleware you want to apply to the login route
    ],
    'logout-middleware' => [
        'auth:sanctum',
        // Add any additional middleware you want to apply to the logout route
    ],
    'use-spatie-permission-middleware' => true,
];
