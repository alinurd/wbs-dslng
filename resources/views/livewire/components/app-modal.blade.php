<div>
    @if ($show)
        <div class="fixed inset-0 z-40 flex items-center justify-center bg-gray-900/50">
            <div class="bg-white rounded-2xl shadow-lg w-full max-w-{{ $size }} p-6 relative">
                {{-- Header --}}
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">{{ $title }}</h2>
                    <button wire:click="close" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>

                {{-- Body --}}
                <div class="modal-body">
                    {{ $slot }}
                </div>

                {{-- Footer --}}
                <div class="mt-6 flex justify-end space-x-2">
                    <button
                        wire:click="cancel"
                        type="button"
                        class="px-4 py-2 rounded-lg border text-gray-700 hover:bg-gray-100">
                        {{ $cancelText }}
                    </button>

                    <button
                        wire:click="confirm"
                        type="button"
                        class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700"
                        @disabled($disableConfirm)>
                        {{ $confirmText }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
