@props(['title', 'actions' => null])

<div class="chapter-layout">
    <!-- Chapter Header -->
    <div class="chapter-layout__head">
        <h2 class="chapter-layout__title">{{ $title }}</h2>

        @if ($actions)
            <div class="chapter-layout__actions">
                {{ $actions }}
            </div>
        @endif
    </div>

    <!-- Chapter Content -->
    <div class="chapter-layout__content">
        {{ $slot }}
    </div>
</div>
