<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'iPet - 宠物管理系统')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=nunito:400,500,600,700,800|fredoka:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-warm-50 text-warm-900 font-sans antialiased">
    @include('components.nav')
    <main class="min-h-screen">
        @yield('content')
    </main>
    @include('components.footer')

    @php
        $isImpersonating = \STS\FilamentImpersonate\Facades\Impersonation::isImpersonating();
    @endphp

    @if($isImpersonating)
        <div class="w-full border-b border-amber-300 bg-amber-100 text-amber-900">
            <div class="mx-auto flex max-w-7xl items-center justify-center gap-3 px-4 py-2 text-sm">
                <span>当前正在模拟登录用户：<strong>{{ auth('web')->user()?->name ?? '未知用户' }}</strong></span>
                <a href="{{ route('filament-impersonate.leave') }}" class="rounded bg-amber-900 px-3 py-1 text-amber-100 hover:bg-amber-800">退出模拟登录</a>
            </div>
        </div>
    @endif

    @stack('scripts')
</body>
</html>
