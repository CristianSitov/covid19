@extends('layouts.app')

@section('title', 'by deaths')

@section('content')
<div class="container">
    <h6>COVID 19 <small>(data: <span class="date">____-__-__</span>)</small> | <a href="/covid19/">by cases</a></h6>
    <h6>No. of deaths starting with the 1st deceased</h6>
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
                        beginAtZero: false,
                        ticks: {
                            display: false,
                        },
                    }]
                },
                legend: {
                    display: false,
                }
            }
        });
    }
    function getChartData() {
        $.ajax({
            url: window.location.origin + "/covid19/covid.json?type=Deaths&days=45&start_from=0&end_at=10000&cut_off=10",
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
