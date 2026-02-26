<div>
    <div wire:ignore id="hazardTrend" style="height: 320px;" class="w-full"></div>
    <!-- Load ECharts dari CDN -->
    <script type="module">
        const data = @json($data);
        var dom = document.getElementById('hazardTrend');
        var myChart = echarts.init(dom);
        var option;

        /**
         * Fungsi untuk mengambil warna dari variabel CSS daisyUI
         * dan mengonversinya ke format RGB agar didukung oleh ECharts Canvas
         */
        const getThemeColor = (variable) => {
            const temp = document.createElement('div');
            temp.style.color = `var(${variable})`;
            document.body.appendChild(temp);
            const style = getComputedStyle(temp).color; // Browser mengonversi OKLCH ke RGB di sini
            document.body.removeChild(temp);
            return style;
        };

        // Fungsi helper untuk mendapatkan semua warna yang dibutuhkan
        const fetchColors = () => ({
            primary: getThemeColor('--color-primary'),
            content: getThemeColor('--color-base-content'),
            base100: getThemeColor('--color-base-100'),
            base300: getThemeColor('--color-base-300'),
        });

        let colors = fetchColors();

        option = {
            backgroundColor: 'transparent',
            title: {
                text: 'Jumlah Laporan Hazard per Bulan',
                left: 'center',
                top: 5,
                textStyle: {
                    fontFamily: 'Microsoft YaHei',
                    fontSize: 14,
                    fontWeight: 'bold',
                    color: colors.content // Warna teks judul
                },
                subtext: 'Data laporan berdasarkan bulan berjalan',
                subtextStyle: {
                    fontFamily: 'Microsoft YaHei',
                    fontSize: 8,
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
                    lineStyle: {
                        color: colors.content
                    }
                },
                axisLabel: {
                    color: colors.content,
                    fontFamily: 'Microsoft YaHei',
                    fontSize: 12
                }
            },
            yAxis: {
                type: 'value',
                axisLine: {
                    lineStyle: {
                        color: colors.content
                    }
                },
                splitLine: {
                    lineStyle: {
                        type: 'dashed',
                        color: colors.base300 // Garis bantu grid
                    }
                },
                axisLabel: {
                    color: colors.content,
                    fontFamily: 'Microsoft YaHei',
                    fontSize: 12
                }
            },
            series: [{
                name: 'Jumlah Laporan',
                data: data.counts,
                type: 'line',
                smooth: false,
                lineStyle: {
                    width: 3,
                    color: colors.primary // Warna garis utama
                },
                symbol: 'circle',
                symbolSize: 6,
                itemStyle: {
                    color: colors.primary // Warna titik data
                }
            }]
        };

        if (option && typeof option === 'object') {
            myChart.setOption(option);

            // Update warna otomatis saat tema daisyUI diganti
            const observer = new MutationObserver(() => {
                const newColors = fetchColors();
                myChart.setOption({
                    backgroundColor: 'transparent',
                    title: {
                        textStyle: {
                            color: newColors.content
                        },
                        subtextStyle: {
                            color: newColors.content
                        }
                    },
                    legend: {
                        textStyle: {
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

            Livewire.on('trandChart', event => {
                let payload_trand = JSON.parse(event);
                myChart.setOption({
                    xAxis: {
                        data: payload_trand.months
                    },
                    series: [{
                        data: payload_trand.counts
                    }]
                });
            });
        }

        window.addEventListener('resize', myChart.resize);
    </script>
</div>
