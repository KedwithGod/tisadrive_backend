<?php

namespace App\Http\Controllers\Web\Admin;

use App\Models\User;
use App\Models\Admin\Airport;
use App\Models\Admin\ServiceLocation;
use App\Http\Controllers\ApiController;
use Inertia\Inertia;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use App\Base\Constants\Auth\Role as RoleSlug;
use App\Base\Exceptions\CustomValidationException;
use App\Base\Filters\Master\CommonMasterFilter;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use Grimzy\LaravelMysqlSpatial\Types\Polygon;
use App\Http\Controllers\Api\V1\BaseController;
use Grimzy\LaravelMysqlSpatial\Types\LineString;
use Grimzy\LaravelMysqlSpatial\Types\MultiPolygon;
use App\Models\Request\Request;
use Carbon\Carbon;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Prewk\XmlStringStreamer;
use Prewk\XmlStringStreamer\Stream;
use Prewk\XmlStringStreamer\Parser;

/**
 * @resource Airport
 *
 * Airport CRUD Apis
 */
class AirportController extends BaseController
{
    /**
     * The Airport model instance.
     *
     * @var \App\Models\Admin\Airport
     */
    protected $airport;

    /**
     * AirportController constructor.
     *
     * @param \App\Models\Admin\Airport $airport
     */
    public function __construct(Airport $airport)
    {
        $this->airport = $airport;
    }

    /**
    * Get all Airports
    * @return \Illuminate\Http\JsonResponse
    */
    public function index()
    {
        return inertia('airport/index', ['app_for'=>env('APP_FOR'),]);
        
    }

    public function getAllAirports(QueryFilterContract $queryFilter)
    {
        $query = Airport::companyKey();
        $results = $queryFilter->builder($query)->customFilter(new CommonMasterFilter)->paginate();

        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);
    }

    /**
    * Create Airport view
    */
    public function create()
    {
        $googleMapKey =  get_map_settings('google_map_key'); // Retrieve the Google Map API key

        // dd($requestData);
        $map_type = get_map_settings('map_type');

        if($map_type=="open_street_map")
        {
            return inertia('airport/open-create',[
                'serviceLocations'=> ServiceLocation::active()->get(),
                'default_lat'=>get_settings('default_latitude'),
                'default_lng'=>get_settings('default_longitude'),
            ]);
        }else{
            return inertia('airport/create',[
                'default_lat'=>get_settings('default_latitude'),
                'default_lng'=>get_settings('default_longitude'),
                'googleMapKey' => $googleMapKey, // Pass the Google Map API key to the Vue component
                'serviceLocations'=> ServiceLocation::active()->get(),
            ]);
        }
    }

    /**
     * Get Airport By ID
     * @param id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getById($id)
    {
        $airport = Airport::findOrFail($id);
        $googleMapKey = get_map_settings('google_map_key'); // Retrieve the Google Map API key

        // dd($airport->coordinates);
        $map_type = get_map_settings('map_type');

        if($map_type=="open_street_map")
        {
            return inertia('airport/open-edit',['airport' => $airport,
            'default_lat'=>get_settings('default_latitude'),
            'default_lng'=>get_settings('default_longitude'),
            ]);

        }else{
            return inertia('airport/edit',['airport' => $airport,
            'default_lat'=>get_settings('default_latitude'),
            'default_lng'=>get_settings('default_longitude'),
            'googleMapKey' => $googleMapKey,'app_for'=>env('APP_FOR'),]);

        }
    }

    /**
     * Create Airport.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(HttpRequest $request)
    {
        if(env('APP_FOR') == 'demo'){
            return response()->json([
                'alertMessage' => 'You are not Authorized'
            ], 403);
        }
        // dd($request->all());
        $validated = $request->validate(['name' => 'required']);
        $created_params = $request->only(['service_location_id','airport_surge_fee']);
        $set = [];
        if ($request->coordinates == null) {
            throw ValidationException::withMessages(['name' => __('Please Complete the shape before submit')]);
        }

        // Decode the coordinates JSON string
        $decodedCoordinates = json_decode($request->coordinates, true);

        // Check if the decoding was successful
        if ($decodedCoordinates === null) {
            throw ValidationException::withMessages(['coordinates' => __('Invalid coordinates format')]);
        }

        foreach ($decodedCoordinates as $coordinates) {
            $points = [];
            foreach ($coordinates as $key => $coordinate) {

                // Check if the coordinate is an array with exactly two elements (lng, lat)
                if (is_array($coordinate) && count($coordinate) === 2)
                 {

                    if ($key == 0) {
                        $created_params['lat'] = $coordinate[1];
                        $created_params['lng'] = $coordinate[0];
                    }

                    $point = new Point($coordinate[1], $coordinate[0]); // Point(lat, lng)

                    $check_if_exists = Airport::companyKey()->contains('coordinates', $point)->exists();
                    if ($check_if_exists) {
                        throw ValidationException::withMessages(['airport_name' => __('Coordinates already exists with our exists airport')]);
                    }

                    $points[] = $point;
                } else {
                    throw ValidationException::withMessages(['coordinates' => __('Invalid coordinate data')]);
                }
            }
            // Close the polygon by repeating the first point
            if (count($points) > 0) {
                array_push($points, $points[0]);
            }

            $lineStrings = [new LineString($points)];
            $set[] = new Polygon($lineStrings);
        }

        $multi_polygon = new MultiPolygon($set);


        $created_params['name'] = $request->input('name');
        $created_params['airport_surge_fee'] = $request->input('airport_surge_fee')?? 0;

        $created_params['coordinates'] = $multi_polygon;

        // dd($created_params);
        $created_params['company_key'] = auth()->user()->company_key;

        $airport = $this->airport->create($created_params);

        return response()->json(['airport' => $airport], 201);
    }

    public function list() 
    {
        $results = ServiceLocation::all();
        return response()->json(['results' => $results]);
    }
    /**
    * Create Airport.
    *
    * @return \Illuminate\Http\JsonResponse
    */
    public function update(Airport $airport, HttpRequest $request)
    {
        if(env('APP_FOR') == 'demo'){
            return response()->json([
                'alertMessage' => 'You are not Authorized'
            ], 403);
        }
        // Validate request data
        $validated = $request->validate([
            'coordinates' => 'required', // Ensure coordinates is required and is an array
            'name' => 'required', // Example: Assuming airport_name is required
        ]);

        // Prepare updated parameters
        $updated_params['service_location_id'] = $request->service_location_id;
        
        $set = [];

        if ($request->coordinates == null) {
            throw ValidationException::withMessages(['name' => __('Please Complete the shape before submit')]);
        }

        // Decode the coordinates JSON string
        $decodedCoordinates = json_decode($request->coordinates, true);

        // Check if the decoding was successful
        if ($decodedCoordinates === null) {
            throw ValidationException::withMessages(['coordinates' => __('Invalid coordinates format')]);
        }

        foreach ($decodedCoordinates as $coordinates) {
            $points = [];
            foreach ($coordinates as $key => $coordinate) {

                // Check if the coordinate is an array with exactly two elements (lng, lat)
                if (is_array($coordinate) && count($coordinate) === 2)
                 {

                    if ($key == 0) {
                        $created_params['lat'] = $coordinate[1];
                        $created_params['lng'] = $coordinate[0];
                    }

                    $point = new Point($coordinate[1], $coordinate[0]); // Point(lat, lng)

                    $check_if_exists = Airport::companyKey()->contains('coordinates', $point)->where('id','!=',$airport->id)->exists();
                    if ($check_if_exists) {
                        throw ValidationException::withMessages(['airport_name' => __('Coordinates already exists with our exists airport')]);
                    }

                    $points[] = $point;
                } else {
                    throw ValidationException::withMessages(['coordinates' => __('Invalid coordinate data')]);
                }
            }
            // Close the polygon by repeating the first point
            if (count($points) > 0) {
                array_push($points, $points[0]);
            }

            $lineStrings = [new LineString($points)];
            $set[] = new Polygon($lineStrings);
        }


        // Create a MultiPolygon from the set of polygons
        $multi_polygon = new MultiPolygon($set);

        // Update additional parameters
        $updated_params['name'] = $validated['name'];
        $updated_params['coordinates'] = $multi_polygon;
        
        $airport->update($updated_params);

        // Return a response indicating success
        return response()->json(['airport' => $airport], 200);
    }

    /**
    * Airport map view
    */
    public function airportMapView($id)
    {
        $airport = Airport::findOrFail($id);
        $googleMapKey = get_map_settings('google_map_key'); // Retrieve the Google Map API key

        $map_type = get_map_settings('map_type');

        if($map_type=="open_street_map")
        {

            return inertia('airport/open-map', [
                'airport' => $airport,
            ]);
        }else{
    
            return inertia('airport/map', [
                'airport' => $airport,
                'googleMapKey' => $googleMapKey, // Pass the Google Map API key to the Vue component
            ]);         
        }
    }



    /**
    * Airport Delete
    *
    */
    public function delete(Airport $airport)
    {
        if(env('APP_FOR') == 'demo'){
            return response()->json([
                'alertMessage' => 'You are not Authorized'
            ], 403);
        }
        $airport->delete();

        return response()->json([
            'successMessage' => 'Airport deleted successfully',
        ]);
    }



    public function toggleAirportStatus(Airport $airport,HttpRequest $request)
    {
        if(env('APP_FOR') == 'demo'){
            return response()->json([
                'alertMessage' => 'You are not Authorized'
            ], 403);
        }
        Airport::where('id', $request->id)->update(['active'=> $request->status]);

        return response()->json([
            'successMessage' => 'Airport status updated successfully',
        ]);
    }



}
