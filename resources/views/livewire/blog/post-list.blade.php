<div class="space-y-4">

    <div class="flex justify-between items-center">
        <h2 class="text-xl font-bold">Daftar Artikel</h2>

        <button
            wire:click="$dispatch('openModal', {
                title: 'Tambah Artikel',
                size: 'lg',
                onConfirm: 'createRecord'
            })"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg">
            + Tambah
        </button>
    </div>

    <table class="w-full border">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2">Judul</th>
                <th class="p-2">Status</th>
                <th class="p-2 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $post)
                <tr class="border-t">
                    <td class="p-2">{{ $post->title }}</td>
                    <td class="p-2">{{ ucfirst($post->status) }}</td>
                    <td class="p-2 text-right space-x-2">

                        <button
                            wire:click="$dispatch('openModal', {
                                title: 'Edit Artikel',
                                size: 'lg',
                                onConfirm: 'editRecord',
                                recordId: {{ $post->id }}
                            })"
                            class="px-3 py-1 bg-yellow-500 text-white rounded">
                            Edit
                        </button>

                        <button
                            wire:click="$dispatch('openModal', {
                                title: 'Hapus Artikel?',
                                size: 'sm',
                                confirmText: 'Ya, Hapus',
                                onConfirm: 'deleteRecord',
                                recordId: {{ $post->id }}
                            })"
                            class="px-3 py-1 bg-red-600 text-white rounded">
                            Hapus
                        </button>

                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- GLOBAL MODAL --}}
    <livewire:components.app-modal />

</div>
