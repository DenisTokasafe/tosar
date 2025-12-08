<div wire:ignore id="manhoursCombinedChart" style="height: 320px;" class="w-full"></div>

@push('scripts')
    <script type="module">
        // Pastikan ECharts diinisialisasi hanya sekali
        var dom = document.getElementById('manhoursCombinedChart');

        // Mengubah ID dari hazardTrend ke manhoursCombinedChart untuk kejelasan
        // Jika Anda harus tetap menggunakan 'hazardTrend', ganti ID di atas.

        if (!dom) {
            console.error("Elemen ID 'manhoursCombinedChart' tidak ditemukan di DOM.");
            return;
        }
        var myChart = echarts.init(dom);

        // Fungsi untuk Menggambar/Memperbarui Grafik
        function updateChart(data) {
            if (!data || !data.months) {
                console.error("Data Manhours tidak valid atau kosong.");
                return;
            }

            var option = {
                title: {
                    text: 'Manhours Bulanan: PT. MSM, PT. TTN & All Contractor',
                    left: 'center',
                    top: 5,
                    textStyle: {
                        fontSize: 14,
                        fontWeight: 'bold'
                    },
                    subtext: 'Total Manhours per bulan (Filter: ' + ({{ $start_date }} ? '{{ $start_date }}' :
                        'Awal Tahun') + ' - ' + ({{ $end_date }} ? '{{ $end_date }}' : 'Akhir Tahun') + ')',
                    subtextStyle: {
                        fontSize: 8
                    }
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
                    // Formatter untuk menampilkan total jam kerja
                    formatter: function(params) {
                        let tooltip = params[0].name + '<br/>';
                        params.forEach(function(item) {
                            tooltip += item.marker + item.seriesName + ': ' + item.value.toLocaleString() +
                                ' Jam<br/>';
                        });
                        return tooltip;
                    }
                },
                legend: {
                    data: ['PT. MSM', 'PT. TTN', 'All Contractor'],
                    top: 50,
                    left: 'center',
                },
                xAxis: {
                    type: 'category',
                    data: data.months,
                    axisTick: {
                        show: false
                    }
                },
                yAxis: {
                    type: 'value',
                    name: 'Total Manhours (Jam)',
                    splitLine: {
                        lineStyle: {
                            type: 'dashed',
                            color: '#ddd'
                        }
                    },
                },
                series: [{
                        name: 'PT. MSM',
                        type: 'line',
                        data: data.msm_manhours,
                        smooth: true // Garis halus
                    },
                    {
                        name: 'PT. TTN',
                        type: 'line',
                        data: data.ttn_manhours,
                        smooth: true
                    },
                    {
                        name: 'All Contractor',
                        type: 'line',
                        data: data.all_contractor_manhours,
                        smooth: true,
                        lineStyle: {
                            width: 4,
                            type: 'dashed'
                        },
                        itemStyle: {
                            color: '#000' // Warna hitam untuk Total
                        }
                    }
                ]
            };

            myChart.setOption(option, true);
        }

        // 3. Listener Livewire untuk Update Dinamis
        document.addEventListener('livewire:initialized', () => {
            @this.on('updateCombinedChart', (dataJson) => {
                try {
                    const data = JSON.parse(dataJson);
                    updateChart(data);
                } catch (e) {
                    console.error("Gagal parse JSON dari Livewire:", e);
                }
            });
        });

        // 4. Load Data Awal (Menggunakan data dari render awal Blade)
        try {
            // Mengambil string JSON awal dari Livewire dan mem-parse
            const initialDataString = @json($combinedChartData);
            if (initialDataString) {
                // Cek jika data sudah dalam bentuk string JSON valid
                const initialData = JSON.parse(initialDataString);
                updateChart(initialData);
            } else {
                // Jika $combinedChartData kosong, coba gunakan nilai default yang diinisialisasi
                // Ini penting untuk menghindari error saat load pertama jika data kosong
                updateChart({
                    "months": ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                    "msm_manhours": [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                    "ttn_manhours": [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                    "all_contractor_manhours": [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
                });
            }
        } catch (e) {
            console.warn("Gagal inisialisasi data awal (Pastikan properti Livewire diinisialisasi):", e);
        }

        // 5. Responsive Resize
        window.addEventListener('resize', myChart.resize);
    </script>
@endpush
