<?php

namespace App\Http\Controllers;

use App\Models\Daily;
use App\Models\Record;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Csv\Reader;

class CovidController extends Controller
{
    public function dataJson(Request $request)
    {
        $data = $request->all();

        $records = Record::orderBy('capture_date', 'ASC');
        if (isset($data['country'])) {
            $records->where('country', '=', $data['country']);
        }
        $records = $records
            ->get()
            ->groupBy('country');

        $last_date = $records->first()->last()['capture_date'];

        if ($data['base'] === 'totals') {
            if ($data['mode'] === 'normal') {
                if ($data['type'] === 'confirmed') {
                    $records = $records
                        ->map(static function ($record) use ($data) {
                            return $record->last()['totals_confirmed'];
                        });
                }

                if ($data['type'] === 'deaths') {
                    $records = $records
                        ->map(static function ($record) use ($data) {
                            return $record->last()['totals_deaths'];
                        });
                }

                $records = $records
                    ->sortDesc()
                    ->filter(function ($value) use ($data) {
                        return $value > $data['current_over'];
                    });
            } elseif ($data['mode'] === 'million') {
                $records = $records
                    ->reject(static function ($item) use ($data) {
                        $sum = $item->sum($data['type']);
                        return $sum === 0 ||
                            $sum < (int)$data['current_over'];
                    })
                    ->reject(static function ($item) {
                        return $item->first()->population == 0 ||
                            $item->first()->population == "" ||
                            Str::startsWith($item->first()->country, 'Cases');
                    })
                    ->map(static function ($record) use ($data) {
                        return [
                            $data['type'] => (int)(($record->sum($data['type']) * 1000 * 1000) / (int)$record->first()->population),
                            'max_value' => $record->sum($data['type'])
                        ];
                    })
                    ->sortDesc();
            }
            $x = $records->keys();
            if ($data['mode'] === 'normal') {
                $y = $records->values();
            } else {
                $y = $records->values()->pluck($data['type']);
            }

            $records = collect([
                [
                    'x' => $x,
                    'y' => $y,
                    'type' => 'bar',
                    'marker' => [
                        'color' => $records->keys()->map(static function ($value) {
                            return '#' . substr(md5($value), 0, 6);
                        })
                    ]
                ]
            ]);
        } elseif ($data['base'] === 'record') {
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

            $records = $records
                ->sortByDesc('max_value')
                ->except(['max_value'])
                ->values();
        } elseif ($data['base'] === 'daily') {
            $records = $records
                ->map(static function ($row) use ($data) {
                    $values = $row
                        ->mapWithKeys(static function ($item) use ($data) {
                            return [$item['capture_date'] => (int)$item[$data['type']]];
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
                            'type' => 'scatter',
                            'mode' => 'lines+markers',
                            'name' => $row->first()['country'],
                            'max_value' => (int)$row->max($data['type'])
                        ];
                })
                ->reject(static function ($item) use ($data) {
                    return $item['max_value'] < (int)$data['current_over'];
                });

            $records = $records
                ->sortByDesc('max_value')
                ->except(['max_value'])
                ->values();

        }

        return [
            'data_sets' => $records->toArray(),
            'date' => $last_date,
        ];
    }
}
