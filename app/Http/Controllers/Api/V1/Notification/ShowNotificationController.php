<?php

namespace App\Http\Controllers\Api\V1\Notification;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\V1\BaseController;
use App\Models\Admin\Notification;
use App\Models\Admin\UserDriverNotification;
use App\Models\User;
use App\Base\Constants\Auth\Role;
use App\Transformers\Common\NotificationTransformer;
use Illuminate\Http\Request;

/**
 * @authenticated
 * @group Notification
 */
class ShowNotificationController extends BaseController
{
    /**
     * List Notification
     * @authenticated
     * @response
     * {
     *     "success": true,
     *     "message": "success",
     *     "data": [
     *      {
     *          "id" : "8841c737-357f-4f9a-ad8d-f585504e0694",
     *          "title" : "Title",
     *          "body" : "Body",
     *          "image": "https://scribe.knuckles.wtf/img/logo.png",
     *          "converted_created_at": "23rd Nov 04:07 PM"
     *      }
     * }
     */
    public function getNotifications()
    {
                
        $user = auth()->user();

        if (access()->hasRole('user'))
        {
            $user_id = auth()->user()->id;
            $user_notification = UserDriverNotification::where('user_id', $user_id)->pluck('notify_id');
            $notifications = Notification::whereIn('id', $user_notification)->orderBy('created_at','desc');
            UserDriverNotification::whereIn('user_id', [$user_id])->update(['is_read' => true]);
        }
        elseif ($user->hasRole('driver'))
        {
            $user_id = $user->driver->id;
            $user_notification = UserDriverNotification::where('driver_id', $user_id)->pluck('notify_id');
            $notifications = Notification::whereIn('id', $user_notification)->orderBy('created_at','desc');
            UserDriverNotification::whereIn('driver_id', [$user_id])->update(['is_read' => true]);
        }
        elseif ($user->hasRole('owner')){
            $user_id = $user->owner->id;
            $user_notification = UserDriverNotification::where('owner_id', $user_id)->pluck('notify_id');
            $notifications = Notification::whereIn('id', $user_notification)->orderBy('created_at','desc');
            UserDriverNotification::whereIn('owner_id', [$user_id])->update(['is_read' => true]);
        }
        $result=filter($notifications, new NotificationTransformer())->paginate();

        return $this->respondSuccess($result);
    }



    /**
     * Delete Notifications
     *
     * @authenticated
     * @response
     * {
     *     "success": true,
     *     "message": "success",
     * }
     *
     * */
    public function deleteNotification(Notification $notification){

        $user = auth()->user();

        // Log::info($user);

        if (access()->hasRole('user'))
        {
            $notification->userNotification()->where('user_id',$user->id)->delete();

        }elseif ($user->hasRole('driver'))
        {
            $user_id = $user->driver->id;

            $notification->userNotification()->where('driver_id',$user_id)->delete();

        }
        elseif ($user->hasRole('owner'))
        {
            $user_id = $user->owner->id;

            $notification->userNotification()->where('owner_id',$user_id)->delete();

        }

        return $this->respondSuccess();


    }


}
