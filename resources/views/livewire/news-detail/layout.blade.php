<div>
    @include('components.partials.header')
    <livewire:news-detail.index
        wire:key="news-detail-index-{{ $locale }}"
        wire:listener="languageChanged"
    />
    @include('components.partials.footer')
</div>
