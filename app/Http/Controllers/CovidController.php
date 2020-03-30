<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;

class CovidController extends Controller
{
    public function dataJson(Request $request) {
        $data = $request->all();

        $type = [
            'cases' => 'confirmed',
            'world' => 'confirmed',
            'deaths' => 'deaths',
        ];

        $url = "https://raw.githubusercontent.com/CSSEGISandData/COVID-19/master/csse_covid_19_data/csse_covid_19_time_series/time_series_covid19_".$type[$data['type']]."_global.csv";
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
                    ->map(static function ($subRow) {
                        return collect(array_slice($subRow, 4));
                    });
            });

        if ($data['type'] === 'world') {
            $totals = $byCountry->pipe(static function ($collection) {
                return $collection
                    ->map(static function ($row) {
                        $keys = $row->first()->keys();
                        $ret = [];

                        foreach ($keys as $k => $key) {
                            $ret[$key] = $row->sum(static function ($val) use ($key) {
                                return $val[$key] === "" ? 0 : $val[$key];
                            });
                        }

                        return collect($ret);
                    });
            });
            $header = $totals->first()->keys();
            $col = [];
            foreach ($header as $k => $key) {
                $col[] = [
                    'x' => $key,
                    'y' => $totals->sum(static function ($val) use ($key) {
                        return $val[$key] === "" ? 0 : $val[$key];
                    })
                ];
            }
            $result[] = [
                'borderColor' => '#ff0000',
                'fill' => false,
                'data' => collect($col)
            ];
        } else {
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
//                    ->filter(static function ($item, $key) {
//                        return in_array($key, ['US', 'China', 'Italy', 'Spain', 'Germany', 'France', 'Iran', 'Korea, South', 'Japan', 'Romania']);
//                    })
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
        }

        $date = $byCountry->first()->first()->keys()->last();

        return [
            'labels' => $header,
            'datasets' => $result,
            'date' => date('Y-m-d', strtotime($date)),
        ];
    }
}
