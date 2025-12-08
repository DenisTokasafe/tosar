<div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-2">
        <div wire:ignore id="grafik-contractor" style="height: 320px"></div>
        <div wire:ignore id="grafik-msm-ttn" style="height: 320px"></div>
    </div>

    @push('scripts')
        <script type="module">
            // --- INI ADALAH GRAFIK 1: TOTAL MANHOURS CONTRACTOR ---
            var contractorChartDom = document.getElementById('grafik-contractor');
            var contractorChart = echarts.init(contractorChartDom);
            var contractorOption;

            document.addEventListener('livewire:initialized', () => {
                @this.on('updateContractorChart', (dataJson) => {
                    try {
                        const data = JSON.parse(dataJson);

                        contractorOption = {
                            title: { text: 'Total Manhours ALL Contractor', left: 'center' },
                            tooltip: { trigger: 'axis' },
                            legend: { data: ['Total Manhours'], bottom: '0%' },
                            grid: { left: '3%', right: '4%', bottom: '15%', containLabel: true },
                            xAxis: { type: 'category', data: data.months },
                            yAxis: { type: 'value' },
                            series: [{
                                name: 'Total Manhours',
                                type: 'bar',
                                data: data.manhours
                            }]
                        };

                        contractorChart.setOption(contractorOption);
                    } catch (e) { console.error("Gagal memproses data Contractor chart:", e); }
                });
            });

            // --- INI ADALAH GRAFIK 2: MANHOURS PT. MSM & PT. TTN ---
            var msmTtnChartDom = document.getElementById('grafik-msm-ttn');
            var msmTtnChart = echarts.init(msmTtnChartDom);
            var msmTtnOption;

            document.addEventListener('livewire:initialized', () => {
                @this.on('updateMSMTTNChart', (dataJson) => {
                    try {
                        const data = JSON.parse(dataJson);

                        msmTtnOption = {
                            title: { text: 'Manhours PT. MSM vs PT. TTN', left: 'center' },
                            tooltip: { trigger: 'axis' },
                            legend: { data: ['PT. MSM', 'PT. TTN'], bottom: '0%' },
                            grid: { left: '3%', right: '4%', bottom: '15%', containLabel: true },
                            xAxis: { type: 'category', boundaryGap: false, data: data.months },
                            yAxis: { type: 'value' },
                            series: [
                                {
                                    name: 'PT. MSM',
                                    type: 'line',
                                    stack: 'Total',
                                    data: data.ptmsm
                                },
                                {
                                    name: 'PT. TTN',
                                    type: 'line',
                                    stack: 'Total',
                                    data: data.ptttn
                                }
                            ]
                        };

                        msmTtnChart.setOption(msmTtnOption);
                    } catch (e) { console.error("Gagal memproses data MSM/TTN chart:", e); }
                });
            });

            // Resize Observer (Penting untuk Livewire dan ECharts)
            window.addEventListener('resize', function () {
                if (contractorChart) contractorChart.resize();
                if (msmTtnChart) msmTtnChart.resize();
            });
        </script>
    @endpush
</div>
