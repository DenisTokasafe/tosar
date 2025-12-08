<div>
    {{-- Pastikan ID kontainer ini sesuai dengan yang Anda gunakan di script --}}
    <div wire:ignore id="manhoursCombinedChart" style="height: 320px;" class="w-full"></div>

    @push('scripts')
        <script type="module">
            // ⚠️ PENTING: Hapus setInterval() ini. Polling seharusnya diatur di Livewire Component jika diperlukan.
            // setInterval(() => Livewire.dispatch('chartManhoursUpdated'), 1000);

            // --- INI ADALAH GRAFIK MANHOURS GABUNGAN ---

            // 1. Inisialisasi ECharts di luar event listener
            var dom = document.getElementById('manhoursCombinedChart');
            // Cek untuk menghindari error 'Cannot read properties of null'
            if (!dom) return;

            var myChart = echarts.init(dom);

            // 2. Event Listener Livewire untuk menerima data baru
            document.addEventListener('livewire:initialized', () => {

                // Ganti 'updateCombinedChart' dengan nama event dispatch yang Anda gunakan di komponen Livewire
                @this.on('updateCombinedChart', (dataJson) => {

                    try {
                        const data = JSON.parse(dataJson);

                        var option = {
                            title: {
                                // ⚠️ Ganti judul dari 'Hazard' menjadi 'Manhours'
                                text: 'Manhours Bulanan: PT. MSM, PT. TTN & All Contractor',
                                left: 'center',
                                top: 5,
                                textStyle: { fontSize: 14, fontWeight: 'bold' },
                                subtext: 'Total Manhours per bulan',
                                subtextStyle: { fontSize: 8 }
                            },
                            grid: {
                                top: 90,
                                right: 30,
                                bottom: 50,
                                left: 50,
                                containLabel: true
                            },
                            tooltip: {
                                trigger: 'axis',
                                // Menampilkan nilai total jika menggunakan stacking
                            },
                            legend: {
                                data: ['PT. MSM', 'PT. TTN', 'All Contractor'], // ⚠️ Perbarui Legend
                                top: 50,
                                left: 'center',
                            },
                            xAxis: {
                                type: 'category',
                                data: data.months, // Data Bulan dari Livewire
                                axisTick: { show: false }
                            },
                            yAxis: {
                                type: 'value',
                                splitLine: { lineStyle: { type: 'dashed', color: '#ddd' } },
                            },
                            series: [
                                {
                                    name: 'PT. MSM',
                                    type: 'line',
                                    // Hapus 'stack: Total' jika Anda ingin 3 garis terpisah di sumbu Y yang sama
                                    data: data.msm_manhours
                                },
                                {
                                    name: 'PT. TTN',
                                    type: 'line',
                                    // Hapus 'stack: Total'
                                    data: data.ttn_manhours
                                },
                                {
                                    name: 'All Contractor',
                                    type: 'line',
                                    // Garis ini mewakili total keseluruhan contractor
                                    data: data.all_contractor_manhours,
                                    lineStyle: {
                                        width: 4,
                                        type: 'dashed'
                                    }
                                }
                            ]
                        };

                        // 3. Set Option ke Chart
                        myChart.setOption(option);

                    } catch (e) {
                        console.error("Gagal memperbarui data ECharts:", e);
                    }
                });
            });

            // 4. Resize Chart
            window.addEventListener('resize', () => {
                if (myChart) myChart.resize();
            });
        </script>
    @endpush
</div>
