<div class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-tasks mr-2 text-purple-500"></i>
                        {{ __('dashboard.charts.progress') }}
                    </h2>
                    <div class="space-y-4">
                        @foreach($progress_bulanan as $progress)
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-3 h-3 rounded-full bg-{{ $progress['color'] }}-500"></div>
                                    <span class="text-sm font-medium text-gray-700">{{ $progress['label'] }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm text-gray-600">{{ $progress['jumlah'] }}                         
                                        {{ __('root.complain') }}</span>
                                    <span class="text-sm font-bold text-{{ $progress['color'] }}-600">{{ $progress['persentase'] }}%</span>
                                </div>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-{{ $progress['color'] }}-500 h-2 rounded-full transition-all duration-1000 ease-out" 
                                     style="width: {{ $progress['persentase'] }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>