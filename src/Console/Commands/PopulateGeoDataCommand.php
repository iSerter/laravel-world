<?php

namespace Iserter\World\Console\Commands;

use Illuminate\Console\Command;
use Iserter\World\Models\Continent;
use Iserter\World\Models\Country;
use Iserter\World\Models\Province;

class PopulateGeoDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'world:geo:populate';

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
        if(Country::query()->count() !== 0) {
            $this->error('there are country records');
            return;
        }

        if(Continent::query()->count() === 0) {
            $this->createContinents();
        }

        if(Country::query()->count() === 0) {
            $this->createCountries();
        }

        if(Province::query()->count() === 0) {
            $this->createProvinces();
        }

        $this->updateCountriesData();

        $this->comment('Populated GEO data');
    }


    protected function createContinents()
    {
        $continents = json_decode(file_get_contents(__DIR__ . '/../../../database/data/geography/continents.json'), 1);
        foreach ($continents as $k => $v) {
            $c = new Continent();
            $c->code = $k;
            $c->name = $v;
            $c->save();
            $this->info($v);
        }
    }


    private function createCountries()
    {
        $countries = json_decode(file_get_contents(__DIR__ . '/../../../database/data/geography/countries.json'), 1);
        foreach ($countries as $k => $v) {
            $c = new Country();
            $c->code = $k;
            $c->name = $v['name'];
            $c->native_name = $v['native'];
            $c->emoji = $v['emoji'];
            $c->continent_code = $v['continent'];
            $c->currency_code = $v['currency'];
            $c->phone_code = $v['phone'];
            $c->save();
            $this->info($c->name);
        }
    }

    private function createProvinces()
    {
        $provinces = json_decode(file_get_contents(__DIR__ . '/../../../database/data/geography/provinces.json'), 1);
        foreach ($provinces as $k => $v) {
            $p = new Province();
            $p->country_code = $v['country'];
            $p->name = isset($v['english']) ? $v['english'] : $v['name'];
            $p->native_name = isset($v['english']) ? $v['name'] : null;
            $p->short_name = isset($v['short']) ? $v['short'] : null;
            $p->region = isset($v['region']) ? $v['region'] : null;
            $p->save();
        }
    }

    private function updateCountriesData()
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', 'https://restcountries.eu/rest/v2/all');

        $data = json_decode($response->getBody());
        foreach ($data as $c) {
            $country = Country::query()->where('code', $c->alpha2Code)->first();
            if (!$country) {
                $country = new Country(['code' => $c->alpha2Code]);
            }
            $country->name = $c->name;
            $country->code_alpha3 = $c->alpha3Code;

            if (!empty($c->currencies)) {
                $country->currency_code = $c->currencies[0]->code;
                $country->currency_name = $c->currencies[0]->name;
                if ($c->currencies[0]->symbol) {
                    $country->currency_symbol = $c->currencies[0]->symbol;
                }
            }

            $country->save();
        }
    }
}
