<?php

namespace App\Http\Controllers\Web\Common;

use App\Models\Country;
use App\Http\Controllers\ApiController;
use App\Transformers\CountryTransformer;
use App\Models\TimeZone;
use App\Transformers\TimezoneTransformer;
use Inertia\Inertia;
use App\Base\Filters\Admin\UserFilter;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Http\Requests\Country\CreateCountryRequest;
use App\Base\Services\ImageUploader\ImageUploaderContract;

/**
 * @group Web-Spa-Countries&Timezones
 *
 * APIs for User-Management
 */
class CountryController extends ApiController
{

    protected $imageUploader;

    /**
     * CountryController constructor.
     *
     * @param \App\Models\Admin\ImageUploaderContract $imageUploader
     */

     public function __construct(ImageUploaderContract $imageUploader)
     {
         $this->imageUploader = $imageUploader;
     }
 
     /**
     * Country View
     * 
     * */
    public function index()
    {
        return Inertia::render('pages/countries/index');

    }

    /**
     * Get all the countries.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(QueryFilterContract $queryFilter)
    {
        $query = Country::query();

        $results = $queryFilter->builder($query)->customFilter(new UserFilter)->paginate();

        return response()->json([
            'items' => $results->items(),
            'paginator' => $results,
        ]);

        // return $this->respondOk($countries);
    }

    public function create()
    {
        return Inertia::render('pages/countries/create');
    }


    public function store(CreateCountryRequest $request)
    {
        $created_params = $request->only([
            'name',
            'dial_code',
            'code',
            'currency_name',
            'currency_code',
            'currency_symbol',
            'dial_min_length',
            'dial_max_length'
        ]);
        $created_params['active'] = 1;


        if ($uploadedFile = $this->getValidatedUpload('flag', $request)) {
            $created_params['flag'] = $this->imageUploader->file($uploadedFile)
                ->saveCountryFlagImage();
        }
        $country = Country::create($created_params);

        $message = trans('succes_messages.country_added_succesfully');


        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Service location created successfully.',
            'country' => $country,
        ], 201);
    }


    public function edit(Country $country)
    {

        $timeZones = TimeZone::get();

        return Inertia::render(
            'pages/countries/create',
            ['country' => $country, 'app_for'=>env('APP_FOR'),]
        );
    }


    public function update(CreateCountryRequest $request, Country $country)
    {

        $updated_params = $request->only([
            'name',
            'dial_code',
            'code',
            'currency_name',
            'currency_code',
            'currency_symbol',
            'dial_min_length',
            'dial_max_length'
        ]);


        if ($uploadedFile = $this->getValidatedUpload('flag', $request)) {
            $updated_params['flag'] = $this->imageUploader->file($uploadedFile)
                ->saveCountryFlagImage();
        }
        $country->update($updated_params);

        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Service location updated successfully.',
            'country' => $country,
        ], 201);
    }

    public function toggleStatus(Country $country)
    {
        $status = $country->isActive() ? false: true;
        $country->update(['active' => $status]);

        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Service location created successfully.',
            'country' => $country,
        ], 201);
    }

    /**
     * Get all the timezone.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function timezones()
    {
        $timezonesQuery = TimeZone::active();

        $timezones = filter($timezonesQuery, new TimezoneTransformer)->defaultSort('name')->get();

        return $this->respondOk($timezones);
    }


}
