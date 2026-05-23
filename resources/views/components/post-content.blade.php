@props([
    'content',
    'expandable' => false,
    'highlight' => true,
    'collapseMaxClass' => 'max-h-[6.5rem]',
    'wrapperClass' => 'mb-4',
    'textClass' => 'whitespace-pre-wrap break-words text-lg leading-relaxed text-warm-800',
])

@php
    $escapedContent = e((string) $content);
    $formattedContent = $escapedContent;

    if ($highlight) {
        $formattedContent = preg_replace('/(#([^#\s]{1,50})#)/u', '<span class="text-orange-500">$1</span>', $formattedContent);
        $formattedContent = preg_replace('/@([\p{L}\p{N}_-]{1,30})/u', '<span class="text-primary-600">@$1</span>', $formattedContent ?? $escapedContent);
    }

    $formattedContent = nl2br($formattedContent ?? $escapedContent);
@endphp

<div class="{{ $wrapperClass }}">
    <p @class([
        'js-post-content overflow-hidden' => $expandable,
        $collapseMaxClass => $expandable,
        $textClass,
    ]) @if($expandable) data-expanded="false" @endif>{!! $formattedContent !!}</p>

    @if($expandable)
        <button type="button" class="js-post-expand mt-1 text-sm font-medium text-primary-600 hover:text-primary-700" onclick="event.stopPropagation()">展开全文</button>
    @endif
</div>
