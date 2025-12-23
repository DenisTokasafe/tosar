<div>
    <div wire:ignore id="container" style="height: 320px"></div>
<script type="module">
    var dom_divis = document.getElementById('container');
    const categories = @json($categories);
    var myChart_divis = echarts.init(dom_divis, null, {
        renderer: 'canvas'
        , useDirtyRect: false
    });
    // 🎨 Fungsi untuk menghasilkan warna berbeda-beda otomatis
    function generateColor(index, total) {
        // Gunakan lingkaran warna (HSL)
        const hue = (index * (360 / total)) % 360; // bagi rata keliling 360°
        return `hsl(${hue}, 65%, 55%)`; // saturasi & lightness agar tetap cerah
    }

    var option_divis;

    option_divis = {
        title: {
            text: 'Jumlah Laporan'
        }
        , grid: {
            top: 50
            , left: 110
            , right: 30
            , bottom: 60
        }
        , tooltip: {
            trigger: 'axis'
            , axisPointer: {
                type: 'shadow'
            }
        }
        , legend: {}
        , xAxis: {
            type: 'value'
            , boundaryGap: [0, 0.01]
        }
        , yAxis: {
            type: 'category'
            , data: categories.label
            , inverse: true
            , axisLabel: {
                color: '#333', // warna teks
                fontSize: 7, // ukuran font
                fontWeight: 'bold', // ketebalan font (normal | bold | bolder | lighter)
                fontFamily: 'Poppins, sans-serif', // jenis font
                overflow: 'truncate', // potong teks jika terlalu panjang
                width: 150, // batas lebar teks (bisa disesuaikan)
                align: 'right' // posisi teks relatif ke sumbu
            }, // ⬅️ urutkan dari atas ke bawah sesuai urutan data // ⬅️ urutkan dari atas ke bawah sesuai urutan data
        }
        , series: [{
            name: categories.year // ✅ ambil dari data Livewire
            , type: 'bar'
            , data: categories.counts
            , itemStyle: {
                color: function(params) {
                    // Gunakan warna dinamis berdasarkan posisi bar
                    return generateColor(params.dataIndex, categories.counts.length);
                }
                , borderRadius: [0, 6, 6, 0]
            }
        }]
    };

    if (option_divis && typeof option_divis === 'object') {
        myChart_divis.setOption(option_divis);
        Livewire.on('distribusiDivisi', event => {
            const payload_divisi = JSON.parse(event);

            // Bentuk ulang warna berdasarkan jumlah bar baru
            const seriesData = payload_divisi.counts.map((count, index) => ({
                value: count
                , itemStyle: {
                    color: generateColor(index, payload_divisi.counts.length)
                }
            }));

            // Update chart tanpa re-init
            myChart_divis.setOption({
                title: {
                    text: 'Jumlah Laporan ' + payload_divisi.year
                }
                , yAxis: {
                    data: payload_divisi.label
                    , inverse: true // biar tetap urut dari atas ke bawah
                }
                , series: [{
                    name: payload_divisi.year
                    , data: payload_divisi.counts
                    , itemStyle: {
                        color: function(params) {
                            return generateColor(params.dataIndex, payload_divisi.counts.length);
                        }
                        , borderRadius: [0, 6, 6, 0]
                    }
                }]
            });
        });
    }

    window.addEventListener('resize', myChart_divis.resize);

</script>
</div>
