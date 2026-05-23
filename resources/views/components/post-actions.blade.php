@props([
    'post',
    'layout' => 'default',
    'showView' => true,
    'showLike' => true,
    'alignEnd' => true,
    'size' => 'sm',
    'isLiked' => false,
])

@php
    $isCompact = $size === 'xs';
    $paddingClass = $isCompact ? 'px-2.5 py-1 text-xs' : 'px-3 py-1.5 text-sm';
    $gapClass = $isCompact ? 'gap-1' : 'gap-1.5';
@endphp

@if($layout === 'weibo')
    <div class="grid grid-cols-3 border-t border-warm-100 pt-2 text-sm">
        <a href="{{ route('posts.show', $post) }}" class="inline-flex items-center justify-center gap-1.5 rounded-lg py-2 text-warm-500 hover:bg-warm-50 hover:text-primary-600" onclick="event.stopPropagation()">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.5 8.25h9m-9 3h6.75M3 11.25c0-4.142 3.358-7.5 7.5-7.5h3c4.142 0 7.5 3.358 7.5 7.5v.75c0 4.142-3.358 7.5-7.5 7.5h-1.543a1.5 1.5 0 00-.88.285l-2.17 1.553a.75.75 0 01-1.177-.61v-1.35a1.5 1.5 0 00-1.5-1.5C4.007 17.883 3 16.876 3 15.633v-4.383z"></path></svg>
            <span>{{ $post->comment_count }}</span>
        </a>

        @if($showView)
            <span class="inline-flex items-center justify-center gap-1.5 rounded-lg py-2 text-warm-500">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                <span>{{ $post->view_count }}</span>
            </span>
        @else
            <span></span>
        @endif

        @if($showLike)
            @auth
                <form action="{{ route('posts.like', $post) }}" method="POST" class="flex" onclick="event.stopPropagation()">
                    @csrf
                    <input type="hidden" name="return_to" value="{{ request()->fullUrl() }}">
                    <button type="submit" class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg py-2 transition-colors {{ $isLiked ? 'text-rose-500 hover:bg-rose-50' : 'text-warm-500 hover:bg-warm-50 hover:text-rose-500' }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 8.25c0-2.485-2.236-4.5-4.995-4.5A5.266 5.266 0 0012 5.67a5.266 5.266 0 00-4.005-1.92C5.236 3.75 3 5.765 3 8.25c0 7.22 9 11.25 9 11.25s9-4.03 9-11.25z"></path></svg>
                        <span>{{ $post->like_count }}</span>
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-1.5 rounded-lg py-2 text-warm-500 hover:bg-warm-50 hover:text-rose-500" onclick="event.stopPropagation()">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 8.25c0-2.485-2.236-4.5-4.995-4.5A5.266 5.266 0 0012 5.67a5.266 5.266 0 00-4.005-1.92C5.236 3.75 3 5.765 3 8.25c0 7.22 9 11.25 9 11.25s9-4.03 9-11.25z"></path></svg>
                    <span>{{ $post->like_count }}</span>
                </a>
            @endauth
        @else
            <span></span>
        @endif
    </div>
@else
    <div class="flex flex-wrap items-center gap-2.5 pt-3 border-t border-warm-100">
        <span class="inline-flex items-center {{ $gapClass }} rounded-full bg-sky-50 {{ $paddingClass }} text-sky-500">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.5 8.25h9m-9 3h6.75M3 11.25c0-4.142 3.358-7.5 7.5-7.5h3c4.142 0 7.5 3.358 7.5 7.5v.75c0 4.142-3.358 7.5-7.5 7.5h-1.543a1.5 1.5 0 00-.88.285l-2.17 1.553a.75.75 0 01-1.177-.61v-1.35a1.5 1.5 0 00-1.5-1.5C4.007 17.883 3 16.876 3 15.633v-4.383z"></path></svg>
            <span class="font-semibold">{{ $post->comment_count }}</span>
        </span>

        @if($showView)
            <span class="inline-flex items-center {{ $gapClass }} rounded-full bg-primary-50 {{ $paddingClass }} text-primary-600">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                <span class="font-semibold">{{ $post->view_count }}</span>
            </span>
        @endif

        @if($showLike)
            @auth
                <form action="{{ route('posts.like', $post) }}" method="POST" @class(['ml-auto' => $alignEnd])>
                    @csrf
                    <input type="hidden" name="return_to" value="{{ request()->fullUrl() }}">
                    <button type="submit" @class([
                        'inline-flex items-center rounded-full transition-colors',
                        $gapClass,
                        $paddingClass,
                        'bg-rose-100 text-rose-600 hover:bg-rose-200' => $isLiked,
                        'bg-rose-50 text-rose-500 hover:bg-rose-100' => ! $isLiked,
                    ])>
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 8.25c0-2.485-2.236-4.5-4.995-4.5A5.266 5.266 0 0012 5.67a5.266 5.266 0 00-4.005-1.92C5.236 3.75 3 5.765 3 8.25c0 7.22 9 11.25 9 11.25s9-4.03 9-11.25z"></path></svg>
                        <span class="font-semibold">{{ $isLiked ? '已赞' : '点赞' }} {{ $post->like_count }}</span>
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" @class([
                    'inline-flex items-center rounded-full bg-rose-50 text-rose-500 hover:bg-rose-100 transition-colors',
                    $gapClass,
                    $paddingClass,
                    'ml-auto' => $alignEnd,
                ])>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 8.25c0-2.485-2.236-4.5-4.995-4.5A5.266 5.266 0 0012 5.67a5.266 5.266 0 00-4.005-1.92C5.236 3.75 3 5.765 3 8.25c0 7.22 9 11.25 9 11.25s9-4.03 9-11.25z"></path></svg>
                    <span class="font-semibold">点赞 {{ $post->like_count }}</span>
                </a>
            @endauth
        @endif
    </div>
@endif
