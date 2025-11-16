@props(['title', 'actions' => null])

<div class="chapter-layout">
    <!-- Chapter Header -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-3xl font-semibold">{{ $title }}</h2>

        @if ($actions)
            <div class="chapter-actions">
                {{ $actions }}
            </div>
        @endif
    </div>

    <!-- Chapter Content -->
    <div class="chapter-content">
        {{ $slot }}
    </div>
</div>
