<?php

namespace App\Console\Commands;

use App\Models\Daily;
use App\Models\Record;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use League\Csv\Statement;

class FetchBaseData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:base {--init} {--skip-refresh}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch base data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->option('init')) {
            $this->info('Refreshing DB...');

            $this->call('migrate:fresh');
        }

        if (! $this->option('skip-refresh')) {
            $this->info('Fetching Data...');
            $sourceJson = file_get_contents('https://opendata.ecdc.europa.eu/covid19/casedistribution/json/');
            $source = json_decode($sourceJson);

            $this->info('');
            $this->info('Importing data...');
            $bar = $this->output->createProgressBar(count($source->records));
            $bar->start();

            foreach ($source->records as $row) {
                Record::updateOrCreate([
                    'capture_date' => Carbon::createFromFormat('d/m/Y', $row->dateRep)->toDateString(),
                    'country' => $row->countriesAndTerritories,
                    'population' => $row->popData2018 === "" ? 0 : $row->popData2018,
                ], [
                    'confirmed' => $row->cases,
                    'deaths' => $row->deaths,
                ]);

                $bar->advance();
            }

            $bar->finish();
        }

        ////// COMPUTE SUMS
        // fetch all data
        $records = Record::where([
            ['population', '>', 0]
        ])
        ->orderBy('country', 'ASC')
        ->orderBy('capture_date', 'ASC')
        ->get();

        $this->info('');
        $this->info('Crunch numbers...');
        $bar = $this->output->createProgressBar(count($records));
        $bar->start();

        $recordsByCountry = $records->groupBy('country');
        foreach ($recordsByCountry as $country) {
            $deaths = $country->pluck('deaths');
            $confirmed = $country->pluck('confirmed');

            $carry = [
                'country' => '',
                'totals_confirmed' => 0,
                'avg3_confirmed' => 0,
                'avg7_confirmed' => 0,
                'totals_deaths' => 0,
                'avg3_deaths' => 0,
                'avg7_deaths' => 0,
                'confirmed_million' => 0,
                'deaths_million' => 0,
            ];

            foreach ($country as $k => $record) {
                $avg3_confirmed = $avg3_deaths = $avg7_confirmed = $avg7_deaths = 0;
                if ($k >= 1) {
                    $avg3_confirmed = $confirmed->slice($k-1, 3)->avg();
                    $avg3_deaths = $deaths->slice($k-1, 3)->avg();
                }
                if ($k >= 3) {
                    $avg7_confirmed = $confirmed->slice($k-3, 7)->avg();
                    $avg7_deaths = $deaths->slice($k-3, 7)->avg();
                }

                // update $carry
                $carry = [
                    'country' => $record->country,
                    'totals_confirmed' => $carry['totals_confirmed'] + $record->confirmed,
                    'avg3_confirmed' => number_format($avg3_confirmed, 0, '', ''),
                    'avg7_confirmed' => number_format($avg7_confirmed, 0, '', ''),
                    'totals_deaths' => $carry['totals_deaths'] + $record->deaths,
                    'avg3_deaths' => number_format($avg3_deaths, 0, '', ''),
                    'avg7_deaths' => number_format($avg7_deaths, 0, '', ''),
                    'confirmed_million' => ($carry['confirmed_million'] + $record->confirmed) * (1000000 / $record->population),
                    'deaths_million' => ($carry['deaths_million'] + $record->deaths) * (1000000 / $record->population),
                ];

                // write $carry
                $record->fill($carry);
                $record->save();

                $bar->advance();
            }
        }

        $bar->finish();
        $this->info('');
    }
}
