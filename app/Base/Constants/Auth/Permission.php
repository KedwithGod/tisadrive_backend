<?php

namespace App\Base\Constants\Auth;

class Permission
{
//nav lis
    const ACCESS_HOME = 'access-home';
    const ACCESS_USER_NAV_LIST = 'access-user-nav-list';
    const ACCESS_SETTINGS_NAV_LIST = 'access-settings-nav-list';
    const ACCESS_MASTER_NAV_LIST = 'access-master-nav-list';



/* Dashboard and dispatcher*/
    const ACCESS_DASHBOARD = 'access-dashboard';
    const ACCESS_OWNER_DASHBOARD = 'access-owner-dashboard';
    const DISPATCHER = 'dispatcher';
    const CHAT = 'chat';
    const OTHERS = 'others';
    const ACCESS_NOTIFICATIONS = 'access-notifications';


    const PROMOTIONS_MANAGEMENT = 'promotion-management';
//* Promo code */
    const MANAGE_PROMO = 'manage-promo';
    const ADD_PROMO = 'add-promo';
    const EDIT_PROMO = 'edit-promo';
    const TOGGLE_PROMO = 'toggle-promo';
    const DELETE_PROMO = 'delete-promo';

//* Subscription */
    const MANAGE_SUBSCRIPTION = 'manage-subscription';
    const ADD_SUBSCRIPTION = 'add-subscription';
    const EDIT_SUBSCRIPTION = 'edit-subscription';
    const TOGGLE_SUBSCRIPTION = 'toggle-subscription';
    const DELETE_SUBSCRIPTION = 'delete-subscription';

// price management
    const PRICE_MANAGEMENT = 'price-management';
/* Service-Location */
    const SERVICE_LOCATION = 'service-location';
    const ADD_SERVICE_LOCATION = 'add_service_Location';
    const EDIT_SERVICE_LOCATION = 'edit_service_Location';
    const DELETE_SERVICE_LOCATION = 'delete_service_Location';
    const TOGGLE_SERVICE_LOCATION = 'toggle_service_Location'; 
/* Zone */
    const VIEW_ZONE = 'view-zone';
    const VIEW_ZONE_MAP = 'view-zone-map';
    const ADD_ZONE = 'add-zone';
    const EDIT_ZONE = 'edit-zone';
    const DELETE_ZONE = 'delete-zone';
    const TOGGLE_ZONE = 'toggle-zone';
    const ZONE_SURGE = 'zone-surge';
    const UPDATE_ZONE_SURGE = 'update-zone-surge';

//Vehicle Types 
    const VEHICLE_TYPES = 'vehicle-types';
    const ADD_TYPES = 'add-vehicle-types';
    const EDIT_TYPES = 'edit-vehicle-types';
    const DELETE_TYPES = 'delete-vehicle-types';
    const TOGGLE_TYPES = 'toggle-vehicle-types';
// rental package
    const RENTAL_PACKAGE = 'rental-package';
    const ADD_RENTAL_PACKAGE = 'add-rental-package';
    const EDIT_RENTAL_PACKAGE = 'edit-rental-package';
    const DELETE_RENTAL_PACKAGE = 'delete-rental-package';
    const TOGGLE_RENTAL_PACKAGE = 'toggle-rental-package';
/* Vehicle Fare */
    const SET_PRICE = 'vehicle-fare';
    const ADD_PRICE = 'add-price';
    const EDIT_PRICE= 'edit-price';
    const DELETE_PRICE = 'delete-price';
    const TOGGLE_PRICE = 'toggle-price';
    const ADD_PACKAGE_PRICE = 'add-package-price';
/* incentives */
    const INCENTIVE = 'incentives';
    const UPDATE_INCENTIVE = 'update-incentives';
// Manage Goods Type
    const GOODS_TYPES = 'manage-goods-types';
    const ADD_GOODS_TYPES = 'add-goods-types';
    const EDIT_GOODS_TYPES = 'edit-goods-types';
    const DELETE_GOODS_TYPES = 'delete-goods-types';
    const TOGGLE_GOODS_TYPES = 'toggle-goods-types'; 

/*geo-fencing*/
    const GEO_FENCING = 'geo-fencing';
    const HEAT_MAP = 'heat-map';
    const GODS_EYE = 'gods-eye';

// complaint management
    const COMPLAINTS_MANAGE = 'complaints-management';
    const USER_COMPLAINT = 'user-complaint';
    const DRIVER_COMPLAINT = 'driver-complaint';
    const OWNER_COMPLAINT = 'owner-complaint';
    /*complaint titile */
    const COMPLAINT_TITLE = 'complaint-title';
    const ADD_COMPLAINT_TITLE = 'add-complaint-title';
    const EDIT_COMPLAINT_TITLE = 'edit-complaint-title';
    const TOGGLE_COMPLAINT_TITLE = 'toggle-complaint-title';
    const DELETE_COMPLAINT_TITLE = 'delete-complaint-title';

/* SOS */
    const VIEW_SOS = 'view-sos';
    const DELETE_SOS = 'delete-sos';
    const EDIT_SOS = 'edit-sos';
    const ADD_SOS = 'add-sos';
    const TOGGLE_SOS = 'toggle-sos';

//Notifications
    const VIEW_NOTIFICATIONS = 'view-notifications';
    const ADD_NOTIFICATIONS = 'add-notifications';
    const EDIT_NOTIFICATIONS ='edit-notifications';
    const DELETE_NOTIFICATIONS = 'delete-notifications';

//cancelation
    const VIEW_CANCELLATION_REASON = 'view-cancellation';
    const ADD_CANCELLATION = 'add-cancellation';
    const EDIT_CANCELLATION = 'edit-cancellation';
    const DELETE_CANCELLATION = 'delete-cancellation';
    const TOGGLE_CANCELLATION = 'toggle-cancellation';

//FAQ
     const VIEW_FAQ = 'view-faq';
     const DELETE_FAQ = 'delete-faq';
     const EDIT_FAQ = 'edit-faq';
     const ADD_FAQ = 'add-faq';
     const TOGGLE_FAQ = 'toggle-faq';


//mail template
const VIEW_MAIL_TEMPLATE = 'view-mail-template';
const DELETE_MAIL_TEMPLATE = 'delete-mail-template';
const EDIT_MAIL_TEMPLATE = 'edit-mail-template';
const ADD_MAIL_TEMPLATE = 'add-mail-template';
const TOGGLE_MAIL_TEMPLATE = 'toggle-mail-template';
// users menu 
/* Users */
    const USER_MANAGEMENT = 'user-management';
    const VIEW_USERS = 'view-users';
    const DELETE_USER = 'delete-user';
    const EDIT_USER = 'edit-user';
    const ADD_USER = 'add-user';
    const TOGGLE_USER = 'toggle-user';
    const VIEW_USER_PROFILE = 'view-user-profile';
    const VIEW_DELETE_USER = 'view-delete-user';

/* Drivers management*/
    const DRIVERS_MANAGEMENT = 'drivers-management';
    const VIEW_APPROVAL_PENDING_DRIVERS = 'view-approval-pending-drivers';
    const VIEW_APPROVED_DRIVERS = 'view-approved-drivers';
    const ADD_DRIVER = 'add-driver';
    const EDIT_DRIVER ='edit-driver';
    const DELETE_DRIVER = 'delete-driver';
    const VIEW_DRIVER_PROFILE = 'view-driver-profile';
    const DRIVER_APPROVAL = 'driver-approval';
    CONST DRIVER_DISAPPROVAL = 'driver-disapproval';
    const DRIVER_UPLOAD_DOCUMENTS = 'driver-upload-documents';
    const VIEW_DRIVERS_LEVELUP = 'view-drivers-levelup';
    const CHANGE_REWARD_VALUE = 'change-reward-value';
    const ADD_DRIVER_LEVELUP = 'add-drivers-levelup';
    const EDIT_DRIVER_LEVELUP = 'edit-drivers-levelup';
    const DELETE_DRIVER_LEVELUP = 'delete-drivers-levelup';
    const DRIVER_RATING_LIST = 'driver-rating-list';
    const DRIVER_RATING_VIEW = 'driver-rating-view';
    const DRIVER_WALLET = 'driver-wallet';
    const NEGETIVE_BALANCE_DRIVERS = 'negetive-balance-drivers';
    const WITHDRAWAL_REQUEST_DRIVERS = 'withdrawal-request-drivers';
    const DELETE_REQ_DRIVERS = 'delete-request-drivers';
// Manage Driver Needed Doc
    const MANAGE_DRIVER_NEEDED_DOC = 'manage-driver-needed-document';
    const ADD_DRIVER_NEEEDED_DOC = 'add-driver-needed-document';
    const EDIT_DRIVER_NEEEDED_DOC = 'edit-driver-needed-document';
    const DELETE_DRIVER_NEEEDED_DOC = 'delete-driver-needed-document';
    const TOGGLE_DRIVER_NEEEDED_DOC = 'toggle-driver-needed-document';   

//mange drivers bank info
    const MANAGE_DRIVER_BANK_INFO = 'manage-driver-bank-info';
    const TOGGLE_BANK_INFO = 'toggle-bank-info';

    //loyalty-rewards
    const LOYALTY_REWARDS = 'loyalty-rewards';

/* Admin  */
    const ADMIN_MANAGEMENT = 'admin-management';
    const ADMIN = 'admin';
    const CREATE_ADMIN = 'add-admin';
    const EDIT_ADMIN = 'edit-admin';
    const DELETE_ADMIN = 'delete-admin';
    const TOGGLE_ADMIN = 'toggle-admin';   
    
/*Reports*/
    const REPORT_MANAGEMENT = 'report-management';
    const USER_REPORT = 'user-report';
    const DRIVER_REPORT = 'driver-report';
    const DRIVER_DUTY_REPORT = 'driver-duty-report';
    const FINANCE_REPORT = 'finance-report';
    const OWNER_REPORT ='owner-report';
    const FLEET_REPORT = 'fleet-report';

/* SUPPORT-Tickets-Management */
    const VIEW_SUPPORT_MANAGEMENT = 'view-support-management';


    /* SUPPORT-TICKET TITLE */
    const VIEW_TICKET_TITLE = 'view-ticket-title';
    const ADD_TICKET_TITLE = 'add-ticket-title';
    const EDIT_TICKET_TITLE = 'edit-ticket-title';
    const DELETE_TICKET_TITLE = 'delete-ticket-title';
    const TOGGLE_TICKET_TITLE = 'toggle-ticket-title';

      /* SUPPORT-TICKET  */
      const VIEW_SUPPORT_TICKET = 'view-support-ticket';

/* Manage Owner */ 
    const OWNER_MANAGEMENT = 'owner-management';
    const MANAGE_OWNER = 'manage-owner';
    const ADD_OWNER = 'add-owner';
    const EDIT_OWNER = 'edit-owner';
    const DELETE_OWNER = 'delete-owner';
    const TOGGLE_OWNER_STATUS = 'toggle-owner';
    const VIEW_OWNER_DOCUMENT = 'view-owner-document';
    const VIEW_DELETE_OWNER = 'view-delete-owner';
    const VIEW_OWNER_PROFILE = 'view-owner-profile';

// Manage Owner Needed Doc
    const MANAGE_OWNER_NEEDED_DOC = 'manage-owner-needed-document';
    const ADD_OWNER_NEEEDED_DOC = 'add-owner-needed-document';
    const EDIT_OWNER_NEEEDED_DOC = 'edit-owner-needed-document';
    const DELETE_OWNER_NEEEDED_DOC = 'delete-owner-needed-document';
    const TOGGLE_OWNER_NEEEDED_DOC = 'toggle-owner-needed-document';

/* Manage Fleets */ 
    const MANAGE_FLEET = 'manage-fleet';
    const VIEW_FLEET = 'view-fleet';
    const CREATE_FLEET = 'add-fleet';
    const EDIT_FLEET = 'edit-fleet';
    const DELETE_FLEET = 'delete-fleet';
    const TOGGLE_FLEET = 'toggle-fleet';
    const FLEET_APPROVE = 'fleet-approval'; 
    const VIEW_FLEET_DOCUMENT = 'view-fleet-document'; 

/* Fleet Drivers */
   const FLEET_DRIVER_LOCATION_FILTER = 'fleet-drivers-location-filter';
   const FLEET_DRIVER_OWNER_FILTER =  'fleet-drivers-owner-filter';
    const VIEW_APPROVED_FLEET_DRIVERS = 'view-approved-fleet-drivers';
    const VIEW_PENDING_FLEET_DRIVERS = 'view-pending-fleet-drivers';
    const ADD_FLEET_DRIVERS = 'add-fleet-drivers';
    const EDIT_FLEET_DRIVERS ='edit-fleet-drivers';
    const APPROVE_FLEET_DRIVERS = 'approve-fleet-drivers';
    const DELETE_FLEET_DRIVERS = 'delete-fleet-drivers';
    const VIEW_FLEET_DRIVER_PROFILE = 'view-fleet-driver-profile';

/* Fleet Drivers Document*/
    const FLEET_DRIVER_DOCUMENT_VIEW = 'fleet-driver-document-view';
    const ADD_FLEET_DRIVER_DOCUMENT = 'add-fleet-driver-document';
    const EDIT_FLEET_DRIVER_DOCUMENT = 'edit-fleet-driver-document';
    const DELETE_FLEET_DRIVER_DOCUMENT = 'delete-fleet-driver-document';
    const TOGGLE_FLEET_DRIVER_DOCUMENT = 'toggle-fleet-driver-document';
    

/* trip request view*/
    const TRIP_REQUEST_VIEW = 'trip-request-view';

/* ongoing request view */
    const ONGOING_REQUEST_VIEW = 'ongoing-request-view';

/* delivery req view*/
    const MANAGE_DELIVERY_REQUEST = 'manage-delivery-request';
    const DELIVERY_REQUEST_VIEW = 'delivery-request-view';
    const SCHEDULE_DELIVERY_REQUEST_VIEW = 'schedule-delivery-request-view';
    const CANCELLED_DELIVERY_REQUEST_VIEW = 'cancelled-delivery-request-view';

 /* business settings*/
    const BUSINESS_SETTINGS = 'manage-business-settings';
    const GENERAL_SETTINGS_VIEW = 'general-settings-view';
    const TRANSPORT_RIDE_SETTINGS_VIEW = 'transport-ride-settings-view';
    const BID_RIDE_SETTINGS_VIEW = 'bid-ride-settings-view';
    const CUSTOMIZATION_SETTINGS_VIEW = 'customization-settings-view';
    const PEAK_ZONE_SETTINGS_VIEW = 'peak-zone-settings-view';
    const PEAK_ZONE_VIEW = 'peak-zone-view';
    const PEAK_ZONE_MAP_VIEW = 'peak-zone-map-view';
    const TOGGLE_PEAK_ZONE = 'peak-zone-toggle';

 /* app settings*/
 const APP_SETTINGS = 'manage-app-settings';
 const WALLET_SETTINGS_VIEW = 'wallet-settings-view';
 const TIP_SETTINGS_VIEW = 'tip-settings-view';
 const REFERRAL_SETTINGS_VIEW = 'referral-settings-view';   
 const ONBOARDING_SCREEN_SETTINGS_VIEW = 'onboarding-screen-settings-view';  
 const TOGGLE_ONBOARDING = 'toggle_onboarding';
 const EDIT_ONBOARDING = 'edit_onboarding';
 
    //Country
    const MANAGE_COUNTRY = 'manage-country';
    const DELETE_COUNTRY = 'delete-country';
    const EDIT_COUNTRY = 'edit-country';
    const ADD_COUNTRY = 'add-country';
    const TOGGLE_COUNTRY = 'toggle-country';
 
/* third-party settings*/
const THIRD_PARTY_SETTINGS = 'manage-third-party-settings';
const PAYMENT_GATEWAY_SETTINGS_VIEW = 'payment-gateway-settings-view';
const SMS_GATEWAY_SETTINGS_VIEW = 'sms-gateway-settings-view';   
const FIREBASE_SETTINGS_VIEW = 'firebase-settings-view'; 
const MAP_SETTINGS_VIEW = 'map-settings-view';
const MAIL_CONFIGURATION_VIEW = 'mail-configuration-view';
// const MAP_APIS_VIEW = 'map-apis-view';   
const RECAPTCHA_VIEW = 'recaptcha-view'; 
const INVOICE_CONFIGURATION_VIEW = 'invoice-configuration-view'; 
const NOTIFICATION_CHANNEL_VIEW = 'notification-channel-view';
// cms landing website
const CMS_LANDING_WEBSITE = 'cms-landing-website';

/* header-footer */
    const HEADER_FOOTER = 'header_footer';
    const ADD_HEADER_FOOTER = 'add_header_footer';
    const EDIT_HEADER_FOOTER = 'edit_header_footer';
    const DELETE_HEADER_FOOTER = 'delete_header_footer';

/* landing home */
const LANDING_HOME = 'landing_home';
const ADD_LANDING_HOME = 'add_landing_home';
const EDIT_LANDING_HOME = 'edit_landing_home';
const DELETE_LANDING_HOME = 'delete_landing_home';  

/* landing aboutus */
const LANDING_ABOUTUS = 'landing_aboutus';
const ADD_LANDING_ABOUTUS = 'add_landing_aboutus';
const EDIT_LANDING_ABOUTUS = 'edit_landing_aboutus';
const DELETE_LANDING_ABOUTUS = 'delete_landing_aboutus';  

/* landing driver */
const LANDING_DRIVER = 'landing_driver';
const ADD_LANDING_DRIVER = 'add_landing_driver';
const EDIT_LANDING_DRIVER = 'edit_landing_driver';
const DELETE_LANDING_DRIVER = 'delete_landing_driver';   

/* landing user */
const LANDING_USER = 'landing_user';
const ADD_LANDING_USER = 'add_landing_user';
const EDIT_LANDING_USER = 'edit_landing_user';
const DELETE_LANDING_USER = 'delete_landing_user';  

/* landing contact */
const LANDING_CONTACT = 'landing_contact';
const ADD_LANDING_CONTACT = 'add_landing_contact';
const EDIT_LANDING_CONTACT = 'edit_landing_contact';
const DELETE_LANDING_CONTACT = 'delete_landing_contact';  


/* landing quicklinks */
const LANDING_QUICKLINKS = 'landing_quicklinks';
const ADD_LANDING_QUICKLINKS = 'add_landing_quicklinks';
const EDIT_LANDING_QUICKLINKS = 'edit_landing_quicklinks';
const DELETE_LANDING_QUICKLINKS = 'delete_landing_quicklinks'; 


// masters
const MASTERS = 'masters';


/* languages */
const LANGUAGES = 'languages';
const ADD_LANGUAGES = 'add_languages';
const DELETE_LANGUAGES = 'delete_languages';
const BROWSE_LANGUAGES = 'browse_languages'; 

/* banner image */
const BANNER_IMAGE = 'banner_image';
const ADD_BANNER_IMAGE = 'add_banner_image';
const EDIT_BANNER_IMAGE = 'edit_banner_image';
const DELETE_BANNER_IMAGE = 'delete_banner_image';
const TOGGLE_BANNER_IMAGE = 'toggle_banner_image';

/* roles */
const ROLES = 'roles';
const CREATE_ROLES = 'create_roles';
const EDIT_ROLES = 'edit_roles';
const PERMISSIONS_ROLES = 'permissions_roles';
const DELETE_ROLES = 'delete_roles';


const DISPATHCER_DASHBOARD = 'dispatcher-dashboard';
const DISPATHCER_DRIVERS = 'dispatcher-drivers';
const DISPATHCER_RIDE = 'dispatcher-ride';
const DISPATHCER_RIDE_REQUEST = 'dispatcher-ride-request';
const DISPATHCER_ONGOING_REQUEST = 'dispatcher-ongoing-request';

const DISPATHCER_RIDE_REQUEST_VIEW = 'dispatcher-ride-request-view';
const DISPATHCER_ONGOING_REQUEST_VIEW = 'dispatcher-ongoing-request-view';

const DISPATHCER_RIDE_REQUEST_ASSIGN = 'dispatcher-ride-request-assign';
const DISPATHCER_ONGOING_REQUEST_ASSIGN = 'dispatcher-ongoing-request-assign';

const DISPATHCER_RIDE_REQUEST_CANCEL = 'dispatcher-ride-request-cancel';
const DISPATHCER_ONGOING_REQUEST_CANCEL = 'dispatcher-ongoing-request-cancel';

    // Ariport Slugs
    const VIEW_AIRPORT = 'view-airport';
    const ADD_AIRPORT = 'Add-Airport';
    const EDIT_AIRPORT = 'Edit-airport';
    const DELETE_AIRPORT = 'Delete-Airport';
    const MAP_VIEW_AIRPORT = 'Map-view-Airport';
    const TOGGLE_AIRPORT = 'Toggle-Airport';
}
