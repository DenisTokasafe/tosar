<div>

    <div class="bg-base-100" wire:ignore id="container" style="height: 310px"></div>

    <script type="module">
        var dom_divis = document.getElementById('container');
        const categories = @json($categories);
        var myChart_divis = echarts.init(dom_divis, null, {
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

        // 🎨 Fungsi Warna HSL Tetap Cerah
        function generateColor(index, total) {
            const hue = (index * (360 / total)) % 360;
            return `hsl(${hue}, 65%, 55%)`;
        }

        var option_divis;

        option_divis = {
            backgroundColor: 'transparent', // Agar menyatu dengan card
            title: {
                text: 'Jumlah Laporan',
                textStyle: {
                    color: theme.content,
                    fontFamily: 'Poppins, sans-serif'
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
                textStyle: {
                    color: theme.content
                },
                axisPointer: {
                    type: 'shadow'
                }
            },
            legend: {
                textStyle: {
                    color: theme.content
                }
            },
            xAxis: {
                type: 'value',
                boundaryGap: [0, 0.01],
                axisLabel: {
                    color: theme.content
                },
                splitLine: {
                    lineStyle: {
                        color: theme.base300,
                        type: 'dashed'
                    }
                }
            },
            yAxis: {
                type: 'category',
                data: categories.label,
                inverse: true,
                axisLabel: {
                    color: theme.content, // Dinamis mengikuti tema
                    fontSize: 9,
                    fontWeight: 'bold',
                    fontFamily: 'Poppins, sans-serif',
                    overflow: 'truncate',
                    width: 100,
                    align: 'right'
                },
                axisLine: {
                    lineStyle: {
                        color: theme.base300
                    }
                }
            },
            series: [{
                name: categories.year,
                type: 'bar',
                data: categories.counts,
                // --- FIX: Agar bar tidak hilang saat kursor masuk ---
                emphasis: {
                    focus: 'none', // Mencegah bar lain menjadi blur berlebihan
                    itemStyle: {
                        shadowBlur: 10,
                        shadowOffsetX: 0,
                        shadowColor: 'rgba(0,0,0,0.5)'
                    }
                },
                itemStyle: {
                    color: function(params) {
                        return generateColor(params.dataIndex, categories.counts.length);
                    },
                    borderRadius: [0, 6, 6, 0]
                }
            }]
        };

        if (option_divis && typeof option_divis === 'object') {
            myChart_divis.setOption(option_divis);

            // --- OBSERVER UNTUK PERUBAHAN TEMA ---
            const observer = new MutationObserver(() => {
                const newTheme = fetchColors();
                myChart_divis.setOption({
                    title: {
                        textStyle: {
                            color: newTheme.content
                        }
                    },
                    tooltip: {
                        backgroundColor: newTheme.base100,
                        borderColor: newTheme.primary,
                        textStyle: {
                            color: newTheme.content
                        }
                    },
                    legend: {
                        textStyle: {
                            color: newTheme.content
                        }
                    },
                    xAxis: {
                        axisLabel: {
                            color: newTheme.content
                        },
                        splitLine: {
                            lineStyle: {
                                color: newTheme.base300
                            }
                        }
                    },
                    yAxis: {
                        axisLabel: {
                            color: newTheme.content
                        },
                        axisLine: {
                            lineStyle: {
                                color: newTheme.base300
                            }
                        }
                    }
                });
            });

            observer.observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['data-theme']
            });

            // --- LIVEWIRE EVENT ---
            Livewire.on('distribusiDivisi', event => {
                const payload_divisi = JSON.parse(event);
                myChart_divis.setOption({
                    title: {
                        text: 'Jumlah Laporan ' + payload_divisi.year
                    },
                    yAxis: {
                        data: payload_divisi.label
                    },
                    series: [{
                        name: payload_divisi.year,
                        data: payload_divisi.counts,
                        itemStyle: {
                            color: function(params) {
                                return generateColor(params.dataIndex, payload_divisi.counts.length);
                            }
                        }
                    }]
                });
            });
        }

        window.addEventListener('resize', myChart_divis.resize);
    </script>
</div>
