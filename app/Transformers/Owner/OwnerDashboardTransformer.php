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
use App\Transformers\Owner\FleetTransformer;
use App\Transformers\Owner\FleetDriverTransformer;
use App\Models\Admin\Fleet;
use App\Models\Request\Request as RequestModel;
use Illuminate\Http\Request;

class OwnerDashboardTransformer extends Transformer
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
        'fleetDetail', 'driverDetail'

    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Owner $owner)
    {
        // Fetch fleet data with the required conditions
        $fleet_data = Fleet::where('owner_id', $owner->user_id)
            ->selectRaw("
                SUM(CASE WHEN approve = false THEN 1 ELSE 0 END) as blockedFleets,
                SUM(CASE WHEN approve = true AND active = true THEN 1 ELSE 0 END) as activeFleets,
                SUM(CASE WHEN approve = true AND active = false THEN 1 ELSE 0 END) as inActiveFleets
            ")
            ->first();
    
            $cardEarningsQuery = "IFNULL(SUM(IF(requests.payment_opt=0,request_bills.total_amount,0)),0)";
            $cashEarningsQuery = "IFNULL(SUM(IF(requests.payment_opt=1,request_bills.total_amount,0)),0)";
            $walletEarningsQuery = "IFNULL(SUM(IF(requests.payment_opt=2,request_bills.total_amount,0)),0)";
            $adminCommissionQuery = "IFNULL(SUM(request_bills.admin_commision_with_tax),0)";
            $driverCommissionQuery = "IFNULL(SUM(request_bills.driver_commision),0)";
            $discountQuery = "IFNULL(SUM(request_bills.promo_discount),0)";
            $totalEarningsQuery = "$adminCommissionQuery + $driverCommissionQuery"; //revenue 

            // dd(request()->all());
        // Define date filters based on the request input
        if (request()->has('date_format')) {
            $dateFormat = request()->input('date_format');
    
            switch ($dateFormat) {
                case 'today':
                    $startDate = Carbon::today();
                    $endDate = Carbon::now();
                    break;
    
                case 'this_week':
                    $startDate = Carbon::now()->startOfWeek();
                    $endDate = Carbon::now()->endOfWeek();
                    break;
    
                case 'this_month':
                    $startDate = Carbon::now()->startOfMonth();
                    $endDate = Carbon::now()->endOfMonth();
                    break;
    
                case 'this_year':
                    $startDate = Carbon::now()->startOfYear();
                    $endDate = Carbon::now()->endOfYear();
                    break;
    
                case 'custom':
                    $startDate = Carbon::parse(request()->input('start_date'));
                    $endDate = Carbon::parse(request()->input('end_date'));
                    break;
    
                default:
                    $startDate = Carbon::today();
                    $endDate = Carbon::now();
                    break;
            }
                //Today Earnings && today trips
        
            // Apply the date range filter to your query
            $overallEarnings = RequestModel::leftJoin('request_bills', 'requests.id', 'request_bills.request_id')
                ->selectRaw("
                    {$cardEarningsQuery} AS card,
                    {$cashEarningsQuery} AS cash,
                    {$walletEarningsQuery} AS wallet,
                    {$totalEarningsQuery} AS revenue,
                    {$adminCommissionQuery} as admin_commission,
                    {$driverCommissionQuery} as net_earnings,
                    {$discountQuery} as discount,
                    ({$cardEarningsQuery} + {$walletEarningsQuery}) AS digital_earnings
                ")
                ->companyKey()
                ->where('requests.owner_id', $owner->id)
                ->where('requests.is_completed', true)
                ->whereBetween('requests.created_at', [$startDate, $endDate]) // Apply date filter here
                ->first();
        } else {
            // Default query without date filter
            $overallEarnings = RequestModel::leftJoin('request_bills', 'requests.id', 'request_bills.request_id')
                ->selectRaw("
                    {$cardEarningsQuery} AS card,
                    {$cashEarningsQuery} AS cash,
                    {$walletEarningsQuery} AS wallet,
                    {$totalEarningsQuery} AS revenue,
                    {$adminCommissionQuery} as admin_commission,
                    {$driverCommissionQuery} as net_earnings,
                    {$discountQuery} as discount,
                    ({$cardEarningsQuery} + {$walletEarningsQuery}) AS digital_earnings
                ")
                ->companyKey()
                ->where('requests.owner_id', $owner->id)
                ->where('requests.is_completed', true)
                ->first();
        }
    
        // Populate the params array with the data
        $params = [
            'id' => $owner->id,
            'user_id' => $owner->user_id,
            'company_name' => $owner->company_name ?? null,
            'blocked_fleets' => $fleet_data->blockedFleets ?? 0,
            'active_fleets' => $fleet_data->activeFleets ?? 0,
            'inactive_fleets' => $fleet_data->inActiveFleets ?? 0,
            'card_earnings' => $overallEarnings->card ?? 0,
            'cash_earnings' => $overallEarnings->cash ?? 0,
            'wallet_earnings' => $overallEarnings->wallet ?? 0,
            'revenue' => $overallEarnings->revenue ?? 0,
            'admin_commission' => $overallEarnings->admin_commission ?? 0,
            'net_earnings' => $overallEarnings->net_earnings ?? 0,
            'discount' => $overallEarnings->discount ?? 0,
            'digital_earnings' => $overallEarnings->digital_earnings ?? 0,
        ];
    
        return $params;
    }
        /**
     * Include the driver of the request.
     *
     * @param Fleet $fleet
     * @return \League\Fractal\Resource\Item|\League\Fractal\Resource\NullResource
     */
    public function includeFleetDetail(Owner $owner)
    {
        $fleetDetail = $owner->fleetDetail()->where('approve',true)->get();;

        // dd($fleetDetail);

        return $fleetDetail
        ? $this->collection($fleetDetail, new FleetTransformer)
        : $this->null();
    }
    /**
     * Include the driver of the request.
     *
     * @param Fleet $fleet
     * @return \League\Fractal\Resource\Item|\League\Fractal\Resource\NullResource
     */
    public function includeDriverDetail(Owner $owner)
    {
        $driverDetail = $owner->driverDetail()->where('approve',true)->get();


        return $driverDetail
        ? $this->collection($driverDetail, new FleetDriverTransformer)
        : $this->null();        

    }   

   
}
