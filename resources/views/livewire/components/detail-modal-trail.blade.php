@props([
    'show' => false,
    'title' => 'Detail Data',
    'data' => [],
    'onClose' => '',
])

@if ($show)
    <div class="fixed inset-0 z-50 overflow-y-auto animate-fade-in" style="background-color: rgba(0,0,0,0.5)">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="modal-content bg-white rounded-lg shadow-xl w-full max-w-6xl transform transition-all duration-300 scale-95">
                <!-- Header -->
                <div class="modal-header bg-gradient-to-r from-[rgb(0,111,188)] to-[rgb(0,95,160)] text-white rounded-t-lg px-6 py-5 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="flex items-center justify-center w-10 h-10 bg-white/20 rounded-full">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div>
                                <h5 class="modal-title text-xl font-bold tracking-tight">
                                    {{ $title }}
                                </h5>
                                <p class="text-white/80 text-sm">
                                    Detail informasi audit log
                                </p>
                            </div>
                        </div>
                        <button type="button" wire:click="{{ $onClose }}"
                            class="flex items-center justify-center w-9 h-9 rounded-full hover:bg-white/20 transition-all duration-300 hover:rotate-90">
                            <i class="fas fa-times text-base"></i>
                        </button>
                    </div>
                </div>

                <!-- Body -->
                <div class="modal-body p-6 max-h-[80vh] overflow-y-auto">
                    <!-- Informasi Umum -->
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-6">
                        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 rounded-t-lg">
                            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                Informasi Umum
                            </h3>
                        </div>
                        <div class="p-4 space-y-4">
                            @foreach ($data['common'] ?? [] as $label => $item)
                                @if (!in_array($label, ['Data Baru', 'Data Lama']))
                                    <div class="flex flex-col sm:flex-row sm:items-start justify-between border-b border-gray-100 pb-3 last:border-b-0">
                                        <div class="sm:w-2/5 mb-1 sm:mb-0">
                                            <span class="font-semibold text-gray-700 text-sm">{{ $label }}:</span>
                                        </div>
                                        <div class="sm:w-3/5">
                                            @if ($label === 'Action')
                                                @php
                                                    // Handle both string and array formats for Action
                                                    $actionValue = '';
                                                    $actionColor = 'bg-gray-100 text-gray-800';
                                                    $actionIcon = 'fas fa-history';
                                                    $actionText = '';
                                                    
                                                    if (is_string($item)) {
                                                        $actionValue = $item;
                                                    } elseif (is_array($item) && isset($item['value'])) {
                                                        $actionValue = $item['value'];
                                                    }
                                                    
                                                    $actionColors = [
                                                        'created' => 'bg-green-100 text-green-800',
                                                        'updated' => 'bg-blue-100 text-blue-800', 
                                                        'deleted' => 'bg-red-100 text-red-800'
                                                    ];
                                                    
                                                    $actionIcons = [
                                                        'created' => 'fas fa-plus',
                                                        'updated' => 'fas fa-edit',
                                                        'deleted' => 'fas fa-trash'
                                                    ];
                                                    
                                                    $actionLabels = [
                                                        'created' => 'Dibuat',
                                                        'updated' => 'Diperbarui',
                                                        'deleted' => 'Dihapus'
                                                    ];
                                                    
                                                    if (isset($actionColors[$actionValue])) {
                                                        $actionColor = $actionColors[$actionValue];
                                                        $actionIcon = $actionIcons[$actionValue] ?? 'fas fa-history';
                                                        $actionText = $actionLabels[$actionValue] ?? ucfirst($actionValue);
                                                    } elseif (is_array($item) && isset($item['label'])) {
                                                        // Handle jika sudah ada format array
                                                        $actionColor = $item['color'] ?? 'bg-gray-100 text-gray-800';
                                                        $actionIcon = $item['icon'] ?? 'fas fa-history';
                                                        $actionText = $item['label'] ?? ucfirst($actionValue);
                                                    } else {
                                                        $actionText = ucfirst($actionValue);
                                                    }
                                                @endphp
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $actionColor }}">
                                                    <i class="{{ $actionIcon }} mr-1.5 text-xs"></i>
                                                    {{ $actionText }}
                                                </span>
                                            @elseif($label === 'User')
                                                @if(is_array($item))
                                                    <div class="space-y-1">
                                                        <div class="flex items-center space-x-2">
                                                            <i class="fas fa-user text-gray-400"></i>
                                                            <span class="text-gray-900 text-sm font-medium">
                                                                {{ $item['value'] ?? $item['name'] ?? 'System' }}
                                                            </span>
                                                        </div>
                                                        @if(isset($item['email']))
                                                            <div class="flex items-center space-x-2 text-sm text-gray-600">
                                                                <i class="fas fa-envelope text-gray-400 text-xs"></i>
                                                                <span>{{ $item['email'] }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <div class="flex items-center space-x-2">
                                                        <i class="fas fa-user text-gray-400"></i>
                                                        <span class="text-gray-900 text-sm font-medium">{{ $item ?? 'System' }}</span>
                                                    </div>
                                                @endif
                                            @elseif($label === 'IP Address')
                                                <div class="flex items-center space-x-2">
                                                    <i class="fas fa-globe text-gray-400"></i>
                                                    <span class="text-gray-900 text-sm font-mono">{{ $item ?? '-' }}</span>
                                                </div>
                                            @elseif($label === 'Dibuat Pada')
                                                <div class="flex items-center space-x-2">
                                                    <i class="fas fa-calendar text-gray-400"></i>
                                                    <span class="text-gray-900 text-sm">{{ $item }}</span>
                                                </div>
                                            @elseif(is_array($item))
                                                @if(isset($item['formatted']))
                                                    {!! $item['formatted'] !!}
                                                @elseif(isset($item['display']))
                                                    {!! $item['display'] !!}
                                                @else
                                                    <pre class="text-xs bg-gray-100 p-2 rounded overflow-x-auto">{{ json_encode($item, JSON_PRETTY_PRINT) }}</pre>
                                                @endif
                                            @else
                                                <span class="text-gray-900 text-sm font-medium">{{ $item ?? '-' }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- Layout Dua Kolom untuk Data Baru & Lama -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Kolom Kiri - Data Baru -->
                        <div class="space-y-6">
                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 rounded-t-lg">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                            <i class="fas fa-plus-circle text-green-500 mr-2"></i>
                                            Data Baru
                                        </h3>
                                        @if(isset($data['Data Baru']) && !empty($data['Data Baru']))
                                            @php
                                                $count = 0;
                                                if (is_array($data['Data Baru'])) {
                                                    $count = count($data['Data Baru']);
                                                } elseif (is_string($data['Data Baru']) && !empty($data['Data Baru'])) {
                                                    $count = 1;
                                                }
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-database mr-1"></i>
                                                {{ $count }} field{{ $count != 1 ? 's' : '' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="p-4">
                                    @if(isset($data['Data Baru']) && !empty($data['Data Baru']))
                                        <div class="bg-gray-50 border border-gray-200 rounded-lg overflow-hidden">
                                            @if(is_array($data['Data Baru']))
                                                <table class="min-w-full divide-y divide-gray-200">
                                                    <tbody class="divide-y divide-gray-200">
                                                        @foreach($data['Data Baru'] as $key => $item)
                                                            @php
                                                                // Determine the actual value to display
                                                                $value = $item;
                                                                $formattedValue = null;
                                                                $isArray = false;
                                                                
                                                                if (is_array($item)) {
                                                                    if (isset($item['formatted'])) {
                                                                        $formattedValue = $item['formatted'];
                                                                    } elseif (isset($item['display'])) {
                                                                        $formattedValue = $item['display'];
                                                                    } elseif (isset($item['value'])) {
                                                                        $value = $item['value'];
                                                                        $isArray = is_array($value);
                                                                    } else {
                                                                        $isArray = true;
                                                                        $value = $item;
                                                                    }
                                                                } else {
                                                                    $value = $item;
                                                                    $isArray = is_array($value);
                                                                }
                                                                
                                                                // Format if not already formatted
                                                                if (!$formattedValue) {
                                                                    if ($isArray) {
                                                                        $formattedValue = '<pre class="text-xs bg-gray-800 text-gray-100 p-3 rounded-lg overflow-x-auto whitespace-pre-wrap font-mono">' . htmlspecialchars(json_encode($value, JSON_PRETTY_PRINT)) . '</pre>';
                                                                    } elseif (is_bool($value)) {
                                                                        $text = $value ? 'Ya' : 'Tidak';
                                                                        $color = $value ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                                                                        $icon = $value ? 'fa-check' : 'fa-times';
                                                                        $formattedValue = '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ' . $color . '"><i class="fas ' . $icon . ' mr-1"></i>' . $text . '</span>';
                                                                    } elseif ($value === null) {
                                                                        $formattedValue = '<span class="text-gray-400 italic">NULL</span>';
                                                                    } elseif (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                                                                        $formattedValue = '<a href="mailto:' . htmlspecialchars($value) . '" class="text-blue-600 hover:text-blue-800 hover:underline">' . htmlspecialchars($value) . '</a>';
                                                                    } elseif (filter_var($value, FILTER_VALIDATE_URL)) {
                                                                        $formattedValue = '<a href="' . htmlspecialchars($value) . '" target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline truncate block max-w-full">' . htmlspecialchars($value) . '</a>';
                                                                    } elseif (preg_match('/^\d{4}-\d{2}-\d{2}/', $value) || preg_match('/^\d{2}-\d{2}-\d{4}/', $value)) {
                                                                        try {
                                                                            $date = \Carbon\Carbon::parse($value);
                                                                            $formattedValue = '<div class="flex items-center space-x-2"><i class="fas fa-calendar text-gray-400 text-xs"></i><span>' . $date->format('d/m/Y H:i:s') . '</span></div>';
                                                                        } catch (\Exception $e) {
                                                                            $formattedValue = htmlspecialchars($value);
                                                                        }
                                                                    } else {
                                                                        $formattedValue = htmlspecialchars($value);
                                                                    }
                                                                }
                                                            @endphp
                                                            <tr class="hover:bg-gray-100/50 transition-colors">
                                                                <td class="px-4 py-3 w-1/3 align-top">
                                                                    <div class="font-medium text-sm text-gray-700">
                                                                        {{ $key }}
                                                                    </div>
                                                                    <div class="text-xs text-gray-500 mt-1">
                                                                        {{ ucwords(str_replace(['_', '-'], ' ', $key)) }}
                                                                    </div>
                                                                </td>
                                                                <td class="px-4 py-3 w-2/3 align-top">
                                                                    <div class="text-sm text-gray-900 break-words">
                                                                        {!! $formattedValue !!}
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @else
                                                <div class="px-4 py-3">
                                                    <pre class="text-sm bg-gray-800 text-gray-100 p-3 rounded-lg overflow-x-auto whitespace-pre-wrap font-mono">{{ $data['Data Baru'] }}</pre>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="text-center py-8">
                                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 mb-3">
                                                <i class="fas fa-database text-gray-400"></i>
                                            </div>
                                            <p class="text-gray-500">Tidak ada data baru</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Kolom Kanan - Data Lama -->
                        <div class="space-y-6">
                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 rounded-t-lg">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                            <i class="fas fa-history text-gray-500 mr-2"></i>
                                            Data Lama
                                        </h3>
                                        @if(isset($data['Data Lama']) && !empty($data['Data Lama']))
                                            @php
                                                $count = 0;
                                                if (is_array($data['Data Lama'])) {
                                                    $count = count($data['Data Lama']);
                                                } elseif (is_string($data['Data Lama']) && !empty($data['Data Lama'])) {
                                                    $count = 1;
                                                }
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <i class="fas fa-database mr-1"></i>
                                                {{ $count }} field{{ $count != 1 ? 's' : '' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="p-4">
                                    @if(isset($data['Data Lama']) && !empty($data['Data Lama']))
                                        <div class="bg-gray-50 border border-gray-200 rounded-lg overflow-hidden">
                                            @if(is_array($data['Data Lama']))
                                                <table class="min-w-full divide-y divide-gray-200">
                                                    <tbody class="divide-y divide-gray-200">
                                                        @foreach($data['Data Lama'] as $key => $item)
                                                            @php
                                                                // Determine the actual value to display
                                                                $value = $item;
                                                                $formattedValue = null;
                                                                $isArray = false;
                                                                
                                                                if (is_array($item)) {
                                                                    if (isset($item['formatted'])) {
                                                                        $formattedValue = $item['formatted'];
                                                                    } elseif (isset($item['display'])) {
                                                                        $formattedValue = $item['display'];
                                                                    } elseif (isset($item['value'])) {
                                                                        $value = $item['value'];
                                                                        $isArray = is_array($value);
                                                                    } else {
                                                                        $isArray = true;
                                                                        $value = $item;
                                                                    }
                                                                } else {
                                                                    $value = $item;
                                                                    $isArray = is_array($value);
                                                                }
                                                                
                                                                // Format if not already formatted
                                                                if (!$formattedValue) {
                                                                    if ($isArray) {
                                                                        $formattedValue = '<pre class="text-xs bg-gray-800 text-gray-100 p-3 rounded-lg overflow-x-auto whitespace-pre-wrap font-mono">' . htmlspecialchars(json_encode($value, JSON_PRETTY_PRINT)) . '</pre>';
                                                                    } elseif (is_bool($value)) {
                                                                        $text = $value ? 'Ya' : 'Tidak';
                                                                        $color = $value ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                                                                        $icon = $value ? 'fa-check' : 'fa-times';
                                                                        $formattedValue = '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ' . $color . '"><i class="fas ' . $icon . ' mr-1"></i>' . $text . '</span>';
                                                                    } elseif ($value === null) {
                                                                        $formattedValue = '<span class="text-gray-400 italic">NULL</span>';
                                                                    } elseif (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                                                                        $formattedValue = '<a href="mailto:' . htmlspecialchars($value) . '" class="text-blue-600 hover:text-blue-800 hover:underline">' . htmlspecialchars($value) . '</a>';
                                                                    } elseif (filter_var($value, FILTER_VALIDATE_URL)) {
                                                                        $formattedValue = '<a href="' . htmlspecialchars($value) . '" target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline truncate block max-w-full">' . htmlspecialchars($value) . '</a>';
                                                                    } elseif (preg_match('/^\d{4}-\d{2}-\d{2}/', $value) || preg_match('/^\d{2}-\d{2}-\d{4}/', $value)) {
                                                                        try {
                                                                            $date = \Carbon\Carbon::parse($value);
                                                                            $formattedValue = '<div class="flex items-center space-x-2"><i class="fas fa-calendar text-gray-400 text-xs"></i><span>' . $date->format('d/m/Y H:i:s') . '</span></div>';
                                                                        } catch (\Exception $e) {
                                                                            $formattedValue = htmlspecialchars($value);
                                                                        }
                                                                    } else {
                                                                        $formattedValue = htmlspecialchars($value);
                                                                    }
                                                                }
                                                            @endphp
                                                            <tr class="hover:bg-gray-100/50 transition-colors">
                                                                <td class="px-4 py-3 w-1/3 align-top">
                                                                    <div class="font-medium text-sm text-gray-700">
                                                                        {{ $key }}
                                                                    </div>
                                                                    <div class="text-xs text-gray-500 mt-1">
                                                                        {{ ucwords(str_replace(['_', '-'], ' ', $key)) }}
                                                                    </div>
                                                                </td>
                                                                <td class="px-4 py-3 w-2/3 align-top">
                                                                    <div class="text-sm text-gray-900 break-words">
                                                                        {!! $formattedValue !!}
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @else
                                                <div class="px-4 py-3">
                                                    <pre class="text-sm bg-gray-800 text-gray-100 p-3 rounded-lg overflow-x-auto whitespace-pre-wrap font-mono">{{ $data['Data Lama'] }}</pre>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="text-center py-8">
                                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 mb-3">
                                                <i class="fas fa-database text-gray-400"></i>
                                            </div>
                                            <p class="text-gray-500">Tidak ada data lama</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer border-t border-gray-200 px-6 py-4 flex justify-end">
                    <button type="button" wire:click="{{ $onClose }}"
                        class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-all duration-300 transform hover:scale-105 font-medium">
                        <i class="fas fa-times me-2"></i>Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif