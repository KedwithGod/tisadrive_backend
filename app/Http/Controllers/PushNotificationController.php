<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\User;
use App\Models\Admin\Zone;
use App\Models\Admin\Driver;
use App\Models\Admin\Owner;
use App\Base\Constants\Auth\Role;
use App\Models\Admin\Notification;
use App\Models\Admin\ServiceLocation;
use App\Base\Constants\Masters\PushEnums;
use App\Jobs\UserDriverNotificationSaveJob;
use App\Http\Controllers\Web\BaseController;
use App\Jobs\Notifications\SendPushNotification;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Base\Services\ImageUploader\ImageUploaderContract;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class PushNotificationController extends BaseController
{
    protected $notification;

    protected $imageUploader;
    /**
     * NotificationController constructor.
     *
     * @param \App\Models\Admin\Notification $notification
     */
    public function __construct(Notification $notification, ImageUploaderContract $imageUploader)
    {
        $this->notification = $notification;
        $this->imageUploader = $imageUploader;
    }

    public function index() 
    {
        return Inertia::render('pages/push_notifications/index');
    }

    public function create() 
    {
        $services = ServiceLocation::whereActive(true)->get();
        return Inertia::render('pages/push_notifications/create',['serviceLocations'=>$services]);
    }

    public function fetch(QueryFilterContract $queryFilter)
    {
        $query = $this->notification->query();
        $results = $queryFilter->builder($query)->paginate();


        return response()->json([
            'successMessage' => 'Notification Listed successfully.',
            'results' => $results,
        ], 201);
    }

    public function edit(notification $notification) {
        $services = ServiceLocation::whereActive(true)->get();
        return Inertia::render('pages/push_notifications/create',[
            'serviceLocations'=>$services,
            'notification'=>$notification,
        ]);
    }

    public function delete(notification $notification) {
        $notification->delete();
        return response()->json([
            'successMessage' => 'Notification Deleted successfully.',
        ], 201);
    }

    public function sendPush(Request $request)
    {
        $created_params = $request->only(['title']);
        $created_params['push_enum'] = PushEnums::GENERAL_NOTIFICATION;
        $created_params['body'] = $request->message;
        $created_params['url'] = $request->url;
        
        // Check if image is provided
        if ($uploadedFile = $request->file('image')) {
            $created_params['image'] = $this->imageUploader->file($uploadedFile)
                ->savePushImage();
        }
        if($request->send_to == 'user'){
            $created_params['for_user'] = true;
        }elseif($request->send_to == 'driver'){
            $created_params['for_driver'] = true;
        }elseif($request->send_to == 'owner'){
            $created_params['for_owner'] = true;
        }
        
        $notification = $this->notification->create($created_params);
        
        $title = $notification->title;
        $body = $notification->body;
        $image=$notification->image;


        $serviceLocationId = $request->service_location_id;
        $zones = Zone::where('service_location_id', $serviceLocationId)->get();
        

        switch ($request->send_to) {
            case 'user':
                    $perPage = 100;
                    $page = 1;
                    do {
                        $users = User::select('users.*')
                        ->whereNotNull('users.fcm_token')
                        ->where('service_location_id', $serviceLocationId)
                        ->belongsToRole(Role::USER)
                        ->skip(($page - 1) * $perPage)
                        ->take($perPage)
                        ->get();
                    

                        $userIds = $users->pluck('id');



                        foreach ($users as $user) {
                            dispatch(new SendPushNotification($user, $title, $body));
                        }
                        // Dispatch job to save notification details
                        dispatch(new UserDriverNotificationSaveJob($userIds, null, null, $notification));

                        $page++;
                    } while (!$users->isEmpty());
                
                break;
            case 'driver':

                    $perPage = 100;
                    $page = 1;
                    do {
                        $drivers = Driver::where('service_location_id', $serviceLocationId)
                            ->whereHas('user', function ($query) {
                                $query->whereNotNull('fcm_token');
                            })
                            ->skip(($page - 1) * $perPage)
                            ->take($perPage)
                            ->get();

                            // dd($drivers);
                
                        $driverIds = $drivers->pluck('id');
                
                        foreach ($drivers as $driver) {
                            // dd($driver);
                            dispatch(new SendPushNotification($driver->user, $title, $body));
                        }
                        // Dispatch job to save notification details
                        dispatch(new UserDriverNotificationSaveJob(null, $driverIds, null, $notification));
                        $page++;
                    } while ($drivers->count() > 0);
                
                break;

                case 'owner':

                        $perPage = 100;
                        $page = 1;
                        do {
                            $owners = Owner::where('service_location_id', $serviceLocationId)
                                ->whereHas('user', function ($query) {
                                    $query->whereNotNull('fcm_token');
                                })
                                ->skip(($page - 1) * $perPage)
                                ->take($perPage)
                                ->get();
    
                                // dd($owners);
                    
                            $ownerIds = $owners->pluck('id');
                    
                            foreach ($owners as $owner) {
                                // dd($driver);
                                dispatch(new SendPushNotification($owner->user, $title, $body));
                            }
                            // Dispatch job to save notification details
                            dispatch(new UserDriverNotificationSaveJob(null, null, $ownerIds, $notification));
                            $page++;
                        } while ($owners->count() > 0);
                    
                    break;
        }

        return response()->json([
            'successMessage' => 'Notification Sent successfully.',
        ], 201);
    }

}
