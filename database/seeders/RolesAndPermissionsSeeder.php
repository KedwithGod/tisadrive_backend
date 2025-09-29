<?php

namespace Database\Seeders;

use App\Base\Constants\Auth\Permission as PermissionSlug;
use App\Base\Constants\Auth\Role as RoleSlug;
use App\Models\Access\Permission;
use App\Models\Access\Role;
use Illuminate\Database\Seeder;
use DB;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
      * List of all the permissions to be created.
      *
      * @var array
      */

    protected $permissions = [

        //Nave Bar Permissions
        PermissionSlug::ACCESS_HOME => [
            'name' => 'access-home',
            'description' => 'Access Home',
            'main_menu' => 'dashboard',
            'sub_menu' => null,
            'main_link' => 'dashboard',
        ],
        PermissionSlug::ACCESS_USER_NAV_LIST => [
            'name' => 'access-user-nav-list',
            'description' => 'Access Home',
            'main_menu' => 'dashboard',
            'sub_menu' => null,
            'main_link' => 'dashboard',
        ],
        PermissionSlug::ACCESS_SETTINGS_NAV_LIST => [
            'name' => 'access-settings-nav-list',
            'description' => 'Access Home',
            'main_menu' => 'dashboard',
            'sub_menu' => null,
            'main_link' => 'dashboard',
        ],
        PermissionSlug::ACCESS_MASTER_NAV_LIST => [
            'name' => 'access-master-nav-list',
            'description' => 'Access Home',
            'main_menu' => 'dashboard',
            'sub_menu' => null,
            'main_link' => 'dashboard',
        ],
    //chat
        PermissionSlug::CHAT => [
            'name' => 'chat',
            'description' => 'Access Chat',
            'main_menu' => 'chat',
            'sub_menu' => null,
            'main_link' => 'chat',
        ],
        
    //Dashboard and Configurations
       PermissionSlug::ACCESS_DASHBOARD => [
            'name' => 'access-dashboard',
            'description' => 'Access Dashboard',
            'main_menu' => 'dashboard',
            'sub_menu' => null,
            'main_link' => 'dashboard',
        ],
        PermissionSlug::ACCESS_NOTIFICATIONS => [
            'name' => 'access-notifications',
            'description' => 'Access Notifications',
            'main_menu' => 'Notifications',
            'sub_menu' => null,
            'main_link' => 'Notifications',
        ],        
        PermissionSlug::ACCESS_OWNER_DASHBOARD => [
            'name' => 'access-owner-dashboard',
            'description' => 'Access Owner Dashboard',
            'main_menu' => 'owner-management',
            'sub_menu' => null,
            'main_link' => 'owner-dashboard',
        ],
        PermissionSlug::DISPATCHER => [
            'name' => 'dispatcher',
            'description' => 'Dispatcher',
            'main_menu' => 'dispatcher',
            'sub_menu' => null,
            'main_link' => 'dispatcher',
        ],
        PermissionSlug::CHAT => [
            'name' => 'chat',
            'description' => 'chat',
            'main_menu' => 'chat',
            'sub_menu' => null,
            'main_link' => 'chat',
        ],

        // promotions-management
        PermissionSlug::PROMOTIONS_MANAGEMENT => [
            'name' => 'promotion-management',
            'description' => 'promotion management',
            'main_menu' => 'promotion-management',
            'sub_menu' => null,
            'main_link' => 'promotion-management',
        ],
        // manage-promo
        PermissionSlug::MANAGE_PROMO => [
            'name' => 'manage-promo',
            'description' => 'View Promo code',
            'main_menu'=>'promotion-management',
            'sub_menu'=> null,
            'main_link'=>'promo',
        ],
        PermissionSlug::ADD_PROMO => [
            'name' => 'add-promo',
            'description' => 'add Promo code',
            'main_menu'=>'promotion-management',
            'sub_menu'=> null,
            'sub_link'=>null,
            'main_link'=>'promo',
        ],
        PermissionSlug::EDIT_PROMO => [
            'name' => 'edit-promo',
            'description' => 'edit promo',
            'main_menu'=>'promotion-management',
            'main_link'=>'promo',
        ],  
        PermissionSlug::DELETE_PROMO => [
            'name' => 'delete-promo',
            'description' => 'delete promo',
            'main_menu'=>'promotion-management',
            'main_link'=>'promo',    
        ],  
        PermissionSlug::TOGGLE_PROMO => [
            'name' => 'toggle-promo',
            'description' => 'toggle-promo',
            'main_menu'=>'promotion-management',
            'main_link'=>'promo',
        ],
        
        // banner-image
        PermissionSlug::BANNER_IMAGE => [
            'name' => 'banner_image',
            'description' => 'banner image',
            'main_menu'=>'promotion-management',
            'sub_menu'=> null,
            'main_link'=>'banner_image',
        ],
        PermissionSlug::ADD_BANNER_IMAGE => [
            'name' => 'add_banner_image',
            'description' => 'Add banner image',
            'main_menu'=>'promotion-management',
            'sub_menu'=> null,
            'main_link'=>'banner_image',
        ],       
        PermissionSlug::EDIT_BANNER_IMAGE => [
            'name' => 'edit_banner_image',
            'description' => 'Edit banner image',
            'main_menu'=>'promotion-management',
            'sub_menu'=> null,
            'main_link'=>'banner_image',
        ],  
        PermissionSlug::DELETE_BANNER_IMAGE => [
            'name' => 'delete_banner_image',
            'description' => 'delete banner image',
            'main_menu'=>'promotion-management',            
            'sub_menu'=> null,
            'main_link'=>'banner_image',

        ],  
        PermissionSlug::TOGGLE_BANNER_IMAGE => [
            'name' => 'toggle_banner_image',
            'description' => 'toggle banner image',
            'main_menu'=>'promotion-management',            
            'sub_menu'=> null,
            'main_link'=>'banner_image',
        ], 

 //price management 
        PermissionSlug::PRICE_MANAGEMENT => [
            'name' => 'price-management',
            'description' => 'price-management',
            'main_menu'=>'price-management',
            'main_link'=>'null',
        ],
/* service-Location */
        PermissionSlug::SERVICE_LOCATION => [
        'name' => 'service-Location',
        'description' => 'Available location for app',
        'main_menu'=>'price-management',
        'sub_menu'=> null,
        'main_link'=>'service-location',
        ],
        PermissionSlug::ADD_SERVICE_LOCATION => [
            'name' => 'add_service_Location',
            'description' => 'Add location for app',
            'main_menu'=>'price-management',
            'sub_menu'=> null,
            'main_link'=>'service-location',
        ],       
        PermissionSlug::EDIT_SERVICE_LOCATION => [
            'name' => 'edit_service_Location',
            'description' => 'Edit location for app',
            'main_menu'=>'price-management',
            'sub_menu'=> null,
            'main_link'=>'service-location',
        ],       
        PermissionSlug::DELETE_SERVICE_LOCATION => [
            'name' => 'delete_service_Location',
            'description' => 'Delete location for app',
            'main_menu'=>'price-management',
            'sub_menu'=> null,
            'main_link'=>'service-location',
        ],        
        PermissionSlug::TOGGLE_SERVICE_LOCATION => [
            'name' => 'toggle_service_Location',
            'description' => 'toggle location for app',
            'main_menu'=>'price-management',
            'sub_menu'=> null,
            'main_link'=>'service-location',
        ],

/* Zone */       
        PermissionSlug::VIEW_ZONE => [
            'name' => 'view-zone',
            'description' => 'View all zones',
            'main_menu'=>'price-management',
            'sub_menu'=>null,
            'main_link'=>'zone',
        ],
        PermissionSlug::VIEW_ZONE_MAP => [
            'name' => 'view-zone-map',
            'description' => 'map-view',
            'main_menu'=>'price-management',
            'sub_menu'=>null,
            'main_link'=>'zone',
        ],
        
        PermissionSlug::ADD_ZONE => [
            'name' => 'add-zones',
            'description' => 'Add zones',
            'main_menu'=>'price-management',
            'sub_menu'=>null,
            'main_link'=>'zone',
        ],
        PermissionSlug::EDIT_ZONE => [
            'name' => 'edit-zones',
            'description' => 'Edit zones',
            'main_menu'=>'price-management',
            'sub_menu'=>null,
            'main_link'=>'zone',
        ],
        PermissionSlug::DELETE_ZONE => [
            'name' => 'delete-zones',
            'description' => 'Get all zones',
            'main_menu'=>'price-management',
            'sub_menu'=>null,
            'main_link'=>'zone',
        ],
        PermissionSlug::TOGGLE_ZONE => [
            'name' => 'toggle-zone',
            'description' => 'Toggle Status Zones',
            'main_menu'=>'price-management',
            'sub_menu'=>null,
            'main_link'=>'zone',
        ],
        PermissionSlug::ZONE_SURGE => [
            'name' => 'zone-surge',
            'description' => 'zone Surge',
            'main_menu'=>'price-management',
            'sub_menu'=>null,
            'main_link'=>'zone',
        ],
        PermissionSlug::UPDATE_ZONE_SURGE => [
            'name' => 'update-zone-surge',
            'description' => 'Update zone Surge',
            'main_menu'=>'price-management',
            'sub_menu'=>null,
            'main_link'=>'zone',
        ],
/* Vehicle Type */
        PermissionSlug::VEHICLE_TYPES => [
            'name' => 'view-vehicle-types',
            'description' => 'Get all types',
            'main_menu'=>'price-management',
            'sub_menu'=>'null',
            'main_link'=>'vehicle-type',

        ],   
        PermissionSlug::ADD_TYPES => [
            'name' => 'add-vehicle-types',
            'description' => 'Get all types',
            'main_menu'=>'price-management',
            'sub_menu'=>'null',
            'main_link'=>'vehicle-type',

        ],  
        PermissionSlug::EDIT_TYPES => [
            'name' => 'edit-vehicle-types',
            'description' => 'Get all types',
            'main_menu'=>'price-management',
            'sub_menu'=>'null',
            'main_link'=>'vehicle-type',

        ],  
        PermissionSlug::DELETE_TYPES => [
            'name' => 'delete-vehicle-types',
            'description' => 'Get all types',
            'main_menu'=>'price-management',
            'sub_menu'=>'null',
            'main_link'=>'vehicle-type',

        ],  
        PermissionSlug::TOGGLE_TYPES => [
            'name' => 'toggle-vehicle-types',
            'description' => 'Get all types',
            'main_menu'=>'price-management',
            'sub_menu'=>'null',
            'main_link'=>'vehicle-type',
        ], 
// rental package type  
        PermissionSlug::RENTAL_PACKAGE => [
            'name' => 'rental-package',
            'description' => 'rental-package',
            'main_menu'=>'price-management',
            'sub_menu'=>null,
            'sub_link'=>null,
            'main_link'=>'rental-package',
        ],   
        PermissionSlug::ADD_RENTAL_PACKAGE => [
            'name' => 'add-rental-package',
            'description' => 'add-rental-package',
            'main_menu'=>'price-management',
            'sub_menu'=>null,
            'sub_link'=>null,
            'main_link'=>'rental-package',
        ],  
        PermissionSlug::EDIT_RENTAL_PACKAGE => [
            'name' => 'edit-rental-package',
            'description' => 'edit-rental-package',
            'main_menu'=>'price-management',
            'sub_menu'=>null,
            'sub_link'=>null,
            'main_link'=>'rental-package',
        ],   
        PermissionSlug::DELETE_RENTAL_PACKAGE => [
            'name' => 'delete-rental-package',
            'description' => 'delete-rental-package',
            'main_menu'=>'price-management',
            'sub_menu'=>null,
            'sub_link'=>null,
            'main_link'=>'rental-package',
        ],  
        PermissionSlug::TOGGLE_RENTAL_PACKAGE => [
            'name' => 'toggle-rental-package',
            'description' => 'toggle-rental-package',
            'main_menu'=>'price-management',
            'sub_menu'=>null,
            'sub_link'=>null,
            'main_link'=>'rental-package',
        ],
/* Vehicle Fare */
        PermissionSlug::SET_PRICE => [
            'name' => 'vehicle-fare',
            'description' => 'view-vehicle_fare',
            'main_menu'=>'price-management',
            'sub_menu'=>null,
            'sub_link'=>null,
            'main_link'=>'vehicle-fare',

        ],   
        PermissionSlug::ADD_PRICE => [
            'name' => 'add-price',
            'description' => 'add price',
            'main_menu'=>'price-management',
            'sub_menu'=>null,
            'sub_link'=>null,
            'main_link'=>'vehicle-fare',

        ],  
        PermissionSlug::EDIT_PRICE => [
            'name' => 'edit-price',
            'description' => 'edit price',
            'main_menu'=>'price-management',
            'sub_menu'=>null,
            'sub_link'=>null,
            'main_link'=>'vehicle-fare',

        ],  
        PermissionSlug::DELETE_PRICE => [
            'name' => 'delete-price',
            'description' => 'delete price',
            'main_menu'=>'price-management',
            'sub_menu'=>null,
            'sub_link'=>null,
            'main_link'=>'vehicle-fare',

        ],  
        PermissionSlug::TOGGLE_PRICE => [
            'name' => 'toggle-price',
            'description' => 'toggle price',
            'main_menu'=>'price-management',
            'sub_menu'=>null,
            'sub_link'=>null,
            'main_link'=>'vehicle-fare',
        ], 
        PermissionSlug::ADD_PACKAGE_PRICE => [
            'name' => 'add-package-price',
            'description' => 'package price adding',
            'main_menu'=>'price-management',
            'sub_menu'=>null,
            'sub_link'=>null,
            'main_link'=>'vehicle-fare',
        ],  
        //Manage Goods Type
        PermissionSlug::GOODS_TYPES => [
            'name' => 'manage-goods-types',
            'description' => 'List Package Type',
            'main_menu'=>'price-management',
            'sub_menu'=>'goods_types',
            'sub_link'=>'goods_types',
        ],
        PermissionSlug::ADD_GOODS_TYPES => [
            'name' => 'add-goods-types',
            'description' => 'Add Goods Types',
            'main_menu'=>'price-management',
            'sub_menu'=>'goods_types',
            'sub_link'=>'goods_types',
        ],         
        PermissionSlug::EDIT_GOODS_TYPES => [
            'name' => 'edit-goods-types',
            'description' => 'Edit Goods Types',
            'main_menu'=>'price-management',
            'sub_menu'=>'goods_types',
            'sub_link'=>'goods_types',
        ],         
        PermissionSlug::DELETE_GOODS_TYPES => [
            'name' => 'delete-goods-types',
            'description' => 'Delete Goods Types',
            'main_menu'=>'price-management',
            'sub_menu'=>'goods_types',
            'sub_link'=>'goods_types',
        ], 
        PermissionSlug::TOGGLE_GOODS_TYPES => [
            'name' => 'toggle-goods-types',
            'description' => 'toggle Goods Types',
            'main_menu'=>'price-management',
            'sub_menu'=>'goods_types',
            'sub_link'=>'goods_types',
        ],   



/*geo-fencing*/
        PermissionSlug::GEO_FENCING => [
            'name' => 'geo-fencing',
            'description' => 'geo-fencing',
            'main_menu'=>'geo-fencing',
            'sub_menu'=> null,
            'main_link'=>'geo-fencing',
        ],
        PermissionSlug::GODS_EYE => [
            'name' => 'gods-eye',
            'description' => 'gods-eye',
            'main_menu'=>'geo-fencing',
            'sub_menu'=> null,
            'main_link'=>'gods-eye',
        ], 
        PermissionSlug::HEAT_MAP => [
            'name' => 'heat-map',
            'description' => 'View Heat Map',
            'main_menu'=>'geo-fencing',
            'sub_menu'=> null,
            'main_link'=>'heat-map',
        ],

/*complaint management*/
        PermissionSlug::COMPLAINTS_MANAGE => [
            'name' => 'complaints-management',
            'description' => 'View Complaints',
            'main_menu'=>'complaints-management',
            'sub_menu'=> null,
            'main_link'=>'complaint',
        ],
        PermissionSlug::COMPLAINT_TITLE => [
            'name' => 'complaint-title',
            'description' => 'View Complaint Title',
            'main_menu'=>'complaints-management',
            'sub_menu'=> null,
            'main_link'=>'complaint-title',
        ],

        PermissionSlug::ADD_COMPLAINT_TITLE => [
            'name' => 'add-complaint-title',
            'description' => 'View Complaint Title',
            'main_menu'=>'complaints-management',
            'sub_menu'=> null,
            'main_link'=>'complaint-title',
        ],
        PermissionSlug::EDIT_COMPLAINT_TITLE => [
            'name' => 'edit-complaint-title',
            'description' => 'View Complaint Title',
            'main_menu'=>'complaints-management',
            'sub_menu'=> null,
            'main_link'=>'complaint-title',
        ],
        PermissionSlug::DELETE_COMPLAINT_TITLE => [
            'name' => 'delete-complaint-title',
            'description' => 'View Complaint Title',
            'main_menu'=>'complaints-management',
            'sub_menu'=> null,
            'main_link'=>'complaint-title',
        ],
        PermissionSlug::TOGGLE_COMPLAINT_TITLE => [
            'name' => 'toggle-complaint-title',
            'description' => 'View Complaint Title',
            'main_menu'=>'complaints-management',
            'sub_menu'=> null,
            'main_link'=>'complaint-title',
        ],
        
        PermissionSlug::USER_COMPLAINT => [
            'name' => 'user-complaint',
            'description' => 'Manange User Complaint',
            'main_menu'=>'complaints-management',
            'sub_menu'=> null,
            'main_link'=>'users-complaint',
        ],
        PermissionSlug::DRIVER_COMPLAINT => [
            'name' => 'driver-complaint',
            'description' => 'Manage Driver Complaint',
            'main_menu'=>'complaints-management',
            'sub_menu'=> null,
            'main_link'=>'drivers-complaint',
        ],
        PermissionSlug::OWNER_COMPLAINT => [
            'name' => 'owner-complaint',
            'description' => 'Manage Owner Complaint',
            'main_menu'=>'complaints-management',
            'sub_menu'=> null,
            'main_link'=>'owners-complaint',
        ],

/*others*/
        PermissionSlug::OTHERS => [
            'name' => 'others',
            'description' => 'Access others',
            'main_menu' => 'others',
            'sub_menu' => null,
            'main_link' => 'others',
        ],
/*sos*/
        PermissionSlug::VIEW_SOS => [
            'name' => 'view-sos',
            'description' => 'Emergency Numbers',
            'main_menu'=>'others',
            'sub_menu'=> null,
            'main_link'=>'sos',
        ],
        PermissionSlug::ADD_SOS => [
            'name' => 'add-sos',
            'description' => 'add sos',
            'main_menu'=>'others',
            'main_link'=>'sos',

        ],  
        PermissionSlug::EDIT_SOS => [
            'name' => 'edit-sos',
            'description' => 'edit sos',
            'main_menu'=>'others',
            'main_link'=>'sos',

        ],  
        PermissionSlug::DELETE_SOS => [
            'name' => 'delete-sos',
            'description' => 'delete sos',
            'main_menu'=>'others',
            'main_link'=>'sos',

        ],  
        PermissionSlug::TOGGLE_SOS => [
            'name' => 'toggle-sos',
            'description' => 'toggle-sos',
            'main_menu'=>'others',
            'main_link'=>'sos',
        ],


        /*mail template*/
        // PermissionSlug::VIEW_MAIL_TEMPLATE => [
        //     'name' => 'view-mail-template',
        //     'description' => 'mail Template',
        //     'main_menu'=>'others',
        //     'sub_menu'=> null,
        //     'main_link'=>'mail-template',
        // ],
        // PermissionSlug::ADD_MAIL_TEMPLATE => [
        //     'name' => 'add-mail-template',
        //     'description' => 'add mail Template',
        //     'main_menu'=>'others',
        //     'main_link'=>'mail-template',

        // ],  
        // PermissionSlug::EDIT_MAIL_TEMPLATE => [
        //     'name' => 'edit-mail-template',
        //     'description' => 'edit mail Template',
        //     'main_menu'=>'others',
        //     'main_link'=>'mail-template',

        // ],  
        // PermissionSlug::DELETE_MAIL_TEMPLATE => [
        //     'name' => 'delete-mail-template',
        //     'description' => 'delete mail Template',
        //     'main_menu'=>'others',
        //     'main_link'=>'mail-template',

        // ],  
        // PermissionSlug::TOGGLE_MAIL_TEMPLATE => [
        //     'name' => 'toggle-mail-template',
        //     'description' => 'toggle mail Template',
        //     'main_menu'=>'others',
        //     'main_link'=>'mail-template',
        // ],
//Push Notifications
        PermissionSlug::VIEW_NOTIFICATIONS => [
            'name' => 'view-notifications',
            'description' => 'view notifications',
            'main_menu'=>'promotion-management',
            'main_link'=>'notifications',
    
        ],  
        PermissionSlug::ADD_NOTIFICATIONS => [
            'name' => 'add-notifications',
            'description' => 'view notifications',
            'main_menu'=>'promotion-management',
            'main_link'=>'notifications',
    
        ],  
        PermissionSlug::EDIT_NOTIFICATIONS => [
            'name' => 'edit-notifications',
            'description' => 'edit notifications',
            'main_menu'=>'promotion-management',
            'main_link'=>'notifications',
        ],       
        PermissionSlug::DELETE_NOTIFICATIONS => [
            'name' => 'delete-notification',
            'description' => 'delete notifications',
            'main_menu'=>'promotion-management',
            'main_link'=>'notifications',
        ], 
        
// manage-cancellation reason
        PermissionSlug::VIEW_CANCELLATION_REASON => [
            'name' => 'view-cancellation',
            'description' => 'View Cancellation Reason',
            'main_menu'=>'others',
            'sub_menu'=> null,
            'main_link'=>'cancellation',
        ],
        PermissionSlug::ADD_CANCELLATION => [
            'name' => 'add-cancellation',
            'description' => 'add cancellation',
            'main_menu'=>'others',
            'sub_menu'=> null,
            'main_link'=>'cancellation',

        ],
        PermissionSlug::EDIT_CANCELLATION => [
            'name' => 'edit-cancellation',
            'description' => 'edit cancellation',
            'main_menu'=>'others',
            'sub_menu'=>null,
            'main_link'=>'cancellation',
    
        ],  
        PermissionSlug::DELETE_CANCELLATION => [
            'name' => 'delete-cancellation',
            'description' => 'delete cancellation',
            'main_menu'=>'others',
            'sub_menu'=>null,
            'main_link'=>'cancellation',
    
        ],  
        PermissionSlug::TOGGLE_CANCELLATION => [
            'name' => 'toggle-cancellation',
            'description' => 'toggle cancellation',
            'main_menu'=>'others',
            'sub_menu'=>null,
            'main_link'=>'cancellation',
        ],

// manage-FAQ
        PermissionSlug::VIEW_FAQ => [
            'name' => 'view-faq',
            'description' => 'View FAQ',
            'main_menu'=>'others',
            'sub_menu'=> null,
            'main_link'=>'faq',
        ],
        PermissionSlug::ADD_FAQ => [
            'name' => 'add-faq',
            'description' => 'add FAQ',
            'main_menu'=>'others',
            'sub_menu'=> null,
            'main_link'=>'faq',

        ],
        PermissionSlug::EDIT_FAQ => [
            'name' => 'edit-faq',
            'description' => 'edit FAQ',
            'main_menu'=>'others',
            'sub_menu'=>null,
            'main_link'=>'faq',
    
        ],  
        PermissionSlug::DELETE_FAQ => [
            'name' => 'delete-faq',
            'description' => 'delete FAQ',
            'main_menu'=>'others',
            'sub_menu'=>null,
            'main_link'=>'faq',
    
        ],  
        PermissionSlug::TOGGLE_FAQ => [
            'name' => 'toggle-faq',
            'description' => 'toggle FAQ',
            'main_menu'=>'others',
            'sub_menu'=>null,
            'main_link'=>'faq',
        ],
    
/* Users */
        PermissionSlug::USER_MANAGEMENT => [
            'name' => 'user-management',
            'description' => 'View all user related menus',
            'main_menu' => 'user-management',
            'sub_menu' => null,
        ],
        PermissionSlug::VIEW_USERS => [
            'name' => 'view-users',
            'description' => 'Get all Users',
            'main_menu'=>'user-management',
            'sub_menu'=>null,
            'main_link'=>'users',
        ],
        PermissionSlug::ADD_USER => [
            'name' => 'add-user',
            'description' => 'add user',
            'main_menu'=>'user-management',
            'sub_menu'=>null,
            'main_link'=>'add-user',

        ],  
        PermissionSlug::EDIT_USER => [
            'name' => 'edit-user',
            'description' => 'edit user',
            'main_menu'=>'user-management',
            'sub_menu'=>null,
            'main_link'=>'edit-user',

        ],  
        PermissionSlug::DELETE_USER => [
            'name' => 'delete-user',
            'description' => 'delete user',
            'main_menu'=>'user-management',
            'sub_menu'=>null,
            'main_link'=>'delete-user',

        ],  
        PermissionSlug::TOGGLE_USER => [
            'name' => 'toggle-user',
            'description' => 'toggle-user',
            'main_menu'=>'user-management',
            'sub_menu'=>null,
            'main_link'=>'toggle-user',
        ],
        PermissionSlug::VIEW_USER_PROFILE => [
            'name' => 'view-user-profile',
            'description' => 'view Users profile',
            'main_menu'=>'user-management',
            'sub_menu'=>null,
            'main_link'=>'view-user-profile',
        ],
        PermissionSlug::VIEW_DELETE_USER => [
            'name' => 'view-delete-user',
            'description' => 'view Deleted Users',
            'main_menu'=>'user-management',
            'sub_menu'=>null,
            'main_link'=>'view-delete-user',
        ],

/* Drivers management */
        PermissionSlug::DRIVERS_MANAGEMENT => [
            'name' => 'drivers-management',
            'description' => 'drivers management',
            'main_menu' => 'drivers-management',
            'sub_menu' => null,
        ],
        PermissionSlug::VIEW_APPROVAL_PENDING_DRIVERS => [
            'name' => 'view-approval-pending-drivers',
            'description' => 'Get-All-approval-pending-Drivers',
            'main_menu'=>'drivers-management',
            'sub_menu'=>null,
            'main_link'=>'view-approval-pending-drivers',
        ],
        PermissionSlug::VIEW_APPROVED_DRIVERS => [
            'name' => 'view-approved-drivers',
            'description' => 'Get all approved drivers',
            'main_menu'=>'drivers-management',
            'sub_menu'=>null,
            'main_link'=>'view-approved-drivers',
        ],
        PermissionSlug::ADD_DRIVER => [
            'name' => 'add-driver',
            'description' => 'add drivers',
            'main_menu'=>'drivers-management',
            'sub_menu'=>null,
            'main_link'=>'add-driver',
        ],
        PermissionSlug::EDIT_DRIVER => [
            'name' => 'edit-driver',
            'description' => 'edit drivers',
            'main_menu'=>'drivers-management',
            'sub_menu'=>null,
            'main_link'=>'edit-driver',
        ],
        PermissionSlug::DELETE_DRIVER => [
            'name' => 'delete-driver',
            'description' => 'delete drivers',
            'main_menu'=>'drivers-management',
            'sub_menu'=>null,
            'main_link'=>'delete-driver',
        ],
        PermissionSlug::VIEW_DRIVER_PROFILE => [
            'name' => 'view-driver-profile',
            'description' => 'View drivers Profile',
            'main_menu'=>'drivers-management',
            'sub_menu'=>null,
            'main_link'=>'view-driver-profile',
        ],
        PermissionSlug::DRIVER_APPROVAL => [
            'name' => 'driver-approval',
            'description' => 'drivers approval',
            'main_menu'=>'drivers-management',
            'sub_menu'=>null,
            'main_link'=>'driver-approval',
        ],
        PermissionSlug::DRIVER_DISAPPROVAL => [
            'name' => 'driver-disapproval',
            'description' => 'drivers disapproval',
            'main_menu'=>'drivers-management',
            'sub_menu'=>null,
            'main_link'=>'driver-disapproval',
        ],
        PermissionSlug::DRIVER_UPLOAD_DOCUMENTS => [
            'name' => 'driver-upload-documents',
            'description' => 'drivers documents upload',
            'main_menu'=>'drivers-management',
            'sub_menu'=>null,
            'main_link'=>'driver-upload-documents',
        ],
        PermissionSlug::DRIVER_RATING_LIST => [
            'name' => 'driver-rating-list',
            'description' => 'drivers rating list',
            'main_menu'=>'drivers-management',
            'sub_menu'=>null,
            'main_link'=>'driver-rating-list',
        ],
        PermissionSlug::DRIVER_RATING_VIEW => [
            'name' => 'driver-rating-view',
            'description' => 'drivers rating view',
            'main_menu'=>'drivers-management',
            'sub_menu'=>null,
            'main_link'=>'driver-rating-view',
        ],
        PermissionSlug::DRIVER_WALLET => [
            'name' => 'driver-wallet',
            'description' => 'drivers rating view',
            'main_menu'=>'drivers-management',
            'sub_menu'=>null,
            'main_link'=>'driver-wallet',
        ],
        PermissionSlug::NEGETIVE_BALANCE_DRIVERS => [
            'name' => 'negetive-balance-drivers',
            'description' => 'drivers rating view',
            'main_menu'=>'drivers-management',
            'sub_menu'=>null,
            'main_link'=>'driver-wallet',
            'sub_link'=>'negetive-balance-drivers',
        ],
        PermissionSlug::WITHDRAWAL_REQUEST_DRIVERS => [
            'name' => 'withdrawal-request-drivers',
            'description' => 'drivers rating view',
            'main_menu'=>'drivers-management',
            'sub_menu'=>null,
            'main_link'=>'driver-wallet',
            'sub_link'=>'withdrawal-request-drivers',
        ],
        PermissionSlug::DELETE_REQ_DRIVERS => [
            'name' => 'delete-request-drivers',
            'description' => 'deleted drivers view',
            'main_menu'=>'drivers-management',
            'sub_menu'=>null,
            'main_link'=>'delete-request-drivers',
        ],

// Manage Driver Needed Doc
        PermissionSlug::MANAGE_DRIVER_NEEDED_DOC => [
            'name' => 'manage-driver-needed-document',
            'description' => 'List driver needed documents',
            'main_menu'=>'drivers-management',
            'sub_menu'=>'needed_doc',
            'sub_link'=>'needed_doc',
        ],     
        PermissionSlug::ADD_DRIVER_NEEEDED_DOC => [
            'name' => 'add-driver-needed-document',
            'description' => 'Add driver needed documents',
            'main_menu'=>'drivers-management',
            'sub_menu'=>'needed_doc',
            'sub_link'=>'needed_doc',
        ],         
        PermissionSlug::EDIT_DRIVER_NEEEDED_DOC => [
            'name' => 'edit-driver-needed-document',
            'description' => 'Edit driver needed documents',
            'main_menu'=>'drivers-management',
            'sub_menu'=>'needed_doc',
            'sub_link'=>'needed_doc',
        ],         
        PermissionSlug::DELETE_DRIVER_NEEEDED_DOC => [
            'name' => 'delete-driver-needed-document',
            'description' => 'Delete driver needed documents',
            'main_menu'=>'drivers-management',
            'sub_menu'=>'needed_doc',
            'sub_link'=>'needed_doc',
        ], 
        PermissionSlug::TOGGLE_DRIVER_NEEEDED_DOC => [
            'name' => 'toggle-driver-needed-documentt',
            'description' => 'toggle-driver-needed-document',
            'main_menu'=>'drivers-management',
            'sub_menu'=>'needed_doc',
            'sub_link'=>'needed_doc',
        ], 
        PermissionSlug::MANAGE_DRIVER_BANK_INFO => [
            'name' => 'manage-driver-bank-info',
            'description' => 'List driver bank info',
            'main_menu'=>'drivers-management',
            'sub_menu'=>'bank_info',
            'sub_link'=>'bank_info',
        ],  
        PermissionSlug::TOGGLE_BANK_INFO => [
            'name' => 'toggle-bank-info',
            'description' => 'List driver bank info',
            'main_menu'=>'drivers-management',
            'sub_menu'=>'bank_info',
            'sub_link'=>'bank_info',
        ],   
        // manage-Subscription
        PermissionSlug::MANAGE_SUBSCRIPTION => [
            'name' => 'manage-subscription',
            'description' => 'View Promo code',
            'main_menu'=>'drivers-management',
            'sub_menu'=> null,
            'main_link'=>'subscription',
        ],
        PermissionSlug::ADD_SUBSCRIPTION => [
            'name' => 'add-subscription',
            'description' => 'add Promo code',
            'main_menu'=>'drivers-management',
            'sub_menu'=> null,
            'sub_link'=>null,
            'main_link'=>'subscription',
        ],
        PermissionSlug::EDIT_SUBSCRIPTION => [
            'name' => 'edit-subscription',
            'description' => 'edit subscription',
            'main_menu'=>'drivers-management',
            'main_link'=>'subscription',
        ],  
        PermissionSlug::DELETE_SUBSCRIPTION => [
            'name' => 'delete-subscription',
            'description' => 'delete subscription',
            'main_menu'=>'drivers-management',
            'main_link'=>'subscription',    
        ],  
        PermissionSlug::TOGGLE_SUBSCRIPTION => [
            'name' => 'toggle-subscription',
            'description' => 'toggle-subscription',
            'main_menu'=>'drivers-management',
            'main_link'=>'subscription',
        ], 
            
        /* loyalty rewards */
        PermissionSlug::LOYALTY_REWARDS => [
            'name' => 'loyalty-rewards',
            'description' => 'Loyalty Rewards',
            'main_menu'=>'loyalty-rewards',
            'sub_menu'=> null,
            'main_link'=>'loyalty-rewards',
        ], 
        // incentives
        PermissionSlug::INCENTIVE => [
            'name' => 'incentives',
            'description' => 'incentives',
            'main_menu'=>'loyalty-rewards',
            'sub_menu'=>null,
            'sub_link'=>null,
            'main_link'=>'incentives',

        ],   
        PermissionSlug::UPDATE_INCENTIVE => [
            'name' => 'update-incentives',
            'description' => 'update incentives',
            'main_menu'=>'loyalty-rewards',
            'sub_menu'=>null,
            'sub_link'=>null,
            'main_link'=>'incentives',

        ],  
        // PermissionSlug::EDIT_PRICE => [
        //     'name' => 'edit-price',
        //     'description' => 'edit price',
        //     'main_menu'=>'loyalty-rewards',
        //     'sub_menu'=>null,
        //     'sub_link'=>null,
        //     'main_link'=>'vehicle-fare',

        // ],  
        PermissionSlug::REFERRAL_SETTINGS_VIEW => [
            'name' => 'referral-settings-view',
            'description' => 'referral settings view lists',
            'main_menu'=>'loyalty-rewards',
            'sub_menu'=>null,
            'main_link'=>'referral-settings-view',
        ],  

        PermissionSlug::VIEW_DRIVERS_LEVELUP => [
            'name' => 'view-drivers-levelup',
            'description' => 'View Driver Levelup',
            'main_menu'=>'loyalty-rewards',
            'sub_menu'=>null,
            'main_link'=>'view-drivers-levelup',
        ],
        PermissionSlug::CHANGE_REWARD_VALUE => [
            'name' => 'change-reward-value',
            'description' => 'Change Rward Value',
            'main_menu'=>'loyalty-rewards',
            'sub_menu'=>null,
            'main_link'=>'change-reward-value',
        ],
        PermissionSlug::ADD_DRIVER_LEVELUP => [
            'name' => 'add-drivers-levelup',
            'description' => 'add drivers',
            'main_menu'=>'loyalty-rewards',
            'sub_menu'=>null,
            'main_link'=>'add-drivers-levelup',
        ],
        PermissionSlug::EDIT_DRIVER_LEVELUP => [
            'name' => 'edit-drivers-levelup',
            'description' => 'edit drivers',
            'main_menu'=>'loyalty-rewards',
            'sub_menu'=>null,
            'main_link'=>'edit-drivers-levelup',
        ],
        PermissionSlug::DELETE_DRIVER_LEVELUP => [
            'name' => 'delete-driver',
            'description' => 'delete drivers',
            'main_menu'=>'loyalty-rewards',
            'sub_menu'=>null,
            'main_link'=>'delete-drivers-levelup',
        ],

/* Admin */
        PermissionSlug::ADMIN_MANAGEMENT => [
            'name' => 'admin-management',
            'description' => 'Admin  List',
            'main_menu'=>'admin',
            'sub_menu'=> null,
            'main_link'=>'admin',
        ], 
        PermissionSlug::ADMIN => [
            'name' => 'admin',
            'description' => 'Admin  List',
            'main_menu'=>'admin',
            'sub_menu'=> null,
            'main_link'=>'admin',
        ],    
        PermissionSlug::CREATE_ADMIN => [
            'name' => 'add-admin',
            'description' => 'create admin',
            'main_menu'=>'admin',
            'sub_menu'=> null,
            'main_link'=>'admin',
        ],        
        PermissionSlug::EDIT_ADMIN => [
            'name' => 'edit-admin',
            'description' => 'Edit Admin',
            'main_menu'=>'admin',
            'sub_menu'=> null,
            'main_link'=>'admin',
        ],       
        PermissionSlug::DELETE_ADMIN => [
            'name' => 'delete-admin',
            'description' => 'Delete Admin',
            'main_menu'=>'admin',
            'sub_menu'=> null,
            'main_link'=>'admin',
        ],
        PermissionSlug::TOGGLE_ADMIN => [
            'name' => 'toggle-admin',
            'description' => 'toggle status admin',
            'main_menu'=>'admin',
            'sub_menu'=> null,
            'main_link'=>'admin',
        ], 

/*Reports*/  
        PermissionSlug::REPORT_MANAGEMENT => [
            'name' => 'report-management',
            'description' => 'View reports',
            'main_menu'=>'report-management',
            'sub_menu'=> null,
            'main_link'=>'report-management',
        ],
        PermissionSlug::USER_REPORT => [
            'name' => 'user-report',
            'description' => 'Download User Report',
            'main_menu'=>'report-management',
            'sub_menu'=> null,
            'main_link'=>'user-report',
        ],
        PermissionSlug::DRIVER_REPORT => [
            'name' => 'driver-report',
            'description' => 'Download Driver Report',
            'main_menu'=>'report-management',
            'sub_menu'=> null,
            'main_link'=>'driver-report',
        ],
        PermissionSlug::DRIVER_DUTY_REPORT => [
            'name' => 'driver-duty-report',
            'description' => 'Download Driver Report',
            'main_menu'=>'report-management',
            'sub_menu'=> null,
            'main_link'=>'driver-duty-report',
        ],
        PermissionSlug::FINANCE_REPORT => [
            'name' => 'finance-report',
            'description' => 'Download Finance Report',
            'main_menu'=>'report-management',
            'sub_menu'=> null,
            'main_link'=>'finance-report',
        ],
        PermissionSlug::OWNER_REPORT => [
            'name' => 'owner-report',
            'description' => 'Download Owner Report',
            'main_menu'=>'report-management',
            'sub_menu'=> null,
            'main_link'=>'owner-report',
        ],  
        PermissionSlug::FLEET_REPORT => [
            'name' => 'fleet-report',
            'description' => 'Download Finance Report',
            'main_menu'=>'report-management',
            'sub_menu'=> null,
            'main_link'=>'fleet-report',
        ],       
/* owner management */
        PermissionSlug::OWNER_MANAGEMENT => [
            'name' => 'owner-management',
            'description' => 'Owner management',
            'main_menu' => 'owner-management',
            'sub_menu' => null,
            'main_link' => 'owners',
        ],
        PermissionSlug::MANAGE_OWNER => [
            'name' => 'manage-owners',
            'description' => 'Owner list',
            'main_menu' => 'owner-management',
            'sub_menu' => 'manage-owners',
            'main_link' => 'owners',
        ],
        PermissionSlug::ADD_OWNER => [
            'name' => 'add-owner',
            'description' => 'Add Owner',
            'main_menu' => 'owner-management',
            'sub_menu' => 'manage-owners',
            'main_link' => 'add-owner',
        ],
        PermissionSlug::EDIT_OWNER => [
            'name' => 'edit-owner',
            'description' => 'Edit Owner',
            'main_menu' => 'owner-management',
            'sub_menu' => 'manage-owners',
            'main_link' => 'edit-owner',
        ],
        PermissionSlug::DELETE_OWNER => [
            'name' => 'delete-owner',
            'description' => 'Delete Owner',
            'main_menu' => 'owner-management',
            'sub_menu' => 'manage-owners',
            'main_link' => 'delete-owner',
        ],
        PermissionSlug::VIEW_OWNER_PROFILE => [
            'name' => 'view-owner-profile',
            'description' => 'View owner Profile',
            'main_menu'=>'owner-management',
            'sub_menu'=>null,
            'main_link'=>'view-owner-profile',
        ],
        PermissionSlug::TOGGLE_OWNER_STATUS => [
            'name' => 'toggle-owner',
            'description' => 'Toggle Owner Status',
            'main_menu' => 'owner-management',
            'sub_menu' => 'manage-owners',
            'main_link' => 'toggle-owner',
        ],
        PermissionSlug::VIEW_OWNER_DOCUMENT => [
            'name' => 'view-owner-document',
            'description' => 'View Owner Document',
            'main_menu' => 'owner-management',
            'sub_menu' => 'manage-owners',
            'main_link' => 'view-owner-document',
        ],
        PermissionSlug::VIEW_DELETE_OWNER => [
            'name' => 'view-delete-owner',
            'description' => 'View Delete Owner',
            'main_menu' => 'owner-management',
            'sub_menu' => 'manage-owners',
            'main_link' => 'view-delete-owner',
        ],

// Manage Owner Needed Doc
        PermissionSlug::MANAGE_OWNER_NEEDED_DOC => [
            'name' => 'manage-owner-needed-document',
            'description' => 'List owner needed documents',
            'main_menu'=>'owner-management',
            'sub_menu'=>null,
            'main_link'=>'manage-owner-needed-document',
        ],
        PermissionSlug::ADD_OWNER_NEEEDED_DOC => [
            'name' => 'add-owner-needed-document',
            'description' => 'Add Owner needed documents',
            'main_menu'=>'owner-management',
            'sub_menu'=>null,
            'main_link'=>'add-owner-needed-document',
        ], 
        PermissionSlug::EDIT_OWNER_NEEEDED_DOC => [
            'name' => 'edit-owner-needed-document',
            'description' => 'Edit Owner needed documents',
            'main_menu'=>'owner-management',
            'sub_menu'=>null,
            'main_link'=>'edit-owner-needed-document',
        ], 
        PermissionSlug::DELETE_OWNER_NEEEDED_DOC => [
            'name' => 'delete-owner-needed-document',
            'description' => 'Delete Owner needed documents',
            'main_menu'=>'owner-management',
            'sub_menu'=>null,
            'main_link'=>'delete-owner-needed-document',
        ], 
        PermissionSlug::TOGGLE_OWNER_NEEEDED_DOC => [
            'name' => 'toggle-owner-needed-document',
            'description' => 'toggle Owner needed document',
            'main_menu'=>'owner-management',
            'sub_menu'=>null,
            'main_link'=>'toggle-owner-needed-document',
        ], 

/*support-category*/
        PermissionSlug::VIEW_SUPPORT_MANAGEMENT => [
            'name' => 'view-support-management',
            'description' => 'View Support Ticket Management',
            'main_menu' => 'support-management',
            'sub_menu' => null,
            'main_link' => 'support-management',
        ],

        PermissionSlug::VIEW_TICKET_TITLE => [
            'name' => 'view-ticket-title',
            'description' => 'View ticket title',
            'main_menu'=>'support-management',
            'sub_menu'=> null,
            'main_link'=>'support-management',
        ],
        PermissionSlug::ADD_TICKET_TITLE => [
            'name' => 'add-ticket-title',
            'description' => 'Add ticket title',
            'main_menu'=>'support-management',
            'sub_menu'=> null,
            'main_link'=>'support-management',
        ],
        PermissionSlug::EDIT_TICKET_TITLE => [
            'name' => 'edit-ticket-title',
            'description' => 'Edit ticket title',
            'main_menu'=>'support-management',
            'sub_menu'=> null,
            'main_link'=>'support-management',
        ],
        PermissionSlug::DELETE_TICKET_TITLE => [
            'name' => 'delete-ticket-title',
            'description' => 'Delete ticket title',
            'main_menu'=>'support-management',
            'sub_menu'=> null,
            'main_link'=>'support-management',
        ],
        PermissionSlug::TOGGLE_TICKET_TITLE => [
            'name' => 'toggle-ticket-title',
            'description' => 'Toggle ticket title',
            'main_menu'=>'support-management',
            'sub_menu'=> null,
            'main_link'=>'support-management',
        ],

        PermissionSlug::VIEW_SUPPORT_TICKET => [
            'name' => 'view-support-ticket',
            'description' => 'View support ticket',
            'main_menu'=>'support-management',
            'sub_menu'=> null,
            'main_link'=>'support-management',
        ],

/* Manage Fleet */
        PermissionSlug::MANAGE_FLEET => [
            'name' => 'manage-fleet',
            'description' => 'Manage fleets',
            'main_menu' => 'owner-management',
            'sub_menu' => null,
            'main_link' => 'manage-fleet',
        ],
        PermissionSlug::VIEW_FLEET => [
            'name' => 'view-fleet',
            'description' => 'view fleets',
            'main_menu' => 'owner-management',
            'sub_menu' => null,
            'main_link' => 'view-fleet',
        ],
        PermissionSlug::CREATE_FLEET => [
            'name' => 'add-fleet',
            'description' => 'Create new fleet',
            'main_menu' => 'owner-management',
            'sub_menu' => null,
            'main_link' => 'add-fleet'
        ],
        PermissionSlug::EDIT_FLEET => [
            'name' => 'edit-fleet',
            'description' => 'Edit fleet',
            'main_menu' => 'owner-management',
            'sub_menu' => null,
            'main_link' => 'edit-fleet'
        ],
        PermissionSlug::DELETE_FLEET => [
            'name' => 'delete-fleet',
            'description' => 'Delete fleet',
            'main_menu' => 'owner-management',
            'sub_menu' => null,
            'main_link' => 'delete-fleet'
        ],
        PermissionSlug::TOGGLE_FLEET => [
            'name' => 'toggle-fleet',
            'description' => 'Change status of fleet',
            'main_menu' => 'owner-management',
            'sub_menu' => null,
            'main_link' => 'toggle-fleet'
        ],
        PermissionSlug::FLEET_APPROVE => [
            'name' => 'fleet-approval',
            'description' => 'Change Approve status of fleet',
            'main_menu' => 'owner-management',
            'sub_menu' => null,
            'main_link' => 'fleet-approval'
        ],
        PermissionSlug::VIEW_FLEET_DOCUMENT => [
            'name' => 'view-fleet-document',
            'description' => 'View Fleet Document',
            'main_menu' => 'owner-management',
            'sub_menu' => null,
            'main_link' => 'view-fleet-document'
        ],

/* Fleet Drivers */
        PermissionSlug::FLEET_DRIVER_LOCATION_FILTER => [
            'name' => 'fleet-drivers-location-filter',
            'description' => 'Get all drivers',
            'main_menu'=>'owner-management',
            'sub_menu'=>null,
            'main_link'=>'view-approved-drivers',
        ],
        PermissionSlug::FLEET_DRIVER_OWNER_FILTER => [
            'name' => 'fleet-drivers-owner-filter',
            'description' => 'Get all drivers',
            'main_menu'=>'owner-management',
            'sub_menu'=>null,
            'main_link'=>'view-approved-drivers',
        ],
        PermissionSlug::VIEW_APPROVED_FLEET_DRIVERS => [
            'name' => 'view-approved-fleet-drivers',
            'description' => 'Get all approved drivers',
            'main_menu'=>'owner-management',
            'sub_menu'=>null,
            'main_link'=>'view-approved-fleet-drivers',
        ],
        PermissionSlug::VIEW_PENDING_FLEET_DRIVERS => [
            'name' => 'view-pending-fleet-drivers',
            'description' => 'Get all blocked drivers',
            'main_menu'=>'owner-management',
            'sub_menu'=>null,
            'main_link'=>'view-pending-fleet-drivers',
        ],
        PermissionSlug::ADD_FLEET_DRIVERS => [
            'name' => 'add-fleet-drivers',
            'description' => 'add-fleet-drivers',
            'main_menu'=>'owner-management',
            'sub_menu'=>null,
            'main_link'=>'add-fleet-drivers',
        ],
        PermissionSlug::EDIT_FLEET_DRIVERS => [
            'name' => 'edit-fleet-drivers',
            'description' => 'edit-fleet-drivers',
            'main_menu'=>'owner-management',
            'sub_menu'=>null,
            'main_link'=>'edit-fleet-drivers',
        ],
        PermissionSlug::APPROVE_FLEET_DRIVERS => [
            'name' => 'approve-fleet-drivers',
            'description' => 'toggle-fleet-drivers',
            'main_menu'=>'owner-management',
            'sub_menu'=>null,
            'main_link'=>'toggle-fleet-drivers',
        ],
        PermissionSlug::DELETE_FLEET_DRIVERS => [
            'name' => 'delete-fleet-drivers',
            'description' => 'delete-fleet-drivers',
            'main_menu'=>'owner-management',
            'sub_menu'=>null,
            'main_link'=>'delete-fleet-drivers',
        ],    
        PermissionSlug::VIEW_FLEET_DRIVER_PROFILE => [
            'name' => 'view-fleet-driver-profile',
            'description' => 'view-fleet-driver-profile',
            'main_menu'=>'owner-management',
            'sub_menu'=>null,
            'main_link'=>'view-fleet-driver-profile',
        ],

/*Fleet Driver needed Document */
        PermissionSlug::FLEET_DRIVER_DOCUMENT_VIEW => [
            'name' => 'fleet-driver-document-view',
            'description' => 'fleet-driver-document-view',
            'main_menu'=>'owner-management',
            'sub_menu'=>null,
            'main_link'=>'fleet-driver-document-view',
        ],
        PermissionSlug::ADD_FLEET_DRIVER_DOCUMENT => [
            'name' => 'add-fleet-driver-document',
            'description' => 'fleet-driver-document-view',
            'main_menu'=>'owner-management',
            'sub_menu'=>null,
            'main_link'=>'add-fleet-driver-document',
        ],
        PermissionSlug::EDIT_FLEET_DRIVER_DOCUMENT => [
            'name' => 'edit-fleet-driver-document',
            'description' => 'fleet-driver-document-edit',
            'main_menu'=>'owner-management',
            'sub_menu'=>null,
            'main_link'=>'edit-fleet-driver-document',
        ],
        PermissionSlug::DELETE_FLEET_DRIVER_DOCUMENT => [
            'name' => 'delete-fleet-driver-document',
            'description' => 'fleet-driver-document-upload',
            'main_menu'=>'owner-management',
            'sub_menu'=>null,
            'main_link'=>'delete-fleet-driver-document',
        ],
        PermissionSlug::TOGGLE_FLEET_DRIVER_DOCUMENT => [
            'name' => 'toggle-fleet-driver-document',
            'description' => 'fleet-driver-document-toggle',
            'main_menu'=>'owner-management',
            'sub_menu'=>null,
            'main_link'=>'toggle-fleet-driver-document',
        ], 

// trip request
        PermissionSlug::TRIP_REQUEST_VIEW => [
            'name' => 'trip-request-view',
            'description' => 'trip request view lists',
            'main_menu'=>'trip-request',
            'sub_menu'=>null,
            'main_link'=>'trip-request-view',
        ], 
// ongoing request
        PermissionSlug::ONGOING_REQUEST_VIEW => [
            'name' => 'ongoing-request-view',
            'description' => 'ongoing request view lists',
            'main_menu'=>'ongoing-request',
            'sub_menu'=>null,
            'main_link'=>'ongoing-request-view',
        ], 

// delivery trip request
        PermissionSlug::DELIVERY_REQUEST_VIEW => [
            'name' => 'delivery-request-view',
            'description' => 'trip request view lists',
            'main_menu'=>'delivery-request',
            'sub_menu'=>null,
            'main_link'=>'delivery-request-view',
        ], 
        PermissionSlug::MANAGE_DELIVERY_REQUEST => [
            'name' => 'manage-delivery-request',
            'description' => 'trip request view lists',
            'main_menu'=>'delivery-request',
            'sub_menu'=>null,
            'main_link'=>'manage-delivery-request',
        ], 
        PermissionSlug::SCHEDULE_DELIVERY_REQUEST_VIEW => [
            'name' => 'schedule-delivery-request-view',
            'description' => 'trip request view lists',
            'main_menu'=>'delivery-request',
            'sub_menu'=>null,
            'main_link'=>'schedule-delivery-request-view',
        ], 
        PermissionSlug::CANCELLED_DELIVERY_REQUEST_VIEW => [
            'name' => 'cancelled-delivery-request-view',
            'description' => 'trip request view lists',
            'main_menu'=>'delivery-request',
            'sub_menu'=>null,
            'main_link'=>'cancelled-delivery-request-view',
        ], 

// setting menu
//   business settings
        PermissionSlug::BUSINESS_SETTINGS => [
            'name' => 'manage-business-settings',
            'description' => 'business settings lists',
            'main_menu'=>'business-settings',
            'sub_menu'=>null,
            'main_link'=>'manage-business-settings',
        ], 
        PermissionSlug::GENERAL_SETTINGS_VIEW => [
            'name' => 'general-settings-view',
            'description' => 'general settings view lists',
            'main_menu'=>'business-settings',
            'sub_menu'=>null,
            'main_link'=>'general-settings-view',
        ], 
        PermissionSlug::TRANSPORT_RIDE_SETTINGS_VIEW => [
            'name' => 'transport-ride-settings-view',
            'description' => 'transport ride settings view lists',
            'main_menu'=>'business-settings',
            'sub_menu'=>null,
            'main_link'=>'transport-ride-settings-view',
        ],  
        PermissionSlug::BID_RIDE_SETTINGS_VIEW => [
            'name' => 'bid-ride-settings-view',
            'description' => 'Bid ride settings view lists',
            'main_menu'=>'business-settings',
            'sub_menu'=>null,
            'main_link'=>'bid-ride-settings-view',
        ], 
        PermissionSlug::CUSTOMIZATION_SETTINGS_VIEW => [
            'name' => 'customization-settings-view',
            'description' => 'customization settings view lists',
            'main_menu'=>'business-settings',
            'sub_menu'=>null,
            'main_link'=>'customization-settings-view',
        ], 

//      PeakZone
        PermissionSlug::PEAK_ZONE_SETTINGS_VIEW => [
            'name' => 'peak-zone-settings-view',
            'description' => 'Peak Zone settings view',
            'main_menu'=>'peak-zone-settings',
            'sub_menu'=>null,
            'main_link'=>'peak-zone-settings',
        ], 
        PermissionSlug::PEAK_ZONE_VIEW => [
            'name' => 'peak-zone-view',
            'description' => 'Peak Zone view',
            'main_menu'=>'peak-zone',
            'sub_menu'=>null,
            'main_link'=>'peak-zone',
        ], 
        PermissionSlug::PEAK_ZONE_MAP_VIEW => [
            'name' => 'peak-zone-map-view',
            'description' => 'Peak Zone Map view',
            'main_menu'=>'peak-zone-map',
            'sub_menu'=>null,
            'main_link'=>'peak-zone',
        ], 
        PermissionSlug::TOGGLE_PEAK_ZONE => [
            'name' => 'peak-zone-toggle',
            'description' => 'Toggle Peak Zone status',
            'main_menu'=>'peak-zone-toggle',
            'sub_menu'=>null,
            'main_link'=>'peak-zone',
        ], 

//   app settings
        PermissionSlug::APP_SETTINGS => [
            'name' => 'manage-app-settings',
            'description' => 'app settings lists',
            'main_menu'=>'app-settings',
            'sub_menu'=>null,
            'main_link'=>'manage-app-settings',
        ], 
        PermissionSlug::WALLET_SETTINGS_VIEW => [
            'name' => 'wallet-settings-view',
            'description' => 'wallet settings view lists',
            'main_menu'=>'app-settings',
            'sub_menu'=>null,
            'main_link'=>'wallet-settings-view',
        ], 
        PermissionSlug::TIP_SETTINGS_VIEW => [
            'name' => 'tip-settings-view',
            'description' => 'tips settings view lists',
            'main_menu'=>'app-settings',
            'sub_menu'=>null,
            'main_link'=>'tip-settings-view',
        ], 
        
        PermissionSlug::ONBOARDING_SCREEN_SETTINGS_VIEW => [
            'name' => 'onboarding-screen-settings-view',
            'description' => 'onboarding screen settings view lists',
            'main_menu'=>'app-settings',
            'sub_menu'=>null,
            'main_link'=>'onboarding-screen-settings-view',
        ],  
        PermissionSlug::TOGGLE_ONBOARDING => [
            'name' => 'toggle_onboarding',
            'description' => 'toggle onboarding',
            'main_menu'=>'app-settings',
            'sub_menu'=>null,
            'main_link'=>'onboarding',
        ],
        PermissionSlug::EDIT_ONBOARDING => [
            'name' => 'edit_onboarding',
            'description' => 'edit onboarding',
            'main_menu'=>'app-settings',
            'sub_menu'=>null,
            'main_link'=>'onboarding',
        ],

//   thirdparty settings
        PermissionSlug::THIRD_PARTY_SETTINGS => [
            'name' => 'manage-third-party-settings',
            'description' => 'third party settings lists',
            'main_menu'=>'third-party-settings',
            'sub_menu'=>null,
            'main_link'=>'manage-third-party-settings',
        ], 
        PermissionSlug::PAYMENT_GATEWAY_SETTINGS_VIEW => [
            'name' => 'payment-gateway-settings-view',
            'description' => 'payment gateway settings view lists',
            'main_menu'=>'third-party-settings',
            'sub_menu'=>null,
            'main_link'=>'payment-gateway-settings-view',
        ], 
        PermissionSlug::SMS_GATEWAY_SETTINGS_VIEW => [
            'name' => 'sms-gateway-settings-view',
            'description' => 'sms gateway settings view lists',
            'main_menu'=>'third-party-settings',
            'sub_menu'=>null,
            'main_link'=>'sms-gateway-settings-view',
        ],
        PermissionSlug::FIREBASE_SETTINGS_VIEW => [
            'name' => 'firebase-settings-view',
            'description' => 'firebase settings view lists',
            'main_menu'=>'third-party-settings',
            'sub_menu'=>null,
            'main_link'=>'firebase-settings-view',
        ],  
        PermissionSlug::MAP_SETTINGS_VIEW => [
            'name' => 'map-settings-view',
            'description' => 'map settings view lists',
            'main_menu'=>'third-party-settings',
            'sub_menu'=>null,
            'main_link'=>'map-settings-view',
        ],
        PermissionSlug::MAIL_CONFIGURATION_VIEW => [
            'name' => 'mail-configuration-view',
            'description' => 'mail configuration view lists',
            'main_menu'=>'third-party-settings',
            'sub_menu'=>null,
            'main_link'=>'mail-configuration-view',
        ],
        // PermissionSlug::MAP_APIS_VIEW => [
        //     'name' => 'map-apis-view',
        //     'description' => 'map apis view lists',
        //     'main_menu'=>'third-party-settings',
        //     'sub_menu'=>null,
        //     'main_link'=>'map-apis-view',
        // ],
        PermissionSlug::RECAPTCHA_VIEW => [
            'name' => 'recaptcha-view',
            'description' => 'recaptcha view lists',
            'main_menu'=>'third-party-settings',
            'sub_menu'=>null,
            'main_link'=>'recaptcha-view',
        ],
        PermissionSlug::INVOICE_CONFIGURATION_VIEW => [
            'name' => 'invoice-configuration-view',
            'description' => 'invoice configuration view lists',
            'main_menu'=>'third-party-settings',
            'sub_menu'=>null,
            'main_link'=>'invoice-configuration-view',
        ], 
        PermissionSlug::NOTIFICATION_CHANNEL_VIEW => [
            'name' => 'notification-channel-view',
            'description' => 'notification channel view lists',
            'main_menu'=>'third-party-settings',
            'sub_menu'=>null,
            'main_link'=>'notification-channel-view',
        ], 
            
 //CMS landing-website 
        PermissionSlug::CMS_LANDING_WEBSITE => [
            'name' => 'cms-landing-website',
            'description' => 'cms-landing-website',
            'main_menu'=>'cms-landing-website',
            'main_link'=>'null',
            'main_link'=>'cms-landing-website',
        ],
/* header-footer */
        PermissionSlug::HEADER_FOOTER => [
            'name' => 'header_footer',
            'description' => 'header footer',
            'main_menu'=>'cms-landing-website',
            'sub_menu'=> null,
            'main_link'=>'header-footer',
        ],
        PermissionSlug::ADD_HEADER_FOOTER => [
            'name' => 'add_header_footer',
            'description' => 'Add header footer',
            'main_menu'=>'cms-landing-website',
            'sub_menu'=> null,
            'main_link'=>'header-footer',
        ],       
        PermissionSlug::EDIT_HEADER_FOOTER=> [
            'name' => 'edit_header_footer',
            'description' => 'Edit header footer',
            'main_menu'=>'cms-landing-website',
            'sub_menu'=> null,
            'main_link'=>'header-footer',
        ],       
        PermissionSlug::DELETE_HEADER_FOOTER => [
            'name' => 'delete_header_footer',
            'description' => 'Delete header footer',
            'main_menu'=>'cms-landing-website',
            'sub_menu'=> null,
            'main_link'=>'header-footer',
        ],
/* landing-home */
        PermissionSlug::LANDING_HOME => [
            'name' => 'landing_home',
            'description' => 'landing home',
            'main_menu'=>'cms-landing-website',
            'sub_menu'=> null,
            'main_link'=>'landing-home',
        ],
        PermissionSlug::ADD_LANDING_HOME => [
            'name' => 'add_landing_home',
            'description' => 'Add landing home',
            'main_menu'=>'cms-landing-website',
            'sub_menu'=> null,
            'main_link'=>'landing-home',
        ],       
        PermissionSlug::EDIT_LANDING_HOME=> [
            'name' => 'edit_landing_home',
            'description' => 'Edit landing home',
            'main_menu'=>'cms-landing-website',
            'sub_menu'=> null,
            'main_link'=>'landing-home',
        ],       
        PermissionSlug::DELETE_LANDING_HOME => [
            'name' => 'delete_landing_home',
            'description' => 'Delete landing home',
            'main_menu'=>'cms-landing-website',
            'sub_menu'=> null,
            'main_link'=>'landing-home',
        ],
/* landing-driver */
        PermissionSlug::LANDING_DRIVER => [
            'name' => 'landing_driver',
            'description' => 'landing driver',
            'main_menu'=>'cms-landing-website',
            'sub_menu'=> null,
            'main_link'=>'landing-driver',
        ],
        PermissionSlug::ADD_LANDING_DRIVER => [
            'name' => 'add_landing_driver',
            'description' => 'Add landing driver',
            'main_menu'=>'cms-landing-website',
            'sub_menu'=> null,
            'main_link'=>'landing-driver',
        ],       
        PermissionSlug::EDIT_LANDING_DRIVER=> [
            'name' => 'edit_landing_driver',
            'description' => 'Edit landing driver',
            'main_menu'=>'cms-landing-website',
            'sub_menu'=> null,
            'main_link'=>'landing-driver',
        ],       
        PermissionSlug::DELETE_LANDING_DRIVER => [
            'name' => 'delete_landing_driver',
            'description' => 'Delete landing driver',
            'main_menu'=>'cms-landing-website',
            'sub_menu'=> null,
            'main_link'=>'landing-driver',
        ],

/* landing-aboutus */
        PermissionSlug::LANDING_ABOUTUS => [
            'name' => 'landing_aboutus',
            'description' => 'landing aboutus',
            'main_menu'=>'cms-landing-website',
            'sub_menu'=> null,
            'main_link'=>'landing-aboutus',
        ],
        PermissionSlug::ADD_LANDING_ABOUTUS => [
            'name' => 'add_landing_aboutus',
            'description' => 'Add landing aboutus',
            'main_menu'=>'cms-landing-website',
            'sub_menu'=> null,
            'main_link'=>'landing-aboutus',
        ],       
        PermissionSlug::EDIT_LANDING_ABOUTUS=> [
            'name' => 'edit_landing_aboutus',
            'description' => 'Edit landing aboutus',
            'main_menu'=>'cms-landing-website',
            'sub_menu'=> null,
            'main_link'=>'landing-aboutus',
        ],       
        PermissionSlug::DELETE_LANDING_ABOUTUS => [
            'name' => 'delete_landing_aboutus',
            'description' => 'Delete landing aboutus',
            'main_menu'=>'cms-landing-website',
            'sub_menu'=> null,
            'main_link'=>'landing-aboutus',
        ],

/* landing-user */
        PermissionSlug::LANDING_USER => [
            'name' => 'landing_user',
            'description' => 'landing user',
            'main_menu'=>'cms-landing-website',
            'sub_menu'=> null,
            'main_link'=>'landing-user',
        ],
        PermissionSlug::ADD_LANDING_USER => [
            'name' => 'add_landing_user',
            'description' => 'Add landing user',
            'main_menu'=>'cms-landing-website',
            'sub_menu'=> null,
            'main_link'=>'landing-user',
        ],       
        PermissionSlug::EDIT_LANDING_USER=> [
            'name' => 'edit_landing_user',
            'description' => 'Edit landing user',
            'main_menu'=>'cms-landing-website',
            'sub_menu'=> null,
            'main_link'=>'landing-user',
        ],       
        PermissionSlug::DELETE_LANDING_USER => [
            'name' => 'delete_landing_user',
            'description' => 'Delete landing user',
            'main_menu'=>'cms-landing-website',
            'sub_menu'=> null,
            'main_link'=>'landing-user',
        ],

/* landing-contact */
        PermissionSlug::LANDING_CONTACT => [
            'name' => 'landing_contact',
            'description' => 'landing contact',
            'main_menu'=>'cms-landing-website',
            'sub_menu'=> null,
            'main_link'=>'landing-contact',
        ],
        PermissionSlug::ADD_LANDING_CONTACT => [
            'name' => 'add_landing_contact',
            'description' => 'Add landing user',
            'main_menu'=>'cms-landing-website',
            'sub_menu'=> null,
            'main_link'=>'landing-user',
        ],       
        PermissionSlug::EDIT_LANDING_CONTACT=> [
            'name' => 'edit_landing_contact',
            'description' => 'Edit landing contact',
            'main_menu'=>'cms-landing-website',
            'sub_menu'=> null,
            'main_link'=>'landing-contact',
        ],       
        PermissionSlug::DELETE_LANDING_CONTACT => [
            'name' => 'delete_landing_contact',
            'description' => 'Delete landing contact',
            'main_menu'=>'cms-landing-website',
            'sub_menu'=> null,
            'main_link'=>'landing-contact',
        ],

/* landing-quicklinks */
        PermissionSlug::LANDING_QUICKLINKS => [
            'name' => 'landing_quicklinks',
            'description' => 'landing quicklinks',
            'main_menu'=>'cms-landing-website',
            'sub_menu'=> null,
            'main_link'=>'landing-quicklinks',
        ],
        PermissionSlug::ADD_LANDING_QUICKLINKS => [
            'name' => 'add_landing_quicklinks',
            'description' => 'Add landing quicklinks',
            'main_menu'=>'cms-landing-website',
            'sub_menu'=> null,
            'main_link'=>'landing-quicklinks',
        ],       
        PermissionSlug::EDIT_LANDING_QUICKLINKS => [
            'name' => 'edit_landing_quicklinks',
            'description' => 'Edit landing quicklinks',
            'main_menu'=>'cms-landing-website',
            'sub_menu'=> null,
            'main_link'=>'landing-quicklinks',
        ],       
        PermissionSlug::DELETE_LANDING_QUICKLINKS  => [
            'name' => 'delete_landing_quicklinks',
            'description' => 'Delete landing quicklinks',
            'main_menu'=>'cms-landing-website',
            'sub_menu'=> null,
            'main_link'=>'landing-quicklinks',
        ],

//masters 
        PermissionSlug::MASTERS => [
            'name' => 'masters',
            'description' => 'masters',
            'main_menu'=>'masters',
            'main_link'=>'null',
            'main_link'=>'masters',
        ],
/* languages */
        PermissionSlug::LANGUAGES => [
            'name' => 'languages',
            'description' => 'languages',
            'main_menu'=>'masters',
            'sub_menu'=> null,
            'main_link'=>'languages',
        ],
        PermissionSlug::ADD_LANGUAGES => [
            'name' => 'add_languages',
            'description' => 'Add languages',
            'main_menu'=>'masters',
            'sub_menu'=> null,
            'main_link'=>'languages',
        ],       
        PermissionSlug::DELETE_LANGUAGES=> [
            'name' => 'delete_languages',
            'description' => 'delete languages',
            'main_menu'=>'masters',
            'sub_menu'=> null,
            'main_link'=>'languages',
        ],       
        PermissionSlug::BROWSE_LANGUAGES => [
            'name' => 'browse_languages',
            'description' => 'Browse languages',
            'main_menu'=>'masters',
            'sub_menu'=> null,
            'main_link'=>'languages',
        ],       
/* roleS */
        PermissionSlug::ROLES => [
            'name' => 'roles',
            'description' => 'roles',
            'main_menu'=>'masters',
            'sub_menu'=> null,
            'main_link'=>'roles',
        ],
        PermissionSlug::CREATE_ROLES => [
            'name' => 'create_roles',
            'description' => 'create roles',
            'main_menu'=>'masters',
            'sub_menu'=> null,
            'main_link'=>'roles',
        ],       
        PermissionSlug::EDIT_ROLES=> [
            'name' => 'edit_roles',
            'description' => 'Edit roles',
            'main_menu'=>'masters',
            'sub_menu'=> null,
            'main_link'=>'roles',
        ],  
        PermissionSlug::PERMISSIONS_ROLES=> [
            'name' => 'permissions_roles',
            'description' => 'permissions roles',
            'main_menu'=>'masters',
            'sub_menu'=> null,
            'main_link'=>'roles',
        ], 
        PermissionSlug::DELETE_ROLES=> [
            'name' => 'delete_roles',
            'description' => 'delete roles',
            'main_menu'=>'masters',
            'sub_menu'=> null,
            'main_link'=>'roles',
        ],   


            //Dispathcer panel permissions

        PermissionSlug::DISPATHCER_DASHBOARD => [
            'name' => 'dispatcher-dashboard',
            'description' => 'dispatcher dashboard',
            'main_menu' => 'dispatcher dashboard',
            'sub_menu' => null,
            'main_link' => 'dispatcher dashboard',
        ],

        PermissionSlug::DISPATHCER_DRIVERS => [
            'name' => 'dispatcher-drivers',
            'description' => 'dispatcher drivers',
            'main_menu' => 'dispatcher drivers',
            'sub_menu' => null,
            'main_link' => 'dispatcher drivers',
        ],
        PermissionSlug::DISPATHCER_RIDE => [
            'name' => 'dispatcher-ride',
            'description' => 'dispatcher ride',
            'main_menu' => 'dispatcher ride',
            'sub_menu' => null,
            'main_link' => 'dispatcher ride',
        ],
        PermissionSlug::DISPATHCER_RIDE_REQUEST => [
            'name' => 'dispatcher-ride-request',
            'description' => 'dispatcher ride request',
            'main_menu' => 'dispatcher ride request',
            'sub_menu' => null,
            'main_link' => 'dispatcher ride request',
        ],
        PermissionSlug::DISPATHCER_RIDE_REQUEST_VIEW => [
            'name' => 'dispatcher-ride-request-view',
            'description' => 'dispatcher ride request view',
            'main_menu' => 'dispatcher ride request',
            'sub_menu' => null,
            'main_link' => 'dispatcher ride request view',
        ],
        PermissionSlug::DISPATHCER_RIDE_REQUEST_CANCEL => [
            'name' => 'dispatcher-ride-request-cancel',
            'description' => 'dispatcher ride request cancel',
            'main_menu' => 'dispatcher ride request',
            'sub_menu' => null,
            'main_link' => 'dispatcher ride request cancel',
        ],
        PermissionSlug::DISPATHCER_RIDE_REQUEST_ASSIGN => [
            'name' => 'dispatcher-ride-request-assign',
            'description' => 'dispatcher ride request assign',
            'main_menu' => 'dispatcher ride request',
            'sub_menu' => null,
            'main_link' => 'dispatcher ride request assign',
        ],
        PermissionSlug::DISPATHCER_ONGOING_REQUEST => [
            'name' => 'dispatcher-ongoing-request',
            'description' => 'dispatcher ongoing request',
            'main_menu' => 'dispatcher ongoing request',
            'sub_menu' => null,
            'main_link' => 'dispatcher ongoing request',
        ],
        PermissionSlug::DISPATHCER_ONGOING_REQUEST_VIEW => [
            'name' => 'dispatcher-ongoing-request-view',
            'description' => 'dispatcher ongoing request view',
            'main_menu' => 'dispatcher ongoing request',
            'sub_menu' => null,
            'main_link' => 'dispatcher ongoing request view',
        ],
        PermissionSlug::DISPATHCER_ONGOING_REQUEST_CANCEL => [
            'name' => 'dispatcher-ongoing-request-cancel',
            'description' => 'dispatcher ongoing request cancel',
            'main_menu' => 'dispatcher ongoing request',
            'sub_menu' => null,
            'main_link' => 'dispatcher ongoing request cancel',
        ],
        PermissionSlug::DISPATHCER_ONGOING_REQUEST_ASSIGN => [
            'name' => 'dispatcher-ongoing-request-assign',
            'description' => 'dispatcher ongoing request assign',
            'main_menu' => 'dispatcher ongoing request',
            'sub_menu' => null,
            'main_link' => 'dispatcher ongoing request assign',
        ],


        /**
         * Country Management
         * */
        PermissionSlug::MANAGE_COUNTRY => [
            'name' => 'manage-country',
            'description' => 'View Country',
            'main_menu'=>'settings',
            'sub_menu'=> 'country',
            'sub_link'=> 'country',
            'main_link'=>'settings',
        ],
        PermissionSlug::ADD_COUNTRY => [
            'name' => 'add-country',
            'description' => 'add Country',
            'main_menu'=>'settings',
            'sub_menu'=> 'country',
            'sub_link'=> 'country',
            'main_link'=>'settings',

        ],
        PermissionSlug::EDIT_COUNTRY => [
            'name' => 'edit-country',
            'description' => 'edit country',
            'main_menu'=>'settings',
            'sub_menu'=> 'country',
            'sub_link'=> 'country',
            'main_link'=>'settings',

        ],
        PermissionSlug::DELETE_COUNTRY => [
            'name' => 'delete-country',
            'description' => 'delete country',
            'main_menu'=>'settings',
            'sub_menu'=> 'country',
            'sub_link'=> 'country',
            'main_link'=>'settings',

        ],
        PermissionSlug::TOGGLE_COUNTRY => [
            'name' => 'toggle-country',
            'description' => 'toggle-country',
            'main_menu'=>'settings',
            'sub_menu'=> 'country',
            'sub_link'=> 'country',
            'main_link'=>'settings',
        ],


/* Airport */       
        PermissionSlug::VIEW_AIRPORT => [
            'name' => 'view-airport',
            'description' => 'View all airport',
            'main_menu'=>'price-management',
            'sub_menu'=>null,
            'main_link'=>'airport',
        ],
        PermissionSlug::MAP_VIEW_AIRPORT => [
            'name' => 'view-airport-map',
            'description' => 'map-view',
            'main_menu'=>'price-management',
            'sub_menu'=>null,
            'main_link'=>'airport',
        ],

        PermissionSlug::ADD_AIRPORT => [
            'name' => 'add-airport',
            'description' => 'Add airport',
            'main_menu'=>'price-management',
            'sub_menu'=>null,
            'main_link'=>'airport',
        ],
        PermissionSlug::EDIT_AIRPORT => [
            'name' => 'edit-airport',
            'description' => 'Edit airport',
            'main_menu'=>'price-management',
            'sub_menu'=>null,
            'main_link'=>'airport',
        ],
        PermissionSlug::DELETE_AIRPORT => [
            'name' => 'delete-airport',
            'description' => 'Get all airport',
            'main_menu'=>'price-management',
            'sub_menu'=>null,
            'main_link'=>'airport',
        ],
        PermissionSlug::TOGGLE_AIRPORT => [
            'name' => 'toggle-airport',
            'description' => 'Toggle Status Zones',
            'main_menu'=>'price-management',
            'sub_menu'=>null,
            'main_link'=>'airport',
        ],

    ];

    /**
     * List of all the roles to be created along with their permissions.
     *
     * @var array
     */
    protected $roles = [
        RoleSlug::SUPER_ADMIN => [
            'name' => 'Super Admin',
            'description' => 'Admin group with unrestricted access',
            'all' => true,
        ],
        RoleSlug::USER => [
            'name' => 'Normal User',
            'description' => 'Normal user with standard access',
            'permissions' => []
        ],
         RoleSlug::OWNER=>[
            'name' => 'Normal Owner',
            'description' => 'Normal Owner with standard access',
            'permissions' => [
                            PermissionSlug::ACCESS_DASHBOARD.
                            PermissionSlug::ACCESS_OWNER_DASHBOARD,
                            PermissionSlug::ACCESS_HOME,
                          //  PermissionSlug::DISPATCHER,
                          //  PermissionSlug::OWNER_REPORT,
                            PermissionSlug::REPORT_MANAGEMENT,
                            PermissionSlug::MANAGE_FLEET ,
                            PermissionSlug::VIEW_FLEET ,
                            PermissionSlug::CREATE_FLEET ,
                            PermissionSlug::EDIT_FLEET ,
                            PermissionSlug::DELETE_FLEET ,
                            PermissionSlug::TOGGLE_FLEET ,
                            PermissionSlug::FLEET_APPROVE ,
                            PermissionSlug::VIEW_FLEET_DOCUMENT ,
                            PermissionSlug::VIEW_APPROVED_FLEET_DRIVERS,
                            PermissionSlug::ADD_FLEET_DRIVERS,
                            PermissionSlug::EDIT_FLEET_DRIVERS ,
                            PermissionSlug::APPROVE_FLEET_DRIVERS,
                            PermissionSlug::DELETE_FLEET_DRIVERS,
                            PermissionSlug::VIEW_FLEET_DRIVER_PROFILE,
                            PermissionSlug::ADD_FLEET_DRIVER_DOCUMENT,
                            PermissionSlug::EDIT_FLEET_DRIVER_DOCUMENT,
                            PermissionSlug::DELETE_FLEET_DRIVER_DOCUMENT,
                            PermissionSlug::TOGGLE_FLEET_DRIVER_DOCUMENT,
                            PermissionSlug:: ACCESS_USER_NAV_LIST,
                            PermissionSlug::OWNER_MANAGEMENT,
                            PermissionSlug::TRIP_REQUEST_VIEW,
                            PermissionSlug::FLEET_REPORT
                ],
        ],
        RoleSlug::DRIVER=>[
            'name' => 'Driver',
            'description' => 'Driver user with standard access',
            'permissions' => [],
        ],
        RoleSlug::DISPATCHER=>[
            'name' => 'Dispatcher',
            'description' => 'Taxi Dispatcher user with standard access',
            'permissions' => [
                PermissionSlug::DISPATHCER_DASHBOARD,
                PermissionSlug::DISPATHCER_DRIVERS,
                PermissionSlug::DISPATHCER_RIDE,
                PermissionSlug::DISPATHCER_RIDE_REQUEST ,
                PermissionSlug::DISPATHCER_RIDE_REQUEST_VIEW ,
                PermissionSlug::DISPATHCER_RIDE_REQUEST_CANCEL ,
                PermissionSlug::DISPATHCER_RIDE_REQUEST_ASSIGN ,
                PermissionSlug::DISPATHCER_ONGOING_REQUEST ,
                PermissionSlug::DISPATHCER_ONGOING_REQUEST_VIEW ,
                PermissionSlug::DISPATHCER_ONGOING_REQUEST_CANCEL ,
                PermissionSlug::DISPATHCER_ONGOING_REQUEST_ASSIGN ,
            ],
        ],
        RoleSlug::EMPLOYEE=>[
            'name' => 'employee',
            'description' => 'Taxi EMployee user with standard access',
            'permissions' => [
                permissionSlug::ACCESS_USER_NAV_LIST,
                PermissionSlug::VIEW_SUPPORT_MANAGEMENT,
                PermissionSlug::VIEW_SUPPORT_TICKET,
            ],
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function () {
            foreach ($this->permissions as $permissionSlug => $attributes) {
                // Create permission if it doesn't exist
                Permission::firstOrCreate(['slug' => $permissionSlug], $attributes);
            }

            foreach ($this->roles as $roleSlug => $role) {
                // Create role if it doesn't exist
                $createdRole = Role::firstOrCreate(
                    ['slug' => $roleSlug],
                    array_merge(array_except($role, ['permissions']), ['locked' => true])
                );

                // Sync permissions
                if (isset($role['permissions'])) {
                    $rolePermissions = $role['permissions'];
                    $rolePermissionIds = Permission::whereIn('slug', $rolePermissions)->pluck('id');
                    $createdRole->permissions()->sync($rolePermissionIds);
                }
            }

            /**
             * Delete all unused permissions
             */
            $existingPermissions = Permission::pluck('slug')->toArray();

            $newPermissions = array_keys($this->permissions);

            $permissionsToDelete = array_diff($existingPermissions, $newPermissions);

            Permission::whereIn('slug', $permissionsToDelete)->delete();
        });
    }
}
