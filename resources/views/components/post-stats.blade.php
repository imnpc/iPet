@props([
    'post',
    'showTime' => false,
    'showView' => true,
    'showLike' => true,
    'alignEnd' => true,
    'size' => 'sm',
    'layout' => 'default',
])

@php
    $isLiked = (bool) ($post->is_liked ?? false);
@endphp

@if($showTime)
    <div class="mb-2 flex flex-wrap items-center gap-2">
        <span class="inline-flex items-center gap-1 rounded-full bg-warm-100 px-2.5 py-1 text-xs text-warm-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="font-semibold">{{ $post->created_at->diffForHumans() }}</span>
        </span>
    </div>
@endif

<x-post-actions
    :post="$post"
    :layout="$layout"
    :show-view="$showView"
    :show-like="$showLike"
    :align-end="$alignEnd"
    :size="$size"
    :is-liked="$isLiked"
/>
