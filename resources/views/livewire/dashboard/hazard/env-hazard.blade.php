<div class="grid grid-cols-1 gap-2 my-2 lg:grid-cols-3">
    <div class="shadow rounded-xl lg:col-span-2 bg-base-100 p-2">
        <div wire:ignore id="hazardEnvJenisChart" style="height: 350px;" class="w-full"></div>
    </div>

    <div class="shadow rounded-xl bg-base-100 p-2">
        <div wire:ignore id="ktaTtaEnvPieChart" style="height: 350px;" class="w-full"></div>
    </div>

    <script type="module">
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

        // --- WARNA KHUSUS ENV (Hijau/Teal) AGAR BEDA DENGAN OHS ---
        const barColors = ['#10B981', '#34D399', '#059669', '#6EE7B7', '#115E59', '#2DD4BF', '#065F46'];

        const barChart = echarts.init(document.getElementById('hazardEnvJenisChart'));
        const pieChart = echarts.init(document.getElementById('ktaTtaEnvPieChart'));

        const getBarOption = (data, currentTheme) => ({
            backgroundColor: 'transparent',
            color: barColors,
            title: {
                text: 'Tren Env Hazard Report per Jenis Bahaya',
                left: 'center',
                textStyle: {
                    color: currentTheme.content,
                    fontFamily: 'Poppins, sans-serif',
                    fontSize: 14
                },
                subtext: data.range,
                subtextStyle: {
                    color: currentTheme.content,
                    opacity: 0.7
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
                }
            },
            legend: {
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
                axisLabel: {
                    color: currentTheme.content,
                    fontSize: 10
                },
                axisLine: {
                    lineStyle: {
                        color: currentTheme.base300
                    }
                },
                splitLine: {
                    show: true,
                    lineStyle: {
                        color: currentTheme.base300,
                        opacity: 0.5
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
            series: data.series.map(s => ({
                ...s,
                type: 'bar',
                barMaxWidth: 20,
                label: {
                    show: true,
                    position: 'top',
                    color: currentTheme.content,
                    fontSize: 10,
                    formatter: (p) => p.value > 0 ? p.value : ''
                }
            }))
        });

        const getPieOption = (data, currentTheme) => ({
            backgroundColor: 'transparent',
            color: ['#059669', '#FBBF24'], // Hijau untuk KTA, Kuning untuk TTA
            title: {
                text: 'Kategori Bahaya Env (KTA vs TTA)',
                left: 'center',
                textStyle: {
                    color: currentTheme.content,
                    fontFamily: 'Poppins, sans-serif',
                    fontSize: 14
                }
            },
            tooltip: {
                trigger: 'item',
                formatter: '{b}: <b>{c}</b> ({d}%)'
            },
            legend: {
                bottom: 0,
                textStyle: {
                    color: currentTheme.content
                }
            },
            series: [{
                type: 'pie',
                radius: ['35%', '60%'],
                avoidLabelOverlap: true,
                itemStyle: {
                    borderRadius: 10,
                    borderColor: currentTheme.base100,
                    borderWidth: 2
                },
                label: {
                    color: currentTheme.content,
                    formatter: '{b}\n{c} ({d}%)'
                },
                data: data.series
            }]
        });

        // --- 5. RENDER AWAL ---
        barChart.setOption(getBarOption(@json(json_decode($chartJenisBahaya, true)), theme));
        pieChart.setOption(getPieOption(@json(json_decode($chartKtaTta, true)), theme));

        // --- 6. LIVEWIRE UPDATE EVENTS (Disesuaikan dengan Dispatch baru) ---
        Livewire.on('updateEnvJenisBahayaChart', event => {
            barChart.setOption(getBarOption(JSON.parse(event), theme), true);
        });

        Livewire.on('updateEnvPieChart', event => {
            pieChart.setOption(getPieOption(JSON.parse(event), theme), true);
        });

        // --- 7. OBSERVER TEMA ---
        const observer = new MutationObserver(() => {
            theme = fetchColors();
            // Gunakan @this untuk mengambil data terbaru dari property Livewire
            barChart.setOption(getBarOption(JSON.parse(@this.chartJenisBahaya), theme));
            pieChart.setOption(getPieOption(JSON.parse(@this.chartKtaTta), theme));
        });
        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['data-theme']
        });

        window.addEventListener('resize', () => {
            barChart.resize();
            pieChart.resize();
        });
    </script>
</div>