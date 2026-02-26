<div>
    <div wire:ignore id="container_reportby" style="height: 355px" class="w-full border bg-base-100 border-base-200"></div>
    <script type="module">
        var dom_reportBy = document.getElementById('container_reportby');
        const pelapor = @json($pelapor);
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

        // 🎨 Fungsi Warna HSL Acak tapi Tetap Konsisten
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
                    fontFamily: 'Poppins, sans-serif',
                    fontSize: 16
                }
            },
            grid: {
                top: 50,
                left: 110,
                right: 30,
                bottom: 60,
                containLabel: true
            },
            tooltip: {
                trigger: 'axis',
                backgroundColor: theme.base100,
                borderColor: theme.primary,
                borderWidth: 1,
                textStyle: { color: theme.content },
                axisPointer: { type: 'shadow' }
            },
            legend: {
                textStyle: { color: theme.content }
            },
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
                    color: theme.content, // Warna teks dinamis
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
                // --- FIX: Mencegah elemen lain blur saat hover ---
                emphasis: {
                    focus: 'none',
                    itemStyle: {
                        shadowBlur: 10,
                        shadowColor: 'rgba(0,0,0,0.3)'
                    }
                },
                itemStyle: {
                    color: params => generateColor(params.dataIndex, pelapor.counts.length),
                    borderRadius: [0, 6, 6, 0]
                }
            }]
        };

        if (option_reportBy && typeof option_reportBy === 'object') {
            myChart_reportBy.setOption(option_reportBy);

            // --- OBSERVER PERUBAHAN TEMA ---
            const observer = new MutationObserver(() => {
                const newTheme = fetchColors();
                myChart_reportBy.setOption({
                    title: { textStyle: { color: newTheme.content } },
                    legend: { textStyle: { color: newTheme.content } },
                    tooltip: {
                        backgroundColor: newTheme.base100,
                        borderColor: newTheme.primary,
                        textStyle: { color: newTheme.content }
                    },
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

            // --- LIVEWIRE EVENT ---
            Livewire.on('distribusiPelapor', event => {
                const payload = JSON.parse(event);
                myChart_reportBy.setOption({
                    title: { text: 'Top Kontributor ' + payload.year },
                    yAxis: { data: payload.label },
                    series: [{
                        name: payload.year,
                        data: payload.counts,
                        itemStyle: {
                            color: params => generateColor(params.dataIndex, payload.counts.length)
                        }
                    }]
                });
            });
        }

        window.addEventListener('resize', myChart_reportBy.resize);
    </script>
</div>
