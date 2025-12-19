<div>
    <div wire:ignore id="hazardStatusByContDept" style="height: 320px;" class="w-full"></div>
    @push('scripts')
        <!-- Load ECharts dari CDN -->
        <script type="module">
            setInterval(() => Livewire.dispatch('hazardStatusByCont_Dept'), 1000);
            const rawData = @json(json_decode($statusDeptCont, true));
            var dom = document.getElementById('hazardStatusByContDept');
            var myChart = echarts.init(dom);

            var option = {
                title: {
                    text: 'Status Laporan per Departemen/Kontraktor',
                    left: 'center',
                    subtext: 'Tahun Berjalan',
                    top: 0
                },
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'shadow'
                    }
                },
                legend: {
                    data: ['Open', 'Closed'],
                    bottom: 0 // Letakkan di bawah agar tidak menabrak judul
                },
                // --- PERBAIKAN GRID ---
                grid: {
                    top: '15%',
                    left: '3%',
                    right: '4%',
                    bottom: '30%', // Beri ruang yang luas untuk label sumbu X
                    containLabel: true
                },
                xAxis: {
                    type: 'category',
                    data: rawData.labels || [],
                    axisLabel: {
                        interval: 0,
                        rotate: 45, // Miringkan 45 derajat agar teks panjang terbaca
                        fontSize: 10,
                        // Memotong teks jika lebih dari 15 karakter agar tidak merusak layout
                        formatter: function(value) {
                            return value.length > 15 ? value.substring(0, 15) + '...' : value;
                        }
                    }
                },
                yAxis: {
                    type: 'value',
                    name: 'Jumlah'
                },
                series: [{
                        name: 'Open',
                        type: 'bar',
                        stack: 'total',
                        itemStyle: {
                            color: '#F87171'
                        },
                        data: rawData.open || []
                    },
                    {
                        name: 'Closed',
                        type: 'bar',
                        stack: 'total',
                        itemStyle: {
                            color: '#34D399',
                            borderRadius: [5, 5, 0, 0] // Rounded hanya di atas
                        },
                        data: rawData.closed || []
                    }
                ]
            };

            myChart.setOption(option);

            // Listener Livewire v3
            Livewire.on('hazardStatus_DeptOrCont', event => {
                // Pastikan event mengirim data yang benar
                let payload = (typeof event.data === 'string') ? JSON.parse(event.data) : event[0];

                myChart.setOption({
                    xAxis: {
                        data: payload.labels
                    },
                    series: [{
                            data: payload.open
                        },
                        {
                            data: payload.closed
                        }
                    ]
                });
            });

            window.addEventListener('resize', myChart.resize);
        </script>
    @endpush
</div>
