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
        </div>
        <p class="mt-1 text-sm text-warm-400">主人 {{ $post->user->name }} · {{ $post->created_at->diffForHumans() }}</p>
    </div>

    <div class="flex items-center gap-2">
        @if($showDropdown)
            <button type="button" @if($stopPropagation) onclick="event.stopPropagation()" @endif class="text-warm-400 hover:text-warm-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6"></path></svg>
            </button>
        @endif
    </div>
</div>
