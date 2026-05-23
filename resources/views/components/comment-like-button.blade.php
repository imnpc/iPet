@props([
    'post',
    'comment',
    'commentSort' => 'time',
])

@auth
    @php($isLiked = (bool) ($comment->is_liked ?? false))
    <form action="{{ route('posts.comments.like', ['post' => $post, 'comment' => $comment]) }}" method="POST">
        @csrf
        <input type="hidden" name="comment_sort" value="{{ $commentSort }}">
        <button
            type="submit"
            class="inline-flex items-center gap-1 rounded-full border px-2.5 py-1 font-medium transition {{ $isLiked ? 'border-primary-200 bg-primary-50 text-primary-600 hover:bg-primary-100' : 'border-warm-200 text-warm-600 hover:border-primary-300 hover:text-primary-600' }}"
        >
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 9V5a3 3 0 00-3-3l-4 9v11h11a3 3 0 003-3l1-7.5A2 2 0 0020 9h-6zM7 22H4a1 1 0 01-1-1v-9a1 1 0 011-1h3"></path></svg>
            {{ $isLiked ? '已点赞' : '点赞' }} {{ $comment->like_count }}
        </button>
    </form>
@else
    <a href="{{ route('login') }}" class="inline-flex items-center gap-1 rounded-full border border-warm-200 px-2.5 py-1 font-medium text-warm-600 hover:border-primary-300 hover:text-primary-600">
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 9V5a3 3 0 00-3-3l-4 9v11h11a3 3 0 003-3l1-7.5A2 2 0 0020 9h-6zM7 22H4a1 1 0 01-1-1v-9a1 1 0 011-1h3"></path></svg>
        点赞 {{ $comment->like_count }}
    </a>
@endauth
