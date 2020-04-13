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
    protected $signature = 'fetch:base';

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
        $this->info('Refreshing DB...');
//        $this->call('migrate:fresh');

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
                'population' => $row->popData2018,
            ], [
                'confirmed' => $row->cases,
                'deaths' => $row->deaths,
            ]);

            $bar->advance();
        }

        $bar->finish();
    }
}
