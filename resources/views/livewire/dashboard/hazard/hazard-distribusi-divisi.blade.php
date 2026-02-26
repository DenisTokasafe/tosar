<div>
    <div class="border bg-base-100 border-base-200" wire:ignore id="container-distribusi" style="height: 355px; width: 100%;"></div>

    <script type="module">
        // Bungkus dalam fungsi agar bisa dipanggil berulang saat navigasi
        const initHazardChart = () => {
            const dom_divis = document.getElementById('container-distribusi');

            // Cek apakah elemen ada di DOM (mencegah error di halaman lain)
            if (!dom_divis) return;

            // 1. Bersihkan instance lama jika sudah ada (Penting untuk Livewire v3)
            let myChart_divis = echarts.getInstanceByDom(dom_divis);
            if (myChart_divis) {
                myChart_divis.dispose();
            }

            myChart_divis = echarts.init(dom_divis, null, {
                renderer: 'canvas',
                useDirtyRect: false
            });

            // 2. Ambil data awal dari PHP
            // Catatan: Karena $categories di-encode di PHP, kita decode sekali di JS
            const categories = @json(json_decode($categories));

            // --- UTILS TEMA ---
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

            function generateColor(index, total) {
                const hue = (index * (360 / (total || 1))) % 360;
                return `hsl(${hue}, 65%, 55%)`;
            }

            // 3. Konfigurasi Option
            const option_divis = {
                backgroundColor: 'transparent',
                title: {
                    text: 'Jumlah Laporan ' + (categories.year || ''),
                    textStyle: {
                        color: theme.content,
                        fontFamily: 'Poppins, sans-serif'
                    }
                },
                grid: {
                    top: 50, left: 110, right: 30, bottom: 60,
                    containLabel: true
                },
                tooltip: {
                    trigger: 'axis',
                    backgroundColor: theme.base100,
                    borderColor: theme.primary,
                    borderWidth: 1,
                    textStyle: { color: theme.content },
                    axisPointer: { type: 'shadow' }
                },
                xAxis: {
                    type: 'value',
                    axisLabel: { color: theme.content },
                    splitLine: { lineStyle: { color: theme.base300, type: 'dashed' } }
                },
                yAxis: {
                    type: 'category',
                    data: categories.label,
                    inverse: true,
                    axisLabel: {
                        color: theme.content,
                        fontSize: 9,
                        fontWeight: 'bold',
                        width: 100,
                        overflow: 'truncate'
                    }
                },
                series: [{
                    name: categories.year,
                    type: 'bar',
                    data: categories.counts,
                    emphasis: { focus: 'none' },
                    itemStyle: {
                        color: (params) => generateColor(params.dataIndex, categories.counts.length),
                        borderRadius: [0, 6, 6, 0]
                    }
                }]
            };

            myChart_divis.setOption(option_divis);

            // --- PERUBAHAN TEMA (DaisyUI) ---
            const observer = new MutationObserver(() => {
                const newTheme = fetchColors();
                myChart_divis.setOption({
                    title: { textStyle: { color: newTheme.content } },
                    tooltip: { backgroundColor: newTheme.base100, borderColor: newTheme.primary, textStyle: { color: newTheme.content } },
                    xAxis: { axisLabel: { color: newTheme.content }, splitLine: { lineStyle: { color: newTheme.base300 } } },
                    yAxis: { axisLabel: { color: newTheme.content } }
                });
            });
            observer.observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });

            // --- LIVEWIRE UPDATE EVENT ---
            // Listener ini harus di-refresh tiap kali initChart dipanggil
            Livewire.on('distribusiDivisi', (event) => {
                const data = typeof event[0] === 'string' ? JSON.parse(event[0]) : event[0];
                myChart_divis.setOption({
                    title: { text: 'Jumlah Laporan ' + data.year },
                    yAxis: { data: data.label },
                    series: [{
                        name: data.year,
                        data: data.counts,
                        itemStyle: {
                            color: (params) => generateColor(params.dataIndex, data.counts.length)
                        }
                    }]
                });
            });

            window.addEventListener('resize', () => myChart_divis.resize());
        };

        // Jalankan saat navigasi Livewire selesai
        document.addEventListener('livewire:navigated', initHazardChart);

        // Fallback untuk load pertama kali tanpa wire:navigate
        initHazardChart();
    </script>
</div>
