<?php

namespace App\Http\Controllers\Web\Admin;

use App\Models\User;
use App\Models\Admin\Zone;
use App\Models\Admin\PeakZone;
use App\Base\Constants\Auth\Role as RoleSlug;
use App\Base\Filters\Master\CommonMasterFilter;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Http\Controllers\Api\V1\BaseController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;
use Inertia\Inertia;

/**
 * @resource Zone
 *
 * Zone CRUD Apis
 */
class PeakZoneController extends BaseController
{
    /**
     * The Zone model instance.
     *
     * @var \App\Models\Admin\PeakZone
     */
    protected $peak_zones;
    protected $pick_lat;
    protected $pick_lng;
    protected $zone_id;

    protected $database;

    protected $timezone;


    /**
     * ZoneController constructor.
     *
     * @param \App\Models\Admin\PeakZone $peak_zones
     */
    public function __construct(PeakZone $peak_zones,Database $database)
    {
        $this->peak_zones = $peak_zones;

        $this->database = $database;

    }

    /**
    * Get all zones
    * @return \Illuminate\Http\JsonResponse
    */
    public function index()
    {
        $zones = Zone::where('active',true)->get();
        return Inertia::render('pages/peak_zone/index',['zones'=>$zones]);
    }

    public function getAllZone(QueryFilterContract $queryFilter)
    {
        $query = PeakZone::companyKey();
        $results = $queryFilter->builder($query)->customFilter(new CommonMasterFilter)->paginate();

        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);
    }
    public function zoneMapView(PeakZone $zone)
    {
        $peak_zone = $zone;
        $peak_zone->zone_name = $zone->zoneDetail->name;

        $coordinates = $zone->coordinates->toArray();

        $multi_polygon = [];

        foreach ($coordinates as $key => $zone) {
            $polygon = [];
            foreach ($zone[0] as $key => $point) {
                $pp = new \stdClass;
                $pp->lat = $point->getLat();
                $pp->lng = $point->getLng();
                $polygon [] = $pp;
            }
            $multi_polygon[] = $polygon;
        }

        $default_lat = $polygon[0]->lat;
        $default_lng = $polygon[0]->lng;

        $zones = json_encode($multi_polygon);

        if(get_map_settings('map_type') == "open_street_map"){
            dd('open');
        }
        
        return Inertia::render('pages/peak_zone/map',[
            'zone' => $zones,
            'peakzone' => $peak_zone,
            'googleMapKey'=> get_map_settings('google_map_key')]);

    }

    public function updateStatus(Request $request, PeakZone $peak_zones,Database $database)
    {
        // dd($request->active);
        $active = $request->active;
        $expiry_duration = get_settings('peak_zone_duration');
        if (!$active) {


            $start_time = Carbon::now()->format('H:i:s');
            $end_time = Carbon::now()->addMinutes($expiry_duration)->format('H:i:s');

            $start_time_timestamp = Carbon::now()->timestamp;
            $end_time_timestamp = Carbon::now()->addMinutes($expiry_duration)->timestamp;

            $peak_zones->active = false;
            $peak_zones->start_time = $start_time;
            $peak_zones->end_time = $end_time;
            $peak_zones->save();

            $database->getReference('peak-zones/'.$peak_zones->id)->update(['active'=>0,'start_time'=>$start_time_timestamp,'end_time'=>$end_time_timestamp,'updated_at'=> Database::SERVER_TIMESTAMP]);

        }
         else {

            $start_time = Carbon::now()->format('H:i:s');
            $end_time = Carbon::now()->addMinutes($expiry_duration)->format('H:i:s');

            $start_time_timestamp = Carbon::now()->timestamp;
            $end_time_timestamp = Carbon::now()->addMinutes($expiry_duration)->timestamp;

            $peak_zones->active = true;
            $peak_zones->start_time = $start_time;
            $peak_zones->end_time = $end_time;
            $peak_zones->save();

            $database->getReference('peak-zones/'.$peak_zones->id)->update(['active'=>1,'start_time'=>$start_time_timestamp,'end_time'=>$end_time_timestamp,'updated_at'=> Database::SERVER_TIMESTAMP]);

        }

        return response()->json([
            'peakzone' => $peak_zones,
        ]);

}}
