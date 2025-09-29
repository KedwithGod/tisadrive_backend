<script>
import { Link } from '@inertiajs/vue3';
import NavBar from "@/Components/nav-bar.vue";
import RightBar from "@/Components/right-bar.vue";
import Footer from "@/Components/footer.vue";

export default {
  mounted() {
    this.initActiveMenu();
  },
  methods: {
    initActiveMenu(ele) {
      setTimeout(() => {
        var currentPath = window.location.pathname;
        if (document.querySelector("#navbar-nav")) {
          let a = document.querySelector("#navbar-nav").querySelector('[href="' + currentPath + '"]');

          if (a) {
            a.classList.add("active");
            let parentCollapseDiv = a.closest(".collapse.menu-dropdown");
            if (parentCollapseDiv) {
              parentCollapseDiv.classList.add("show");
              parentCollapseDiv.parentElement.children[0].classList.add("active");
              parentCollapseDiv.parentElement.children[0].setAttribute("aria-expanded", "true");
              if (parentCollapseDiv.parentElement.closest(".collapse.menu-dropdown")) {
                parentCollapseDiv.parentElement.closest(".collapse").classList.add("show");
                if (parentCollapseDiv.parentElement.closest(".collapse").previousElementSibling)
                  parentCollapseDiv.parentElement.closest(".collapse").previousElementSibling.classList.add("active");
              }
            }
          }
        }
      }, 1000);
    },
  },
  components: {
    NavBar,
    RightBar,
    Footer,
    Link
  },
};
</script>

<template>
  <div>
    <div id="layout-wrapper">
      <NavBar />
      <!-- ========== App Menu ========== -->
      <div class="app-menu navbar-menu">
        <!-- LOGO -->
        <div class="navbar-brand-box">
          <!-- Dark Logo-->
          <Link href="/" class="logo logo-dark">
          <span class="logo-sm">
            <img src="@assets/images/logo-sm.png" alt="" height="22" />
          </span>
          <span class="logo-lg">
            <img src="@assets/images/logo-dark.png" alt="" height="17" />
          </span>
          </Link>
          <!-- Light Logo-->
          <Link href="/" class="logo logo-light">
          <span class="logo-sm">
            <img src="@assets/images/logo-sm.png" alt="" height="22" /> 
          </span>
          <span class="logo-lg">
            <img src="@assets/images/logo-light.png" alt="" height="17" />
          </span>
          </Link>
          <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
            id="vertical-hover">
            <i class="ri-record-circle-line"></i>
          </button>
        </div>
        <div id="scrollbar">
          <BContainer fluid>
            <ul class="navbar-nav h-100" id="navbar-nav">
              <li class="menu-title">
                <span data-key="t-menu"> {{ $t("t-menu") }}</span>
              </li>
              <li class="nav-item">
                <a class="nav-link menu-link" href="#sidebarDashboards" data-bs-toggle="collapse" role="button"
                  aria-expanded="false" aria-controls="sidebarDashboards">
                  <i class="ri-dashboard-2-line"></i>
                  <span data-key="t-dashboards"> {{ $t("t-dashboards") }}</span>
                </a>
                <div class="collapse menu-dropdown" id="sidebarDashboards">
                  <ul class="nav nav-sm flex-column">
                    <li class="nav-item">
                      <Link href="/" class="nav-link" data-key="t-ecommerce">
                      {{ $t("t-ecommerce") }}
                      </Link>
                    </li>
                  </ul>
                </div>
              </li>
              <!-- end Dashboard Menu -->

              <li class="menu-title">
                <i class="ri-more-fill"></i>
                <span data-key="t-pages">{{ $t("t-pages") }}</span>
              </li>

              <li class="nav-item">
                <a class="nav-link menu-link" href="#sidebarAuth" data-bs-toggle="collapse" role="button"
                  aria-expanded="false" aria-controls="sidebarAuth">
                  <i class="ri-account-circle-line"></i>
                  <span data-key="t-authentication">{{
                    $t("t-authentication")
                  }}</span>
                </a>
                <div class="collapse menu-dropdown" id="sidebarAuth">
                  <ul class="nav nav-sm flex-column">
                    <li class="nav-item">
                      <a class="nav-link" href="#sidebarSignIn" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarSignIn" data-key="t-signin">{{ $t("t-signin") }}
                      </a>
                      <div class="collapse menu-dropdown" id="sidebarSignIn">
                        <ul class="nav nav-sm flex-column">
                          <li class="nav-item">
                            <Link href="/auth/signin-basic" class="nav-link" data-key="t-basic">{{ $t("t-basic") }}
                            </Link>
                          </li>
                          <li class="nav-item">
                            <Link href="/auth/signin-cover" class="nav-link" data-key="t-cover">{{ $t("t-cover") }}
                            </Link>
                          </li>
                        </ul>
                      </div>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="#sidebarSignUp" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarSignUp" data-key="t-signup">{{ $t("t-signup") }}
                      </a>
                      <div class="collapse menu-dropdown" id="sidebarSignUp">
                        <ul class="nav nav-sm flex-column">
                          <li class="nav-item">
                            <Link href="/auth/signup-basic" class="nav-link" data-key="t-basic">{{ $t("t-basic") }}
                            </Link>
                          </li>
                          <li class="nav-item">
                            <Link href="/auth/signup-cover" class="nav-link" data-key="t-cover">{{ $t("t-cover") }}
                            </Link>
                          </li>
                        </ul>
                      </div>
                    </li>

                    <li class="nav-item">
                      <a class="nav-link" href="#sidebarResetPass" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarResetPass" data-key="t-password-reset">
                        {{ $t("t-password-reset") }}
                      </a>
                      <div class="collapse menu-dropdown" id="sidebarResetPass">
                        <ul class="nav nav-sm flex-column">
                          <li class="nav-item">
                            <Link href="/auth/reset-pwd-basic" class="nav-link" data-key="t-basic">
                            {{ $t("t-basic") }}
                            </Link>
                          </li>
                          <li class="nav-item">
                            <Link href="/auth/reset-pwd-cover" class="nav-link" data-key="t-cover">
                            {{ $t("t-cover") }}
                            </Link>
                          </li>
                        </ul>
                      </div>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="#sidebarcreatepass" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarcreatepass" data-key="t-password-reset">
                        {{ $t("t-password-create") }}
                      </a>
                      <div class="collapse menu-dropdown" id="sidebarcreatepass">
                        <ul class="nav nav-sm flex-column">
                          <li class="nav-item">
                            <Link href="/auth/create-pwd-basic" class="nav-link" data-key="t-basic">
                            {{ $t("t-basic") }}
                            </Link>
                          </li>
                          <li class="nav-item">
                            <Link href="/auth/create-pwd-cover" class="nav-link" data-key="t-cover">
                            {{ $t("t-cover") }}
                            </Link>
                          </li>
                        </ul>
                      </div>
                    </li>

                    <li class="nav-item">
                      <a class="nav-link" href="#sidebarLockScreen" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarLockScreen" data-key="t-lock-screen">
                        {{ $t("t-lock-screen") }}
                      </a>
                      <div class="collapse menu-dropdown" id="sidebarLockScreen">
                        <ul class="nav nav-sm flex-column">
                          <li class="nav-item">
                            <Link href="/auth/lockscreen-basic" class="nav-link" data-key="t-basic">
                            {{ $t("t-basic") }}
                            </Link>
                          </li>
                          <li class="nav-item">
                            <Link href="/auth/lockscreen-cover" class="nav-link" data-key="t-cover">
                            {{ $t("t-cover") }}
                            </Link>
                          </li>
                        </ul>
                      </div>
                    </li>

                    <li class="nav-item">
                      <a class="nav-link" href="#sidebarLogout" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarLogout" data-key="t-logout">
                        {{ $t("t-logout") }}
                      </a>
                      <div class="collapse menu-dropdown" id="sidebarLogout">
                        <ul class="nav nav-sm flex-column">
                          <li class="nav-item">
                            <Link href="/auth/logout-basic" class="nav-link" data-key="t-basic">
                            {{ $t("t-basic") }}
                            </Link>
                          </li>
                          <li class="nav-item">
                            <Link href="/auth/logout-cover" class="nav-link" data-key="t-cover">
                            {{ $t("t-cover") }}
                            </Link>
                          </li>
                        </ul>
                      </div>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="#sidebarSuccessMsg" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarSuccessMsg" data-key="t-success-message">
                        {{ $t("t-success-message") }}
                      </a>
                      <div class="collapse menu-dropdown" id="sidebarSuccessMsg">
                        <ul class="nav nav-sm flex-column">
                          <li class="nav-item">
                            <Link href="/auth/success-msg-basic" class="nav-link" data-key="t-basic">
                            {{ $t("t-basic") }}
                            </Link>
                          </li>
                          <li class="nav-item">
                            <Link href="/auth/success-msg-cover" class="nav-link" data-key="t-cover">
                            {{ $t("t-cover") }}
                            </Link>
                          </li>
                        </ul>
                      </div>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="#sidebarTwoStep" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarTwoStep" data-key="t-two-step-verification">
                        {{ $t("t-two-step-verification") }}
                      </a>
                      <div class="collapse menu-dropdown" id="sidebarTwoStep">
                        <ul class="nav nav-sm flex-column">
                          <li class="nav-item">
                            <Link href="/auth/twostep-basic" class="nav-link" data-key="t-basic">
                            {{ $t("t-basic") }}
                            </Link>
                          </li>
                          <li class="nav-item">
                            <Link href="/auth/twostep-cover" class="nav-link" data-key="t-cover">
                            {{ $t("t-cover") }}
                            </Link>
                          </li>
                        </ul>
                      </div>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="#sidebarErrors" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarErrors" data-key="t-errors">
                        {{ $t("t-errors") }}
                      </a>
                      <div class="collapse menu-dropdown" id="sidebarErrors">
                        <ul class="nav nav-sm flex-column">
                          <li class="nav-item">
                            <Link href="/auth/404-basic" class="nav-link" data-key="t-404-basic">
                            {{ $t("t-404-basic") }}
                            </Link>
                          </li>
                          <li class="nav-item">
                            <Link href="/auth/404-cover" class="nav-link" data-key="t-404-cover">
                            {{ $t("t-404-cover") }}
                            </Link>
                          </li>
                          <li class="nav-item">
                            <Link href="/auth/404" class="nav-link" data-key="t-404-alt">
                            {{ $t("t-404-alt") }}
                            </Link>
                          </li>
                          <li class="nav-item">
                            <Link href="/auth/500" class="nav-link" data-key="t-500">
                            {{ $t("t-500") }}
                            </Link>
                          </li>
                          <li class="nav-item">
                            <Link href="/auth/ofline" class="nav-link" data-key="t-ofline-page">
                            {{ $t("t-offline-page") }}
                            </Link>
                          </li>
                        </ul>
                      </div>
                    </li>
                  </ul>
                </div>
              </li>

              <li class="nav-item">
                <a class="nav-link menu-link" href="#sidebarPages" data-bs-toggle="collapse" role="button"
                  aria-expanded="false" aria-controls="sidebarPages">
                  <i class="ri-pages-line"></i>
                  <span data-key="t-pages">{{ $t("t-pages") }}</span>
                </a>
                <div class="collapse menu-dropdown" id="sidebarPages">
                  <ul class="nav nav-sm flex-column">
                    <li class="nav-item">
                      <Link href="/pages/starter" class="nav-link" data-key="t-starter">{{ $t("t-starter") }}
                      </Link>
                    </li>
                    <li class="nav-item">
                      <Link href="/pages/maintenance" class="nav-link" data-key="t-maintenance">
                      {{ $t("t-maintenance") }}
                      </Link>
                    </li>
                    <li class="nav-item">
                      <Link href="/pages/coming-soon" class="nav-link" data-key="t-coming-soon">
                      {{ $t("t-coming-soon") }}
                      </Link>
                    </li>
                  </ul>
                </div>
              </li>
              <li class="menu-title">
                <i class="ri-more-fill"></i>
                <span data-key="t-components">{{ $t("t-components") }}</span>
              </li>


              <li class="nav-item">
                <a class="nav-link" href="#sidebarMore" data-bs-toggle="collapse" role="button" aria-expanded="false"
                  aria-controls="sidebarMore">
                  <i class="ri-briefcase-2-line"></i> More
                </a>
                <div class="collapse menu-dropdown show" id="sidebarMore">
                  <ul class="nav nav-sm flex-column">
                    <li class="nav-item">
                      <a class="nav-link menu-link" href="#sidebarMultilevel" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarMultilevel">
                        <i class="ri-share-line"></i>
                        <span data-key="t-multi-level">{{
                          $t("t-multi-level")
                        }}</span>
                      </a>
                      <div class="collapse menu-dropdown" id="sidebarMultilevel">
                        <ul class="nav nav-sm flex-column">
                          <li class="nav-item">
                            <a class="nav-link" data-key="t-level-1.1">
                              {{ $t("t-level-1.1") }}
                            </a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" href="#sidebarAccount" data-bs-toggle="collapse" role="button"
                              aria-expanded="false" aria-controls="sidebarAccount" data-key="t-level-1.2">
                              {{ $t("t-level-1.2") }}
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarAccount">
                              <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                  <a class="nav-link" data-key="t-level-2.1">
                                    {{ $t("t-level-2.1") }}
                                  </a>
                                </li>
                                <li class="nav-item">
                                  <a class="nav-link" href="#sidebarCrm" data-bs-toggle="collapse" role="button"
                                    aria-expanded="false" aria-controls="sidebarCrm" data-key="t-level-2.2">
                                    {{ $t("t-level-2.2") }}
                                  </a>
                                  <div class="collapse menu-dropdown" id="sidebarCrm">
                                    <ul class="nav nav-sm flex-column">
                                      <li class="nav-item">
                                        <a class="nav-link" data-key="t-level-3.1">
                                          {{ $t("t-level-3.1") }}
                                        </a>
                                      </li>
                                      <li class="nav-item">
                                        <a class="nav-link" data-key="t-level-3.2">
                                          {{ $t("t-level-3.2") }}
                                        </a>
                                      </li>
                                    </ul>
                                  </div>
                                </li>
                              </ul>
                            </div>
                          </li>
                        </ul>
                      </div>
                    </li>
                  </ul>
                </div>
              </li>
            </ul>
          </BContainer>
          <!-- Sidebar -->
        </div>
        <!-- Left Sidebar End -->
        <!-- Vertical Overlay-->
        <div class="vertical-overlay"></div>
      </div>
      <!-- ============================================================== -->
      <!-- Start Page Content here -->
      <!-- ============================================================== -->

      <div class="main-content">
        <div class="page-content">
          <!-- Start Content-->
          <BContainer fluid>
            <slot />
          </BContainer>
        </div>
        <Footer />
      </div>
      <RightBar />
    </div>
  </div>
</template>