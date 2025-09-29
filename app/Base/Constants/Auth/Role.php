<?php

namespace App\Base\Constants\Auth;

class Role
{
    const USER = 'user';
    const SUPER_ADMIN = 'super-admin';
    const DRIVER = 'driver';
    const DISPATCHER = 'dispatcher';
    const OWNER = 'owner';
    const DELIVERY_DISPATCHER = 'delivery-dispatcher';
    const STORE_OWNER='store_owner';
    const FLEET_OWNER='fleet_owner';
    const EMPLOYEE='employee';



    /**
     * Get all the admin roles.
     *
     * @return array
     */
    public static function adminRoles()
    {
        return [
            self::SUPER_ADMIN,
            self::DISPATCHER,
            self::DELIVERY_DISPATCHER,

        ];
    }

    /* dispatchRoles */

        public static function dispatchRoles()
        {
            return [
                self::DISPATCHER,
                self::DELIVERY_DISPATCHER,
            ];
        }

    
    /**
     * Get all the admin roles.
     *
     * @return array
     */
    public static function exceptSuperAdminRoles()
    {
        return [
        ];
    }

    /**
     * Get all the web panel roles.
     *
     * @return array
     */
    public static function webPanelLoginRoles()
    {
        return [
            self::SUPER_ADMIN,

        ];
    }
    /**
    * Get all the web panel roles.
    *
    * @return array
    */
    public static function webShowableRoles()
    {
        return [
            self::DISPATCHER,
            self::DELIVERY_DISPATCHER,
            self::DRIVER,
            self::USER,
            self::FLEET_OWNER,
        ];
    }

    /**
     * Get all the Merchant and Admin roles
     * @return array
     */
    public static function masterDataAccessRoles()
    {
        return [
            self::SUPER_ADMIN,

        ];
    }

    /**
     * Get all the user and Admin roles
     * @return array
     */
    public static function userAndAdminRoles()
    {
        return [
            self::USER,
            self::SUPER_ADMIN,
        ];
    }

    /**
     * Get all the user and driver roles
     * @return array
     */
    public static function mobileAppRoles()
    {
        return [
            self::USER,
            self::DRIVER,
            self::STORE_OWNER,
            self::OWNER,
        ];
    }
}
