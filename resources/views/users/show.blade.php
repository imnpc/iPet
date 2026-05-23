@extends('layouts.app')

@section('title', $user->name . ' 的主页 - iPet')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8">
        <a href="{{ route('posts.index') }}" class="inline-flex items-center gap-2 text-warm-500 hover:text-primary-600 font-medium transition-colors mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            返回动态广场
        </a>

        <div class="ui-card ui-card-shadow p-6">
            <h1 class="ui-page-title">{{ $user->name }}</h1>
            <p class="ui-page-subtitle">公开动态 {{ $posts->total() }} 条</p>
        </div>
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
