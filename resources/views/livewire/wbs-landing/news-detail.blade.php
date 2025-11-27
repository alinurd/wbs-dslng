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

            @if($newsDetail)
                <div class="bg-white rounded-3xl shadow-xl overflow-hidden">

                    <!-- Gambar Berita -->
                    @if(!empty($newsDetail['image']))
                    <div class="overflow-hidden rounded-t-3xl">
                        <img src="{{ $newsDetail['image'] ? url('/file/'.base64_encode($newsDetail['image'])) : asset('assets/images/news/4.png') }}" alt="{{ $newsDetail['title_' . $locale] }}" class="w-full h-[400px] object-cover transition-transform duration-500 hover:scale-105"/>
                    </div>
                    @endif

                    <!-- Konten Berita -->
                    <div class="p-10 sm:p-12">
                        <!-- Metadata -->
                        <div class="flex justify-between items-center mb-6">
                            <span class="text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($newsDetail['created'])->format('d M Y -h:s') }}
                            </span>
                            <span class="text-xs font-semibold px-3 py-1 text-gray-500">
                                @admin
                            </span>
                        </div>

                        <!-- Judul -->
                        <h1 class="text-4xl sm:text-5xl font-bold text-gray-900 mb-6">
                            {{ $newsDetail['title_' . $locale] }} 
                        </h1>

                        <!-- Isi Berita -->
                        <p class="text-gray-700 text-lg sm:text-xl leading-relaxed mb-8">
                                     {!! $newsDetail['content_' . $locale] !!}
                        </p>

                        <!-- Tombol Download File -->
                       <a href="#" download
                           class="inline-flex items-center bg-green-600 text-white font-semibold px-5 py-3 rounded-lg hover:bg-green-700 transition-colors duration-300 mb-4">
                            <i class="fas fa-download mr-2"></i> Download File
                        </a>
                        <!-- Tombol Kembali -->
                        <a href="{{ route('new.index') }}"
                           class="inline-flex items-center text-blue-600 font-semibold hover:text-blue-800 transition-colors duration-300 ml-4">
                            <i class="fas fa-arrow-left mr-2"></i> {{ __('wbs.news.all_new') }}
                        </a>
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
