<?php

use Illuminate\Support\Facades\Route;
use League\Csv\Reader;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/covid.json', function () {
    $url = "https://raw.githubusercontent.com/CSSEGISandData/COVID-19/master/csse_covid_19_data/csse_covid_19_time_series/time_series_19-covid-Confirmed.csv";
    $contents = file_get_contents($url);
    $name = substr($url, strrpos($url, '/') + 1);
    Storage::put($name, $contents);

    $csv = Reader::createFromPath(storage_path('app') . DIRECTORY_SEPARATOR . $name);
    $csv->setHeaderOffset(0);

    $records = collect($csv->getRecords());
    $header = range(0, 25);

    $byCountry = $records
        ->groupBy('Country/Region')
        ->map(static function ($row) {
            return $row
                ->map(static function ($subrow) {
                    return collect(array_slice($subrow, 4));
                });
        })
        ->map(static function ($row) {
            $keys = $row->first()->keys();
            $ret = [];

            foreach ($keys as $key) {
                $ret[$key] = $row->sum($key);
            }

            return collect($ret)->filter(static function ($value, $key) {
                return $value > 100 && $value < 40000;
            })->take(25)->values();
        })
        ->transform(static function ($item, $key) {
            return [
                'label' => $key . " (".$item->values()->last().")",
                'borderColor' => '#'.substr(md5($key), 0, 6),
                'fill' => false,
                'data' => $item->values(),
                'last' => $item->values()->last(),
            ];
        })
        ->sortByDesc('last')
        ->filter(static function ($value, $key) {
            return count($value['data']) > 0 && $value['data']->last() > 750;
        })
        ->values();

    return [
        'labels' => $header,
        'datasets' => $byCountry,
    ];
});
