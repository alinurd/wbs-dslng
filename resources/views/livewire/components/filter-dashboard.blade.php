<div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-filter mr-2 text-blue-500"></i>
                    Filter Data
                </h2>
                <button wire:click="resetFilters" 
                        class="text-sm text-gray-600 hover:text-gray-800 flex items-center">
                    <i class="fas fa-undo mr-1"></i>Reset Filter
                </button>
            </div>
             <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Filter Tahun -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                    <select wire:model.live="tahunFilter" 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Tahun</option>
                        @foreach($this->getTahunOptions() as $tahun)
                            <option value="{{ $tahun }}">Tahun {{ $tahun }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Jenis Pengaduan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Pengaduan</label>
                    <select wire:model.live="jenisPengaduanFilter" 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Jenis</option>
                        @foreach($jenisPengaduanList as $p)
                            <option value="{{ $p['id'] }}">{{ $p['data_id'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Direktorat -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Direktorat</label>
                    <select wire:model.live="direktoratFilter" 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Direktorat</option>
                        @foreach($direktoratList as $p)
                            <option value="{{ $p['id'] }}">{{ $p['owner_name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select wire:model.live="statusFilter" 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Status</option>
                        @foreach($stsPengaduanList as $p)
                            <option value="{{ $p['id'] }}">{{ $p['data_id'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter FWD To -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Diteruskan Ke</label>
                    <select wire:model.live="fwdToFilter" 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua</option>
                        @foreach($fwdList as $key => $p)
                            <option value="{{ $p['id'] }}">{{ $p['data_en'] }}</option>
                        @endforeach
                    </select>
                </div> 
                <!-- Filter Code Pengaduan -->
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Code Pengaduan</label>
                    <input type="text" wire:model.live="codePengaduanFilter" 
                           placeholder="Masukkan code pengaduan..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <!-- Active Filters Badge -->
            @if($this->hasActiveFilters())
            <div class="mt-4 flex flex-wrap gap-2">
                @if($tahunFilter)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    Tahun: {{ $tahunFilter }}
                    <button wire:click="$set('tahunFilter', '')" class="ml-1 hover:text-blue-600">
                        <i class="fas fa-times"></i>
                    </button>
                </span>
                @endif
                @if($jenisPengaduanFilter)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Jenis: {{ $this->getJenisPengaduanOptions()[$jenisPengaduanFilter] ?? $jenisPengaduanFilter }}
                    <button wire:click="$set('jenisPengaduanFilter', '')" class="ml-1 hover:text-green-600">
                        <i class="fas fa-times"></i>
                    </button>
                </span>
                @endif
                @if($direktoratFilter)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                    Direktorat: {{$this->getDirektoratName($direktoratFilter) }}
                    <button wire:click="$set('direktoratFilter', '')" class="ml-1 hover:text-purple-600">
                        <i class="fas fa-times"></i>
                    </button>
                </span>
                @endif
                @if($statusFilter)
                {{-- {{dd($this->getComboById($statusFilter))}} --}}
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                    Status: {{$this->getComboById($statusFilter) }}
                    <button wire:click="$set('statusFilter', '')" class="ml-1 hover:text-yellow-600">
                        <i class="fas fa-times"></i>
                    </button>
                </span>
                @endif
                @if($fwdToFilter)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-cyan-100 text-cyan-800">
                    Forward: {{ $this->getComboById($fwdToFilter)}}
                    <button wire:click="$set('fwdToFilter', '')" class="ml-1 hover:text-cyan-600">
                        <i class="fas fa-times"></i>
                    </button>
                </span>
                @endif
                @if($codePengaduanFilter)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-teal-100 text-teal-800">
                    Code: {{ ucfirst(str_replace('_', ' ', $codePengaduanFilter)) }}
                    <button wire:click="$set('codePengaduanFilter', '')" class="ml-1 hover:text-teal-600">
                        <i class="fas fa-times"></i>
                    </button>
                </span>
                @endif 
            </div>
            @endif
        </div>