@props([
    'show' => false,
    'title' => 'Detail Data',
    'data' => [],
    'onClose' => '',
])
@if ($show)
    {{-- {{dd($data['user']['sts'])}} --}}
    <div class="fixed inset-0 z-50  animate-fade-in" style="background-color: rgba(0,0,0,0.5)">
        <div class="flex min-h-full items-center justify-center p-4">
            <div
                class="modal-content bg-white rounded-lg shadow-xl w-full transform transition-all duration-300 scale-95 ">
                <!-- Form wrapper -->
                <form wire:submit.prevent="submitForm">
                    <!-- Header -->
                    <div
                        class="modal-header bg-gradient-to-r from-[rgb(0,111,188)] to-[rgb(0,95,160)] text-white rounded-t-lg px-6 py-5 shadow-lg">
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
                                        Silahkan Berikan Catatan Anda
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
                    <div class="modal-body p-0 max-h-[75vh]  flex">
                         <!-- Sidebar Informasi Pengaduan -->
                        <div class="w-1/3 border-r border-gray-200 bg-gray-50 flex flex-col">
                            <div class="p-6 flex-1 flex flex-col">
                                <h6 class="font-semibold text-gray-700 mb-4 flex items-center">
                                    <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                                    Informasi Pengaduan
                                </h6>
                                
                                <!-- Komponen log dengan height yang sesuai -->
                                <div class="flex-1">
                                    @include('livewire.components.log', [
                                        'data' => $data['log'],
                                    ])
                                </div>
                            </div>
                        </div>

                        <!-- Main Content Area -->
                        <div class="w-2/3 flex flex-col">
                            <!-- Header -->
                            <div class="border-b border-gray-200 px-6 py-4 bg-white">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                                        <h6 class="font-semibold text-gray-700">Berikan Catatan Anda</h6>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <div
                                            class="flex items-center space-x-2 px-3 py-1.5 bg-{{ $data['status_ex']['color'] }}-50 rounded-lg border border-{{ $data['status_ex']['color'] }}-200">
                                            <div
                                                class="w-2 h-2 bg-{{ $data['status_ex']['color'] }}-500 rounded-full animate-pulse">
                                            </div>
                                            <span
                                                class="text-sm font-medium text-{{ $data['status_ex']['color'] }}-700">{{ $data['status_ex']['name'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Input -->
                            <div class="flex-1 overflow-y-auto p-6 pt-0 border-b bg-white">
                                <div class="space-y-6">
                                    <!-- Hidden input untuk action -->
                                    <input type="hidden" name="submission_action" wire:model="submission_action">
                                    <input type="hidden" name="pengaduan_id" wire:model="pengaduan_id"
                                        value="pengaduan_id">

                                    <!-- Textarea untuk Catatan -->
                                    <div>
                                        <label for="catatan" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-edit mr-2 text-blue-500"></i>
                                            Catatan / Komentar
                                        </label>
                                        <textarea id="catatan" name="catatan" wire:model="catatan" rows="6"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                                            placeholder="Tulis catatan atau komentar Anda di sini..."></textarea>
                                        @error('catatan')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Input File -->
                                    <div
                                        class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center transition-colors duration-200 ease-in-out hover:border-blue-400 bg-white">
                                        <input type="file" wire:model="lampiran" multiple id="file-input"
                                            class="hidden">

                                        <div class="flex flex-col items-center justify-center space-y-4">
                                            <i class="fas fa-cloud-upload-alt text-5xl text-gray-400"></i>
                                            <div class="space-y-2">
                                                <p class="text-lg font-medium text-gray-700">Klik untuk memilih file</p>
                                                <p class="text-sm text-gray-500 max-w-2xl">
                                                    Maksimal 100MB per file. Format yang didukung:
                                                    ZIP, RAR, DOC, DOCX, XLS, XLSX, PPT, PPTX, PDF,
                                                    JPG, JPEG, PNG, AVI, MP4, 3GP, MP3
                                                </p>
                                            </div>
                                            <button type="button"
                                                onclick="document.getElementById('file-input').click()"
                                                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center">
                                                <i class="fas fa-folder-open mr-2"></i>Pilih File
                                            </button>
                                        </div>
                                        @error('lampiran.*')
                                            <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                                                <div class="flex items-center">
                                                    <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
                                                    <span class="text-red-700">{{ $message }}</span>
                                                </div>
                                            </div>
                                        @enderror
                                        @if ($lampiran && count($lampiran) > 0)
                                            <div class="mt-6">
                                                <h4 class="text-md font-medium text-gray-700 mb-3">File yang akan
                                                    diunggah:</h4>
                                                <div class="space-y-3 max-h-60 overflow-y-auto">
                                                    @foreach ($lampiran as $index => $file)
                                                        <div
                                                            class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
                                                            <div class="flex items-center space-x-4 flex-1">
                                                                <!-- File Icon -->
                                                                @php
                                                                    $extension = strtolower(
                                                                        pathinfo(
                                                                            $file->getClientOriginalName(),
                                                                            PATHINFO_EXTENSION,
                                                                        ),
                                                                    );
                                                                    $icon = 'fa-file';
                                                                    $iconColor = 'text-blue-500';

                                                                    if (
                                                                        in_array($extension, [
                                                                            'jpg',
                                                                            'jpeg',
                                                                            'png',
                                                                            'gif',
                                                                            'bmp',
                                                                        ])
                                                                    ) {
                                                                        $icon = 'fa-file-image';
                                                                        $iconColor = 'text-green-500';
                                                                    } elseif (in_array($extension, ['pdf'])) {
                                                                        $icon = 'fa-file-pdf';
                                                                        $iconColor = 'text-red-500';
                                                                    } elseif (in_array($extension, ['doc', 'docx'])) {
                                                                        $icon = 'fa-file-word';
                                                                        $iconColor = 'text-blue-600';
                                                                    } elseif (in_array($extension, ['xls', 'xlsx'])) {
                                                                        $icon = 'fa-file-excel';
                                                                        $iconColor = 'text-green-600';
                                                                    } elseif (in_array($extension, ['ppt', 'pptx'])) {
                                                                        $icon = 'fa-file-powerpoint';
                                                                        $iconColor = 'text-orange-500';
                                                                    } elseif (in_array($extension, ['zip', 'rar'])) {
                                                                        $icon = 'fa-file-archive';
                                                                        $iconColor = 'text-yellow-500';
                                                                    } elseif (
                                                                        in_array($extension, ['mp3', 'wav', 'aac'])
                                                                    ) {
                                                                        $icon = 'fa-file-audio';
                                                                        $iconColor = 'text-purple-500';
                                                                    } elseif (
                                                                        in_array($extension, [
                                                                            'mp4',
                                                                            'avi',
                                                                            'mov',
                                                                            '3gp',
                                                                        ])
                                                                    ) {
                                                                        $icon = 'fa-file-video';
                                                                        $iconColor = 'text-pink-500';
                                                                    }
                                                                @endphp

                                                                <i
                                                                    class="fas {{ $icon }} {{ $iconColor }} text-2xl"></i>
                                                                <div class="flex-1 min-w-0">
                                                                    <p
                                                                        class="text-sm font-medium text-gray-900 truncate">
                                                                        {{ $file->getClientOriginalName() }}</p>
                                                                    <div
                                                                        class="flex items-center space-x-4 text-xs text-gray-500 mt-1">
                                                                        <span class="flex items-center">
                                                                            <i class="fas fa-weight-hanging mr-1"></i>
                                                                            {{ round($file->getSize() / 1024, 2) }} KB
                                                                        </span>
                                                                        <span class="flex items-center">
                                                                            <i class="fas fa-expand-alt mr-1"></i>
                                                                            {{ strtoupper($extension) }}
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <button type="button"
                                                                wire:click="removeLampiran({{ $index }})"
                                                                class="text-red-500 hover:text-red-700 transition-colors p-2 rounded-full hover:bg-red-100 ml-4"
                                                                title="Hapus file">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <!-- Total Files Info -->
                                                <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                                    <div class="flex items-center justify-between text-sm">
                                                        <span class="text-blue-700 font-medium">
                                                            Total: {{ count($lampiran) }} file
                                                        </span>
                                                        <span class="text-blue-600">
                                                            {{ round(array_sum(array_map(function ($file) {return $file->getSize();}, $lampiran)) /1024 /1024,2) }}
                                                            MB
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>


                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-info-circle text-blue-400"></i>
                                            </div>
                                            <div class="ml-3">
                                                <h3 class="text-sm font-medium text-blue-800">
                                                    Informasi Penting
                                                </h3>
                                                <div class="mt-2 text-sm text-blue-700">
                                                    <p>• Catatan yang Anda berikan akan tercatat dalam history pengaduan
                                                    </p>
                                                    <p>• File yang diupload akan disimpan sebagai lampiran</p>
                                                    <p>• Pastikan informasi yang diberikan sudah benar sebelum submit
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <!-- Button Lengkap -->
                    {{-- {{dd($data['user']['sts'])}} --}}
                    <!-- Footer -->
                    <!-- Footer -->
                    <div class="modal-footer px-6 py-4 flex justify-end space-x-3 flex-wrap gap-2 relative">
    @foreach ($data['user']['sts'] as $p)
        @if ($p['param_int'] !== $data['status_id'])
            @if ($p['param_int'] == 5 && $data['user']['role']['id'] == 4)
                {{-- Forward untuk role 4 --}}
                <div class="relative">
                    <button type="button" wire:click="ShowFWD({{ $data['id'] }})"
                        class="px-6 py-2 bg-{{ $p['param_str'] }}-500 hover:bg-{{ $p['param_str'] }}-600 text-white rounded-lg transition-all duration-300 transform hover:scale-105 font-medium shadow-sm flex items-center">
                        <i class="fas fa-share me-1"></i>
                        <span>{{ $p['data_en'] }}</span>
                    </button>

                    <!-- Dropdown untuk pilihan forward -->
                    @if ($showForwardDropdown)
                        <div class="absolute bottom-full left-0 mb-2 p-4 bg-white border border-gray-200 rounded-lg shadow-lg z-50 w-64">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Pilih Tujuan Forward:
                            </label>
                            <select wire:model="forwardDestination"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                <option value="">-- Pilih Tujuan --</option>
                                @foreach ($this->getForwardOptions() as $option)
                                    <option value="{{ $option->id }}">{{ $option->data_id }}</option>
                                @endforeach
                            </select>

                            <div class="mt-3 flex space-x-2">
                                <button type="button"
                                    wire:click="setActionWithForward({{ $p['param_int'] }}, {{ $data['id'] }})"
                                    class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg font-medium flex-1 text-sm disabled:bg-gray-400 disabled:cursor-not-allowed"
                                    {{ empty($showForwardDropdown) ? 'disabled' : '' }}>
                                    <i class="fas fa-paper-plane me-1"></i>
                                    Submit Forward
                                </button>
                                <button type="button" wire:click="hideForwardDropdown"
                                    class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            @else
                {{-- Button untuk role lainnya --}}
                @if($data['user']['role']['id'] == 6)
                    {{-- Untuk role 6: hanya tampilkan button jika belum di-forward --}}
                    @if($data['sts_fwd'] !== 1)
                        <button type="submit"
                            wire:click="setAction({{ $p['param_int'] }}, {{ $data['id'] }})"
                            class="px-6 py-2 bg-{{ $p['param_str'] }}-500 hover:bg-{{ $p['param_str'] }}-600 text-white rounded-lg transition-all duration-300 transform hover:scale-105 font-medium shadow-sm flex items-center">
                            <i class="fas fa-check-circle me-1"></i>
                            <span>{{ $p['data_en'] }}</span>
                        </button>
                    @endif
                @else
                    {{-- Untuk role selain 6 dan 4 --}}
                    <button type="submit"
                        wire:click="setAction({{ $p['param_int'] }}, {{ $data['id'] }})"
                        class="px-6 py-2 bg-{{ $p['param_str'] }}-500 hover:bg-{{ $p['param_str'] }}-600 text-white rounded-lg transition-all duration-300 transform hover:scale-105 font-medium shadow-sm flex items-center">
                        <i class="fas fa-check-circle me-1"></i>
                        <span>{{ $p['data_en'] }}</span>
                    </button>
                @endif
            @endif
        @endif
    @endforeach

    {{-- Button Read untuk role 6 yang sudah di-forward --}}
    @if($data['sts_fwd'] === 1 && $data['user']['role']['id'] === 6)
        <button 
            class="px-6 py-2 bg-gray-500 text-white rounded-lg font-medium shadow-sm flex items-center opacity-50 cursor-not-allowed"
            disabled>
            <i class="fas fa-check-circle me-1"></i>
            <span>Read</span>
        </button>
    @endif

    <button type="button" wire:click="closeModal"
        class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-all duration-300 transform hover:scale-105 font-medium">
        <i class="fas fa-times me-2"></i>Tutup
    </button>
</div>



                </form>
            </div>
        </div>
    </div>
@endif
