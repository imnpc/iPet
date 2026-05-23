@extends('layouts.app')

@section('title', '动态广场 - iPet')

@push('styles')
<style>
    .post-card {
        opacity: 0;
        animation: fadeInUp 0.5s ease-out forwards;
    }
    .post-card:nth-child(1) { animation-delay: 0.1s; }
    .post-card:nth-child(2) { animation-delay: 0.15s; }
    .post-card:nth-child(3) { animation-delay: 0.2s; }
    .post-card:nth-child(4) { animation-delay: 0.25s; }
    .post-card:nth-child(5) { animation-delay: 0.3s; }
    .post-card:nth-child(6) { animation-delay: 0.35s; }
    .post-card:nth-child(7) { animation-delay: 0.4s; }
    .post-card:nth-child(8) { animation-delay: 0.45s; }
    .post-card:nth-child(9) { animation-delay: 0.5s; }
    .post-card:nth-child(10) { animation-delay: 0.55s; }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
    @php
        $speciesFilters = collect($speciesOptions ?? [])
            ->map(fn ($species) => trim((string) $species))
            ->filter()
            ->unique()
            ->values();
        $currentSpecies = request('species');
    @endphp

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-[220px_minmax(0,1fr)_260px] lg:gap-5">
        <aside class="hidden lg:block">
            <div class="sticky top-20 ui-card ui-card-shadow p-4">
                <h2 class="mb-3 text-sm font-bold text-warm-900">快捷入口</h2>
                <div class="space-y-2 text-sm">
                    <a href="{{ route('posts.index') }}" class="flex items-center gap-2 rounded-lg bg-primary-50 px-3 py-2 font-semibold text-primary-700">🏠 全部动态</a>
                    <a href="{{ route('pets.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-warm-700 hover:bg-warm-100">🐾 我的宠物</a>
                    <a href="{{ route('posts.create') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-warm-700 hover:bg-warm-100">✍️ 发动态</a>
                </div>
            </div>
        </aside>

        <div>
            <div class="ui-card ui-card-shadow mb-4 p-4 animate-fade-in-up">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h1 class="ui-page-title">动态广场</h1>
                        <p class="ui-page-subtitle">按宠物视角记录日常，快速找到你关心的毛孩子故事</p>
                    </div>
                    <div class="flex items-center gap-2">
                        @if(request('tag') || request('species') || request('q'))
                            <a href="{{ route('posts.index') }}" class="inline-flex items-center gap-1 rounded-lg bg-warm-100 px-3 py-2 text-xs font-medium text-warm-700 hover:bg-warm-200">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                清除筛选
                            </a>
                        @endif
                        <a href="{{ route('posts.create') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-primary-600 px-3.5 py-2 text-sm font-semibold text-white hover:bg-primary-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            发布
                        </a>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-2">
                    <a href="{{ route('posts.index', request()->except(['species', 'page'])) }}" @class([
                        'inline-flex items-center rounded-full px-3 py-1.5 text-xs font-semibold transition-colors',
                        'bg-warm-900 text-white' => blank($currentSpecies),
                        'bg-warm-100 text-warm-700 hover:bg-warm-200' => filled($currentSpecies),
                    ])>
                        全部
                    </a>

                    @foreach($speciesFilters as $species)
                        <a href="{{ route('posts.index', array_merge(request()->except(['species', 'page']), ['species' => $species])) }}" @class([
                            'inline-flex items-center gap-1 rounded-full px-3 py-1.5 text-xs font-semibold transition-colors',
                            'bg-primary-600 text-white' => $currentSpecies === $species,
                            'bg-primary-50 text-primary-700 hover:bg-primary-100' => $currentSpecies !== $species,
                        ])>
                            @if($species === '猫')
                                🐱 猫咪
                            @elseif($species === '狗')
                                🐶 狗狗
                            @else
                                {{ $species }}
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="space-y-4">
        @forelse($posts as $post)
            @php
                $postShowUrl = route('posts.show', $post);
                $speciesLabel = filled($post->pet?->species) ? trim($post->pet->species) : '未分类';
            @endphp

            <div class="post-card js-post-card overflow-hidden rounded-2xl border border-warm-200 bg-white hover:border-warm-300 hover:shadow-sm transition-all duration-300 cursor-pointer" data-href="{{ $postShowUrl }}">
                <div class="px-5 py-4 sm:px-6 sm:py-5">
                    <x-post-header :post="$post" :species-label="$speciesLabel" :show-pet-link="true" :show-dropdown="true" :stop-propagation="true" />

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
                                <a href="{{ route('posts.index', array_merge(request()->except('page'), ['tag' => $tag->name])) }}" onclick="event.stopPropagation()" class="text-xs bg-warm-100 text-warm-600 px-3 py-1.5 rounded-lg font-medium hover:bg-primary-100 hover:text-primary-600 transition-colors">
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
        @empty
            <div class="ui-card ui-card-shadow p-12 text-center animate-fade-in-up">
                <div class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-primary-100 to-accent-100 text-4xl">🐾</div>
                <h2 class="text-xl font-display font-bold text-warm-900">还没有找到宠物动态</h2>
                <p class="mt-2 text-sm text-warm-500">试试换个关键词或物种筛选，也可以先发布一条属于毛孩子的日常。</p>
                <div class="mt-6 flex flex-wrap justify-center gap-3">
                    <a href="{{ route('posts.index') }}" class="ui-btn-secondary px-5 py-2.5">查看全部动态</a>
                    <a href="{{ route('posts.create') }}" class="ui-btn-primary px-5 py-2.5">发布第一条</a>
                </div>
            </div>
        @endforelse
            </div>

            <div class="mt-8">
                {{ $posts->links() }}
            </div>
        </div>

        <aside class="hidden lg:block">
            <div class="sticky top-20 space-y-4">
                <div class="ui-card ui-card-shadow p-4">
                    <h3 class="mb-3 text-sm font-bold text-warm-900">热门物种</h3>
                    <div class="space-y-2 text-sm">
                        @foreach($speciesFilters->take(8) as $index => $species)
                            <a href="{{ route('posts.index', array_merge(request()->except(['species', 'page']), ['species' => $species])) }}" class="flex items-center justify-between rounded-lg px-2.5 py-2 text-warm-700 hover:bg-warm-100">
                                <span>{{ $species }}</span>
                                <span class="text-xs text-warm-400">#{{ $index + 1 }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="ui-card ui-card-shadow p-4">
                    <h3 class="mb-3 text-sm font-bold text-warm-900">使用提示</h3>
                    <ul class="space-y-2 text-xs leading-5 text-warm-500">
                        <li>· 左侧是功能入口，右侧是热点导航</li>
                        <li>· 中间按时间流展示所有动态</li>
                        <li>· 顶部标签可快速切换宠物类别</li>
                    </ul>
                </div>
            </div>
        </aside>
    </div>
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
                expandButton.textContent = '收起全文';
            }
        });
    });
</script>
@endsection
