@extends('layouts.app')

@section('title', $user->name . ' 的主页 - iPet')

@push('styles')
<style>
    .user-stat-card {
        opacity: 0;
        animation: fadeInUp 0.5s ease-out forwards;
    }
    .user-stat-card:nth-child(1) { animation-delay: 0.1s; }
    .user-stat-card:nth-child(2) { animation-delay: 0.2s; }
    .user-stat-card:nth-child(3) { animation-delay: 0.3s; }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- 用户信息头部 -->
    <div class="ui-card ui-card-shadow-strong overflow-hidden mb-8 animate-fade-in-up" style="border-radius: 1.5rem;">
        <div class="h-56 bg-gradient-to-br from-primary-100 via-primary-50 to-accent-50 relative">
            <div class="absolute inset-0 flex items-center justify-center opacity-20">
                <svg class="w-48 h-48 text-primary-300/40" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div class="absolute bottom-0 left-0 right-0 p-8">
                <div class="flex items-end gap-5">
                    <div class="flex h-20 w-20 items-center justify-center rounded-full bg-white shadow-lg ring-4 ring-white/50">
                        <svg class="h-10 w-10 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div class="pb-2">
                        <h1 class="text-4xl font-display font-bold text-white drop-shadow-lg">{{ $user->name }}</h1>
                        <p class="text-white/90 text-lg font-medium">公开动态 {{ $posts->total() }} 条</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-8">
            <!-- 统计卡片 -->
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-8">
                <div class="user-stat-card bg-gradient-to-br from-primary-50 to-primary-100 rounded-2xl p-5 text-center border border-primary-200/50">
                    <p class="text-3xl font-display font-bold text-primary-600">{{ $posts->total() }}</p>
                    <p class="text-sm text-warm-600 font-medium mt-1">公开动态</p>
                </div>
                <div class="user-stat-card bg-gradient-to-br from-accent-50 to-accent-100 rounded-2xl p-5 text-center border border-accent-200/50">
                    <p class="text-3xl font-display font-bold text-accent-600">{{ $posts->sum(fn($p) => $p->comment_count ?? 0) }}</p>
                    <p class="text-sm text-warm-600 font-medium mt-1">收到评论</p>
                </div>
                <div class="user-stat-card bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-5 text-center border border-purple-200/50">
                    <p class="text-3xl font-display font-bold text-purple-600">{{ $posts->sum(fn($p) => $p->like_count ?? 0) }}</p>
                    <p class="text-sm text-warm-600 font-medium mt-1">获得点赞</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 返回按钮 -->
    <div class="mb-6">
        <a href="{{ route('posts.index') }}" class="inline-flex items-center gap-2 text-warm-500 hover:text-primary-600 font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            返回动态广场
        </a>
    </div>

    @if($posts->count() > 0)
        <div class="space-y-4">
            @foreach($posts as $post)
                @php
                    $sortedMedia = $post->media->sortBy('sort_order')->values();
                    $allImages = $sortedMedia->where('type', 'image')->values();
                    $previewImages = $allImages->take(4);
                    $hiddenImageCount = max($allImages->count() - 4, 0);
                    $firstVideo = $sortedMedia->where('type', 'video')->first();
                @endphp

                <a href="{{ route('posts.show', $post) }}" class="ui-card ui-card-shadow block p-5 hover:shadow-md transition-shadow">
                    <p class="text-warm-800 leading-relaxed line-clamp-3">{{ $post->content }}</p>

                    @if($previewImages->isNotEmpty())
                        <div class="mt-3 grid grid-cols-4 gap-2">
                            @foreach($previewImages as $index => $media)
                                <div class="relative aspect-square overflow-hidden rounded-lg bg-warm-100">
                                    <img src="{{ $media->url() }}" alt="动态图片" class="h-full w-full object-cover">
                                    @if($hiddenImageCount > 0 && $index === 3)
                                        <div class="absolute inset-0 flex items-center justify-center bg-black/45 text-sm font-bold text-white">+{{ $hiddenImageCount }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if($firstVideo)
                        <div class="mt-3 relative h-36 overflow-hidden rounded-lg bg-black">
                            @if($firstVideo->thumbnailUrl())
                                <img src="{{ $firstVideo->thumbnailUrl() }}" alt="视频封面" class="h-full w-full object-cover opacity-80">
                            @endif
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white/25">
                                    <svg class="h-5 w-5 text-white ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="mt-3 flex items-center justify-between text-xs text-warm-500">
                        <span>{{ $post->published_at?->diffForHumans() }}</span>
                        <span>评论 {{ $post->comment_count }} · 点赞 {{ $post->like_count }}</span>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $posts->links() }}
        </div>
    @else
        <div class="ui-card ui-card-shadow p-10 text-center text-warm-500">
            该用户还没有公开动态
        </div>
    @endif
</div>
@endsection
