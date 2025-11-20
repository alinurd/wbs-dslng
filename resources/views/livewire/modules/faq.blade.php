<div class="faq">    
@include('livewire.components.head-card',['title'=>'Frequently Asked Questions', 'dsc'=>'Temukan jawaban atas pertanyaan umum seputar sistem pelaporan'])
    <div class="faq-container">

        <div class="faq-content">
            @foreach ($dataFAQ as $index => $faq)
                <div
                    class="faq-item bg-white rounded-xl shadow-md border border-gray-100 mb-5 transition-all duration-300 hover:shadow-lg">
                    <div class="faq-question p-6 cursor-pointer flex justify-between items-center"
                        onclick="toggleAnswer({{ $index }})">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <span
                                class="faq-number bg-blue-100 text-blue-600 rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm font-bold">
                                {{ $index + 1 }}
                            </span>
                            {{ $faq['pertanyaan']->data_id }}
                        </h3>
                        <span class="faq-icon text-blue-500 text-xl transition-transform duration-300">+</span>
                    </div>

                    <div class="faq-answer hidden px-6 pb-6">
                        @if (count($faq['jawaban']) > 0)
                            <div class="space-y-4">
                                @foreach ($faq['jawaban'] as $jawaban)
                                    <div class="bg-blue-50 rounded-lg p-4 border-l-4 border-blue-500">
                                        <div class="flex items-start">
                                            <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-3 flex-shrink-0"
                                                fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            <p class="text-gray-700 leading-relaxed">{{ $jawaban->data_id }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="bg-yellow-50 rounded-lg p-4 border-l-4 border-yellow-400">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-yellow-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <p class="text-yellow-700 italic">Belum ada jawaban untuk pertanyaan ini</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        @include('livewire.components.suport')
    </div>

    <script>
        function toggleAnswer(index) {
            const answer = document.querySelectorAll('.faq-answer')[index];
            const icon = document.querySelectorAll('.faq-icon')[index];

            if (answer.classList.contains('hidden')) {
                answer.classList.remove('hidden');
                icon.textContent = '−';
                icon.style.transform = 'rotate(180deg)';
            } else {
                answer.classList.add('hidden');
                icon.textContent = '+';
                icon.style.transform = 'rotate(0deg)';
            }
        }
    </script>
</div>
