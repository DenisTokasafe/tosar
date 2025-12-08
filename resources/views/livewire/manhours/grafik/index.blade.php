<div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-2">
        <div wire:ignore id="grafik-manpower" style="height: 320px">
            Grafik Manpower Placeholder
        </div>

        {{-- Gunakan grafik-manhours untuk Line Chart Gabungan --}}
        <div wire:ignore id="grafik-manhours" style="height: 320px"></div>
    </div>

    @push('scripts')
        <script type="module">
            // --- INISIALISASI GRAFIK MANHOURS GABUNGAN (LINE CHART) ---
            var combinedChartDom = document.getElementById('grafik-manhours');
            var combinedChart = echarts.init(combinedChartDom);
            var combinedOption;

            document.addEventListener('livewire:initialized', () => {
                @this.on('updateCombinedChart', (dataJson) => {
                    try {
                        const data = JSON.parse(dataJson);

                        combinedOption = {
                            title: {
                                text: 'Manhours Bulanan: PT. MSM vs PT. TTN vs All Contractor',
                                left: 'center',
                                textStyle: { fontSize: 14 }
                            },
                            tooltip: {
                                trigger: 'axis',
                                axisPointer: { type: 'shadow' }
                            },
                            legend: {
                                data: ['PT. MSM', 'PT. TTN', 'All Contractor'],
                                bottom: '0%'
                            },
                            grid: {
                                left: '3%',
                                right: '4%',
                                bottom: '15%',
                                containLabel: true
                            },
                            toolbox: {
                                feature: {
                                    saveAsImage: {}
                                }
                            },
                            xAxis: {
                                type: 'category',
                                boundaryGap: false,
                                data: data.months // Data Bulan
                            },
                            yAxis: {
                                type: 'value',
                                name: 'Manhours',
                                nameLocation: 'middle',
                                nameGap: 30
                            },
                            series: [
                                {
                                    name: 'PT. MSM',
                                    type: 'line',
                                    stack: 'Total', // Optional: Stacked line
                                    data: data.msm_manhours
                                },
                                {
                                    name: 'PT. TTN',
                                    type: 'line',
                                    stack: 'Total', // Optional: Stacked line
                                    data: data.ttn_manhours
                                },
                                {
                                    name: 'All Contractor',
                                    type: 'line',
                                    // Tidak di-stack, agar garis ini menunjukkan total sesungguhnya
                                    // Stack: 'Total',
                                    data: data.all_contractor_manhours,
                                    lineStyle: {
                                        width: 4, // Garis lebih tebal untuk total
                                        type: 'dashed' // Garis putus-putus untuk total
                                    }
                                }
                            ]
                        };

                        combinedChart.setOption(combinedOption);

                    } catch (e) {
                        console.error("Gagal memproses data Combined chart:", e);
                    }
                });
            });

            // --- JANGAN LUPA CODE UNTUK grafik-manpower (jika ada) ---

            // 🛠️ Pastikan grafik responsif
            window.addEventListener('resize', function () {
                if (combinedChart) {
                    combinedChart.resize();
                }
                // Resize chart manpower jika ada
            });
        </script>
    @endpush
</div>
