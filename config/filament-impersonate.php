<?php

return [
    // 模拟登录后跳转到前台首页（web guard 上下文）
    'redirect_to' => env('FILAMENT_IMPERSONATE_REDIRECT', '/'),

    // 退出模拟登录路由使用 web 中间件
    'leave_middleware' => env('FILAMENT_IMPERSONATE_LEAVE_MIDDLEWARE', 'web'),

    // 路由前缀（默认不加）
    'route_prefix' => env('FILAMENT_IMPERSONATE_ROUTE_PREFIX', null),

    // 是否允许模拟已软删除用户
    'allow_soft_deleted' => env('FILAMENT_IMPERSONATE_ALLOW_SOFT_DELETED', false),

    'banner' => [
        'render_hook' => env('FILAMENT_IMPERSONATE_BANNER_RENDER_HOOK', 'panels::body.start'),
        'style' => env('FILAMENT_IMPERSONATE_BANNER_STYLE', 'dark'),
        'fixed' => env('FILAMENT_IMPERSONATE_BANNER_FIXED', true),
        'position' => env('FILAMENT_IMPERSONATE_BANNER_POSITION', 'top'),
        'styles' => [
            'light' => [
                'text' => '#1f2937',
                'background' => '#f3f4f6',
                'border' => '#e8eaec',
            ],
            'dark' => [
                'text' => '#f3f4f6',
                'background' => '#1f2937',
                'border' => '#374151',
            ],
        ],
    ],
];
