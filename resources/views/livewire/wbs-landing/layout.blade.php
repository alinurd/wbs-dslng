<div>
    @include('components.partials.header')

    <livewire:wbs-landing.index
        wire:key="landing-index-{{ $locale }}"
        wire:listener="languageChanged"
    />

    @include('components.partials.footer')
</div>
