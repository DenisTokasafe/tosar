<div>
    <!-- Container Grafik -->
    <div id="combinedChart" style="height: 450px;"></div>

    <script>
        document.addEventListener("livewire:init", () => {
            // Inisialisasi Grafik
            var chartDom = document.getElementById('combinedChart');
            var combinedChart = echarts.init(chartDom);

            function renderChart(chartData) {
                let data = JSON.parse(chartData);

                let option = {
                    tooltip: {
                        trigger: 'axis'
                    },
                    legend: {
                        data: ['PT. MSM', 'PT. TTN', 'All Contractor'],
                    },
                    toolbox: {
                        feature: {
                            saveAsImage: {}
                        }
                    },
                    grid: {
                        left: '3%',
                        right: '4%',
                        bottom: '3%',
                        containLabel: true
                    },
                    xAxis: {
                        type: 'category',
                        boundaryGap: false,
                        data: data.months
                    },
                    yAxis: {
                        type: 'value'
                    },
                    series: [
                        {
                            name: 'PT. MSM',
                            type: 'line',
                            stack: 'total',
                            smooth: true,
                            areaStyle: {},
                            emphasis: { focus: 'series' },
                            data: data.msm_manhours
                        },
                        {
                            name: 'PT. TTN',
                            type: 'line',
                            stack: 'total',
                            smooth: true,
                            areaStyle: {},
                            emphasis: { focus: 'series' },
                            data: data.ttn_manhours
                        },
                        {
                            name: 'All Contractor',
                            type: 'line',
                            stack: 'total',
                            smooth: true,
                            areaStyle: {},
                            emphasis: { focus: 'series' },
                            data: data.all_contractor_manhours
                        }
                    ]
                };

                combinedChart.setOption(option);
            }

            // Event pertama kali load
            $wire.on('updateCombinedChart', (chartData) => {
                renderChart(chartData);
            });
        });
    </script>
</div>
