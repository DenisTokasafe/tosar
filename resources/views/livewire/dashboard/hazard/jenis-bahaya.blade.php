<div class="grid grid-cols-1 gap-2 my-2 lg:grid-cols-3">
    <div class="shadow rounded-xl lg:col-span-2 bg-base-100 p-2">
        <div wire:ignore id="hazardJenisChart" style="height: 350px;" class="w-full"></div>
    </div>

    <div class="shadow rounded-xl bg-base-100 p-2">
        <div wire:ignore id="ktaTtaPieChart" style="height: 350px;" class="w-full"></div>
    </div>

    <script type="module">
        // --- 1. UTILS TEMA & WARNA ---
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
        const barColors = ['#5470c6', '#91cc75', '#fac858', '#ee6666', '#73c0de', '#3ba272', '#fc8452', '#9a60b4', '#ea7ccc'];

        // --- 2. INISIALISASI CHART ---
        const barChart = echarts.init(document.getElementById('hazardJenisChart'));
        const pieChart = echarts.init(document.getElementById('ktaTtaPieChart'));

        // --- 3. KONFIGURASI BAR CHART ---
        const getBarOption = (data, currentTheme) => ({
            backgroundColor: 'transparent',
            color: barColors,
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
                },
                formatter: function(params) {
                    let res = '<b>' + params[0].name + '</b>';
                    params.sort((a, b) => b.value - a.value);
                    params.forEach(item => {
                        if (item.value > 0) res += `<br/>${item.marker} ${item.seriesName}: <b>${item.value}</b>`;
                    });
                    return res;
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
                name: s.name,
                data: s.data,
                type: 'bar',
                barMaxWidth: 20,
                barGap: '15%',
                label: {
                    show: true,
                    position: 'top',
                    color: currentTheme.content,
                    fontSize: 10,
                    formatter: (p) => p.value > 0 ? p.value : ''
                },
                itemStyle: {
                    borderRadius: [3, 3, 0, 0]
                },
                emphasis: {
                    focus: 'series'
                }
            }))
        });

        // --- 4. KONFIGURASI PIE CHART (DIPERBARUI AGAR TIDAK TINDIH) ---
        const getPieOption = (data, currentTheme) => ({
            backgroundColor: 'transparent',
            color: ['#4F75FE', '#FAC858'],
            title: {
                text: 'Kategori Hazard (KTA vs TTA)',
                left: 'center',
                textStyle: {
                    color: currentTheme.content,
                    fontFamily: 'Poppins, sans-serif',
                    fontSize: 14
                }
            },
            tooltip: {
                trigger: 'item',
                backgroundColor: currentTheme.base100,
                borderColor: currentTheme.primary,
                borderWidth: 1,
                textStyle: {
                    color: currentTheme.content
                },
                formatter: '{b}: <b>{c}</b> ({d}%)'
            },
            legend: {
                bottom: 0,
                textStyle: {
                    color: currentTheme.content
                }
            },
            series: [{
                name: 'Kategori',
                type: 'pie',
                // Radius dikecilkan (dari 70% ke 60%) untuk memberi ruang label
                radius: ['35%', '60%'],
                center: ['50%', '50%'],
                avoidLabelOverlap: true, // Mencegah tumpang tindih
                itemStyle: {
                    borderRadius: 10,
                    borderColor: currentTheme.base100,
                    borderWidth: 2
                },
                label: {
                    show: true,
                    position: 'outer',
                    alignTo: 'none',
                    bleedMargin: 5,
                    color: currentTheme.content,
                    fontSize: 11,
                    // Penambahan margin antar label
                    minMargin: 5,
                    formatter: '{b}\n{c} ({d}%)'
                },
                labelLine: {
                    show: true,
                    length: 15, // Garis pertama
                    length2: 10, // Garis kedua horizontal ke teks
                    smooth: true // Garis melengkung halus
                },
                // Layout otomatis untuk memindahkan label jika terdeteksi tabrakan
                labelLayout: {
                    hideOverlap: false,
                    moveOverlap: 'shiftY'
                },
                emphasis: {
                    label: {
                        show: true,
                        fontSize: 13,
                        fontWeight: 'bold'
                    }
                },
                data: data.series
            }]
        });

        // --- 5. RENDER AWAL ---
        const rawBarData = @json(json_decode($chartJenisBahaya, true));
        const rawPieData = @json(json_decode($chartKtaTta, true));

        barChart.setOption(getBarOption(rawBarData, theme));
        pieChart.setOption(getPieOption(rawPieData, theme));

        // --- 6. LIVEWIRE UPDATE EVENTS ---
        Livewire.on('updateJenisBahayaChart', event => {
            barChart.setOption(getBarOption(JSON.parse(event), theme), true);
        });

        Livewire.on('updatePieChart', event => {
            pieChart.setOption(getPieOption(JSON.parse(event), theme), true);
        });

        // --- 7. OBSERVER TEMA (DARK/LIGHT) ---
        const observer = new MutationObserver(() => {
            theme = fetchColors();
            barChart.setOption(getBarOption(JSON.parse(@json($chartJenisBahaya)), theme));
            pieChart.setOption(getPieOption(JSON.parse(@json($chartKtaTta)), theme));
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