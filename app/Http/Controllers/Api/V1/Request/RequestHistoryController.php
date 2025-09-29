<?php

namespace App\Http\Controllers\Api\V1\Request;

use App\Models\Admin\Driver;
use App\Base\Constants\Auth\Role;
use App\Base\Filters\Admin\RequestFilter;
use App\Http\Controllers\Api\V1\BaseController;
use App\Models\Request\Request as RequestModel;
use App\Transformers\Requests\TripRequestTransformer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Admin\Setting;
use App\Models\ThirdPartySetting;
use Carbon\Carbon;
use App\Jobs\Mails\SendUserInvoiceMailNotification;
use App\Jobs\Mails\SendDriverInvoiceMailNotification;

/**
 * @group Request-Histories
 *
 * APIs request history list & history by request id
 */
class RequestHistoryController extends BaseController
{
    protected $request;

    public function __construct(RequestModel $request)
    {
        $this->request = $request;
    }

    /**
    * Request History List
    * @return \Illuminate\Http\JsonResponse
    * @responseFile responses/requests/history-list.json
    * @responseFile responses/requests/driver-history-list.json
    */
    public function index()
    {
        $query = $this->request->orderBy('created_at', 'desc');
        $includes=['driverDetail','requestBill','userDetail'];

        if (access()->hasRole(Role::DRIVER)) {
            $driver_id = Driver::where('user_id', auth()->user()->id)->pluck('id')->first();
            $query = $this->request->where('driver_id', $driver_id)->orderBy('created_at', 'desc');
            $includes = ['userDetail','requestBill'];
        }
        if (access()->hasRole(Role::OWNER)) {
            $owner_id = auth()->user()->owner->id;
            $query = $this->request->where('owner_id', $owner_id)->orderBy('created_at', 'desc');
            $includes = ['userDetail','requestBill','driverDetail'];
        }
        if (access()->hasRole(Role::USER)) {


            $query = $this->request->where('user_id', auth()->user()->id)->orderBy('created_at', 'desc');

            if(request()->has('on_trip') && request()->on_trip==0){

                $query = $query->where('is_out_station',0);
            }


            if(request()->has('out_station') && (int)request()->out_station==1){
                
                $query = $query->where('is_out_station', 1)->where('is_completed',false)->where('is_cancelled',false);
            }



            $includes = ['driverDetail','requestBill'];
        }

        $result  = filter($query, new TripRequestTransformer, new RequestFilter)->customIncludes($includes)->paginate();

        return $this->respondSuccess($result,'history_listed');
    }


    /**
     * Outstation Ride histories
     *
     * @return \Illuminate\Http\JsonResponse
     *
     *
     * */
    public function outStationHistory(){

        $query = $this->request->where('user_id', auth()->user()->id)->orderBy('created_at', 'desc')->where('is_out_station', 1)->where('is_completed',false)->where('is_cancelled',false)->where('is_driver_started', false);

        $includes = ['driverDetail','requestBill'];

        // dd($query->get());

        $result  = filter($query, new TripRequestTransformer, new RequestFilter)->customIncludes($includes)->paginate();

        return $this->respondSuccess($result,'history_listed');

    }

    /**
    * Single Request History by request id
    * @responseFile responses/requests/user-single-history.json
    * @responseFile responses/requests/driver-single-history.json
    */
    public function getById($id)
    {
        if (access()->hasRole(Role::DRIVER)) {
            $includes = ['userDetail','requestBill'];
        } else {
            $includes=['driverDetail','requestBill'];
        }
        $query = $this->request->where('id', $id);

        $result  = filter($query, new TripRequestTransformer)->customIncludes($includes)->first();

        return $this->respondSuccess($result);
    }

    /**
    * Get Request detail by request id
    * @return \Illuminate\Http\JsonResponse
    *
    */
    public function getRequestByIdForDispatcher($id)
    {
        $query = $this->request->where('id', $id);
        $includes=['driverDetail','requestBill'];
        $result  = filter($query, new TripRequestTransformer)->customIncludes($includes)->first();
        return $this->respondSuccess($result);
    }

    /**
     * 
     * @response 
     * {
     *     "success": true,
     *     "message": "Invoice Sent"
     * }
     */
    public function invoice(RequestModel $requestmodel,)
    {
        $fractalData = fractal($requestmodel, new TripRequestTransformer)
                        ->parseIncludes(['userDetail', 'driverDetail', 'requestBill', 'rejectedDrivers'])
                        ->toArray();
    
        $logo = Setting::where('name', 'logo')->first();
        $invoice = ThirdPartySetting::where('module', 'mail_config')->pluck('value', 'name')->toArray();
    
        $data = $fractalData;
    
        $data['formatted_completed_at'] = null;
        if(isset($data['completed_at'])) {
            $data['formatted_completed_at'] = Carbon::parse($data['completed_at'])
                ->setTimezone(config('app.timezone', 'Asia/Kolkata'))
                ->format('M j, Y - h:i A');
        }

        $img = $logo;
        $user = auth()->user();
        if(request()->email){
            $email_user = (object)[
                'lang' => $user->lang,
                'name' => $user->name,
                'email' => request()->email,
            ];
            $user = $email_user;
        }

        if (access()->hasRole(Role::USER)) {
            if($user->email){
                dispatch(new SendUserInvoiceMailNotification($user, $data, $logo, $invoice));
            } else {
                return response()->json(['error' => 'Invalid role'], 403);
            }
        } elseif (access()->hasRole(Role::DRIVER) || access()->hasRole(Role::OWNER)) {
            if($user->email){
                dispatch(new SendDriverInvoiceMailNotification($user, $data, $logo, $invoice));
            } else {
                return response()->json(['error' => 'Invalid role'], 403);
            }
        } else {
            return response()->json(['error' => 'Invalid role'], 403);
        }
    

        return $this->respondSuccess(null, 'Invoice Sent');
    }
    
}
