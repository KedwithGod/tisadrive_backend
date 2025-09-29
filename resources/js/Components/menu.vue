<script>
import { Link, router } from '@inertiajs/vue3';
import { layoutComputed } from "@/state/helpers";
import simplebar from "simplebar-vue";
import { mapGetters } from 'vuex';

export default {
  components: {
    simplebar,
    Link
  },
  props: ['activeMenu','supportTicket'],
  data() {
    return {
      settings: {
        minScrollbarLength: 60,
      },
     
    };
  },
  computed: {
    ...layoutComputed,
    ...mapGetters(['permissions']),
    layoutType: {
      get() {
        return this.$store ? this.$store.state.layout.layoutType : {} || {};
      },
    },
  },
  mounted() {
    this.initActiveMenu();
    this.onRoutechange();
    if (document.querySelectorAll(".navbar-nav .collapse")) {
      let collapses = document.querySelectorAll(".navbar-nav .collapse");

      collapses.forEach((collapse) => {
        // Hide sibling collapses on `show.bs.collapse`
        collapse.addEventListener("show.bs.collapse", (e) => {
          e.stopPropagation();
          let closestCollapse = collapse.parentElement.closest(".collapse");
          if (closestCollapse) {
            let siblingCollapses =
              closestCollapse.querySelectorAll(".collapse");
            siblingCollapses.forEach((siblingCollapse) => {
              if (siblingCollapse.classList.contains("show")) {
                siblingCollapse.classList.remove("show");
                siblingCollapse.parentElement.firstChild.setAttribute("aria-expanded", "false");
              }
            });
          } else {
            let getSiblings = (elem) => {
              // Setup siblings array and get the first sibling
              let siblings = [];
              let sibling = elem.parentNode.firstChild;
              // Loop through each sibling and push to the array
              while (sibling) {
                if (sibling.nodeType === 1 && sibling !== elem) {
                  siblings.push(sibling);
                }
                sibling = sibling.nextSibling;
              }
              return siblings;
            };
            let siblings = getSiblings(collapse.parentElement);
            siblings.forEach((item) => {
              if (item.childNodes.length > 2) {
                item.firstElementChild.setAttribute("aria-expanded", "false");
                item.firstElementChild.classList.remove("active");
              }
              let ids = item.querySelectorAll("*[id]");
              ids.forEach((item1) => {
                item1.classList.remove("show");
                item1.parentElement.firstChild.setAttribute("aria-expanded", "false");
                item1.parentElement.firstChild.classList.remove("active");
                if (item1.childNodes.length > 2) {
                  let val = item1.querySelectorAll("ul li a");

                  val.forEach((subitem) => {
                    if (subitem.hasAttribute("aria-expanded"))
                      subitem.setAttribute("aria-expanded", "false");
                  });
                }
              });
            });
          }
        });

        // Hide nested collapses on `hide.bs.collapse`
        collapse.addEventListener("hide.bs.collapse", (e) => {
          e.stopPropagation();
          let childCollapses = collapse.querySelectorAll(".collapse");
          childCollapses.forEach((childCollapse) => {
            let childCollapseInstance = childCollapse;
            childCollapseInstance.classList.remove("show");
            childCollapseInstance.parentElement.firstChild.setAttribute("aria-expanded", "false");
          });
        });
      });
    }
  },

  methods: {
    onRoutechange() {
      // this.initActiveMenu();
      setTimeout(() => {
        var currentPath = window.location.pathname;
        if (document.querySelector("#navbar-nav")) {
          let currentPosition = document.querySelector("#navbar-nav").querySelector('[href="' + currentPath + '"]')?.offsetTop;
          if (currentPosition > document.documentElement.clientHeight) {
            document.querySelector("#scrollbar .simplebar-content-wrapper") ? document.querySelector("#scrollbar .simplebar-content-wrapper").scrollTop = currentPosition + 300 : '';
          }
        }
      }, 500);
    },

    isSubMenuActive(submenuPaths) {
      // Check if the current URL starts with any of the submenu paths
      return submenuPaths.some((path) => window.location.pathname.startsWith('/' + path));
    },



  initActiveMenu() {
    const intervalId = setInterval(() => {
      const currentPath = window.location.pathname;
      const menuLinks = document.querySelectorAll(".navbar-nav .nav-link");
      if (menuLinks.length > 0) {
        clearInterval(intervalId); // Stop the interval once menu links are found
        // Iterate over the menu links and apply the "active" class
        menuLinks.forEach(link => {
          const linkHref = link.getAttribute("href");
          if (currentPath.startsWith(linkHref)) {
            link.classList.add("active");
            let parentCollapseDiv = link.closest(".collapse.menu-dropdown");
            if (parentCollapseDiv) {
              parentCollapseDiv.classList.add("show");
              parentCollapseDiv.parentElement.children[0].classList.add("active");
              parentCollapseDiv.parentElement.children[0].setAttribute("aria-expanded", "true");
             let ancestorCollapse = parentCollapseDiv.parentElement.closest(".collapse.menu-dropdown");
              while (ancestorCollapse) {
                ancestorCollapse.classList.add("show");
                const previousSibling = ancestorCollapse.previousElementSibling;
                if (previousSibling) {
                  previousSibling.classList.add("active");
                }
                ancestorCollapse = ancestorCollapse.closest(".collapse.menu-dropdown")?.previousElementSibling;
              }
            }
          }
        });
      }
    }, 100); // Check every 100ms
  }
  },
};
</script>

<template>
  <BContainer fluid>
    <div id="two-column-menu"></div>

    <template v-if="layoutType === 'vertical' || layoutType === 'semibox'">
      <ul class="navbar-nav h-100" id="navbar-nav">
        <div  v-if = "activeMenu === 'Home'" class="menu">
          <li class="menu-title">
            <span data-key="t-home"> {{ $t("home") }}</span>
          </li>

          <li class="nav-item" v-if="permissions.includes('access-dashboard')">
              <Link class="nav-link menu-link" href="/dashboard">
                  <i class=" ri-home-4-line"></i> <span data-key="t-dashboard">{{ $t("dashboard") }}</span>
              </Link>
          </li>
          <!-- <li class="nav-item" v-if="permissions.includes('dispatcher')">
              <Link class="nav-link menu-link" href="/dispatch">
                  <i class=" ri-taxi-fill"></i> <span data-key="t-dispatch-ride">{{ $t("dispatch-ride") }}</span>
              </Link>
          </li> -->
          <li class="nav-item" v-if="permissions.includes('chat')">
              <Link class="nav-link menu-link" href="/chat">
                  <i class=" bx bx-message-rounded-dots"></i> <span data-key="t-chat">{{ $t("chat") }}</span>
              </Link>
          </li>
          
          <li class="nav-item" v-if="permissions.includes('promotion-management')">
            <a class="nav-link menu-link" href="#promo" data-bs-toggle="collapse" role="button"
              aria-expanded="false" aria-controls="promo">
              <i class="ri-gift-line"></i>
              <span data-key="t-geo">{{ $t("promotion-management") }}</span>
            </a>
            <div class="menu-dropdown collapse" id="promo">
              <ul class="nav nav-sm flex-column">
                <li class="nav-item" v-if="permissions.includes('manage-promo')">
                  <Link class="nav-link" href="/promo-code">
                      {{ $t("promo_code") }}
                  </Link>
                </li>
                
              <li class="nav-item" v-if="permissions.includes('view-notifications')">
                <Link href="/push-notifications" class="nav-link" data-key="t-push">
                  {{ $t("send-notification") }}
                </Link>
              </li>
                <li class="nav-item" v-if="permissions.includes('banner_image')">
                <Link href="/banner-image" class="nav-link" data-key="t-banner-image">
                  {{ $t("banner-image") }}
                </Link>
              </li>
              </ul>
            </div>
          </li>
          <li class="nav-item" v-if="permissions.includes('price-management')">
            <a class="nav-link menu-link" href="#vehiclemanage" data-bs-toggle="collapse" role="button"
            aria-expanded="false" aria-controls="vehiclemanage" :class="{ 'active': [ '/drivers-levelup','/incentives'].some(path => $page.url.startsWith(path)) }">
              <i class="bx bx-money"></i>
              <span data-key="t-price">{{ $t("price-management") }}</span>
            </a>
            <div class="collapse menu-dropdown" id="vehiclemanage">
              <ul class="nav nav-sm flex-column">
                <li class="nav-item" v-if="permissions.includes('service-location')">
                  <Link class="nav-link" href="/service-locations" data-key="t-service-location">
                      {{ $t("service_location") }}
                  </Link>
                </li>
                <li class="nav-item" v-if="permissions.includes('view-zone')">
                  <Link class="nav-link" href="/zones" data-key="t-zone" >
                    {{ $t("zone") }}
                  </Link>
                </li>
                <li class="nav-item" v-if="permissions.includes('view-airport')">
                  <Link class="nav-link" href="/airport" data-key="t-airport" >
                    {{ $t("airport") }}
                  </Link>
                </li>
                <li class="nav-item" v-if="permissions.includes('vehicle-types')">
                  <Link href="/vehicle_type" class="nav-link" data-key="t-type">
                    {{ $t("vehicle_type") }}
                  </Link>
                </li>
                <li class="nav-item" v-if="permissions.includes('rental-package')">
                  <Link href="/rental-package-types" class="nav-link" data-key="t-rental">
                    {{ $t("rental-package") }}
                  </Link>
                </li>
                <li class="nav-item" v-if="permissions.includes('vehicle-fare')">
                  <Link href="/set-prices" class="nav-link" data-key="t-price" :class="{ 'active': [ '/drivers-levelup','/incentives'].some(path => $page.url.startsWith(path)) }">
                    {{ $t("set-price") }}
                  </Link>
                </li>
                <!-- <li class="nav-item" v-if="permissions.includes('incentives')">
                  <Link href="/incentives" class="nav-link" data-key="t-incentives">
                    {{ $t("incentives") }}
                  </Link>
                </li> -->
                <li class="nav-item" v-if="permissions.includes('manage-goods-types')">
                  <Link href="/goods-type" class="nav-link" data-key="t-goods-type">
                    {{ $t("goods-type") }}
                  </Link>
                </li>
              </ul>
            </div>
          </li>
          <li class="nav-item" v-if="permissions.includes('geo-fencing')">
            <a class="nav-link menu-link" href="#mapview" data-bs-toggle="collapse" role="button"
              aria-expanded="false" aria-controls="mapview">
              <i class="bx bx-map"></i>
              <span data-key="t-geo">{{ $t("geofencing") }}</span>
            </a>
            <div class="menu-dropdown collapse" id="mapview">
              <ul class="nav nav-sm flex-column">
                <li class="nav-item" v-if="permissions.includes('heat-map')">
                  <Link class="nav-link" href="/map/heat_map" data-key="t-heat-map">
                    {{ $t("heat-map") }}
                  </Link>
                </li>
                <li class="nav-item" v-if="permissions.includes('gods-eye')">
                  <Link href="/map/gods_eye" class="nav-link" data-key="t-gods">
                    {{ $t("gods-eye") }}
                  </Link>
                </li>
                <!-- <li class="nav-item" v-if="permissions.includes('peak-zone-view')">
                  <Link href="/peak_zone" class="nav-link" data-key="t-peakzone">
                    {{$t("peakzone")}}
                  </Link>
                </li> -->
              </ul>
            </div>
          </li>
          <li class="nav-item" v-if="permissions.includes('complaints-management')">
            <a class="nav-link menu-link" href="#complaint" data-bs-toggle="collapse" role="button"
              aria-expanded="false" aria-controls="complaint">
              <i class=" ri-question-line"></i>
              <span data-key="t-dashboards">{{ $t("complaint-management") }}</span>
            </a>
            <div class="collapse menu-dropdown" id="complaint">
              <ul class="nav nav-sm flex-column">
                <li class="nav-item" v-if="permissions.includes('complaint-title')">
                  <Link class="nav-link" href="/complaint-title" data-key="t-complaint-title">
                    {{ $t("complaint-title") }}
                  </Link>
                </li>
                <li class="nav-item" v-if="permissions.includes('user-complaint')">
                  <BLink class="nav-link" href="#usercomplaints" data-bs-toggle="collapse" role="button"
                    aria-expanded="false" aria-controls="usercomplaints" data-key="t-user-complaint">
                    {{ $t("user-complaint") }}
                  </BLink>
                  <div class="collapse menu-dropdown" id="usercomplaints">
                    <ul class="nav nav-sm flex-column">
                      <li class="nav-item">
                        <Link  href="/user-complaint/general-complaint" class="nav-link" data-key="t-general" > {{ $t("general-complaint") }}
                        </Link>
                      </li>
                      <li class="nav-item">
                        <Link href="/user-complaint/request-complaint" class="nav-link" data-key="t-request"> {{ $t("request-complaint") }}
                        </Link>
                      </li>
                    </ul>
                  </div>
                </li>
                <li class="nav-item" v-if="permissions.includes('driver-complaint')">
                  <BLink class="nav-link " href="#drivercomplaints" data-bs-toggle="collapse" role="button"
                    aria-expanded="false" aria-controls="drivercomplaints" data-key="t-driver">
                    {{ $t("driver-complaint") }}
                  </BLink>
                  <div class="collapse menu-dropdown" id="drivercomplaints">
                    <ul class="nav nav-sm flex-column">
                      <li class="nav-item">
                        <Link href="/driver-complaint/general-complaint" class="nav-link" data-key="t-general"> {{ $t("general-complaint") }}
                        </Link>
                      </li>
                      <li class="nav-item">
                        <Link href="/driver-complaint/request-complaint" class="nav-link" data-key="t-request"> {{ $t("request-complaint") }}
                        </Link>
                      </li>
                    </ul>
                  </div>
                </li>
                <li class="nav-item" v-if="permissions.includes('owner-complaint')">
                  <BLink class="nav-link " href="#ownercomplaints" data-bs-toggle="collapse" role="button"
                    aria-expanded="false" aria-controls="ownercomplaints" data-key="t-owner">
                    {{ $t("owner-complaint") }}
                  </BLink>
                  <div class="collapse menu-dropdown" id="ownercomplaints">
                    <ul class="nav nav-sm flex-column">
                      <li class="nav-item">
                        <Link href="/owner-complaint/general-complaint" class="nav-link" data-key="t-general"> {{ $t("general-complaint") }}
                        </Link>
                      </li>
                      <li class="nav-item">
                        <Link href="/owner-complaint/request-complaint" class="nav-link" data-key="t-request"> {{ $t("request-complaint") }}
                        </Link>
                      </li>
                    </ul>
                  </div>
                </li>
              </ul>
            </div>
          </li>
        <li class="nav-item" v-if="permissions.includes('trip-request-view')">
          <Link href="/rides-request" class="nav-link menu-link" data-key="t-rides-request">
            <i class=" ri-taxi-fill"></i>
            <span data-key="t-trip-requests">{{ $t("trip-requests") }}</span>
          </Link>
        </li>
        <li class="nav-item" v-if="permissions.includes('manage-delivery-request')">
          <Link href="/delivery-rides-request" class="nav-link menu-link" data-key="t-delivery-rides-request">
            <i class=" bx bx-package"></i>
            <span data-key="t-delivery-requests">{{ $t("delivery-requests") }}</span>
          </Link>
        </li>        
          <li class="nav-item" v-if="permissions.includes('ongoing-request-view')">
            <Link href="/ongoing-rides" class="nav-link menu-link" data-key="t-ongoing-rides">
              <i class=" ri-taxi-fill"></i>
              <span data-key="t-ongoing-requests">{{ $t("ongoing-rides") }}</span>
            </Link>
          </li>
        </div>
              <!-- Others -->
       <div v-if = "activeMenu === 'Home'"  >
        <li class="menu-title"  v-if="permissions.includes('others')">
          <span data-key="t-other">{{ $t("others") }}</span>
        </li>
        <li class="nav-item"  v-if="permissions.includes('others')">
          <a class="nav-link menu-link" href="#Others" data-bs-toggle="collapse" role="button"
            aria-expanded="false" aria-controls="Others">
            <i class="mdi mdi mdi-dots-horizontal-circle"></i>
            <span data-key="t-dashboards"> {{ $t("others") }}</span>
          </a>
          <div class="collapse menu-dropdown" id="Others">
            <ul class="nav nav-sm flex-column">
              <li class="nav-item" v-if="permissions.includes('view-sos')">
                <Link href="/sos" class="nav-link" data-key="t-sos">
                  {{ $t("sos") }}
                </Link>
              </li>


            
              <li class="nav-item" v-if="permissions.includes('view-cancellation')">
                <Link href="/cancellation" class="nav-link" data-key="t-cancel">
                  {{ $t("cancellation") }}
                </Link>
              </li>
              <li class="nav-item" v-if="permissions.includes('view-faq')">
                <Link href="/faq" class="nav-link" data-key="t-faq">
                  {{ $t("faq") }}
                </Link>
              </li>
                <!-- <li class="nav-item" v-if="permissions.includes('view-mail-template')">
                <Link href="/mail-template" class="nav-link" data-key="t-mail-template">
                  {{ $t("email-template") }}
                </Link>
              </li> -->
            </ul>
          </div>
        </li>
        </div>
        <!-- end Dashboard Menu -->
<!-- users menu -->
      <div  v-if = "activeMenu === 'Users'" class="menu">
        <li class="menu-title">
          <span data-key="t-users"> {{ $t("users") }}</span>
        </li>
        <!-- <li class="nav-item">
            <Link class="nav-link menu-link" href="/user-dashboard">
                <i class=" ri-user-3-line"></i> <span data-key="t-user-dashboard">{{ $t("user-dashboard") }}</span>
            </Link>
        </li> -->
        <li class="nav-item" v-if="permissions.includes('user-management')">
          <a class="nav-link menu-link" href="#customer" data-bs-toggle="collapse" role="button"
            aria-expanded="false" aria-controls="customer">
            <i class="ri-team-fill"></i>
            <span data-key="t-customer-management"> {{ $t("customer-management") }}</span>
          </a>
          <div class="collapse menu-dropdown" id="customer">
            <ul class="nav nav-sm flex-column">
              <!-- <li class="nav-item" v-if="permissions.includes('view-users')">
                  <Link class="nav-link" href="/users/user-dashboard" data-key="t-user-dashboard">
                      {{ $t("user-dashboard") }}
                  </Link>
              </li> -->
              <li class="nav-item" v-if="permissions.includes('view-users')">
                <Link href="/users" class="nav-link" data-key="t-user-list">
                  {{ $t("user-list") }}
                </Link>
              </li>
              <li class="nav-item" v-if="permissions.includes('view-delete-user')">
                <Link href="/users/deleted-user" class="nav-link" data-key="t-delete-request-users">
                  {{ $t("delete-request-users") }}
                </Link>
              </li>
              
            </ul>
          </div>
        </li>
        <li class="nav-item" v-if="permissions.includes('drivers-management')">
          <a class="nav-link menu-link" href="#driver" data-bs-toggle="collapse" role="button"
            aria-expanded="false" aria-controls="driver">
            <i class="ri-user-follow-line"></i>
            <span data-key="t-driver-management"> {{ $t("driver-management") }}</span>
          </a>
          <div class="collapse menu-dropdown" id="driver">
            <ul class="nav nav-sm flex-column">
              <!-- <li class="nav-item">
                <Link href="/drivers" class="nav-link" data-key="t-drivers">
                  {{ $t("drivers") }}
                </Link>
              </li> -->
              <li class="nav-item" v-if="permissions.includes('view-approval-pending-drivers')">
                <Link href="/pending-drivers" class="nav-link" data-key="t-pending-drivers" >
                  {{ $t("pending-drivers") }}
                </Link>
              </li>
              <li class="nav-item" v-if="permissions.includes('view-approved-drivers')">
                <Link href="/approved-drivers" class="nav-link" data-key="t-approved-drivers">
                  {{ $t("approved-drivers") }}
                </Link>
              </li>
              <li class="nav-item" v-if="permissions.includes('manage-subscription')">
                <Link href="/subscription" class="nav-link" data-key="t-subscription">
                  {{ $t("subscription") }}
                </Link>
              </li>
              <!-- <li class="nav-item" v-if="permissions.includes('view-drivers-levelup')">
                <Link href="/drivers-levelup" class="nav-link" data-key="t-approved-drivers">
                  {{ $t("drivers-levelup") }}
                </Link>
              </li> -->
              <li class="nav-item" v-if="permissions.includes('driver-rating-list')">
                <Link href="/drivers-rating" class="nav-link" data-key="t-drivers-ratings">
                  {{ $t("drivers-ratings") }}
                </Link>
              </li>
              <li class="nav-item" v-if="permissions.includes('driver-wallet')">
                <BLink class="nav-link " href="#driverwallet" data-bs-toggle="collapse" role="button"
                  aria-expanded="false" aria-controls="driverwallet" data-key="t-driver-wallet">
                  {{ $t("driver-wallet") }}
                </BLink>
                <div class="collapse menu-dropdown" id="driverwallet">
                  <ul class="nav nav-sm flex-column">
                    <li class="nav-item" v-if="permissions.includes('negetive-balance-drivers')">
                      <Link href="/negative-balance-drivers" class="nav-link" data-key="t-negative-balance-drivers"> {{ $t("negative-balance-drivers") }}
                      </Link>
                    </li>
                    <li class="nav-item" v-if="permissions.includes('withdrawal-request-drivers')">
                      <Link href="/withdrawal-request-drivers" class="nav-link" data-key="t-withdrawal-request-drivers"> {{ $t("withdrawal-request-drivers") }}
                      </Link>
                    </li>
                  </ul>
                </div>
              </li>
              <li class="nav-item" v-if="permissions.includes('delete-request-drivers')">
                <Link href="/delete-request-drivers" class="nav-link" data-key="t-delete-request-drivers">
                  {{ $t("delete-request-drivers") }}
                </Link>
              </li>
              <li class="nav-item" v-if="permissions.includes('manage-driver-needed-document')">
                <Link href="/driver-needed-documents" class="nav-link" data-key="t-driver-needed-documents">
                  {{ $t("driver-needed-documents") }}
                </Link>
              </li>
              <li class="nav-item" v-if="permissions.includes('manage-driver-bank-info')">
                <Link href="/driver-bank-info" class="nav-link" data-key="t-driver-bank-info">
                  {{ $t("driver-bank-info") }}
                </Link>
              </li>              
            </ul>
          </div>
        </li>
        <li class="nav-item" v-if="permissions.includes('referral-settings-view')">
              <Link class="nav-link menu-link" href="/referral-settings">
                  <i class="bx bx-share-alt"></i> <span data-key="t-dashboard">{{ $t("referral-settings") }}</span>
              </Link>
          </li>
        <!-- <li class="nav-item" v-if="permissions.includes('loyalty-rewards')">
          <a class="nav-link menu-link" href="#loyalty" data-bs-toggle="collapse" role="button"
            aria-expanded="false" aria-controls="loyalty">
            <i class="ri-team-fill"></i>
            <span data-key="t-loyalty-management"> {{ $t("loyalty-rewards") }}</span>
          </a>
          <div class="collapse menu-dropdown" id="loyalty">
            <ul class="nav nav-sm flex-column">
              <li class="nav-item" v-if="permissions.includes('incentives')">
                  <Link href="/incentives" class="nav-link" data-key="t-incentives">
                    {{ $t("incentives") }}
                  </Link>
                </li>
              <li class="nav-item" v-if="permissions.includes('referral-settings-view')">
                <Link href="/referral-settings" class="nav-link" data-key="t-referral-settings">
                  {{ $t("referral-settings") }}
                </Link>
              </li>
              <li class="nav-item" v-if="permissions.includes('view-drivers-levelup')">
                <Link href="/drivers-levelup" class="nav-link" data-key="t-approved-drivers">
                  {{ $t("drivers-levelup") }}
                </Link>
              </li>
              
            </ul>
          </div>
        </li> -->
        <li class="nav-item" v-if="permissions.includes('admin')">
          <a class="nav-link menu-link" href="#admin" data-bs-toggle="collapse" role="button"
            aria-expanded="false" aria-controls="admin">
            <i class=" ri-group-line"></i>
            <span data-key="t-admin-management"> {{ $t("admin-management") }}</span>
          </a>
          <div class="collapse menu-dropdown" id="admin">
            <ul class="nav nav-sm flex-column">
              <!-- <li class="nav-item">
                <Link href="/roles" class="nav-link" data-key="t-roles-permissions">
                  {{ $t("roles-permissions") }}
                </Link>
              </li> -->
              <li class="nav-item" v-if="permissions.includes('admin')">
                <Link href="/admins" class="nav-link" data-key="t-admins">
                  {{ $t("admins") }}
                </Link>
              </li>
            </ul>
          </div>
        </li>
        
        <li class="nav-item" v-if="permissions.includes('owner-management')">
          <a class="nav-link menu-link" href="#owner" data-bs-toggle="collapse" role="button"
            aria-expanded="false" aria-controls="admin">
            <i class="ri-dashboard-2-line"></i>
            <span data-key="t-owner-management">{{ $t("owner-management") }}</span>
          </a>
          <div class="collapse menu-dropdown" id="owner">
            <ul class="nav nav-sm flex-column">              
            <li class="nav-item" v-if="permissions.includes('access-owner-dashboard')">
                <Link class="nav-link" href="/owner-dashboard"><span data-key="t-owner-dashboard">{{ $t("owner-dashboard") }}</span>
                </Link>
            </li>
              <li class="nav-item" v-if="permissions.includes('manage-owner')">

                <Link href="/manage-owners" class="nav-link" data-key="t-manage-owners">
                  {{ $t("manage-owners") }}
                </Link>
              </li>
              <li class="nav-item" v-if="permissions.includes('manage-owner')">

                <Link href="/withdrawal-request-owners" class="nav-link" data-key="t-owner-wallet">
                  {{ $t("owner-wallet") }}
                </Link>
              </li>              
              <li class="nav-item" v-if="permissions.includes('manage-fleet')">
                <BLink class="nav-link" href="#fleet" data-bs-toggle="collapse" role="button"
                  aria-expanded="false" aria-controls="fleet" data-key="t-fleet-management">
                  {{ $t("fleet-management") }}
                </BLink>
                <div class="collapse menu-dropdown" id="fleet">
                  <ul class="nav nav-sm flex-column">
                    <li class="nav-item" v-if="permissions.includes('view-approved-fleet-drivers')">
                      <Link href="/fleet-drivers" class="nav-link" data-key="t-drivers"> {{ $t("fleet-drivers") }}
                      </Link>
                    </li>
                    <li class="nav-item" v-if="permissions.includes('view-pending-fleet-drivers')">
                      <Link href="/fleet-drivers/pending" class="nav-link" data-key="t-drivers"> {{ $t("fleet-pending-drivers") }}
                      </Link>
                    </li>
                    <li class="nav-item" v-if="permissions.includes('fleet-driver-document-view')">
                      <Link href="/fleet-needed-documents" class="nav-link" data-key="t-fleet-needed-document">  {{ $t("fleet-needed-document") }}
                      </Link>
                    </li>
                    <li class="nav-item" v-if="permissions.includes('view-fleet')">
                      <Link href="/manage-fleet" class="nav-link" data-key="t-manage-fleet"> {{ $t("manage-fleet") }}
                      </Link>
                    </li>
                  </ul>
                </div>
              </li>
              <li class="nav-item" v-if="permissions.includes('manage-owner-needed-document')"> 
                <Link href="/owner-needed-documents" class="nav-link" data-key="t-owner-needed-document">
                  {{ $t("owner-needed-document") }}
                </Link>
              </li>
              <li class="nav-item" v-if="permissions.includes('view-delete-owner')">
                <Link href="/manage-owners/deleted-owner" class="nav-link" data-key="t-delete-request-owners">
                  {{ $t("delete-request-owners") }}
                </Link>
              </li>
            </ul>
          </div>
        </li>
        <li class="nav-item" v-if="permissions.includes('report-management')">
          <a class="nav-link menu-link" href="#report" data-bs-toggle="collapse" role="button"
            aria-expanded="false" aria-controls="report">
            <i class="ri-file-3-fill"></i>
            <span data-key="t-report"> {{ $t("report") }}</span>
          </a>
          <div class="collapse menu-dropdown" id="report">
            <ul class="nav nav-sm flex-column">
              <li class="nav-item" v-if="permissions.includes('user-report')">
                <Link href="/report/user-report" class="nav-link" data-key="t-user-report">
                  {{ $t("user-report") }}
                </Link>
              </li>
              <li class="nav-item" v-if="permissions.includes('driver-report')">
                <Link href="/report/driver-report" class="nav-link" data-key="t-driver-report">
                  {{ $t("driver-report") }}
                </Link>
              </li>
              <li class="nav-item" v-if="permissions.includes('driver-duty-report')">
                <Link href="/report/driver-duty-report" class="nav-link" data-key="t-driver-duty-report">
                  {{ $t("driver-duty-report") }}
                </Link>
              </li>              
              <li class="nav-item" v-if="permissions.includes('owner-report')">
                <Link href="/report/owner-report" class="nav-link" data-key="t-owner-report">
                  {{ $t("owner-report") }}
                </Link>
              </li>
              <li class="nav-item" v-if="permissions.includes('finance-report')">
                <Link href="/report/finance-report" class="nav-link" data-key="t-finance-report">
                  {{ $t("finance-report") }}
                </Link>
              </li>
              <li class="nav-item" v-if="permissions.includes('fleet-report')">
                <Link href="/report/fleet-report" class="nav-link" data-key="t-fleet-report">
                  {{ $t("fleet-report") }}
                </Link>
              </li>              
            </ul>
          </div>
        </li>
        <div v-if="supportTicket == 1">
          <li class="nav-item" v-if="permissions.includes('view-support-management')">
          <a class="nav-link menu-link" href="#support" data-bs-toggle="collapse" role="button"
            aria-expanded="false" aria-controls="report">
            <i class="bx bx-support"></i>
            <span data-key="t-report"> {{ $t("support_management") }}</span>
          </a>
          <div class="collapse menu-dropdown" id="support">
            <ul class="nav nav-sm flex-column">
               <!-- <li class="nav-item" v-if="permissions.includes('view-category')">
                <Link href="/category" class="nav-link" data-key="t-category">
                  {{ $t("category") }}
                </Link>
              </li>    -->
              <li class="nav-item" v-if="permissions.includes('view-ticket-title')">
                <Link href="/title" class="nav-link" data-key="t-ticket_title">
                  {{ $t("ticket_title") }}
                </Link>
              </li>    
              <li class="nav-item" v-if="permissions.includes('view-support-ticket')">
                <Link href="/support-tickets" class="nav-link" data-key="t-ticket_title">
                  {{ $t("support_tickets") }}
                </Link>
              </li>        
            </ul>
          </div>
        </li>
        </div>        
      </div>
        
<!-- user management end -->
 <!-- configuration management -->
      <div  v-if = "activeMenu === 'Settings'" class="menu">
        <li class="menu-title">
          <span data-key="t-configuration"> {{ $t("configuration") }}</span>
        </li>
        <li class="nav-item" v-if="permissions.includes('manage-business-settings')">
          <a class="nav-link menu-link" href="#businesssetting" data-bs-toggle="collapse" role="button"
            aria-expanded="false" aria-controls="businesssetting">
            <i class="ri-settings-5-fill"></i>
            <span data-key="t-business-settings">{{ $t("business-settings") }}</span>
          </a>
          <div class="collapse menu-dropdown" id="businesssetting">
            <ul class="nav nav-sm flex-column">
              <li class="nav-item" v-if="permissions.includes('general-settings-view')">
                <Link href="/general-settings" class="nav-link" data-key="t-general-settings">
                  {{ $t("general-settings") }}
                </Link>
              </li>
              <li class="nav-item" v-if="permissions.includes('customization-settings-view')">
                <Link href="/customization-settings" class="nav-link" data-key="t-customization-settings">
                  {{$t("customization-settings")}}
                </Link>
              </li>
              <!-- <li class="nav-item" v-if="permissions.includes('peak-zone-settings-view')">
                <Link href="/peakzone-setting" class="nav-link" data-key="t-peakzone-setting">
                  {{$t("peakzone-setting")}}
                </Link>
              </li> -->
              <li class="nav-item" v-if="permissions.includes('transport-ride-settings-view')">
                <Link href="/transport-ride-settings" class="nav-link" data-key="t-transport-ride-settings">
                  {{ $t("transport-ride-settings") }}
                </Link>
              </li>
              <li class="nav-item" v-if="permissions.includes('bid-ride-settings-view')">
                <Link href="/bid-ride-settings" class="nav-link" data-key="t-bid-ride-settings">
                  {{ $t("bid-ride-settings") }}
                </Link>
              </li>
            </ul>
          </div>
        </li>
        <li class="nav-item" v-if="permissions.includes('manage-app-settings')">
          <a class="nav-link menu-link" href="#appsetting" data-bs-toggle="collapse" role="button"
            aria-expanded="false" aria-controls="appsetting">
            <i class="mdi mdi mdi-cellphone-cog"></i>
            <span data-key="t-app-settings">{{ $t("app-settings") }}</span>
          </a>
          <div class="collapse menu-dropdown" id="appsetting">
            <ul class="nav nav-sm flex-column">
              <li class="nav-item" v-if="permissions.includes('wallet-settings-view')">
                <Link href="/wallet-settings" class="nav-link" data-key="t-wallet-settings">
                  {{ $t("wallet-settings") }}
                </Link>
              </li>
              <li class="nav-item" v-if="permissions.includes('tip-settings-view')">
                <Link href="/tip-settings" class="nav-link" data-key="t-tip-settings">
                  {{ $t("tip-settings") }}
                </Link>
              </li>
              <li class="nav-item" v-if="permissions.includes('manage-country')">
                <Link href="/country" class="nav-link" data-key="t-country">
                  {{ $t("country") }}
                </Link>
              </li>
              <li class="nav-item" v-if="permissions.includes('onboarding-screen-settings-view')">
                <Link href="/onboarding-screen" class="nav-link" data-key="t-onboard-screens">
                  {{ $t("onboard-screens") }}
                </Link>
              </li>
            </ul>
          </div>
        </li>
        <li class="nav-item" v-if="permissions.includes('manage-third-party-settings')">
          <a class="nav-link menu-link" href="#thirdpartysetting" data-bs-toggle="collapse" role="button"
            aria-expanded="false" aria-controls="thirdpartysetting">
            <i class="mdi mdi mdi-cogs"></i>
            <span data-key="t-third-party-settings">{{ $t("third-party-settings") }}</span>
          </a>
          <div class="collapse menu-dropdown" id="thirdpartysetting">
            <ul class="nav nav-sm flex-column">
              <li class="nav-item" v-if="permissions.includes('payment-gateway-settings-view')">
                <Link href="/payment-gateway" class="nav-link" data-key="t-payment-gateway">
                  {{ $t("payment-gateway-settings") }}
                </Link>
              </li>
              <li class="nav-item" v-if="permissions.includes('sms-gateway-settings-view')">
                <Link href="/sms-gateway" class="nav-link" data-key="t-sms-gateway">
                  {{ $t("sms-gateway-settings") }}
                </Link>
              </li>
              <li class="nav-item" v-if="permissions.includes('firebase-settings-view')">
                <Link href="/firebase" class="nav-link" data-key="t-firebase">
                  {{ $t("firebase-settings") }}
                </Link>
              </li>
              <li class="nav-item" v-if="permissions.includes('map-settings-view')">
                <Link href="/map-setting" class="nav-link" data-key="t-map-settings">
                  {{ $t("map-settings") }}
                </Link>
              </li>
              <!-- <li class="nav-item">
                <Link href="" class="nav-link" data-key="t-translation">
                  {{ $t("translation") }}
                </Link>
              </li> -->
              <li class="nav-item" v-if="permissions.includes('mail-configuration-view')">
                <Link href="/mail-configuration" class="nav-link" data-key="t-mail-configuration">
                  {{ $t("mail-configuration") }}
                </Link>
              </li>
              <!-- <li class="nav-item" v-if="permissions.includes('map-apis-view')">
                <Link href="/map-apis" class="nav-link" data-key="t-map-apis">
                  {{ $t("map-apis") }}
                </Link>
              </li> -->
              <li class="nav-item" v-if="permissions.includes('recaptcha-view')">
                <Link href="/recaptcha" class="nav-link" data-key="t-recaptcha">
                  {{ $t("recaptcha") }}
                </Link>
              </li>
              <!-- <li class="nav-item" v-if="permissions.includes('invoice-configuration-view')">
                <Link href="/invoice-configuration" class="nav-link" data-key="t-invoice-configuration">
                  {{ $t("invoice-configuration") }}
                </Link>
              </li> -->
              <li class="nav-item" v-if="permissions.includes('notification-channel-view')">
                <Link href="/notification-channel" class="nav-link" data-key="t-notification-channel">
                  {{ $t("notification-channel") }}
                </Link>
              </li>
            </ul>
          </div>
        </li>
        <li class="nav-item" v-if="permissions.includes('cms-landing-website')">
          <a class="nav-link menu-link" href="#cms" data-bs-toggle="collapse" role="button"
            aria-expanded="false" aria-controls="cms">
            <i class="ri-macbook-line"></i>
            <span data-key="t-cms-landing-website">{{ $t("cms-landing-website") }}</span>
          </a>
          <div class="collapse menu-dropdown" id="cms">
            <ul class="nav nav-sm flex-column">
              <li class="nav-item" v-if="permissions.includes('header_footer')">
                <Link href="/landing-header" class="nav-link" data-key="t-header-footer">
                  {{ $t("header-footer") }}
                </Link>
              </li>
              <li class="nav-item" v-if="permissions.includes('landing_home')">
                <Link href="/landing-home" class="nav-link" data-key="t-landing-home">
                  {{ $t("home") }}
                </Link>
              </li>
              <li class="nav-item" v-if="permissions.includes('landing_aboutus')">
                <Link href="/landing-aboutus" class="nav-link" data-key="t-landing-aboutus">
                  {{ $t("aboutus") }}
                </Link>
              </li>
              <li class="nav-item" v-if="permissions.includes('landing_driver')">
                <Link href="/landing-driver" class="nav-link" data-key="t-landing-driver">
                  {{ $t("driver") }}
                </Link>
              </li>
              <li class="nav-item" v-if="permissions.includes('landing_user')">
                <Link href="/landing-user" class="nav-link" data-key="t-landing-user">
                  {{ $t("user") }}
                </Link>
              </li>
              <li class="nav-item" v-if="permissions.includes('landing_contact')">
                <Link href="/landing-contact" class="nav-link" data-key="t-landing-contact">
                  {{ $t("contact") }}
                </Link>
              </li>
              <li class="nav-item" v-if="permissions.includes('landing_quicklinks')">
                <Link href="/landing-quicklink" class="nav-link" data-key="t-landing-quicklinks">
                  {{ $t("quicklinks") }}
                </Link>
              </li>
            </ul>
          </div>
        </li>
      </div>
        
 <!-- Masters management -->
      <div   v-if ="activeMenu === 'Masters'" class="menu">
        <li class="menu-title">
          <span data-key="t-masters"> {{ $t("masters") }}</span>
        </li>
        <li class="nav-item" v-if="permissions.includes('masters')">
          <a class="nav-link menu-link" href="#Masters" data-bs-toggle="collapse" role="button"
            aria-expanded="false" aria-controls="Masters">
            <i class=" bx bx-file"></i>
            <span data-key="t-masters"> {{ $t("masters") }}</span>
          </a>
          <div class="collapse menu-dropdown" id="Masters">
            <ul class="nav nav-sm flex-column">
              <li class="nav-item" v-if="permissions.includes('languages')">
                <Link href="/languages" class="nav-link menu" data-key="t-language">
                  {{ $t("language") }}
                </Link>
              </li>
              <li class="nav-item" v-if="permissions.includes('roles')">
                <Link href="/roles" class="nav-link menu" data-key="t-roles">
                  {{ $t("roles") }}
                </Link>
              </li>
              <!-- <li class="nav-item" v-if="permissions.includes('banner_image')">
                <Link href="/banner-image" class="nav-link" data-key="t-banner-image">
                  {{ $t("banner-image") }}
                </Link>
              </li> -->

            </ul>
          </div>
        </li>
      </div>
      

        
    </ul>
  </template>
</BContainer></template>
  <style>
.ltr .navbar-menu .navbar-nav .nav-sm .nav-link:before {
    content: "";
    width: 8px;
    height: 8px;
    border-radius: 50%;
    position: absolute;
    left: 2px;
    top: 14.5px;
    opacity: 0.8;
}
</style>