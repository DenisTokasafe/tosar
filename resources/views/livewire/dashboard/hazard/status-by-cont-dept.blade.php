<div>

    <div wire:ignore id="hazardStatusByContDept" style="height: 320px;" class="w-full"></div>
    <!-- Load ECharts dari CDN -->
    <script type="module">
    // Mengambil data awal dari Livewire
    let rawData = @json(json_decode($statusDeptCont, true));

    var dom = document.getElementById('hazardStatusByContDept');
    var myChart = echarts.init(dom);

    var option = {
        title: {
            text: 'Status Laporan per Departemen/Kontraktor',
            left: 'center',
            // Dinamis mengambil range tanggal (misal: "01-02-2025 s/d 08-01-2026")
            subtext: rawData.range ? 'Periode: ' + rawData.range : '12 Bulan Terakhir',
            subtextStyle: {
                color: '#6B7280',
                fontSize: 12
            }
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            },
            // Custom tooltip agar menampilkan total saat di-hover
            formatter: function (params) {
                let res = '<b>' + params[0].name + '</b>';
                let total = 0;
                params.forEach(item => {
                    res += '<br/>' + item.marker + ' ' + item.seriesName + ': ' + item.value;
                    total += item.value;
                });
                res += '<br/><b>Total: ' + total + '</b>';
                return res;
            }
        },
        legend: {
            data: ['Open', 'Closed'],
            bottom: 5
        },
        grid: {
            top: 80,
            left: '3%',
            right: '4%',
            bottom: '15%', // Memberi ruang untuk rotasi label xAxis
            containLabel: true
        },
        // Fitur Zoom/Slider jika data departemen sangat banyak
        dataZoom: [
            {
                type: 'inside',
                start: 0,
                end: 100
            },
            {
                show: true,
                type: 'slider',
                top: 'bottom',
                start: 0,
                end: 100,
                height: 20
            }
        ],
        xAxis: {
            type: 'category',
            data: rawData.labels,
            axisLabel: {
                interval: 0,
                rotate: 35, // Kemiringan optimal untuk keterbacaan
                fontSize: 10,
                // Memotong teks panjang agar tidak menabrak batas bawah
                formatter: function(value) {
                    return value.length > 12 ? value.substring(0, 12) + '...' : value;
                }
            }
        },
        yAxis: {
            type: 'value',
            name: 'Jumlah Laporan',
            splitLine: {
                lineStyle: {
                    type: 'dashed'
                }
            }
        },
        series: [
            {
                name: 'Open',
                type: 'bar',
                stack: 'total',
                barMaxWidth: 40, // Membatasi lebar bar agar tidak terlalu gemuk jika data sedikit
                itemStyle: {
                    color: '#F87171', // Red-400 (Tailwind)
                },
                emphasis: { focus: 'series' },
                data: rawData.open
            },
            {
                name: 'Closed',
                type: 'bar',
                stack: 'total',
                barMaxWidth: 40,
                itemStyle: {
                    color: '#34D399', // Emerald-400 (Tailwind)
                    borderRadius: [4, 4, 0, 0] // Melengkung hanya di atas bar tumpukan
                },
                emphasis: { focus: 'series' },
                data: rawData.closed
            }
        ]
    };

    myChart.setOption(option);

    // Listener untuk update data dari Livewire (Filter dinamis)
    Livewire.on('hazardStatus_DeptOrCont', event => {
        let payload = JSON.parse(event);

        myChart.setOption({
            title: {
                subtext: payload.range ? 'Periode: ' + payload.range : '12 Bulan Terakhir'
            },
            xAxis: {
                data: payload.labels
            },
            series: [
                { data: payload.open },
                { data: payload.closed }
            ]
        });
    });

    // Responsif saat ukuran layar berubah
    window.addEventListener('resize', () => {
        myChart.resize();
    });
</script>
</div>
