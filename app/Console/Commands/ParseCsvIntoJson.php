<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use League\Csv\Reader;

class ParseCsvIntoJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $csv = Reader::createFromPath(base_path() . DIRECTORY_SEPARATOR . 'csse_covid_19_data/csse_covid_19_time_series/time_series_19-covid-Confirmed.csv');
        $csv->setHeaderOffset(0);

        $records = collect($csv->getRecords());
        $byCountry = $records
            ->groupBy('Country/Region')
            ->map(static function ($row) {
                return $row->sum('3/17/20');
            })
        ;

        dd($byCountry);
    }
}
