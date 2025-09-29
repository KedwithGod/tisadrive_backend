<script>
import { Link, Head } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import simplebar from "simplebar-vue";
import { layoutComputed } from "@/state/helpers";
import { mapGetters } from 'vuex';


export default {
    components: {
        Layout,
        PageHeader,
        Link, Head,
        simplebar
    },
    computed: {
    ...layoutComputed,
    ...mapGetters(['permissions']),
  },

    data() {
        return {
            openSection: null, // Tracks which section is open
        };
    },
    methods: {
        // Toggle a specific section open or close
        toggleSection(section) {
            this.openSection = this.openSection === section ? null : section;
        },

        // Close all sections if the click is outside of any section
        handleClickOutside(event) {
            const sections = ['promoManagementSection','priceManagementSection', 'geoFencingSection', 'complaintSection', 'otherSection', 'customerSection',
                'driverSection','reportSection','ownerSection','businessSection','appSection','thirdSection','loyaltySection'
            ];

            // Check if click is outside all sections
            const isClickOutside = sections.every(ref => {
                const section = this.$refs[ref];
                return section && !section.contains(event.target);
            });

            if (isClickOutside) {
                this.openSection = null;
            }
        }
    },
    
    mounted() {
        // Listen for click events to close sections if clicked outside
        window.addEventListener('click', this.handleClickOutside);
    },

    beforeUnmount() {
        // Cleanup the event listener
        window.removeEventListener('click', this.handleClickOutside);
    }
};
</script>

<template>
    <Layout>
        <Head title="Sitemap" />
        <PageHeader :title="$t('menu')" :pageTitle="$t('overall')" />
        <BRow>
            <BCol lg="12">
                <BCard no-body>
                    <BCardHeader>
                        <BCardTitle class="mb-0">{{$t("overall_menu")}}</BCardTitle>
                    </BCardHeader>
                    <BCardBody>
                        <div class="sitemap-content">
                            <figure class="sitemap-horizontal">
                                <ul class="administration">
                                    <li>
                                        <ul class="director">
                                            <li>
                                                <BLink href="javascript:void(0);" class="fw-semibold "><span>
                                                        {{$t("admin_main_menu")}}</span></BLink>
                                                <ul class="subdirector">
                                                    <!-- <li>
                                                        <BLink href="javascript:void(0);" class="fw-semibold"><span>Contact
                                                                Us</span></BLink>
                                                    </li> -->
                                                </ul>
                                                <ul class="departments">
                                                    <li>
                                                        <!-- <BLink href="javascript:void(0);" class="fw-semibold"><span>Main
                                                                Pages</span></BLink> -->
                                                    </li>

                                                    <li class="department">
                                                        <BLink href="javascript:void(0);" class="fw-semibold "><span>{{$t("home_menu")}}</span></BLink>
                                                        <ul>
                                                            <li v-if="permissions.includes('access-dashboard')">
                                                                <Link href="/dashboard" class="menu-item "><span>{{$t("dashboard")}}</span>
                                                                </Link>
                                                            </li>
                                                            
                                                            <li v-if="permissions.includes('chat')">
                                                                <Link href="/chat" class="menu-item "><span>{{$t("chat")}}</span>
                                                                </Link>
                                                            </li>
                                                            <li ref="promoManagementSection">
                                                                <div class="first-list">
                                                                    <div class="list-wrap " v-if="permissions.includes('promotion-management')">
                                                                        <BLink href="javascript: void(0);" class="fw-medium  menu-item" > {{$t("promotion-management")}}</BLink>                                                                        
                                                                    </div>
                                                                    <ul class="second-list">
                                                                        <li v-if="permissions.includes('manage-promo')">
                                                                            <Link href="/promo-code" class="list-unstyled ">{{$t("promo_code")}}</Link>
                                                                        </li>
                                                                        <li style="height: 50px;" v-if="permissions.includes('view-notifications')">
                                                                            <Link href="/push-notifications"  class="list-unstyled mt-3 ">{{$t("send-notification")}}</Link>
                                                                        </li>
                                                                        <li style="height: 39px;" v-if="permissions.includes('banner_image')">
                                                                            <Link href="/banner-image"  class="list-unstyled  mt-3 ">{{$t("banner-image")}}</Link>
                                                                        </li>
                                                                    </ul>                                                                    
                                                                </div>
                                                            </li>

                                                             <!-- Price Management Collapsible Section -->
                                                             <li ref="priceManagementSection" style="margin-top: 148px;">
                                                                <div class="first-list">
                                                                    <div class="list-wrap " v-if="permissions.includes('price-management')">
                                                                        <BLink href="javascript: void(0);" class="fw-medium  menu-item" > {{$t("price-management")}}</BLink>                                                                        
                                                                    </div>
                                                                    <ul class="second-list">
                                                                        <li v-if="permissions.includes('service-location')">
                                                                            <Link href="/service-locations" class="list-unstyled ">{{$t("service_location")}}</Link>
                                                                        </li>
                                                                        <li style="height: 50px;" v-if="permissions.includes('view-zone')">
                                                                            <Link href="/zones"  class="list-unstyled mt-3 ">{{$t("zone")}}</Link>
                                                                        </li>
                                                                        <li  style="height: 39px;" v-if="permissions.includes('vehicle-types')">
                                                                            <Link href="/vehicle_type"  class="list-unstyled  mt-2 ">{{$t("vehicle_type")}}</Link>
                                                                        </li>
                                                                        <li  style="height: 42px;" v-if="permissions.includes('rental-package')">
                                                                            <Link href="/rental-package-types"  class="list-unstyled  mt-3">{{$t("rental-package")}}</Link>
                                                                        </li>
                                                                        <li  style="height: 55px;" v-if="permissions.includes('vehicle-fare')">
                                                                            <Link href="/set-prices"  class="list-unstyled  mt-4 ">{{$t("set-price")}}</Link>
                                                                        </li>
                                                                        <li  style="height: 48px;" v-if="permissions.includes('manage-goods-types')">
                                                                            <Link href="/goods-type"  class="list-unstyled  mt-3 ">{{$t("goods-type")}}</Link>
                                                                        </li>
                                                                    </ul>                                                                    
                                                                </div>
                                                            </li>
                                                            <!-- End Price Management -->

                                                            <!-- Geo Fencing Collapsible Section -->
                                                            <li ref="geoFencingSection" style="margin-top: 326px;">
                                                                <div class="first-list" >
                                                                    <div class="list-wrap" v-if="permissions.includes('geo-fencing')">
                                                                        <BLink href="javascript:void(0);" class="menu-item ">
                                                                            <span>{{$t("geofencing")}}</span>
                                                                        </BLink>
                                                                   </div>     
                                                                   <ul class="second-list">
                                                                        <li v-if="permissions.includes('heat-map')">
                                                                            <Link href="/map/heat_map" class="list-unstyled ">{{$t("heat-map")}}</Link>
                                                                        </li>
                                                                        <li  style="height: 60px;" v-if="permissions.includes('gods-eye')">
                                                                            <Link href="/map/gods_eye"  class="list-unstyled mt-4 ">{{$t("gods-eye")}}</Link>
                                                                        </li>
                                                                    </ul>                                                                    
                                                                </div>
                                                            </li>
                                                            <!-- End Geo Fencing -->
                                                           <!-- Complaints Collapsible Section -->
                                                           <li ref="complaintSection"  style="margin-top: 110px;">
                                                                <div class="first-list" >
                                                                    <div class="list-wrap" v-if="permissions.includes('complaints-management')">
                                                                        <BLink href="javascript:void(0);" class="menu-item ">
                                                                            <span>{{$t("complaint-management")}}</span>
                                                                        </BLink>
                                                                    </div>  
                                                                    <ul class="second-list">
                                                                        <li v-if="permissions.includes('complaint-title')">
                                                                            <Link href="complaint-title" class="list-unstyled ">{{$t("complaint-title")}}</Link>
                                                                        </li>
                                                                        <li style="height: 50px;" v-if="permissions.includes('user-complaint')">
                                                                            <Link href="/user-complaint/general-complaint"  class="list-unstyled mt-3 ">{{$t("user-complaint")}}</Link>
                                                                        </li>
                                                                        <li  style="height: 45px;" v-if="permissions.includes('driver-complaint')">
                                                                            <Link href="/driver-complaint/general-complaint"  class="list-unstyled  mt-3 ">{{$t("driver-complaint")}}</Link>
                                                                        </li>
                                                                        <li  style="height: 52px;" v-if="permissions.includes('owner-complaint')">
                                                                            <Link href="/owner-complaint/general-complaint"  class="list-unstyled  mt-4 ">{{$t("owner-complaint")}}</Link>
                                                                        </li>
                                                                    </ul> 
                                                                </div>    
                                                            </li>
                                                            <!-- End Complaints -->
                                                              <!-- Complaints Collapsible Section -->
                                                              <li style="margin-top: 207px;" v-if="permissions.includes('trip-request-view')">
                                                                <Link href="/rides-request" class="menu-item "><span>{{$t("trip-requests")}}</span>
                                                                </Link>
                                                            </li>
                                                            <li v-if="permissions.includes('manage-delivery-request')">
                                                                <Link href="delivery-rides-request" class="menu-item "><span>{{$t("delivery-requests")}}
                                                                        </span></Link>
                                                            </li>
                                                            <li v-if="permissions.includes('ongoing-request-view')">
                                                                <Link href="ongoing-rides" class="menu-item "><span>{{$t("ongoing-rides")}}</span>
                                                                </Link>
                                                            </li>
                                                           <li ref="otherSection" style="margin-top: 0px;">
                                                            <div class="first-list" >
                                                                <div class="list-wrap" v-if="permissions.includes('others')">
                                                                    <BLink href="javascript:void(0);" class="menu-item ">
                                                                        <span>{{$t("others")}}</span>
                                                                    </BLink>
                                                                </div>  
                                                                <ul class="second-list">
                                                                    <li v-if="permissions.includes('view-sos')">
                                                                        <a href="/sos" class="list-unstyled ">{{$t("sos")}}</a>
                                                                    </li>
                                                                    <li  style="height: 45px;" v-if="permissions.includes('view-cancellation')">
                                                                        <a href="/cancellation"  class="list-unstyled  mt-3 ">{{$t("cancellation")}}</a>
                                                                    </li>
                                                                    <li  style="height: 42px;" v-if="permissions.includes('view-faq')">
                                                                        <a href="/faq"  class="list-unstyled  mt-3 ">{{$t("faq")}}</a>
                                                                    </li>
                                                                </ul> 
                                                                </div>  
                                                            </li>
                                                            <!-- End Complaints -->
                                                        </ul>
                                                    </li>
                                                    <li class="department department2">                                                        
                                                        <BLink href="javascript:void(0);" class="fw-semibold "><span>{{$t("users_menu")}}</span></BLink>
                                                        <ul>
                                                             <!-- Customer Collapsible Section -->
                                                           <li ref="customerSection">
                                                            <div class="first-list" >
                                                                <div class="list-wrap" v-if="permissions.includes('user-management')">
                                                                    <BLink href="javascript:void(0);" class="menu-item ">
                                                                        <span>{{$t("customer-management")}}</span>
                                                                    </BLink>
                                                                </div>  
                                                                <ul class="second-list">
                                                                        <li v-if="permissions.includes('view-users')">
                                                                            <Link href="/users" class="list-unstyled ">{{$t("user-list")}}</Link>
                                                                        </li>
                                                                        <li style="height: 50px;" v-if="permissions.includes('view-delete-user')">
                                                                            <Link href="/users/deleted-user"  class="list-unstyled mt-3 ">{{$t("delete-request-users")}}</Link>
                                                                        </li>
                                                                    </ul> 
                                                                </div>    
                                                            </li>
                                                            <!-- End Complaints -->
                                                              <!-- Driver Collapsible Section -->
                                                              <li ref="driverSection" style="margin-top: 100px">
                                                                <div class="first-list" >
                                                                    <div class="list-wrap" v-if="permissions.includes('drivers-management')">
                                                                        <BLink href="javascript:void(0);" class="menu-item ">
                                                                            <span>{{$t("driver-management")}}</span>
                                                                        </BLink>
                                                                    </div>  
                                                                    <ul class="second-list">
                                                                        <li v-if="permissions.includes('view-approval-pending-drivers')">
                                                                            <Link href="/pending-drivers" class="list-unstyled ">{{$t("pending-drivers")}}</Link>
                                                                        </li>
                                                                        <li style="height: 50px;" v-if="permissions.includes('view-approved-drivers')">
                                                                            <Link href="/approved-drivers"  class="list-unstyled mt-3 ">{{$t("approved-drivers")}}</Link>
                                                                        </li>
                                                                        <li  style="height: 45px;" v-if="permissions.includes('manage-subscription')">
                                                                            <Link href="/subscription"  class="list-unstyled  mt-3 ">{{$t("subscription")}}</Link>
                                                                        </li>
                                                                        <li  style="height: 45px;" v-if="permissions.includes('driver-rating-list')">
                                                                            <Link href="/drivers-rating"  class="list-unstyled  mt-3 ">{{$t("drivers-ratings")}}</Link>
                                                                        </li>
                                                                        <li  style="height: 55px;" v-if="permissions.includes('negetive-balance-drivers')">
                                                                            <Link href="/negative-balance-drivers"  class="list-unstyled  mt-4 ">{{$t("driver-wallet")}}</Link>
                                                                        </li>
                                                                        <li  style="height: 48px;" v-if="permissions.includes('delete-request-drivers')">
                                                                            <Link href="/delete-request-drivers"  class="list-unstyled  mt-3 ">{{$t("delete-request-drivers")}}</Link>
                                                                        </li>
                                                                        <li  style="height: 48px;" v-if="permissions.includes('manage-driver-needed-document')">
                                                                            <Link href="/driver-needed-documents"  class="list-unstyled  mt-4 ">{{$t("driver-needed-documents")}}</Link>
                                                                        </li>
                                                                        <li  style="height: 81px;" v-if="permissions.includes('manage-driver-bank-info')">
                                                                            <Link href="/driver-bank-info"  class="list-unstyled  mt-5 ">{{$t("driver-bank-info")}}</Link>
                                                                        </li>
                                                                    </ul>                                                                    
                                                                </div>   
                                                            </li>
                                                            <!-- End Driver Management -->
                                                             <!-- Loyalty rewards section -->
                                                            <li ref="loyaltySection" style="margin-top: 407px;">
                                                            <div class="first-list" >
                                                                <div class="list-wrap" v-if="permissions.includes('loyalty-rewards')">
                                                                    <BLink href="/referral-settings" class="menu-item ">
                                                                        <!-- <span>{{$t("loyalty-rewards")}}</span> -->
                                                                        <span>{{$t("referral-settings")}}</span>
                                                                    </BLink>
                                                                </div>  
                                                                </div>    
                                                            </li>
                                                            <!-- End Loyalty rewards -->
                                                            <li  style="margin-top: 30px" v-if="permissions.includes('admin')">
                                                                <Link href="/admins" class="menu-item "><span>{{$t("admin-management")}}
                                                                        </span></Link>
                                                            </li>
                                                             <!-- Owner Collapsible Section -->
                                                            <li ref="ownerSection" style="margin-top: 0px;">
                                                                <div class="first-list" >
                                                                    <div class="list-wrap" v-if="permissions.includes('owner-management')">
                                                                        <BLink href="javascript:void(0);" class="menu-item ">
                                                                            <span>{{$t("owner-management")}}</span>
                                                                        </BLink>
                                                                    </div> 
                                                                    <ul class="second-list">
                                                                        <li v-if="permissions.includes('manage-owner')">
                                                                            <Link href="/manage-owners" class="list-unstyled ">{{$t("manage-owners")}}</Link>
                                                                        </li>
                                                                        <li style="height: 50px;" v-if="permissions.includes('access-owner-dashboard')">
                                                                            <Link href="/owner-dashboard"  class="list-unstyled mt-3 ">{{$t("owner-dashboard")}}</Link>
                                                                        </li>
                                                                        <li style="height: 50px;" v-if="permissions.includes('manage-owner')">
                                                                            <Link href="/withdrawal-request-owners"  class="list-unstyled mt-3 ">{{$t("owner-wallet")}}</Link>
                                                                        </li>
                                                                        <li style="height: 50px;" v-if="permissions.includes('manage-fleet')">
                                                                            <Link href="/fleet-drivers/"  class="list-unstyled mt-3 ">{{$t("fleet-drivers")}}</Link>
                                                                        </li>
                                                                        <li  style="height: 39px;" v-if="permissions.includes('view-pending-fleet-drivers')">
                                                                            <Link href="/fleet-drivers/pending"  class="list-unstyled  mt-3 ">{{$t("fleet-pending-drivers")}}</Link>
                                                                        </li>
                                                                        <li  style="height: 73px;" v-if="permissions.includes('fleet-driver-document-view')">
                                                                            <Link href="/fleet-needed-documents"  class="list-unstyled  mt-5 ">{{$t("fleet-needed-document")}}</Link>
                                                                        </li>
                                                                        <li  style="height: 80px;" v-if="permissions.includes('view-fleet')">
                                                                            <Link href="/manage-fleet"  class="list-unstyled  mt-5 ">{{$t("manage-fleet")}}</Link>
                                                                        </li>
                                                                        <li  style="height: 65px;" v-if="permissions.includes('manage-owner-needed-document')">
                                                                            <Link href="/owner-needed-documents"  class="list-unstyled  mt-4 ">{{$t("owner-needed-document")}}</Link>
                                                                        </li>
                                                                    </ul>                                                                    
                                                                </div>
                                                            </li>
                                                            <!-- End Owner  -->
                                                             
                                                            <!-- Report Collapsible Section -->
                                                            <li ref="reportSection" style="margin-top:448px;">
                                                                <div class="first-list" >
                                                                    <div class="list-wrap" v-if="permissions.includes('report-management')">
                                                                        <BLink href="javascript:void(0);" class="menu-item ">
                                                                            <span>{{ $t("report") }}</span>
                                                                        </BLink>
                                                                    </div>   
                                                                    <ul class="second-list">
                                                                        <li v-if="permissions.includes('user-report')">
                                                                            <Link href="/report/user-report" class="list-unstyled ">{{$t("user-report")}}</Link>
                                                                        </li>
                                                                        <li style="height: 50px;" v-if="permissions.includes('driver-report')">
                                                                            <Link href="/report/driver-report"  class="list-unstyled mt-3 ">{{$t("driver-report")}}</Link>
                                                                        </li>
                                                                        <li style="height: 50px;" v-if="permissions.includes('driver-duty-report')">
                                                                            <Link href="/report/driver-duty-report"  class="list-unstyled mt-3 ">{{$t("driver-duty-report")}}</Link>
                                                                        </li>
                                                                        <li  style="height: 45px;" v-if="permissions.includes('owner-report')">
                                                                            <Link href="/report/owner-report"  class="list-unstyled  mt-3 ">{{$t("owner-report")}}</Link>
                                                                        </li>
                                                                        <li  style="height: 45px;" v-if="permissions.includes('finance-report')">
                                                                            <Link href="/report/finance-report"  class="list-unstyled  mt-3 ">{{$t("finance-report")}}</Link>
                                                                        </li>
                                                                        <li  style="height: 50px;" v-if="permissions.includes('fleet-report')">
                                                                            <Link href="/report/fleet-report"  class="list-unstyled  mt-4 ">{{$t("fleet-report")}}</Link>
                                                                        </li>
                                                                    </ul>                                                                    
                                                                </div>   
                                                            </li>                                                            
                                                            <!-- End Reports  -->

                                                               <!-- Support Management Collapsible Section -->
                                                               <li ref="reportSection" style="margin-top:300px;">
                                                                <div class="first-list" >
                                                                    <div class="list-wrap" v-if="permissions.includes('view-support-management')">
                                                                        <BLink href="javascript:void(0);" class="menu-item ">
                                                                            <span>{{ $t("support_management") }}</span>
                                                                        </BLink>
                                                                    </div>   
                                                                    <ul class="second-list">
                                                                        <li v-if="permissions.includes('view-ticket-title')">
                                                                            <Link href="/title" class="list-unstyled ">{{$t("ticket_title")}}</Link>
                                                                        </li>
                                                                        <li style="height: 50px;" v-if="permissions.includes('view-support-ticket')">
                                                                            <Link href="/support-tickets"  class="list-unstyled mt-3 ">{{$t("support_tickets")}}</Link>
                                                                        </li>
                                                                    </ul>                                                                    
                                                                </div>   
                                                            </li>                                                            
                                                            <!-- End Support Management  -->
                                                            
                                                        </ul>
                                                    </li>
                                                    <li class="department department4">
                                                        <BLink href="javascript:void(0);" class="fw-semibold "><span>{{$t("settings_menu")}}</span></BLink>
                                                        <ul>
                                                             <!-- Business settings Collapsible Section -->
                                                             <li ref="businessSection">
                                                                <div class="first-list" >
                                                                    <div class="list-wrap" v-if="permissions.includes('manage-business-settings')">
                                                                    <BLink href="/general-settings" class="menu-item ">
                                                                        <span>{{$t("business-settings")}}</span>
                                                                    </BLink>
                                                                </div>  
                                                                <ul class="second-list">
                                                                    <li v-if="permissions.includes('general-settings-view')">
                                                                        <Link href="/general-settings" class="list-unstyled ">{{$t("general-settings")}}</Link>
                                                                    </li>
                                                                    <li style="height: 50px;" v-if="permissions.includes('customization-settings-view')">
                                                                        <Link href="/customization-settings"  class="list-unstyled mt-3 ">{{$t("customization-settings")}}</Link>
                                                                    </li>
                                                                    <li style="height: 50px;" v-if="permissions.includes('transport-ride-settings-view')">
                                                                        <Link href="/transport-ride-settings"  class="list-unstyled mt-3 ">{{$t("transport-ride-settings")}}</Link>
                                                                    </li>
                                                                    <li  style="height: 50px;" v-if="permissions.includes('bid-ride-settings-view')">
                                                                        <Link href="/bid-ride-settings"  class="list-unstyled  mt-4 ">{{$t("bid-ride-settings")}}</Link>
                                                                    </li>
                                                                </ul> 
                                                            </div>
                                                            </li>
                                                            <!-- End business  -->
                                                              <!-- App Settings Collapsible Section -->
                                                            <li ref="appSection" style="margin-top: 200px;">
                                                                <div class="first-list" >
                                                                    <div class="list-wrap" v-if="permissions.includes('manage-app-settings')">
                                                                        <BLink href="javascript:void(0);" class="menu-item ">
                                                                            <span>{{$t("app-settings")}}</span>
                                                                        </BLink>
                                                                    </div>  
                                                                    <ul class="second-list">
                                                                        <li v-if="permissions.includes('wallet-settings-view')">
                                                                            <Link href="/wallet-settings" class="list-unstyled ">{{$t("wallet-settings")}}</Link>
                                                                        </li>
                                                                        <li v-if="permissions.includes('tip-settings-view')">
                                                                            <Link href="/tip-settings" class="list-unstyled ">{{$t("tip-settings")}}</Link>
                                                                        </li>
                                                                        <li v-if="permissions.includes('manage-country')">
                                                                            <Link href="/country" class="list-unstyled">{{$t("country")}}</Link>
                                                                        </li>
                                                                        <li  style="height: 60px;" v-if="permissions.includes('onboarding-screen-settings-view')">
                                                                            <Link href="onboarding-screen"  class="list-unstyled  mt-4 ">{{$t("onboard-screens")}}</Link>
                                                                        </li>
                                                                    </ul>  
                                                                </div> 
                                                                
                                                            </li>
                                                            <!-- End App settings  -->
                                                                 <!-- third party Collapsible Section -->
                                                            <li ref="thirdSection" style="margin-top: 280px;">
                                                                <div class="first-list" >
                                                                    <div class="list-wrap" v-if="permissions.includes('manage-third-party-settings')">
                                                                        <BLink href="javascript:void(0);" class="menu-item ">
                                                                            <span>{{$t("third-party-settings")}}</span>
                                                                        </BLink>
                                                                    </div>  
                                                                    <ul class="second-list">
                                                                        <li v-if="permissions.includes('payment-gateway-settings-view')">
                                                                            <Link href="/payment-gateway" class="list-unstyled ">{{$t("payment-gateway-settings")}}</Link>
                                                                        </li>
                                                                        <li style="height: 73px;" v-if="permissions.includes('sms-gateway-settings-view')">
                                                                            <Link href="/sms-gateway"  class="list-unstyled mt-5 ">{{$t("sms-gateway-settings")}}</Link>
                                                                        </li>
                                                                        <li  style="height: 84px;" v-if="permissions.includes('firebase-settings-view')">
                                                                            <Link href="/firebase"  class="list-unstyled  mt-5 ">{{$t("firebase-settings")}}</Link>
                                                                        </li>
                                                                        <li  style="height: 55px;" v-if="permissions.includes('map-settings-view')">
                                                                            <Link href="/map-setting"  class="list-unstyled  mt-4 ">{{$t("map-settings")}}</Link>
                                                                        </li>
                                                                        <li  style="height: 84px;" v-if="permissions.includes('mail-configuration-view')">
                                                                            <Link href="/mail-configuration"  class="list-unstyled  mt-5 ">{{$t("mail-configuration")}}</Link>
                                                                        </li>
                                                                        <li  style="height: 48px;" v-if="permissions.includes('recaptcha-view')">
                                                                            <Link href="/recaptcha"  class="list-unstyled  mt-3 ">{{$t("recaptcha")}}</Link>
                                                                        </li>
                                                                        <li  style="height: 48px;" v-if="permissions.includes('notification-channel-view')">
                                                                            <Link href="/notification-channel"  class="list-unstyled  mt-3 ">{{$t("notification-channel")}}</Link>
                                                                        </li>
                                                                    </ul>                                                                    
                                                                </div>
                                                            </li>
                                                            <li ref="cmdSection"style="margin-top: 440px">
                                                                <div class="first-list" >
                                                                    <div class="list-wrap" v-if="permissions.includes('cms-landing-website')">
                                                                        <BLink href="javascript:void(0);" class="menu-item ">
                                                                            <span>{{ $t("cms-landing-website") }}</span>
                                                                        </BLink>
                                                                    </div>  
                                                                    <ul class="second-list">
                                                                        <li v-if="permissions.includes('header_footer')">
                                                                            <Link href="/landing-header" class="list-unstyled ">{{$t("header-footer")}}</Link>
                                                                        </li>
                                                                        <li style="height: 50px;" v-if="permissions.includes('landing_home')">
                                                                            <Link href="/landing-home"  class="list-unstyled mt-4 ">{{$t("home")}}</Link>
                                                                        </li>
                                                                        <li  style="height: 50px;" v-if="permissions.includes('landing_driver')">
                                                                            <Link href="/landing-driver"  class="list-unstyled  mt-4 ">{{$t("driver")}}</Link>
                                                                        </li>
                                                                        <li  style="height: 55px;" v-if="permissions.includes('landing_contact')">
                                                                            <Link href="/landing-contact"  class="list-unstyled  mt-4 ">{{$t("contact")}}</Link>
                                                                        </li>
                                                                        <li  style="height: 50px;" v-if="permissions.includes('landing_aboutus')">
                                                                            <Link href="/landing-aboutus"  class="list-unstyled  mt-3 ">{{$t("aboutus")}}</Link>
                                                                        </li>
                                                                        <li  style="height: 48px;" v-if="permissions.includes('landing_user')">
                                                                            <Link href="/landing-user"  class="list-unstyled  mt-3 ">{{$t("user")}}</Link>
                                                                        </li>
                                                                        <li  style="height: 48px;" v-if="permissions.includes('landing_quicklinks')">
                                                                            <Link href="/landing-quicklink"  class="list-unstyled  mt-3 ">{{$t("quicklinks")}}</Link>
                                                                        </li>
                                                                    </ul>                                                                    
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </li>
                                                    <li class="department department3" v-if="permissions.includes('masters')">
                                                        <BLink href="javascript:void(0);" class="fw-semibold ">
                                                            <span>{{$t("master_menu")}}</span>
                                                        </BLink>
                                                        <ul>
                                                            <li v-if="permissions.includes('languages')">
                                                                <Link href="/languages" class="menu-item "><span>{{$t("language")}}</span>
                                                                </Link>
                                                            </li>
                                                            <li v-if="permissions.includes('roles')">
                                                                <Link href="/roles" class="menu-item "><span>{{$t("roles")}}</span></Link>
                                                            </li>
                                                        </ul>
                                                    </li>
                                                    <!-- <li class="department">
                                                        <BLink href="javascript:void(0);" class="fw-semibold"><span>Travel
                                                                Tips</span></BLink>
                                                        <ul>
                                                            <li>
                                                                <BLink href="javascript:void(0);"><span>General
                                                                        Travel</span></BLink>
                                                            </li>
                                                            <li>
                                                                <BLink href="javascript:void(0);"><span>Helpth
                                                                        Concerns</span></BLink>
                                                            </li>
                                                            <li>
                                                                <BLink href="javascript:void(0);"><span>Safety
                                                                        Measures</span></BLink>
                                                            </li>
                                                            <li>
                                                                <BLink href="javascript:void(0);"><span>FAQ's</span></BLink>
                                                            </li>
                                                        </ul>
                                                    </li> -->
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </figure>
                        </div>
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>

    </Layout>
</template>

<style>
.departments > li:first-child {
    width: 18.59%;
    height: 64px;
    margin: 0 auto 92px auto;
    padding-top: 25px;
    border-bottom: 0px dashed var(--vz-border-color);
    z-index: 1;
    float: left;
    left: 27%;
}
.administration .subdirector::after {
    content: "";
    display: block;
    width: 0;
    height: 130px;
    border-left: 2px dashed var(--vz-border-color);
    left: 49%;
    position: relative;
}
.rtl .departments > li:first-child {
    float: right;
    left: 35%;
    right: auto;
}
.department {
    border-left: 2px dashed var(--vz-border-color);
    float: left;
    margin-left: 8.75%;
    margin-bottom: 290px;
    width: 18.25%;
}
.rtl .department{
    float: right;
    margin-right: 7.75%;
    margin-bottom: 260px;
}
.sitemap-horizontal ul a {
    display: block;
    background: var(--vz-light);
    border: 2px solid var(--vz-secondary-bg);
    box-shadow:  0  1px 3px rgba(56, 65, 74, 0.15);
    font-size: 0.8125rem;
    height: 60px;
    padding: 8px;
    display: flex;
    justify-content: center;
    align-items: center;
}
.rtl .department ul li a {
    right: 42px;
    left: auto;
}
.clpse{
    position:relative;
    z-index:5;
    left: 170px;
}

.scrollable-section {
    max-height: 300px; /* Set a fixed height for scrollable content */
    overflow-y: auto; /* Enable vertical scrolling */
    padding-right: 12px; /* Ensure padding so scrollbar doesn't overlap content */
}

/* Custom Scrollbar Styling for WebKit browsers (Chrome, Safari) */
.scrollable-section::-webkit-scrollbar {
    width: 2px; 
}

.scrollable-section::-webkit-scrollbar-track {
    background: #f1f1f1; /* Scrollbar track color */
}

.scrollable-section::-webkit-scrollbar-thumb {
    background: #e7e7e7; /* Scrollbar thumb color */
    border-radius: 10px; /* Rounded corners for thumb */
}

.scrollable-section::-webkit-scrollbar-thumb:hover {
    background: #555; /* Darker color on hover */
}

/* Firefox Custom Scrollbar */
.scrollable-section {
    scrollbar-width: thin; /* Thin scrollbar for Firefox */
    scrollbar-color: #888 #f1f1f1; /* Thumb and track color */
}

.department .second-list{
    margin-top: 64px;
    margin-bottom: 0px;
}


.rtl .department .second-list{
    margin-top: 64px;
    margin-bottom: 0px;
}
.first-list{
    position: relative;
    padding-top: 10px;
}
.rtl .first-list{
    position: relative;
    padding-top: 10px;
}

.verti-sitemap .first-list .second-list, .verti-sitemap .first-list .third-list {
    position: relative;
}
.verti-sitemap .first-list .second-list, .verti-sitemap .first-list .third-list {
    margin-left: 42px;
}
.list-unstyled{
    box-shadow: none !important;
    justify-content: normal !important;
    text-align: left !important;
}
.second-list:before {
    content: "";
    height: 100%;
    border-left: 2px dashed var(--vz-border-color);
    position: absolute;
    top: 0;
    left: 0;
    margin: 0 auto;
}
.rtl .second-list:before {
    content: "";
    height: 97%;
    border-left: 2px dashed var(--vz-border-color);
    position: absolute;
    top: 6px;
    left: 151px;
    margin: 0 auto;
}
.menu-item{
    border-width: 1px !important;
    border-style: solid !important;
    border-color: #405193 !important;
}
.rtl .menu-item{
    border-width: 1px !important;
    border-style: solid !important;
    border-color: #405193 !important;
}
/* Mobile menu styles */
@media (max-width: 768px) {
    .department {
        width: 100%; 
        margin-left: 0px;
    }
    .department2{
        margin-top: 0px;
    }
    .department3{
        margin-top: 167px;
    }
    .department4{
        margin-top:104px;
    }
    .clpse{
        position:relative;
        z-index:5;
        left: 10px;
    }
    .rtl .department {
        width: 100%; 
        margin-left: 0px;
    }
    .rtl .department2{
        margin-top: 0px;
    }
    .rtl .department3{
        margin-top: 167px;
    }
    .rtl .department4{
        margin-top:104px;
    }
    .rtl .clpse{
        position:relative;
        z-index:5;
        left: 10px;
    }
    .rtl .departments > li:first-child {
        float: right;
        left: 61%;
        right: auto;
    }
    .rtl .second-list:before {
        content: "";
        height: 97%;
        border-left: 2px dashed var(--vz-border-color);
        position: absolute;
        top: 6px;
        left: 526px;
        margin: 0 auto;
    }
    .rtl .department{
        float: right;
        margin-right: 0.75%;
        margin-bottom: 260px;
    }
    .rtl .department ul li a {
        left: -22px;
        right: auto;
    }
}

@media (max-width: 426px) {
    .rtl .second-list:before {
        content: "";
        height: 97%;
        border-left: 2px dashed var(--vz-border-color);
        position: absolute;
        top: 6px;
        left: 254px;
        margin: 0 auto;
    }
}
@media (max-width: 1025px) and (min-width : 769px) {
    .rtl .second-list:before {
        content: "";
        height: 98%;
        border-left: 2px dashed var(--vz-border-color);
        position: absolute;
        top: 6px;
        left: 99px;
        margin: 0 auto;
    }
    .rtl .department ul li a {
        width: 106%;
    }
}
.department .second-list {
    margin-top: 70px;
    margin-bottom: 0px;
}
</style>