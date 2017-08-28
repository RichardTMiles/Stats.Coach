<!-- Main content -->
<section class="invoice">

    <style>
        #count {
            font: bold 150px arial;
            margin: auto;
            padding: 10px;
            text-align: center;
        }
    </style>


    <div id="count"></div>

    <script>
        var ws = new WebSocket('wss://stats.coach:1234/');
        ws.onopen = function () {
            document.body.style.backgroundColor = '#cfc';
        };
        ws.onclose = function () {
            document.body.style.backgroundColor = null;
        };
        ws.onerror = function () {
            bootstrapAlert('Socket Failed');
        };
        ws.onmessage = function (event) {
            document.getElementById('count').textContent = event.data;
        };
    </script>

</section>