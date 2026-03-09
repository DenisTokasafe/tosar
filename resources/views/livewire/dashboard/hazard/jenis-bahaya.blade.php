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
                // Definisi Palet Warna sesuai Gambar Referensi (Biru, Hijau, Slate)
                color: ['#4F75FE', '#B6DB35', '#4B4E6D', '#70A1FF', '#E9F0C4', '#FF9F43', '#00CFE8'],
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
                        params.forEach(item => {
                            res += `<br/>${item.marker} ${item.seriesName}: <b>${item.value}</b>`;
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
                    type: 'scroll'
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
                    },
                    axisLabel: {
                        interval: 0,
                        rotate: 0, // Dibuat horizontal agar rapi seperti gambar
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
                            type: 'solid',
                            opacity: 0.4
                        }
                    },
                    axisLabel: {
                        color: currentTheme.content
                    }
                },
                series: data.series.map(s => ({
                    name: s.name,
                    data: s.data,
                    type: 'bar',
                    // Menghapus 'stack' agar menjadi Grouped Bar
                    barMaxWidth: 25,
                    barGap: '15%', // Spasi antar batang dalam satu kategori
                    itemStyle: {
                        borderRadius: [3, 3, 0, 0] // Membuat ujung atas sedikit rounded
                    },
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

        // --- LIVEWIRE EVENT ---
        Livewire.on('updateJenisBahayaChart', event => {
            let payload = JSON.parse(event);
            // Gunakan true agar series lama (stacking) dibersihkan sepenuhnya
            myChart.setOption(getOption(payload, theme), true);
        });

        window.addEventListener('resize', () => {
            myChart.resize();
        });
    </script>
</div>