@props([
    'post',
    'commentSort' => 'time',
])

<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <h2 class="text-xl font-display font-bold text-warm-900 flex items-center gap-2">
        <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
        评论 ({{ $post->comment_count }})
    </h2>
    <div class="inline-flex items-center rounded-xl border border-warm-200 bg-white p-1 text-xs">
        <a href="{{ route('posts.show', ['post' => $post, 'comment_sort' => 'time']) }}" class="rounded-lg px-3 py-1 font-semibold {{ $commentSort === 'time' ? 'bg-primary-100 text-primary-700' : 'text-warm-500 hover:text-primary-600' }}">按时间</a>
        <a href="{{ route('posts.show', ['post' => $post, 'comment_sort' => 'hot']) }}" class="rounded-lg px-3 py-1 font-semibold {{ $commentSort === 'hot' ? 'bg-primary-100 text-primary-700' : 'text-warm-500 hover:text-primary-600' }}">按热度</a>
    </div>
</div>
