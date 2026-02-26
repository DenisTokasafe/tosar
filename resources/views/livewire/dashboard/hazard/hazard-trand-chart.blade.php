<div>
    <div class="overflow-hidden shadow-xl card bg-base-100">
        <div wire:ignore id="hazardTrend" style="height: 320px;" class="w-full"></div>
    </div>

    <script type="module">
        const data = @json($data);
        var dom = document.getElementById('hazardTrend');
        var myChart = echarts.init(dom);
        var option;

        /**
         * Mengonversi variabel CSS OKLCH daisyUI ke RGB agar terbaca oleh Canvas
         */
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

        option = {
            // backgroundColor: 'transparent' adalah kunci agar mengikuti background card
            backgroundColor: 'transparent',
            title: {
                text: 'Jumlah Laporan Hazard per Bulan',
                left: 'center',
                top: 5,
                textStyle: {
                    fontFamily: 'Microsoft YaHei',
                    fontSize: 14,
                    fontWeight: 'bold',
                    color: colors.content
                },
                subtext: 'Data laporan berdasarkan bulan berjalan',
                subtextStyle: {
                    fontFamily: 'Microsoft YaHei',
                    fontSize: 10,
                    color: colors.content
                }
            },
            textStyle: {
                fontFamily: 'Microsoft YaHei',
                fontSize: 12,
                color: colors.content
            },
            grid: {
                top: 90,
                right: 30,
                bottom: 50,
                left: 50,
                containLabel: true
            },
            tooltip: {
                trigger: 'axis',
                backgroundColor: colors.base100,
                borderColor: colors.primary,
                borderWidth: 1,
                textStyle: {
                    color: colors.content,
                    fontFamily: 'Microsoft YaHei',
                    fontSize: 12,
                }
            },
            legend: {
                data: ['Jumlah Laporan'],
                top: 50,
                left: 'center',
                textStyle: {
                    color: colors.content
                }
            },
            xAxis: {
                type: 'category',
                data: data.months,
                axisLine: {
                    lineStyle: { color: colors.content }
                },
                axisLabel: {
                    color: colors.content,
                    fontFamily: 'Microsoft YaHei',
                    fontSize: 11
                },
                axisTick: { show: false }
            },
            yAxis: {
                type: 'value',
                axisLine: {
                    lineStyle: { color: colors.content }
                },
                splitLine: {
                    lineStyle: {
                        type: 'dashed',
                        color: colors.base300
                    }
                },
                axisLabel: {
                    color: colors.content,
                    fontFamily: 'Microsoft YaHei',
                    fontSize: 11
                }
            },
            series: [{
                name: 'Jumlah Laporan',
                data: data.counts,
                type: 'line',
                smooth: true, // Membuat garis lebih melengkung (modern)
                lineStyle: {
                    width: 4,
                    color: colors.primary
                },
                symbol: 'circle',
                symbolSize: 8,
                itemStyle: {
                    color: colors.primary,
                    borderWidth: 2,
                    borderColor: colors.base100 // Efek dot putih di tengah jika perlu
                },
                // Efek area di bawah garis
                areaStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: colors.primary.replace('rgb', 'rgba').replace(')', ', 0.3)') },
                        { offset: 1, color: colors.primary.replace('rgb', 'rgba').replace(')', ', 0)') }
                    ])
                }
            }]
        };

        if (option && typeof option === 'object') {
            myChart.setOption(option);

            const observer = new MutationObserver(() => {
                const newColors = fetchColors();
                myChart.setOption({
                    backgroundColor: 'transparent',
                    title: {
                        textStyle: { color: newColors.content },
                        subtextStyle: { color: newColors.content }
                    },
                    legend: { textStyle: { color: newColors.content } },
                    tooltip: {
                        backgroundColor: newColors.base100,
                        borderColor: newColors.primary,
                        textStyle: { color: newColors.content }
                    },
                    xAxis: {
                        axisLine: { lineStyle: { color: newColors.content } },
                        axisLabel: { color: newColors.content }
                    },
                    yAxis: {
                        axisLine: { lineStyle: { color: newColors.content } },
                        axisLabel: { color: newColors.content },
                        splitLine: { lineStyle: { color: newColors.base300 } }
                    },
                    series: [{
                        lineStyle: { color: newColors.primary },
                        itemStyle: {
                            color: newColors.primary,
                            borderColor: newColors.base100
                        },
                        areaStyle: {
                            color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                                { offset: 0, color: newColors.primary.replace('rgb', 'rgba').replace(')', ', 0.3)') },
                                { offset: 1, color: newColors.primary.replace('rgb', 'rgba').replace(')', ', 0)') }
                            ])
                        }
                    }]
                });
            });

            observer.observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['data-theme']
            });

            Livewire.on('trandChart', event => {
                let payload_trand = JSON.parse(event);
                myChart.setOption({
                    xAxis: { data: payload_trand.months },
                    series: [{ data: payload_trand.counts }]
                });
            });
        }

        window.addEventListener('resize', myChart.resize);
    </script>
</div>
