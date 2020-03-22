<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;

class CovidController extends Controller
{
    public function byTotalCases() {
        $url = "https://raw.githubusercontent.com/CSSEGISandData/COVID-19/master/csse_covid_19_data/csse_covid_19_time_series/time_series_19-covid-Confirmed.csv";
        $contents = file_get_contents($url);
        $name = substr($url, strrpos($url, '/') + 1);
        Storage::put($name, $contents);

        $csv = Reader::createFromPath(storage_path('app') . DIRECTORY_SEPARATOR . $name);
        $csv->setHeaderOffset(0);

        $days = 35;
        $startFrom = 1000;
        $endAt = 80000;
        $records = collect($csv->getRecords());
        $header = range(0, $days);

        $byCountry = $records
            ->groupBy('Country/Region')
            ->map(static function ($row) {
                // cut off meta data
                return $row
                    ->map(static function ($subrow) {
                        return collect(array_slice($subrow, 4));
                    });
            })
            ->map(static function ($row) use ($days, $startFrom, $endAt) {
                $keys = $row->first()->keys();
                $ret = [];

                foreach ($keys as $key) {
                    $ret[$key] = $row->sum(static function ($val) use ($key) {
                        return $val[$key] === "" ? 0 : $val[$key];
                    });
                }

                return collect($ret)->filter(static function ($value, $key) use ($startFrom, $endAt) {
                    return $value > $startFrom && $value < $endAt;
                })
                    ->take($days);
            })
            ->filter()
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
                return count($value['data']) > 0 && $value['data']->last() > 350;
            })
            ->values()
        ;

        return [
            'labels' => $header,
            'datasets' => $byCountry,
        ];
    }

    public function byDeaths() {
        $url = "https://raw.githubusercontent.com/CSSEGISandData/COVID-19/master/csse_covid_19_data/csse_covid_19_time_series/time_series_19-covid-Deaths.csv";
        $contents = file_get_contents($url);
        $name = substr($url, strrpos($url, '/') + 1);
        Storage::put($name, $contents);

        $csv = Reader::createFromPath(storage_path('app') . DIRECTORY_SEPARATOR . $name);
        $csv->setHeaderOffset(0);

        $days = 35;
        $startFrom = 1;
        $endAt = 6000;
        $records = collect($csv->getRecords());
        $header = range(0, $days);

        $byCountry = $records
            ->groupBy('Country/Region')
            ->map(static function ($row) {
                // cut off meta data
                return $row
                    ->map(static function ($subrow) {
                        return collect(array_slice($subrow, 4));
                    });
            })
            ->map(static function ($row) use ($days, $startFrom, $endAt) {
                $keys = $row->first()->keys();
                $ret = [];

                foreach ($keys as $key) {
                    $ret[$key] = $row->sum(static function ($val) use ($key) {
                        return $val[$key] === "" ? 0 : $val[$key];
                    });
                }

                return collect($ret)->filter(static function ($value, $key) use ($startFrom, $endAt) {
                    return $value > $startFrom && $value < $endAt;
                })
                    ->take($days);
            })
            ->filter()
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
                return count($value['data']) > 0 && $value['data']->last() > 10;
            })
            ->values()
        ;

        return [
            'labels' => $header,
            'datasets' => $byCountry,
        ];
    }
}
