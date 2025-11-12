<div class="p-4 bg-white shadow rounded-lg">
    <h2 class="text-xl font-semibold mb-4">{{ $title }}</h2>

    <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    @foreach ($columns as $col)
                        <th class="px-4 py-2 text-left text-gray-700 font-semibold border-b">
                            {{ $col }}
                        </th>
                    @endforeach

                    @if ($actions)
                        <th class="px-4 py-2 text-center text-gray-700 font-semibold border-b">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $row)
                    <tr class="hover:bg-gray-50">
                        @foreach (array_keys($columns) as $key)
                            <td class="px-4 py-2 border-b">{{ $row[$key] ?? '-' }}</td>
                        @endforeach

                        @if ($actions)
                            <td class="px-4 py-2 text-center border-b">
                                @if (in_array('edit', $actions))
                                    <button wire:click="$emit('editData', {{ $row['id'] ?? 0 }})"
                                            class="text-blue-600 hover:underline">Edit</button>
                                @endif
                                @if (in_array('delete', $actions))
                                    <button wire:click="$emit('deleteData', {{ $row['id'] ?? 0 }})"
                                            class="text-red-600 hover:underline ml-2">Delete</button>
                                @endif
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) + ($actions ? 1 : 0) }}" 
                            class="text-center py-4 text-gray-500">
                            Tidak ada data
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination (kalau data adalah Paginator) --}}
    @if (method_exists($data, 'links'))
        <div class="mt-3">
            {{ $data->links() }}
        </div>
    @endif
</div>
