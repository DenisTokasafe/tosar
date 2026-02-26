<div class="grid grid-cols-1 gap-2 mb-5 lg:grid-cols-2">
    <div wire:ignore id="grafik-manhours" style="height: 320px"></div>
    <div wire:ignore id="grafik-manpower" style="height: 320px"></div>

    <script type="module">
        const initSafetyCharts = () => {
            // --- AMBIL DATA DARI PHP ---
            const dataMH = @json($data);
            const dataMP = @json($manpowerData);
            const currentYear = @json($years);

            // --- FUNGSI HELPER UNTUK INSTANCE ---
            const setupChart = (elementId) => {
                const dom = document.getElementById(elementId);
                if (!dom) return null;

                // Dispose jika instance sudah ada (penting untuk wire:navigate)
                let existingChart = echarts.getInstanceByDom(dom);
                if (existingChart) { existingChart.dispose(); }

                return echarts.init(dom);
            };

            const myChartMH = setupChart('grafik-manhours');
            const myChartMP = setupChart('grafik-manpower');

            // --- CONFIG GENERATOR ---
            const getOption = (title, data, year) => ({
                title: { text: `${title} Bulanan Tahun ${year}` },
                tooltip: {
                    trigger: 'axis',
                    axisPointer: { type: 'line', lineStyle: { type: 'dashed' } }
                },
                legend: {
                    data: ['PT. MSM', 'PT. TTN', 'CONTRACTOR'],
                    selected: (function() {
                        let selected = { 'PT. MSM': true, 'PT. TTN': true, 'CONTRACTOR': true };
                        if (data.hidden_legends) {
                            data.hidden_legends.forEach(name => { selected[name] = false; });
                        }
                        return selected;
                    })()
                },
                grid: { left: '3%', right: '4%', bottom: '3%', containLabel: true },
                toolbox: { feature: { saveAsImage: {} } },
                xAxis: { type: 'category', boundaryGap: false, data: data.months },
                yAxis: { type: 'value' },
                series: [
                    { name: 'PT. MSM', type: 'line', data: data.msm, emphasis: { focus: 'series' } },
                    { name: 'PT. TTN', type: 'line', data: data.ttn, emphasis: { focus: 'series' } },
                    { name: 'CONTRACTOR', type: 'line', data: data.contractor, emphasis: { focus: 'series' } }
                ]
            });

            // --- SET INITIAL OPTIONS ---
            if (myChartMH) myChartMH.setOption(getOption('Manhours', dataMH, currentYear));
            if (myChartMP) myChartMP.setOption(getOption('Manpower', dataMP, currentYear));

            // --- LIVEWIRE LISTENERS ---
            Livewire.on('manhoursChart', event => {
                if (!myChartMH) return;
                const payload = typeof event[0] === 'string' ? JSON.parse(event[0]) : event[0];
                let selected = { 'PT. MSM': true, 'PT. TTN': true, 'CONTRACTOR': true };
                if (payload.hidden_legends) {
                    payload.hidden_legends.forEach(name => { selected[name] = false; });
                }
                myChartMH.setOption({
                    legend: { selected: selected },
                    xAxis: { data: payload.months },
                    series: [
                        { name: 'PT. MSM', data: payload.msm },
                        { name: 'PT. TTN', data: payload.ttn },
                        { name: 'CONTRACTOR', data: payload.contractor }
                    ]
                });
            });

            Livewire.on('manpowerChart', event => {
                if (!myChartMP) return;
                const payload = typeof event[0] === 'string' ? JSON.parse(event[0]) : event[0];
                let selected = { 'PT. MSM': true, 'PT. TTN': true, 'CONTRACTOR': true };
                if (payload.hidden_legends) {
                    payload.hidden_legends.forEach(name => { selected[name] = false; });
                }
                myChartMP.setOption({
                    legend: { selected: selected },
                    xAxis: { data: payload.months },
                    series: [
                        { name: 'PT. MSM', data: payload.msm },
                        { name: 'PT. TTN', data: payload.ttn },
                        { name: 'CONTRACTOR', data: payload.contractor }
                    ]
                });
            });

            // --- GLOBAL RESIZE ---
            const handleResize = () => {
                myChartMH?.resize();
                myChartMP?.resize();
            };
            window.addEventListener('resize', handleResize);
        };

        // Inisialisasi awal
        initSafetyCharts();

        // Re-inisialisasi saat navigasi wire:navigate
        document.addEventListener('livewire:navigated', () => {
            initSafetyCharts();
        });
    </script>
</div>
