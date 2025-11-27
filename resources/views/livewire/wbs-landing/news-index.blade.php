<div>
    <section class="new-bg-page text-white py-20">
       <div class="text-center mb-12">
                <h2 class="font-bold text-white text-3xl" style="text-shadow: 1px 4px 4px rgba(0,0,0,0.78);">
                    {{ __('wbs.news.title') }}
                </h2>
            <p class="text-gray-50 m-3 text-lg max-w-2xl mx-auto">
                {{ __('wbs.news.dsc') }}
            </p>
            </div>
    </section>
    {{-- News Section --}}
    <section class="py-16 bg-gray-50 ">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-8">
                @foreach ($newsData as $news)
                    <div
                        class=" bg-white rounded-xl shadow-md overflow-hidden hover:scale-[1.02] transition relative">
                        <div class="relative">
                            <img src="{{ $news['image'] ? url('/file/'.base64_encode($news['image'])) : asset('assets/images/news/4.png') }}" alt="{{ $news['title_' . $locale] }}" class="w-full h-30 object-cover"/>
                               <div class="absolute inset-0 bg-black bg-opacity-5"></div>

                            <span
                                class="absolute top-3 left-3 bg-white bg-opacity-80 text-gray-700 text-xs font-medium px-2 py-1 rounded shadow">
                                {{ \Carbon\Carbon::parse($news['created'])->format('M d, Y') }}
                            </span>

                            <span
                                class="absolute top-3 right-3 bg-blue-600 text-white text-xs font-semibold px-3 py-1 rounded-full shadow-md bg-opacity-90">
                                {{ $news['category'] }}
                            </span>

                            <h3 class="absolute inset-0 flex items-center justify-center text-center text-white text-lg font-bold px-4"
                                style="text-shadow: 5px 2px 7px rgba(0, 0, 0, 0.926);">
                                {{ $news['title_'.$locale] }}
                            </h3>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-600 mb-4 leading-relaxed line-clamp-3">{{ $news['desc_'.$locale] }}</p>
                            <a href="{{ route('new-detail.index', ['slug' => $news['slug']]) }}"
                                class="inline-flex items-center text-blue-600 font-semibold hover:text-blue-800">
                                {{ __('wbs.news.read_more') }}
                                <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                        </div>
                    </div>
                @endforeach


            </div>
        </div>
    </section>
 


</div>
