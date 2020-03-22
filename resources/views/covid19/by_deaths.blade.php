@extends('layouts.app')

@section('title', 'by deaths')

@section('content')
<div class="container">
    <a href="/covid19/">Switch to proj. by total cases</a>
    <h6>COVID 19 - Proj. by deaths <span class="date"></span></h6>
    <p class="small justify-content-start">Methodology: all countries having currently over 10 registered deaths, all offset to the day of the first case</p>
    <div class="chart-container">
        <canvas id="myChart" width="600" height="600"></canvas>
    </div>
</div>

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
            url: window.location.origin + "/covid19/covid.json?type=Deaths&days=35&start_from=1&end_at=10000&cut_off=10",
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
@endsection
