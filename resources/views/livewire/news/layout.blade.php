<div>
    @include('components.partials.header')
    <livewire:news.index
        wire:key="news-index-{{ $locale }}"
        wire:listener="languageChanged"
    />
    @include('components.partials.footer')
</div>
