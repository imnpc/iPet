@extends('layouts.app')

@section('title', '我的宠物 - iPet')

@push('styles')
<style>
    .pet-card {
        opacity: 0;
        animation: fadeInUp 0.5s ease-out forwards;
    }
    .pet-card:nth-child(1) { animation-delay: 0.1s; }
    .pet-card:nth-child(2) { animation-delay: 0.2s; }
    .pet-card:nth-child(3) { animation-delay: 0.3s; }
    .pet-card:nth-child(4) { animation-delay: 0.4s; }
    .pet-card:nth-child(5) { animation-delay: 0.5s; }
    .pet-card:nth-child(6) { animation-delay: 0.6s; }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-[220px_minmax(0,1fr)_260px] lg:gap-5">
        <aside class="hidden lg:block">
            <div class="sticky top-20 ui-card ui-card-shadow p-4">
                <h2 class="mb-3 text-sm font-bold text-warm-900">快捷入口</h2>
                <div class="space-y-2 text-sm">
                    <a href="{{ route('pets.index') }}" class="flex items-center gap-2 rounded-lg bg-primary-50 px-3 py-2 font-semibold text-primary-700">🐾 我的宠物</a>
                    <a href="{{ route('posts.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-warm-700 hover:bg-warm-100">📰 动态广场</a>
                    <a href="{{ route('pets.create') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-warm-700 hover:bg-warm-100">➕ 添加宠物</a>
                </div>
            </div>
        </aside>

        <div>
            <div class="ui-card ui-card-shadow mb-6 p-4">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="ui-page-title">我的宠物</h1>
                        <p class="ui-page-subtitle">像信息流一样管理宠物档案，查找更直观</p>
                    </div>
                    <a href="{{ route('pets.create') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-primary-600 px-3.5 py-2 text-sm font-semibold text-white hover:bg-primary-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        添加宠物
                    </a>
                </div>
            </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-accent-50 border border-accent-200 rounded-xl text-accent-700 text-sm animate-fade-in-up">
            {{ session('success') }}
        </div>
    @endif

    @if($pets->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($pets as $pet)
                <div class="pet-card group ui-card ui-card-shadow overflow-hidden transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl">
                    <a href="{{ route('pets.show', $pet) }}" class="block">
                        <div class="h-52 bg-gradient-to-br from-primary-50 to-accent-50 relative overflow-hidden">
                            @if($pet->avatar)
                                <img src="{{ $pet->avatar }}" alt="{{ $pet->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-20 h-20 text-primary-300" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M4.5 9.5a2 2 0 110-4 2 2 0 010 4zM9 7a2 2 0 110-4 2 2 0 010 4zM15 7a2 2 0 110-4 2 2 0 010 4zM19.5 9.5a2 2 0 110-4 2 2 0 010 4zM6 14c0 2.5 2 4.5 6 4.5s6-2 6-4.5c0-1.5-1-2.5-2-3-1-.5-2.5-.5-4-.5s-3 0-4 .5c-1 .5-2 1.5-2 3z"/>
                                    </svg>
                                </div>
                            @endif
                            @if($pet->is_default)
                                <span class="absolute top-3 left-3 bg-gradient-to-r from-yellow-400 to-yellow-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-md">默认</span>
                            @endif
                            <span class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm text-warm-700 text-xs font-semibold px-3 py-1.5 rounded-full shadow-sm">{{ $pet->species }}</span>
                        </div>
                        <div class="p-5">
                            <div class="flex justify-between items-start mb-3">
                                <h3 class="text-xl font-display font-bold text-warm-900 group-hover:text-primary-600 transition-colors">{{ $pet->name }}</h3>
                                <span class="text-sm text-warm-500 font-medium">{{ $pet->breed ?: '未知品种' }}</span>
                            </div>
                            <div class="flex items-center gap-4 text-sm text-warm-500">
                                <span class="flex items-center gap-1">
                                    @if($pet->gender === 'male')
                                        <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-blue-100 text-sm text-blue-600">♂</span>
                                    @elseif($pet->gender === 'female')
                                        <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-pink-100 text-sm text-pink-600">♀</span>
                                    @else
                                        <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-warm-100 text-sm text-warm-500">?</span>
                                    @endif
                                    {{ $pet->gender === 'male' ? '公' : ($pet->gender === 'female' ? '母' : '未知') }}
                                </span>
                                @if($pet->birthday)
                                    <span>{{ $pet->birthday->age }} 岁</span>
                                @endif
                                <span>{{ $pet->records->count() }} 条记录</span>
                            </div>
                        </div>
                    </a>
                    <div class="px-5 pb-4 flex gap-2">
                        <a href="{{ route('pets.edit', $pet) }}" class="flex-1 inline-flex items-center justify-center gap-1.5 text-sm font-semibold text-primary-600 hover:text-primary-700 bg-primary-50 hover:bg-primary-100 py-2 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            编辑
                        </a>
                        <form action="{{ route('pets.destroy', $pet) }}" method="POST" class="flex-1" onsubmit="return confirm('确定要删除 {{ $pet->name }} 吗？')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 text-sm font-semibold text-danger-600 hover:text-danger-700 bg-danger-50 hover:bg-danger-100 py-2 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                删除
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-24 animate-fade-in-up">
            <div class="w-32 h-32 mx-auto mb-8 bg-gradient-to-br from-primary-100 to-accent-100 rounded-full flex items-center justify-center">
                <svg class="w-16 h-16 text-primary-400" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M4.5 9.5a2 2 0 110-4 2 2 0 010 4zM9 7a2 2 0 110-4 2 2 0 010 4zM15 7a2 2 0 110-4 2 2 0 010 4zM19.5 9.5a2 2 0 110-4 2 2 0 010 4zM6 14c0 2.5 2 4.5 6 4.5s6-2 6-4.5c0-1.5-1-2.5-2-3-1-.5-2.5-.5-4-.5s-3 0-4 .5c-1 .5-2 1.5-2 3z"/>
                </svg>
            </div>
            <h3 class="text-2xl font-display font-bold text-warm-900 mb-3">还没有宠物</h3>
            <p class="text-warm-500 mb-8 text-lg">添加您的第一只宠物，开始记录美好时光</p>
            <a href="{{ route('pets.create') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white px-8 py-4 rounded-xl font-bold text-lg shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                添加宠物
            </a>
        </div>
    @endif
        </div>

        <aside class="hidden lg:block">
            <div class="sticky top-20 space-y-4">
                <div class="ui-card ui-card-shadow p-4">
                    <h3 class="mb-3 text-sm font-bold text-warm-900">数据概览</h3>
                    <ul class="space-y-2 text-xs text-warm-500">
                        <li>· 宠物数量：{{ $pets->count() }}</li>
                        <li>· 默认宠物：{{ $pets->where('is_default', true)->count() }}</li>
                        <li>· 可从左侧快速跳转动态广场</li>
                    </ul>
                </div>
                <div class="ui-card ui-card-shadow p-4">
                    <h3 class="mb-3 text-sm font-bold text-warm-900">使用提示</h3>
                    <ul class="space-y-2 text-xs text-warm-500">
                        <li>· 点击卡片进入宠物详情页</li>
                        <li>· 卡片底部可直接编辑或删除</li>
                        <li>· 添加新宠物后可发布专属动态</li>
                    </ul>
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
