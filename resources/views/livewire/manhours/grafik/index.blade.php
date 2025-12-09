<div class="grid grid-cols-1 lg:grid-cols-2 gap-2">
    <div wire:ignore id="grafik-manhours" style="height: 320px"></div>
    {{-- Gunakan grafik-manhours untuk Line Chart Gabungan --}}
    <div wire:ignore id="grafik-manpower" style="height: 320px"></div>
</div>
@push('scripts')
    <!-- Load ECharts dari CDN -->
    <script type="module">
        // ⚠️ Hapus atau Nonaktifkan ini. Polling 1000ms sangat membebani server dan browser.
        // setInterval(() => Livewire.dispatch('chartManhoursUpdate'), 1000);

        // 1. Ambil Data Awal dan Tahun
        const data_initial = @json($data);
        const currentYear = @json($years);

        // 2. Inisialisasi ECharts di luar event listener
        var dom = document.getElementById('grafik-manhours');
        if (!dom) {
            console.error("Elemen ID 'grafik-manhours' tidak ditemukan.");
            return;
        }
        var myChart = echarts.init(dom);

        // 3. Fungsi untuk Menggambar/Memperbarui Grafik
        function updateChart(payload) {
            if (!payload || !payload.months || payload.months.length === 0) {
                console.warn("Payload data chart Manhours kosong.");
                // Opsi: Tampilkan placeholder "Data Kosong" jika perlu
                // myChart.clear();
                return;
            }

            var option = {
                title: {
                    text: 'Manhours Bulanan Tahun ' + currentYear,
                    left: 'center'
                },
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    // 🔑 HARUS SAMA PERSIS dengan nama 'name' di series
                    data: ['PT. MSM', 'PT. TTN', 'Contractor']
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
                    data: payload.months
                },
                yAxis: {
                    type: 'value'
                },
                series: [{
                        // 🔑 Nama series di sini
                        name: 'PT. MSM',
                        type: 'line',
                        stack: 'Total',
                        data: payload.msm
                    },
                    {
                        // 🔑 Nama series di sini
                        name: 'PT. TTN',
                        type: 'line',
                        stack: 'Total',
                        data: payload.ttn
                    },
                    {
                        // 🔑 Nama series di sini
                        name: 'Contractor',
                        type: 'line',
                        stack: 'Total',
                        data: payload.contractor
                    }
                ]
            };

            // Menggunakan setOption dengan parameter kedua 'true' untuk update penuh
            myChart.setOption(option, true);
        }

        // 4. Load Data Awal (Menggunakan data yang disuntikkan dari Blade)
        try {
            const initialPayload = JSON.parse(JSON.stringify(data_initial)); // Kloning data untuk keamanan
            updateChart(initialPayload);
        } catch (e) {
            console.warn("Gagal inisialisasi data awal Manhours:", e);
        }

        // 5. Livewire Listener untuk Update Dinamis
        Livewire.on('manhoursChart', event => {
            try {
                // Event di Livewire 3 bisa berupa array atau string, kita ambil payloadnya
                const payloadString = (typeof event === 'string') ? event : event[0];
                let payload_trand = JSON.parse(payloadString);
                updateChart(payload_trand);
            } catch (e) {
                console.error("Gagal parse data update Manhours dari Livewire:", e);
            }
        });

        window.addEventListener('resize', myChart.resize);
    </script>

    <script type="module">
        setInterval(() => Livewire.dispatch('chartManpowerUpdate'), 1000);
        const data_manpower = @json($manpowerData);
        const currentYear = @json($years);

        var dom_mp = document.getElementById('grafik-manpower');
        var myChart_mp = echarts.init(dom_mp);
        var option_mp;

        // --- OPSI ECHARTS UNTUK MANPOWER ---
        option_mp = {
            title: {
                text: 'Manpower Bulanan Tahun ' + currentYear,
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
                data: data_manpower.months // Menggunakan data manpower
            },
            yAxis: {
                type: 'value'
            },
            series: [{
                    name: 'PT. MSM', // Sesuai Legend
                    type: 'line',
                    stack: 'Total',
                    data: data_manpower.msm
                },
                {
                    name: 'PT. TTN', // Sesuai Legend
                    type: 'line',
                    stack: 'Total',
                    data: data_manpower.ttn
                },
                {
                    name: 'CONTRACTOR', // Sesuai Legend
                    type: 'line',
                    stack: 'Total',
                    data: data_manpower.contractor
                }
            ]
        };

        if (option_mp && typeof option_mp === 'object') {
            myChart_mp.setOption(option_mp);

            // 🔑 PERUBAHAN KRITIS: Menggunakan event listener 'manpowerChart'
            Livewire.on('manpowerChart', event => {
                let payload_manpower = JSON.parse(event);
                myChart_mp.setOption({
                    xAxis: {
                        data: payload_manpower.months
                    },
                    series: [{
                            name: 'PT. MSM', // Harus sinkron dengan load awal
                            type: 'line',
                            stack: 'Total',
                            data: payload_manpower.msm
                        },
                        {
                            name: 'PT. TTN', // Harus sinkron dengan load awal
                            type: 'line',
                            stack: 'Total',
                            data: payload_manpower.ttn
                        },
                        {
                            name: 'CONTRACTOR', // Harus sinkron dengan load awal
                            type: 'line',
                            stack: 'Total',
                            data: payload_manpower.contractor
                        }
                    ]
                });
            });
        }
        window.addEventListener('resize', myChart_mp.resize);
    </script>
@endpush
