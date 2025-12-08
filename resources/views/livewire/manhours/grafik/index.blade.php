    <div wire:ignore id="hazardTrend" style="height: 320px;" class="w-full"></div>
    @push('scripts')
        <!-- Load ECharts dari CDN -->
        <script type="module">
            setInterval(() => Livewire.dispatch('chartManhoursUpdate'), 1000);
            const data = @json($data);
            var dom = document.getElementById('hazardTrend');
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
    @endpush
