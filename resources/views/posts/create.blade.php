@extends('layouts.app')

@section('title', '发布动态 - iPet')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8 animate-fade-in-up">
        <a href="{{ route('posts.index') }}" class="inline-flex items-center gap-2 text-warm-500 hover:text-primary-600 font-medium transition-colors mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            返回动态广场
        </a>
        <h1 class="ui-page-title">发布动态</h1>
        <p class="ui-page-subtitle">分享你和宠物的美好时刻</p>
    </div>

    <div class="ui-card ui-card-shadow-strong p-8 animate-fade-in-up delay-100">
        <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label class="ui-label">关联宠物</label>
                <select name="pet_id" class="ui-select">
                    <option value="">不关联宠物</option>
                    @foreach($pets as $pet)
                        <option value="{{ $pet->id }}" {{ (string) old('pet_id', request('pet_id')) === (string) $pet->id ? 'selected' : '' }}>{{ $pet->name }}</option>
                    @endforeach
                </select>
                @error('pet_id')
                    <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="ui-label">内容</label>
                <textarea name="content" rows="5" placeholder="分享你和宠物的美好时刻..." class="ui-textarea @error('content') border-danger-300 @enderror" required>{{ old('content') }}</textarea>
                @error('content')
                    <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="ui-label">位置</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-warm-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <input type="text" name="location" value="{{ old('location') }}" placeholder="添加位置" class="ui-input pl-10">
                </div>
            </div>

            <div>
                <label class="ui-label">标签</label>
                <input type="text" name="tags" value="{{ old('tags') }}" placeholder="如：金毛, 公园, 日常" class="ui-input">
                <p class="ui-helper">多个标签请用英文逗号分隔</p>
            </div>

            <div>
                <label class="ui-label">发布时间</label>
                <input type="datetime-local" name="published_at" value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}" class="ui-input">
                <p class="ui-helper">可选择未来时间进行定时发布</p>
            </div>

            <div>
                <label class="ui-label">可见性</label>
                <div class="grid grid-cols-3 gap-3">
                    <label class="relative flex flex-col items-center gap-2 p-4 rounded-xl border-2 border-warm-200 cursor-pointer hover:border-primary-300 hover:bg-primary-50 transition-all duration-200">
                        <input type="radio" name="visibility" value="public" {{ old('visibility', 'public') === 'public' ? 'checked' : '' }} class="sr-only">
                        <svg class="w-6 h-6 text-warm-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="text-sm font-medium text-warm-700">公开</span>
                    </label>
                    <label class="relative flex flex-col items-center gap-2 p-4 rounded-xl border-2 border-warm-200 cursor-pointer hover:border-primary-300 hover:bg-primary-50 transition-all duration-200">
                        <input type="radio" name="visibility" value="followers" {{ old('visibility') === 'followers' ? 'checked' : '' }} class="sr-only">
                        <svg class="w-6 h-6 text-warm-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <span class="text-sm font-medium text-warm-700">粉丝可见</span>
                    </label>
                    <label class="relative flex flex-col items-center gap-2 p-4 rounded-xl border-2 border-warm-200 cursor-pointer hover:border-primary-300 hover:bg-primary-50 transition-all duration-200">
                        <input type="radio" name="visibility" value="private" {{ old('visibility') === 'private' ? 'checked' : '' }} class="sr-only">
                        <svg class="w-6 h-6 text-warm-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        <span class="text-sm font-medium text-warm-700">仅自己</span>
                    </label>
                </div>
            </div>

            <div class="border-t border-warm-100 pt-6 space-y-4">
                <div>
                    <label class="ui-label">上传图片</label>
                    <input id="image-files" type="file" name="image_files[]" accept="image/*" multiple class="w-full border-warm-200 rounded-xl shadow-sm focus:border-primary-400 focus:ring-2 focus:ring-primary-200 transition-all duration-200 file:mr-4 file:rounded-lg file:border-0 file:bg-primary-50 file:px-4 file:py-2 file:text-primary-700 hover:file:bg-primary-100">
                    <p class="ui-helper">支持 JPG/PNG/WebP，单张不超过 10MB，可拖拽缩略图调整顺序</p>
                    <p id="image-files-error" class="mt-2 hidden text-xs text-red-600"></p>
                    <div id="image-files-preview" class="mt-3 grid grid-cols-3 gap-3"></div>
                </div>

                <div>
                    <label class="ui-label">上传视频</label>
                    <input id="video-files" type="file" name="video_files[]" accept="video/*" multiple class="w-full border-warm-200 rounded-xl shadow-sm focus:border-primary-400 focus:ring-2 focus:ring-primary-200 transition-all duration-200 file:mr-4 file:rounded-lg file:border-0 file:bg-primary-50 file:px-4 file:py-2 file:text-primary-700 hover:file:bg-primary-100">
                    <p class="ui-helper">支持 MP4/MOV/WebM，单个不超过 100MB</p>
                    <p id="video-files-error" class="mt-2 hidden text-xs text-red-600"></p>
                    <div id="video-files-preview" class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3"></div>
                </div>
            </div>

            <div class="flex gap-4 pt-4 border-t border-warm-100">
                <button type="submit" class="ui-btn-primary flex-1 py-3.5 shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                    发布
                </button>
                <a href="{{ route('posts.index') }}" class="ui-btn-secondary px-6 py-3.5">
                    取消
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    const imageInput = document.getElementById('image-files');
    const videoInput = document.getElementById('video-files');
    const imageError = document.getElementById('image-files-error');
    const videoError = document.getElementById('video-files-error');
    const imagePreview = document.getElementById('image-files-preview');
    const videoPreview = document.getElementById('video-files-preview');

    const maxImageSize = 10 * 1024 * 1024;
    const maxVideoSize = 100 * 1024 * 1024;

    let imageFiles = [];
    let videoFiles = [];
    let dragImageIndex = null;

    function clearError(el) {
        el.textContent = '';
        el.classList.add('hidden');
    }

    function showError(el, message) {
        el.textContent = message;
        el.classList.remove('hidden');
    }

    function syncInputFiles(input, files) {
        const dataTransfer = new DataTransfer();

        files.forEach((item) => {
            dataTransfer.items.add(item.file);
        });

        input.files = dataTransfer.files;
    }

    function removeImage(index) {
        imageFiles.splice(index, 1);
        syncInputFiles(imageInput, imageFiles);
        renderImagePreview();
    }

    function removeVideo(index) {
        videoFiles.splice(index, 1);
        syncInputFiles(videoInput, videoFiles);
        renderVideoPreview();
    }

    function reorderImages(fromIndex, toIndex) {
        if (fromIndex === toIndex || fromIndex === null || toIndex === null) {
            return;
        }

        const moved = imageFiles.splice(fromIndex, 1)[0];
        imageFiles.splice(toIndex, 0, moved);
        syncInputFiles(imageInput, imageFiles);
        renderImagePreview();
    }

    function renderImagePreview() {
        imagePreview.innerHTML = '';

        imageFiles.forEach((item, index) => {
            const wrapper = document.createElement('div');
            wrapper.className = 'relative aspect-square overflow-hidden rounded-xl border border-warm-200 bg-warm-50';
            wrapper.draggable = true;
            wrapper.dataset.index = index;

            wrapper.addEventListener('dragstart', (event) => {
                dragImageIndex = Number(event.currentTarget.dataset.index);
                event.dataTransfer.effectAllowed = 'move';
            });

            wrapper.addEventListener('dragover', (event) => {
                event.preventDefault();
                wrapper.classList.add('ring-2', 'ring-primary-300');
            });

            wrapper.addEventListener('dragleave', () => {
                wrapper.classList.remove('ring-2', 'ring-primary-300');
            });

            wrapper.addEventListener('drop', (event) => {
                event.preventDefault();
                wrapper.classList.remove('ring-2', 'ring-primary-300');
                const targetIndex = Number(event.currentTarget.dataset.index);
                reorderImages(dragImageIndex, targetIndex);
                dragImageIndex = null;
            });

            const img = document.createElement('img');
            img.className = 'h-full w-full object-cover';
            img.src = item.preview;

            const indexBadge = document.createElement('span');
            indexBadge.className = 'absolute left-2 top-2 rounded bg-black/65 px-2 py-0.5 text-xs text-white';
            indexBadge.textContent = '#' + (index + 1);

            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'absolute right-2 top-2 rounded-full bg-black/60 px-2 py-1 text-xs text-white hover:bg-black/80';
            button.textContent = '删除';
            button.addEventListener('click', () => removeImage(index));

            wrapper.appendChild(img);
            wrapper.appendChild(indexBadge);
            wrapper.appendChild(button);
            imagePreview.appendChild(wrapper);
        });
    }

    function createVideoThumbnail(file) {
        return new Promise((resolve) => {
            const video = document.createElement('video');
            const objectUrl = URL.createObjectURL(file);
            video.src = objectUrl;
            video.muted = true;
            video.playsInline = true;
            video.preload = 'metadata';

            video.addEventListener('loadeddata', () => {
                const canvas = document.createElement('canvas');
                canvas.width = video.videoWidth || 320;
                canvas.height = video.videoHeight || 180;
                const context = canvas.getContext('2d');
                context.drawImage(video, 0, 0, canvas.width, canvas.height);
                const thumbnail = canvas.toDataURL('image/jpeg', 0.82);
                URL.revokeObjectURL(objectUrl);
                resolve(thumbnail);
            }, { once: true });

            video.addEventListener('error', () => {
                URL.revokeObjectURL(objectUrl);
                resolve(null);
            }, { once: true });
        });
    }

    function renderVideoPreview() {
        videoPreview.innerHTML = '';

        videoFiles.forEach((item, index) => {
            const card = document.createElement('div');
            card.className = 'overflow-hidden rounded-xl border border-warm-200 bg-warm-50';

            const cover = document.createElement('div');
            cover.className = 'relative aspect-video bg-warm-200';

            if (item.thumbnail) {
                const img = document.createElement('img');
                img.src = item.thumbnail;
                img.className = 'h-full w-full object-cover';
                cover.appendChild(img);
            }

            const play = document.createElement('div');
            play.className = 'absolute inset-0 flex items-center justify-center';
            play.innerHTML = '<div class="w-10 h-10 rounded-full bg-black/45 flex items-center justify-center"><svg class="w-5 h-5 text-white ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg></div>';
            cover.appendChild(play);

            const meta = document.createElement('div');
            meta.className = 'flex items-center justify-between gap-2 px-3 py-2 text-xs text-warm-700';

            const text = document.createElement('span');
            text.className = 'truncate';
            text.textContent = item.file.name + ' (' + (item.file.size / 1024 / 1024).toFixed(2) + ' MB)';

            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'rounded bg-black/70 px-2 py-1 text-white hover:bg-black/85';
            button.textContent = '删除';
            button.addEventListener('click', () => removeVideo(index));

            meta.appendChild(text);
            meta.appendChild(button);
            card.appendChild(cover);
            card.appendChild(meta);
            videoPreview.appendChild(card);
        });
    }

    imageInput.addEventListener('change', () => {
        clearError(imageError);

        const incoming = [...imageInput.files];
        const invalid = incoming.find((file) => !file.type.startsWith('image/') || file.size > maxImageSize);

        if (invalid) {
            showError(imageError, '图片格式或大小不合法（单张最多 10MB）。');
            syncInputFiles(imageInput, imageFiles);
            return;
        }

        imageFiles = [
            ...imageFiles,
            ...incoming.map((file, index) => ({
                file,
                id: Date.now() + '-' + index + '-' + Math.random().toString(36).slice(2),
                preview: URL.createObjectURL(file),
            })),
        ];

        syncInputFiles(imageInput, imageFiles);
        renderImagePreview();
    });

    videoInput.addEventListener('change', async () => {
        clearError(videoError);

        const incoming = [...videoInput.files];
        const invalid = incoming.find((file) => !file.type.startsWith('video/') || file.size > maxVideoSize);

        if (invalid) {
            showError(videoError, '视频格式或大小不合法（单个最多 100MB）。');
            syncInputFiles(videoInput, videoFiles);
            return;
        }

        const prepared = await Promise.all(incoming.map(async (file) => ({
            file,
            id: Date.now() + '-' + Math.random().toString(36).slice(2),
            thumbnail: await createVideoThumbnail(file),
        })));

        videoFiles = [...videoFiles, ...prepared];
        syncInputFiles(videoInput, videoFiles);
        renderVideoPreview();
    });
</script>
@endsection
