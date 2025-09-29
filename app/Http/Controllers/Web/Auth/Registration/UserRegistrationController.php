<?php

namespace App\Http\Controllers\Web\Auth\Registration;

use App\Base\Constants\Auth\Role;
use App\Events\Auth\UserRegistered;
use App\Http\Controllers\ApiController;
use App\Models\User;
use DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Helpers\Exception\ExceptionHelpers;
use Illuminate\Http\Request;

/**
 * @resource User-Register
 *
 * Web-panel-user register
 */
class UserRegistrationController extends LoginController
{
    use ExceptionHelpers;
    

    /**
     * The user model instance.
     *
     * @var \App\Models\User
     */
    protected $user;

    /**
     * AdminRegistrationController constructor.
     *
     * @param \App\Models\User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Register the admin user.
     * @param \App\Http\Requests\Auth\Registration\UserRegistrationRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @response
     * {
     *"success": true,
     *"message": "success"
     *}
     */
    public function register(Request $request)
    {
        $request->validate([
        'mobile' => 'required',
        'country' => 'required',
        ]);

        $user_params = [
            'mobile' => $request->input('mobile'),
            'mobile_confirmed' => true,
            'country'=>$request->input('country'),
            'refferal_code'=>str_random(6),
            'lang'=>'en'
        ];


        $user = $this->user->create($user_params);

        // Create Empty Wallet to the user
        $user->userWallet()->create(['amount_added'=>0]);

        $user->attachRole(Role::USER);

        event(new UserRegistered($user));

        if ($user) {
            return $this->authenticateAndRespond($user, $request);
        }

        return $this->respondBadRequest('Unknown error occurred. Please try again later or contact us if it continues.');

    }
}
