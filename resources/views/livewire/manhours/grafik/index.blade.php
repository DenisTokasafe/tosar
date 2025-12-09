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
