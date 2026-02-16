<div>
    <div wire:ignore id="container" style="height: 320px"></div>
    <script type="module">
        // var dom_divis = document.getElementById('container');
        const chartDom = document.getElementById('container');
        let myChart = echarts.init(chartDom);

        // Fungsi generate warna HSL Anda
        function generateColor(index, total) {
            const hue = (index * (360 / total)) % 360;
            return `hsl(${hue}, 65%, 55%)`;
        }

        const parseAndRender = (rawData) => {
            if (!rawData) return;
            const data = typeof rawData === 'string' ? JSON.parse(rawData) : rawData;

            // Buat array warna berdasarkan jumlah data.label
            const dynamicColors = data.label.map((_, i) => generateColor(i, data.label.length));

            myChart.setOption({
                title: {
                    text: 'Periode: ' + data.year,
                    left: 'center',
                    textStyle: {
                        fontSize: 13,
                        fontWeight: 'normal'
                    }
                },
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'shadow'
                    }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: {
                    type: 'value'
                },
                yAxis: {
                    type: 'category',
                    data: data.label,
                    inverse: true
                },
                series: [{
                    name: 'Total',
                    data: data.counts.map((val, i) => {
                        // Masukkan warna secara spesifik ke tiap item data
                        return {
                            value: val,
                            itemStyle: {
                                color: dynamicColors[i]
                            }
                        };
                    }),
                    type: 'bar',
                    itemStyle: {
                        borderRadius: [0, 4, 4, 0]
                    },
                    label: {
                        show: true,
                        position: 'right'
                    }
                }]
            });
        };

        // Render awal
        parseAndRender($wire.categories);

        // Event listener untuk update data
        $wire.on('distribusiDivisi', (event) => {
            const data = Array.isArray(event) ? event[0] : event;
            parseAndRender(data);
        });

        window.addEventListener('resize', () => myChart.resize());
    </script>
</div>
