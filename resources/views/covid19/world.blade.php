@extends('layouts.app')

@section('title', 'by total number of cases')

@section('content')
<div class="container">
    <h6>COVID 19 <small>(data: <span class="date">____-__-__</span>)</small> | <a href="/covid19/by-deaths">by deaths</a> | <a href="/covid19/">by cases</a></h6>
    <h6>No. of cases starting with the 500th discovered case</h6>
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
                        beginAtZero: true,
                        ticks: {
                            display: true,
                        },
                        // type: 'logarithmic'
                    }],
                },
                legend: {
                    display: false,
                }
            }
        });
    }
    function getChartData() {
        $.ajax({
            url: window.location.origin + "/covid19/covid.json?type=world&days=40",
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
