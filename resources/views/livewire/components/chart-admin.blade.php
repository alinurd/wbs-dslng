 <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Analytics & Charts 📊</h2>
                    <p class="text-gray-600">Visualisasi data pengaduan untuk analisis yang lebih baik</p>
                </div>
                
            </div>
            
            <!-- Charts Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Status Aduan -->
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200" 
                     x-data="chartContainer('statusAduanChart')">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-chart-pie mr-2 text-blue-600"></i>
                        Status Aduan
                    </h3>
                    <div class="h-72">
                        <canvas x-ref="canvas" 
                                data-chart-data='@json($chartData['status_aduan'] ?? [])'></canvas>
                    </div>
                </div>

                <!-- Jenis Pelanggaran -->
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200"
                     x-data="chartContainer('jenisPelanggaranChart')">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-chart-bar mr-2 text-green-600"></i>
                        Jenis Pelanggaran
                    </h3>
                    <div class="h-72">
                        <canvas x-ref="canvas" 
                                data-chart-data='@json($chartData['jenis_pelanggaran'] ?? [])'></canvas>
                    </div>
                </div>

                <!-- Pergerakan Tahunan -->
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 lg:col-span-2"
                     x-data="chartContainer('pergerakanTahunanChart')">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-chart-line mr-2 text-purple-600"></i>
                        Trend Bulanan {{ $tahunFilter ? 'Tahun ' . $tahunFilter : 'Semua Tahun' }}
                    </h3>
                    <div class="h-72">
                        <canvas x-ref="canvas" 
                                data-chart-data='@json($chartData['pergerakan_tahunan'] ?? [])'></canvas>
                    </div>
                </div>

                <!-- Saluran Aduan -->
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200"
                     x-data="chartContainer('saluranAduanChart')">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-inbox mr-2 text-orange-600"></i>
                        Saluran Aduan
                    </h3>
                    <div class="h-72">
                        <canvas x-ref="canvas" 
                                data-chart-data='@json($chartData['saluran_aduan'] ?? [])'></canvas>
                    </div>
                </div>

                <!-- Direktorat -->
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200"
                     x-data="chartContainer('direktoratChart')">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-building mr-2 text-red-600"></i>
                        Per Direktorat
                    </h3>
                    <div class="h-72">
                        <canvas x-ref="canvas" 
                                data-chart-data='@json($chartData['direktorat'] ?? [])'></canvas>
                    </div>
                </div>
            </div>
        </div>

        <script>
// Alpine.js component untuk mengelola chart
function chartContainer(chartId) {
    return {
        chart: null,
        chartId: chartId,
        
        init() {
            this.initializeChart();
            
            // Listen untuk Livewire update
            Livewire.hook('commit', ({ component, succeed }) => {
                succeed(() => {
                    if (component.name === 'modules.dashboard-index') {
                        console.log(`🔄 Livewire update detected for ${this.chartId}`);
                        setTimeout(() => {
                            this.initializeChart();
                        }, 100);
                    }
                });
            });
        },
        
        initializeChart() {
            const canvas = this.$refs.canvas;
            if (!canvas) return;
            
            const chartDataAttr = canvas.getAttribute('data-chart-data');
            
            if (!chartDataAttr || chartDataAttr === '[]' || chartDataAttr === '{}') {
                this.showNoDataMessage(canvas);
                return;
            }
            
            try {
                const chartData = JSON.parse(chartDataAttr);
                
                // Validasi data
                const isValidData = chartData.data && 
                                  chartData.data.labels && 
                                  chartData.data.labels.length > 0 &&
                                  chartData.data.datasets && 
                                  chartData.data.datasets.length > 0;
                
                if (!isValidData) {
                    this.showNoDataMessage(canvas, 'Data tidak valid');
                    return;
                }
                
                // Destroy existing chart
                if (this.chart) {
                    this.chart.destroy();
                }
                
                // Create new chart
                this.chart = new Chart(canvas, chartData); 
                
            } catch (error) {
                console.error(`❌ Error initializing ${this.chartId}:`, error);
                this.showErrorMessage(canvas, error.message);
            }
        },
        
        showNoDataMessage(canvas, reason = 'Tidak ada data untuk ditampilkan') {
            const parent = canvas.parentElement;
            parent.innerHTML = `
                <div class="flex flex-col items-center justify-center h-full text-gray-500 p-4 text-center">
                    <i class="fas fa-chart-bar text-4xl mb-3 opacity-50"></i>
                    <p class="text-sm font-medium mb-1">${this.getChartName()}</p>
                    <p class="text-xs text-red-500 mb-1">${reason}</p>
                    <p class="text-xs text-gray-400">Filter: {{ $this->getFilterDescription() }}</p>
                </div>
            `;
        },
        
        showErrorMessage(canvas, errorDetail = '') {
            const parent = canvas.parentElement;
            parent.innerHTML = `
                <div class="flex flex-col items-center justify-center h-full text-red-500 p-4 text-center">
                    <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                    <p class="text-sm font-medium">Error: ${this.getChartName()}</p>
                    <p class="text-xs mt-1">${errorDetail}</p>
                    <button @click="initializeChart()" 
                            class="mt-2 px-3 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600">
                        Coba Lagi
                    </button>
                </div>
            `;
        },
        
        getChartName() {
            const chartNames = {
                'statusAduanChart': 'Status Aduan',
                'jenisPelanggaranChart': 'Jenis Pelanggaran',
                'pergerakanTahunanChart': 'Pergerakan Tahunan',
                'saluranAduanChart': 'Saluran Aduan',
                'direktoratChart': 'Direktorat'
            };
            return chartNames[this.chartId] || this.chartId;
        }
    }
}

// Initialize semua charts ketika DOM ready
document.addEventListener('DOMContentLoaded', function() {
 });
 
let resizeTimer;
window.addEventListener('resize', () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(() => { 
    }, 250);
});
</script>