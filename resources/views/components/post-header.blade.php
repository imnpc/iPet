@props([
    'post',
    'speciesLabel' => '未分类',
    'showPetLink' => true,
    'showDropdown' => false,
    'stopPropagation' => false,
])

<div class="mb-4 flex items-start gap-3">
    @if($post->pet?->avatar)
        <img src="{{ $post->pet->avatar }}" alt="{{ $post->pet->name }}" class="h-12 w-12 rounded-full object-cover shadow-sm ring-2 ring-primary-100">
    @else
        <div class="h-12 w-12 rounded-full bg-gradient-to-br from-primary-100 to-accent-100 flex items-center justify-center text-xl shadow-sm ring-2 ring-primary-50">
            🐾
        </div>
    @endif

    <div class="min-w-0 flex-1">
        <div class="flex items-center gap-2">
            @if($showPetLink && $post->pet)
                <a href="{{ route('pets.show', $post->pet) }}" @if($stopPropagation) onclick="event.stopPropagation()" @endif class="truncate text-base font-bold text-warm-900 hover:text-primary-600 transition-colors">
                    {{ $post->pet->name }}
                </a>
            @else
                <p class="truncate text-base font-bold text-warm-900">{{ $post->pet?->name ?? $post->user->name }}</p>
            @endif
            <span class="inline-flex items-center rounded-full bg-accent-50 px-2.5 py-1 text-xs font-semibold text-accent-700">{{ $speciesLabel }}</span>

            @if($post->visibility === 'public')
                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800 ring-1 ring-emerald-200">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    公开
                </span>
            @elseif($post->visibility === 'followers')
                <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800 ring-1 ring-amber-200">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    粉丝可见
                </span>
            @elseif($post->visibility === 'private')
                <span class="inline-flex items-center gap-1 rounded-full bg-violet-100 px-2.5 py-1 text-xs font-semibold text-violet-800 ring-1 ring-violet-200">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    私有
                </span>
            @endif
        </div>
        <p class="mt-1 text-sm text-warm-400">主人 {{ $post->user->name }} · {{ $post->published_at?->format('Y年m月d日 H:i') ?? '未发布' }}</p>
    </div>

    <div class="flex items-center gap-2">
        @if($showDropdown)
            <button type="button" @if($stopPropagation) onclick="event.stopPropagation()" @endif class="text-warm-400 hover:text-warm-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6"></path></svg>
            </button>
        @endif
    </div>
</div>
