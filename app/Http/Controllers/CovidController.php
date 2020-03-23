<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;

class CovidController extends Controller
{
    public function dataJson(Request $request) {
        $data = $request->all();

        $url = "https://raw.githubusercontent.com/CSSEGISandData/COVID-19/master/csse_covid_19_data/csse_covid_19_time_series/time_series_19-covid-".$data['type'].".csv";
        $contents = file_get_contents($url);
        $name = substr($url, strrpos($url, '/') + 1);
        Storage::put($name, $contents);

        $csv = Reader::createFromPath(storage_path('app') . DIRECTORY_SEPARATOR . $name);
        $csv->setHeaderOffset(0);

        $records = collect($csv->getRecords());
        $header = range(0, $data['days']);

        $byCountry = $records
            ->groupBy('Country/Region')
            ->map(static function ($row) {
                // cut off meta data
                return $row
                    ->map(static function ($subrow) {
                        return collect(array_slice($subrow, 4));
                    });
            });

        $result = $byCountry->pipe(static function ($collection) use ($data) {
            return $collection
                ->map(static function ($row) use ($data) {
                    $keys = $row->first()->keys();
                    $ret = [];

                    foreach ($keys as $k => $key) {
                        $ret[$key] = $row->sum(static function ($val) use ($key) {
                            return $val[$key] === "" ? 0 : $val[$key];
                        });
                    }

                    return collect($ret)->filter(static function ($value) use ($data) {
                        return $value > (int) $data['start_from'] && $value < (int) $data['end_at'];
                    })
                        ->take($data['days']);
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
                ->filter(static function ($value, $key) use ($data) {
                    return count($value['data']) > 0 && $value['data']->last() > $data['cut_off'];
                })
                ->values()
            ;
        });

        $date = $byCountry->first()->first()->keys()->last();

        return [
            'labels' => $header,
            'datasets' => $result,
            'date' => date('Y-m-d', strtotime($date)),
        ];
    }
}
