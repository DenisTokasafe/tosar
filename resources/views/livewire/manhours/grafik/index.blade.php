<div class="grid grid-cols-1 gap-2 mb-5 lg:grid-cols-2">
    <div wire:ignore id="grafik-manhours" style="height: 320px"></div>
    {{-- Gunakan grafik-manhours untuk Line Chart Gabungan --}}
    <div wire:ignore id="grafik-manpower" style="height: 320px"></div>
        <script type="module">
            // CATATAN: Karena Livewire me-render data awal, pastikan data awal juga mengandung 'hidden_legends' jika Anda ingin legend disembunyikan pada load pertama.
            // Jika tidak, legend akan mengikuti data awal default (aktif semua).
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
                    // Konfigurasi legend di load awal
                    data: ['PT. MSM', 'PT. TTN', 'CONTRACTOR'],
                    // Set initial selection based on PHP data structure if it contains 'hidden_legends'
                    selected: (function(initialData) {
                        let selected = {
                            'PT. MSM': true,
                            'PT. TTN': true,
                            'CONTRACTOR': true
                        };
                        if (initialData.hidden_legends) {
                            initialData.hidden_legends.forEach(name => {
                                selected[name] = false;
                            });
                        }
                        return selected;
                    })(@json($data))
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
                        data: data.msm
                    },
                    {
                        name: 'PT. TTN',
                        type: 'line',
                        data: data.ttn
                    },
                    {
                        name: 'CONTRACTOR',
                        type: 'line',
                        data: data.contractor
                    }
                ]
            };

            if (option && typeof option === 'object') {
                myChart.setOption(option);

                // --- MODIFIKASI LISTENER LIVEWIRE UNTUK GRAFIK MANHOURS ---
                Livewire.on('manhoursChart', event => {
                    let payload_trand = JSON.parse(event);

                    let selectedLegends = {
                        'PT. MSM': true,
                        'PT. TTN': true,
                        'CONTRACTOR': true
                    };

                    // Set legend yang tidak ada data (berdasarkan hidden_legends dari Livewire) menjadi false (non-aktif)
                    if (payload_trand.hidden_legends) {
                        payload_trand.hidden_legends.forEach(name => {
                            selectedLegends[name] = false;
                        });
                    }

                    myChart.setOption({
                        legend: {
                            selected: selectedLegends // Terapkan status legend yang dipilih
                        },
                        xAxis: {
                            data: payload_trand.months
                        },
                        series: [{
                                name: 'PT. MSM',
                                type: 'line',
                                data: payload_trand.msm
                            },
                            {
                                name: 'PT. TTN',
                                type: 'line',
                                data: payload_trand.ttn
                            },
                            {
                                name: 'CONTRACTOR',
                                type: 'line',
                                data: payload_trand.contractor
                            }
                        ]
                    });
                });
            }
            window.addEventListener('resize', myChart.resize);
        </script>
        <script type="module">
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
                    // Konfigurasi legend di load awal
                    data: ['PT. MSM', 'PT. TTN', 'CONTRACTOR'],
                    // Set initial selection based on PHP data structure
                    selected: (function(initialData) {
                        let selected = {
                            'PT. MSM': true,
                            'PT. TTN': true,
                            'CONTRACTOR': true
                        };
                        if (initialData.hidden_legends) {
                            initialData.hidden_legends.forEach(name => {
                                selected[name] = false;
                            });
                        }
                        return selected;
                    })(@json($manpowerData))
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
                        data: data_manpower.msm
                    },
                    {
                        name: 'PT. TTN', // Sesuai Legend
                        type: 'line',
                        data: data_manpower.ttn
                    },
                    {
                        name: 'CONTRACTOR', // Sesuai Legend
                        type: 'line',
                        data: data_manpower.contractor
                    }
                ]
            };

            if (option_mp && typeof option_mp === 'object') {
                myChart_mp.setOption(option_mp);

                // --- MODIFIKASI LISTENER LIVEWIRE UNTUK GRAFIK MANPOWER ---
                Livewire.on('manpowerChart', event => {
                    let payload_manpower = JSON.parse(event);

                    let selectedLegends_mp = {
                        'PT. MSM': true,
                        'PT. TTN': true,
                        'CONTRACTOR': true
                    };

                    // Set legend yang tidak ada data (berdasarkan hidden_legends dari Livewire) menjadi false (non-aktif)
                    if (payload_manpower.hidden_legends) {
                        payload_manpower.hidden_legends.forEach(name => {
                            selectedLegends_mp[name] = false;
                        });
                    }

                    myChart_mp.setOption({
                        legend: {
                            selected: selectedLegends_mp // Terapkan status legend yang dipilih
                        },
                        xAxis: {
                            data: payload_manpower.months
                        },
                        series: [{
                                name: 'PT. MSM', // Harus sinkron dengan load awal
                                type: 'line',
                                data: payload_manpower.msm
                            },
                            {
                                name: 'PT. TTN', // Harus sinkron dengan load awal
                                type: 'line',
                                data: payload_manpower.ttn
                            },
                            {
                                name: 'CONTRACTOR', // Harus sinkron dengan load awal
                                type: 'line',
                                data: payload_manpower.contractor
                            }
                        ]
                    });
                });
            }
            window.addEventListener('resize', myChart_mp.resize);
        </script>
</div>
