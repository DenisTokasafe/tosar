<div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
    {{-- Container Grafik --}}
    <div class="p-4 bg-white rounded-lg shadow">
        <div wire:ignore id="grafik-manhours" style="height: 350px; width: 100%;"></div>
    </div>

    <div class="p-4 bg-white rounded-lg shadow">
        <div wire:ignore id="grafik-manpower" style="height: 350px; width: 100%;"></div>
    </div>

    <script type="module">
        // --- LOGIKA HELPER UNTUK LEGEND ---
        const getSelectedLegends = (jsonPayload) => {
            let payload = typeof jsonPayload === 'string' ? JSON.parse(jsonPayload) : jsonPayload;
            let selected = { 'PT. MSM': true, 'PT. TTN': true, 'CONTRACTOR': true };
            if (payload && payload.hidden_legends) {
                payload.hidden_legends.forEach(name => { selected[name] = false; });
            }
            return selected;
        };

        // --- GRAFIK MANHOURS ---
        let rawData = @json(json_decode($data, true));
        var chartManhours = echarts.init(document.getElementById('grafik-manhours'));

        var optionManhours = {
            title: { text: 'Tren Manhours (Bulan Berjalan)', left: 'center' },
            tooltip: { trigger: 'axis' },
            legend: { bottom: 0, selected: getSelectedLegends(rawData) },
            grid: { left: '3%', right: '4%', bottom: '15%', containLabel: true },
            toolbox: { feature: { saveAsImage: {} } },
            xAxis: {
                type: 'category',
                data: rawData.months,
                axisLabel: { rotate: 30, fontSize: 10 }
            },
            yAxis: { type: 'value' },
            series: [
                { name: 'PT. MSM', type: 'line', data: rawData.msm, smooth: true },
                { name: 'PT. TTN', type: 'line', data: rawData.ttn, smooth: true },
                { name: 'CONTRACTOR', type: 'line', data: rawData.contractor, smooth: true }
            ]
        };
        chartManhours.setOption(optionManhours);

        // --- GRAFIK MANPOWER ---
        let rawDataMp = @json(json_decode($manpowerData, true));
        var chartManpower = echarts.init(document.getElementById('grafik-manpower'));

        var optionManpower = {
            title: { text: 'Tren Manpower (Bulan Berjalan)', left: 'center' },
            tooltip: { trigger: 'axis' },
            legend: { bottom: 0, selected: getSelectedLegends(rawDataMp) },
            grid: { left: '3%', right: '4%', bottom: '15%', containLabel: true },
            xAxis: {
                type: 'category',
                data: rawDataMp.months,
                axisLabel: { rotate: 30, fontSize: 10 }
            },
            yAxis: { type: 'value' },
            series: [
                { name: 'PT. MSM', type: 'line', data: rawDataMp.msm, smooth: true },
                { name: 'PT. TTN', type: 'line', data: rawDataMp.ttn, smooth: true },
                { name: 'CONTRACTOR', type: 'line', data: rawDataMp.contractor, smooth: true }
            ]
        };
        chartManpower.setOption(optionManpower);

        // --- LISTENERS LIVEWIRE ---
        Livewire.on('manhoursChart', event => {
            let payload = JSON.parse(event);
            chartManhours.setOption({
                legend: { selected: getSelectedLegends(payload) },
                xAxis: { data: payload.months },
                series: [{ data: payload.msm }, { data: payload.ttn }, { data: payload.contractor }]
            });
        });

        Livewire.on('manpowerChart', event => {
            let payload = JSON.parse(event);
            chartManpower.setOption({
                legend: { selected: getSelectedLegends(payload) },
                xAxis: { data: payload.months },
                series: [{ data: payload.msm }, { data: payload.ttn }, { data: payload.contractor }]
            });
        });

        window.addEventListener('resize', () => {
            chartManhours.resize();
            chartManpower.resize();
        });
    </script>
</div>
