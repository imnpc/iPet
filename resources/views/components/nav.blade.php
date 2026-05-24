<nav class="sticky top-0 z-50 border-b border-warm-200/70 bg-white/90 backdrop-blur-md shadow-sm">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center gap-3">
            <a href="{{ url('/') }}" class="flex items-center gap-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-primary-400 to-primary-600 text-white shadow-md">
                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M4.5 9.5a2 2 0 110-4 2 2 0 010 4zM9 7a2 2 0 110-4 2 2 0 010 4zM15 7a2 2 0 110-4 2 2 0 010 4zM19.5 9.5a2 2 0 110-4 2 2 0 010 4zM6 14c0 2.5 2 4.5 6 4.5s6-2 6-4.5c0-1.5-1-2.5-2-3-1-.5-2.5-.5-4-.5s-3 0-4 .5c-1 .5-2 1.5-2 3z"/>
                    </svg>
                </div>
                <span class="hidden text-2xl font-display font-bold text-primary-700 sm:block">iPet</span>
            </a>

            <div class="hidden flex-1 md:block">
                <form action="{{ route('posts.index') }}" method="GET" class="relative mx-auto w-full max-w-md">
                    <svg class="pointer-events-none absolute left-3 top-2.5 h-4 w-4 text-warm-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="搜索宠物、动态、地点" class="h-9 w-full rounded-full border border-warm-200 bg-warm-50 pl-9 pr-3 text-sm text-warm-700 placeholder:text-warm-400 focus:border-primary-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-100" />
                </form>
            </div>

            <div class="hidden items-center gap-1 md:flex">
                <a href="{{ route('posts.index') }}" @class([
                    'rounded-lg px-3 py-2 text-sm font-semibold transition-colors',
                    'bg-primary-50 text-primary-700' => request()->routeIs('posts.index', 'posts.show'),
                    'text-warm-600 hover:bg-warm-100 hover:text-primary-700' => !request()->routeIs('posts.index', 'posts.show'),
                ])>首页</a>
                <a href="{{ route('pets.index') }}" @class([
                    'rounded-lg px-3 py-2 text-sm font-semibold transition-colors',
                    'bg-primary-50 text-primary-700' => request()->routeIs('pets.*'),
                    'text-warm-600 hover:bg-warm-100 hover:text-primary-700' => !request()->routeIs('pets.*'),
                ])>宠物</a>
                <a href="{{ route('posts.create') }}" class="rounded-lg bg-primary-600 px-3 py-2 text-sm font-semibold text-white hover:bg-primary-700">发布</a>
            </div>

            <div class="ml-auto flex items-center gap-2">
                @auth
                    <details class="relative">
                        <summary class="flex cursor-pointer list-none items-center gap-2 rounded-xl p-1.5 hover:bg-warm-100 transition-colors">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-primary-100 text-primary-600 ring-2 ring-primary-200/50">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <span class="hidden text-sm font-semibold text-warm-700 sm:block">{{ auth()->user()->name }}</span>
                            <svg class="h-4 w-4 text-warm-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </summary>
                        <div class="absolute right-0 z-50 mt-2 w-48 rounded-xl border border-warm-100 bg-white py-2 shadow-xl">
                            <a href="{{ route('pets.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-warm-700 hover:bg-warm-50">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M4.5 9.5a2 2 0 110-4 2 2 0 010 4zM9 7a2 2 0 110-4 2 2 0 010 4zM15 7a2 2 0 110-4 2 2 0 010 4zM19.5 9.5a2 2 0 110-4 2 2 0 010 4zM6 14c0 2.5 2 4.5 6 4.5s6-2 6-4.5c0-1.5-1-2.5-2-3-1-.5-2.5-.5-4-.5s-3 0-4 .5c-1 .5-2 1.5-2 3z"/></svg>
                                我的宠物
                            </a>
                            <a href="{{ route('settings') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-warm-700 hover:bg-warm-50">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                账号设置
                            </a>
                            <div class="my-1 border-t border-warm-100"></div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-danger-600 hover:bg-danger-50">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                    退出登录
                                </button>
                            </form>
                        </div>
                    </details>
                @else
                    <a href="{{ route('login') }}" class="px-3 py-2 text-sm font-semibold text-warm-600 hover:text-primary-600">登录</a>
                    <a href="{{ route('register') }}" class="rounded-lg bg-primary-600 px-3 py-2 text-sm font-semibold text-white hover:bg-primary-700">注册</a>
                @endauth
            </div>
        </div>

        <div class="flex items-center gap-1 pb-2 md:hidden">
            <a href="{{ route('posts.index') }}" @class([
                'flex-1 rounded-lg px-3 py-2 text-center text-xs font-semibold',
                'bg-primary-50 text-primary-700' => request()->routeIs('posts.index', 'posts.show'),
                'text-warm-600 bg-warm-100' => !request()->routeIs('posts.index', 'posts.show'),
            ])>首页</a>
            <a href="{{ route('pets.index') }}" @class([
                'flex-1 rounded-lg px-3 py-2 text-center text-xs font-semibold',
                'bg-primary-50 text-primary-700' => request()->routeIs('pets.*'),
                'text-warm-600 bg-warm-100' => !request()->routeIs('pets.*'),
            ])>宠物</a>
            <a href="{{ route('posts.create') }}" class="flex-1 rounded-lg bg-primary-600 px-3 py-2 text-center text-xs font-semibold text-white">发布</a>
        </div>
    </div>
</nav>
