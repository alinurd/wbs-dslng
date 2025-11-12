<div>

    {{-- Hero Section --}}
    <section class="hero-bg text-white py-20">
        {{-- <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight">
                {{ __('wbs.hero.welcome') }}<br>
                <span class="text-yellow-300">{{ __('wbs.hero.system') }}</span><br>
                {{ __('wbs.hero.company') }}
            </h1>
            <p class="text-xl text-blue-100 max-w-3xl mx-auto">
                {{ __('wbs.hero.subtitle') }}
            </p>
        </div> --}}
    </section>

    {{-- Required Information Section --}}
    <section class="py-16 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">
                {{ __('wbs.required_info.title') }}
            </h2>

            <div class="grid md:grid-cols-2 lg:grid-cols-2 gap-6">
                @php
                    $items = ['what',  'who', 'where', 'how', 'when', 'evidence'];
                @endphp

                @foreach ($items as $item)
                    @php
                        $info = __('wbs.required_info.' . $item);
                        if (is_array($info)) {
                            $icon = $info['i'];
                            $color = $info['c'];
                            $title = $info['t'];
                            $description = $info['d'];
                        } else {
                            // Fallback for old structure
                            continue; // or handle differently
                        }
                    @endphp

                    <div class="required-info-item p-3 bg-white rounded-lg shadow-md">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 bg-{{ $color }} rounded-full flex items-center justify-center">
                                <i class="fas fa-{{ $icon }} text-{{ $color }}-600 text-xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 uppercase">{{ $title }}</h3>
                        </div>
                        <p class="text-gray-600 leading-relaxed">
                            {{ $description }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- News Section --}}
    <section class="py-16 bg-gray-50 new-bg">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="font-bold text-white text-3xl" style="text-shadow: 1px 4px 4px rgba(0,0,0,0.78);">
                    {{ __('wbs.news.title') }}
                </h2>
                <x-button class="mt-4 ms-4 bg-red-500">
                    <i class="fa fa-globe"></i> &nbsp;{{ __('wbs.news.all_new') }}
                </x-button>
            </div>

            <div class="grid md:grid-cols-4 gap-8">
                @foreach (array_slice(__('wbs.news.items'), 0, 4) as $news)
                    <div
                        class="news-card bg-white rounded-xl shadow-md overflow-hidden hover:scale-[1.02] transition relative">
                        <div class="relative">
                            <img src="{{ asset($news['image']) }}" alt="{{ $news['title'] }}"
                                class="w-full h-30 object-cover">

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
                                {{ $news['title'] }}
                            </h3>
                        </div>

                        <div class="p-6">
                            <p class="text-gray-600 mb-4 leading-relaxed line-clamp-3">{{ $news['desc'] }}</p>
                            <a href="#"
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

    {{-- Confidentiality Notice --}}
    <section class="py-12 bg-blue-900 text-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="bg-blue-800 rounded-2xl p-8">
                <i class="fas fa-shield-alt text-4xl mb-6 text-yellow-300"></i>
                <h3 class="text-2xl font-bold mb-4">{{ __('wbs.confidentiality.title') }}</h3>
                <div class="text-blue-100 space-y-4 text-lg leading-relaxed">
                    <p>{{ __('wbs.confidentiality.text1') }}</p>
                    <p>{{ __('wbs.confidentiality.text2') }}</p>
                    <p>{{ __('wbs.confidentiality.text3') }}</p>
                </div>
            </div>
        </div>
    </section>


</div>
