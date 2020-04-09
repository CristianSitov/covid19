@extends('layouts.app')

@section('title', 'by total number of cases')

@section('content')
    <div class="container">
        <h5>COVID 19</h5>
        <div id="chart-confirmed" style="width: 100%; height: 600px;"></div>
        <div id="chart-confirmed-reset" style="width: 100%; height: 600px;"></div>
        <div id="chart-deaths" style="width: 100%; height: 600px;"></div>
        <div id="chart-deaths-reset" style="width: 100%; height: 600px;"></div>
        <div id="chart-daily" style="width: 100%; height: 600px;"></div>
        <div id="chart-daily-reset" style="width: 100%; height: 600px;"></div>
    </div>

    <script>
        $(document).ready(function () {
            $.ajax({
                url: window.location.origin + "/covid19/covid.json?base=record&type=confirmed&mode=normal&start_from=0&current_over=3500",
                success: function (result) {
                    Plotly.newPlot(document.getElementById('chart-confirmed'), result.data_sets, {
                        title: 'Confirmed cases, starting with 1st case, timeline',
                    }, {
                        responsive: true
                    });
                },
                error: function (err) {
                }
            });
            $.ajax({
                url: window.location.origin + "/covid19/covid.json?base=record&type=confirmed&mode=reset&start_from=100&current_over=3500",
                success: function (result) {
                    Plotly.newPlot(document.getElementById('chart-confirmed-reset'), result.data_sets, {
                        title: 'Confirmed cases, starting with 100th, aligned',
                    }, {
                        responsive: true
                    });
                },
                error: function (err) {
                }
            });
            $.ajax({
                url: window.location.origin + "/covid19/covid.json?base=record&type=deaths&mode=normal&start_from=0&current_over=100",
                success: function (result) {
                    Plotly.newPlot(document.getElementById('chart-deaths'), result.data_sets, {
                        title: 'Deaths, timeline',
                    }, {
                        responsive: true
                    });
                },
                error: function (err) {
                }
            });
            $.ajax({
                url: window.location.origin + "/covid19/covid.json?base=record&type=deaths&mode=reset&start_from=100&current_over=100",
                success: function (result) {
                    Plotly.newPlot(document.getElementById('chart-deaths-reset'), result.data_sets, {
                        title: 'Deaths starting with 100th, aligned',
                    }, {
                        responsive: true
                    });
                },
                error: function (err) {
                }
            });
            $.ajax({
                url: window.location.origin + "/covid19/covid.json?base=daily&type=confirmed&mode=normal&start_from=10&current_over=300",
                success: function (result) {
                    Plotly.newPlot(document.getElementById('chart-daily'), result.data_sets, {
                        title: 'Confirmed per day (had at least 300/day), timeline',
                        // barmode: 'stack'
                    }, {
                        responsive: true
                    });
                },
                error: function (err) {
                }
            });
            $.ajax({
                url: window.location.origin + "/covid19/covid.json?base=daily&type=confirmed&mode=reset&start_from=0&current_over=300",
                success: function (result) {
                    Plotly.newPlot(document.getElementById('chart-daily-reset'), result.data_sets, {
                        title: 'Confirmed per day (had at least 300/day), reset',
                        barmode: 'stack'
                    }, {
                        responsive: true
                    });
                },
                error: function (err) {
                }
            });
        });
    </script>
@endsection
