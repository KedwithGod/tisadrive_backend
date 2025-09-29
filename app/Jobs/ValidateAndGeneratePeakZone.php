<?php

namespace App\Jobs;

use Illuminate\Mail\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use NotificationChannels\Fcm\Resources\AndroidConfig;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Jobs\Notifications\AndroidPushNotification;
use App\Models\Request\RequestPlace;
use Carbon\Carbon;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Grimzy\LaravelMysqlSpatial\Types\LineString;
use Grimzy\LaravelMysqlSpatial\Types\MultiPolygon;
use Grimzy\LaravelMysqlSpatial\Types\Polygon;
use App\Models\Admin\PeakZone;
use Kreait\Firebase\Contract\Database;
use Sk\Geohash\Geohash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;

class ValidateAndGeneratePeakZone implements ShouldQueue
{
    use Dispatchable,Queueable,InteractsWithQueue,SerializesModels;

    /**
     * The User.
     *
     * @var pick_lat
     */
    protected $pick_lat;
    /**
     * The pick_lng.
     *
     * @var pick_lng
     */
    protected $pick_lng;
    protected $zone_id;

    protected $database;

    protected $timezone;

    /**
     * Create a new job instance.
     *
     * @param $pick_lng,$body,$image,$data
     */
    public function __construct($pick_lat,$pick_lng,$zone_id,$timezone)
    {
        $this->pick_lat = $pick_lat;
        $this->pick_lng = $pick_lng;
        $this->zone_id = $zone_id;
        $this->timezone = $timezone;


    }

      /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $database = App::make(Database::class);


        $search_radius = get_settings('peak_zone_radius');

        $findable_duration = get_settings('peak_zone_history_duration');
        $expiry_duration = get_settings('peak_zone_duration');
        $distance_price_percentage = get_settings('distance_price_percentage');


        $minimum_no_rides = get_settings('peak_zone_ride_count');

        $haversine = "(6371 * acos(cos(radians($this->pick_lat)) * cos(radians(pick_lat)) * cos(radians(pick_lng) - radians($this->pick_lng)) + sin(radians($this->pick_lat)) * sin(radians(pick_lat))))";



        $current_time =  Carbon::now()->format('Y-m-d H:i:s');

        $sub_15_min = Carbon::now()->subMinutes($findable_duration)->format('Y-m-d H:i:s');

        $nearest_rides = RequestPlace::select('request_places.*')->selectRaw("{$haversine} AS distance")->whereRaw("{$haversine} < ?", [$search_radius])->where('created_at', '<=', $current_time)->where('created_at', '>', $sub_15_min)->get();



        $ride_count = $nearest_rides->count();

        if($minimum_no_rides>$ride_count){

            goto end;

        }


        // Create a Peak zone record

        $coordinates = [];

        foreach ($nearest_rides as $key => $nearest_ride) {

            $coordinates[$key]['lat'] = $nearest_ride->pick_lat;
            $coordinates[$key]['lng'] = $nearest_ride->pick_lng;
            $coordinates[$key]['pick_address'] = $nearest_ride->pick_address;

        }

        // Calculate centroid
        $centroidLat = 0;
        $centroidLng = 0;

        foreach ($coordinates as $coord) {
            $centroidLat += $coord['lat'];
            $centroidLng += $coord['lng'];
        }

        $centroidLat /= count($coordinates);
        $centroidLng /= count($coordinates);

        // Find the coordinate closest to the centroid
        $minDistance = PHP_INT_MAX;
        $centerCoord = null;

        foreach ($coordinates as $coord) {
            $distance = sqrt(pow($centroidLat - $coord['lat'], 2) + pow($centroidLng - $coord['lng'], 2));
            if ($distance < $minDistance) {
                $minDistance = $distance;
                $centerCoord = $coord;
            }
        }


        $center_lat = $centerCoord['lat'];
        $center_lng = $centerCoord['lng'];

        $zone_name = $centerCoord['pick_address'];


        // Find if there is any existing peak zone
        $peak_zone = find_peak_zone($center_lat,$center_lng);

        if($peak_zone){

            if($peak_zone->active){
                goto end;
            }else{

        $start_time = Carbon::now()->format('H:i:s');
        $end_time = Carbon::now()->addMinutes($expiry_duration)->format('H:i:s');

        $start_time_timestamp = Carbon::now()->timestamp;
        $end_time_timestamp = Carbon::now()->addMinutes($expiry_duration)->timestamp;

        $peak_zone->active = true;
        $peak_zone->start_time = $start_time;
        $peak_zone->end_time = $end_time;
        $peak_zone->save();

        $database->getReference('peak-zones/'.$peak_zone->id)->update(['active'=>1,'start_time'=>$start_time_timestamp,'end_time'=>$end_time_timestamp,'updated_at'=> Database::SERVER_TIMESTAMP]);

                goto end;
            }
        }

        $numPoints = 5; // Number of coordinates to generate


        $polygonCoordinates = generatePolygonCoordinates($center_lat, $center_lng, $search_radius, $numPoints);

        $points = [];

        // Output the generated coordinates
        foreach ($polygonCoordinates as $key=>$coordinate) {


                    $lineStrings = [];

                    if ($key == 0) {
                            $created_params['lat'] = $coordinate['latitude'];
                            $created_params['lng'] = $coordinate['longitude'];
                    }

                $points []= new Point($coordinate['latitude'], $coordinate['longitude']);
        }

        array_push($points, $points[0]);
        $lineStrings[] = new LineString($points);


        $polygon = new Polygon($lineStrings);

        $set[] = $polygon;


        $multi_polygon = new MultiPolygon($set);

        $created_params['zone_id'] = $this->zone_id;
        // $created_params['coordinates'] = json_encode($wktPolygon);
        $created_params['coordinates'] = $multi_polygon;
        $created_params['unit'] = 1;


        $start_time = Carbon::now()->format('H:i:s');
        $end_time = Carbon::now()->addMinutes($expiry_duration)->format('H:i:s');

        $start_time_timestamp = Carbon::now()->timestamp;
        $end_time_timestamp = Carbon::now()->addMinutes($expiry_duration)->timestamp;

        $created_params['name'] = $zone_name;
        $created_params['start_time'] = $start_time;
        $created_params['end_time'] = $end_time;
        $created_params['distance_price_percentage'] = $distance_price_percentage;

        $zone = PeakZone::create($created_params);

        $g = new Geohash();

        $geohash = $g->encode($created_params['lat'],$created_params['lng'], 12);

        $database->getReference('peak-zones/'.$zone->id)->set(['id'=>$zone->id,'name'=>$zone->name,'active'=>1,'g'=>$geohash,'start_time'=>$start_time_timestamp,'end_time'=>$end_time_timestamp,'coordinates'=>$polygonCoordinates,'updated_at'=> Database::SERVER_TIMESTAMP]);

        end:

    }
}
