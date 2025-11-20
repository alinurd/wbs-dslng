@props([
    'title' => '',
    'dsc' => '',
])
<div class="bg-gradient-to-r from-[#0077C8] to-[#003B73] rounded-2xl p-8 text-white mb-8 shadow-lg">
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between">
            <div class="mb-6 lg:mb-0 flex-1">
                <h2 class="text-3xl font-bold mb-2">{{$title}}</h2>
                <p class="text-blue-100 text-lg">
                    {{$dsc}}
                </p>
            </div>
            <div class="flex flex-col lg:flex-row items-center lg:items-end space-y-4 lg:space-y-0 lg:space-x-6">
                 <div class="hidden lg:flex flex-col items-center">
                    <i class="fas fa-shield-alt text-blue-300 text-8xl opacity-50 mb-4"></i> 
                    <a href="{{ route('dashboard') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors shadow-md text-sm">
                        <i class="fas fa-arrow-left"></i>
                        <span>Kembali</span>
                    </a>
                </div>
            </div>
        </div>
    </div>