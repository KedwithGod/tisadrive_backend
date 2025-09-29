<script setup>
import { layoutMethods } from "@/state/helpers";
import { Link, router } from '@inertiajs/vue3';
import simplebar from "simplebar-vue";
import { ref, onMounted } from 'vue';
import i18n from '../i18n';
import {loadLocaleMessages } from '../i18n';
import { useSharedState } from '@/composables/useSharedState';
import store from '/resources/js/state/store.js';
import Swal from "sweetalert2";

// State and shared methods from composable
const selectedLanguageCode = ref(i18n.global.locale);
const {
  languages,
  fetchData,
  fetchLocations,
  fetchFirebaseSettings,
} = useSharedState();


// Format the timestamp to show time difference from now

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
      logo: window.logo,
      favicon: window.favicon     
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
            <div class=" d-flex align-items-center  navbar-header">
              <div class="navbar-brand-box horizontal-logo">
          <!-- Dark Logo-->
          <Link href="/dashboard" class="logo logo-dark">
            <span class="logo-sm">
              <img :src="favicon" alt="" height="50" />
            </span>
            <span class="logo-lg">
              <img :src="logo" alt="" height="25" />
            </span>
          </Link>
          <!-- Light Logo-->
          <Link href="/dashboard" class="logo logo-light">
            <span class="logo-sm">
              <img :src="favicon" alt="" height="50" />
            </span>
            <span class="logo-lg">
              <!-- <img src="@assets/images/logo-light.png" alt="" height="30" /> -->
               <img :src="logo" alt="logo" height="30" />

            </span>
          </Link>
        </div>
              <!-- <div class="col-lg-8"> -->
                <div class="d-flex  align-items-center ms-auto me-2">
                    <nav class="navbar navbar-expand">
                        <div class="top-navbar ">
                            <div class="navbar-nav nav">
                                <ul class="navbar-nav">


                                  <li class="nav-item dropdown">
                                        <Link class="nav-link nav-menu" href="/dispatcher/bookride" role="button" v-if="permissions.includes('dispatcher-ride')"
                                            :class="{ 'active': $page.url == '/dispatcher/bookride' }">
                                                <i class="ri-settings-5-fill"></i>
                                                <span class="ms-2">{{ $t("dispatch-ride") }}</span>
                                        </Link>
                                    </li>

                                    <li class="nav-item dropdown">
                                        <Link class="nav-link nav-menu" href="/dispatcher/godeye" role="button" v-if="permissions.includes('dispatcher-drivers')"
                                                :class="{ 'active': $page.url == '/dispatcher/godeye'}">
                                                    
                                            <i class=" ri-user-3-line"></i>
                                            <span class="ms-2">{{ $t("drivers") }}</span>
                                        </Link>
                                    </li>


                                    <li class="nav-item dropdown">
                                        <Link class="nav-link nav-menu" href="/dispatcher/rides_request" role="button" v-if="permissions.includes('dispatcher-ride-request')"
                                            :class="{ 'active': $page.url == '/dispatcher/rides_request' }">
                                                <i class="ri-taxi-fill"></i>
                                                <span class="ms-2">{{ $t("trip-requests") }}</span>
                                        </Link>
                                    </li>

                                    <li class="nav-item dropdown">
                                        <Link class="nav-link nav-menu" href="/dispatcher/ongoing_request" role="button" v-if="permissions.includes('dispatcher-ongoing-request')"
                                            :class="{ 'active': $page.url == '/dispatcher/ongoing_request' }">
                                                <i class="mdi mdi-car-hatchback"></i>
                                                <span class="ms-2">{{ $t("ongoing-rides") }}</span>
                                        </Link>
                                    </li>

                                    <!-- <li class="nav-item dropdown">
                                        <Link class="nav-link nav-menu" href="/dispatcher"  role="button" v-if="permissions.includes('dispatcher-dashboard')"
                                            :class="{ 'active': $page.url == '/dispatcher'}">
                                            <i class=" ri-home-4-line"></i>
                                            <span class="ms-2">{{ $t("dashboard") }}</span>
                                        </Link> 
                                    </li> -->
                                    
                                </ul>
                            </div>   
                        </div>
                    </nav> 
                </div>
              <!-- </div> -->
              <!-- <div class="col-lg-4"> -->
                <div class="d-flex  align-items-center">
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
            
        <!-- </div> -->
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
@media screen and (max-width: 1024px)  and (min-width: 769px)
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
/* @media (max-width: 767.98px) {
    .logo span.logo-sm {
        display: inline-block;
    }
    .navbar-brand-box {
        display: block;
    }
} */

</style>