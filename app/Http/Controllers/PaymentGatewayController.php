<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use App\Models\Admin\Setting;
use Illuminate\Http\Request;
use App\Models\ThirdPartySetting;

class PaymentGatewayController  extends Controller
{
    public function index() 
    {
        $settings = ThirdPartySetting::where('module', 'payment')
            ->pluck('value', 'name')
            ->toArray();
    // dd($settings);
        // Transform settings into a structured object
        $formattedSettings = [
            'enable_paystack' => filter_var($settings['enable_paystack'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'paystack_test_publish_key' => $settings['paystack_test_publish_key'] ?? '',
            'paystack_production_publish_key' => $settings['paystack_production_publish_key'] ?? '',
            'paystack_test_secret_key' => $settings['paystack_test_secret_key'] ?? '',
            'paystack_production_secret_key' => $settings['paystack_production_secret_key'] ?? '',
            'paystack_environment' => $settings['paystack_environment'] ?? '',
    
            'enable_cashfree' => filter_var($settings['enable_cashfree'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'cash_free_environment' => $settings['cash_free_environment'] ?? '',
            'cash_free_secret_key' => $settings['cash_free_secret_key'] ?? '',
            'cash_free_production_secret_key' => $settings['cash_free_production_secret_key'] ?? '',
            'cash_free_app_id' => $settings['cash_free_app_id'] ?? '',
            'cash_free_production_app_id' => $settings['cash_free_production_app_id'] ?? '',
    
            'enable_mercadopago' => filter_var($settings['enable_mercadopago'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'mercadopago_environment' => $settings['mercadopago_environment'] ?? '',
            'mercadopago_test_public_key' => $settings['mercadopago_test_public_key'] ?? '',
            'mercadopago_live_public_key' => $settings['mercadopago_live_public_key'] ?? '',
            'mercadopago_test_access_token' => $settings['mercadopago_test_access_token'] ?? '',
            'mercadopago_live_access_token' => $settings['mercadopago_live_access_token'] ?? '',
    
            'enable_stripe' => filter_var($settings['enable_stripe'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'enable_stripe_authorization' => filter_var($settings['enable_stripe_authorization'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'stripe_environment' => $settings['stripe_environment'] ?? '',
            'stripe_test_secret_key' => $settings['stripe_test_secret_key'] ?? '',
            'stripe_live_secret_key' => $settings['stripe_live_secret_key'] ?? '',
            'stripe_test_publishable_key' => $settings['stripe_test_publishable_key'] ?? '',
            'stripe_live_publishable_key' => $settings['stripe_live_publishable_key'] ?? '',
    
            'enable_flutterwave' => filter_var($settings['enable_flutterwave'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'flutter_wave_environment' => $settings['flutter_wave_environment'] ?? '',
            'flutter_wave_test_secret_key' => $settings['flutter_wave_test_secret_key'] ?? '',
            'flutter_wave_production_secret_key' => $settings['flutter_wave_production_secret_key'] ?? '',
    
            'enable_razorpay' => filter_var($settings['enable_razorpay'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'razor_pay_environment' => $settings['razor_pay_environment'] ?? '',
            'razor_pay_test_api_key' => $settings['razor_pay_test_api_key'] ?? '',
            'razor_pay_live_api_key' => $settings['razor_pay_live_api_key'] ?? '',
            'razor_pay_secrect_key' => $settings['razor_pay_secrect_key'] ?? '',
            'razor_pay_test_secrect_key' => $settings['razor_pay_test_secrect_key'] ?? '',
    
            'enable_khalti' => filter_var($settings['enable_khalti'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'khalti_pay_environment' => $settings['khalti_pay_environment'] ?? '',
            'khalti_pay_test_api_key' => $settings['khalti_pay_test_api_key'] ?? '',
            'khalti_pay_live_api_key' => $settings['khalti_pay_live_api_key'] ?? '',

            // 'enable_easypaisa' => filter_var($settings['enable_easypaisa'] ?? false, FILTER_VALIDATE_BOOLEAN),
            // 'easypay_environment' => $settings['easypay_environment'] ?? '',
            // 'easypaisa_store_id' => $settings['easypaisa_store_id'] ?? '',
            // 'easypaisa_hash_key' => $settings['easypaisa_hash_key'] ?? '',

            'enable_xendit' => filter_var($settings['enable_xendit'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'xendi_pay_environment' => $settings['xendi_pay_environment'] ?? '',
            'xendi_pay_test_api_key' => $settings['xendi_pay_test_api_key'] ?? '',
            // 'xendi_pay_test_secrect_key' => $settings['xendi_pay_test_secrect_key'] ?? '',
            'xendit_pay_live_api_key' => $settings['xendit_pay_live_api_key'] ?? '',
            // 'xendit_pay_secrect_key' => $settings['xendit_pay_secrect_key'] ?? '',


            'enable_flexpaie' => filter_var($settings['enable_flexpaie'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'flexpaie_environment' => $settings['flexpaie_environment'] ?? '',
            'flexpaie_test_bearer_token' => $settings['flexpaie_test_bearer_token'] ?? '',
            'flexpaie_production_bearer_token' => $settings['flexpaie_production_bearer_token'] ?? '',
        ];
    
        return Inertia::render('pages/payment_gateway/index', [
            'app_for'=>env('APP_FOR'),
            'settings' => $formattedSettings,
        ]);
    }
    

    public function update(Request $request)
    {
        // dd($request->all());
        $settings = $request->validate([
            'enable_paystack' => "required",
            'paystack_environment' => "required",
            'paystack_test_secret_key' => "sometimes",
            'paystack_production_secret_key' => "sometimes",
            'paystack_test_publish_key' => "sometimes",
            'paystack_production_publish_key' => "sometimes",

            'enable_cashfree' => "required",
            'cash_free_environment' => "required",
            'cash_free_secret_key' => "sometimes",
            'cash_free_production_secret_key' => "sometimes",
            'cash_free_app_id' => "sometimes",
            'cash_free_production_app_id' => "sometimes",
    
            'enable_mercadopago' => "required",
            'mercadopago_environment' => "required",
            'mercadopago_test_public_key' => "sometimes",
            'mercadopago_live_public_key' => "sometimes",
            'mercadopago_test_access_token' => "sometimes",
            'mercadopago_live_access_token' => "sometimes",
    
            'enable_stripe' => "required",
            'enable_stripe_authorization' => "required",
            'stripe_environment' => "required",
            'stripe_test_secret_key' => "sometimes",
            'stripe_live_secret_key' => "sometimes",
            'stripe_test_publishable_key' => "sometimes",
            'stripe_live_publishable_key' => "sometimes",
    
            'enable_flutterwave' => "required",
            'flutter_wave_environment' => "required",
            'flutter_wave_test_secret_key' => "sometimes",
            'flutter_wave_production_secret_key' => "sometimes",
    
            'enable_razorpay' => "required",
            'razor_pay_environment' => "required",
            'razor_pay_test_api_key' => "sometimes",
            'razor_pay_live_api_key' => "sometimes",
            'razor_pay_secrect_key' => "sometimes",
            'razor_pay_test_secrect_key' => "sometimes",
    
            'enable_khalti' => "required",
            'khalti_pay_environment' => "required",
            'khalti_pay_test_api_key' => "sometimes",
            'khalti_pay_live_api_key' => "sometimes",

            'enable_xendit' => "required",
            'xendi_pay_environment' => "sometimes",
            'xendi_pay_test_api_key' => "sometimes",
            // 'xendi_pay_test_secrect_key' => "sometimes",
            'xendit_pay_live_api_key' => "sometimes",
            // 'xendit_pay_secrect_key' => "sometimes",

            // 'easypay_environment' => "required",
            // 'easypaisa_store_id' => "sometimes",
            // 'easypaisa_hash_key' => "sometimes",
            
            'enable_flexpaie' => "required",
            'flexpaie_environment' => "required",
            'flexpaie_test_bearer_token' => "sometimes",
            'flexpaie_production_bearer_token' => "sometimes",
        ]);

        // dd($settings);

        ThirdPartySetting::where('module', 'payment')->delete(); // corrected delete command


        foreach ($settings as $key => $setting) 
        {
            // dd($setting);

            ThirdPartySetting::create(['name' => $key, 'value' => $setting, 'module' => 'payment']);                 
        }

        return response()->json(['message' => 'Sms  Destails updated successfully'], 201);

    }

}
