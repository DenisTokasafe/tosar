<div>
    <div class="overflow-hidden shadow-xl card bg-base-100">
        <div wire:ignore id="hazardTrend" style="height: 320px;" class="w-full"></div>
    </div>
    <!-- Load ECharts dari CDN -->
    <script type="module">
        // ... kode data dan init ...

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
            base100: getThemeColor('--color-base-100'), // Warna latar belakang dasar
            base300: getThemeColor('--color-base-300'),
        });

        let colors = fetchColors();

        option = {
            // --- TAMBAHKAN INI ---
            backgroundColor: 'transparent', // Mengatur agar chart tidak memiliki background sendiri (transparan)
            // ---------------------
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
                    fontSize: 8,
                    color: colors.content
                }
            },
            // ... grid, tooltip, legend tetap sama ...

            series: [{
                name: 'Jumlah Laporan',
                data: data.counts,
                type: 'line',
                smooth: false,
                lineStyle: {
                    width: 3,
                    color: colors.primary
                },
                symbol: 'circle',
                symbolSize: 6,
                itemStyle: {
                    color: colors.primary
                }
            }]
        };

        if (option && typeof option === 'object') {
            myChart.setOption(option);

            // Update otomatis saat ganti tema
            const observer = new MutationObserver(() => {
                const newColors = fetchColors();
                myChart.setOption({
                    // Opsional: Jika tidak ingin transparan, set ke newColors.base100
                    backgroundColor: 'transparent',
                    title: {
                        textStyle: {
                            color: newColors.content
                        },
                        subtextStyle: {
                            color: newColors.content
                        }
                    },
                    xAxis: {
                        axisLine: {
                            lineStyle: {
                                color: newColors.content
                            }
                        },
                        axisLabel: {
                            color: newColors.content
                        }
                    },
                    yAxis: {
                        axisLine: {
                            lineStyle: {
                                color: newColors.content
                            }
                        },
                        axisLabel: {
                            color: newColors.content
                        },
                        splitLine: {
                            lineStyle: {
                                color: newColors.base300
                            }
                        }
                    },
                    series: [{
                        lineStyle: {
                            color: newColors.primary
                        },
                        itemStyle: {
                            color: newColors.primary
                        }
                    }]
                });
            });

            observer.observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['data-theme']
            });

            // ... Livewire.on dan resize ...
        }
    </script>
</div>
