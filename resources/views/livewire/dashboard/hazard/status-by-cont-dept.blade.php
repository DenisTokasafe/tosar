<div>
    <div wire:ignore id="hazardStatusByContDept" style="height: 320px;" class="w-full"></div>
    @push('scripts')
        <!-- Load ECharts dari CDN -->
        <script type="module">
             setInterval(() => Livewire.dispatch('hazardStatusByCont_Dept'), 1000);
            const rawData = @json(json_decode($status, true));
            var dom = document.getElementById('hazardStatusByContDept');
            var myChart = echarts.init(dom);

            var option = {
                title: {
                    text: 'Status Laporan per Departemen/Kontraktor',
                    left: 'center',
                    subtext: 'Tahun Berjalan'
                },
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'shadow'
                    }
                },
                legend: {
                    data: ['Open', 'Closed'],
                    top: 50
                },
                grid: {
                    top: 100,
                    containLabel: true
                },
                xAxis: {
                    type: 'category',
                    data: rawData.labels || [],
                    axisLabel: {
                        interval: 0,
                        rotate: 30
                    }
                },
                yAxis: {
                    type: 'value'
                },
                series: [{
                        name: 'Open',
                        type: 'bar',
                        stack: 'total', // Nama stack harus sama agar bertumpuk
                        itemStyle: {
                            color: '#F87171', // Merah muda/Orange untuk Open
                            borderRadius: [0, 0, 0, 0] // [TopLeft, TopRight, BottomRight, BottomLeft]
                        },
                        data: rawData.open || []
                    },
                    {
                        name: 'Closed',
                        type: 'bar',
                        stack: 'total',
                        itemStyle: {
                            color: '#34D399', // Hijau untuk Closed
                            borderRadius: [5, 5, 0, 0] // Memberi efek melengkung hanya di bagian atas bar tertinggi
                        },
                        data: rawData.closed || []
                    }
                ]
            };

            myChart.setOption(option);

            Livewire.on('hazardStatus_DeptOrCont', event => {
                let payload = JSON.parse(event);
                myChart.setOption({
                    xAxis: {
                        data: payload.labels
                    },
                    series: [{
                            data: payload.open
                        },
                        {
                            data: payload.closed
                        }
                    ]
                });
            });

            window.addEventListener('resize', myChart.resize);
        </script>
    @endpush
</div>
