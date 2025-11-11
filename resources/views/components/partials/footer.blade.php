{{-- Footer --}}
    <footer class="bg-gray-900 text-white py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <div
                        class="w-10 h-10 bg-gradient-to-r from-blue-600 to-blue-800 rounded-full flex items-center justify-center text-white font-bold text-xs mx-auto md:mx-0">
                        DSLNG
                    </div>
                </div>
                <div class="text-gray-400">
                    {{ __('wbs.footer.copyright') }}
                </div>
            </div>
        </div>
    </footer>

    {{-- Floating Report Button --}}
    <div class="fixed bottom-6 right-6 z-50">
        <a href="#"
            class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-full shadow-lg font-semibold flex items-center gap-2 transition transform hover:scale-105">
            <i class="fas fa-whistle"></i>
            {{ __('wbs.buttons.report_now') }}
        </a>
    </div>