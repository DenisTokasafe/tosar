<div class="grid grid-cols-1 lg:grid-cols-2 gap-2">
    <div wire:ignore id="grafik-manhours" style="height: 320px"></div>
    {{-- Gunakan grafik-manhours untuk Line Chart Gabungan --}}
    <div wire:ignore id="grafik-manpower" style="height: 320px"></div>
</div>
@push('scripts')
    <!-- Load ECharts dari CDN -->
    <script type="module">
        setInterval(() => Livewire.dispatch('chartManhoursUpdate'), 1000);
        const data = @json($data);
        const currentYear = @json($years);
        var dom = document.getElementById('grafik-manhours');
        var myChart = echarts.init(dom);
        var option;

        option = {
            title: {
                text: 'Manhours Bulanan Tahun ' + currentYear,
            },
            tooltip: {
                trigger: 'axis'
            },
            legend: {
                data: ['PT. MSM', 'PT. TTN', 'CONTRACTOR']
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
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
                data: data.months
            },
            yAxis: {
                type: 'value'
            },
            series: [{
                    name: 'PT. MSM',
                    type: 'line',
                    stack: 'Total',
                    data: data.msm
                },
                {
                    name: 'PT. TTN',
                    type: 'line',
                    stack: 'Total',
                    data: data.ttn
                },
                {
                    name: 'CONTRACTOR',
                    type: 'line',
                    stack: 'Total',
                    data: data.contractor
                }
            ]
        };

        if (option && typeof option === 'object') {
            myChart.setOption(option);
            Livewire.on('manhoursChart', event => {
                let payload_trand = JSON.parse(event);
                myChart.setOption({
                    xAxis: {
                        data: payload_trand.months
                    },
                    series: [{
                            name: 'PT. MSM',
                            type: 'line',
                            stack: 'Total',
                            data: payload_trand.msm
                        },
                        {
                            name: 'PT. TTN',
                            type: 'line',
                            stack: 'Total',
                            data: payload_trand.ttn
                        },
                        {
                            name: 'CONTRACTOR',
                            type: 'line',
                            stack: 'Total',
                            data: payload_trand.contractor
                        }
                    ]

                });
            });
        }
        window.addEventListener('resize', myChart.resize);
    </script>
    <script type="module">
        // ⚠️ Hati-hati dengan interval polling 1000ms. Hapus jika tidak diperlukan.
        // setInterval(() => Livewire.dispatch('chartManpowerUpdate'), 1000);

        // 1. Ambil Data Awal dan Tahun
        const data_manpower = @json($manpowerData);
        const currentYear = @json($years);

        // 2. Inisialisasi ECharts dengan Tema 'dark'
        // Jika tema 'dark' tidak dimuat/didefinisikan di halaman Anda, ECharts akan mengabaikannya.
        var dom_mp = document.getElementById('grafik-manpower');
        if (!dom_mp) {
            console.error("Elemen ID 'grafik-manpower' tidak ditemukan.");
            return;
        }

        // 🔑 Inisialisasi dengan tema 'dark'
        var myChart_mp = echarts.init(dom_mp, 'dark');


        // 3. Definisi Warna dan Gaya untuk Dark Mode Manual (Fallback jika tema 'dark' tidak ada)
        const darkTextColor = '#ccc'; // Warna teks terang untuk latar gelap
        const darkAxisLineColor = '#666'; // Warna garis sumbu abu-abu gelap

        // 4. Fungsi untuk Menggambar/Memperbarui Grafik
        function updateChart(payload) {
            if (!payload || !payload.months || payload.months.length === 0) {
                // Opsional: Hapus chart jika data kosong
                myChart_mp.clear();
                return;
            }

            var option_mp = {
                // Gaya Dark Mode diterapkan di sini jika tema 'dark' tidak bekerja
                backgroundColor: 'transparent', // Biarkan transparan agar latar belakang CSS/Tailwind terlihat
                textStyle: {
                    color: darkTextColor // Teks di seluruh chart (default)
                },

                title: {
                    text: 'Manpower Bulanan Tahun ' + currentYear,
                    left: 'center',
                    textStyle: {
                        color: darkTextColor
                    } // Warna Judul
                },
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data: ['PT. MSM', 'PT. TTN', 'CONTRACTOR'],
                    textStyle: {
                        color: darkTextColor
                    } // Warna Legend
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
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
                    data: payload.months,
                    axisLine: {
                        lineStyle: {
                            color: darkAxisLineColor
                        } // Garis sumbu X
                    },
                    axisLabel: {
                        color: darkTextColor // Label sumbu X
                    }
                },
                yAxis: {
                    type: 'value',
                    axisLine: {
                        lineStyle: {
                            color: darkAxisLineColor
                        } // Garis sumbu Y
                    },
                    axisLabel: {
                        color: darkTextColor // Label sumbu Y
                    },
                    splitLine: {
                        lineStyle: {
                            color: darkAxisLineColor + '40'
                        } // Garis horizontal grid
                    }
                },
                series: [{
                        name: 'PT. MSM',
                        type: 'line',
                        stack: 'Total',
                        data: payload.msm
                    },
                    {
                        name: 'PT. TTN',
                        type: 'line',
                        stack: 'Total',
                        data: payload.ttn
                    },
                    {
                        name: 'CONTRACTOR',
                        type: 'line',
                        stack: 'Total',
                        data: payload.contractor
                    }
                ]
            };

            myChart_mp.setOption(option_mp, true);
        }

        // 5. Load Data Awal
        if (data_manpower && data_manpower.months) {
            updateChart(data_manpower);
        }


        // 6. Livewire Listener untuk Update Dinamis
        Livewire.on('manpowerChart', event => {
            try {
                const payloadString = (typeof event === 'string') ? event : event[0];
                let payload_manpower = JSON.parse(payloadString);
                updateChart(payload_manpower);
            } catch (e) {
                console.error("Gagal parse data update Manpower dari Livewire:", e);
            }
        });

        window.addEventListener('resize', myChart_mp.resize);
    </script>
@endpush
