<div>
        <div wire:ignore id="chart-container" style="height: 320px;" class="w-full"></div>
    <!-- Load ECharts dari CDN -->
     <script type="module">
        var dom_status = document.getElementById('chart-container');
        // 🧠 Ambil data dari Livewire (JSON string → object JS)
        // const chartData = JSON.parse('<?php echo $statusChart ?>');
        const chartData = JSON.parse(@json($statusChart));

        const labels = chartData.labels;
        const values = chartData.values;
        // 🔥 Masukkan data Livewire ke format yang ECharts butuh
        const seriesData = labels.map((label, i) => ({
            name: label
            , value: values[i]
        }));
        var myChart_status = echarts.init(dom_status, null, {
            renderer: 'canvas'
            , useDirtyRect: false
        });
        var app = {};
        var option_status;
        option_status = {
            title: {
                text: 'Distribusi Status'
                , left: 'center'
            }
            , tooltip: {
                trigger: 'item'
                , formatter: '{b}: {c} laporan ({d}%)' // tooltip tetap bisa tampil dua-duanya
            }
            , legend: {
                top: 'bottom'
                , left: 'center'
                , textStyle: {
                    fontSize: 8, // 🔹 Ukuran teks legend
                    fontWeight: 'normal', // opsional: 'bold', 'bolder', dll
                    color: '#333', // opsional: warna teks legend
                    fontFamily: 'Arial' // opsional: jenis font
                }
            }
            , series: [{
                name: 'Status'
                , type: 'pie'
                , radius: '40%'
                , data: seriesData
                , label: {
                    formatter: '{c}' // 🔥 tampilkan total value (jumlah laporan)
                }
                , emphasis: {
                    itemStyle: {
                        shadowBlur: 10
                        , shadowOffsetX: 0
                        , shadowColor: 'rgba(0, 0, 0, 0.5)'
                    }
                }
            }]
        };
        if (option_status && typeof option_status === 'object') {
            myChart_status.setOption(option_status);
            Livewire.on('distribusiStatus', event => {
                let payload_status = JSON.parse(event); // ini parse JSON dari PHP
                const labels = payload_status.labels;
                const values = payload_status.values;

                // Bentuk ulang data untuk series chart
                const seriesData = labels.map((label, i) => ({
                    name: label
                    , value: values[i]
                }));

                myChart_status.setOption({
                    series: [{
                        name: 'Status'
                        , type: 'pie'
                        , radius: '40%'
                        , data: seriesData
                        , label: {
                            formatter: '{c}' // 🔥 tampilkan total value (jumlah laporan)
                        }
                        , emphasis: {
                            itemStyle: {
                                shadowBlur: 10
                                , shadowOffsetX: 0
                                , shadowColor: 'rgba(0, 0, 0, 0.5)'
                            }
                        }
                    }]
                });
            });
        }
        window.addEventListener('resize', myChart_status.resize);

    </script>
</div>
