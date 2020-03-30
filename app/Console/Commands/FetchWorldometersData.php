<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler;

class FetchWorldometersData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Worldometers.info Data';

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
        $htmlURL = 'https://www.worldometers.info/coronavirus/';
        $html = file_get_contents($htmlURL);

        $crawler = new Crawler($html);
        $rows = $crawler
            ->filterXpath('//table/tbody/tr')
            ->each(function (Crawler $node, $i) {
                return $node
                    ->filter('td')
                    ->each(function (Crawler $td, $i) {
                        return trim($td->text());
                    });
            });

        $columns = collect([
            'country',
            'total_cases',
            'new_cases',
            'total_deaths',
            'new_deaths',
            'total_recovered',
            'active_cases',
            'serious_critical',
            'case_per_milion',
            'deaths_per_milion',
            'first_case',
        ]);

        $collection = collect($rows)
            ->map(function ($item) use ($columns) {
                $item = collect($item)->map(function ($value, $key) {
                    if ($key !== 0 && $key !== 10) {
                        return str_replace([',', '+'], ['', ''], $value);
                    }

                    return $value;
                });

                return $columns->combine($item);
            })
            ->slice(0, -1)
            ->reject(function ($value, $key) {
                return $value['total_cases'] < 10000;
            })
        ;

        dd($collection);
    }
}
