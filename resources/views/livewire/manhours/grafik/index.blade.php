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
                    data: ['PT. MSM', 'PT. TTN', 'Video Ads']
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
                    data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
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
                        name: 'Union Ads',
                        type: 'line',
                        stack: 'Total',
                        data: data.ttn
                    },
                    {
                        name: 'Video Ads',
                        type: 'line',
                        stack: 'Total',
                        data: [150, 232, 201, 154, 190, 330, 410]
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
                            data: payload_trand.counts
                        }]

                    });
                });
            }
            window.addEventListener('resize', myChart.resize);
        </script>
    @endpush
