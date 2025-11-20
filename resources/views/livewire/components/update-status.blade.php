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
                class="modal-content bg-white rounded-lg shadow-xl w-full transform transition-all duration-300 scale-95 animate-scale-in">
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
                    <div class="modal-body p-0 max-h-[75vh] overflow-hidden flex">
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
                                            class="flex items-center space-x-2 px-3 py-1.5 bg-{{$data['status_ex']['color']}}-50 rounded-lg border border-{{$data['status_ex']['color']}}-200">
                                            <div class="w-2 h-2 bg-{{$data['status_ex']['color']}}-500 rounded-full animate-pulse"></div>
                                            <span class="text-sm font-medium text-{{$data['status_ex']['color']}}-700">{{ $data['status_ex']['name'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Input -->
                            <div class="flex-1 overflow-y-auto p-6 bg-white">
                                <div class="space-y-6">
                                    <!-- Hidden input untuk action -->
                                    <input type="hidden" name="submission_action" wire:model="submission_action">
                                    <input type="hidden" name="pengaduan_id" wire:model="pengaduan_id" value="pengaduan_id">

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
                                    <div>
                                        <label for="file_upload" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-paperclip mr-2 text-blue-500"></i>
                                            Lampiran File
                                        </label>
                                        <div
                                            class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition duration-150 ease-in-out">
                                            <div class="space-y-1 text-center">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor"
                                                    fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                    <path
                                                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <div class="flex text-sm text-gray-600">
                                                    <label for="file_upload"
                                                        class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                        <span>Upload file</span>
                                                        <input id="file_upload" name="file_upload" type="file"
                                                            wire:model="file_upload" class="sr-only" multiple>
                                                    </label>
                                                    <p class="pl-1">atau drag and drop</p>
                                                </div>
                                                <p class="text-xs text-gray-500">
                                                    PNG, JPG, PDF, DOCX maksimal 10MB
                                                </p>
                                            </div>
                                        </div>

                                        <!-- File Preview -->
                                        @if ($file_upload)
                                            <div class="mt-3">
                                                <p class="text-sm font-medium text-gray-700">File terpilih:</p>
                                                <div
                                                    class="mt-1 flex items-center justify-between bg-gray-50 px-3 py-2 rounded-lg">
                                                    <div class="flex items-center">
                                                        <i class="fas fa-file mr-2 text-gray-400"></i>
                                                        {{-- <span
                                                            class="text-sm text-gray-600">{{ $file_upload->getClientOriginalName() }}</span> --}}
                                                    </div>
                                                    <button type="button" wire:click="removeFile"
                                                        class="text-red-500 hover:text-red-700">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endif

                                        @error('file_upload')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
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
                                                    <p>• Catatan yang Anda berikan akan tercatat dalam history pengaduan</p>
                                                    <p>• File yang diupload akan disimpan sebagai lampiran</p>
                                                    <p>• Pastikan informasi yang diberikan sudah benar sebelum submit</p>
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
                            {{-- {{dd($data['user']['user'])}} --}}
                          <!-- Footer -->
<!-- Footer -->
<div class="modal-footer border-t border-gray-200 px-6 py-4 flex justify-end space-x-3 flex-wrap gap-2 relative">
    @foreach ($data['user']['sts'] as $p)
        @if($p['param_int'] !== $data['status_id'])
            @if($p['param_int'] == 5) {{-- Forward --}}
                <!-- Button untuk membuka dropdown forward -->
                <div class="relative">
                    <button type="button" 
                wire:click="ShowFWD({{$data['id']}})"
                class="px-6 py-2 bg-{{$p['param_str']}}-500 hover:bg-{{$p['param_str']}}-600 text-white rounded-lg transition-all duration-300 transform hover:scale-105 font-medium shadow-sm flex items-center">
            <i class="fas fa-share me-1"></i>
            <span>{{$p['data_en']}} FORWARD</span>
        </button>

                    <!-- Dropdown untuk pilihan forward -->
                    @if($showForwardDropdown)
                    <div class="absolute bottom-full left-0 mb-2 p-4 bg-white border border-gray-200 rounded-lg shadow-lg z-50 w-64">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Pilih Tujuan Forward:
                        </label>
                       <select wire:model="forwardDestination" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                <option value="">-- Pilih Tujuan --</option>
                @foreach($this->getForwardOptions() as $option)
                    <option value="{{ $option->id }}">{{ $option->data_id }}</option>
                @endforeach
            </select>
                        
                        <div class="mt-3 flex space-x-2">
                <button type="button" 
                        wire:click="setActionWithForward({{$p['param_int']}}, {{$data['id']}})"
                        class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg font-medium flex-1 text-sm disabled:bg-gray-400 disabled:cursor-not-allowed"
                        {{ empty($showForwardDropdown) ? 'disabled' : '' }}>
                    <i class="fas fa-paper-plane me-1"></i>
                    Submit Forward
                </button>
                <button type="button" 
                        wire:click="hideForwardDropdown"
                        class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium">
                    <i class="fas fa-times"></i>
                </button>
            </div>
                    </div>
                    @endif
                </div>

            @else
                <!-- Button untuk status lainnya -->
                <button type="submit" 
                        wire:click="setAction({{$p['param_int']}}, {{$data['id']}})"
                        class="px-6 py-2 bg-{{$p['param_str']}}-500 hover:bg-{{$p['param_str']}}-600 text-white rounded-lg transition-all duration-300 transform hover:scale-105 font-medium shadow-sm flex items-center">
                    <i class="fas fa-check-circle me-1"></i>
                    <span>{{$p['data_en']}}</span>
                </button>
            @endif
        @endif
    @endforeach

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
