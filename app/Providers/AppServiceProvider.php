<?php

namespace App\Providers;

use Schema;
use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Validation\Factory as Validator;
use Illuminate\Pagination\Paginator;
use App\Models\Admin\LandingHeader;
use App\Models\Admin\LandingHome;
use Illuminate\Support\Facades\View;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Kreait\Firebase\Factory as Firebase;
use App\Models\ThirdPartySetting;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{

// use Braintree\Configuration as Braintree_Configuration;
    /**
     * Validator instance.
     *
     * @var \Illuminate\Contracts\Validation\Factory
     */
    protected $validator;

    /**
     * Bootstrap any application services.
     *
     * @param \Illuminate\Contracts\Validation\Factory $validator
     * @return void
     */
    public function boot(Validator $validator)
    {

        Inertia::share([
            'app_for' => env('APP_FOR')
        ]);
        
        $this->validator = $validator;

        Schema::defaultStringLength(191);

        $this->loadCustomValidators();

        Paginator::useBootstrap();

        if (Schema::hasTable('third_party_settings')) {
            $firebase_database_url = ThirdPartySetting::where('name','firebase_database_url')->pluck('value')->first();
        } else {
            // Handle missing table (e.g., log a warning or create the table)
            $firebase_database_url = 'https://your-firebase-db.firebaseio.com/';
        }

        $firebaseCredentialsPath = public_path('push-configurations/firebase.json'); // Get full path

        if(!$firebase_database_url){
            
            $firebase_database_url = 'https://your-firebase-db.firebaseio.com/';

        }


    if ($firebase_database_url) {
        
        config([
            'firebase.projects.app.credentials' => $firebaseCredentialsPath,
            'firebase.projects.app.database.url' => $firebase_database_url,
        ]);


        $firebase = (new Firebase)
            ->withServiceAccount($firebaseCredentialsPath)
            ->withDatabaseUri($firebase_database_url);
        
        app()->instance('firebase', $firebase);
    }

        // if (Schema::hasTable('landing_headers')) {
        //     $headers = LandingHeader::first();
        //     View::share('headers', $headers);
        // } else {
        //     View::share('headers', [
        //         'home' => 'Home',
        //         'driver' => 'Driver',
        //         'user' => 'User',
        //         'contact' => 'Contact',
        //         'locale' => 'en',
        //         'language' => 'English',
        //     ]);
        // }

        if (Schema::hasTable('settings')) {
            $supportTicket = Setting::whereName('enable_support_ticket_feature')->firstOrNew([]);
            view()->share('supportTicket', $supportTicket->value);

            $navs = Setting::whereName('nav_color')->firstOrNew([]);
            view()->share('navs', $navs);

            $side = Setting::whereName('sidebar_color')->firstOrNew([]);
            view()->share('side', $side);

            $side_txt = Setting::whereName('sidebar_text_color')->firstOrNew([]);
            view()->share('side_txt', $side_txt);

            $landing_header_bg_color = Setting::whereName('landing_header_bg_color')->firstOrNew([]);
            view()->share('landing_header_bg_color', $landing_header_bg_color);

            $landing_header_text_color = Setting::whereName('landing_header_text_color')->firstOrNew([]);
            view()->share('landing_header_text_color', $landing_header_text_color);

            $landing_header_active_text_color = Setting::whereName('landing_header_active_text_color')->firstOrNew([]);
            view()->share('landing_header_active_text_color', $landing_header_active_text_color);

            $landing_footer_bg_color = Setting::whereName('landing_footer_bg_color')->firstOrNew([]);
            view()->share('landing_footer_bg_color', $landing_footer_bg_color);

            $landing_footer_text_color = Setting::whereName('landing_footer_text_color')->firstOrNew([]);
            view()->share('landing_footer_text_color', $landing_footer_text_color);

            $footer_content1 = Setting::whereName('footer_content1')->firstOrNew([]);
            view()->share('footer_content1', $footer_content1->value);
            $footer_content2 = Setting::whereName('footer_content2')->firstOrNew([]);
            view()->share('footer_content2', $footer_content2->value);

            $logo = Setting::whereName('logo')->firstOrNew([]);
            view()->share('logo', !empty($logo->value) ? asset('storage/uploads/system-admin/logo/' . $logo->value) : asset('storage/uploads/system-admin/logo/rest.png')); 

            $favicon = Setting::whereName('favicon')->firstOrNew([]);
            view()->share('favicon', !empty($favicon->value) ? asset('storage/uploads/system-admin/logo/' . $favicon->value) : asset('storage/uploads/system-admin/logo/Restart user.jpg')); 

            $loginbg = Setting::whereName('loginbg')->firstOrNew([]);
            view()->share('loginbg', !empty($loginbg->value) ? asset('storage/uploads/system-admin/logo/' . $loginbg->value) : asset('storage/uploads/system-admin/logo/workspace.jpg')); 

            $owner_loginbg = Setting::whereName('owner_loginbg')->firstOrNew([]);
            view()->share('owner_loginbg', !empty($owner_loginbg->value) ? asset('storage/uploads/system-admin/logo/' . $owner_loginbg->value) : asset('storage/uploads/system-admin/logo/workspace.jpg')); 
        } else {

            view()->share('navs', "#0ab39c");

            view()->share('side', "#405189");

            view()->share('side_txt', "#a2a5af");

            view()->share('landing_header_bg_color', "#ffffff");
            view()->share('landing_header_text_color', "#212529");
            view()->share('landing_header_active_text_color', "#0ab39c");
            view()->share('landing_footer_bg_color', "#000000");
            view()->share('landing_footer_text_color', "#f1ffff");

            view()->share('logo', asset('storage/uploads/system-admin/logo/rest.png'));
            view()->share('favicon',asset('storage/uploads/system-admin/logo/Restart user.jpg'));
            view()->share('loginbg', asset('storage/uploads/system-admin/logo/workspace.jpg'));
        }


        if (Schema::hasTable('landing_headers')) {
            // $headers = LandingHeader::all();
            $headers = LandingHeader::all()->map(function ($header) {
                $header->header_logo_url = asset('storage/uploads/website/images/' . $header->header_logo);
                $header->footer_logo_url = asset('storage/uploads/website/images/' . $header->footer_logo);
                $enable_web_booking = Setting::whereName('enable_web_booking_feature')->firstOrNew([]);
                $header->enable_web_booking = $enable_web_booking->value  ? 1 : 0;
                $user_login_url = Setting::whereName('user_login')->firstOrNew([]);
                $header->userlogin = 'login/'.$user_login_url->value;
                return $header;
            });
            $locales = $headers->pluck('locale', 'id');
            View::share('headers', $headers);
            View::share('locales', $locales);
        } else {
            $defaultHeaders = collect([
                'id' => '1',
                'header_logo' => 'rest.png',
                'header_logo_url' => asset('storage/uploads/website/images/rest.png'),
                'home' => 'Home',
                'driver' => 'Driver',
                'user' => 'User',
                'contact' => 'Contact',                
                'book_now_btn' => 'Book Now',
                'footer_logo' => 'rest.png',
                'footer_logo_url' => asset('storage/uploads/website/images/rest.png'),
                'footer_para' => 'Tagxi is a rideshare platform facilitating peer to peer ridesharing by means of connecting passengers who are in need of rides from drivers with available cars to get from point A to point B with the press of a button.',
                'quick_links' => 'Quick Links',
                'compliance' => 'Compliance',
                'privacy' => 'Privacy Policy',
                'terms' => 'Terms & Conditions',
                'dmv' => 'DMV Check',
                'user_app' => 'Use Apps',
                'user_play' => 'Play Store',
                'user_play_link' => 'https://play.google.com/store/apps/details?id=com.user.tagxi',
                'user_apple' => 'Apple Store',
                'user_apple_link' => 'misoftwares.in',
                'driver_app' => 'Driver Apps',
                'driver_play' => 'Play Store',
                'driver_play_link' => 'misoftwares.in',
                'driver_apple' => 'Apple Store',
                'driver_apple_link' => 'misoftwares.in',
                'copy_rights' => '2021 @ Misoftwares',
                'fb_link' => 'fb.com',
                'linkdin_link' => 'linkdin.com',
                'x_link' => 'x.com',
                'insta_link' => 'instagram.com',
                'locale' => 'En',
                'language' => 'English',
                'enable_web_booking' => 0,
                'userlogin' => 'login',
            ]);
            View::share('headers', $defaultHeaders);
            View::share('locales', $defaultHeaders->pluck('locale', 'id'));
        }
        // Set the locale from the session or default to 'en'
    $selectedLocale = session('selectedLocale', 'en');
    app()->setLocale($selectedLocale);

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('local')) {
           
        }
    }

    /**
     * Load the custom validator methods.
     *
     * @return void
     */
    protected function loadCustomValidators()
    {
        $customValidatorClass = 'App\Base\Validators\CustomValidators';

        $this->extendValidator('mobile_number', $customValidatorClass);
        $this->extendValidator('numeric_max', $customValidatorClass);
        $this->extendValidator('numeric_min', $customValidatorClass);
        $this->extendValidator('otp', $customValidatorClass);
        $this->extendValidator('uuid', $customValidatorClass);
        $this->extendValidator('decimal', $customValidatorClass);
        $this->extendValidator('double', $customValidatorClass);
    }

    /**
     * Extend the validator with custom methods.
     *
     * @param string $name
     * @param string $class
     * @return void
     */
    protected function extendValidator($name, $class)
    {
        $method = 'validate' . Str::studly($name);

        $this->validator->extend($name, "{$class}@{$method}");
    }

    public function nav() 
    {
    
    
            $navs = Setting::whereName('nav_color')->first();
    
    
    
         view()->share('navs', $navs);
    
    
        }
    
        public function side() 
        {
        
                $side = Setting::whereName('sidebar_color')->first();
        
        
             view()->share('side', $side);
        
        
            }
    
            public function sidetxt() 
        {
        
                $side_txt = Setting::whereName('sidebar_text_color')->first();
        
        
             view()->share('side_txt', $side_txt);
        
        
            }
            public function landingbgcolor() 
            {
            
                $landing_header_bg_color = Setting::whereName('landing_header_bg_color')->first();
        
        
                view()->share('landing_header_bg_color', $landing_header_bg_color);
            
            
            }

            public function landingtextcolor() 
            {
            
                $landing_header_text_color = Setting::whereName('landing_header_text_color')->first();
        
        
                view()->share('landing_header_text_color', $landing_header_text_color);
            
            
            }

            public function landingacttextcolor() 
            {
            
                $landing_header_active_text_color = Setting::whereName('landing_header_active_text_color')->first();
        
        
                view()->share('landing_header_active_text_color', $landing_header_active_text_color);
            
            
            }

            public function landingfooterbgcolor() 
            {
            
                $landing_footer_bg_color = Setting::whereName('landing_footer_bg_color')->first();
        
        
                view()->share('landing_footer_bg_color', $landing_footer_bg_color);
            
            
            }

            public function landingfootertextcolor() 
            {
            
                $landing_footer_text_color = Setting::whereName('landing_footer_text_color')->first();
        
        
                view()->share('landing_footer_text_color', $landing_footer_text_color);
            
            
            }
            
            public function logo() 
        {
        
                $logo = Setting::whereName('logo')->first();
        
        
             view()->share('logo', $logo);
        
        
            }
            public function favicon() 
        {
        
                $favicon = Setting::whereName('favicon')->first();
        
        
             view()->share('favicon', $favicon);
        
        
            }
            public function loginbg() 
        {
        
                $loginbg = Setting::whereName('loginbg')->first();
        
        
             view()->share('loginbg', $loginbg);
        
        
            }

            public function owner_loginbg() 
            {
            
                    $loginbg = Setting::whereName('owner_loginbg')->first();
            
            
                 view()->share('owner_loginbg', $owner_loginbg);
            
            
                }
}
