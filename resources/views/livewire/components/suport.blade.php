<div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
    <div class="flex items-center space-x-3 mb-3">
        <i class="fas fa-headset text-blue-600 text-xl"></i>
        <h3 class="font-semibold text-blue-900">{{ __('global.need_help') }}</h3>
    </div>
    <p class="text-blue-800 text-sm mb-4">
        {{ __('global.need_help_dsc') }}
    </p>

    {{-- <select wire:model="selectedPengaduanId"
        class="w-full border border-blue-300 rounded-lg py-2 px-3 focus:outline-none focus:ring mb-3">

        <option value="">-- Pilih Pengaduan --</option>
        @foreach ($pengaduanAll as $item)
            <option value="{{ $item['id'] }}">
                {{ $item['code_pengaduan'] }} — {{ $item['perihal'] }}
            </option>
        @endforeach
    </select> --}}

    <button wire:click="runComment"
        class="w-full bg-white hover:bg-blue-100 text-blue-600 border border-blue-300 py-2 px-4 rounded-lg font-medium">
        {{ __('global.contact_suport') }}
    </button>
</div>
