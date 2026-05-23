@extends('layouts.app')

@section('title', $pet->name . ' - iPet')

@push('styles')
<style>
    .stat-card {
        opacity: 0;
        animation: fadeInUp 0.5s ease-out forwards;
    }
    .stat-card:nth-child(1) { animation-delay: 0.1s; }
    .stat-card:nth-child(2) { animation-delay: 0.2s; }
    .stat-card:nth-child(3) { animation-delay: 0.3s; }
    .stat-card:nth-child(4) { animation-delay: 0.4s; }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- 宠物头部信息 -->
    <div class="ui-card ui-card-shadow-strong overflow-hidden mb-8 animate-fade-in-up" style="border-radius: 1.5rem;">
        <div class="h-72 bg-gradient-to-br from-primary-100 via-primary-50 to-accent-50 relative">
            @if($pet->avatar)
                <img src="{{ $pet->avatar }}" alt="{{ $pet->name }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent"></div>
            @else
                <div class="w-full h-full flex items-center justify-center">
                    <svg class="w-32 h-32 text-primary-300/60" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M4.5 9.5a2 2 0 110-4 2 2 0 010 4zM9 7a2 2 0 110-4 2 2 0 010 4zM15 7a2 2 0 110-4 2 2 0 010 4zM19.5 9.5a2 2 0 110-4 2 2 0 010 4zM6 14c0 2.5 2 4.5 6 4.5s6-2 6-4.5c0-1.5-1-2.5-2-3-1-.5-2.5-.5-4-.5s-3 0-4 .5c-1 .5-2 1.5-2 3z"/>
                    </svg>
                </div>
            @endif
            <div class="absolute bottom-0 left-0 right-0 p-8">
                <div class="flex items-end justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <h1 class="text-4xl font-display font-bold text-white drop-shadow-lg">{{ $pet->name }}</h1>
                            @if($pet->is_default)
                                <span class="bg-gradient-to-r from-yellow-400 to-yellow-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg">默认宠物</span>
                            @endif
                        </div>
                        <p class="text-white/90 text-lg font-medium">{{ $pet->species }} · {{ $pet->breed ?: '未知品种' }}</p>
                    </div>
                    @if(auth()->check() && auth()->id() === $pet->user_id)
                        <div class="hidden sm:flex items-center gap-3">
                            <a href="{{ route('pets.edit', $pet) }}" class="inline-flex items-center gap-2 bg-white/90 backdrop-blur-sm hover:bg-white text-warm-700 px-5 py-3 rounded-xl font-bold shadow-lg transition-all duration-200 hover:-translate-y-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                编辑
                            </a>
                            <a href="{{ route('posts.create', ['pet_id' => $pet->id]) }}" class="inline-flex items-center gap-2 bg-white/90 backdrop-blur-sm hover:bg-white text-primary-600 px-5 py-3 rounded-xl font-bold shadow-lg transition-all duration-200 hover:-translate-y-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                发动态
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="p-8">
            @if(session('success'))
                <div class="mb-6 p-4 bg-accent-50 border border-accent-200 rounded-xl text-accent-700 text-sm animate-fade-in-up">
                    {{ session('success') }}
                </div>
            @endif

            <!-- 移动端操作按钮 -->
            @if(auth()->check() && auth()->id() === $pet->user_id)
                <div class="flex sm:hidden gap-3 mb-6">
                    <a href="{{ route('pets.edit', $pet) }}" class="flex-1 inline-flex items-center justify-center gap-2 bg-warm-100 hover:bg-warm-200 text-warm-700 py-3 rounded-xl font-bold transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        编辑
                    </a>
                    <a href="{{ route('posts.create', ['pet_id' => $pet->id]) }}" class="flex-1 inline-flex items-center justify-center gap-2 bg-primary-500 hover:bg-primary-600 text-white py-3 rounded-xl font-bold transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        发动态
                    </a>
                </div>
            @endif

            <!-- 统计卡片 -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="stat-card bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-5 text-center border border-blue-200/50">
                    <div class="flex justify-center">
                        @if($pet->gender === 'male')
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-2xl text-blue-600">♂</span>
                        @elseif($pet->gender === 'female')
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-pink-100 text-2xl text-pink-600">♀</span>
                        @else
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-warm-100 text-2xl text-warm-500">?</span>
                        @endif
                    </div>
                    <p class="text-sm text-warm-600 font-medium mt-1">性别</p>
                </div>
                <div class="stat-card bg-gradient-to-br from-primary-50 to-primary-100 rounded-2xl p-5 text-center border border-primary-200/50">
                    <p class="text-3xl font-display font-bold text-primary-600">{{ $pet->birthday ? $pet->birthday->age : '-' }}</p>
                    <p class="text-sm text-warm-600 font-medium mt-1">年龄(岁)</p>
                </div>
                <div class="stat-card bg-gradient-to-br from-accent-50 to-accent-100 rounded-2xl p-5 text-center border border-accent-200/50">
                    <p class="text-3xl font-display font-bold text-accent-600">{{ $pet->records->count() }}</p>
                    <p class="text-sm text-warm-600 font-medium mt-1">医疗记录</p>
                </div>
                <div class="stat-card bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-5 text-center border border-purple-200/50">
                    <p class="text-3xl font-display font-bold text-purple-600">{{ $pet->posts_count }}</p>
                    <p class="text-sm text-warm-600 font-medium mt-1">动态</p>
                </div>
            </div>

            <!-- 详细信息 -->
            <div class="border-t border-warm-100 pt-8">
                <h3 class="text-xl font-display font-bold text-warm-900 mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    详细信息
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
                    <div class="flex justify-between py-3 border-b border-warm-100">
                        <span class="text-warm-500 font-medium">物种</span>
                        <span class="font-semibold text-warm-900">{{ $pet->species }}</span>
                    </div>
                    <div class="flex justify-between py-3 border-b border-warm-100">
                        <span class="text-warm-500 font-medium">品种</span>
                        <span class="font-semibold text-warm-900">{{ $pet->breed ?: '未知' }}</span>
                    </div>
                    <div class="flex justify-between py-3 border-b border-warm-100">
                        <span class="text-warm-500 font-medium">生日</span>
                        <span class="font-semibold text-warm-900">{{ $pet->birthday ? $pet->birthday->format('Y-m-d') : '未知' }}</span>
                    </div>
                    <div class="flex justify-between py-3 border-b border-warm-100">
                        <span class="text-warm-500 font-medium">到家日期</span>
                        <span class="font-semibold text-warm-900">{{ $pet->adoption_date ? $pet->adoption_date->format('Y-m-d') : '未知' }}</span>
                    </div>
                </div>
            </div>

            @if($pet->metadata)
                <div class="border-t border-warm-100 pt-8 mt-8">
                    <h3 class="text-xl font-display font-bold text-warm-900 mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-accent-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                        扩展信息
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
                        @foreach($pet->metadata as $key => $value)
                            <div class="flex justify-between py-3 border-b border-warm-100">
                                <span class="text-warm-500 font-medium">{{ $key }}</span>
                                <span class="font-semibold text-warm-900">{{ $value }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- 宠物动态 -->
    @if($petPosts->count() > 0)
        <h2 class="text-2xl font-display font-bold text-warm-900 mb-6 animate-fade-in-up">{{ $pet->name }} 的动态</h2>
        <div class="space-y-4">
            @foreach($petPosts as $post)
                @php
                    $postShowUrl = route('posts.show', $post);
                    $speciesLabel = filled($post->pet?->species) ? trim($post->pet->species) : '未分类';
                @endphp

                <div class="post-card js-post-card overflow-hidden rounded-2xl border border-warm-200 bg-white hover:border-warm-300 hover:shadow-sm transition-all duration-300 cursor-pointer animate-fade-in-up" data-href="{{ $postShowUrl }}">
                    <div class="px-5 py-4 sm:px-6 sm:py-5">
                        <x-post-header :post="$post" :species-label="$speciesLabel" :show-pet-link="true" :show-dropdown="false" :stop-propagation="true" />

                        <x-post-content :content="$post->content" :expandable="true" />

                        @if($post->location)
                            <p class="text-sm text-warm-500 mb-4 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                {{ $post->location }}
                            </p>
                        @endif

                        @if($post->tags && $post->tags->count() > 0)
                            <div class="flex flex-wrap gap-2 mb-4">
                                @foreach($post->tags as $tag)
                                    <a href="{{ route('posts.index', ['tag' => $tag->name]) }}" onclick="event.stopPropagation()" class="text-xs bg-warm-100 text-warm-600 px-3 py-1.5 rounded-lg font-medium hover:bg-primary-100 hover:text-primary-600 transition-colors">
                                        #{{ $tag->name }}
                                    </a>
                                @endforeach
                            </div>
                        @endif

                        <div onclick="event.stopPropagation()">
                            <x-post-media :post="$post" mode="feed" />
                        </div>

                        <x-post-stats :post="$post" layout="weibo" :show-view="true" :show-like="true" />
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $petPosts->links() }}
        </div>
    @endif
</div>

<script>
    document.querySelectorAll('.js-post-card').forEach((card) => {
        card.addEventListener('click', () => {
            window.location.href = card.dataset.href;
        });
    });

    document.querySelectorAll('.js-post-expand').forEach((expandButton) => {
        const content = expandButton.closest('div')?.querySelector('.js-post-content');
        if (!content) {
            return;
        }

        const computedStyle = window.getComputedStyle(content);
        const lineHeight = Number.parseFloat(computedStyle.lineHeight || '0');
        const estimatedLineCount = lineHeight > 0 ? Math.round(content.scrollHeight / lineHeight) : 0;
        const textLength = (content.textContent || '').trim().length;
        const shouldShowExpand = content.scrollHeight > content.clientHeight + 4 || estimatedLineCount > 4 || textLength > 140;

        if (!shouldShowExpand) {
            expandButton.classList.add('hidden');
            return;
        }

        expandButton.addEventListener('click', () => {
            const expanded = content.dataset.expanded === 'true';
            if (expanded) {
                content.dataset.expanded = 'false';
                content.classList.add('max-h-[6.5rem]', 'overflow-hidden');
                expandButton.textContent = '展开全文';
            } else {
                content.dataset.expanded = 'true';
                content.classList.remove('max-h-[6.5rem]', 'overflow-hidden');
                expandButton.textContent = '收起';
            }
        });
    });
</script>
@endsection
