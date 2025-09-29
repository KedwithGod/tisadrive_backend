<script>
import { Link } from '@inertiajs/vue3';
// import router from "@/router";
import simplebar from "simplebar-vue";
import { layoutComputed } from "@/state/helpers";

import NavBar from "@/Components/nav-bar.vue";
import Menu from "@/Components/menu.vue";
import RightBar from "@/Components/right-bar.vue";
import Footer from "@/Components/footer.vue";
localStorage.setItem('hoverd', false);

/**
 * Vertical layout
 */
export default {
  components: { NavBar, RightBar, Footer, simplebar, Menu, Link },
  // props: {
  //   activeMenu: {
  //     type: String,
  //     required: true
  //   },
  // },
  data() {
    return {
      isMenuCondensed: false,
      activeMenu:'',
      logo: window.logo,
      favicon: window.favicon,
      supportTicket:window.supportTicket
    };
  },
  computed: {
    ...layoutComputed,
    sidebarSize: {
      get() {
        return this.$store ? this.$store.state.layout.sidebarSize : {} || {};
      },
      set(type) {
        return this.changeSidebarSize({
          sidebarSize: type,
        });
      },
    },
  },
  created: () => {
    document.body.removeAttribute("data-layout", "horizontal");
    document.body.removeAttribute("data-topbar", "dark");
    document.body.removeAttribute("data-layout-size", "boxed");
  },
  methods: {
    initActiveMenu() {
      if (document.documentElement.getAttribute('data-sidebar-size') === 'sm-hover') {
        localStorage.setItem('hoverd', true);
        document.documentElement.setAttribute('data-sidebar-size', 'sm-hover-active');
      } else if (document.documentElement.getAttribute('data-sidebar-size') === 'sm-hover-active') {
        localStorage.setItem('hoverd', false);
        document.documentElement.setAttribute('data-sidebar-size', 'sm-hover');
      } else {
        document.documentElement.setAttribute('data-sidebar-size', 'sm-hover');
      }
    },
    toggleMenu() {
      document.body.classList.toggle("sidebar-enable");

      if (window.screen.width >= 992) {
        // eslint-disable-next-line no-unused-vars
        router.afterEach((routeTo, routeFrom) => {
          document.body.classList.remove("sidebar-enable");
          document.body.classList.remove("vertical-collpsed");
        });
        document.body.classList.toggle("vertical-collpsed");
      } else {
        // eslint-disable-next-line no-unused-vars
        router.afterEach((routeTo, routeFrom) => {
          document.body.classList.remove("sidebar-enable");
        });
        document.body.classList.remove("vertical-collpsed");
      }
      this.isMenuCondensed = !this.isMenuCondensed;
    },
    toggleRightSidebar() {
      document.body.classList.toggle("right-bar-enabled");
    },
    hideRightSidebar() {
      document.body.classList.remove("right-bar-enabled");
    },   

  },
  mounted() {
    if (localStorage.getItem('hoverd') == 'true') {
      document.documentElement.setAttribute('data-sidebar-size', 'sm-hover-active');
    }

    document.getElementById('overlay').addEventListener('click', () => {
      document.body.classList.remove('vertical-sidebar-enable');
    });

    

    const path = window.location.pathname;   

    if (path.startsWith('/general-settings') || path.startsWith('/wallet-settings') || path.startsWith('/tip-settings') 
    || path.startsWith ('/transport-ride-settings') || path.startsWith ('/bid-ride-settings')
    || path.startsWith('/payment-gateway') || path.startsWith('/sms-gateway')
    || path.startsWith('/firebase') || path.startsWith('/map-setting') || path.startsWith('/peakzone-setting')
    || path.startsWith('/landing-home')  || path.startsWith('/landing-driver') ||path.startsWith('/country') ||
    path.startsWith('/landing-user') || path.startsWith('/landing-contact') || path.startsWith('/landing-header') || path.startsWith('/onboarding-screen')
    || path.startsWith('/landing-home') || path.startsWith('/landing-header') || path.startsWith('/landing-quicklink') || path.startsWith('/onboarding-screen')
    || path.startsWith('/invoice-configuration') || path.startsWith('/mail-configuration') || path.startsWith('/map-apis') || path.startsWith('/recaptcha')
    || path.startsWith('/landing-aboutus') || path.startsWith('/notification-channel') || path.startsWith('/customization-settings'))
    {
      this.activeMenu = 'Settings';
    } 
    else if ((path.startsWith('/languages')) 
    || (path.startsWith('/roles'))  || (path.startsWith('/permissions')))
    {
      this.activeMenu = 'Masters';
    } 
    else if (path.startsWith('/manage-owners') 
     || path.startsWith('/owner-needed-documents') || path.startsWith('/report/owner-report') 
     || path.startsWith('/report/finance-report') || path.startsWith('/roles') || path.startsWith('/admins') 
     || path.startsWith('/users') || path.startsWith('/users/deleted-user') || path.startsWith('/report/user-report')
     || path.startsWith('/negative-balance-drivers') || path.startsWith('/withdrawal-request-drivers') || path.startsWith('/delete-request-drivers')
     || path.startsWith('/driver-needed-documents') || path.startsWith('/report/driver-report') || path.startsWith('/user-dashboard') || path.startsWith('/drivers-rating')
     || path.startsWith('/fleet-drivers')  || path.startsWith('/fleet-drivers/pending')  || path.startsWith('/fleet-needed-documents')
     || path.startsWith('/manage-fleet') || path.startsWith('/pending-drivers') || path.startsWith('/approved-drivers')  || path.startsWith('/fleet-drivers') 
     || path.startsWith('/driver-bank-info') || path.startsWith('/report/fleet-report')
     || path.startsWith('/report/driver-duty-report') || path.startsWith('/subscription')
     || path.startsWith('/referral-settings') || path.startsWith('/owner-dashboard') || path.startsWith('/withdrawal-request-owners') ||
        path.startsWith('/category') ||  path.startsWith('/title')||  path.startsWith('/support-tickets')
    ) 
    {
      this.activeMenu = 'Users';
    }
    else{
      this.activeMenu = 'Home';
    }
  }
};
</script>
  
<template>
  <div id="layout-wrapper">
    <NavBar />
    <div>
      <!-- ========== Left Sidebar Start ========== -->
      <!-- ========== App Menu ========== -->
      <div class="app-menu navbar-menu">
        <!-- LOGO -->
        <div class="navbar-brand-box">
          <!-- Dark Logo-->
          <Link href="/dashboard" class="logo logo-dark">
            <span class="logo-sm">
              <img :src="favicon" alt="" height="22" />
            </span>
            <span class="logo-lg">
              <img :src="logo" alt="" height="30" />
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
          <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
            id="vertical-hover" @click="initActiveMenu" style="position: relative;z-index: 100;">
            <i class="ri-record-circle-line"></i>
          </button>
        </div>

        <simplebar id="scrollbar" class="h-100" ref="scrollbar">
          <Menu :activeMenu = "activeMenu"  :supportTicket="supportTicket"></Menu>
        </simplebar>
        <div class="sidebar-background"></div>
      </div>
      <!-- Left Sidebar End -->
      <!-- Vertical Overlay-->
      <div class="vertical-overlay" id="overlay"></div>
    </div>
    <!-- ============================================================== -->
    <!-- Start Page Content here -->
    <!-- ============================================================== -->

    <div class="main-content">
      <div class="page-content">
        <!-- Start Content-->
        <b-container fluid>
          <slot />
        </b-container>
      </div>
      <Footer />
    </div>
    <RightBar />
  </div>
</template>