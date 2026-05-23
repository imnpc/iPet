@props([
    'child',
    'comment',
    'post',
    'commentSort' => 'time',
])

<div class="flex gap-2">
    <div class="w-7 h-7 rounded-full bg-warm-100 flex items-center justify-center text-warm-500 flex-shrink-0">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
    </div>
    <div class="relative flex-1">
        <div class="absolute right-0 top-0 text-xs" onclick="event.stopPropagation()">
            <x-comment-like-button :post="$post" :comment="$child" :comment-sort="$commentSort" />
        </div>

        <div class="mb-1 flex items-center gap-2 pr-24">
            <span class="font-semibold text-warm-900 text-sm">{{ $child->user->name }}</span>
            <span class="text-xs text-warm-400">{{ $child->created_at->diffForHumans() }}</span>
        </div>
        <p class="text-warm-700 text-sm">
            <span class="text-primary-600">回复 <a href="{{ route('users.show', $comment->user) }}" class="font-semibold hover:text-primary-700" onclick="event.stopPropagation()">{{ '@' . $comment->user->name }}</a>：</span>{{ $child->content }}
        </p>

        @auth
            @if(($post->allow_comment ?? true) !== false)
                <details class="mt-2" data-reply-to="{{ $child->user->name }}">
                    <summary class="cursor-pointer text-xs font-medium text-primary-600 hover:text-primary-700">回复</summary>
                    <form action="{{ route('posts.comments.store', $post) }}" method="POST" class="mt-2 space-y-2 rounded-lg border border-warm-200 bg-white p-3">
                        @csrf
                        <input type="hidden" name="comment_sort" value="{{ $commentSort }}">
                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                        <textarea
                            name="content"
                            rows="2"
                            maxlength="1000"
                            placeholder="回复 {{ $child->user->name }}..."
                            class="js-reply-textarea w-full border-warm-200 rounded-lg shadow-sm focus:border-primary-400 focus:ring-2 focus:ring-primary-200 transition-all duration-200 resize-none"
                            required
                        ></textarea>
                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-primary-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-primary-700">
                                发布回复
                            </button>
                        </div>
                    </form>
                </details>
            @endif
        @endauth
    </div>
</div>
