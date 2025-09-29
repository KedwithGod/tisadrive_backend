<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use App\Base\Services\ImageUploader\ImageUploaderContract;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Models\User;
use App\Models\Admin\Owner;
use App\Models\Admin\OwnerNeededDocument;
use App\Models\Admin\OwnerDocument;
use App\Base\Filters\Admin\OwnerFilter;
use App\Base\Constants\Masters\DriverDocumentStatus;
use App\Base\Constants\Auth\Role;
use App\Models\Admin\ServiceLocation;
use Illuminate\Http\Request;
use App\Http\Controllers\Web\BaseController;
use Kreait\Firebase\Contract\Database;
use App\Models\Country;
use App\Transformers\CountryTransformer;
use Log;
use DB;
use Carbon\Carbon;
use App\Base\Constants\Masters\WalletRemarks;
use App\Models\Method;
use App\Jobs\Notifications\SendPushNotification;
use App\Transformers\Payment\WalletWithdrawalRequestsTransformer;
use App\Models\Payment\WalletWithdrawalRequest;
use  App\Models\Payment\OwnerWallet;
use App\Models\Payment\OwnerWalletHistory;
use App\Transformers\Payment\OwnerWalletHistoryTransformer;
use App\Models\Admin\Fleet;
use App\Models\Admin\Driver;
use App\Models\Request\RequestBill;
use Illuminate\Support\Facades\Session;
use App\Models\Request\Request as RequestRequest;
use App\Base\Filters\Master\CommonMasterFilter;

class ManageOwnerController extends BaseController
{

    /**
    * ImageUploader instance.
    *
    * @var ImageUploaderContract
    */
    protected $imageUploader;
    protected $owner;
    protected $database;

    /**
     * DriverDocumentController constructor.
     *
     * @param ImageUploaderContract $imageUploader
     */
    public function __construct(ImageUploaderContract $imageUploader, owner $owner,Database $database)
    {
        $this->imageUploader = $imageUploader;
        $this->owner = $owner;
        $this->database = $database;
    }
    public function index() {
        $location = ServiceLocation::active()->get();
        return Inertia::render('pages/manage_owners/index',['serviceLocations' => $location,'app_for'=>env("APP_FOR"),]);
    }

    public function list(QueryFilterContract $queryFilter, Request $request)
    {
        $query = Owner::query()->orderBy('created_at','DESC');
        $results = $queryFilter->builder($query)->customFilter(new OwnerFilter)->paginate();

        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);
    }

    public function checkMobileExists($mobile, $ownerId = null)
    {
        $query = Owner::where('mobile', $mobile);
        if ($ownerId !== null) {
            $query->where('id', '!=', $ownerId);
        }
        $ownerExists = $query->exists();
        return response()->json(['exists' => $ownerExists]);
    }

    public function checkEmailExists($email, $ownerId = null)
    {
        $query = Owner::where('email', $email);
        if ($ownerId !== null) {
            $query->where('id', '!=', $ownerId);
        }
        $ownerExists = $query->exists();
        return response()->json(['exists' => $ownerExists]);
    }
    public function create()
    {
        $location = ServiceLocation::active()->get();

        $query = Country::active()->get();

        $countries = fractal($query, new CountryTransformer);

        $result = json_decode($countries->toJson(),true);
        
        $default_country = Country::active()->where('code',get_settings('default_country_code_for_mobile_app'))->first();


        $default_dial_code = $default_country->dial_code;
        $default_country_id = $default_country->id;
        $default_flag = $default_country->flag;
        return Inertia::render('pages/manage_owners/create',[
            'countries'=>$result['data'],
            'default_dial_code'=>$default_dial_code,
            'serviceLocation' => $location,
            'default_flag'=>$default_flag,
            'default_country_id'=>$default_country_id]);
    }
    public function store(Request $request) {
        // dd($request->all());
        $validated = $request->validate([
            'company_name' => 'required',
            'name' => 'required',
            'country' => 'required',
            'email' => 'required',
            'mobile'=>'required|mobile_number|min:8',
            'password' => 'required',
            'service_location_id' => 'required',
            'transport_type' => 'required',
        ]);
        $validated['password'] = bcrypt($request->input('password'));
        $validated['first_name'] = bcrypt($request->input('name'));
        $token = str_random(40);
        $validated['email_confirmation_token'] = bcrypt($token);
        $user_params = $request->only([
            'mobile','email'
        ]);
        $user_params['name'] = $validated['name'];
        $user_params['country'] = $validated['country'];
        $user_params['password'] = bcrypt($request->input('password'));

        $validate_exists_email = $this->checkEmailExists($request->email)->getData()->exists;
        $validate_exists_mobile = $this->checkMobileExists($request->mobile)->getData()->exists;
        // dd($validate_exists_email, $validate_exists_mobile);

        if ($validate_exists_email) {
            return response()->json([
                'email'=>'Provided email has already been taken',
            ], 422);
        }
        if ($validate_exists_mobile) {
            return response()->json([
                'email'=>'Provided mobile has already been taken',
            ], 422);
        }
        $user = User::create($user_params);
        $user->attachRole(Role::OWNER);

        
        $owner = $user->owner()->create($validated);

        $owner_wallet = $owner->ownerWalletDetail()->create(['amount_added'=>0]);
        return response()->json([
            'results' => 'Owner Created Successfully',
        ],201);
    }

    public function edit(Owner $owner) 
    {
        // dd($owner);
        $location = [$owner->area];

        $query = Country::active()->get();

        $countries = fractal($query, new CountryTransformer);

        $result = json_decode($countries->toJson(),true);
        
        $default_country = Country::where('id',$owner->user->country)->first();


        $default_dial_code = $default_country->dial_code;
        $default_country_id = $default_country->id;
        $default_flag = $default_country->flag;

        return Inertia::render('pages/manage_owners/create',['serviceLocation' => $location,
        'default_dial_code'=>$default_dial_code,
        'countries'=>$result['data'],
        'default_flag'=>$default_flag,
        'default_country_id'=>$default_country_id,'owner'=>$owner,'app_for'=>env('APP_FOR')]);
    }
    public function update(Owner $owner, Request $request) 
    {
        $validated = $request->validate([
            'company_name' => 'required',
            'name' => 'required',
            'country' => 'required',
            'email' => 'required',
            'mobile'=>'required|mobile_number|min:8',
            'password' => 'sometimes',
            'service_location_id' => 'required',
            'transport_type' => 'required',
        ]);
        if($request->input('password')){
            $validated['password'] = bcrypt($request->input('password'));
        }
        $token = str_random(40);
        $validated['email_confirmation_token'] = bcrypt($token);
        $user_params = $request->only([
            'mobile','email'
        ]);
        $user_params['name'] = $validated['name'];
        $user_params['country'] = $validated['country'];
        if($request->input('password')){
            $user_params['password'] = $validated['password'];
        }
        $validate_exists_email = $this->checkEmailExists($request->email, $owner->id)->getData()->exists;
        $validate_exists_mobile = $this->checkMobileExists($request->mobile, $owner->id)->getData()->exists;

        if ($validate_exists_email) {
            return response()->json([
                'email'=>'Provided email has already been taken',
            ], 422);
        }
        if ($validate_exists_mobile) {
            return response()->json([
                'email'=>'Provided mobile has already been taken',
            ], 422);
        }
        $owner->user->update($user_params);
        $owner->update($validated);
        return response()->json([
            'successMessage' => 'Owner Updated Successfully',
        ],201);
    }

    public function editPassword(Owner $owner)
        {
            $location = ServiceLocation::active()->get();

        $query = Country::active()->get();

        $countries = fractal($query, new CountryTransformer);

        $result = json_decode($countries->toJson(),true);
        
        $default_country = Country::where('id',$owner->user->country)->first();


        $default_dial_code = $default_country->dial_code;
        $default_country_id = $default_country->id;
        $default_flag = $default_country->flag;

        return Inertia::render('pages/manage_owners/edit',['serviceLocation' => $location,
        'default_dial_code'=>$default_dial_code,
        'countries'=>$result['data'],
        'default_flag'=>$default_flag,
        'default_country_id'=>$default_country_id,'owner'=>$owner,'app_for'=>env('APP_FOR')]);
        }

        public function updatePasswords(Owner $owner, Request $request)
        {
            // Validate the password and confirmation
            $updated_params = $request->validate([
                'password' => 'required|min:8',  // Confirmed is for password_confirmation
                'confirm_password' => 'required|same:password',
            ]);

            if($request->input('password')){
                $validated['password'] = bcrypt($request->input('password'));
            }
            if($request->input('password')){
                $user_params['password'] = $validated['password'];
            }
            $owner->user->update($user_params);
            $owner->update($validated);
        }

    public function delete(Owner $owner) {
        $owner->delete();
        $owner->user->delete();
        return response()->json([
            'successMessage' => 'Owner Deleted Successfully',
            'serviceLocations' =>ServiceLocation::active()->get(),
        ],201);
    }


    public function viewProfile(Owner $owner) 
    {
        $completed_ride_count = RequestRequest::whereHas('driverDetail', function ($query) use ($owner) {
            $query->where('owner_id', $owner->id);
        })->where('is_completed', 1)->count();

        $canceled_ride_count = RequestRequest::whereHas('driverDetail', function ($query) use ($owner) {
            $query->where('owner_id', $owner->id);
        })->where('is_cancelled', 1)->count();
        

        $firebaseSettings = [
            'firebase_api_key' => get_firebase_settings('firebase_api_key'),
            'firebase_auth_domain' => get_firebase_settings('firebase_auth_domain'),
            'firebase_database_url' => get_firebase_settings('firebase_database_url'),
            'firebase_project_id' => get_firebase_settings('firebase_project_id'),
            'firebase_storage_bucket' => get_firebase_settings('firebase_storage_bucket'),
            'firebase_messaging_sender_id' => get_firebase_settings('firebase_messaging_sender_id'),
            'firebase_app_id' => get_firebase_settings('firebase_app_id'),
        ];

        $currency_code = get_settings('currency_code');
        $currency_symbol = get_settings('currency_symbol');

        $owner_wallet = $owner->ownerWalletDetail;

        $total_fleets = Fleet::selectRaw('
                                        IFNULL(SUM(CASE WHEN approve=1 THEN 1 ELSE 0 END),0) AS approved,
                                        IFNULL((SUM(CASE WHEN approve=1 THEN 1 ELSE 0 END) / count(*)),0) * 100 AS approve_percentage,
                                        IFNULL((SUM(CASE WHEN approve=0 THEN 1 ELSE 0 END) / count(*)),0) * 100 AS decline_percentage,
                                        IFNULL(SUM(CASE WHEN approve=0 THEN 1 ELSE 0 END),0) AS declined,
                                        count(*) AS total
                                    ')
                                ->whereHas('user', function ($query) {
                                    $query->companyKey();
                                });
        $total_fleets = $total_fleets->where('owner_id', $owner->user_id)->first();

        $total_drivers = Driver::selectRaw('
                        IFNULL(SUM(CASE WHEN approve=1 THEN 1 ELSE 0 END),0) AS approved,
                        IFNULL((SUM(CASE WHEN approve=1 THEN 1 ELSE 0 END) / count(*)),0) * 100 AS approve_percentage,
                        IFNULL((SUM(CASE WHEN approve=0 THEN 1 ELSE 0 END) / count(*)),0) * 100 AS decline_percentage,
                        IFNULL(SUM(CASE WHEN approve=0 THEN 1 ELSE 0 END),0) AS declined,
                        count(*) AS total
                    ')
                ->whereHas('user', function ($query) {
                    $query->companyKey();
                });

        $total_drivers = $total_drivers->whereNotNull('owner_id')->first();

        $today = date('Y-m-d');
            // card Datas 
            
            $driver_ids = Driver::where('owner_id', $owner->id)->with('fleetDetail')->get(); 
            $fleet_ids = Fleet::where('owner_id',$owner->user->id)->with('driverDetail')->get();
        
            $fire_base_driver_ids = Driver::where('owner_id', $owner->id)
            ->pluck('id')
            ->map(function ($id) {
                return 'driver_' . $id;
            });
        // dd($fire_base_driver_ids);

            //Today Earnings && today trips
            $cardEarningsQuery = "IFNULL(SUM(IF(requests.payment_opt=0,request_bills.total_amount,0)),0)";
            $cashEarningsQuery = "IFNULL(SUM(IF(requests.payment_opt=1,request_bills.total_amount,0)),0)";
            $walletEarningsQuery = "IFNULL(SUM(IF(requests.payment_opt=2,request_bills.total_amount,0)),0)";
            $adminCommissionQuery = "IFNULL(SUM(request_bills.admin_commision_with_tax),0)";
            $driverCommissionQuery = "IFNULL(SUM(request_bills.driver_commision),0)";
            $totalEarningsQuery = "$cardEarningsQuery + $cashEarningsQuery + $walletEarningsQuery";

            $todayEarnings = RequestRequest::leftJoin('request_bills', 'requests.id', '=', 'request_bills.request_id')
                            ->selectRaw("
                                {$cardEarningsQuery} AS card,
                                {$cashEarningsQuery} AS cash,
                                {$walletEarningsQuery} AS wallet,
                                {$totalEarningsQuery} AS total,
                                {$adminCommissionQuery} AS admin_commision,
                                {$driverCommissionQuery} AS driver_commision
                            ")
                            ->companyKey()
                            ->where('owner_id', $owner->id)
                            ->where('requests.is_completed', true)
                            ->whereDate('requests.trip_start_time', date('Y-m-d'))
                            ->first();


            $todayTrips = RequestRequest::companyKey()
                                        ->whereDate('created_at', $today)
                                        ->where('owner_id', $owner->id)
                                        ->selectRaw('
                                            IFNULL(SUM(CASE WHEN is_completed=1 THEN 1 ELSE 0 END), 0) AS today_completed,
                                            IFNULL(SUM(CASE WHEN is_completed=0 AND is_cancelled=0 THEN 1 ELSE 0 END), 0) AS today_scheduled,
                                            IFNULL(SUM(CASE WHEN is_cancelled=1 THEN 1 ELSE 0 END), 0) AS today_cancelled
                                        ')
                                        ->first();        


            //Over All Earnings
            $overallEarnings = RequestRequest::leftJoin('request_bills','requests.id','request_bills.request_id')
                                ->selectRaw("
                                {$cardEarningsQuery} AS card,
                                {$cashEarningsQuery} AS cash,
                                {$walletEarningsQuery} AS wallet,
                                {$totalEarningsQuery} AS total,
                                {$adminCommissionQuery} as admin_commision,
                                {$driverCommissionQuery} as driver_commision")
                                ->companyKey()
                                ->where('requests.owner_id', $owner->id)
                                ->where('requests.is_completed',true)
                                ->first();


                                $startDate = Carbon::now()->startOfYear(); // Start of the current year (January 1st)
                                $endDate = Carbon::now();
                                $earningsData=[];

                $months = [];
                $a = [];
                $u = [];
                $d = [];                          
                while ($startDate->lte($endDate))
                {
                    $from = Carbon::parse($startDate)->startOfMonth();
                    $to = Carbon::parse($startDate)->endOfMonth();
                    $shortName = $startDate->shortEnglishMonth;
                    $monthName = $startDate->monthName;
                
                    // Collect cancel data directly into arrays
                    $months[] = $shortName;
                    $a[] = RequestRequest::where('owner_id', $owner->id)->whereBetween('created_at', [$from, $to])->where('cancel_method', '0')->whereIsCancelled(true)->count();
                    $u[] = RequestRequest::where('owner_id', $owner->id)->whereBetween('created_at', [$from, $to])->where('cancel_method', '1')->whereIsCancelled(true)->count();
                    $d[] = RequestRequest::where('owner_id', $owner->id)->whereBetween('created_at', [$from, $to])->where('cancel_method', '2')->whereIsCancelled(true)->count();
                
                    $earningsData['earnings']['months'][] = $monthName;
                    $earningsData['earnings']['values'][] = RequestBill::whereHas('requestDetail', function ($query) use ($from,$to, $owner) {
                                        $query->where('owner_id', $owner->id)->whereBetween('trip_start_time', [$from,$to])->whereIsCompleted(true);
                                    })->sum('total_amount');

                    $startDate->addMonth();
                }
            $currency_code = get_settings('currency_code');
            $currency_symbol = get_settings('currency_symbol');

            // Query to calculate Fleet earnings for each fleet
            $fleetsEarnings = DB::table('fleets')
            ->join('requests', 'fleets.id', '=', 'requests.fleet_id')
            ->join('request_bills', 'requests.id', '=', 'request_bills.request_id')
            ->join('vehicle_types', 'fleets.vehicle_type', '=', 'vehicle_types.id')
            ->leftJoin('request_ratings', 'requests.id', '=', 'request_ratings.request_id')
            ->where('fleets.owner_id', $owner->user_id) // Assuming this is the correct field name
            ->select(
                'fleets.id as fleet_id',
                'fleets.license_number',
                'vehicle_types.name as vehicle_type_name',
                DB::raw('SUM(request_bills.total_amount) as total_earnings'),
                DB::raw('SUM(request_bills.driver_commision) as total_driver_earnings'),
                DB::raw('SUM(request_bills.admin_commision) as total_admin_earnings'),
                DB::raw('COUNT(requests.id) as total_trips'),
                DB::raw('SUM(CASE WHEN requests.is_completed = 1 THEN 1 ELSE 0 END) as completed_requests'),
                DB::raw('AVG(COALESCE(request_ratings.user_rating, 0)) as average_user_rating')
            )
            ->groupBy('fleets.id', 'fleets.license_number', 'vehicle_types.name')
            ->get();
        

        // Driver earning by fleet
            // Query to calculate Fleet earnings for each fleet
            $fleetDriverEarnings = DB::table('drivers')
            ->join('requests', 'drivers.id', '=', 'requests.driver_id')
            ->join('request_bills', 'requests.id', '=', 'request_bills.request_id')
            ->leftJoin('request_ratings', 'requests.id', '=', 'request_ratings.request_id')
            ->where('drivers.owner_id', $owner->id) // Assuming this is the correct field name
            ->select(
                'drivers.id as driver_id',
                'drivers.name',
                DB::raw('FORMAT(SUM(request_bills.total_amount), 2) as total_earnings'),
                DB::raw('FORMAT(SUM(request_bills.driver_commision), 2) as total_driver_earnings'),
                DB::raw('FORMAT(SUM(request_bills.admin_commision), 2) as total_admin_earnings'),
                DB::raw('COUNT(requests.id) as total_trips'),
                DB::raw('SUM(CASE WHEN requests.is_completed = 1 THEN 1 ELSE 0 END) as completed_requests'),
                DB::raw('FORMAT(AVG(request_ratings.user_rating), 2) as average_user_rating')
            )
            ->groupBy('drivers.id', 'drivers.name')
            ->get();

        if(get_map_settings('map_type') == "open_street_map"){
            return Inertia::render('pages/manage_owners/open-view_profile',[
                'owner'=>$owner, 
                'app_for'=>env("APP_FOR"),
                'default_lat'=>get_settings('default_latitude'),
                'default_lng'=>get_settings('default_longitude'),
                'owner_wallet'=>$owner_wallet,
                'firebaseSettings'=>$firebaseSettings,
                'fleetsEarnings' => $fleetsEarnings,
                'total_fleets'=> $total_fleets,
                'total_drivers'=> $total_drivers,
                'driverIds' => $driver_ids,
                'earningsData' => $earningsData,
                'todayTrips' => $todayTrips,
                'fleetsEarnings' => $fleetsEarnings,
                'todayEarnings' => $todayEarnings,
                'overallEarnings' => $overallEarnings,
                'fleetDriverEarnings' => $fleetDriverEarnings,
                'currency_code' => $currency_code,
                'currencySymbol' => $currency_symbol,
                'completed_ride_count'=>$completed_ride_count,
                'canceled_ride_count'=>$canceled_ride_count,
                "fleet_ids" => $fleet_ids
            ]);

        }

        $map_key = get_map_settings('google_map_key');
        // dd($tripsChartData);

        return Inertia::render('pages/manage_owners/view_profile',
            [
                'owner'=>$owner, 
                'app_for'=>env("APP_FOR"),
                'default_lat'=>get_settings('default_latitude'),
                'default_lng'=>get_settings('default_longitude'),
                'owner_wallet'=>$owner_wallet,
                'firebaseSettings'=>$firebaseSettings,
                'total_fleets'=> $total_fleets,
                'total_drivers'=> $total_drivers,
                'driverIds' => $driver_ids,
                'earningsData' => $earningsData,
                'todayTrips' => $todayTrips,
                'fleetsEarnings' => $fleetsEarnings,
                'todayEarnings' => $todayEarnings,
                'overallEarnings' => $overallEarnings,
                'fleetDriverEarnings' => $fleetDriverEarnings,
                'map_key'=>$map_key,
                'currency_code' => $currency_code,
                'currencySymbol' => $currency_symbol,
                'completed_ride_count'=>$completed_ride_count,
                'canceled_ride_count'=>$canceled_ride_count,
                "fleet_ids" => $fleet_ids,
            ]);
    }

    public function driverList(Owner $owner) {

        $query = Driver::where('owner_id', $owner->id)->orderBy('created_at', 'desc') // Order by descending
        ->paginate();

        return response()->json([
            'query' => $query->items(),
            'paginator' => $query,
        ]); 
    }

    public function fleetList(Owner $owner) {

        $query = Fleet::where('owner_id', $owner->user->id)->orderBy('created_at', 'desc') // Order by descending
        ->paginate();

        return response()->json([
            'query' => $query->items(),
            'paginator' => $query,
        ]); 
    }

    // walletHistoryList
    public function walletHistoryList(Owner $owner)
    {

        // dd($driver);
        $results = $owner->ownerWalletHistoryDetail()->orderBy('created_at', 'desc')->paginate();        
        $items = fractal($results, new OwnerWalletHistoryTransformer)->toArray();

        return response()->json([
            'results' => $items['data'],
            'paginator' => $results,
        ]);
    }

    public function walletAddAmount(Request $request, Owner $owner)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'operation' => 'required|in:add,subtract'
        ]);

        $owner_wallet = $owner->ownerWalletDetail;
      
        if (!$owner_wallet) {
            // Create a new wallet for the driver
            $owner_wallet = $owner->ownerWalletDetail()->create([
                // Add the necessary fields and their default values
                'amount_added' => 0, 
                'amount_balance' => 0, 
                'amount_spent' => 0, 
            ]);
        }

        $amount = $request->input('amount');
        $operation = $request->input('operation');
        $transaction_id = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);

        if ($operation === 'subtract' && $owner_wallet->amount_balance < $amount) {
            return response()->json(['message' => 'Insufficient funds'], 400);
        }


        if ($operation === 'add') {
            $owner_wallet->amount_added += $amount;
            $owner_wallet->amount_balance += $amount;
            $is_credit = true;
            $remarks = WalletRemarks::MONEY_DEPOSITED_TO_E_WALLET_FROM_ADMIN;
        } else {
            $owner_wallet->amount_balance -= $amount;
            $owner_wallet->amount_spent += $amount;
            $is_credit = false;
            $remarks = WalletRemarks::WITHDRAWN_FROM_WALLET;
        }

        $owner_wallet->save();

        OwnerWalletHistory::create([
            'user_id' => $owner->id,
            'amount' => $amount,
            'transaction_id' => $transaction_id,
            'remarks' => $remarks,
            'is_credit' => $is_credit,
        ]);

        // $currency = $owner->user->countryDetail()->pluck('currency_symbol')->first();

        // send mail wallet amount added
        // SendDriverWalletAmountMailNotification::dispatch($driver, $transaction_id, $currency, $amount, $owner_wallet);
        return response()->json(['message' => 'Amount adjusted successfully', 'transaction_id' => $transaction_id], 200);
    }
    public function document(Owner $owner) 
    {

        // Fetch uploaded documents
        $ownerDocuments = $owner->ownerDocument ?: collect(); // Default to empty collection if null
        $ownerDocuments = $ownerDocuments->keyBy('document_id'); // Key by document_id for easy lookup
    
        // Fetch required documents
        $ownerNeededDocuments = OwnerNeededDocument::where('active', true)->get();
    
        // Merge data
        $documents = $ownerNeededDocuments->map(function ($doc) use ($ownerDocuments) {
            $uploadedDoc = $ownerDocuments->get($doc->id);
            return [
                'id' => $doc->id,
                'name' => $doc->name,
                'doc_type' => $doc->doc_type,
                'has_identify_number' => $doc->has_identify_number,
                'has_expiry_date' => $doc->has_expiry_date,
                'active' => $doc->active,
                'identify_number_locale_key' => $doc->identify_number_locale_key,
                'uploaded' => $uploadedDoc ? true : false,
                'expiry_date' => $uploadedDoc->expiry_date ?? null,
                'identify_number' => $uploadedDoc->identify_number ?? null,
                'document_status' => $uploadedDoc->document_status ?? null,
                'comment' => $uploadedDoc->comment ?? null,
                'image' => $uploadedDoc->image ?? null,
                'back_image' => $uploadedDoc->back_image ?? null,
                'document_name_front' => $doc->document_name_front, // Include front name
                'document_name_back' => $doc->document_name_back, // Include back name
            ];
        });

        // dd($documents);
    
        return Inertia::render('pages/manage_owners/document', [
            'documents' => $documents,
            'ownerId' => $owner->id,
        ]);

    }
    
    public function driverDocumentList(owner $owner,QueryFilterContract $queryFilter) {

        // Fetch uploaded documents
        $ownerDocuments = $owner->ownerDocument ?: collect(); // Default to empty collection if null
        $ownerDocuments = $ownerDocuments->keyBy('document_id'); // Key by document_id for easy lookup
    
        // Fetch required documents
        $ownerNeededDocuments = OwnerNeededDocument::where('active', true)->get();
    
        // Merge data

        $documents = $ownerNeededDocuments->map(function ($doc) use ($ownerDocuments) {
            $uploadedDoc = $ownerDocuments->get($doc->id);
            return [
                'id' => $doc->id,
                'name' => $doc->name,
                'doc_type' => $doc->doc_type,
                'has_identify_number' => $doc->has_identify_number,
                'has_expiry_date' => $doc->has_expiry_date,
                'active' => $doc->active,
                'identify_number_locale_key' => $doc->identify_number_locale_key,
                'account_type' => $doc->account_type,
                'uploaded' => $uploadedDoc ? true : false,
                'expiry_date' => $uploadedDoc->expiry_date ?? null,
                'identify_number' => $uploadedDoc->identify_number ?? null,
                'document_status' => $uploadedDoc->document_status ?? null,
                'comment' => $uploadedDoc->comment ?? null,
                'image' => $uploadedDoc->image ?? null,
                'back_image' => $uploadedDoc->back_image ?? null,
                'document_name_front' => $doc->document_name_front, // Include front name
                'document_name_back' => $doc->document_name_back, // Include back name
            ];
        });
        
        return response()->json([
            'results' => $documents,
        ]);
    }

    public function documentUpload(OwnerNeededDocument $document, Owner $ownerId)
    {
        $uploaded = $ownerId->ownerDocument()->where('document_id', $document->id)->first();

// dd($document);
    return Inertia::render('pages/manage_owners/document_upload',['ownerId'=>$ownerId,
    'uploaded'=>$uploaded, 'document'=>$document,]);

    }
    public function documentUploadStore(Request $request, OwnerNeededDocument $document, Owner $ownerId,)
    {

        // dd($request->all());
        $created_params = $request->only(['identify_number']);

        $created_params['owner_id'] = $ownerId->id;
        $created_params['document_id'] = $document->id;

        $created_params['expiry_date'] = null;


        if($request->expiry_date!=null)
        {
            $expiry_date = Carbon::parse($request->expiry_date)->toDateTimeString();

            $created_params['expiry_date'] = $expiry_date;
        }


        if ($uploadedFile = $request->file('image')) {
            $created_params['image'] = $this->imageUploader->file($uploadedFile)
                ->saveOwnerDocument($ownerId->id);
        }

        if ($uploadedFile = $request->file('back_image')) {
            $created_params['back_image'] = $this->imageUploader->file($uploadedFile)
                ->saveOwnerDocument($ownerId->id);
        }
        // dd($created_params);

        // Check if document exists
        $owner_documents = OwnerDocument::where('owner_id', $ownerId->id)->where('document_id', $document->id)->first();

        if ($owner_documents) {
            $created_params['document_status'] = DriverDocumentStatus::REUPLOADED_AND_WAITING_FOR_APPROVAL;
            OwnerDocument::where('owner_id', $ownerId->id)->where('document_id', $document->id)->update($created_params);
        } else {
            $created_params['document_status'] = DriverDocumentStatus::UPLOADED_AND_WAITING_FOR_APPROVAL;
            OwnerDocument::create($created_params);
        }


        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Owner Document uploaded successfully.',
                'ownerId'=>$ownerId,
                'document'=>$document
                ], 201);

    }
    public function updateStatus(Request $request)
    {
        // dd($request->all());
        $neededDoc = OwnerNeededDocument::active()->count();
        $owner = Owner::with('ownerDocument')->find($request->id);
        $uploadedDoc = $owner->ownerDocument()
            ->whereIn('document_status', [1])
            ->count();

        $status = $request->status ? 1 : 0;

        if($neededDoc != $uploadedDoc){
            return response()->json([
                'status' => 'failure',
                'message' => 'Driver document Disapproved.',
                'data' =>'uploaddocument'
            ]);          
        }
    
        $owner->update([
            'approve'=>$request->status,
            'reason' =>Null
        ]);


        $this->database->getReference('owners/owner_' . $owner->id)
        ->update(['approve' => $status, 'updated_at' => Database::SERVER_TIMESTAMP]);

        $notification = \DB::table('notification_channels')
            ->where('topics', 'Driver Account Approval')
            ->first();

        //    send push notification 
        if ($notification && $notification->push_notification == 1) {
                // Determine the user's language or default to 'en'
            $userLang = $owner->user->lang ?? 'en';
            // dd($userLang);

            // Fetch the translation based on user language or fall back to 'en'
            $translation = \DB::table('notification_channels_translations')
                ->where('notification_channel_id', $notification->id)
                ->where('locale', $userLang)
                ->first();

            // If no translation exists, fetch the default language (English)
            if (!$translation) {
                $translation = \DB::table('notification_channels_translations')
                    ->where('notification_channel_id', $notification->id)
                    ->where('locale', 'en')
                    ->first();
            }            
            
            $title =  $translation->push_title ?? $notification->push_title;
            $body = strip_tags($translation->push_body ?? $notification->push_body);
            dispatch(new SendPushNotification($owner->user, $title, $body));
        }

        return response()->json([
            'successMessage' => 'Owner updated successfully',
        ]);
    }
    public function approvOwnerDocument($documentId,$ownerId,$status)
    {
        $owner = Owner::find($ownerId);

        $ownerDoc = OwnerDocument::where('owner_id', $ownerId)->where('document_id', $documentId)->first();

        if (!$ownerDoc) {
            return response()->json([
                'status' => 'failure',
                'message' => 'Document not found for the given driver.'
            ], 404); // Return a 404 status code for better semantics
        }

        $ownerDoc->update(['document_status' => $status]);


        $documentStatuses = $owner->ownerDocument->pluck('document_status');

        $neededDoc = OwnerNeededDocument::active()->count();
        $uploadedDoc = count($owner->ownerDocument);

        if( $neededDoc != $uploadedDoc){
            return redirect('manage-owners/document/'.$owner->id);
        }

        if($status==1)
        {
            $is_required = OwnerNeededDocument::active()->where('is_required', true)->pluck("id");
            $documentStatus = OwnerDocument::whereIn('document_id',$is_required)->pluck('document_status');
       
            $allDocumentsApproved = $documentStatus->every(function ($value) {
                return $value == 1;
            });
            // dd($allDocumentsApproved);
            if ($allDocumentsApproved)
            {
                $owner->update(['approve'=>1]);
    
                $this->database->getReference('owners/owner_' . $owner->id)
                ->update(['approve' => 1, 'updated_at' => Database::SERVER_TIMESTAMP]);
        
                // $title = custom_trans('driver_approved', [], $owner->user->lang);
                // $body = custom_trans('driver_approved_body', [], $owner->user->lang);
            
                // dispatch(new SendPushNotification($owner->user, $title, $body));

                 $notification = \DB::table('notification_channels')
                ->where('topics', 'Driver Account Approval') // Match the correct topic
                ->first();

            //    send push notification 
                if ($notification && $notification->push_notification == 1) {
                     // Determine the user's language or default to 'en'
                    $userLang = $owner->user->lang ?? 'en';
                    // dd($userLang);
    
                    // Fetch the translation based on user language or fall back to 'en'
                    $translation = \DB::table('notification_channels_translations')
                        ->where('notification_channel_id', $notification->id)
                        ->where('locale', $userLang)
                        ->first();
    
                    // If no translation exists, fetch the default language (English)
                    if (!$translation) {
                        $translation = \DB::table('notification_channels_translations')
                            ->where('notification_channel_id', $notification->id)
                            ->where('locale', 'en')
                            ->first();
                    }            
                    
                    $title =  $translation->push_title ?? $notification->push_title;
                    $body = strip_tags($translation->push_body ?? $notification->push_body);
                    dispatch(new SendPushNotification($owner->user, $title, $body));
                }
                // return redirect()->route('manageowners.index');
                return response()->json([
                    'status' => 'success',
                    'message' => 'Owner document approved successfully.',
                    'allDocumentsApproved'=>$allDocumentsApproved,
                ]);
           }
    
        }else{
            $allDocumentsDisapproved = $documentStatuses->contains(5);
    
            if ($allDocumentsDisapproved){
                $owner->update(['approve'=>0]);
        
                $this->database->getReference('owners/owner_' . $owner->id)
                ->update(['approve' => 0, 'updated_at' => Database::SERVER_TIMESTAMP]);
        

                // $title = custom_trans('driver_declined_title', [], $owner->user->lang);
                // $body = custom_trans('driver_declined_body', [], $owner->user->lang);
            
                // dispatch(new SendPushNotification($owner->user, $title, $body)); 
                
                $notification = \DB::table('notification_channels')
                ->where('topics', 'Account Disapproval') // Match the correct topic
                ->first();

            //    send push notification 
                if ($notification && $notification->push_notification == 1) {
                     // Determine the user's language or default to 'en'
                    $userLang = $owner->user->lang ?? 'en';
                    // dd($userLang);
    
                    // Fetch the translation based on user language or fall back to 'en'
                    $translation = \DB::table('notification_channels_translations')
                        ->where('notification_channel_id', $notification->id)
                        ->where('locale', $userLang)
                        ->first();
    
                    // If no translation exists, fetch the default language (English)
                    if (!$translation) {
                        $translation = \DB::table('notification_channels_translations')
                            ->where('notification_channel_id', $notification->id)
                            ->where('locale', 'en')
                            ->first();
                    }            
                    
                    $title =  $translation->push_title ?? $notification->push_title;
                    $body = strip_tags($translation->push_body ?? $notification->push_body);
                    dispatch(new SendPushNotification($owner->user, $title, $body));
                }
                // return redirect()->route('manageowners.index');
                return response()->json([
                    'status' => 'success',
                    'message' => 'Owner document disapproved successfully.',
                    'allDocumentsDisapproved'=>$allDocumentsDisapproved
                ]);
            }
        }


    
        // return response()->json([
        //     'status' => 'success',
        //     'message' => 'Owner document approved successfully.'
        // ]);


// dd($owner);

    }
    public function updateAndApprove(Owner $ownerId)
    {
        $documentStatuses = $ownerId->ownerDocument->pluck('document_status');

         // Handle the case where no document statuses exist
         if ($documentStatuses->isEmpty()) {           
            return response()->json(['message' => 'No documents found. Update not performed.']);
        }
       
        $allDocumentsApproved = $documentStatuses->every(function ($value) {
            return $value == 1;
        });
        // dd($allDocumentsApproved);
        if ($allDocumentsApproved)
        {
            $ownerId->update(['approve'=>1]);

    
            $this->database->getReference('owners/owner_' . $ownerId->id)
            ->update(['approve' => 1, 'updated_at' => Database::SERVER_TIMESTAMP]);
    

        // $title = custom_trans('driver_approved', [], $ownerId->user->lang);
        // $body = custom_trans('driver_approved_body', [], $ownerId->user->lang);
    
        // dispatch(new SendPushNotification($ownerId->user, $title, $body));

        $notification = \DB::table('notification_channels')
                ->where('topics', 'Driver Account Approval') // Match the correct topic
                ->first();

            //    send push notification 
                if ($notification && $notification->push_notification == 1) {
                     // Determine the user's language or default to 'en'
                    $userLang = $ownerId->user->lang ?? 'en';
                    // dd($userLang);
    
                    // Fetch the translation based on user language or fall back to 'en'
                    $translation = \DB::table('notification_channels_translations')
                        ->where('notification_channel_id', $notification->id)
                        ->where('locale', $userLang)
                        ->first();
    
                    // If no translation exists, fetch the default language (English)
                    if (!$translation) {
                        $translation = \DB::table('notification_channels_translations')
                            ->where('notification_channel_id', $notification->id)
                            ->where('locale', 'en')
                            ->first();
                    }            
                    
                    $title =  $translation->push_title ?? $notification->push_title;
                    $body = strip_tags($translation->push_body ?? $notification->push_body);
                    dispatch(new SendPushNotification($ownerId->user, $title, $body));
                }


            return response()->json([
                'successMessage' => 'Owner  Approved successfully',
            ]);

        }else{
            // dd("Else ");

            return response()->json([
                'failureMessage' => 'Please Upload All Documents',
            ]);

        }
// dd($ownerId);

    }
    public function ownerPaymentHistory() {
        return Inertia::render('pages/manage_owners/owner_payment_history');
    }
//withdrwal request List
    public function WithdrawalRequestOwnersIndex()
    {
        return Inertia::render('pages/withdrawal_request_owners/index',['app_for'=>env("APP_FOR"),]);
    }

    public function WithdrawalRequestOwnersViewDetails(Owner $owner)
    {
        $walletBalance = $owner->ownerWalletDetail ? $owner->ownerWalletDetail->amount_balance : 0;
// dd($owner->ownerWalletDetail);
        $bankDetails = [
            'account_holder_name' => $owner->name,
        ];

        $methods = Method::with('fields')->get(); // Fetch all methods with their fields
        $bankInfos = $owner->bankInfoDetail;
// dd($bankInfos);
        $formattedBankInfos = $methods->map(function ($method) use ($bankInfos) {
            $fields = $method->fields->map(function ($field) use ($bankInfos) {
                $info = $bankInfos->firstWhere('field_id', $field->id);

                return [
                    'field_name' => $field->input_field_name,
                    'value' => $info->value ?? null,
                ];
            });

            return [
                'method_name' => $method->method_name,
                'fields' => $fields,
            ];
        });

        // dd($formattedBankInfos);

        return Inertia::render('pages/withdrawal_request_owners/view_in_detail', [
            'app_for' => env("APP_FOR"),
            'walletBalance' => $walletBalance,
            'bankDetails' => $bankDetails,
            'owner_id' => $owner->id,
            'formattedBankInfos' => $formattedBankInfos,
        ]);
    }

    public function WithdrawalRequestOwnersList(QueryFilterContract $queryFilter)
    {


        $query = WalletWithdrawalRequest::whereHas('ownerDetail.user',function($query){
            $query->companyKey();
            })->orderBy('created_at','desc')->with('ownerDetail');


        $results =  $queryFilter->builder($query)->customFilter(new OwnerFilter())->paginate();
        $items = fractal($results->items(), new WalletWithdrawalRequestsTransformer)->toArray();
        $results->setCollection(collect($items['data']));
        // dd($results);

        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);  
    }   
    //WithdrawalRequestAmount 
    public function WithdrawalRequestAmount(QueryFilterContract $queryFilter, Owner $owner_id)
    {
        // Debugging driver_id for confirmation
        //dd($driver_id);

        $query = WalletWithdrawalRequest::whereHas('ownerDetail.user', function($query) {
            $query->companyKey();
        })
        ->where('owner_id', $owner_id->id) // Filter by driver_id
        ->orderBy('created_at', 'desc')
        ->with('ownerDetail');

        $results = $queryFilter->builder($query)->customFilter(new OwnerFilter())->paginate();
        $items = fractal($results->items(), new WalletWithdrawalRequestsTransformer)->toArray();
        $results->setCollection(collect($items['data']));

        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);
    }
    public function updatePaymentStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:wallet_withdrawal_requests,id',
            'status' => 'required|in:approved,declined',
        ]);

        $wallet_withdrawal_request = WalletWithdrawalRequest::findOrFail($request->id);

        if ($request->status === 'approved') {
            // Handle approval logic
            $owner_wallet = OwnerWallet::firstOrCreate(['user_id' => $wallet_withdrawal_request->owner_id]);
            $owner_wallet->amount_spent += $wallet_withdrawal_request->requested_amount;
            $owner_wallet->amount_balance -= $wallet_withdrawal_request->requested_amount;
            $owner_wallet->save();

            $wallet_withdrawal_request->ownerDetail->ownerWalletHistoryDetail()->create([
                'amount' => $wallet_withdrawal_request->requested_amount,
                'transaction_id' => str_random(6),
                'remarks' => WalletRemarks::WITHDRAWN_FROM_WALLET,
                'is_credit' => false,
            ]);

            $wallet_withdrawal_request->status = 1; // Approved

            $user = $owner_wallet->owner->user;
            // $title = custom_trans('payment_credited',[],$user->lang);
            // $body = custom_trans('payment_credited_body',[],$user->lang);
            // $push_data = ['notification_enum'=>"payment_credited"];
        
            // dispatch(new SendPushNotification($user, $title, $body,$push_data));

            $notification = \DB::table('notification_channels')
                ->where('topics', 'Driver Withdrawal Request Approval') // Match the correct topic
                ->first();

            //    send push notification 
                if ($notification && $notification->push_notification == 1) {
                     // Determine the user's language or default to 'en'
                    $userLang = $user->lang ?? 'en';
                    // dd($userLang);
    
                    // Fetch the translation based on user language or fall back to 'en'
                    $translation = \DB::table('notification_channels_translations')
                        ->where('notification_channel_id', $notification->id)
                        ->where('locale', $userLang)
                        ->first();
    
                    // If no translation exists, fetch the default language (English)
                    if (!$translation) {
                        $translation = \DB::table('notification_channels_translations')
                            ->where('notification_channel_id', $notification->id)
                            ->where('locale', 'en')
                            ->first();
                    }            
                    
                    $title =  $translation->push_title ?? $notification->push_title;
                    $body = strip_tags($translation->push_body ?? $notification->push_body);
            $push_data = ['notification_enum'=>"payment_credited"];
                    dispatch(new SendPushNotification($user, $title, $body, $push_data));
                }

        } elseif ($request->status === 'declined') {
            $wallet_withdrawal_request->status = 2; // Declined

            $owner_wallet = OwnerWallet::firstOrCreate(['user_id' => $wallet_withdrawal_request->owner_id]);


            $user = $owner_wallet->driver->user;
            // $title = custom_trans('payment_declained',[],$user->lang);
            // $body = custom_trans('payment_declained_body',[],$user->lang);
            // $push_data = ['notification_enum'=>"payment_declained"];
        
            // dispatch(new SendPushNotification($user, $title, $body,$push_data));

            $notification = \DB::table('notification_channels')
                ->where('topics', 'Driver Withdrawal Request Decline') // Match the correct topic
                ->first();

            //    send push notification 
                if ($notification && $notification->push_notification == 1) {
                     // Determine the user's language or default to 'en'
                    $userLang = $user->lang ?? 'en';
                    // dd($userLang);
    
                    // Fetch the translation based on user language or fall back to 'en'
                    $translation = \DB::table('notification_channels_translations')
                        ->where('notification_channel_id', $notification->id)
                        ->where('locale', $userLang)
                        ->first();
    
                    // If no translation exists, fetch the default language (English)
                    if (!$translation) {
                        $translation = \DB::table('notification_channels_translations')
                            ->where('notification_channel_id', $notification->id)
                            ->where('locale', 'en')
                            ->first();
                    }            
                    
                    $title =  $translation->push_title ?? $notification->push_title;
                    $body = strip_tags($translation->push_body ?? $notification->push_body);
                     $push_data = ['notification_enum'=>"payment_declained"];
                    dispatch(new SendPushNotification($user, $title, $body,$push_data));
                }

        }

        $wallet_withdrawal_request->payment_status = $request->status;
        $wallet_withdrawal_request->save();

        return response()->json([
            'successMessage' => 'Owner payment status updated successfully.',
        ]);
    }

    public function deletedOwner()
    {
        return Inertia::render('pages/manage_owners/deletedIndex',['app_for'=>env("APP_FOR")]);

    }
    public function deletedList(QueryFilterContract $queryFilter, Request $request)
    {
        $query = User::belongsToRole('owner')->where('is_deleted_at', '!=', null);
        // $query = Owner::whereHas('user', function ($query) {
        //     $query->belongsToRole('owner')->whereNotNull('is_deleted_at');
        // })->orderBy('created_at', 'DESC');
        // $user->companyName = $user->owner->comapny_name;
        $results = $queryFilter->builder($query)->customFilter(new CommonMasterFilter)->paginate();

        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);
    }

    public function restoreOwner($id)
    {
        $user = User::withTrashed()->find($id);
        // $owner = Owner::withTrashed()->whereHas('user', function ($query) {
        //     $query->belongsToRole('owner')->whereNotNull('is_deleted_at');
        // })->find($id);
   
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
    
        $user->update(['is_deleted_at' => null,'active'=>1]);
    
        return response()->json(['message' => 'User restored successfully']);
    }
}
