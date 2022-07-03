@props(['link' => '../', 'text' => __('messages.Prev')])

<div class="lg:absolute lg:top-4.5 lg:right-2.5 mt-2 mr-1 md:my-4 lg:m-0">
    <a href="{{ $link }}" class="py-0 px-6 font-body text-primary font-semibold text-tiny tracking-widest uppercase">
        {{ $text }}
    </a>
</div>