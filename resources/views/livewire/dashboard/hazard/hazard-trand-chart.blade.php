<div>
    <div class="overflow-hidden shadow-xl card bg-base-100">
        <div wire:ignore id="hazardTrend" style="height: 320px;" class="w-full"></div>
    </div>
    <!-- Load ECharts dari CDN -->
    <script type="module">
        const data = @json($data);
        var dom = document.getElementById('hazardTrend');
        var myChart = echarts.init(dom);
        var option;

        /**
         * Helper untuk mengambil warna dari CSS Variable daisyUI.
         * Menggunakan trik getComputedStyle agar format OKLCH dikonversi ke RGB oleh browser
         * sehingga ECharts bisa membacanya dengan benar.
         */
        const getThemeColor = (variable) => {
            const temp = document.createElement('div');
            temp.style.color = `var(${variable})`;
            document.body.appendChild(temp);
            const style = getComputedStyle(temp).color;
            document.body.removeChild(temp);
            return style;
        };

        // Mengumpulkan warna-warna tema yang dibutuhkan
        const fetchColors = () => ({
            primary: getThemeColor('--color-success'), // Warna garis
            content: getThemeColor('--color-base-content'), // Warna teks & sumbu
            base100: getThemeColor('--color-base-100'), // Background tooltip
            base300: getThemeColor('--color-base-300'), // Garis bantu (grid)
        });

        let colors = fetchColors();

        option = {
            // Membuat background chart transparan agar menyatu dengan card
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
                },
                axisTick: {
                    show: false
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
                        color: colors.base300
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
                smooth: true, // Membuat garis lebih modern (melengkung)
                lineStyle: {
                    width: 3,
                    color: colors.primary
                },
                symbol: 'circle',
                symbolSize: 6,
                itemStyle: {
                    color: colors.primary
                },
                // Menambahkan gradasi di bawah garis agar terlihat lebih profesional
                areaStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
                            offset: 0,
                            color: colors.primary.replace('rgb', 'rgba').replace(')', ', 0.3)')
                        },
                        {
                            offset: 1,
                            color: colors.primary.replace('rgb', 'rgba').replace(')', ', 0)')
                        }
                    ])
                }
            }]
        };

        if (option && typeof option === 'object') {
            myChart.setOption(option);

            /**
             * Observer untuk mendeteksi perubahan atribut data-theme pada <html>
             * Chart akan otomatis update warna saat tema diganti.
             */
            const observer = new MutationObserver(() => {
                const newColors = fetchColors();
                myChart.setOption({
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
                    tooltip: {
                        backgroundColor: newColors.base100,
                        borderColor: newColors.primary,
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
                        },
                        areaStyle: {
                            color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
                                    offset: 0,
                                    color: newColors.primary.replace('rgb', 'rgba').replace(')', ', 0.3)')
                                },
                                {
                                    offset: 1,
                                    color: newColors.primary.replace('rgb', 'rgba').replace(')', ', 0)')
                                }
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
