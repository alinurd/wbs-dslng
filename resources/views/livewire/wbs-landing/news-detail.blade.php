<div class="min-h-screen flex flex-col">

    {{-- {{dd($newsDetail)}}  --}}

    <!-- Header Section -->
    <section class="new-bg-page text-white py-32">
        <div class="text-center max-w-3xl mx-auto px-4">

            <h2 class="font-extrabold text-5xl sm:text-6xl mb-4" style="text-shadow: 1px 4px 4px rgba(0,0,0,0.78);">
                {{ $newsDetail['title_' . $locale] }}
            </h2>
            <span class="text-xs font-semibold px-3 py-1 rounded-full bg-blue-600 text-white">
                {{ $newsDetail['category'] }}
            </span>
        </div>
    </section>

    <!-- News Detail Section -->
    <section class="flex-grow bg-gray-50 py-20">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            @if ($newsDetail)
                <div class="bg-white rounded-3xl shadow-xl overflow-hidden">

                    <!-- Gambar Berita -->
                    @if (!empty($newsDetail['image']))
                        <div class="overflow-hidden rounded-t-3xl">
                            <img src="{{ $newsDetail['image'] ? url('/file/' . base64_encode($newsDetail['image'])) : asset('assets/images/news/4.png') }}"
                                alt="{{ $newsDetail['title_' . $locale] }}"
                                class="w-full h-[400px] object-cover transition-transform duration-500 hover:scale-105" />
                        </div>
                    @endif

                    <!-- Konten Berita -->
                    <div class="p-10 sm:p-12">
                        <!-- Metadata -->
                        <div class="flex justify-between items-center mb-6">
                            <span class="text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($newsDetail['created'])->format('d M Y') }} - @admin
                            </span>
                            <span class="text-xs font-semibold px-3 py-1 text-gray-500">
                                <a href="{{ route('landing.index') }}"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors shadow-md text-sm">
                                    <i class="fas fa-arrow-left"></i>
                                    <span>Kembali</span>
                                </a>
                            </span>
                        </div>

                        <!-- Judul -->
                        <h1 class="text-4xl sm:text-5xl font-bold text-gray-900 mb-6">
                            {{ $newsDetail['title_' . $locale] }}
                        </h1>

                        <div class="lg:flex flex-col items-center relative">
                            <!-- Icon sangat besar -->
                            <i
                                class="fas fa-shield-alt text-blue-100 text-[20rem] opacity-30 absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-0"></i>

                            <div class="relative z-10 w-full">
                                <p class="text-lg sm:text-xl leading-relaxed mb-8">
                                    {!! $newsDetail['content_' . $locale] !!}
                                </p>
                            </div>
                        </div>


                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-5">
    @forelse ($newsDetail['files'] as $file)
        <div
            class="relative flex items-center justify-between
                   bg-white rounded-xl p-4 border border-gray-200
                   hover:border-blue-300 hover:shadow-lg
                   transition-all duration-300">

            <!-- FILE INFO -->
            <div class="flex items-center gap-3 min-w-0 flex-1">
                <div
                    class="flex-shrink-0 w-10 h-10
                           bg-gradient-to-br from-blue-500 to-blue-600
                           rounded-lg flex items-center justify-center">
                    <i class="fas fa-file text-white text-sm"></i>
                </div>

                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">
                        {{ $file['original_name'] }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ strtoupper($file['extension'] ?? 'FILE') }} •
                        {{ $this->formatFileSize($file['size'] ?? 0) }}
                    </p>
                </div>
            </div>

            <!-- ACTION -->
            <button
                type="button"
                wire:click.prevent="downloadFile('{{ $file['path'] }}', '{{ $file['original_name'] }}')"
                class="flex-shrink-0
                       flex items-center gap-2
                       text-green-600 hover:text-green-700
                       bg-green-50 hover:bg-green-100
                       px-3 py-2 rounded-lg
                       transition-colors duration-200
                       text-sm font-medium"
                title="Download {{ $file['original_name'] }}">
                <i class="fas fa-download text-xs"></i>
                <span>Download</span>
            </button>
        </div>
    @empty
    @endforelse
</div>


                    </div>
                </div>
            @else
                <div class="text-center py-32">
                    <h2 class="text-3xl font-bold text-gray-800 mb-4">Berita tidak ditemukan</h2>
                    <a href="{{ route('new.index') }}" class="text-blue-600 hover:text-blue-800 inline-block mt-2">
                        Kembali ke semua berita
                    </a>
                </div>
            @endif
        </div>
    </section>
</div>
