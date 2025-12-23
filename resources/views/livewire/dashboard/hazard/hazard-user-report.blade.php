<div>
    <div wire:ignore id="container_reportby" style="height: 320px"></div>
<script type="module">
    var dom_reportBy = document.getElementById('container_reportby');
    const pelapor = @json($pelapor);
    var myChart_reportBy = echarts.init(dom_reportBy, null, {
        renderer: 'canvas',
        useDirtyRect: false
    });

    function generateColor(index, total) {
        const seed = Math.sin(index + 1) * 10000;
        const hue = (seed - Math.floor(seed)) * 360;
        return `hsl(${hue}, 70%, 55%)`;
    }

    var option_reportBy = {
        title: { text: 'Top Kontributor' },
        grid: { top: 50, left: 110, right: 30, bottom: 60 },
        tooltip: { trigger: 'axis', axisPointer: { type: 'shadow' } },
        legend: {},
        xAxis: { type: 'value', boundaryGap: [0, 0.01] },
        yAxis: {
            type: 'category',
            data: pelapor.label,
            inverse: true,
            axisLabel: {
                color: '#333',
                fontSize: 9,
                fontWeight: 'bold',
                fontFamily: 'Poppins, sans-serif',
                overflow: 'truncate',
                width: 150,
                align: 'right'
            }
        },
        series: [{
            name: pelapor.year,
            type: 'bar',
            data: pelapor.counts,
            itemStyle: {
                color: params => generateColor(params.dataIndex, pelapor.counts.length),
                borderRadius: [0, 6, 6, 0]
            }
        }]
    };

    myChart_reportBy.setOption(option_reportBy);

    Livewire.on('distribusiPelapor', event => {
        const payload = JSON.parse(event);
        myChart_reportBy.setOption({
            title: { text: 'Top Kontributor ' + payload.year },
            yAxis: { data: payload.label, inverse: true },
            series: [{
                name: payload.year,
                data: payload.counts,
                itemStyle: {
                    color: params => generateColor(params.dataIndex, payload.counts.length),
                    borderRadius: [0, 6, 6, 0]
                }
            }]
        });
    });

    window.addEventListener('resize', myChart_reportBy.resize);
</script>
</div>
