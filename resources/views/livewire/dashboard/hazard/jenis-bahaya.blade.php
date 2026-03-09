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
        let rawData = @json(json_decode($chartJenisBahaya, true));

        var dom = document.getElementById('hazardJenisChart');
        var myChart = echarts.init(dom);

        const getOption = (data, currentTheme) => {
            return {
                backgroundColor: 'transparent',
                // --- PALET WARNA KONTRAST TINGGI ---
                color: [
                    '#5470c6', // Biru
                    '#91cc75', // Hijau
                    '#fac858', // Kuning
                    '#ee6666', // Merah
                    '#73c0de', // Cyan
                    '#3ba272', // Emerald
                    '#fc8452', // Oranye
                    '#9a60b4', // Ungu
                    '#ea7ccc', // Pink
                    '#333333' // Hitam/Abu Gelap
                ],
                title: {
                    text: 'Tren OHS Hazard Report per Jenis Bahaya',
                    left: 'center',
                    textStyle: {
                        color: currentTheme.content,
                        fontFamily: 'Poppins, sans-serif',
                        fontSize: 14
                    },
                    subtext: data.range,
                    subtextStyle: {
                        color: currentTheme.content,
                        opacity: 0.7,
                        fontSize: 11
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
                        // Sortir agar yang terbanyak muncul di atas pada tooltip
                        params.sort((a, b) => b.value - a.value);
                        params.forEach(item => {
                            if (item.value > 0) {
                                res += `<br/>${item.marker} ${item.seriesName}: <b>${item.value}</b>`;
                            }
                        });
                        return res;
                    }
                },
                legend: {
                    data: data.legend,
                    bottom: 0,
                    textStyle: {
                        color: currentTheme.content
                    },
                    type: 'scroll',
                    itemGap: 12
                },
                grid: {
                    top: 70,
                    left: '3%',
                    right: '4%',
                    bottom: '15%',
                    containLabel: true
                },
                xAxis: {
                    type: 'category',
                    data: data.labels,
                    axisTick: {
                        show: false
                    }, // Menghilangkan garis kecil di sumbu X
                    axisLabel: {
                        interval: 0,
                        rotate: 0,
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
                    splitLine: {
                        lineStyle: {
                            color: currentTheme.base300,
                            type: 'dashed',
                            opacity: 0.5
                        }
                    },
                    axisLabel: {
                        color: currentTheme.content
                    }
                },
                // Mapping series dari PHP ke format grouped bar
                series: data.series.map(s => ({
                    name: s.name,
                    data: s.data,
                    type: 'bar',
                    barMaxWidth: 20,
                    barGap: '15%',
                    barCategoryGap: '35%',
                    // --- PENAMBAHAN DATA LABELS ---
                    label: {
                        show: true,
                        position: 'top', // Angka muncul di atas batang
                        distance: 5, // Jarak antara angka dan batang
                        color: currentTheme.content, // Warna teks mengikuti tema
                        fontSize: 10,
                        fontFamily: 'Poppins, sans-serif',
                        formatter: function(params) {
                            return params.value > 0 ? params.value : ''; // Hanya munculkan jika > 0
                        }
                    },
                    itemStyle: {
                        borderRadius: [3, 3, 0, 0]
                    },
                    emphasis: {
                        focus: 'series'
                    }
                }))
            };
        };

        // Render Awal
        myChart.setOption(getOption(rawData, theme));

        // --- OBSERVER TEMA (DARK/LIGHT) ---
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
                    axisLabel: {
                        color: theme.content
                    },
                    splitLine: {
                        lineStyle: {
                            color: theme.base300
                        }
                    }
                }
            });
        });
        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['data-theme']
        });

        // --- LIVEWIRE UPDATE EVENT ---
        Livewire.on('updateJenisBahayaChart', event => {
            let payload = JSON.parse(event);
            // 'true' di sini sangat penting agar ECharts menghapus series lama
            // dan merender ulang dengan kategori yang mungkin berbeda.
            myChart.setOption(getOption(payload, theme), true);
        });

        window.addEventListener('resize', () => {
            myChart.resize();
        });
    </script>
</div>