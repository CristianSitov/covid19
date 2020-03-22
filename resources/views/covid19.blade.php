<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>COVID19</title>

    <!-- Fonts -->
    <link href="/css/app.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/chart.js@2.9.3/dist/Chart.min.css" rel="stylesheet">

</head>
<body>
<div class="container">
    <h6>COVID 19</h6>
    <p class="small justify-content-start">Methodology: all countries having currently over 1000 registered cases, all offset to the day they passed 500 cases</p>
    <div class="chart-container">
        <canvas id="myChart" width="600" height="600"></canvas>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.3/dist/Chart.js"></script>
<script>
    function renderChart(data) {
        var ctx = document.getElementById("myChart").getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'line',
            data: data,
            options: {
                maintainAspectRatio: false,
                responsive: true,
                scales: {
                    yAxes: [{
                        ticks: {
                            display: false,
                        }
                    }]
                },
            }
        });
    }
    function getChartData() {
        $.ajax({
            url: window.location.origin + "/covid19/covid.json",
            success: function (result) {
                renderChart(result);
            },
            error: function (err) {
            }
        });
    }
    function resize(){
        $("#myChart").outerHeight($(window).height()-$("#myChart").offset().top- Math.abs($("#myChart").outerHeight(true) - $("#myChart").outerHeight()));
        if ($(window).width() > 1024) {
            $("#myChart").width($("#myChart").outerHeight());
        }
    }

    $( document ).ready(function() {
        getChartData();
        resize();
        $(window).on("resize", function(){
            resize();
        });
    });
</script>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-161503923-1"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-161503923-1');
</script>
</body>
</html>
