<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Country;
use App\Models\TimeZone;
use App\Models\Admin\ServiceLocation;
use Illuminate\Support\Facades\Log;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Base\Filters\Admin\UserFilter;
use App\Base\Filters\Admin\ServiceLocationFilter;
use App\Models\Admin\ServiceLocationTranslation;
use Pest\Plugins\Parallel\Handlers\Laravel;
use App\Models\Admin\AdminDetail;
use App\Models\User;

class ServiceLocationController extends Controller
{
    //
    public function index()
    {
        $countries = Country::whereActive(true)->get();
        $timeZones = TimeZone::get();
        return Inertia::render('pages/service_locations/index', ['countries' => $countries, 'timeZones' => $timeZones,'app_for'=>env('APP_FOR'),]);
    }


    // List of service locations
    public function list(QueryFilterContract $queryFilter)
    {
        $query = ServiceLocation::with('serviceLocationTranslationWords');

        $results = $queryFilter->builder($query)->customFilter(new ServiceLocationFilter)->paginate();

        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);
    }
    public function create()
    {

        $countries = Country::whereActive(true)->get();
        $timeZones = TimeZone::get();

        return Inertia::render(
            'pages/service_locations/create',
            ['countries' => $countries, 'timeZones' => $timeZones, 'serviceLocation' => null]
        );
    }

    public function edit($id)
    {

        $serviceLocation = ServiceLocation::find($id);
        foreach ($serviceLocation->serviceLocationTranslationWords as $language) {
            $languageFields[$language->locale]  = $language->name;
        }

        $serviceLocation->languageFields = $languageFields ?? null;
        // dd($serviceLocation);

        $countries = Country::whereActive(true)->get();
        $timeZones = TimeZone::get();

        return Inertia::render(
            'pages/service_locations/create',
            ['countries' => $countries, 'timeZones' => $timeZones, 'serviceLocation' => $serviceLocation,'app_for'=>env('APP_FOR'),]
        );
    }

    public function toggle(ServiceLocation $location)
    {
        if(env('APP_FOR') == 'demo'){
            return response()->json([
                'alertMessage' => 'You are not Authorized'
            ], 403);
        }
        $toggle['active'] = !$location->active;
        $location->update($toggle);
        $serviceLocation = $location->fresh();

        return response()->json([
            'successMessage' => 'Service location updated successfully.',
            'serviceLocation' => $serviceLocation,
        ], 201);
    }

    public function store(Request $request)
    {
        
        if(env('APP_FOR') == 'demo'){
            return response()->json([
                'alertMessage' => 'You are not Authorized'
            ], 403);
        }
        // Validate incoming request
        $validated = $request->validate([
            'country' => 'required',
            'currency_code' => 'required',
            'currency_symbol' => 'required',
            'currency_pointer' => 'required',
            'timezone' => 'required',
            'languageFields' => 'required|array',

        ]);
        $validated['currency_name'] = $request->currency_name;

        $validated['name'] = $validated['languageFields']['en'];
        $validated['active'] =true;

        $translations_data = [];

        $first_area = ServiceLocation::first();
        if(!$first_area){
            $user = auth()->user();
            $user->timezone = $validated['timezone'];
        }
        $serviceLocation = ServiceLocation::create($validated);

        foreach ($validated['languageFields'] as $code => $language) {

            $translationData[] = ['name' => $language, 'locale' => $code, 'service_location_id' => $serviceLocation->id];

            $translations_data[$code] = new \stdClass();
            $translations_data[$code]->locale = $code;
            $translations_data[$code]->name = $language;
        }

        $serviceLocation->serviceLocationTranslationWords()->insert($translationData);

        $serviceLocation->translation_dataset = json_encode($translations_data);

        $serviceLocation->save();

        // Query to find users with "Super Admin" role and a null timezone
        $super_admins = User::whereNull('timezone')
            ->whereHas('roles', function ($query) {
                $query->where('name', 'Super Admin'); // Adjust 'name' if your role field differs
            })
            ->get();

        // Check if any super admins exist
        if ($super_admins->isNotEmpty()) {
            $location = ServiceLocation::first();

            if ($location) { // Ensure there's at least one service location
                foreach ($super_admins as $super_admin) {
                    $super_admin->update(['timezone' => $location->timezone]);
                }
            }
        }
        

        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Service location created successfully.',
            'serviceLocation' => $serviceLocation,
        ], 201);
    }
    public function update(Request $request, ServiceLocation $location)
    {
        if(env('APP_FOR') == 'demo'){
            return response()->json([
                'alertMessage' => 'You are not Authorized'
            ], 403);
        }

        $validated = $request->validate([
            'country' => 'required',
            'currency_code' => 'required',
            'currency_symbol' => 'required',
            'currency_pointer' => 'required',
            'timezone' => 'required',
            'languageFields' => 'required|array',
        ]);

        $validated['currency_name'] = $request->currency_name;


        foreach ($validated['languageFields'] as $locale => $name) {

            if ($locale == "en")
                $validated['name'] = $name;

            $location->serviceLocationTranslationWords()->delete();

            if (!is_null($name)) {

                $translationData[] = ['name' => $name, 'locale' => $locale, 'service_location_id' => $location->id];

                $translations_data[$locale] = new \stdClass();
                $translations_data[$locale]->locale = $locale;
                $translations_data[$locale]->name = $name;
            }
        }

        $location->serviceLocationTranslationWords()->insert($translationData);

        $location['translation_dataset'] = json_encode($translations_data);

        $location->update($validated);

        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Service location updated successfully.',
            'serviceLocation' => $location,
        ], 201);
    }
    public function delete(ServiceLocation $location){
        if(env('APP_FOR') == 'demo'){
            return response()->json([
                'alertMessage' => 'You are not Authorized'
            ], 403);
        }
        ServiceLocationTranslation::where('service_location_id',$location->id)->delete();
        $location->delete();
        return response()->json(['successMessage' => 'Service Location deleted successfully']);
    }
}
