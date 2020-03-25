<?php


namespace Iserter\World\Http\Controllers;

use Illuminate\Routing\Controller;
use Iserter\World\Models\Country;
use Iserter\World\Models\Province;
use Spatie\QueryBuilder\QueryBuilder;

class GeoController extends Controller
{
    public function continents()
    {

    }

    public function countries()
    {
        $countries = Country::query()->get();
        return response()->json(compact('countries'));
    }

    public function provinces()
    {
        $provinces = QueryBuilder::for(Province::class)
                ->allowedFilters([

                ])
                ->allowedSorts([

                ])
                ->paginate();
    }

    public function cities()
    {

    }


}
