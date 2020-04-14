@extends('layouts.app')

@section('title', 'by total number of cases')

@section('content')
    <div class="container">
        <h5>COVID 19</h5>
        <div id="chart-totals-confirmed" style="width: 100%; height: 600px;"></div>
        <div id="chart-totals-deaths" style="width: 100%; height: 600px;"></div>
        <div id="chart-ratios-million" style="width: 100%; height: 600px;"></div>
        <div id="chart-confirmed" style="width: 100%; height: 600px;"></div>
        <div id="chart-confirmed-reset" style="width: 100%; height: 600px;"></div>
        <div id="chart-deaths" style="width: 100%; height: 600px;"></div>
        <div id="chart-deaths-reset" style="width: 100%; height: 600px;"></div>
        <div id="chart-confirmed-daily" style="width: 100%; height: 600px;"></div>
        <div id="chart-confirmed-daily-reset" style="width: 100%; height: 600px;"></div>
        <div id="chart-deaths-daily" style="width: 100%; height: 600px;"></div>
        <div id="chart-deaths-daily-reset" style="width: 100%; height: 600px;"></div>
    </div>

    <script>
        $(document).ready(function () {
            $.ajax({
                url: window.location.origin + "/covid19/covid.json?base=totals&type=confirmed&mode=normal&start_from=0&current_over=6000",
                success: function (result) {
                    Plotly.newPlot(document.getElementById('chart-totals-confirmed'), result.data_sets, {
                        title: 'Top countries by confirmed, over 6000',
                        barmode: 'stack'
                    }, {
                        responsive: true
                    });
                },
                error: function (err) {
                }
            });
            $.ajax({
                url: window.location.origin + "/covid19/covid.json?base=totals&type=deaths&mode=normal&start_from=0&current_over=300",
                success: function (result) {
                    Plotly.newPlot(document.getElementById('chart-totals-deaths'), result.data_sets, {
                        title: 'Top countries by deaths, over 300',
                        barmode: 'stack'
                    }, {
                        responsive: true
                    });
                },
                error: function (err) {
                }
            });
            $.ajax({
                url: window.location.origin + "/covid19/covid.json?base=totals&type=confirmed&mode=million&start_from=0&current_over=6000",
                success: function (result) {
                    Plotly.newPlot(document.getElementById('chart-ratios-million'), result.data_sets, {
                        title: 'Top countries by deaths per million, over 300',
                        barmode: 'stack'
                    }, {
                        responsive: true
                    });
                },
                error: function (err) {
                }
            });
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
                    Plotly.newPlot(document.getElementById('chart-confirmed-daily'), result.data_sets, {
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
                    Plotly.newPlot(document.getElementById('chart-confirmed-daily-reset'), result.data_sets, {
                        title: 'Confirmed per day (had at least 300/day), reset',
                        barmode: 'stack'
                    }, {
                        responsive: true
                    });
                },
                error: function (err) {
                }
            });
            $.ajax({
                url: window.location.origin + "/covid19/covid.json?base=daily&type=deaths&mode=normal&start_from=10&current_over=35",
                success: function (result) {
                    Plotly.newPlot(document.getElementById('chart-deaths-daily'), result.data_sets, {
                        title: 'Deaths per day (had at least 35/day), timeline',
                        // barmode: 'stack'
                    }, {
                        responsive: true
                    });
                },
                error: function (err) {
                }
            });
            $.ajax({
                url: window.location.origin + "/covid19/covid.json?base=daily&type=deaths&mode=reset&start_from=0&current_over=35",
                success: function (result) {
                    Plotly.newPlot(document.getElementById('chart-deaths-daily-reset'), result.data_sets, {
                        title: 'Deaths per day (had at least 35/day), reset',
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
