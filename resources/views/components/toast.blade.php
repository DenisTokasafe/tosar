<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('alert', (event) => {
            const data = event
            Toastify({
                text: data[0]['text']
                , duration: data[0]['duration']
                , destination: data[0]['duration']
                , newWindow: data[0]['newWindow']
                , close: data[0]['close']
                , gravity: "top", // `top` or `bottom`
                positionLeft: false, // `true` or `false`
                backgroundColor: data[0]['backgroundColor']
            }).showToast();
        });
    });

</script>
