@extends('layouts.app')

@section('title', 'by total number of cases')

@section('content')
<div class="container">
    <a href="/covid19/by-deaths">Switch to proj. by deaths</a>
    <h6>COVID 19 - Proj. by total cases <small>(<span class="date"></span>)</small></h6>
    <p class="small justify-content-start d-none d-lg-block">Methodology: all countries having currently over 1000 registered cases, all offset to the day they passed 500 cases</p>
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
            url: window.location.origin + "/covid19/covid.json?type=Confirmed&days=35&start_from=1000&end_at=80000&cut_off=350",
            success: function (result) {
                renderChart(result);
                $('.date').html(result.date);
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
