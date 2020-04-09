<?php

namespace App\Http\Controllers;

use App\Models\Daily;
use App\Models\Record;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;

class CovidController extends Controller
{
    public function dataJson(Request $request)
    {
        $data = $request->all();

        $records = Record::orderBy('capture_date', 'ASC')
            ->get()
            ->groupBy('country');

        $last_date = $records->first()->last()['capture_date'];

        if ($data['base'] === 'record') {
            $records = $records
                ->map(static function ($record) use ($data) {
                    $country = $record->first()['country'];

                    $keys = [
                        'confirmed' => 0,
                        'deaths' => 0,
                    ];
                    $record
                        ->each(static function ($item, $k) use (&$keys, $data) {
                            foreach (array_keys($keys) as $key) {
                                $keys[$key] = $item[$key] + $keys[$key];
                                $item[$key] = $keys[$key];
                            }
                        });

                    if ($data['mode'] === 'normal') {
                        $x = $record
                            ->pluck($data['type'])
                            ->values();
                        $y = $record
                            ->pluck('capture_date')
                            ->values();
                    } elseif ($data['mode'] === 'reset') {
                        $x = $record
                            ->pluck($data['type'])
                            ->reject(static function ($val) use ($data) {
                                return $val < $data['start_from'];
                            })
                            ->values()
                            ->take(date('z') - 50);

                        if (isset($data['return']) && $data['return'] === 'r0') {
                            $x = $x
                                ->map(static function ($item, $key) {
                                    return $item === 0 ? 0 : log($item) / ($key + 1) * 100;
                                })
                                ->reject(static function ($val) {
                                    return $val < 0.1;
                                })
                                ->map(static function ($item) {
                                    return number_format($item, 2);
                                })
                                ->values();
                        }

                        $y = $x
                            ->keys();
                    }

                    return [
                        'y' => $x,
                        'x' => $y,
                        'mode' => 'lines',
                        'name' => $country,
                        'line' => [
                            'color' => '#' . substr(md5($country), 0, 6),
                        ],
                        'width' => 1,
                        'connectgaps' => 'true',
                        'max_value' => $record->last()[$data['type']]
                    ];
                })
                ->reject(static function ($item) use ($data) {
                    return $item['max_value'] < (int)$data['current_over'];
                });
        }
        elseif ($data['base'] === 'daily') {
            $records = $records
                ->map(static function ($row) use ($data) {
                    $country = $row->first()['country'];
                    $max_value = (int)$row->last()[$data['type']];
                    $values = $row
                        ->mapWithKeys(static function ($item) use ($data) {
                            return [$item['capture_date'] => (int) $item[$data['type']]];
                        });

                    $cnt = 0;
                    $values->each(static function ($item, $key) use ($data, &$cnt) {
                        if ($item > $data['start_from']) {
                            $cnt = $key;
                            return false;
                        }
                    });
                    $values = $values->splice($values->keys()->search($cnt));

                    $y = $values->values();
                    $options = [];
                    if ($data['mode'] === 'reset') {
                        $x = $y->keys();
                    } else {
                        $x = $values->keys();
                    }

                    return $options + [
                        'x' => $x,
                        'y' => $y,
                        'type' => $data['mode'] === 'normal' ? 'scatter' : 'bar',
                        'name' => $country,
                        'max_value' => $max_value
                    ];
                })
                ->reject(static function ($item) use ($data) {
                    return $item['max_value'] < (int)$data['current_over'];
                });
        }

        $records = $records
            ->sortByDesc('max_value')
            ->except(['max_value'])
            ->values();

        return [
            'data_sets' => $records->toArray(),
            'date' => $last_date,
        ];
    }
}
