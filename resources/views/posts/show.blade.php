@extends('layouts.app')

@section('title', ($post->pet ? $post->pet->name . ' 的动态' : '动态详情') . ' - iPet')

@section('content')
@php
    $speciesLabel = $post->pet?->species?->name ?? '未分类';
@endphp
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-[220px_minmax(0,1fr)_260px] lg:gap-5">
        <aside class="hidden lg:block">
            <div class="sticky top-20 ui-card ui-card-shadow p-4 overflow-hidden" style="border-radius: 1rem;">
                <div class="pointer-events-none absolute -right-4 -top-4 h-20 w-20 rounded-full bg-gradient-to-br from-primary-100 to-accent-100 opacity-60"></div>
                <h2 class="relative mb-3 text-sm font-bold text-warm-900">快捷入口</h2>
                <div class="space-y-2 text-sm">
                    <a href="{{ route('posts.index') }}" class="flex items-center gap-2 rounded-lg bg-primary-50 px-3 py-2 font-semibold text-primary-700 transition-colors hover:bg-primary-100">🏠 动态广场</a>
                    @if($post->pet)
                        <a href="{{ route('pets.show', $post->pet) }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-warm-700 transition-colors hover:bg-warm-100">🐾 {{ $post->pet->name }}</a>
                    @endif
                    <a href="{{ route('posts.create') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-warm-700 transition-colors hover:bg-warm-100">✍️ 发布动态</a>
                </div>
            </div>
        </aside>

        <main>
            <div class="ui-card ui-card-shadow mb-4 p-4 animate-fade-in-up">
                <a href="{{ route('posts.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-warm-500 hover:text-primary-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    返回动态广场
                </a>
            </div>

            <!-- 动态头部装饰 -->
            <div class="ui-card ui-card-shadow-strong overflow-hidden mb-8 animate-fade-in-up" style="border-radius: 1.5rem;">
                <div class="h-32 bg-gradient-to-br from-primary-100 via-primary-50 to-accent-50 relative">
                    @if($post->pet?->avatar)
                        <img src="{{ $post->pet->avatar }}" alt="{{ $post->pet->name }}" class="w-full h-full object-cover opacity-50">
                    @else
                        <div class="absolute inset-0 flex items-center justify-center opacity-20">
                            <svg class="w-32 h-32 text-primary-300/40" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M4.5 9.5a2 2 0 110-4 2 2 0 010 4zM9 7a2 2 0 110-4 2 2 0 010 4zM15 7a2 2 0 110-4 2 2 0 010 4zM19.5 9.5a2 2 0 110-4 2 2 0 010 4zM6 14c0 2.5 2 4.5 6 4.5s6-2 6-4.5c0-1.5-1-2.5-2-3-1-.5-2.5-.5-4-.5s-3 0-4 .5c-1 .5-2 1.5-2 3z"/>
                            </svg>
                        </div>
                    @endif
                    <div class="absolute bottom-0 left-0 right-0 p-6">
                        <div class="flex items-center gap-3">
                            @if($post->pet)
                                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-white shadow-lg ring-2 ring-white/50">
                                    @if($post->pet->avatar)
                                        <img src="{{ $post->pet->avatar }}" alt="{{ $post->pet->name }}" class="h-full w-full rounded-full object-cover">
                                    @else
                                        <div class="h-8 w-8 rounded-full bg-gradient-to-br from-primary-100 to-accent-100"></div>
                                    @endif
                                </div>
                                <div>
                                    <h2 class="text-2xl font-display font-bold text-white drop-shadow-lg">{{ $post->pet->name }}</h2>
                                    <p class="text-white/90 text-sm font-medium">{{ $speciesLabel }} · {{ $post->created_at->format('Y-m-d H:i') }}</p>
                                </div>
                            @else
                                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-white shadow-lg ring-2 ring-white/50">
                                    <div class="h-8 w-8 rounded-full bg-gradient-to-br from-primary-100 to-accent-100"></div>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-display font-bold text-white drop-shadow-lg">动态详情</h2>
                                    <p class="text-white/90 text-sm font-medium">{{ $post->created_at->format('Y-m-d H:i') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="p-6">
            <x-post-header :post="$post" :species-label="$speciesLabel" :show-pet-link="true" />

            <x-post-content :content="$post->content" />

            @if($post->location)
                <p class="text-sm text-warm-500 mb-4 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    {{ $post->location }}
                </p>
            @endif

            @if($post->tags && $post->tags->count() > 0)
                <div class="flex flex-wrap gap-2 mb-4">
                    @foreach($post->tags as $tag)
                        <a href="{{ route('posts.index', ['tag' => $tag->name]) }}" class="text-xs bg-warm-100 text-warm-600 px-3 py-1.5 rounded-lg font-medium hover:bg-primary-100 hover:text-primary-600 transition-colors">
                            #{{ $tag->name }}
                        </a>
                    @endforeach
                </div>
            @endif

            <x-post-media :post="$post" mode="detail" />

            <x-post-stats :post="$post" layout="weibo" :show-view="true" :show-like="true" />
        </div>
    </div>

    <div class="ui-card ui-card-shadow-strong p-6 animate-fade-in-up delay-100">
        <x-comment-section-header :post="$post" :comment-sort="$commentSort" />

        <x-comment-create-form :post="$post" :comment-sort="$commentSort" />

        @if($post->comment_count > 0)
            <div class="space-y-4">
                @foreach($post->comments as $comment)
                    <x-comment-item :comment="$comment" :post="$post" :comment-sort="$commentSort" />
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <svg class="w-12 h-12 mx-auto text-warm-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                <p class="text-warm-500 font-medium">暂无评论</p>
                <p class="text-sm text-warm-400 mt-1">成为第一个评论的人吧</p>
            </div>
        @endif
    </div>
        </main>

        <aside class="hidden lg:block">
            <div class="sticky top-20 space-y-4">
                <div class="ui-card ui-card-shadow p-4 overflow-hidden" style="border-radius: 1rem;">
                    <div class="pointer-events-none absolute -right-4 -top-4 h-20 w-20 rounded-full bg-gradient-to-br from-primary-100 to-accent-100 opacity-60"></div>
                    <h3 class="relative mb-3 text-sm font-bold text-warm-900">动态信息</h3>
                    <ul class="space-y-2 text-xs text-warm-500">
                        <li>· 物种：{{ $speciesLabel }}</li>
                        <li>· 发布时间：{{ $post->published_at?->format('Y年m月d日 H:i') ?? '未发布' }}</li>
                        <li>· 评论数：{{ $post->comment_count }}</li>
                    </ul>
                </div>
                <div class="ui-card ui-card-shadow p-4 overflow-hidden" style="border-radius: 1rem;">
                    <div class="pointer-events-none absolute -right-4 -top-4 h-20 w-20 rounded-full bg-gradient-to-br from-accent-100 to-primary-100 opacity-60"></div>
                    <h3 class="relative mb-3 text-sm font-bold text-warm-900">浏览建议</h3>
                    <ul class="space-y-2 text-xs leading-5 text-warm-500">
                        <li>· 先看正文，再看图文或视频</li>
                        <li>· 下方评论区支持楼中楼回复</li>
                        <li>· 可回到广场继续刷新动态</li>
                    </ul>
                </div>
            </div>
        </aside>
    </div>
</div>

<script>
    document.querySelectorAll('details[data-reply-to]').forEach((replyDetails) => {
        replyDetails.addEventListener('toggle', () => {
            if (!replyDetails.open) {
                return;
            }

            const replyTextarea = replyDetails.querySelector('.js-reply-textarea');
            if (!replyTextarea) {
                return;
            }

            const replyToName = (replyDetails.dataset.replyTo || '').trim();
            const replyPrefix = replyToName ? `@${replyToName} ` : '';

            if (replyPrefix && replyTextarea.value.trim() === '') {
                replyTextarea.value = replyPrefix;
            }

            replyTextarea.focus();
            replyTextarea.setSelectionRange(replyTextarea.value.length, replyTextarea.value.length);
        });
    });

    document.querySelectorAll('.js-children-toggle').forEach((childrenDetails) => {
        const summary = childrenDetails.querySelector('summary');
        if (!summary) {
            return;
        }

        const renderSummaryText = () => {
            const openText = summary.dataset.openText || '收起回复';
            const closeText = summary.dataset.closeText || '展开回复';
            summary.textContent = childrenDetails.open ? openText : closeText;
        };

        renderSummaryText();
        childrenDetails.addEventListener('toggle', renderSummaryText);
    });
</script>

@if($post->media->where('type', 'image')->isNotEmpty())
    <div id="post-image-lightbox" class="fixed inset-0 z-[999] hidden items-center justify-center bg-black/90 p-4" role="dialog" aria-modal="true">
        <button type="button" id="lightbox-close" class="absolute right-5 top-5 rounded-full bg-white/15 p-2 text-white hover:bg-white/25" aria-label="关闭查看器">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <button type="button" id="lightbox-prev" class="absolute left-4 sm:left-6 rounded-full bg-white/15 p-2 text-white hover:bg-white/25" aria-label="上一张">
            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </button>

        <img id="lightbox-image" src="" alt="" class="max-h-[86vh] max-w-[92vw] rounded-lg object-contain shadow-2xl">

        <button type="button" id="lightbox-next" class="absolute right-4 sm:right-6 rounded-full bg-white/15 p-2 text-white hover:bg-white/25" aria-label="下一张">
            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </button>

        <div id="lightbox-counter" class="absolute bottom-6 rounded-full bg-black/55 px-3 py-1 text-sm text-white"></div>
    </div>

    <script>
        const lightboxItems = Array.from(document.querySelectorAll('[data-lightbox-image]'));
        const lightbox = document.getElementById('post-image-lightbox');
        const lightboxImage = document.getElementById('lightbox-image');
        const lightboxCounter = document.getElementById('lightbox-counter');
        const lightboxClose = document.getElementById('lightbox-close');
        const lightboxPrev = document.getElementById('lightbox-prev');
        const lightboxNext = document.getElementById('lightbox-next');

        let currentLightboxIndex = 0;
        let touchStartX = null;
        let touchStartY = null;

        function preloadNeighborImages(index) {
            const total = lightboxItems.length;
            const prevIndex = (index - 1 + total) % total;
            const nextIndex = (index + 1) % total;
            const preloadUrls = [
                lightboxItems[prevIndex].dataset.imageUrl,
                lightboxItems[nextIndex].dataset.imageUrl,
            ];

            preloadUrls.forEach((url) => {
                const image = new Image();
                image.src = url;
            });
        }

        function renderLightbox(index) {
            const total = lightboxItems.length;
            const normalizedIndex = (index + total) % total;
            const target = lightboxItems[normalizedIndex];

            currentLightboxIndex = normalizedIndex;
            lightboxImage.src = target.dataset.imageUrl;
            lightboxImage.alt = target.dataset.imageAlt || '动态图片';
            lightboxCounter.textContent = (normalizedIndex + 1) + ' / ' + total;

            preloadNeighborImages(normalizedIndex);
        }

        function openLightbox(index) {
            renderLightbox(index);
            lightbox.classList.remove('hidden');
            lightbox.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function closeLightbox() {
            lightbox.classList.add('hidden');
            lightbox.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }

        lightboxItems.forEach((item, index) => {
            item.addEventListener('click', () => openLightbox(index));
        });

        lightboxPrev.addEventListener('click', (event) => {
            event.stopPropagation();
            renderLightbox(currentLightboxIndex - 1);
        });

        lightboxNext.addEventListener('click', (event) => {
            event.stopPropagation();
            renderLightbox(currentLightboxIndex + 1);
        });

        lightboxClose.addEventListener('click', closeLightbox);

        lightbox.addEventListener('click', (event) => {
            if (event.target === lightbox) {
                closeLightbox();
            }
        });

        lightbox.addEventListener('touchstart', (event) => {
            if (event.touches.length !== 1) {
                return;
            }

            touchStartX = event.touches[0].clientX;
            touchStartY = event.touches[0].clientY;
        }, { passive: true });

        lightbox.addEventListener('touchend', (event) => {
            if (touchStartX === null || touchStartY === null || event.changedTouches.length !== 1) {
                touchStartX = null;
                touchStartY = null;

                return;
            }

            const touchEndX = event.changedTouches[0].clientX;
            const touchEndY = event.changedTouches[0].clientY;
            const deltaX = touchEndX - touchStartX;
            const deltaY = touchEndY - touchStartY;
            const minSwipeDistance = 50;

            if (Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) >= minSwipeDistance) {
                if (deltaX < 0) {
                    renderLightbox(currentLightboxIndex + 1);
                } else {
                    renderLightbox(currentLightboxIndex - 1);
                }
            }

            touchStartX = null;
            touchStartY = null;
        }, { passive: true });

        document.addEventListener('keydown', (event) => {
            if (lightbox.classList.contains('hidden')) {
                return;
            }

            if (event.key === 'Escape') {
                closeLightbox();
            }

            if (event.key === 'ArrowLeft') {
                renderLightbox(currentLightboxIndex - 1);
            }

            if (event.key === 'ArrowRight') {
                renderLightbox(currentLightboxIndex + 1);
            }
        });
    </script>
@endif

@endsection
