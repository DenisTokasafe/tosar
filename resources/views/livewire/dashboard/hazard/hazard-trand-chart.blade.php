<div>
    <div wire:ignore id="hazardTrend" style="height: 355px" class="w-full border bg-base-100 border-base-200"></div>

    <script type="module">
        const initTrendChart = () => {
            const dom = document.getElementById('hazardTrend');

            if (!dom) return;

            // 1. Bersihkan instance lama untuk mendukung wire:navigate
            let myChart = echarts.getInstanceByDom(dom);
            if (myChart) {
                myChart.dispose();
            }

            myChart = echarts.init(dom);

            // 2. Ambil data awal dari Livewire
            const data = @json($data);

            // --- UTILS TEMA DAISYUI ---
            const getThemeColor = (variable) => {
                const temp = document.createElement('div');
                temp.style.color = `var(${variable})`;
                document.body.appendChild(temp);
                const style = getComputedStyle(temp).color;
                document.body.removeChild(temp);
                return style;
            };

            const fetchColors = () => ({
                primary: getThemeColor('--color-primary'),
                content: getThemeColor('--color-base-content'),
                base100: getThemeColor('--color-base-100'),
                base300: getThemeColor('--color-base-300'),
            });

            let colors = fetchColors();

            // 3. Konfigurasi Option
            const option = {
                backgroundColor: 'transparent',
                title: {
                    text: 'Jumlah Laporan Hazard per Bulan',
                    left: 'center',
                    top: 5,
                    textStyle: {
                        fontFamily: 'Poppins, sans-serif',
                        fontSize: 14,
                        fontWeight: 'bold',
                        color: colors.content
                    },
                    subtext: 'Data laporan berdasarkan bulan berjalan',
                    subtextStyle: {
                        fontSize: 10,
                        color: colors.content
                    }
                },
                grid: {
                    top: 90, right: 30, bottom: 50, left: 50,
                    containLabel: true
                },
                tooltip: {
                    trigger: 'axis',
                    backgroundColor: colors.base100,
                    borderColor: colors.primary,
                    borderWidth: 1,
                    textStyle: { color: colors.content }
                },
                legend: {
                    data: ['Jumlah Laporan'],
                    top: 55,
                    textStyle: { color: colors.content }
                },
                xAxis: {
                    type: 'category',
                    data: data.months,
                    axisLine: { lineStyle: { color: colors.base300 } },
                    axisLabel: { color: colors.content }
                },
                yAxis: {
                    type: 'value',
                    axisLine: { show: false },
                    splitLine: { lineStyle: { type: 'dashed', color: colors.base300 } },
                    axisLabel: { color: colors.content }
                },
                series: [{
                    name: 'Jumlah Laporan',
                    data: data.counts,
                    type: 'line',
                    smooth: 0.3, // Membuat garis sedikit melengkung (lebih modern)
                    lineStyle: { width: 4, color: colors.primary },
                    symbol: 'circle',
                    symbolSize: 8,
                    itemStyle: { color: colors.primary, borderWidth: 2, borderColor: colors.base100 },
                    emphasis: {
                        focus: 'none',
                        lineStyle: { width: 5 }
                    }
                }]
            };

            myChart.setOption(option);

            // --- OBSERVER PERUBAHAN TEMA ---
            const observer = new MutationObserver(() => {
                const newColors = fetchColors();
                myChart.setOption({
                    title: { textStyle: { color: newColors.content }, subtextStyle: { color: newColors.content } },
                    legend: { textStyle: { color: newColors.content } },
                    tooltip: { backgroundColor: newColors.base100, borderColor: newColors.primary, textStyle: { color: newColors.content } },
                    xAxis: { axisLabel: { color: newColors.content }, axisLine: { lineStyle: { color: newColors.base300 } } },
                    yAxis: { axisLabel: { color: newColors.content }, splitLine: { lineStyle: { color: newColors.base300 } } },
                    series: [{ lineStyle: { color: newColors.primary }, itemStyle: { color: newColors.primary, borderColor: newColors.base100 } }]
                });
            });
            observer.observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });

            // --- LIVEWIRE UPDATE EVENT ---
            Livewire.on('trandChart', (event) => {
                // Livewire v3 mengirim data dalam array [payload]
                const payload = typeof event[0] === 'string' ? JSON.parse(event[0]) : event[0];
                myChart.setOption({
                    xAxis: { data: payload.months },
                    series: [{ data: payload.counts }]
                });
            });

            window.addEventListener('resize', () => myChart.resize());
        };

        // Eksekusi saat navigasi Livewire (SPA)
        document.addEventListener('livewire:navigated', initTrendChart);

        // Eksekusi load awal
        initTrendChart();
    </script>
</div>
