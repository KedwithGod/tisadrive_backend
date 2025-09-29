<script setup>
import { layoutMethods } from "@/state/helpers";
import { Link, router } from '@inertiajs/vue3';
import simplebar from "simplebar-vue";
import { ref, onMounted } from 'vue';
import i18n from '../i18n';
import { initI18n, loadLocaleMessages } from '../i18n';
import { useSharedState } from '@/composables/useSharedState';
import store from '/resources/js/state/store.js';
import axios from 'axios';
import { formatDistanceToNowStrict } from 'date-fns'; // Importing the correct function from date-fns
import Swal from "sweetalert2";

// State and shared methods from composable
const selectedLanguageCode = ref(i18n.global.locale);
const {
  languages,
  fetchData,
  locations,
  fetchLocations,
  selectedLocation,
  setServiceLocation,
  notifications,
  chats,
  unreadChat,
  unreadNotification,
  fetchFirebaseSettings,
  handleNotificationClick,
  handleMarkAllAsRead
} = useSharedState();


// Format the timestamp to show time difference from now
const formatTimestamp = (timestamp) => {
  const date = new Date(timestamp);

  // Check if the timestamp is a valid date
  if (isNaN(date.getTime())) {
    console.error("Invalid timestamp:", timestamp);
    return "Invalid date"; // Return a fallback value if the date is invalid
  }

  // Use date-fns to format the valid timestamp
  return formatDistanceToNowStrict(date, { addSuffix: true });
};


onMounted(async () => {
  store.dispatch('fetchPermissions');

  await fetchData();
  await fetchLocations();

  // Initialize Firebase notifications
  await fetchFirebaseSettings();
 
  const currentLocale = localStorage.getItem('locale') || defaultLocale;
  selectedLanguageCode.value = currentLocale;
  const selectedLanguage = languages.value.find(lang => lang.code === currentLocale);

  if (selectedLanguage) {
    const direction = selectedLanguage.direction;
    const body = document.body;

    if (direction === 'rtl') {
      localStorage.setItem('toggleDirection', true);
      body.classList.add('rtl');
      body.classList.remove('ltr');
    } else {
      localStorage.setItem('toggleDirection', false);
      body.classList.add('ltr');
      body.classList.remove('rtl');
    }
  }
});



const setLanguage = async (locale) => {
  await loadLocaleMessages(locale);
  i18n.global.locale = locale;
  selectedLanguageCode.value = locale;
  localStorage.setItem('locale', locale);

  const selectedLanguage = languages.value.find(lang => lang.code === locale);

  if (selectedLanguage) {
    const direction = selectedLanguage.direction;
    const body = document.body;

    if (direction === 'rtl') {      
      window.location.reload();
      localStorage.setItem('toggleDirection', true);
      body.classList.add('rtl');
      body.classList.remove('ltr');
    } else {
      window.location.reload();
      localStorage.setItem('toggleDirection', false);
      body.classList.add('ltr');
      body.classList.remove('rtl');
    }
  }
};

const handleChatClick = (chat_id) => {
  router.get('/chat?conversation_id='+ chat_id);
}

// const logout = () => {
//   router.post(route('logout'));
// };
const logout = () => {
  Swal.fire({
    title: i18n.global.t("are_you_sure"), // Translatable title
    text: i18n.global.t("you_want_to_be_logout"), // Translatable message
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#34c38f",
    cancelButtonColor: "#f46a6a",
    confirmButtonText: i18n.global.t("yes"), // "Yes, Logout"
    cancelButtonText: i18n.global.t("cancel"), // "Cancel"
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(route("logout"));
    }
  });
};
</script>


<script>
import { ref, onMounted } from 'vue';
import simplebar from "simplebar-vue";
import i18n from "../i18n";
import { Link } from '@inertiajs/vue3';
import { mapGetters } from 'vuex';

export default {
  data() {
    return {
      lan: i18n.global.locale,
      text: null,
      flag: null,
      value: null,
      myVar: 1, 
      profilePhotoUrl: this.$page.props.auth.user.profile_picture,       
    };
  },
  components: {
    simplebar,
    Link,
  },
  methods: {
    ...layoutMethods,
    
    toggleHamburgerMenu() {
      const windowSize = document.documentElement.clientWidth;
      const layoutType = document.documentElement.getAttribute("data-layout");

      document.documentElement.setAttribute("data-sidebar-visibility", "show");
      const visibilityType = document.documentElement.getAttribute("data-sidebar-visibility");

      if (windowSize > 767) document.querySelector(".hamburger-icon").classList.toggle("open");

      if (layoutType === "horizontal") {
        document.body.classList.toggle("menu");
      }

      if (visibilityType === "show" && (layoutType === "vertical" || layoutType === "semibox")) {
        if (windowSize < 1025 && windowSize > 767) {
          document.body.classList.remove("vertical-sidebar-enable");
          document.documentElement.setAttribute("data-sidebar-size", document.documentElement.getAttribute("data-sidebar-size") === "sm" ? "" : "sm");
        } else if (windowSize > 1025) {
          document.body.classList.remove("vertical-sidebar-enable");
          document.documentElement.setAttribute("data-sidebar-size", document.documentElement.getAttribute("data-sidebar-size") === "lg" ? "sm" : "lg");
        } else if (windowSize <= 767) {
          document.body.classList.add("vertical-sidebar-enable");
          document.documentElement.setAttribute("data-sidebar-size", "lg");
        }
      }

      if (layoutType === "twocolumn") {
        document.body.classList.toggle("twocolumn-panel");
      }
    },
    
    toggleMenu() {
      this.$parent.toggleMenu();
    },
    
    toggleRightSidebar() {
      this.$parent.toggleRightSidebar();
    },
    
    initFullScreen() {
      document.body.classList.toggle("fullscreen-enable");
      if (!document.fullscreenElement && !document.mozFullScreenElement && !document.webkitFullscreenElement) {
        if (document.documentElement.requestFullscreen) {
          document.documentElement.requestFullscreen();
        } else if (document.documentElement.mozRequestFullScreen) {
          document.documentElement.mozRequestFullScreen();
        } else if (document.documentElement.webkitRequestFullscreen) {
          document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
        }
      } else {
        if (document.cancelFullScreen) {
          document.cancelFullScreen();
        } else if (document.mozCancelFullScreen) {
          document.mozCancelFullScreen();
        } else if (document.webkitCancelFullScreen) {
          document.webkitCancelFullScreen();
        }
      }
    },
    
    toggleDarkMode() {
      const theme = document.documentElement.getAttribute("data-bs-theme") === "dark" ? "light" : "dark";
      const sidebarColor = document.documentElement.getAttribute("data-sidebar") === "dark" ? "light" : "dark";
      document.documentElement.setAttribute("data-bs-theme", theme);
      document.documentElement.setAttribute("data-sidebar", sidebarColor);     

      localStorage.setItem('toggleDarkMode', theme === 'dark');  

      this.changeMode({ mode: theme });
      this.changeSidebarColor({ sidebarColor: sidebarColor });
    },
    savedToggleTheme() {
      const isDarkMode = localStorage.getItem('toggleDarkMode') === 'true'; // Retrieve saved preference
      const theme = isDarkMode ? 'dark' : 'light';
      const sidebarColor = isDarkMode ? 'dark' : 'light';

      document.documentElement.setAttribute("data-bs-theme", theme);
      document.documentElement.setAttribute("data-sidebar", sidebarColor);      
      this.changeMode({ mode: theme });
      this.changeSidebarColor({ sidebarColor: sidebarColor });
    },
    
    removeItem(cartItem) {
      this.cartItems = this.cartItems.filter(item => item.id !== cartItem.id);
      this.$emit("cart-item-price", this.cartItems.length);
    },
  },
  computed: {
    ...mapGetters(['permissions']),
    
    calculateTotalPrice() {
      return this.cartItems.reduce((total, item) => total + parseFloat(item.itemPrice), 0).toFixed(2);
    },
  },
  mounted() {
    this.savedToggleTheme();
    this.flag = this.$i18n.locale;
    document.addEventListener("scroll", function () {
      const pageTopbar = document.getElementById("page-topbar");
      if (pageTopbar) {
        document.body.scrollTop >= 50 || document.documentElement.scrollTop >= 50
          ? pageTopbar.classList.add("topbar-shadow")
          : pageTopbar.classList.remove("topbar-shadow");
      }
    });
    if (document.getElementById("topnav-hamburger-icon")) {
      document.getElementById("topnav-hamburger-icon").addEventListener("click", this.toggleHamburgerMenu);
    }
  },
};
</script>



<template>
    <header id="page-topbar">
        <div class="layout-width">
            <div class="navbar-header">
                <div class="d-flex">
                    <!-- LOGO -->
                    <div class="navbar-brand-box horizontal-logo">
                        <Link href="/" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="@assets/images/logo-sm.png" alt="" height="50" />
                        </span>
                        <span class="logo-lg">
                            <img src="@assets/images/logo-dark.png" alt="" height="50" />
                        </span>
                        </Link>

                        <Link href="/" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="@assets/images/logo-sm.png" alt="" height="50" />
                        </span>
                        <span class="logo-lg">
                            <img src="@assets/images/logo-light.png" alt="" height="50" />
                        </span>
                        </Link>
                    </div>

                    <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger"
                        id="topnav-hamburger-icon">
                        <span class="hamburger-icon">
                            <span></span>
                            <span></span>
                            <span></span>
                        </span>
                    </button>
                </div>
                
                <div class="d-flex align-items-center">  
                    <div class="p-0 me-0 me-lg-4">
                      <div class="col-12 col-lg-12">
                         <div class="mb-2">
                        </div>
                      </div>
                    </div>
                <div>
                    <nav class="navbar navbar-expand">
                        <div class="top-navbar ">
                            <div class="navbar-nav nav">
                                <ul class="navbar-nav">
                                    <li class="nav-item dropdown" v-if="permissions.includes('access-home')">
                                        <Link class="nav-link nav-menu" href="/dashboard" id="dropdownMenuButton" role="button"  aria-expanded="false"
                                            :class="{ 'active': ['/dashboard', '/promo-code','/set-prices', '/drivers-levelup','/incentives',
                                            '/rental-package-types','/dispatch/delivery-package-ride','/dispatch/delivery-ride','/airport',
                                            '/goods-type','/dispatch/delivery-ride','/service-locations','/zones' ,'/vehicle_type','/map/heat_map',
                                            '/map/gods_eye','/user-complaint/general-complaint','/user-complaint/request-complaint','/general-complaint/general-complaint',
                                            '/owner-complaint/request-complaint','/owner-complaint/general-complaint','/dispatch/taxi-package-ride',
                                            '/dispatch/delivery-package-ride','/rides-request', '/scheduled-rides','/cancellation-rides', '/out-station-rides',
                                            '/delivery-rides-request', '/delivery-scheduled-rides', '/delivery-cancellation-rides','/dispatch','/sos','/faq',
                                            '/push-notifications','/cancellation','/mail-template','/banner-image','/complaint-title',
                                            '/driver-complaint/general-complaint','/driver-complaint/request-complaint','/ongoing-rides'].some(path => $page.url.startsWith(path))  }">
                                            <i class=" ri-home-4-line"></i>
                                            <span class="ms-2 dropdown-toggle">{{ $t("home") }}</span>
                                        </Link> 
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton" v-if="permissions.includes('service-location')">
                                            <!-- <li class="dropdown-submenu mb-2" >
                                                <Link class="nav-link" href="/dispatch"
                                                    :class="{ 'active': $page.url.startsWith('/dispatch') }">
                                                    {{ $t("dispatch-ride") }}
                                                </Link>
                                            </li> -->
                                            <li class="dropdown-submenu mb-2" v-if="permissions.includes('service-location')">
                                                <Link class="nav-link" href="/service-locations" 
                                                    :class="{ 'active': $page.url.startsWith('/service-locations') }">
                                                        {{ $t("service_location") }}
                                                </Link>
                                            </li>
                                            <li class="dropdown-submenu mb-2" v-if="permissions.includes('trip-request-view')">
                                                <Link class="nav-link" href="/rides-request" :class="{ 'active': ['/rides-request','/scheduled-rides',
                                                '/cancellation-rides','/out-station-rides',].some(path => $page.url.startsWith(path)) }">
                                                        {{ $t("trip-requests") }}
                                                </Link>
                                            </li> 
                                        </ul>
                                    </li>

                                    <li class="nav-item dropdown"  v-if="permissions.includes('access-user-nav-list')">
                                        <Link class="nav-link nav-menu" href="/users" id="dropdownMenuButton" role="button" aria-expanded="false"  
                                                :class="{ 'active': ['/manage-owners','/fleet-drivers/pending','/report/driver-duty-report',
                                                'owner-needed-documents', '/report/owner-report', '/report/finance-report', '/admins',
                                                '/users', '/users/deleted-user', '/report/user-report', '/negative-balance-drivers', '/withdrawal-request-drivers',
                                                '/delete-request-drivers', '/driver-needed-documents', '/report/driver-report','profile-edit',
                                                '/pending-drivers', '/approved-drivers','/owner-needed-documents','/driver-bank-info','/report/fleet-report'
                                                ,'/fleet-drivers','/referral-settings','/subscription','/drivers-rating','/manage-fleet','/country',
                                                '/fleet-needed-documents','/owner-dashboard','/withdrawal-request-owners','/category','/title','/support-tickets'].some(path => $page.url.startsWith(path)) }"
                                                @click="('/approved-drivers')">
                                                    
                                            <i class=" ri-user-3-line"></i>
                                            <span class="ms-2 dropdown-toggle">{{ $t("users") }}</span>
                                        </Link>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <li class="dropdown-submenu mb-2" v-if="permissions.includes('admin-management')">
                                                <Link class="nav-link" href="/admins"
                                                    :class="{ 'active': [ '/admins',].some(path => $page.url.startsWith(path)) }">
                                                    {{ $t("admin-management") }}
                                                </Link> 
                                            </li> 
                                           <li class="dropdown-submenu mb-2"  v-if="permissions.includes('owner-management')" >
                                                <Link class="nav-link" href="/owner-dashboard"
                                                    :class="{ 'active': [ '/manage-owners','/withdrawal-request-owners','/fleet-drivers/pending','/fleet-drivers',
                                                      '/fleet-needed-documents','/manage-fleet','/owner-needed-documents'
                                                    ].some(path => $page.url.startsWith(path)) }">
                                                    {{ $t("owner-management") }}
                                                </Link> 
                                            </li> 
                                            <li class="dropdown-submenu mb-2"  v-if="permissions.includes('drivers-management')" >
                                                <Link class="nav-link" href="/approved-drivers"
                                                    :class="{ 'active': ['/negative-balance-drivers', '/withdrawal-request-drivers',
                                                '/delete-request-drivers', '/driver-needed-documents',
                                                '/pending-drivers', '/approved-drivers','/driver-bank-info','/drivers-rating'].some(path => $page.url.startsWith(path)) }">
                                                    {{ $t("driver-management") }}
                                                </Link> 
                                            </li> 
                                        </ul>
                                    </li>

                                    <li class="nav-item dropdown"  v-if="permissions.includes('access-settings-nav-list')">
                                        <Link class="nav-link nav-menu" href="/general-settings" id="dropdownMenuButton" aria-expanded="false" 
                                            :class="{ 'active': ['/general-settings','/commission-settings','/transport-ride-settings','/bid-ride-settings',
                                            '/wallet-settings','/payment-gateway','/sms-gateway','/tip-settings',
                                            '/firebase','/map-setting','/landing-header','/landing-home','/landing-driver','/landing-user','/landing-contact','/landing-quicklink','/onboarding-screen','/invoice-configuration',
                                            '/mail-configuration','/map-apis','/recaptcha','/notification-channel','/customization-settings','/landing-aboutus'].some(path => $page.url.startsWith(path)) }">
                                                <i class="ri-settings-5-fill"></i>
                                                <span class="ms-2 dropdown-toggle">{{ $t("settings") }}</span>
                                        </Link>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">                                                    
                                            <li class="dropdown-submenu  mb-2" v-if="permissions.includes('manage-business-settings')">
                                                <Link class="nav-link" href="/general-settings" :class="{ 'active': ['/general-settings','/commission-settings',
                                                '/transport-ride-settings','/customization-settings','/bid-ride-settings'].some(path => $page.url.startsWith(path)) }">
                                                    {{ $t("business-settings") }}
                                                </Link>
                                            </li> 
                                            <li class="dropdown-submenu  mb-2" v-if="permissions.includes('wallet-settings-view')">
                                                <Link class="nav-link" href="/wallet-settings" :class="{ 'active': ['/wallet-settings','/onboarding-screen'].some(path => $page.url.startsWith(path)) }">
                                                    {{ $t("app-settings") }}
                                                </Link>
                                            </li> 
                                            <li class=" dropdown-submenu mb-2" v-if="permissions.includes('payment-gateway-settings-view')">
                                                <Link class="nav-link" href="/payment-gateway" :class="{ 'active': ['/payment-gateway','/sms-gateway',
                                                '/firebase','/map-setting','/invoice-configuration',
                                                '/mail-configuration','/recaptcha'].some(path => $page.url.startsWith(path)) }">
                                                   {{ $t("third-party-settings") }}
                                                </Link>
                                            </li> 
                                        </ul>
                                    </li>
                                    
                                    <li class="nav-item dropdown"  v-if="permissions.includes('access-master-nav-list')">
                                        <Link class="nav-link nav-menu" href="/languages" id="dropdownMenuButton" role="button" aria-expanded="false" 
                                            :class="{ 'active': ['/languages','/roles','/roles1','/countries','/permissions'].some(path => $page.url.startsWith(path)) }">
                                                <i class="bx bx-file master-icon"></i>
                                            <span class="ms-2 dropdown-toggle">{{ $t("masters") }}</span>
                                        </Link>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">                                            
                                            <li class="nav-item mb-2" v-if="permissions.includes('languages')">
                                                <Link class="nav-link" href="/languages" :class="{ 'active': $page.url.startsWith('/languages')}">
                                                  {{ $t("language") }}
                                                </Link>
                                            </li>
                                            <li class="nav-item mb-2" v-if="permissions.includes('roles')">
                                                <Link class="nav-link" href="/roles" :class="{ 'active': $page.url.startsWith('/roles')}">
                                                  {{ $t("roles") }}
                                                </Link>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>   
                        </div>
                    </nav> 
                </div>
              <!-- <BDropdown
                class="dropdown"
                variant="ghost-secondary"
                dropstart
                :offset="{ alignmentAxis: 55, crossAxis: 15, mainAxis: -50 }"
                toggle-class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle arrow-none"
                menu-class="dropdown-menu-end"
              >
                <template #button-content>
                  <div class="bg-primary-subtle" style="height: 38px; width: 38px; border-radius: 45px;">
                    <i class="ri-map-pin-line fs-22 text-primary"></i>
                  </div>
                </template>

                <BLink
                  href="javascript:void(0);"
                  class="dropdown-item notify-item py-2"
                  v-for="(location, key) in locations"
                  :key="key"
                  :data-location="location.value"
                  :title="location.label"
                  @click="setServiceLocation(location.value)"
                  :class="{ 'bg-primary-subtle text-dark': selectedLocation === location.value }"
                >
                  <span class="align-middle">{{ location.label }}</span>
                  <i v-if="selectedLocation === location.value"
                    class="bx bx-check text-primary float-end fs-22"></i>
                </BLink>
              </BDropdown> -->

                    <BDropdown class="dropdown" variant="ghost-secondary" dropstart
                        :offset="{ alignmentAxis: 55, crossAxis: 15, mainAxis: -50 }"
                        toggle-class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle arrow-none"
                        menu-class="dropdown-menu-end">
                        <template #button-content > <div class="bg-success-subtle " style="height: 38px;width: 38px;border-radius: 45px;"><i class="ri-translate fs-22 text-success"></i> </div>
                        </template>
                        <BLink href="javascript:void(0);" class="dropdown-item notify-item language py-2"
                            v-for="(entry, key) in languages" :data-lang="entry.language" :title="entry.label"
                            @click="setLanguage(entry.code)" :key="key" :class="{ 'bg-success-subtle': selectedLanguageCode === entry.code}">
                            <img :src="entry.flag" alt="" class="me-2 rounded" height="18">
                            <span class="align-middle">{{ entry.label }}</span>
                            <!-- Add the checkmark if this language is selected -->
                            <i v-if="selectedLanguageCode === entry.code"
                            class="bx bx-check text-success float-end fs-22"></i>
                        </BLink>
                    </BDropdown>
                   <div class="ms-1 header-item d-none d-sm-flex">
                        <BButton type="button" variant="ghost-secondary" class="btn-icon btn-topbar rounded-circle"
                            data-toggle="fullscreen" @click="initFullScreen">
                            <i class="bx bx-fullscreen fs-22"></i>
                        </BButton>
                    </div>

                    <div class="ms-1 header-item d-none d-sm-flex">
                        <BButton type="button" variant="ghost-secondary"
                            class="btn-icon btn-topbar rounded-circle light-dark-mode" @click="toggleDarkMode">
                            <i class="bx bx-moon fs-22"></i>
                        </BButton>
                    </div>
                      <!-- Notification dropdown template -->
                      <BDropdown variant="ghost-dark" dropstart class="ms-1 dropdown"
                        :offset="{ alignmentAxis: 57, crossAxis: 0, mainAxis: -42 }"
                        toggle-class="btn-icon btn-topbar rounded-circle arrow-none" id="page-header-notifications-dropdown"
                        menu-class="dropdown-menu-lg dropdown-menu-end p-0" auto-close="outside" v-if="permissions.includes('access-notifications')">
                        <template #button-content>
                          <i class='bx bx-bell fs-22'></i>
                          <span v-if="(unreadNotification + unreadChat) >0"  class="position-absolute topbar-badge fs-10 translate-middle badge rounded-pill bg-danger">
                            <span class="notification-badge">{{ unreadNotification + unreadChat }}</span>
                            <span class="visually-hidden">{{$t("unread_messages")}}</span>
                          </span>
                        </template>

                          <div class="dropdown-head bg-primary bg-pattern rounded-top dropdown-menu-lg">
                            <div class="p-3">
                              <BRow class="align-items-center">
                                <BCol>
                                  <h6 class="m-0 fs-16 fw-semibold text-white">
                                    {{$t("notifications")}}
                                  </h6>
                                </BCol>
                                <BCol cols="auto" class="dropdown-tabs">
                                  <BBadge variant="light-subtle" class="bg-light-subtle text-body fs-13">
                                    {{ unreadNotification + unreadChat }} {{$t("new")}}
                                  </BBadge>
                                </BCol>
                              </BRow>
                            </div>
                          </div>

                        <BTabs nav-class="dropdown-tabs nav-tab-custom bg-primary px-2 pt-2">
                          <BTab title="Notification" class="tab-pane fade py-2 ps-2" id="notifications-tab" role="tabpanel" aria-labelledby="notifications-tab">
                            <simplebar data-simplebar style="max-height: 300px" class="pe-2">
                              <div v-for="notification in notifications" :key="notification.id" class="text-reset notification-item d-block dropdown-item">
                                <div class="d-flex">
                                  <img src="/assets/images/Male_default_image.png" class="me-3 rounded-circle avatar-xs" alt="user-pic" />
                                  <div class="flex-grow-1">
                                    <BLink href="#" class="stretched-link" @click.prevent="handleNotificationClick(notification.id)">
                                      <h6 class="mt-0 mb-1 fs-13 fw-semibold">
                                        {{ notification.title }}
                                      </h6>
                                    </BLink>
                                    <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                      <span><i class="mdi mdi-clock-outline"></i> {{ formatTimestamp(notification.updated_at) }}</span>
                                    </p>
                                  </div>
                                </div>
                              </div>

                                <div v-if="notifications.length === 0" class="p-3 text-center text-muted">
                                  {{$t("no_new_notification")}}
                                </div>

                                <div class="my-3 text-center" v-if="unreadNotification > 0">
                                  <BButton type="button" variant="soft-success" @click="handleMarkAllAsRead('notification')">
                                    {{$t("mark_all_as_read")}}
                                    <i class="ri-arrow-right-line align-middle"></i>
                                  </BButton>
                                </div>
                              </simplebar>
                            </BTab>
                          <BTab title="Chat" class="tab-pane fade py-2 ps-2" id="chats-tab" role="tabpanel" aria-labelledby="chats-tab">
                            <simplebar data-simplebar style="max-height: 300px" class="pe-2">
                              <div v-for="chat in chats" :key="chat.id" class="text-reset notification-item d-block dropdown-item">
                                <div class="d-flex align-items-center">
                                  <img :src="chat.profile_picture" class="me-3 rounded-circle avatar-xs" alt="user-pic" />
                                  <div class="flex-grow-1 overflow-x-hidden">
                                    <BLink href="#" class="stretched-link" @click.prevent="handleChatClick(chat.id)">
                                      <h6 class="mt-0 mb-1 fs-13 fw-semibold">
                                        {{ chat.subject }} 
                                      </h6>
                                    </BLink>
                                    <div class="mb-0 fs-11 text-truncate fw-medium text-uppercase text-muted">
                                      <span>
                                      {{ chat.last_message }}
                                      </span>
                                    </div>
                                  </div>
                                  <div class="d-flex align-items-center col-lg-6">
                                    <div class="row">
                                      <span class="col-lg-12">
                                        <i class="mdi mdi-clock-outline"></i>
                                      {{ chat.last_seen }}
                                      </span>
                                    </div>
                                    <div class="row">
                                      <div class="d-flex justify-content-end">
                                      <span v-if="chat.unread > 0" 
                                      class="badge border border-light rounded-circle bg-success p-1">
                                      {{ chat.unread }}</span>
                                    </div>
                                  </div>
                                  </div>
                                </div>
                              </div>

                                <div v-if="chats.length === 0" class="p-3 text-center text-muted">
                                  {{$t("no_new_chat")}}
                                </div>

                                <div class="my-3 text-center" v-if="unreadChat > 0">
                                  <BButton type="button" variant="soft-success" @click="handleMarkAllAsRead('chat')">
                                    {{$t("mark_all_as_read")}}
                                    <i class="ri-arrow-right-line align-middle"></i>
                                  </BButton>
                                </div>
                              </simplebar>
                            </BTab>
                          </BTabs>
                        </BDropdown>
                        <!-- notification end -->

                    <BDropdown variant="link" class="ms-sm-3 header-item topbar-user"
                        toggle-class="rounded-circle arrow-none" menu-class="dropdown-menu-end"
                        :offset="{ alignmentAxis: -14, crossAxis: 0, mainAxis: 0 }">
                        <template #button-content>
                            <span class="d-flex align-items-center">
                                <img v-if="$page.props.jetstream.managesProfilePhotos"
                                    class="rounded-circle header-profile-user"
                                    :src="profilePhotoUrl || '/default-profile.jpeg'" :alt="$page.props.auth.user.name">
                                <span class="text-start ms-xl-2">
                                    <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{
                                        $page.props.auth.user.name }}</span>
                                    <!-- <span class="d-none d-xl-block ms-1 fs-12 user-name-sub-text">Founder</span> -->
                                </span>
                            </span>
                        </template>
                        <h6 class="dropdown-header">{{$t("welcome")}} {{ $page.props.auth.user.name }}!</h6>
                        <Link class="dropdown-item" href="/profile-edit"><i
                            class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i>
                        <span class="align-middle">{{$t("profile")}}</span>
                        </Link>
                        <!-- Authentication -->
                        <form method="POST" @submit.prevent="logout" class="dropdown-item">
                            <BButton variant="none" type="submit" class="btn p-0"><i
                                    class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> {{$t("logout")}}</BButton>
                        </form>
                    </BDropdown>
                </div>
            </div>
        </div>
    </header>
</template>


<style>

div.nav li.dropdown:hover > ul.dropdown-menu 
{
    display: block;    
}
 .top-navbar .nav-item .active
{
  font-weight: 600;
  color: var(--side_menu);
}
.text-truncate {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

@media screen and (max-width: 769px)  and (min-width: 426px)
{ 
    .top-navbar span {
        display: none
    }
    .top-navbar i {
        font-size: 20px;
    }
    .top-navbar{
    margin-right:0px;
}
 .master-icon{
      line-height: 1.5 !important;
    }
/* .top-navbar .nav-item .active
 {
  font-weight: 600;
  color: #405189;
  border-bottom:1px;
  border-style: solid;
  border-bottom-color: #405189;
  border-bottom-width: 3px;
} */
.rtl .form-check-input:checked {
    background-color: #405189;
    border-color: #405189;
}
}

@media screen and (max-width: 426px)  and (min-width: 320px)
{
    .top-navbar span{
        display: none;
    }    
    .top-navbar i {
        font-size: 19px;
    }
    .master-icon{
      line-height: 1.5 !important;
    }
}

</style>