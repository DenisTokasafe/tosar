<div>
        <div wire:ignore id="hazardTrend" style="height: 320px;" class="w-full"></div>
    <!-- Load ECharts dari CDN -->
    <script type="module">
        const data = @json($data);
        var dom = document.getElementById('hazardTrend');
        var myChart = echarts.init(dom);
        var option;

        option = {
            title: {
                text: 'Jumlah Laporan Hazard per Bulan'
                , left: 'center'
                , top: 5
                , textStyle: {
                    fontFamily: 'Microsoft YaHei'
                    , fontSize: 14
                    , fontWeight: 'bold'
                    , color: '#333'
                }
                , subtext: 'Data laporan berdasarkan bulan berjalan'
                , subtextStyle: {
                    fontFamily: 'Microsoft YaHei'
                    , fontSize: 8
                    , color: '#666'
                }
            }
            , textStyle: {
                fontFamily: 'Microsoft YaHei'
                , fontSize: 12
                , fontStyle: 'normal'
                , fontWeight: 'normal'
            , }
            , grid: {
                top: 90
                , right: 30
                , bottom: 50
                , left: 50
                , containLabel: true
            }
            , tooltip: {
                trigger: 'axis'
                , backgroundColor: 'rgba(50,50,50,0.8)'
                , borderWidth: 0
                , textStyle: {
                    color: '#fff'
                    , fontFamily: 'Microsoft YaHei'
                    , fontSize: 12
                , }
            }
            , legend: {
                data: ['Jumlah Laporan']
                , top: 50
                , left: 'center'
                , textStyle: {
                    fontFamily: 'Microsoft YaHei'
                    , fontSize: 12
                    , fontWeight: 'normal'
                }
            }
            , xAxis: {
                type: 'category'
                , data: data.months
                , axisLine: {
                    lineStyle: {
                        color: '#888'
                    }
                }
                , axisLabel: {
                    fontFamily: 'Microsoft YaHei'
                    , fontSize: 12
                }
                , axisTick: {
                    show: false
                }
            }
            , yAxis: {
                type: 'value'
                , axisLine: {
                    lineStyle: {
                        color: '#888'
                    }
                }
                , splitLine: {
                    lineStyle: {
                        type: 'dashed'
                        , color: '#ddd'
                    }
                }
                , axisLabel: {
                    fontFamily: 'Microsoft YaHei'
                    , fontSize: 12
                }
            }
            , series: [{
                name: 'Jumlah Laporan'
                , data: data.counts
                , type: 'line'
                , smooth: false
                , lineStyle: {
                    width: 3
                }
                , symbol: 'circle'
                , symbolSize: 6
                , itemStyle: {
                    color: '#3B82F6'
                }

            }]
        };

        if (option && typeof option === 'object') {
            myChart.setOption(option);
            Livewire.on('trandChart', event => {
                let payload_trand = JSON.parse(event);
                myChart.setOption({
                    xAxis: {
                        data: payload_trand.months
                    }
                    , series: [{
                        data: payload_trand.counts
                    }]

                });
            });
        }
        window.addEventListener('resize', myChart.resize);

    </script>
</div>
