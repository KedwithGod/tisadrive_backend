<?php

namespace App\Http\Controllers\Api\V1\Auth\Registration;

use App\Http\Controllers\Web\BaseController;
use App\Mail\RidedetailsMail;

use App\Models\User;
use App\Models\Country;

use App\Base\Libraries\SMS\SMSContract;

use App\Helpers\Exception\ExceptionHelpers;

use App\Base\Services\OTP\Handler\OTPHandlerContract;

use App\Base\Services\ImageUploader\ImageUploaderContract;

use Illuminate\Support\Facades\Mail;

use Illuminate\Http\Request as HttpRequest;



class EmailSendController extends BaseController
{

    use ExceptionHelpers;
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
     * The SMS contract instance.
     *
     * @var \App\Base\Libraries\SMS\SMSContract
     */
    protected $smsContract;

    /**
     * The image uploader instance.
     *
     * @var \App\Base\Services\ImageUploader\ImageUploaderContract
     */
    protected $imageUploader;

    /**
     * The country model instance.
     *
     * @var \App\Models\Country
     */
    protected $country;

    /**
     * Constructor for EmailSendController.
     *
     * @param \App\Models\User $user
     * @param \App\Base\Services\OTP\Handler\OTPHandlerContract $otpHandler
     * @param \App\Models\Country $country
     * @param \App\Base\Libraries\SMS\SMSContract $smsContract
     * @param \App\Base\Services\ImageUploader\ImageUploaderContract $imageUploader
     */
    public function __construct(User $user, OTPHandlerContract $otpHandler, Country $country, SMSContract $smsContract,ImageUploaderContract $imageUploader)
    {
        $this->user = $user;
        $this->otpHandler = $otpHandler;
        $this->country = $country;
        $this->smsContract = $smsContract;
        $this->imageUploader = $imageUploader;

    }


    /**
     * Display the email send page.
     *
     * @response {
     *  "page": "Email Send Page",
     *  "main_menu": "email_send",
     *  "sub_menu": "",
     *  "modules": ["module1", "module2"],
     *  "show_rental_ride_feature": true,
     *  "user_name": "User",
     *  "rideInfo": [],
     *  "userDetails": null
     * }
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $page = trans('pages_names.email_send');

        $main_menu = 'email_send';
        $sub_menu = '';
        $modules = get_settings('enable_modules_for_applications');
        $show_rental_ride_feature = get_settings('show_rental_ride_feature');
        $user_name = 'User';
        $rideInfo = [];
        $userDetails = null;

        return view('admin.email_send',compact('user_name', 'modules', 'show_rental_ride_feature', 'rideInfo', 'userDetails'));
    }





}
