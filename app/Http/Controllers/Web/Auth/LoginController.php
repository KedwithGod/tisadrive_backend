<?php

namespace App\Http\Controllers\Web\Auth;

use App\Models\User;
use App\Models\Setting;
use App\Events\Event;
use App\Models\MailOtp;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Events\Auth\UserLogin;
use App\Base\Constants\Auth\Role;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Auth\UserLoginRequest;
use Psr\Http\Message\ServerRequestInterface;
use App\Http\Requests\Auth\AdminLoginRequest;
use App\Base\Services\OTP\Handler\OTPHandlerContract;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Illuminate\Support\Facades\Hash;
use App\Models\Access\Role as RoleSlug;
use Log;
use Inertia\Inertia;
use App\Models\Country;
use App\Transformers\CountryTransformer;
use App\Models\Admin\Owner;
/**
 * @group Web-Authentication
 *
 * APIs for Authentication
 */
class LoginController extends ApiController
{
    /**
     * The OTP handler instance.
     *
     * @var \App\Base\Services\OTP\Handler\OTPHandlerContract
     */
    protected $otpHandler;

    /**
     * The user model instance.
     *
     * @var \App\Models\User
     */
    protected $user;

    /**
     * The model user identifier field used during login.
     * Can be email or username.
     *
     * @var string
     */
    protected $loginIdentifier = 'email';

    /**
     * LoginController constructor.
     *
     * @param \App\Models\User $user
     * @param \App\Base\Services\OTP\Handler\OTPHandlerContract $otpHandler
     */
    public function __construct(User $user, OTPHandlerContract $otpHandler)
    {
        $this->user = $user;
        $this->otpHandler = $otpHandler;
    }

    /**
     * Login the normal user.
     *
     * @param \App\Http\Requests\Auth\UserLoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @response
     * {
     *"success": true,
     *"message": "success"
     *}
     */
    public function loginSpaUser(UserLoginRequest $request)
    {
        return $this->loginUserAccountSPA($request, Role::USER);
    }

    /**
     * Login the Web admin users.
     *
     * @param \App\Http\Requests\Auth\AdminLoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @response
     * {
     *"success": true,
     *"message": "success"
     *}
     */
    public function loginWebUsers(AdminLoginRequest $request)
    {
       
    $web_login_roles = RoleSlug::whereNotIn('slug', array_merge(Role::mobileAppRoles()))
        ->pluck('slug')
        ->toArray(); 
        
    return $this->loginUserAccountSPA($request, $web_login_roles);
    }

    public function loginDispatchUsers(AdminLoginRequest $request)
    {
        // dd($request->all());
        return $this->loginUserAccountSPA($request, Role::DISPATCHER);
    }
    // public function loginFleetowners(AdminLoginRequest $request)
    // {
    //     // dd($request->all());    
    //     // Log::info("Owner_login");
    //     // Log::info($request->all());

    //     return $this->loginUserAccountSPA($request, Role::OWNER);
    // }
    public function loginFleetowners(AdminLoginRequest $request)
    {
        $owner = Owner::where('email', $request->email)
            ->select('approve')
            ->first();
    
        if (!$owner) {
            return response()->json([
                'status' => 'failed',
                'errors' => ['email' => ['These credentials do not match our records.']]
            ], 422);
        }
    
        if ($owner->approve != 1) {
            return response()->json([
                'status' => 'failed',
                'errors' => ['email' => ['Owner is not approved by Admin.']]
            ], 422);
        }
    
        return $this->loginUserAccountSPA($request, Role::OWNER);
    }

    /**
     * Logout the user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @response
     * {
     *"success": true,
     *"message": "success"
     *}
     */
    public function logoutSPA(Request $request)
    {
        if (auth()->user()->hasRole(Role::DISPATCHER)) {
            $redirect = 'login-dispatch';

        } else if (auth()->user()->hasRole('owner')) {
            $redirect = 'login/'.get_settings('owner_login');

        }else if (auth()->user()->hasRole('user')) {
            $redirect = 'login/'.get_settings('user_login');

        }else{

            $redirect = 'login/'.get_settings('admin_login');
        }

        auth('web')->logout();

        $request->session()->invalidate();

        return redirect($redirect);
    }

    /**
     * Login the user for SPA and respond accordingly.
     *
     * @param \Illuminate\Foundation\Http\FormRequest $request
     * @param string|array $role
     * @param array $conditions
     * @return \Illuminate\Http\JsonResponse
     */
    protected function loginUserAccountSPA($request, $role, array $conditions = [])
    {
        // dd($role);
        return $this->loginUserAccount($request, $role, false, $conditions);
    }

    /**
     * Login the user for Mobile App and respond accordingly.
     *
     * @param \Illuminate\Foundation\Http\FormRequest $request
     * @param string|array $role
     * @param array $conditions
     * @return \Illuminate\Http\JsonResponse
     */
    protected function loginUserAccountApp($request, $role, array $conditions = [])
    {
        return $this->loginUserAccount($request, $role, true, $conditions);
    }

    /**
     * Login the user for SPA or Mobile App and respond accordingly.
     *
     * @param \Illuminate\Foundation\Http\FormRequest $request
     * @param string|array $role
     * @param bool $needsToken
     * @param array $conditions
     * @return \Illuminate\Http\JsonResponse
     */
    protected function loginUserAccount($request, $role, $needsToken = true, array $conditions = [])
    {
        // if ($needsToken && !$request->has(['client_id', 'client_secret'])) {
        //     return $this->respondBadRequest('Missing password grant client credentials');
        // }
        if ($request->has('social_id')) {
            return $this->setLoginIdentifier('social_id')
                ->loginUserWithSocialUniqueId($request, $role, $needsToken, $conditions);
        }

        if ($request->has('uuid')) {

            return $this->setLoginIdentifier('uuid')
                ->loginUserWithUuid($request, $role, $needsToken, $conditions);
        }

        if ($needsToken && $request->has(['mobile', 'otp']) && $this->roleAllowedOTPLogin($role)) {
            return $this->loginUserWithOTP($request, $role, $needsToken, $conditions);
        }

        if ($request->has(['mobile', 'password'])) {
            return $this->setLoginIdentifier('mobile')
                ->loginUserWithPassword($request, $role, $needsToken, $conditions);
        }

        if ($request->has(['mobile'])) {
            return $this->setLoginIdentifier('mobile')
                ->loginUserWithMobile($request, $role, $needsToken, $conditions);
        }

        if ($request->has(['email', 'password'])) {

            // dd($request->all());

            return $this->setLoginIdentifier('email')
                ->loginUserWithPassword($request, $role, $needsToken, $conditions);
        }

        if ($request->has('social_unique_id')) {
            return $this->setLoginIdentifier('social_unique_id')
                ->loginUserWithSocialUniqueId($request, $role, $needsToken, $conditions);
        }

        

        if ($request->has(['username', 'password']) && $this->roleAllowedUsernameLogin($role)) {
            return $this->setLoginIdentifier('username')
                ->loginUserWithPassword($request, $role, $needsToken, $conditions);
        }

        
        if ($needsToken && $request->has(['email', 'otp']) && $this->roleAllowedOTPLogin($role)) {
            return $this->loginUserWithEmailOtp($request, $role, $needsToken, $conditions);
        }


        return $this->respondBadRequest('Missing login credentials');
    }


    /**
     * Login the user using their mobile and otp.
     *
     * @param \Illuminate\Foundation\Http\FormRequest $request
     * @param string|array $role
     * @param bool $needsToken
     * @param array $conditions
     * @return \Illuminate\Http\JsonResponse
     */
    protected function loginUserWithEmailOtp($request, $role, $needsToken = true, array $conditions = [])
    {


        $request->validate([
        'otp' => 'sometimes|required|exists:mail_otp_verifications,otp',
        ]);

        $email = $request->input('email');
        $otp = $request->input('otp');
        $user = null;
        $identifier = $this->getLoginIdentifier();



        $verify_otp = MailOtp::where('email' ,$email)->where('otp', $otp)->exists();
            // dd($verify_otp);


        if ($verify_otp == false) 
        {
            $this->throwCustomValidationException(['message' => "The otp provided has Invaild" ]);
        }
       
       if (method_exists($this, $method = 'resolveUserFrom' . Str::studly($identifier))) {

            $user = $this->{$method}($email, $role);
           
        }

        if (!$user) {
            $this->throwInvalidCredentialsException($identifier);
        }

        MailOtp::where('email' ,$email)->where('otp', $otp)->update(['verified' => true]);

        return $this->authenticateAndRespond($user, $request, $needsToken);

    }
    /**
     * Login the user using their email and password.
     *
     * @param \Illuminate\Foundation\Http\FormRequest $request
     * @param string|array $role
     * @param bool $needsToken
     * @param array $conditions
     * @return \Illuminate\Http\JsonResponse
     */
    protected function loginUserWithMobile($request, $role, $needsToken = true, array $conditions = [])
    {
        $user = null;

        $identifier = $this->getLoginIdentifier();

        $emailOrUsername = $request->input($identifier);

        if (method_exists($this, $method = 'resolveUserFrom' . Str::studly($identifier))) {
            $user = $this->{$method}($emailOrUsername, $role);
        }

        if (!$user) {
            $this->throwInvalidCredentialsException($identifier);
        }

        if (!$user->isActive() || !$this->validateChecks($user, $conditions, $identifier)) {
            $this->throwAccountDisabledException($identifier);
        }

        return $this->authenticateAndRespond($user, $request, $needsToken);
    }


    /**
     * Login the user using their email and password.
     *
     * @param \Illuminate\Foundation\Http\FormRequest $request
     * @param string|array $role
     * @param bool $needsToken
     * @param array $conditions
     * @return \Illuminate\Http\JsonResponse
     */
    protected function loginUserWithUuid($request, $role, $needsToken = true, array $conditions = [])
    {
        $user = null;

        $identifier = $this->getLoginIdentifier();

        $emailOrUsername = $request->input($identifier);

        if (method_exists($this, $method = 'resolveUserFrom' . Str::studly($identifier))) {
            $user = $this->{$method}($emailOrUsername, $role);
        }

        if (!$user) {
            $this->throwInvalidCredentialsException($identifier);
        }

        if (!$user->isActive() || !$this->validateChecks($user, $conditions, $identifier)) {
            $this->throwAccountDisabledException($identifier);
        }

        return $this->authenticateAndRespond($user, $request, $needsToken);
    }


    /**
     * Login the user using their email and password.
     *
     * @param \Illuminate\Foundation\Http\FormRequest $request
     * @param string|array $role
     * @param bool $needsToken
     * @param array $conditions
     * @return \Illuminate\Http\JsonResponse
     */
    protected function loginUserWithPassword($request, $role, $needsToken = true, array $conditions = [])
    {
        
        $user = null;

        $identifier = $this->getLoginIdentifier();

        $emailOrUsername = $request->input($identifier);

        $password = $request->input('password');

        // dd($emailOrUsername);

        if (method_exists($this, $method = 'resolveUserFrom' . Str::studly($identifier))) {
           
            $user = $this->{$method}($emailOrUsername, $role);

            // Log::info("user-info-from-email-and-role");
            // Log::info($user);

        } 
        if (!$user || !hash_check($password, $user->password)) {
            
            $this->throwInvalidCredentialsException($identifier);
        }

        // if (!$user->isActive() || !$this->validateChecks($user, $conditions, $identifier)) {
        //     $this->throwAccountDisabledException($identifier);
        // }
       
        return $this->authenticateAndRespond($user, $request, $needsToken);
    }


    /**
     * Login the user using social unique id
     *
     * @param \Illuminate\Foundation\Http\FormRequest $request
     * @param string|array $role
     * @param bool $needsToken
     * @param array $conditions
     * @return \Illuminate\Http\JsonResponse
     */
    protected function loginUserWithSocialUniqueId($request, $role, $needsToken = true, array $conditions = [])
    {
        $user = null;
        $identifier = $this->getLoginIdentifier();
        $social_unique_id = $request->input($identifier);

        if (method_exists($this, $method = 'resolveUserFrom' . Str::studly($identifier))) {
            $user = $this->{$method}($social_unique_id, $role);
        }

        if (!$user) {
            $this->throwInvalidCredentialsException($identifier);
        }

        if (!$user->isActive() || !$this->validateChecks($user, $conditions, $identifier)) {
            $this->throwAccountDisabledException($identifier);
        }

        return $this->authenticateAndRespond($user, $request, $needsToken);
    }

    /**
     * Login the user using their mobile and otp.
     *
     * @param \Illuminate\Foundation\Http\FormRequest $request
     * @param string|array $role
     * @param bool $needsToken
     * @param array $conditions
     * @return \Illuminate\Http\JsonResponse
     */
    protected function loginUserWithOTP($request, $role, $needsToken = true, array $conditions = [])
    {
        $mobile = $request->input('mobile');
        $otp = $request->input('otp');

        $user = $this->resolveUserFromMobile($mobile, $role);

        if (!$user) {
            $this->throwCustomValidationException("User with that mobile number doesn't exist.", 'otp');
        }

        if (!$this->otpHandler->setMobile($mobile)->validate($otp)) {
            $this->throwCustomValidationException('The otp provided is invalid.', 'otp');
        }

        if (!$user->isActive() || !$this->validateChecks($user, $conditions, 'otp')) {
            $this->throwAccountDisabledException('otp');
        }

        $this->otpHandler->delete();

        return $this->authenticateAndRespond($user, $request, $needsToken);
    }


    /**
     * Resolve the user from their email for a particular role.
     *
     * @param string $email
     * @param string|array $role
     * @return \App\Models\User|null
     */
    protected function resolveUserFromEmail($email, $role)
    {
        // Log::info("___user___Ac");

        // Log::info($role);
        return $this->user->belongsToRole($role)
            ->where('email', $email)
            ->first();
    }

    /**
     * Resolve the user from their social_unique_id for a particular role.
     *
     * @param string $social_unique_id
     * @param string|array $role
     * @return \App\Models\User|null
     */
    protected function resolveUserFromSocialUniqueId($social_unique_id, $role)
    {
        return $this->user->belongsToRole($role)
            ->where('social_unique_id', $social_unique_id)
            ->first();
    }

    /**
     * Resolve the user from their social_unique_id for a particular role.
     *
     * @param string $social_unique_id
     * @param string|array $role
     * @return \App\Models\User|null
     */
    protected function resolveUserFromSocialId($social_unique_id, $role)
    {
        return $this->user->belongsToRole($role)
            ->where('social_id', $social_unique_id)
            ->first();
    }


    /**
     * Resolve the user from their username for a particular role.
     *
     * @param $username $email
     * @param string|array $role
     * @return \App\Models\User|null
     */
    protected function resolveUserFromUsername($username, $role)
    {
        return $this->user->belongsToRole($role)
            ->where('username', $username)
            ->first();
    }

    /**
     * Resolve the user from their mobile for a particular role.
     *
     * @param string $mobile
     * @param string|array $role
     * @return \App\Models\User|null
     */
    protected function resolveUserFromMobile($mobile, $role)
    {
        return $this->user->belongsToRole($role)
            ->where('mobile', $mobile)
            ->first();
    }

    /**
     * Resolve the user from their mobile for a particular role.
     *
     * @param string $mobile
     * @param string|array $role
     * @return \App\Models\User|null
     */
    protected function resolveUserFromUuid($uuid, $role)
    {
        
        $mobile = $this->otpHandler->getMobileFromUuid($uuid);

        // dd($mobile);
        
        return $this->user->belongsToRole($role)
            ->where('mobile', $mobile)
            ->first();
    }
    /**
     * Validate the user model conditions.
     *
     * @param $user
     * @param array $conditions
     * @param string $field
     * @return bool
     * @internal param $ \App\Models\User|null
     */
    protected function validateChecks($user, array $conditions, $field)
    {
        foreach ($conditions as $key => $value) {
            if ($user->$key != $value) {
                if (mb_strtolower($key) === 'email_confirmed') {
                    /*
                                             * This validation message will be used in the frontend to display
                                             * the 'Resend confirmation email' button/link. Do not alter it!
                    */
                    $this->throwCustomValidationException('Account email confirmation pending.', $field);
                }

                return false;
            }
        }

        return true;
    }

    /**
     * Authenticate the user and respond accordingly.
     * SPA login will authenticate the user using sessions which will create the cookie on refresh.
     * First party apps (Mobile App) will get the access token and refresh token.
     *
     * @param \App\Models\User $user
     * @param \Illuminate\Foundation\Http\FormRequest $request
     * @param bool $needsToken
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    protected function authenticateAndRespond(User $user, $request, $needsToken = false)
    {
        $user->tokens()->delete();
        // dd($user);
        if ($needsToken) {
            
          

            if ($request->has('device_token')) {
                $user->fcm_token = $request->input('device_token')?:null;
                
                $user->save();
            }

            if ($request->has('password')) {

               
                $token =  $user->createToken('Dilip-iphone')->plainTextToken;

                return response()->json(['success'=>true,'message'=>'success','access_token'=>$token]);

               
            } else {

               
                $token =  $user->createToken('Dilip-iphone')->plainTextToken;
                
                return response()->json(['success'=>true,'message'=>'success','access_token'=>$token]);



            }
        }


        return $this->authenticateUser($user, $request->has('remember'));
    }


    /**
     * Login user using standard auth method and respond success.
     *
     * @param \App\Models\User $user
     * @param bool $remember
     * @return \Illuminate\Http\JsonResponse
     */
    protected function authenticateUser(User $user, $remember = false)
    {
        // dd($user->hasRole('owner'));
        auth('web')->login($user, $remember);
        session(['module' => "transport"]);

        return $this->respondSuccess();


        if($user->hasRole('owner'))
        {
            return redirect('/individual-owner-dashboard');

        }elseif($user->hasRole('user')){

            return $this->respondSuccess();

        }elseif($user->hasRole('dispatcher')){

            return redirect()->route('dispatch.dashboard');

        }else{
            return redirect('/dashboard');
        }

    }

    /**
     * Login user using standard auth method and respond success.
     *
     * @param \App\Models\User $user
     * @param bool $remember
     * @return \Illuminate\Http\JsonResponse
     */
    protected function set(User $user, $remember = false)
    {
        auth('web')->login($user, $remember);

        return $this->respondSuccess();
    }

    /**
     * Get the default login identifier.
     *
     * @return string
     */
    protected function getLoginIdentifier()
    {
        return $this->loginIdentifier;
    }

    /**
     * Set the default login identifier.
     * Can be email or username.
     *
     * @param string $loginIdentifier
     * @return $this
     */
    protected function setLoginIdentifier($loginIdentifier)
    {
        $this->loginIdentifier = $loginIdentifier;

        return $this;
    }


    public function adminLogin() 
    {
        return Inertia::render('Auth/Login');
    }

    public function dynamicLoginUrl($redirect) 
    {
        $admin_url = Setting::where('name','admin_login')->pluck('value')->first();

        
        $owner_url = Setting::where('name','owner_login')->pluck('value')->first();

        $dispatcher_url = Setting::where('name','dispatcher_login')->pluck('value')->first();


        $host_name = request()->getHost();

        $conditional_host = explode('.',$host_name);
        if($conditional_host[0] =='restart-dispatch'){
            return Inertia::render('Auth/DispatchLogin');
        }

        if($redirect==$admin_url){

            return Inertia::render('Auth/Login');  
            
        }else if($redirect==$dispatcher_url){

            return Inertia::render('Auth/DispatchLogin');

        }else if($redirect==$owner_url){

            return Inertia::render('Auth/OwnerLogin');

        }else{

            $query = Country::active()->get();

        $countries = fractal($query, new CountryTransformer);

        $result = json_decode($countries->toJson(),true);
            
        $default_country = Country::active()->where('code',get_settings('default_country_code_for_mobile_app'))->first();

        $default_dial_code = $default_country->dial_code;
        $default_flag = $default_country->flag;
        $default_country_id = $default_country->id;
        
        $enable_firebase_otp = get_active_sms_settings() == "enable_firebase_otp" ?? false;

        $firebaseConfig = (object) [
            'apiKey' => get_firebase_settings('firebase_api_key'),
            'authDomain' => get_firebase_settings('firebase_auth_domain'),
            'databaseURL' => get_firebase_settings('firebase_database_url'),
            'projectId' => get_firebase_settings('firebase_project_id'),
            'storageBucket' => get_firebase_settings('firebase_storage_bucket'),
            'messagingSenderId' => get_firebase_settings('firebase_messaging_sender_id'),
            'appId' => get_firebase_settings('firebase_app_id'),
        ];

        return Inertia::render('pages/landing/user-web/index',[
            'countries'=>$result['data'],
            'default_dial_code'=>$default_dial_code,
            'firebaseConfig'=>$firebaseConfig,
            'default_flag'=>$default_flag,
            'app_for' => env('APP_FOR'),
            'enable_firebase_otp'=>$enable_firebase_otp,
            'default_country_id'=>$default_country_id
        ]);

        }
    }
}
