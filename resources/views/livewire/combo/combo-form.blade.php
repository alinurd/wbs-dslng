<div>
    {{-- Inline form jika tidak pakai modal --}}
    @if(!$useModal)
        <div class="bg-white p-4 rounded shadow mb-4">
            @include('livewire.combo.form-fields')
        </div>
    @endif

    {{-- Modal --}}
    @if($useModal && $showModal)
    <div x-data="{ open: @entangle('showModal') }"
         x-show="open"
         class="fixed inset-0 flex items-center justify-center z-50">
        
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" @click="open=false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"></div>

        <div class="bg-white w-full max-w-xl p-6 rounded-xl shadow-2xl relative z-10 transform transition-all"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-90"
             @keydown.escape.window="open=false">

            <button @click="open=false" class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 text-2xl font-bold">&times;</button>
            <h3 class="text-2xl font-bold mb-5 text-gray-800">{{ $updateMode ? 'Edit Data' : 'Tambah Data' }}</h3>
            @include('livewire.combo.form-fields')
        </div>
    </div>
    @endif
</div>
