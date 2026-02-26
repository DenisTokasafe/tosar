<div>
    <div class="border bg-base-100 border-base-200" wire:ignore id="container_reportby" style="height: 355px"></div>

    <script type="module">
        /**
         * Membungkus seluruh logika chart dalam satu fungsi utama
         */
        const initHazardUserChart = () => {
            var dom_reportBy = document.getElementById('container_reportby');
            if (!dom_reportBy) return;

            // --- PEMBERSIHAN INSTANCE LAMA (PENTING UNTUK wire:navigate) ---
            let existingChart = echarts.getInstanceByDom(dom_reportBy);
            if (existingChart) {
                existingChart.dispose();
            }

            const pelaporData = @json($pelapor);
            const pelapor = typeof pelaporData === 'string' ? JSON.parse(pelaporData) : pelaporData;

            var myChart_reportBy = echarts.init(dom_reportBy, null, {
                renderer: 'canvas',
                useDirtyRect: false
            });

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

            let theme = fetchColors();

            function generateColor(index, total) {
                const seed = Math.sin(index + 1) * 10000;
                const hue = (seed - Math.floor(seed)) * 360;
                return `hsl(${hue}, 70%, 55%)`;
            }

            var option_reportBy = {
                backgroundColor: 'transparent',
                title: {
                    text: 'Top Kontributor',
                    textStyle: {
                        color: theme.content,
                        fontFamily: 'Poppins, sans-serif'
                    }
                },
                grid: { top: 50, left: 110, right: 30, bottom: 60, containLabel: true },
                tooltip: {
                    trigger: 'axis',
                    backgroundColor: theme.base100,
                    borderColor: theme.primary,
                    borderWidth: 1,
                    textStyle: { color: theme.content },
                    axisPointer: { type: 'shadow' }
                },
                legend: { textStyle: { color: theme.content } },
                xAxis: {
                    type: 'value',
                    boundaryGap: [0, 0.01],
                    axisLabel: { color: theme.content },
                    splitLine: { lineStyle: { color: theme.base300, type: 'dashed' } }
                },
                yAxis: {
                    type: 'category',
                    data: pelapor.label,
                    inverse: true,
                    axisLabel: {
                        color: theme.content,
                        fontSize: 9,
                        fontWeight: 'bold',
                        fontFamily: 'Poppins, sans-serif',
                        overflow: 'truncate',
                        width: 100,
                        align: 'right'
                    },
                    axisLine: { lineStyle: { color: theme.base300 } }
                },
                series: [{
                    name: pelapor.year,
                    type: 'bar',
                    data: pelapor.counts,
                    emphasis: {
                        focus: 'none',
                        itemStyle: { shadowBlur: 10, shadowOffsetX: 0, shadowColor: 'rgba(0,0,0,0.5)' }
                    },
                    itemStyle: {
                        color: params => generateColor(params.dataIndex, pelapor.counts.length),
                        borderRadius: [0, 6, 6, 0]
                    }
                }]
            };

            myChart_reportBy.setOption(option_reportBy);

            // --- OBSERVER UNTUK PERUBAHAN TEMA ---
            const observer = new MutationObserver(() => {
                const newTheme = fetchColors();
                myChart_reportBy.setOption({
                    title: { textStyle: { color: newTheme.content } },
                    tooltip: {
                        backgroundColor: newTheme.base100,
                        borderColor: newTheme.primary,
                        textStyle: { color: newTheme.content }
                    },
                    legend: { textStyle: { color: newTheme.content } },
                    xAxis: {
                        axisLabel: { color: newTheme.content },
                        splitLine: { lineStyle: { color: newTheme.base300 } }
                    },
                    yAxis: {
                        axisLabel: { color: newTheme.content },
                        axisLine: { lineStyle: { color: newTheme.base300 } }
                    }
                });
            });

            observer.observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['data-theme']
            });

            // --- LIVEWIRE EVENT (v3 menggunakan array payload) ---
            Livewire.on('distribusiPelapor', event => {
                const rawPayload = Array.isArray(event) ? event[0] : event;
                const payload = typeof rawPayload === 'string' ? JSON.parse(rawPayload) : rawPayload;

                myChart_reportBy.setOption({
                    title: { text: 'Top Kontributor ' + payload.year },
                    yAxis: { data: payload.label, inverse: true },
                    series: [{
                        name: payload.year,
                        data: payload.counts,
                        itemStyle: {
                            color: params => generateColor(params.dataIndex, payload.counts.length)
                        }
                    }]
                });
            });

            window.addEventListener('resize', () => myChart_reportBy.resize());
        };

        // 1. Jalankan saat load pertama kali
        initHazardUserChart();

        // 2. Jalankan ulang setiap kali navigasi Livewire (wire:navigate) selesai
        document.addEventListener('livewire:navigated', () => {
            initHazardUserChart();
        });
    </script>
</div>
