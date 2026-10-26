<aside class="momo-note {{ $class ?? '' }}">
    <span class="momo-note-mascot momo-note-{{ $pose ?? 'groom' }}" aria-hidden="true">
        @include('components.momo-companion')
    </span>
    <span class="momo-note-copy">
        <span class="momo-note-label">{{ $label ?? 'A LITTLE NOTE FROM MOMO' }}</span>
        <span class="momo-note-message">{{ $message }}</span>
    </span>
    <span class="momo-note-heart" aria-hidden="true">♡</span>
</aside>
