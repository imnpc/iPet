@props([
    'post',
    'mode' => 'feed',
])

@php
    $sortedMedia = $post->media->sortBy('sort_order')->values();
    $imageMedia = $sortedMedia->where('type', 'image')->values();
    $videoMedia = $sortedMedia->where('type', 'video')->values();

    $visibleImages = $imageMedia->take(9);
    $visibleImageCount = $visibleImages->count();
    $hiddenImageCount = max($imageMedia->count() - 9, 0);

    $gridClass = match (true) {
        $visibleImageCount === 1 => 'grid-cols-1',
        $visibleImageCount === 2 => 'grid-cols-2',
        $visibleImageCount === 4 => 'grid-cols-2',
        default => 'grid-cols-3',
    };

    $mediaMaxWidthClass = match (true) {
        $visibleImageCount === 1 => 'max-w-[540px]',
        $visibleImageCount === 2 => 'max-w-[420px]',
        $visibleImageCount === 4 => 'max-w-[420px]',
        default => 'max-w-[520px]',
    };

    $imageWrapperClass = $mode === 'feed' ? 'mb-4 '.$mediaMaxWidthClass : 'mb-4';
    $videoContainerClass = $mode === 'feed' ? 'rounded-2xl overflow-hidden border border-warm-200 bg-black/95 shadow-sm' : 'rounded-xl overflow-hidden border border-warm-200 bg-black';
@endphp

@if($imageMedia->isNotEmpty())
    <div class="{{ $imageWrapperClass }}">
        <div class="grid {{ $gridClass }} gap-2.5">
            @foreach($visibleImages as $index => $media)
                @if($mode === 'detail')
                    <button type="button" data-lightbox-image class="relative {{ $visibleImageCount === 1 ? 'aspect-[16/10]' : 'aspect-square' }} rounded-xl overflow-hidden bg-warm-100 block" data-image-index="{{ $index }}" data-image-url="{{ $media->url() }}" data-image-alt="动态图片 {{ $index + 1 }}">
                        <img src="{{ $media->url() }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" alt="动态图片 {{ $index + 1 }}">

                        @if($hiddenImageCount > 0 && $index === 8)
                            <div class="absolute inset-0 bg-black/45 flex items-center justify-center text-white text-xl font-bold">
                                +{{ $hiddenImageCount }}
                            </div>
                        @endif
                    </button>
                @else
                    <div class="relative {{ $visibleImageCount === 1 ? 'aspect-[16/10]' : 'aspect-square' }} rounded-xl overflow-hidden bg-warm-100">
                        <img src="{{ $media->url() }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" alt="动态图片 {{ $index + 1 }}">

                        @if($hiddenImageCount > 0 && $index === 8)
                            <div class="absolute inset-0 bg-black/45 flex items-center justify-center text-white text-xl font-bold">
                                +{{ $hiddenImageCount }}
                            </div>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>
    </div>
@endif

@if($videoMedia->isNotEmpty())
    <div class="space-y-3 mb-5">
        @foreach($videoMedia as $media)
            <div class="{{ $videoContainerClass }}">
                @if($mode === 'feed')
                    <div class="relative bg-black" style="height: 420px;">
                        <video controls preload="metadata" class="absolute inset-0 h-full w-full object-contain" poster="{{ $media->thumbnailUrl() }}">
                            <source src="{{ $media->url() }}" type="{{ $media->mime_type ?? 'video/mp4' }}">
                            当前浏览器不支持视频播放。
                        </video>
                    </div>
                @else
                    <video controls preload="metadata" class="w-full max-h-[420px] bg-black" poster="{{ $media->thumbnailUrl() }}">
                        <source src="{{ $media->url() }}" type="{{ $media->mime_type ?? 'video/mp4' }}">
                        当前浏览器不支持视频播放。
                    </video>
                @endif
            </div>
        @endforeach
    </div>
@endif
