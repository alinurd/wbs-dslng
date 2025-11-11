<div>

    {{-- Hero Section --}}
    <section class="hero-bg text-white py-20">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight">
                {{ __('wbs.hero.welcome') }}<br>
                <span class="text-yellow-300">{{ __('wbs.hero.system') }}</span><br>
                {{ __('wbs.hero.company') }}
            </h1>
            <p class="text-xl text-blue-100 max-w-3xl mx-auto">
                {{ __('wbs.hero.subtitle') }}
            </p>
        </div>
    </section>

    {{-- Required Information Section --}}
    <section class="py-16 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">
                {{ __('wbs.required_info.title') }}
            </h2>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach (['what', 'where', 'when', 'who', 'how', 'evidence'] as $item)
                    <div class="required-info-item p-6 bg-white rounded-lg shadow-md">
                        <div class="flex items-center gap-3 mb-4">
                            <div
                                class="w-12 h-12 bg-{{ $item }}-100 rounded-full flex items-center justify-center">
                                <i
                                    class="fas fa-{{ $item === 'what' ? 'question-circle' : ($item === 'where' ? 'map-marker-alt' : ($item === 'when' ? 'clock' : ($item === 'who' ? 'user-friends' : ($item === 'how' ? 'cogs' : 'file-alt')))) }} text-{{ $item }}-600 text-xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 uppercase">{{ $item }}</h3>
                        </div>
                        <p class="text-gray-600 leading-relaxed">
                            {{ __('wbs.required_info.' . $item) }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- News Section --}}
    <section class="py-16 bg-gray-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900">{{ __('wbs.news.title') }}</h2>
            </div>

            <div class="grid md:grid-cols-2 gap-8">
                @foreach (range(1, 4) as $index)
                    <div class="news-card bg-white rounded-xl shadow-md overflow-hidden">
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-3">
                                {{ __('wbs.news.news' . $index . '_title') }}</h3>
                            <p class="text-gray-600 mb-4 leading-relaxed">
                                {{ __('wbs.news.news' . $index . '_desc') }}
                            </p>
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
