<div>
    <div wire:ignore id="hazardJenisChart" style="height: 320px;" class="w-full"></div>

    <script type="module">
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
        // Parsing data awal dari Livewire
        let rawData = @json(json_decode($chartJenisBahaya, true));

        var dom = document.getElementById('hazardJenisChart');
        var myChart = echarts.init(dom);

        const getOption = (data, currentTheme) => {
            return {
                backgroundColor: 'transparent',
                title: {
                    text: 'Tren OHS Hazard Report per Jenis Bahaya',
                    left: 'center',
                    textStyle: {
                        color: currentTheme.content,
                        fontFamily: 'Poppins, sans-serif',
                        fontSize: 14
                    },
                    subtext: data.range ? 'Periode: ' + data.range : '12 Bulan Terakhir',
                    subtextStyle: {
                        color: currentTheme.content,
                        opacity: 0.7,
                        fontSize: 12
                    }
                },
                tooltip: {
                    trigger: 'axis',
                    backgroundColor: currentTheme.base100,
                    borderColor: currentTheme.primary,
                    borderWidth: 1,
                    textStyle: {
                        color: currentTheme.content
                    },
                    axisPointer: {
                        type: 'shadow'
                    },
                    formatter: function(params) {
                        let res = '<b>' + params[0].name + '</b>';
                        let total = 0;
                        params.forEach(item => {
                            res += `<br/>${item.marker} ${item.seriesName}: ${item.value}`;
                            total += item.value;
                        });
                        res += `<br/><b>Total: ${total}</b>`;
                        return res;
                    }
                },
                legend: {
                    data: data.legend,
                    bottom: 5,
                    textStyle: {
                        color: currentTheme.content
                    },
                    type: 'scroll' // Jika jenis bahaya terlalu banyak
                },
                grid: {
                    top: 80,
                    left: '3%',
                    right: '4%',
                    bottom: '20%',
                    containLabel: true
                },
                dataZoom: [{
                        type: 'inside',
                        start: 0,
                        end: 100
                    },
                    {
                        show: true,
                        type: 'slider',
                        top: 'bottom',
                        height: 20,
                        textStyle: {
                            color: currentTheme.content
                        }
                    }
                ],
                xAxis: {
                    type: 'category',
                    data: data.labels,
                    axisLabel: {
                        interval: 0,
                        rotate: 30,
                        fontSize: 10,
                        color: currentTheme.content
                    },
                    axisLine: {
                        lineStyle: {
                            color: currentTheme.base300
                        }
                    }
                },
                yAxis: {
                    type: 'value',
                    name: 'Jumlah',
                    nameTextStyle: {
                        color: currentTheme.content
                    },
                    axisLabel: {
                        color: currentTheme.content
                    },
                    splitLine: {
                        lineStyle: {
                            color: currentTheme.base300,
                            type: 'dashed'
                        }
                    }
                },
                series: data.series.map(s => ({
                    ...s,
                    barMaxWidth: 40,
                    emphasis: {
                        focus: 'series'
                    }
                }))
            };
        };

        // Render Pertama
        myChart.setOption(getOption(rawData, theme));

        // --- OBSERVER PERUBAHAN TEMA ---
        const observer = new MutationObserver(() => {
            theme = fetchColors();
            myChart.setOption({
                title: {
                    textStyle: {
                        color: theme.content
                    },
                    subtextStyle: {
                        color: theme.content
                    }
                },
                tooltip: {
                    backgroundColor: theme.base100,
                    borderColor: theme.primary,
                    textStyle: {
                        color: theme.content
                    }
                },
                legend: {
                    textStyle: {
                        color: theme.content
                    }
                },
                xAxis: {
                    axisLabel: {
                        color: theme.content
                    },
                    axisLine: {
                        lineStyle: {
                            color: theme.base300
                        }
                    }
                },
                yAxis: {
                    nameTextStyle: {
                        color: theme.content
                    },
                    axisLabel: {
                        color: theme.content
                    },
                    splitLine: {
                        lineStyle: {
                            color: theme.base300
                        }
                    }
                },
                dataZoom: [{}, {
                    textStyle: {
                        color: theme.content
                    }
                }]
            });
        });

        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['data-theme']
        });

        // --- LIVEWIRE EVENT ---
        Livewire.on('updateJenisBahayaChart', event => {
            let payload = JSON.parse(event);
            // Gunakan notasi fungsional agar seluruh series terupdate dengan benar
            myChart.setOption(getOption(payload, theme), true);
        });

        window.addEventListener('resize', () => {
            myChart.resize();
        });
    </script>
</div>