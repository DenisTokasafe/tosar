<div>
    <div wire:ignore id="hazardTrend" style="height: 320px;" class="w-full"></div>
    <!-- Load ECharts dari CDN -->
    <script type="module">
        const data = @json($data);
        var dom = document.getElementById('hazardTrend');
        var myChart = echarts.init(dom);
        var option;

        // Fungsi untuk mengambil variabel warna OKLCH dari daisyUI
        const getThemeColor = (variable) => {
            return getComputedStyle(document.documentElement).getPropertyValue(variable).trim();
        };

        // Helper untuk merender warna agar dipahami ECharts
        const currentColor = (prop) => `oklch(${getThemeColor(prop)})`;

        option = {
            title: {
                text: 'Jumlah Laporan Hazard per Bulan',
                left: 'center',
                top: 5,
                textStyle: {
                    fontFamily: 'Microsoft YaHei',
                    fontSize: 14,
                    fontWeight: 'bold',
                    color: currentColor('--color-base-content') // Mengikuti warna teks tema
                },
                subtext: 'Data laporan berdasarkan bulan berjalan',
                subtextStyle: {
                    fontFamily: 'Microsoft YaHei',
                    fontSize: 8,
                    color: currentColor('--color-base-content') // Mengikuti warna teks tema
                }
            },
            textStyle: {
                fontFamily: 'Microsoft YaHei',
                fontSize: 12,
                fontStyle: 'normal',
                fontWeight: 'normal',
                color: currentColor('--color-base-content')
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
                backgroundColor: currentColor('--color-base-100'), // Background tooltip sesuai tema
                borderColor: currentColor('--color-primary'),
                borderWidth: 1,
                textStyle: {
                    color: currentColor('--color-base-content'),
                    fontFamily: 'Microsoft YaHei',
                    fontSize: 12,
                }
            },
            legend: {
                data: ['Jumlah Laporan'],
                top: 50,
                left: 'center',
                textStyle: {
                    fontFamily: 'Microsoft YaHei',
                    fontSize: 12,
                    fontWeight: 'normal',
                    color: currentColor('--color-base-content')
                }
            },
            xAxis: {
                type: 'category',
                data: data.months,
                axisLine: {
                    lineStyle: {
                        color: currentColor('--color-base-content') // Garis axis mengikuti tema
                    }
                },
                axisLabel: {
                    fontFamily: 'Microsoft YaHei',
                    fontSize: 12,
                    color: currentColor('--color-base-content')
                },
                axisTick: {
                    show: false
                }
            },
            yAxis: {
                type: 'value',
                axisLine: {
                    lineStyle: {
                        color: currentColor('--color-base-content')
                    }
                },
                splitLine: {
                    lineStyle: {
                        type: 'dashed',
                        color: currentColor('--color-base-300') // Garis grid mengikuti base-300 tema
                    }
                },
                axisLabel: {
                    fontFamily: 'Microsoft YaHei',
                    fontSize: 12,
                    color: currentColor('--color-base-content')
                }
            },
            series: [{
                name: 'Jumlah Laporan',
                data: data.counts,
                type: 'line',
                smooth: false,
                lineStyle: {
                    width: 3,
                    color: currentColor('--color-primary') // Garis utama mengikuti warna Primary
                },
                symbol: 'circle',
                symbolSize: 6,
                itemStyle: {
                    color: currentColor('--color-primary') // Titik mengikuti warna Primary
                }
            }]
        };

        if (option && typeof option === 'object') {
            myChart.setOption(option);

            // Update warna saat tema berubah (MutationObserver)
            const observer = new MutationObserver(() => {
                myChart.setOption({
                    title: {
                        textStyle: {
                            color: currentColor('--color-base-content')
                        },
                        subtextStyle: {
                            color: currentColor('--color-base-content')
                        }
                    },
                    xAxis: {
                        axisLine: {
                            lineStyle: {
                                color: currentColor('--color-base-content')
                            }
                        },
                        axisLabel: {
                            color: currentColor('--color-base-content')
                        }
                    },
                    yAxis: {
                        axisLabel: {
                            color: currentColor('--color-base-content')
                        },
                        splitLine: {
                            lineStyle: {
                                color: currentColor('--color-base-300')
                            }
                        }
                    },
                    series: [{
                        lineStyle: {
                            color: currentColor('--color-primary')
                        },
                        itemStyle: {
                            color: currentColor('--color-primary')
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
