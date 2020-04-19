@extends('layouts.app')

@section('title', 'by total number of cases')

@section('content')
    <div class="container">
        <h5>COVID 19</h5>
        <div id="chart-totals-confirmed" class="chart"></div>
        <div id="chart-totals-deaths" class="chart"></div>
        <div id="chart-ratios-confirmed-million" class="chart"></div>
        <div id="chart-ratios-deaths-million" class="chart"></div>
        <div id="chart-confirmed" class="chart"></div>
        <div id="chart-confirmed-reset" class="chart"></div>
        <div id="chart-deaths" class="chart"></div>
        <div id="chart-deaths-reset" class="chart"></div>
        <div id="chart-confirmed-daily" class="chart"></div>
        <div id="chart-confirmed-daily-reset" class="chart"></div>
        <div id="chart-deaths-daily" class="chart"></div>
        <div id="chart-deaths-daily-reset" class="chart"></div>
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
                        responsive: true,
                        displayModeBar: false
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
                        responsive: true,
                        displayModeBar: false
                    });
                },
                error: function (err) {
                }
            });
            $.ajax({
                url: window.location.origin + "/covid19/covid.json?base=totals&type=confirmed&mode=million&start_from=0&current_over=6000",
                success: function (result) {
                    Plotly.newPlot(document.getElementById('chart-ratios-confirmed-million'), result.data_sets, {
                        title: 'Top countries by confirmed per million, over 6000',
                        barmode: 'stack'
                    }, {
                        responsive: true,
                        displayModeBar: false
                    });
                },
                error: function (err) {
                }
            });
            $.ajax({
                url: window.location.origin + "/covid19/covid.json?base=totals&type=deaths&mode=million&start_from=0&current_over=400",
                success: function (result) {
                    Plotly.newPlot(document.getElementById('chart-ratios-deaths-million'), result.data_sets, {
                        title: 'Top countries by deaths per million, over 400',
                        barmode: 'stack'
                    }, {
                        responsive: true,
                        displayModeBar: false
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
                        responsive: true,
                        displayModeBar: false
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
                        responsive: true,
                        displayModeBar: false
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
                        responsive: true,
                        displayModeBar: false
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
                        responsive: true,
                        displayModeBar: false
                    });
                },
                error: function (err) {
                }
            });
            $.ajax({
                url: window.location.origin + "/covid19/covid.json?base=daily&type=avg7_confirmed&mode=normal&start_from=10&current_over=400",
                success: function (result) {
                    Plotly.newPlot(document.getElementById('chart-confirmed-daily'), result.data_sets, {
                        title: 'Confirmed per day (had at least 400/day), timeline - 7 day average',
                        // barmode: 'stack'
                    }, {
                        responsive: true,
                        displayModeBar: false
                    });
                },
                error: function (err) {
                }
            });
            $.ajax({
                url: window.location.origin + "/covid19/covid.json?base=daily&type=avg7_confirmed&mode=reset&start_from=0&current_over=300",
                success: function (result) {
                    Plotly.newPlot(document.getElementById('chart-confirmed-daily-reset'), result.data_sets, {
                        title: 'Confirmed per day (had at least 300/day), reset - 7 day average',
                        // barmode: 'stack'
                    }, {
                        responsive: true,
                        displayModeBar: false
                    });
                },
                error: function (err) {
                }
            });
            $.ajax({
                url: window.location.origin + "/covid19/covid.json?base=daily&type=avg7_deaths&mode=normal&start_from=10&current_over=20",
                success: function (result) {
                    Plotly.newPlot(document.getElementById('chart-deaths-daily'), result.data_sets, {
                        title: 'Deaths per day (had at least 20/day), timeline - 7 day average',
                        // barmode: 'stack'
                    }, {
                        responsive: true,
                        displayModeBar: false
                    });
                },
                error: function (err) {
                }
            });
            $.ajax({
                url: window.location.origin + "/covid19/covid.json?base=daily&type=avg7_deaths&mode=reset&start_from=0&current_over=20",
                success: function (result) {
                    Plotly.newPlot(document.getElementById('chart-deaths-daily-reset'), result.data_sets, {
                        title: 'Deaths per day (had at least 20/day), reset - 7 day average',
                        barmode: 'stack'
                    }, {
                        responsive: true,
                        displayModeBar: false
                    });
                },
                error: function (err) {
                }
            });
        });
    </script>
@endsection
