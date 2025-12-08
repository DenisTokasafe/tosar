<div class="grid grid-cols-1 lg:grid-cols-2 gap-2">
        {{-- Grafik Manpower (Anda perlu menginisialisasi datanya jika ini digunakan) --}}
        <div wire:ignore id="grafik-manpower" style="height: 320px"></div>

        {{-- Grafik Manhours --}}
        <div wire:ignore id="grafik-manhours" style="height: 320px"></div>
    </div>

    @push('scripts')
        <script type="module">
            // Pastikan ECharts dimuat sebelum skrip ini

            // --- INISIALISASI GRAFIK MANHOURS ---
            var manhoursChartDom = document.getElementById('grafik-manhours');
            var manhoursChart = echarts.init(manhoursChartDom);
            var manhoursOption;

            // 📝 Listener Livewire untuk memperbarui grafik Manhours
            document.addEventListener('livewire:initialized', () => {
                @this.on('updateManhoursChart', (dataJson) => {
                    try {
                        const data = JSON.parse(dataJson);

                        manhoursOption = {
                            title: {
                                text: 'Manhours Bulanan (PT. MSM & PT. TTN)',
                                left: 'center'
                            },
                            tooltip: {
                                trigger: 'axis'
                            },
                            legend: {
                                data: ['PT. MSM', 'PT. TTN', 'Total Contractor'],
                                bottom: '0%'
                            },
                            grid: {
                                left: '3%',
                                right: '4%',
                                bottom: '15%', // Ruang untuk legend
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
                                    stack: 'Total', // Membuat Stacked Line Chart
                                    data: data.msm_manhours // Data Manhours PT. MSM
                                },
                                {
                                    name: 'PT. TTN',
                                    type: 'line',
                                    stack: 'Total', // Membuat Stacked Line Chart
                                    data: data.ttn_manhours // Data Manhours PT. TTN
                                },
                                {
                                    name: 'Total Contractor',
                                    type: 'line',
                                    stack: 'Total', // Membuat Stacked Line Chart
                                    data: data.contractor_manhours // Data Gabungan
                                }
                            ]
                        };

                        manhoursChart.setOption(manhoursOption);

                    } catch (e) {
                        console.error("Gagal memproses data Manhours chart:", e);
                    }
                });
            });

            // --- INI ADALAH SKRIP LAMA UNTUK GRAFIK MANPOWER (GRAFIK KIRI) ---
            // Anda HARUS MENGUBAH ini agar menggunakan data sebenarnya dari Livewire
            var manpowerChartDom = document.getElementById('grafik-manpower');
            var manpowerChart = echarts.init(manpowerChartDom);
            var manpowerOption;

            // Gunakan data statis dari contoh Anda untuk demonstrasi (HARUS DIGANTI)
            manpowerOption = {
                title: { text: 'Stacked Line (Manpower - Data Statis)' },
                tooltip: { trigger: 'axis' },
                legend: { data: ['Email', 'Union Ads', 'Video Ads', 'Direct', 'Search Engine'] },
                grid: { left: '3%', right: '4%', bottom: '15%', containLabel: true },
                toolbox: { feature: { saveAsImage: {} } },
                xAxis: { type: 'category', boundaryGap: false, data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] },
                yAxis: { type: 'value' },
                series: [{ name: 'Email', type: 'line', stack: 'Total', data: [120, 132, 101, 134, 90, 230, 210] },
                         { name: 'Union Ads', type: 'line', stack: 'Total', data: [220, 182, 191, 234, 290, 330, 310] },
                         // Tambahkan series lain jika ada
                ]
            };

            manpowerOption && manpowerChart.setOption(manpowerOption);
            // ------------------------------------------------------------------

            // 🛠️ Pastikan grafik responsif
            window.addEventListener('resize', function () {
                if (manhoursChart) {
                    manhoursChart.resize();
                }
                if (manpowerChart) {
                    manpowerChart.resize();
                }
            });
        </script>
    @endpush
