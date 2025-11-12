{{-- Footer --}}
    <footer class="bg-gray-900 text-white py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
             <div class="text-gray-400">
                    {{ __('wbs.footer.copyright') }}
                </div>
        </div>
    </footer>

    
    {{-- Floating Report Button --}}
    <div class="fixed bottom-10 right-10 z-50">
        <a id="UItoTop"
            class="font-semibold flex items-center gap-2 transition transform hover:scale-105 cursor-pointer">
            <i class="fas fa-whistle"></i>
            <img src="{{ asset('assets/images/move-top.png') }}" alt="">
        </a>
    </div>
   


    <script>
    document.getElementById('UItoTop').addEventListener('click', function (e) {
        e.preventDefault();
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
</script>