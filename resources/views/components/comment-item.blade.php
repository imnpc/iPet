@props([
    'comment',
    'post',
    'commentSort' => 'time',
])

<div class="flex gap-3">
    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-primary-100 to-primary-200 flex items-center justify-center text-primary-600 flex-shrink-0">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
    </div>
    <div class="flex-1 relative">
        <div class="absolute right-0 top-0 text-xs" onclick="event.stopPropagation()">
            <x-comment-like-button :post="$post" :comment="$comment" :comment-sort="$commentSort" />
        </div>

        <div class="mb-1 flex items-center gap-2 pr-24">
            <span class="font-semibold text-warm-900 text-sm">{{ $comment->user->name }}</span>
            <span class="text-xs text-warm-400">{{ $comment->created_at->diffForHumans() }}</span>
        </div>
        <p class="text-warm-700 text-sm leading-relaxed">{{ $comment->content }}</p>

        @auth
            @if(($post->allow_comment ?? true) !== false)
                <details class="mt-2" data-reply-to="{{ $comment->user->name }}">
                    <summary class="cursor-pointer text-xs font-medium text-primary-600 hover:text-primary-700">回复</summary>
                    <form action="{{ route('posts.comments.store', $post) }}" method="POST" class="mt-2 space-y-2 rounded-lg border border-warm-200 bg-warm-50 p-3">
                        @csrf
                        <input type="hidden" name="comment_sort" value="{{ $commentSort }}">
                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                        <textarea
                            name="content"
                            rows="2"
                            maxlength="1000"
                            placeholder="回复 {{ $comment->user->name }}..."
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

        @if($comment->children->count() > 0)
            <details class="js-children-toggle mt-3" open>
                <summary class="cursor-pointer text-xs font-medium text-primary-600 hover:text-primary-700" data-open-text="收起回复" data-close-text="展开回复（{{ $comment->children->count() }}）">收起回复</summary>
                <div class="mt-2 ml-4 pl-4 border-l-2 border-warm-200 space-y-3">
                    @foreach($comment->children as $child)
                        <x-comment-child-item :child="$child" :comment="$comment" :post="$post" :comment-sort="$commentSort" />
                    @endforeach
                </div>
            </details>
        @endif
    </div>
</div>
