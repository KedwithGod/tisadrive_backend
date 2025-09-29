<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use App\Models\Admin\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Base\Services\ImageUploader\ImageUploaderContract;
use App\Http\Controllers\Api\V1\BaseController;
use App\Models\Country;

class SettingController extends BaseController
{

    protected $imageUploader;

    protected $settings;



    public function __construct(Setting $settings, ImageUploaderContract $imageUploader)
    {
        $this->settings = $settings;
        $this->imageUploader = $imageUploader;
    }

    public function generalSettings() 
    {
        $settings = Setting::where('category', 'general')->get()->pluck('value', 'name')->toArray();
        // dd($settings);

        $logo = Setting::where('name', 'logo')->first();

        $favicon = Setting::where('name', 'favicon')->first();

        $loginbg = Setting::where('name', 'loginbg')->first();
        $owner_loginbg = Setting::where('name', 'owner_loginbg')->first();

        $loginbgURL = $loginbg->getLoginBgAttribute();

        $faviconURL = $favicon-> getFavIconAttribute();

        $logoURL = $logo->getAppLogoAttribute();


        return Inertia::render('pages/general_settings/index', [
            'settings' => $settings,
            'loginbgURL' => $loginbgURL,
            'faviconURL' => $faviconURL,
            'app_for'=>env('APP_FOR'),
            'logoURL' => $logoURL,
        ]);
    }

    public function updateStatus(Request $request)
    {
        $settings = Setting::where('category', 'general')->where('name', $request->id)->first();

        if($settings){
            $settings->update(['value'=>$request->status]);

        }else{
            Setting::create(['category'=>'general','name'=>$request->id,'value'=>$request->status]);

        }
        // dd($request->all());
       
        return response()->json([
            'successMessage' => 'status updated successfully',
        ]);
    }
    public function updateGeneralSettings(Request $request) 
    {
    // Extract settings from validated data
    $settings = $request->only([        
        'nav_color',
        'sidebar_color',
        'sidebar_text_color',
        'app_name',
        'currency_code',
        'currency_symbol',
        'contact_us_mobile1',
        'contact_us_mobile2',
        'contact_us_link',
        'default_latitude',
        'default_longitude',
        'admin_login',
        'owner_login',
        'dispatcher_login',
        'user_login',
        'footer_content1',
        'footer_content2',
        'android_user',
        'android_driver',
        'ios_user',
        'ios_driver',
    ]);



    // Check if files are present and handle them
    // if ($request->hasFile('logo')) {
    //     $uploadedFile = $request->file('logo');
    //     $settings['logo'] = $this->imageUploader->file($uploadedFile)->saveSystemAdminLogo();
    // }

    if ($uploadedFile = $request->file('logo')) {
        $settings['logo'] = $this->imageUploader->file($uploadedFile)
            ->saveSystemAdminLogo();
    }

    if ($uploadedFile = $request->file('favicon')) {
        $settings['favicon'] = $this->imageUploader->file($uploadedFile)
            ->saveSystemAdminLogo();
    }

    if ($uploadedFile = $request->file('loginbg')) {
        $settings['loginbg'] = $this->imageUploader->file($uploadedFile)
            ->saveSystemAdminLogo();
    }

    if ($uploadedFile = $request->file('owner_loginbg')) {
        $settings['owner_loginbg'] = $this->imageUploader->file($uploadedFile)
            ->saveSystemAdminLogo();
    }
    // if ($request->hasFile('favicon')) {
    //     $uploadedFile = $request->file('favicon');
    //     $settings['favicon'] = $this->imageUploader->file($uploadedFile)->saveSystemAdminLogo();
    // }
    // if ($request->hasFile('loginbg')) {
    //     $uploadedFile = $request->file('loginbg');
    //     $settings['loginbg'] = $this->imageUploader->file($uploadedFile)->saveSystemAdminLogo();
    // }

    foreach($settings as $key=>$setting)
    {
        Setting::where('name',$key)->update(['value'=>$setting]);
    }

    // Update settings
        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Settings updated successfully.',
            'settings' => $settings,
        ], 200);
    }

    
    public function customizationSettings() 
    {
        $countries = Country::where('active', true)->whereNotNull('code')->whereRaw('LENGTH(code) > 0')->pluck('code', 'id')->toArray();
        $settings = Setting::where('category', 'customization_settings')->get()->pluck('value', 'name')->toArray();
        return Inertia::render('pages/customization_settings/index',
        [ 'settings' => $settings,'countries' => $countries,'app_for'=>env('APP_FOR')]);
    }
    
    public function updateCustomizationStatus(Request $request)
    {
        $settings = Setting::where('category', 'customization_settings')->where('name', $request->id)->first();

        if($settings){
            $settings->update(['value'=>$request->status, 'category'=>'customization_settings']);
            

        }
        // dd($settings);
       
        return response()->json([
            'successMessage' => 'status updated successfully',
        ]);
    }

    public function updateCustomizationSettings(Request $request) 
    {
        // dd($request->all());
    // Extract settings from validated data
    $settings = request()->only([
        'enable_vase_map',
        'enable_modules_for_applications',        
        'default_country_code_for_mobile_app',
        'default_country_code_for_mobile_app',
        'enable_shipment_load_feature',
        'show_outstation_ride_feature',
        'show_delivery_outstation_ride_feature',
        'enable_shipment_unload_feature',
        'enable_digital_signature',
        'enable_pet_preference_for_user',
        'enable_document_auto_approval',
        'enable_luggage_preference_for_user',
        // 'enable_my_route_booking_feature',
        'enable_country_restrict_on_map',
        'show_wallet_feature_on_mobile_app',
        'show_wallet_feature_on_mobile_app_driver',
        'show_wallet_feature_on_mobile_app_for_owner',
        'show_instant_ride_feature_on_mobile_app',
        'show_owner_module_feature_on_mobile_app',
        'show_wallet_money_transfer_feature_on_mobile_app',
        'show_wallet_money_transfer_feature_on_mobile_app_for_driver',
        'show_wallet_money_transfer_feature_on_mobile_app_for_owner',
        'show_email_otp_feature_on_mobile_app',
        // 'show_bank_info_feature_on_mobile_app',
        'show_rental_ride_feature',
        'show_delivery_rental_ride_feature',
        'show_card_payment_feature',
        'show_taxi_rental_ride_feature',
        'show_ride_otp_feature',
        'show_ride_later_feature',
        'show_ride_without_destination',
        'enable_web_booking_feature',
        'show_incentive_feature_for_driver',
        'enable_landing_site',
        'enable_sub_vehicle_feature',
        'show_driver_level_feature',
        // 'enable_driver_tips_feature',
        'enable_driver_profile_disapprove_on_update',
        'enable_support_ticket_feature',
        'enable_map_appearance_change_on_mobile_app',
        'enable_eta_total_update',
        'enable_driver_leaderboard_feature',
    ]);
   


   foreach($settings as $key=>$setting)
    {
        Setting::where('name',$key)->update(['value'=>$setting, 'category'=>'customization_settings']);
    }

    // Update settings
        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Settings updated successfully.',
            'settings' => $settings,
        ], 200);
    }

    public function transportRideSettings() 
    {
        $settings = Setting::where('category', 'trip_settings')->get()->pluck('value', 'name')->toArray();
// dd($settings);
        return Inertia::render('pages/transport_ride_settings/index', [
            'settings' => $settings,
            'app_for'=>env('APP_FOR'),
        ]);
    }
    public function updateTransportSettings(Request $request) 
    {
        // dd($request->all());
    // Extract settings from validated data
    $settings = request()->only([
        'trip_dispatch_type',
        'driver_search_radius',
        'maximum_time_for_accept_reject_bidding_ride',
        'user_can_make_a_ride_after_x_miniutes',
        'maximum_time_for_find_drivers_for_bitting_ride',
        'minimum_time_for_search_drivers_for_schedule_ride',
        'minimum_time_for_starting_trip_drivers_for_schedule_ride',
        'maximum_time_for_find_drivers_for_regular_ride',
        'trip_accept_reject_duration_for_driver',
        'how_many_times_a_driver_can_enable_the_my_route_booking_per_day',
        // 'bidding_low_percentage',
        // 'bidding_high_percentage',
        // 'bidding_amount_increase_or_decrease',
        'can_round_the_bill_values',
        // 'minimum_trip_distane'
    ]);
   


   foreach($settings as $key=>$setting)
    {
        Setting::where('name',$key)->update(['value'=>$setting]);
    }

    // Update settings
        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Settings updated successfully.',
            'settings' => $settings,
        ], 200);
    }

    public function updateTransportStatus(Request $request)
    {
        $settings = Setting::where('category', 'trip_settings')->where('name', $request->id)->first();
        // dd($request);

        if($settings){
            $settings->update(['value'=>$request->status]);

        }
       
        return response()->json([
            'successMessage' => 'status updated successfully',
        ]);
    }

    public function bidRideSettings() 
    {
        $settings = Setting::where('category', 'trip_settings')->get()->pluck('value', 'name')->toArray();
// dd($settings);
        return Inertia::render('pages/bid_ride_settings/index', [
            'app_for'=>env('APP_FOR'),
            'settings' => $settings
        ]);
    }
    public function updateBidSettings(Request $request) 
    {
        // dd($request->all());
    // Extract settings from validated data
    $settings = request()->only([
        'bidding_low_percentage',
        'bidding_high_percentage',
        'bidding_amount_increase_or_decrease',
        'user_bidding_low_percentage',
        'user_bidding_high_percentage',
        'user_bidding_amount_increase_or_decrease',
    ]);
   


   foreach($settings as $key=>$setting)
    {
        Setting::where('name',$key)->update(['value'=>$setting]);
    }

    // Update settings
        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Settings updated successfully.',
            'settings' => $settings,
        ], 200);
    }
    public function walletSettings() 
    {
        $driver_wallet = Setting::where('category', 'Wallet')->where('name', 'driver_wallet_minimum_amount_to_get_an_order')->first();
        $minimum_wallet = Setting::where('category', 'Wallet')->where('name', 'minimum_wallet_amount_for_transfer')->first();
        $owner_wallet = Setting::where('category', 'Wallet')->where('name', 'owner_wallet_minimum_amount_to_get_an_order')->first();
        $minimum_wallet_add = Setting::where('category', 'Wallet')->where('name', 'minimum_amount_added_to_wallet')->first();
    
        return Inertia::render('pages/wallet_settings/index', [
            'app_for'=>env('APP_FOR'),
            'driver_wallet' => $driver_wallet,
            'minimum_wallet' => $minimum_wallet,
            'owner_wallet' => $owner_wallet, 
            'minimum_wallet_add' => $minimum_wallet_add, 
        ]);
    }
    public function updateWalletSettings(Request $request)
    {
        $settings = $request->only([
            'driver_wallet_minimum_amount_to_get_an_order',
            'minimum_wallet_amount_for_transfer',
            'owner_wallet_minimum_amount_to_get_an_order',
            'minimum_amount_added_to_wallet',
        ]);
    
        // Delete existing wallet settings
        Setting::where('category', 'Wallet')->delete();
    
        // Insert new wallet settings
        foreach ($settings as $key => $setting) {
            Setting::create([
                'name' => $key,
                'field' => 'text',
                'value' => $setting,
                'category' => 'Wallet'
            ]);
        }

        return response()->json([
            'successMessage' => 'Settings updated successfully.',
        ], 201);

    }
    public function referralSettings() 
    {
        $enable_user_referral_earnings = Setting::where('category', 'referral')
            ->where('name', 'enable_user_referral_earnings')
            ->first();
    
        $enable_driver_referral_earnings = Setting::where('category', 'referral')
            ->where('name', 'enable_driver_referral_earnings')
            ->first();
    
        $referral_commission_amount_for_user = Setting::where('category', 'referral')
            ->where('name', 'referral_commission_amount_for_user')
            ->first();
    
        $referral_commission_amount_for_driver = Setting::where('category', 'referral')
            ->where('name', 'referral_commission_amount_for_driver')
            ->first();
    
        $referral_commission_amount_for_driver_reffering_a_user = Setting::where('category', 'referral')
            ->where('name', 'referral_commission_amount_for_driver_reffering_a_user')
            ->first();
    
        $referral_commission_amount_for_user_reffering_a_driver = Setting::where('category', 'referral')
            ->where('name', 'referral_commission_amount_for_user_reffering_a_driver')
            ->first();
    
        // Convert the settings to boolean
        $enable_user_referral_earnings_value = $enable_user_referral_earnings && $enable_user_referral_earnings->value === "1";
        $enable_driver_referral_earnings_value = $enable_driver_referral_earnings && $enable_driver_referral_earnings->value === "1";
    // dd($enable_user_referral_earnings_value);
        return Inertia::render('pages/referral_settings/index', [
            'enable_user_referral_earnings' => $enable_user_referral_earnings_value,
            'enable_driver_referral_earnings' => $enable_driver_referral_earnings_value,
            'referral_commission_amount_for_user' => $referral_commission_amount_for_user,
            'app_for'=>env("APP_FOR"),
            'referral_commission_amount_for_driver' => $referral_commission_amount_for_driver,
            'referral_commission_amount_for_driver_reffering_a_user' => $referral_commission_amount_for_driver_reffering_a_user,
            'referral_commission_amount_for_user_reffering_a_driver' => $referral_commission_amount_for_user_reffering_a_driver,
        ]);
    }
    
    public function updateReferralSettings(Request $request)
    {
        // dd($request->all());
        $settings = $request->only([
            'referral_commission_amount_for_user',
            'referral_commission_amount_for_driver',
            'referral_commission_amount_for_driver_reffering_a_user',
            'referral_commission_amount_for_user_reffering_a_driver',
            'enable_driver_referral_earnings',
            'enable_user_referral_earnings'
        ]);
    
        // Delete existing wallet settings
        Setting::where('category', 'referral')->delete();
    
        // Insert new wallet settings
        foreach ($settings as $key => $setting) {
            Setting::create([
                'name' => $key,
                'field' => 'text',
                'value' => $setting,
                'category' => 'referral'
            ]);
        }

    }
    public function updateReferralToggle(Request $request)
    {

        // dd($request->all());

        $settings = Setting::where('category', 'referral')->where('name', $request->key)->first();

        if($settings)
        {
            $settings->update(['value'=>$request->enabled]);

        }else{
           $settings =  Setting::create(['category'=> 'referral', 'name'=> $request->key,'value'=>$request->enabled]);

            // dd($settings);
        }


        return response()->json([
            'successMessage' => 'Settings updated successfully.',
        ], 200);
    }
    public function peakZoneSettings()
    {
        $settings = Setting::where('category', 'peak_zone_settings')->get()->pluck('value', 'name')->toArray();
        return Inertia::render('pages/peak_zone_setting/index',
        [ 'settings' => $settings,'app_for'=>env('APP_FOR')]);
    }
    public function updatePeakZoneSettings(Request $request) 
    {
    // Extract settings from validated data
    $settings = request()->only([
        'enable_peak_zone_feature',
        'peak_zone_radius',
        'peak_zone_duration',
        'peak_zone_history_duration',
        'peak_zone_ride_count',
        'distance_price_percentage',
    ]);
   


   foreach($settings as $key=>$setting)
    {
        Setting::where('name',$key)->update(['value'=>$setting]);
    }

    // Update settings
        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Settings updated successfully.',
            'settings' => $settings,
        ], 200);
    }

    public function tipSettings() 
    {
        $minimum_tip_add = Setting::where('category', 'tip_settings')->where('name', 'minimum_tip_amount')->first();
        $settings = Setting::where('category', 'tip_settings')->get()->pluck('value', 'name')->toArray();
    
        return Inertia::render('pages/tip_settings/index', [
            'app_for'=>env('APP_FOR'),
            'minimum_tip_add' => $minimum_tip_add,
            'settings' => $settings, 
        ]);
    }
    public function updateTipStatus(Request $request)
    {
        $settings = Setting::where('name', $request->id)->first();
        // dd($request);

        if($settings){
            if($settings->category !== 'tip_settings'){
                $settings->update(['category'=> 'tip_settings']);
            }
            $settings->update(['value'=>$request->status]);

        }
       
        return response()->json([
            'successMessage' => 'status updated successfully',
        ]);
    }
    public function updateTipSettings(Request $request)
    {
        $settings = $request->only([
            'minimum_tip_amount',
            'enable_driver_tips_feature',
        ]);
    
        // Delete existing wallet settings
        Setting::where('category', 'tip_settings')->delete();
    
        // Insert new wallet settings
        foreach ($settings as $key => $setting) {
            Setting::create([
                'name' => $key,
                'field' => 'text',
                'value' => $setting,
                'category' => 'tip_settings'
            ]);
        }

        return response()->json([
            'successMessage' => 'Settings updated successfully.',
        ], 201);

    }

}
