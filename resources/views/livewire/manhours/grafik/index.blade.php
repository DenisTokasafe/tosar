<div class="grid grid-cols-1 gap-2 lg:grid-cols-2">
    <div wire:ignore id="grafik-manhours" style="height: 320px"></div>
    {{-- Gunakan grafik-manhours untuk Line Chart Gabungan --}}
    <div wire:ignore id="grafik-manpower" style="height: 320px"></div>
    <script type="module">
        // --- GRAFIK MANHOURS ---
        const initialData = @json(json_decode($data, true));
        const currentYear = @json($years);
        var dom = document.getElementById('grafik-manhours');
        var myChart = echarts.init(dom);

        const getSelectedLegends = (payload) => {
            let selected = {
                'PT. MSM': true,
                'PT. TTN': true,
                'CONTRACTOR': true
            };
            if (payload && payload.hidden_legends) {
                payload.hidden_legends.forEach(name => {
                    selected[name] = false;
                });
            }
            return selected;
        };

        var option = {
            title: {
                text: 'Manhours Bulanan',
                subtext: 'Periode 12 Bulan Terakhir'
            },
            tooltip: {
                trigger: 'axis'
            },
            legend: {
                data: ['PT. MSM', 'PT. TTN', 'CONTRACTOR'],
                bottom: 0,
                selected: getSelectedLegends(initialData)
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '10%',
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
                data: initialData.months,
                axisLabel: {
                    fontSize: 10,
                    rotate: 30
                } // Miringkan label karena ada tahunnya
            },
            yAxis: {
                type: 'value'
            },
            series: [{
                    name: 'PT. MSM',
                    type: 'line',
                    data: initialData.msm,
                    smooth: true
                },
                {
                    name: 'PT. TTN',
                    type: 'line',
                    data: initialData.ttn,
                    smooth: true
                },
                {
                    name: 'CONTRACTOR',
                    type: 'line',
                    data: initialData.contractor,
                    smooth: true
                }
            ]
        };

        myChart.setOption(option);

        Livewire.on('manhoursChart', event => {
            let payload = JSON.parse(event);
            myChart.setOption({
                legend: {
                    selected: getSelectedLegends(payload)
                },
                xAxis: {
                    data: payload.months
                },
                series: [{
                        data: payload.msm
                    },
                    {
                        data: payload.ttn
                    },
                    {
                        data: payload.contractor
                    }
                ]
            });
        });

        window.addEventListener('resize', myChart.resize);
    </script>

    <script type="module">
        // --- GRAFIK MANPOWER ---
        const initialDataMp = @json(json_decode($manpowerData, true));
        var dom_mp = document.getElementById('grafik-manpower');
        var myChart_mp = echarts.init(dom_mp);

        const getSelectedLegendsMp = (payload) => {
            let selected = {
                'PT. MSM': true,
                'PT. TTN': true,
                'CONTRACTOR': true
            };
            if (payload && payload.hidden_legends) {
                payload.hidden_legends.forEach(name => {
                    selected[name] = false;
                });
            }
            return selected;
        };

        var option_mp = {
            title: {
                text: 'Manpower Bulanan',
                subtext: 'Periode 12 Bulan Terakhir'
            },
            tooltip: {
                trigger: 'axis'
            },
            legend: {
                data: ['PT. MSM', 'PT. TTN', 'CONTRACTOR'],
                bottom: 0,
                selected: getSelectedLegendsMp(initialDataMp)
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '10%',
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
                data: initialDataMp.months,
                axisLabel: {
                    fontSize: 10,
                    rotate: 30
                }
            },
            yAxis: {
                type: 'value'
            },
            series: [{
                    name: 'PT. MSM',
                    type: 'line',
                    data: initialDataMp.msm,
                    smooth: true
                },
                {
                    name: 'PT. TTN',
                    type: 'line',
                    data: initialDataMp.ttn,
                    smooth: true
                },
                {
                    name: 'CONTRACTOR',
                    type: 'line',
                    data: initialDataMp.contractor,
                    smooth: true
                }
            ]
        };

        myChart_mp.setOption(option_mp);

        Livewire.on('manpowerChart', event => {
            let payload = JSON.parse(event);
            myChart_mp.setOption({
                legend: {
                    selected: getSelectedLegendsMp(payload)
                },
                xAxis: {
                    data: payload.months
                },
                series: [{
                        data: payload.msm
                    },
                    {
                        data: payload.ttn
                    },
                    {
                        data: payload.contractor
                    }
                ]
            });
        });

        window.addEventListener('resize', myChart_mp.resize);
    </script>
</div>
