@php
    $feedback = session('feedback');
@endphp

@if($feedback)
    <script>
        window.feedbackBannerData = @json($feedback);
    </script>
@endif

<div id="feedback-banner" class="feedback-banner" data-visible="false" role="alert" aria-live="assertive">
    <div class="feedback-banner__icon">
        <svg class="feedback-banner__icon-success" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
        <svg class="feedback-banner__icon-error" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
        </svg>
    </div>
    <div class="feedback-banner__message"></div>
</div>
