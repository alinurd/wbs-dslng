<!-- resources/views/livewire/components/table.blade.php -->
@props([
    'records' => [],
    'selectedItems' => [],
    'permissions' => [],
    'columns' => [],
    'sortBy' => '',
    'getSortIcon' => '',
    'onSort' => '',
    'onView' => '',
    'onEdit' => '',
    'onDelete' => '',
    'onSelectItem' => '',
    'firstItem' => 0,
    'emptyMessage' => 'Tidak ada data ditemukan'
])

<div class="overflow-x-auto border border-gray-200 rounded-lg bg-white shadow-sm transition-all duration-300">
  @if(session()->has('success') || session()->has('error'))
<div class="fixed top-4 right-4 animate-fade-in z-50">
    <div class="p-4 rounded-xl shadow-lg 
        {{ session()->has('success') ? 'bg-green-500' : 'bg-red-500' }}
        text-white">
        {{ session('success') ?? session('error') }}
    </div>
</div>
@endif

    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-[rgb(0,111,188)]">
            <tr>
                <th class="px-4 py-3 text-center text-sm font-medium text-white uppercase tracking-wider border-b border-[rgb(0,95,160)] w-12">
                    #
                </th>
                <th class="px-4 py-3 text-center text-sm font-medium text-white uppercase tracking-wider border-b border-[rgb(0,95,160)] w-16">
                    No
                </th>
                
                @foreach($columns as $column => $label)
                    <th wire:click="{{ $onSort }}('{{ $column }}')"
                        class="px-4 py-3 text-left text-sm font-medium text-white uppercase tracking-wider border-b border-[rgb(0,95,160)] cursor-pointer group transition-all duration-200">
                        <div class="group inline-flex items-center gap-x-2 hover:text-gray-200">
                            {{ $label }}
                            <i
                                    class="fas {{ $this->getSortIcon($column) }} text-gray-300 group-hover:text-gray-200 transition-all duration-200"></i>
                         </div>
                    </th>
                @endforeach
                
                <th class="px-4 py-3 text-center text-sm font-medium text-white uppercase tracking-wider border-b border-[rgb(0,95,160)]">
                    Aksi
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($records as $index => $record)
                <tr class="hover:bg-blue-50 transition-all duration-200 animate-fade-in">
                    <td class="px-4 py-3 whitespace-nowrap text-center border-b border-gray-100">
                        <input wire:model.live="{{ $onSelectItem }}" type="checkbox" value="{{ $record->id }}"
                            class="h-4 w-4 rounded border-gray-300 text-[rgb(0,111,188)] focus:ring-[rgb(0,111,188)] transition-all duration-200">
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 text-center border-b border-gray-100">
                        {{ $firstItem + $index }}
                    </td>
                    
                    <!-- Dynamic Columns -->
                    @foreach($columns as $column => $label)
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 border-b border-gray-100">
                            @if($column === 'is_active')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $record->$column ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} transition-all duration-200">
                                    {{ $record->$column ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            @elseif(str_contains($column, 'created_at') || str_contains($column, 'updated_at'))
                                {{ $record->$column ? $record->$column->format('d/m/Y H:i') : '-' }}
                            @else
                                {{ $record->$column ?? '-' }}
                            @endif
                        </td>
                    @endforeach
                    
                    <td class="px-4 py-3 whitespace-nowrap text-center border-b border-gray-100">
                        <div class="flex justify-center gap-1">
                            @if ($permissions['view'] ?? false)
                                <button wire:click="{{ $onView }}({{ $record->id }})"
                                    class="inline-flex items-center px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-all duration-200 transform hover:scale-110 text-xs"
                                    title="View">
                                    <i class="fas fa-eye w-3 h-3"></i>
                                </button>
                            @endif

                            @if ($permissions['edit'] ?? false)
                                <button wire:click="{{ $onEdit }}({{ $record->id }})"
                                    class="inline-flex items-center px-2 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition-all duration-200 transform hover:scale-110 text-xs"
                                    title="Edit">
                                    <i class="fas fa-edit w-3 h-3"></i>
                                </button>
                            @endif

                            @if ($permissions['delete'] ?? false)
                                <button wire:click="{{ $onDelete }}({{ $record->id }})"
                                    wire:confirm="Apakah Anda yakin menghapus data ini?"
                                    class="inline-flex items-center px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-all duration-200 transform hover:scale-110 text-xs"
                                    title="Hapus">
                                    <i class="fas fa-trash w-3 h-3"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($columns) + 3 }}" class="px-6 py-12 text-center bg-white">
                        <div class="flex flex-col items-center justify-center animate-pulse">
                            <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 text-base font-medium">{{ $emptyMessage }}</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>