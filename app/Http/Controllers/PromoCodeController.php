<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Admin\Promo;
use App\Models\Admin\PromoCodeUser;
use App\Models\Admin\ServiceLocation;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Base\Filters\Admin\PromoFilter;


use Illuminate\Http\Request;

class PromoCodeController extends Controller
{
    public function index() 
    {

        return Inertia::render('pages/promo_code/index');
    }

    public function create() 
    {
        $serviceLocations = ServiceLocation::where('active', true)->get();
    
        return inertia('pages/promo_code/create', [
            'serviceLocations' => $serviceLocations,
        ]);
    }
    public function store(Request $request)
    {
        // dd($request->all());
        // Validate incoming request data
        $validatedData = $request->validate([
            'service_location_id' => 'required|string',
            'transport_type' => 'required|string',
            'code' => 'required|string',
            'minimum_trip_amount' => 'required|numeric',
            'maximum_discount_amount' => 'required|numeric',
            'discount_percent' => 'required|numeric',
            'uses_per_user' => 'required|numeric',
            'user_specific' => 'required|boolean',
            'date' => 'required|string',
        ]);
        if($validatedData['user_specific']){
            if(count($request->user_id) == 0){
                return response()->json(['message'=>'User must be selected'],422);
            }
        }

        $dateRange = explode(' to ', $validatedData['date']);
        $fromDate = Carbon::parse(trim($dateRange[0]))->startOfDay()->toDateTimeString();
        $toDate = Carbon::parse(trim($dateRange[1]))->endOfDay()->toDateTimeString();
        $promo_params = [
            'transport_type' => $validatedData['transport_type'],
            'code' => $validatedData['code'],
            'minimum_trip_amount' => $validatedData['minimum_trip_amount'],
            'maximum_discount_amount' => $validatedData['maximum_discount_amount'],
            'discount_percent' => $validatedData['discount_percent'],
            'uses_per_user' => $validatedData['uses_per_user'],
            'service_location_id' => $validatedData['service_location_id'],
            'user_specific' => $validatedData['user_specific'],
            'from' => $fromDate,
            'to' => $toDate,
        ];
        $promoCode = Promo::create($promo_params);

        if($validatedData['user_specific']){
            foreach ($request->user_id as $user_id)
            {
                $promo_user_params['promo_code_id'] = $promoCode->id;
                $promo_user_params['user_id'] = $user_id;
                $promo_user_params['service_location_id'] = $request->service_location_id;
                PromoCodeUser::create($promo_user_params);
            }
        }

        // Optionally, you can return a response
        return response()->json(['message' => 'Promo code created successfully.'], 201);
    }   
    
    public function list(QueryFilterContract $queryFilter, Request $request)
    {
        $query = Promo::query();
// dd("ssss");
        $results = $queryFilter->builder($query)->customFilter(new PromoFilter)->paginate();

        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);
    }

    public function userList(Request $request)
    {
        // dd($request->all());
        $results = User::belongsToRole('user')
                        ->where('name','LIKE', "%".$request->search."%")
                        ->orwhere('mobile','LIKE', "%".$request->search."%")->get();
        return response()->json([
            'results' => $results,
        ]);
    }
    public function edit($id)
    {
        $promo = Promo::find($id);

// dd($promo);

        $promo->user_id = $promo->promoCodeUsers()->pluck('user_id')->toArray();
        $users = User::whereIn('id',$promo->user_id)->get()->toArray();
        $serviceLocations = ServiceLocation::all(); // Fetch all service locations or as needed
        return inertia('pages/promo_code/create', [
            'promo' => $promo,
            'users' => $users,
            'serviceLocations' => $serviceLocations,
        ]);
    }
    

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'service_location_id' => 'required|string',
            'transport_type' => 'required|string',
            'code' => 'required|string',
            'minimum_trip_amount' => 'required|numeric',
            'maximum_discount_amount' => 'required|numeric',
            'discount_percent' => 'required|numeric',
            'uses_per_user' => 'required|numeric',
            'user_specific' => 'required|boolean',
            'date' => 'required|string',
        ]);
        if($validatedData['user_specific']){
            if(count($request->user_id) == 0){
                return response()->json(['message'=>'User must be selected'],422);
            }
        }

        $promoCode = Promo::findOrFail($id);
        // Parse date range
        $dateRange = explode(' to ', $validatedData['date']);
        $fromDate = Carbon::parse(trim($dateRange[0]))->startOfDay()->toDateTimeString();
        $toDate = Carbon::parse(trim($dateRange[1]))->endOfDay()->toDateTimeString();

        // Update promo code fields
        $promo_params = [
            'transport_type' => $validatedData['transport_type'],
            'code' => $validatedData['code'],
            'minimum_trip_amount' => $validatedData['minimum_trip_amount'],
            'maximum_discount_amount' => $validatedData['maximum_discount_amount'],
            'discount_percent' => $validatedData['discount_percent'],
            'uses_per_user' => $validatedData['uses_per_user'],
            'service_location_id' => $validatedData['service_location_id'],
            'from' => $fromDate,
            'to' => $toDate,
        ];
        $promoCode->update($promo_params);
        // dd($request->all());
        $promoCode->promoCodeUsers()->delete();

        foreach ($request->user_id as $user_id)
        {
            $promo_user_params['promo_code_id'] = $promoCode->id;
            $promo_user_params['service_location_id'] = $request->service_location_id;
            $promo_user_params['user_id'] = $user_id;
            PromoCodeUser::create($promo_user_params);
        }

        return response()->json(['message' => 'Promo code updated successfully.'], 200);
    }
    public function destroy(Promo $promo)
    {
        // $promo->promoServiceDetails()->delete();
        
        $promo->delete();

        return response()->json([
            'successMessage' => 'Promo Code deleted successfully',
        ]);
    }   
    public function updateStatus(Request $request)
    {
        // dd($request->all());
        Promo::where('id', $request->id)->update(['active'=> $request->status]);

        return response()->json([
            'successMessage' => 'Promo Code Status status updated successfully',
        ]);


    }


}