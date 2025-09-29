<?php

namespace App\Transformers\Owner;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Admin\Owner;
use App\Base\Constants\Auth\Role;
use App\Transformers\Transformer;
use App\Models\Request\RequestBill;
use App\Models\Request\RequestMeta;
use App\Models\Admin\OwnerDocument;
use App\Models\Admin\OwnerNeededDocument;
use App\Transformers\Access\RoleTransformer;
use App\Transformers\Requests\TripRequestTransformer;
use App\Base\Constants\Setting\Settings;
use App\Models\Admin\Sos;
use App\Transformers\Common\SosTransformer;
use App\Models\Chat;
use App\Models\Request\Request;
use App\Transformers\Payment\OwnerWalletTransformer;

class OwnerProfileTransformer extends Transformer
{
    /**
     * Resources that can be included if requested.
     *
     * @var array
     */
    protected array $availableIncludes = [
        
    ];

    /**
    * Resources that can be included default.
    *
    * @var array
    */
    protected array $defaultIncludes = [
        'wallet'
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Owner $user)
    {
        $authorization_code = auth()->user()->authorization_code;
        $params = [
            'id' => $user->id,
            'user_id' => $user->user_id,
            'company_name' => $user->company_name ?? null,
            'address' => $user->address ?? null,
            'postal_code' => $user->postal_code ?? null,
            'city' => $user->city ?? null,
            'tax_number' => $user->tax_number ?? null,
            'name' => $user->user->name ?? null,
            'owner_name' => $user->owner_name ?? null,
            'gender' => $user->user->gender ?? null,
            'email' => $user->email ?? null,
            'mobile' => $user->mobile_number,
            'profile_picture' => $user->user->profile_picture,
            'active' => (bool)$user->active,
            'approve' => (bool)$user->approve,
            'available' => (bool)$user->available,
            'uploaded_document'=>false,
            'declined_reason'=>$user->reason,
            'service_location_id'=>$user->service_location_id ?? null,
            'service_location_name'=>$user->area ?$user->area->name : null,
            'timezone'=>$user->timezone ?? null,
            'refferal_code'=>$user->user ? $user->user->refferal_code : null,
            'country_id'=>$user->user? $user->user->countryDetail->id : null,
            'currency_symbol' => $user->user ? $user->user->countryDetail->currency_symbol : null,
            'role'=>'owner',
            'transport_type' => $user->transport_type??null,
            'authorization_code'=>$authorization_code
        ];

        $params['contact_us_mobile1'] =  get_settings('contact_us_mobile1');
        $params['contact_us_mobile2'] =  get_settings('contact_us_mobile2');
        $params['contact_us_link'] =  get_settings('contact_us_link');

        $current_date = Carbon::now();

        $timezone = $user->user->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');

        $updated_current_date =  $current_date->setTimezone($timezone);

        $params['current_date'] = $updated_current_date->toDateString();

        $driver_documents = OwnerNeededDocument::active()->get();

        foreach ($driver_documents as $key => $needed_document) {
            if (OwnerDocument::where('owner_id', $user->id)->where('document_id', $needed_document->id)->exists()) {
                $params['uploaded_document'] = true;
            } else {
                $params['uploaded_document'] = false;
            }
        }

        $low_balance = false;

        $owner_wallet = auth()->user()->owner->ownerWalletDetail;

        $wallet_balance= $owner_wallet->amount_balance ?? 0;

         $minimum_balance = get_settings(Settings::OWNER_WALLET_MINIMUM_AMOUNT_TO_GET_ORDER);

            if($minimum_balance < 0){
                if ($minimum_balance > $wallet_balance) {

                $user->active = false;

                $user->save();
                
                $params['active'] = false;


                $low_balance = true;
            }
                
            }

            $params['show_wallet_feature_on_mobile_app'] =  get_settings('show_wallet_feature_on_mobile_app_owner');
            $params['show_wallet_money_transfer_feature_on_mobile_app'] =  get_settings('show_wallet_money_transfer_feature_on_mobile_app_for_owner');
            $params['enable_modules_for_applications'] =  get_settings('enable_modules_for_applications');
            $params['enable_map_appearance_change_on_mobile_app'] = (get_settings(Settings::ENABLE_MAP_APPEARANCE_CHANE_ON_MOBILE_APP));

            $params['low_balance'] = $low_balance;
            $params['chat_id'] = null;
            $get_chat_data = Chat::where('user_id',$user->user_id)->first();
            if($get_chat_data)
            {
                $params['chat_id'] = $get_chat_data->id;
            } 

            $params['map_type'] = $user->user->map_type ?? get_map_settings('map_type');

            $zone = find_zone($user->user->current_lat,$user->user->current_lng);
            $distance_unit = "";
            if($zone) {
                $distance_unit = $zone->unit == 1 ? "km" : "mi";
            }


        // Total Trip kms
        $total_trip_kms = Request::where('owner_id', $user->id)->where('is_completed', 1)->whereDate('trip_start_time', $current_date)->sum('total_distance');

        $params['total_trip_kms'] = number_format($total_trip_kms, 2);

        $total_trips = Request::where('owner_id', $user->id)->where('is_completed', 1)->whereDate('trip_start_time', $current_date)->get()->count();

        $params['total_trips'] = $total_trips;
//total Earinings
        $total_earnings = RequestBill::whereHas('requestDetail', function ($query) use ($user, $current_date) {
            $query->where('owner_id', $user->id)
                ->where('is_completed', 1)
                ->whereDate('trip_start_time', $current_date);
        })->sum('driver_commision');

        $params['total_earnings'] = round($total_earnings, 2);

        $params['sub_vehicle_type'] = [];

        return $params;
    }

       /**
     * Include the favourite location of the user.
     *
     * @param User $user
     * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\NullResource
     */
    public function includeWallet(Owner $owner)
    {
        $owner_wallet = $owner->ownerWalletDetail;

        return $owner_wallet
        ? $this->item($owner_wallet, new OwnerWalletTransformer)
        : $this->null();
    }

   
}
