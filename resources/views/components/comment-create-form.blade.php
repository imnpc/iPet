@props([
    'post',
    'commentSort' => 'time',
])

@if(($post->allow_comment ?? true) === false)
    <div class="mb-6 rounded-xl border border-warm-200 bg-warm-50 px-4 py-3 text-sm text-warm-600">
        该动态已关闭评论
    </div>
@else
    @auth
        <form id="post-comment-form" action="{{ route('posts.comments.store', $post) }}" method="POST" class="mb-6 space-y-3">
            @csrf
            <input type="hidden" name="comment_sort" value="{{ $commentSort }}">
            <textarea
                id="post-comment-content"
                name="content"
                rows="3"
                maxlength="1000"
                placeholder="写下你的评论..."
                class="w-full border-warm-200 rounded-xl shadow-sm focus:border-primary-400 focus:ring-2 focus:ring-primary-200 transition-all duration-200 resize-none"
                required
            >{{ old('content') }}</textarea>
            <div class="flex items-center justify-between gap-3">
                @error('content')
                    <p class="text-sm text-danger-600">{{ $message }}</p>
                @else
                    <p class="text-sm text-warm-500">最多 1000 字</p>
                @enderror
                <button
                    id="post-comment-submit"
                    type="submit"
                    class="ml-auto inline-flex items-center justify-center rounded-xl bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700"
                >
                    发布评论
                </button>
            </div>
        </form>
    @else
        <div class="mb-6 rounded-xl border border-warm-200 bg-warm-50 px-4 py-3 text-sm text-warm-600">
            请先 <a class="font-semibold text-primary-600 hover:text-primary-700" href="{{ route('login') }}">登录</a> 后发表评论
        </div>
    @endauth
@endif
