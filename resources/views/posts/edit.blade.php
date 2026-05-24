@extends('layouts.app')

@section('title', '编辑动态 - iPet')

@section('content')
@php
    $existingMedia = $post->media->sortBy('sort_order')->values();
@endphp
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8 animate-fade-in-up">
        <a href="{{ route('posts.show', $post) }}" class="inline-flex items-center gap-2 text-warm-500 hover:text-primary-600 font-medium transition-colors mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            返回动态详情
        </a>
        <h1 class="ui-page-title">编辑动态</h1>
    </div>

    <div class="ui-card ui-card-shadow-strong p-8 animate-fade-in-up delay-100">
        <form action="{{ route('posts.update', $post) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="ui-label">关联宠物</label>
                <select name="pet_id" class="ui-select">
                    <option value="">不关联宠物</option>
                    @foreach($pets as $petOption)
                        <option value="{{ $petOption->id }}" {{ (string) old('pet_id', $post->pet_id) === (string) $petOption->id ? 'selected' : '' }}>{{ $petOption->name }}</option>
                    @endforeach
                </select>
                @error('pet_id')
                    <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="ui-label">内容 <span class="text-danger-500">*</span></label>
                <textarea name="content" rows="5" placeholder="分享你和宠物的美好时刻..." class="ui-textarea @error('content') border-danger-300 @enderror" required>{{ old('content', $post->content) }}</textarea>
                @error('content')
                    <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="ui-label">位置</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-warm-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <input type="text" name="location" value="{{ old('location', $post->location) }}" placeholder="添加位置" class="ui-input pl-10">
                </div>
            </div>

            <div>
                <label class="ui-label">标签</label>
                <input type="text" name="tags" value="{{ old('tags', $post->tags?->pluck('name')->implode(', ')) }}" placeholder="如：金毛, 公园, 日常" class="ui-input">
                <p class="ui-helper">多个标签请用英文逗号分隔</p>
            </div>

            <div>
                <label class="ui-label">可见性</label>
                <div class="grid grid-cols-3 gap-3">
                    <label data-visibility-card class="relative flex flex-col items-center gap-2 p-4 rounded-xl border-2 {{ old('visibility', $post->visibility) === 'public' ? 'border-primary-300 bg-primary-50' : 'border-warm-200' }} cursor-pointer hover:border-primary-300 hover:bg-primary-50 transition-all duration-200">
                        <input type="radio" name="visibility" value="public" {{ old('visibility', $post->visibility) === 'public' ? 'checked' : '' }} class="sr-only">
                        <svg data-visibility-icon class="w-6 h-6 {{ old('visibility', $post->visibility) === 'public' ? 'text-primary-500' : 'text-warm-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="text-sm font-medium text-warm-700">公开</span>
                    </label>
                    <label data-visibility-card class="relative flex flex-col items-center gap-2 p-4 rounded-xl border-2 {{ old('visibility', $post->visibility) === 'followers' ? 'border-primary-300 bg-primary-50' : 'border-warm-200' }} cursor-pointer hover:border-primary-300 hover:bg-primary-50 transition-all duration-200">
                        <input type="radio" name="visibility" value="followers" {{ old('visibility', $post->visibility) === 'followers' ? 'checked' : '' }} class="sr-only">
                        <svg data-visibility-icon class="w-6 h-6 {{ old('visibility', $post->visibility) === 'followers' ? 'text-primary-500' : 'text-warm-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <span class="text-sm font-medium text-warm-700">粉丝可见</span>
                    </label>
                    <label data-visibility-card class="relative flex flex-col items-center gap-2 p-4 rounded-xl border-2 {{ old('visibility', $post->visibility) === 'private' ? 'border-primary-300 bg-primary-50' : 'border-warm-200' }} cursor-pointer hover:border-primary-300 hover:bg-primary-50 transition-all duration-200">
                        <input type="radio" name="visibility" value="private" {{ old('visibility', $post->visibility) === 'private' ? 'checked' : '' }} class="sr-only">
                        <svg data-visibility-icon class="w-6 h-6 {{ old('visibility', $post->visibility) === 'private' ? 'text-primary-500' : 'text-warm-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        <span class="text-sm font-medium text-warm-700">仅自己</span>
                    </label>
                </div>
            </div>

            <div class="border-t border-warm-100 pt-6 space-y-4">
                <div>
                    <label class="ui-label">已有媒体（可拖拽排序 / 删除）</label>
                    <div id="existing-media-grid" class="mt-3 grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach($existingMedia as $media)
                            <div data-existing-media-item data-media-id="{{ $media->id }}" class="relative overflow-hidden rounded-xl border border-warm-200 bg-warm-50" draggable="true">
                                <div class="pointer-events-none absolute left-2 top-2 z-10 inline-flex items-center gap-1 rounded-md bg-black/60 px-2 py-0.5 text-[11px] text-white">
                                    @if($media->type === 'image')
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        图片
                                    @else
                                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                        视频
                                    @endif
                                </div>
                                @if($media->type === 'image')
                                    <img src="{{ $media->url() }}" alt="已上传图片" class="h-36 w-full object-cover">
                                @else
                                    <div class="relative h-36 w-full bg-black/90">
                                        @if($media->thumbnailUrl())
                                            <img src="{{ $media->thumbnailUrl() }}" alt="视频封面" class="h-full w-full object-cover opacity-80">
                                        @endif
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <div class="rounded-full bg-white/25 p-2.5">
                                                <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="flex items-center justify-between gap-2 border-t border-warm-200 px-2 py-1.5 text-xs text-warm-600">
                                    <span>#{{ $loop->iteration }}</span>
                                    <div class="flex items-center gap-1.5">
                                        <span class="inline-flex items-center gap-1 rounded bg-warm-200 px-1.5 py-1 text-[11px] text-warm-700" title="拖拽排序">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9h8M8 15h8"></path></svg>
                                            拖拽
                                        </span>
                                        <button type="button" data-existing-media-remove class="rounded bg-danger-50 px-2 py-1 text-danger-600 hover:bg-danger-100">删除</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <p class="ui-helper">拖拽调整顺序后保存生效；删除后保存将移除该媒体。</p>
                    <div id="existing-media-hidden-inputs"></div>
                </div>

                <div>
                    <label class="ui-label">上传图片</label>
                    <input id="image-files" type="file" name="image_files[]" accept="image/*" multiple class="w-full border-warm-200 rounded-xl shadow-sm focus:border-primary-400 focus:ring-2 focus:ring-primary-200 transition-all duration-200 file:mr-4 file:rounded-lg file:border-0 file:bg-primary-50 file:px-4 file:py-2 file:text-primary-700 hover:file:bg-primary-100">
                    <p class="ui-helper">支持 JPG/PNG/WebP，单张不超过 10MB，新增媒体会追加到当前动态</p>
                    <p id="image-files-error" class="mt-2 hidden text-xs text-red-600"></p>
                </div>

                <div>
                    <label class="ui-label">上传视频</label>
                    <input id="video-files" type="file" name="video_files[]" accept="video/*" multiple class="w-full border-warm-200 rounded-xl shadow-sm focus:border-primary-400 focus:ring-2 focus:ring-primary-200 transition-all duration-200 file:mr-4 file:rounded-lg file:border-0 file:bg-primary-50 file:px-4 file:py-2 file:text-primary-700 hover:file:bg-primary-100">
                    <p class="ui-helper">支持 MP4/MOV/WebM，单个不超过 100MB，新增媒体会追加到当前动态</p>
                    <p id="video-files-error" class="mt-2 hidden text-xs text-red-600"></p>
                </div>
            </div>

            <div class="flex gap-4 pt-4 border-t border-warm-100">
                <button type="submit" class="ui-btn-primary flex-1 py-3.5 shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                    保存修改
                </button>
                <a href="{{ route('posts.show', $post) }}" class="ui-btn-secondary px-6 py-3.5">
                    取消
                </a>
            </div>
        </form>
    </div>

    <!-- 删除区域 -->
    <div class="mt-8 ui-card ui-card-shadow border-danger-100 p-6 animate-fade-in-up delay-200">
        <h2 class="text-lg font-display font-bold text-danger-600 mb-2 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
            危险操作
        </h2>
        <p class="text-sm text-warm-500 mb-4">删除后此动态将无法恢复。</p>
        <form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('确定要删除这条动态吗？此操作不可撤销！')">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-5 py-2.5 bg-danger-50 hover:bg-danger-100 text-danger-600 border border-danger-200 rounded-xl font-semibold transition-colors">
                删除此动态
            </button>
        </form>
    </div>
</div>
<script>
    const imageInput = document.getElementById('image-files');
    const videoInput = document.getElementById('video-files');
    const imageError = document.getElementById('image-files-error');
    const videoError = document.getElementById('video-files-error');
    const existingMediaGrid = document.getElementById('existing-media-grid');
    const existingMediaHiddenInputs = document.getElementById('existing-media-hidden-inputs');
    const maxImageSize = 10 * 1024 * 1024;
    const maxVideoSize = 100 * 1024 * 1024;
    let dragExistingMediaId = null;

    function clearError(el) {
        el.textContent = '';
        el.classList.add('hidden');
    }

    function showError(el, message) {
        el.textContent = message;
        el.classList.remove('hidden');
    }

    function syncVisibilityCards() {
        document.querySelectorAll('[data-visibility-card]').forEach((card) => {
            const input = card.querySelector('input[name="visibility"]');
            const icon = card.querySelector('[data-visibility-icon]');
            const isChecked = input?.checked === true;

            card.classList.toggle('border-primary-300', isChecked);
            card.classList.toggle('bg-primary-50', isChecked);
            card.classList.toggle('border-warm-200', !isChecked);

            if (icon) {
                icon.classList.toggle('text-primary-500', isChecked);
                icon.classList.toggle('text-warm-400', !isChecked);
            }
        });
    }

    function syncExistingMediaHiddenInputs() {
        if (!existingMediaHiddenInputs || !existingMediaGrid) {
            return;
        }

        existingMediaHiddenInputs.innerHTML = '';
        const mediaItems = [...existingMediaGrid.querySelectorAll('[data-existing-media-item]')];

        mediaItems.forEach((item, index) => {
            const mediaId = item.dataset.mediaId;

            const keepInput = document.createElement('input');
            keepInput.type = 'hidden';
            keepInput.name = 'keep_media_ids[]';
            keepInput.value = mediaId;

            const orderInput = document.createElement('input');
            orderInput.type = 'hidden';
            orderInput.name = 'media_order[]';
            orderInput.value = mediaId;

            const indexLabel = item.querySelector('span');
            if (indexLabel) {
                indexLabel.textContent = `#${index + 1}`;
            }

            existingMediaHiddenInputs.appendChild(keepInput);
            existingMediaHiddenInputs.appendChild(orderInput);
        });
    }

    function bindExistingMediaEvents() {
        if (!existingMediaGrid) {
            return;
        }

        existingMediaGrid.querySelectorAll('[data-existing-media-item]').forEach((item) => {
            item.addEventListener('dragstart', (event) => {
                dragExistingMediaId = event.currentTarget.dataset.mediaId;
                event.dataTransfer.effectAllowed = 'move';
            });

            item.addEventListener('dragover', (event) => {
                event.preventDefault();
                event.currentTarget.classList.add('ring-2', 'ring-primary-300');
            });

            item.addEventListener('dragleave', (event) => {
                event.currentTarget.classList.remove('ring-2', 'ring-primary-300');
            });

            item.addEventListener('drop', (event) => {
                event.preventDefault();
                event.currentTarget.classList.remove('ring-2', 'ring-primary-300');

                const target = event.currentTarget;
                const targetId = target.dataset.mediaId;
                const dragged = existingMediaGrid.querySelector(`[data-media-id="${dragExistingMediaId}"]`);

                if (!dragged || !targetId || dragExistingMediaId === targetId) {
                    return;
                }

                const allItems = [...existingMediaGrid.querySelectorAll('[data-existing-media-item]')];
                const draggedIndex = allItems.findIndex((el) => el.dataset.mediaId === dragExistingMediaId);
                const targetIndex = allItems.findIndex((el) => el.dataset.mediaId === targetId);

                if (draggedIndex < targetIndex) {
                    existingMediaGrid.insertBefore(dragged, target.nextSibling);
                } else {
                    existingMediaGrid.insertBefore(dragged, target);
                }

                syncExistingMediaHiddenInputs();
            });
        });

        existingMediaGrid.querySelectorAll('[data-existing-media-remove]').forEach((button) => {
            button.addEventListener('click', (event) => {
                const mediaItem = event.currentTarget.closest('[data-existing-media-item]');
                mediaItem?.remove();
                syncExistingMediaHiddenInputs();
            });
        });
    }

    document.querySelectorAll('input[name="visibility"]').forEach((input) => {
        input.addEventListener('change', syncVisibilityCards);
    });

    imageInput?.addEventListener('change', () => {
        clearError(imageError);
        const invalid = [...imageInput.files].find((file) => !file.type.startsWith('image/') || file.size > maxImageSize);

        if (invalid) {
            showError(imageError, '图片格式或大小不合法（单张最多 10MB）。');
            imageInput.value = '';
        }
    });

    videoInput?.addEventListener('change', () => {
        clearError(videoError);
        const invalid = [...videoInput.files].find((file) => !file.type.startsWith('video/') || file.size > maxVideoSize);

        if (invalid) {
            showError(videoError, '视频格式或大小不合法（单个最多 100MB）。');
            videoInput.value = '';
        }
    });

    bindExistingMediaEvents();
    syncExistingMediaHiddenInputs();
    syncVisibilityCards();
</script>
@endsection
