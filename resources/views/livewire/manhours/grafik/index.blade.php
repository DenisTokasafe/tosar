<div class="grid grid-cols-1 gap-2 mb-5 lg:grid-cols-2">
    <div wire:ignore id="grafik-manhours" style="height: 320px"></div>
    <div wire:ignore id="grafik-manpower" style="height: 320px"></div>

    <script type="module">
        // --- GRAFIK MANHOURS ---
        const data = @json($data);
        const currentYear = @json($years);
        var dom = document.getElementById('grafik-manhours');
        var myChart = echarts.init(dom);

        var option = {
            title: {
                text: 'Manhours Bulanan Tahun ' + currentYear,
            },
            tooltip: {
                trigger: 'axis',
                // Menambahkan garis bantu vertikal
                axisPointer: {
                    type: 'line',
                    lineStyle: { type: 'dashed' }
                }
            },
            legend: {
                data: ['PT. MSM', 'PT. TTN', 'CONTRACTOR'],
                selected: (function(initialData) {
                    let selected = { 'PT. MSM': true, 'PT. TTN': true, 'CONTRACTOR': true };
                    if (initialData.hidden_legends) {
                        initialData.hidden_legends.forEach(name => { selected[name] = false; });
                    }
                    return selected;
                })(@json($data))
            },
            grid: { left: '3%', right: '4%', bottom: '3%', containLabel: true },
            toolbox: { feature: { saveAsImage: {} } },
            xAxis: {
                type: 'category',
                boundaryGap: false,
                data: data.months
            },
            yAxis: { type: 'value' },
            series: [
                {
                    name: 'PT. MSM',
                    type: 'line',
                    data: data.msm,
                    emphasis: { focus: 'series' } // Menjaga garis tetap solid saat hover
                },
                {
                    name: 'PT. TTN',
                    type: 'line',
                    data: data.ttn,
                    emphasis: { focus: 'series' }
                },
                {
                    name: 'CONTRACTOR',
                    type: 'line',
                    data: data.contractor,
                    emphasis: { focus: 'series' }
                }
            ]
        };

        if (option) {
            myChart.setOption(option);
            Livewire.on('manhoursChart', event => {
                let payload = JSON.parse(event);
                let selectedLegends = { 'PT. MSM': true, 'PT. TTN': true, 'CONTRACTOR': true };
                if (payload.hidden_legends) {
                    payload.hidden_legends.forEach(name => { selectedLegends[name] = false; });
                }
                myChart.setOption({
                    legend: { selected: selectedLegends },
                    xAxis: { data: payload.months },
                    series: [
                        { name: 'PT. MSM', data: payload.msm, emphasis: { focus: 'series' } },
                        { name: 'PT. TTN', data: payload.ttn, emphasis: { focus: 'series' } },
                        { name: 'CONTRACTOR', data: payload.contractor, emphasis: { focus: 'series' } }
                    ]
                });
            });
        }
        window.addEventListener('resize', myChart.resize);
    </script>

    <script type="module">
        // --- GRAFIK MANPOWER ---
        const data_manpower = @json($manpowerData);
        const year_mp = @json($years);
        var dom_mp = document.getElementById('grafik-manpower');
        var myChart_mp = echarts.init(dom_mp);

        var option_mp = {
            title: {
                text: 'Manpower Bulanan Tahun ' + year_mp,
            },
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'line',
                    lineStyle: { type: 'dashed' }
                }
            },
            legend: {
                data: ['PT. MSM', 'PT. TTN', 'CONTRACTOR'],
                selected: (function(initialData) {
                    let selected = { 'PT. MSM': true, 'PT. TTN': true, 'CONTRACTOR': true };
                    if (initialData.hidden_legends) {
                        initialData.hidden_legends.forEach(name => { selected[name] = false; });
                    }
                    return selected;
                })(@json($manpowerData))
            },
            grid: { left: '3%', right: '4%', bottom: '3%', containLabel: true },
            toolbox: { feature: { saveAsImage: {} } },
            xAxis: {
                type: 'category',
                boundaryGap: false,
                data: data_manpower.months
            },
            yAxis: { type: 'value' },
            series: [
                {
                    name: 'PT. MSM',
                    type: 'line',
                    data: data_manpower.msm,
                    emphasis: { focus: 'series' }
                },
                {
                    name: 'PT. TTN',
                    type: 'line',
                    data: data_manpower.ttn,
                    emphasis: { focus: 'series' }
                },
                {
                    name: 'CONTRACTOR',
                    type: 'line',
                    data: data_manpower.contractor,
                    emphasis: { focus: 'series' }
                }
            ]
        };

        if (option_mp) {
            myChart_mp.setOption(option_mp);
            Livewire.on('manpowerChart', event => {
                let payload = JSON.parse(event);
                let selectedLegends_mp = { 'PT. MSM': true, 'PT. TTN': true, 'CONTRACTOR': true };
                if (payload.hidden_legends) {
                    payload.hidden_legends.forEach(name => { selectedLegends_mp[name] = false; });
                }
                myChart_mp.setOption({
                    legend: { selected: selectedLegends_mp },
                    xAxis: { data: payload.months },
                    series: [
                        { name: 'PT. MSM', data: payload.msm, emphasis: { focus: 'series' } },
                        { name: 'PT. TTN', data: payload.ttn, emphasis: { focus: 'series' } },
                        { name: 'CONTRACTOR', data: payload.contractor, emphasis: { focus: 'series' } }
                    ]
                });
            });
        }
        window.addEventListener('resize', myChart_mp.resize);
    </script>
</div>
