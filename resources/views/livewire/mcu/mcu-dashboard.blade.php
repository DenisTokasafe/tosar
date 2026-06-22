<div>
    <div class="p-6 bg-base-200 min-h-screen">
        <h1 class="text-2xl font-bold mb-6 text-base-content">Dashboard Medical Check-Up</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

            <div class="bg-base-100 p-4 rounded-xl shadow border border-base-300">
                <h3 class="text-lg font-semibold mb-2 text-center">Tingkat Kehadiran MCU</h3>
                <div id="chartKehadiran" wire:ignore style="width: 100%; height: 300px;"></div>
            </div>

            <div class="bg-base-100 p-4 rounded-xl shadow border border-base-300">
                <h3 class="text-lg font-semibold mb-2 text-center">Sebaran Status Kebugaran</h3>
                <div id="chartFitStatus" wire:ignore style="width: 100%; height: 300px;"></div>
            </div>

            <div class="bg-base-100 p-4 rounded-xl shadow border border-base-300">
                <h3 class="text-lg font-semibold mb-2 text-center">Status Review Dokter</h3>
                <div id="chartWorkflow" wire:ignore style="width: 100%; height: 300px;"></div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/echarts@5.5.0/dist/echarts.min.js"></script>

    <script>
        document.addEventListener('livewire:initialized', () => {

            // ==========================================
            // 1. Grafik Kehadiran (Donut Chart)
            // ==========================================
            var chartKehadiran = echarts.init(document.getElementById('chartKehadiran'));
            chartKehadiran.setOption({
                tooltip: {
                    trigger: 'item'
                },
                legend: {
                    bottom: '0%',
                    left: 'center'
                },
                color: ['#10b981', '#ef4444', '#f59e0b'], // Hijau (Sudah), Merah (Lewat), Kuning (Menunggu)
                series: [{
                    name: 'Kehadiran',
                    type: 'pie',
                    radius: ['40%', '70%'],
                    avoidLabelOverlap: false,
                    label: {
                        show: false,
                        position: 'center'
                    },
                    emphasis: {
                        label: {
                            show: true,
                            fontSize: 20,
                            fontWeight: 'bold'
                        }
                    },
                    data: [{
                            value: {
                                {
                                    $sudahMcu
                                }
                            },
                            name: 'Sudah MCU'
                        },
                        {
                            value: {
                                {
                                    $terlewatMcu
                                }
                            },
                            name: 'Terlewat Jadwal'
                        },
                        {
                            value: {
                                {
                                    $menungguJadwal
                                }
                            },
                            name: 'Menunggu Jadwal'
                        }
                    ]
                }]
            });

            // ==========================================
            // 2. Grafik Status Kebugaran (Pie Chart)
            // ==========================================
            var chartFitStatus = echarts.init(document.getElementById('chartFitStatus'));
            chartFitStatus.setOption({
                tooltip: {
                    trigger: 'item'
                },
                legend: {
                    bottom: '0%',
                    left: 'center',
                    itemWidth: 10,
                    itemHeight: 10,
                    textStyle: {
                        fontSize: 10
                    }
                },
                color: ['#3b82f6', '#eab308', '#f97316', '#ef4444'], // Biru, Kuning, Oranye, Merah
                series: [{
                    name: 'Status',
                    type: 'pie',
                    radius: '60%',
                    data: [{
                            value: {
                                {
                                    $fitStatus['fit_to_work']
                                }
                            },
                            name: '✅ Fit To Work'
                        },
                        {
                            value: {
                                {
                                    $fitStatus['fit_with_notes']
                                }
                            },
                            name: '⚠️ Fit With Notes'
                        },
                        {
                            value: {
                                {
                                    $fitStatus['temporary_unfit']
                                }
                            },
                            name: '⏳ Temp. Unfit'
                        },
                        {
                            value: {
                                {
                                    $fitStatus['unfit']
                                }
                            },
                            name: '❌ Unfit'
                        }
                    ],
                    emphasis: {
                        itemStyle: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)'
                        }
                    }
                }]
            });

            // ==========================================
            // 3. Grafik Workflow Review (Bar Chart)
            // ==========================================
            var chartWorkflow = echarts.init(document.getElementById('chartWorkflow'));
            chartWorkflow.setOption({
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'shadow'
                    }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '15%',
                    containLabel: true
                },
                xAxis: {
                    type: 'category',
                    data: ['Pending Doctor', 'Reviewed'],
                    axisLabel: {
                        fontWeight: 'bold'
                    }
                },
                yAxis: {
                    type: 'value'
                },
                series: [{
                    name: 'Jumlah Data',
                    type: 'bar',
                    barWidth: '50%',
                    // Warna dinamis: Kuning untuk pending, Hijau untuk reviewed
                    itemStyle: {
                        color: function(params) {
                            var colorList = ['#eab308', '#10b981'];
                            return colorList[params.dataIndex];
                        },
                        borderRadius: [4, 4, 0, 0]
                    },
                    data: [{
                            {
                                $workflowStatus['pending_doctor']
                            }
                        },
                        {
                            {
                                $workflowStatus['reviewed']
                            }
                        }
                    ]
                }]
            });

            // Bikin grafik responsive jika jendela browser di-resize
            window.addEventListener('resize', function() {
                chartKehadiran.resize();
                chartFitStatus.resize();
                chartWorkflow.resize();
            });
        });
    </script>
</div>