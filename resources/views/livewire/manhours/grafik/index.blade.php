<div class="grid grid-cols-1 lg:grid-cols-2 gap-2">
        <div wire:ignore id="grafik-manhours" style="height: 320px">
            Grafik Manpower Placeholder
        </div>

        {{-- Gunakan grafik-manhours untuk Line Chart Gabungan --}}
        <div wire:ignore id="grafik-manpower" style="height: 320px"></div>
    </div>
    @push('scripts')
        <!-- Load ECharts dari CDN -->
        <script type="module">
            setInterval(() => Livewire.dispatch('chartManhoursUpdate'), 1000);
            const data = @json($data);
            var dom = document.getElementById('grafik-manhours');
            var myChart = echarts.init(dom);
            var option;

            option = {
                title: {
                    text: 'Stacked Line'
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
                        name: 'Email',
                        type: 'line',
                        stack: 'Total',
                        data: data.msm
                    },
                    {
                        name: 'Email',
                        type: 'line',
                        stack: 'Total',
                        data: data.ttn
                    },
                    {
                        name: 'Union Ads',
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
                        name: 'MSM',
                        type: 'line',
                        stack: 'Total',
                        data: payload_trand.msm
                    },
                    {
                        name: 'TTN',
                        type: 'line',
                        stack: 'Total',
                        data: payload_trand.ttn
                    },
                    {
                        name: 'Contractor',
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
            setInterval(() => Livewire.dispatch('chartManhoursUpdate'), 1000);
            const dataManpower = @json($dataManpower);
            var domManpower = document.getElementById('grafik-manpower');
            var myChartManpower = echarts.init(domManpower);
            var optionManpower;

            optionManpower = {
                title: {
                    text: 'Stacked Line'
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
                    data: dataManpower.monthsManpower
                },
                yAxis: {
                    type: 'value'
                },
                series: [{
                        name: 'Email',
                        type: 'line',
                        stack: 'Total',
                        data: dataManpower.msmManpower
                    },
                    {
                        name: 'Email',
                        type: 'line',
                        stack: 'Total',
                        data: dataManpower.ttnManpower
                    },
                    {
                        name: 'Union Ads',
                        type: 'line',
                        stack: 'Total',
                        data: dataManpower.contractorManpower
                    }
                ]
            };

            if (optionManpower && typeof optionManpower === 'object') {
                myChartManpower.setOption(optionManpower);
                Livewire.on('manhoursChart', event => {
                    let payload_trandManpower = JSON.parse(event);
                    myChartManpower.setOption({
                        xAxis: {
                            data: payload_trandManpower.monthsManpower
                        },
                         series: [{
                        name: 'MSM',
                        type: 'line',
                        stack: 'Total',
                        data: payload_trandManpower.msmManpower
                    },
                    {
                        name: 'TTN',
                        type: 'line',
                        stack: 'Total',
                        data: payload_trandManpower.ttnManpower
                    },
                    {
                        name: 'Contractor',
                        type: 'line',
                        stack: 'Total',
                        data: payload_trandManpower.contractorManpower
                    }
                ]

                    });
                });
            }
            window.addEventListener('resize', myChartManpower.resize);
        </script>
    @endpush
