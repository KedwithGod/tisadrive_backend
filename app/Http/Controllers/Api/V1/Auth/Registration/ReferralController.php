<?php

namespace App\Http\Controllers\Api\V1\Auth\Registration;

use App\Models\User;
use Illuminate\Http\Request;
use App\Base\Constants\Auth\Role;
use App\Transformers\User\UserTransformer;
use App\Base\Constants\Masters\WalletRemarks;
use App\Transformers\User\ReferralTransformer;
use App\Http\Controllers\Api\V1\BaseController;
use App\Jobs\Notifications\AndroidPushNotification;
use App\Jobs\Notifications\SendPushNotification;
use Illuminate\Support\Facades\Log;
use App\Mail\ReferralMail;
use Illuminate\Support\Facades\Mail;
use App\Jobs\Mails\SendReferralMailNotification;

/**
 * @group SignUp-And-Otp-Validation
 *
 * APIs for User-Management
 */
class ReferralController extends BaseController
{
    /**
     * The user model instance.
     *
     * @var \App\Models\User
     */
    protected $user;


    /**
     * ReferralController constructor.
     *
     * @param \App\Models\User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
    * Get Referral code
    * @responseFile responses/auth/get-referral.json
    */
    public function index()
    {
        $user = fractal(auth()->user(), new ReferralTransformer);

        return $this->respondOk($user);
    }
    /**
    * Update User Referral
    * @bodyParam refferal_code string required refferal_code of the another user
    * @response {"success":true,"message":"success"}
    */
    public function updateUserReferral(Request $request)
    {
        // Validate Referral code
        $reffered_user = $this->user->where('refferal_code', $request->refferal_code)->first();
        if (!$reffered_user) {
            $this->throwCustomException('Provided Referral code is not valid', 'refferal_code');
        }

        $auth_user = auth()->user();

        $auth_user->update(['referred_by'=>$reffered_user->id]);

        if($reffered_user->hasRole('user') && $auth_user->hasRole('user'))
        {
            // Log::info("user Reffers User");
        // Update referred user's id to the users table
        $user_wallet = $reffered_user->userWallet;
     
        $referral_commision = get_settings('referral_commission_amount_for_user')?:0;

        // Log::info("referral_commision");
        // Log::info($referral_commision);


  
        if($referral_commision>0)
         {
        // Log::info("-------referral_commision is greater than 0-------");

            $user_wallet->amount_added += $referral_commision;
            $user_wallet->amount_balance += $referral_commision;
            $user_wallet->save();

            // Add the history
            $reffered_user->userWalletHistory()->create([
                'amount'=>$referral_commision,
                'transaction_id'=>str_random(6),
                'remarks'=>WalletRemarks::REFERRAL_COMMISION,
                'refferal_code'=>$reffered_user->refferal_code,
                'is_credit'=>true]);

                // Notify user
                // $title = custom_trans('referral_earnings_notify_title',[],$reffered_user->lang);
                // $body = custom_trans('referral_earnings_notify_body',[],$reffered_user->lang);

                // dispatch(new SendPushNotification($reffered_user,$title,$body));

                 $notification = \DB::table('notification_channels')
                     ->where('topics', 'User Referral') // Match the correct topic
                     ->first();

                //   send push notification 
                if ($notification && $notification->push_notification == 1) {
                     // Determine the user's language or default to 'en'
                    $userLang = $reffered_user->lang ?? 'en';
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
                    dispatch(new SendPushNotification($reffered_user, $title, $body));
                }
                SendReferralMailNotification::dispatch($auth_user, $reffered_user);

            }


        }else{
            
            // Log::info("user Reffers Driver");

        // Update referred user's id to the users table
        $user_wallet = $reffered_user->userWallet;
        $referral_commision = get_settings('referral_commission_amount_for_driver_reffering_a_user')?:0;

     
  
        if($referral_commision>0)
            {
            $user_wallet->amount_added += $referral_commision;
            $user_wallet->amount_balance += $referral_commision;
            $user_wallet->save();

            // Add the history
            $reffered_user->userWalletHistory()->create([
                'amount'=>$referral_commision,
                'transaction_id'=>str_random(6),
                'remarks'=>WalletRemarks::REFERRAL_COMMISION,
                'refferal_code'=>$reffered_user->refferal_code,
                'is_credit'=>true]);

                // Notify user
                // $title = custom_trans('referral_earnings_notify_title',[],$reffered_user->lang);
                // $body = custom_trans('referral_earnings_notify_body',[],$reffered_user->lang);

                // dispatch(new SendPushNotification($reffered_user,$title,$body));

                $notification = \DB::table('notification_channels')
                    ->where('topics', 'User Referral') // Match the correct topic
                    ->first();
                //    send push notification 
                if ($notification && $notification->push_notification == 1) {
                     // Determine the user's language or default to 'en'
                    $userLang = $reffered_user->lang ?? 'en';
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
                    dispatch(new SendPushNotification($reffered_user, $title, $body));
                }

                SendReferralMailNotification::dispatch($auth_user,$reffered_user);

            }


        }
        


        return $this->respondSuccess();
    }

    /**
    * Update Driver Referral code
    * @bodyParam refferal_code string required refferal_code of the another user
    * @response {"success":true,"message":"success"}
    */
    public function updateDriverReferral(Request $request)
    {
        $reffered_user = $this->user->where('refferal_code', $request->refferal_code)->first();

        if (!$reffered_user) {
            $this->throwCustomException('Provided Referral code is not valid', 'refferal_code');
        }

        $auth_user = auth()->user();



        $auth_user->update(['referred_by'=>$reffered_user->id]);

        if($reffered_user->hasRole('driver') && $auth_user->hasRole('driver'))
        {

        // Log::info("Driver Reffers Driver");
            // Add referral commission to the referred user
        $reffered_user = $reffered_user->driver;

        $driver_wallet = $reffered_user->driverWallet;
        $referral_commision = get_settings('referral_commission_amount_for_driver')?:0;
        if($referral_commision>0)
        {
        $driver_wallet->amount_added += $referral_commision;
        $driver_wallet->amount_balance += $referral_commision;
        $driver_wallet->save();

        // Add the history
        $reffered_user->driverWalletHistory()->create([
            'amount'=>$referral_commision,
            'transaction_id'=>str_random(6),
            'remarks'=>WalletRemarks::REFERRAL_COMMISION,
            'refferal_code'=>$reffered_user->refferal_code,
            'is_credit'=>true]);

        // Notify user
        // $title = custom_trans('referral_earnings_notify_title',[],$reffered_user->lang);
        // $body = custom_trans('referral_earnings_notify_body',[],$reffered_user->lang);

        // dispatch(new SendPushNotification($reffered_user->user,$title,$body));

        $notification = \DB::table('notification_channels')
        ->where('topics', 'User Referral') // Match the correct topic
        ->first();

        //    send push notification 
                if ($notification && $notification->push_notification == 1) {
                     // Determine the user's language or default to 'en'
                    $userLang = $reffered_user->lang ?? 'en';
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
                    dispatch(new SendPushNotification($reffered_user, $title, $body));
                }

                SendReferralMailNotification::dispatch($auth_user,$reffered_user);
        }
    
        }else{
            // Log::info("Driver Reffers USer");
            $referral_commision = get_settings('referral_commission_amount_for_user_reffering_a_driver')?:0;

            // Update referred user's id to the users table
        $user_wallet = $reffered_user->userWallet;
          if($referral_commision>0)
            {
            $user_wallet->amount_added += $referral_commision;
            $user_wallet->amount_balance += $referral_commision;
            $user_wallet->save();

            // Add the history
            $reffered_user->userWalletHistory()->create([
                'amount'=>$referral_commision,
                'transaction_id'=>str_random(6),
                'remarks'=>WalletRemarks::REFERRAL_COMMISION,
                'refferal_code'=>$reffered_user->refferal_code,
                'is_credit'=>true]);

                // Notify user
                // $title = custom_trans('referral_earnings_notify_title',[],$reffered_user->lang);
                // $body = custom_trans('referral_earnings_notify_body',[],$reffered_user->lang);

                // dispatch(new SendPushNotification($reffered_user->user,$title,$body));

                $notification = \DB::table('notification_channels')
                     ->where('topics', 'User Referral') // Match the correct topic
                     ->first();
                //    send push notification 
                if ($notification && $notification->push_notification == 1) {
                     // Determine the user's language or default to 'en'
                    $userLang = $reffered_user->lang ?? 'en';
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
                    dispatch(new SendPushNotification($reffered_user, $title, $body));
                }

                SendReferralMailNotification::dispatch($auth_user,$reffered_user);
            }
        }
        


        return $this->respondSuccess();
    }
}
