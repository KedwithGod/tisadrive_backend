<script>
import { Link } from "@inertiajs/vue3";
import simplebar from "simplebar-vue";

import { layoutComputed } from "@/state/helpers";
import Menu from "@/Components/menu.vue";
import NavBar from "@/Components/nav-bar.vue";
import RightBar from "@/Components/right-bar.vue";
import Footer from "@/Components/footer.vue";

/**
 * Vertical layout
 */
export default {
  components: {
    NavBar,
    RightBar,
    Footer,
    Menu,
    simplebar,
    Link
  },
  data() {
    return {
      isMenuCondensed: false,
      rmenu: localStorage.getItem('rmenu') ? localStorage.getItem('rmenu') : 'twocolumn',
    };
  },

  computed: {
    ...layoutComputed,
  },
  created: () => {
    document.body.removeAttribute("data-layout", "horizontal");
    document.body.removeAttribute("data-topbar", "dark");
    document.body.removeAttribute("data-layout-size", "boxed");
  },
  methods: {
    initActiveMenu() {
      const pathName = window.location.pathname;
      const ul = document.getElementById("navbar-nav");
      if (ul) {
        const items = Array.from(ul.querySelectorAll("a.nav-link"));
        let activeItems = items.filter((x) => x.classList.contains("active"));
        this.removeActivation(activeItems);
        let matchingMenuItem = items.find((x) => {
          return x.getAttribute("href") === pathName;
        });
        if (matchingMenuItem) {
          this.activateParentDropdown(matchingMenuItem);
        } else {
          var id = pathName.replace("/", "");
          if (id) document.body.classList.add("twocolumn-panel");
          this.activateIconSidebarActive(pathName);
        }
      }
    },

    updateMenu(e, event) {
      document.body.classList.remove("twocolumn-panel");
      const ul = document.getElementById("navbar-nav");
      if (ul) {
        const items = Array.from(ul.querySelectorAll(".show"));
        items.forEach((item) => {
          item.classList.remove("show");
        });
      }
      const icons = document.getElementById("two-column-menu");
      if (icons) {
        const activeIcons = Array.from(icons.querySelectorAll(".nav-icon.active"));
        activeIcons.forEach((item) => {
          item.classList.remove("active");
        });
      }
      document.getElementById(e).classList.add("show");
      event.target.classList.add("active")
      this.activateIconSidebarActive("#" + e);
    },

    removeActivation(items) {
      items.forEach((item) => {
        if (item.classList.contains("menu-link")) {
          if (!item.classList.contains("active")) {
            item.setAttribute("aria-expanded", false);
          }
          item.nextElementSibling.classList.remove("show");
        }
        if (item.classList.contains("nav-link")) {
          if (item.nextElementSibling) {
            item.nextElementSibling.classList.remove("show");
          }
          item.setAttribute("aria-expanded", false);
        }
        item.classList.remove("active");
      });
    },

    activateIconSidebarActive(id) {
      var menu = document.querySelector("#two-column-menu .simplebar-content-wrapper a[href='" + id + "'].nav-icon");
      if (menu !== null) {
        menu.classList.add("active");
      }
    },

    activateParentDropdown(item) {
      // navbar-nav menu add active
      item.classList.add("active");
      let parentCollapseDiv = item.closest(".collapse.menu-dropdown");
      if (parentCollapseDiv) {
        // to set aria expand true remaining
        parentCollapseDiv.classList.add("show");
        parentCollapseDiv.parentElement.children[0].classList.add("active");
        parentCollapseDiv.parentElement.children[0].setAttribute("aria-expanded", "true");
        if (parentCollapseDiv.parentElement.closest(".collapse.menu-dropdown")) {
          if (parentCollapseDiv.parentElement.closest(".collapse.menu-dropdown").previousElementSibling) {
            if (parentCollapseDiv.parentElement.closest(".collapse.menu-dropdown").previousElementSibling.parentElement.closest(".collapse.menu-dropdown")) {
              const grandparent = parentCollapseDiv.parentElement.closest(".collapse.menu-dropdown").previousElementSibling.parentElement.closest(".collapse.menu-dropdown");
              this.activateIconSidebarActive("#" + grandparent.getAttribute("id"));
              grandparent.classList.add("show");
            }
          }
          this.activateIconSidebarActive("#" + parentCollapseDiv.parentElement.closest(".collapse.menu-dropdown").getAttribute("id"));

          parentCollapseDiv.parentElement.closest(".collapse").classList.add("show");
          if (parentCollapseDiv.parentElement.closest(".collapse").previousElementSibling)
            parentCollapseDiv.parentElement.closest(".collapse").previousElementSibling.classList.add("active");
          return false;
        }
        this.activateIconSidebarActive("#" + parentCollapseDiv.getAttribute("id"));
        return false;
      }
      return false;
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
    this.initActiveMenu();
    if (this.rmenu == 'vertical' && this.layoutType == 'twocolumn') {
      document.documentElement.setAttribute("data-layout", "vertical");
    }
    document.getElementById('overlay').addEventListener('click', () => {
      document.body.classList.remove('vertical-sidebar-enable');
    });

    window.addEventListener("resize", () => {
      if (this.layoutType == 'twocolumn') {
        var windowSize = document.documentElement.clientWidth;
        if (windowSize < 767) {
          document.documentElement.setAttribute("data-layout", "vertical");
          this.rmenu = 'vertical';
          localStorage.setItem('rmenu', 'vertical');
        } else {
          document.documentElement.setAttribute("data-layout", "twocolumn");
          this.rmenu = 'twocolumn';
          localStorage.setItem('rmenu', 'twocolumn');
          setTimeout(() => {
            this.initActiveMenu();
          }, 50);

        }
      }
    });
    if (document.querySelectorAll(".navbar-nav .collapse")) {
      let collapses = document.querySelectorAll(".navbar-nav .collapse");

      collapses.forEach((collapse) => {
        // Hide sibling collapses on `show.bs.collapse`
        collapse.addEventListener("show.bs.collapse", (e) => {
          e.stopPropagation();
          let closestCollapse = collapse.parentElement.closest(".collapse");
          if (closestCollapse) {
            let siblingCollapses = closestCollapse.querySelectorAll(".collapse");
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
          <button type="button" class=" btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
            <i class="ri-record-circle-line"></i>
          </button>
        </div>

        <div id="scrollbar" v-if="rmenu == 'twocolumn'">
          <BContainer fluid>
            <div id="two-column-menu">
              <simplebar class="twocolumn-iconview list-unstyled">
                <a class="logo"><img src="@assets/images/logo-sm.png" alt="Logo" height="22" /></a>
                <li>
                  <a href="#sidebarDashboards" class="nav-icon" role="button" @click.prevent="updateMenu('sidebarDashboards', $event)">
                    <i class="ri-dashboard-2-line"></i>
                  </a>
                </li>
                <li>
                  <a href="#sidebarAuth" class="nav-icon" role="button" @click.prevent="updateMenu('sidebarAuth', $event)">
                    <i class="ri-account-circle-line"></i>
                  </a>
                </li>
                <li>
                  <a href="#sidebarPages" class="nav-icon" role="button" @click.prevent="updateMenu('sidebarPages', $event)">
                    <i class="ri-pages-line"></i>
                  </a>
                </li>
                <li>
                  <a href="#sidebarMultilevel" class="nav-icon" role="button" @click.prevent="updateMenu('sidebarMultilevel', $event)">
                    <i class="ri-share-line"></i>
                  </a>
                </li>
              </simplebar>
            </div>
            <template v-if="layoutType === 'twocolumn'">
              <simplebar class="navbar-nav" id="navbar-nav">
                <li class="menu-title">
                  <span data-key="t-menu"> {{ $t("t-menu") }}</span>
                </li>
                <li class="nav-item">

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

                <li class="nav-item">
                  <div class="collapse menu-dropdown" id="sidebarAuth">
                    <ul class="nav nav-sm flex-column">
                      <li class="nav-item">

                        <a class="nav-link" href="#sidebarSignIn" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarSignIn" data-key="t-signin">{{ $t("t-signin") }}
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarSignIn">

                          <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                              <Link href="/auth/signin-basic" class="nav-link" data-key="t-basic">
                              {{ $t("t-basic") }}
                              </Link>
                            </li>
                            <li class="nav-item">
                              <Link href="/auth/signin-cover" class="nav-link" data-key="t-cover">
                              {{ $t("t-cover") }}
                              </Link>
                            </li>
                          </ul>
                        </div>
                      </li>
                      <li class="nav-item">

                        <a class="nav-link" href="#sidebarSignUp" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarSignUp" data-key="t-signup">{{ $t("t-signup") }}
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarSignUp">

                          <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                              <Link href="/auth/signup-basic" class="nav-link" data-key="t-basic">
                              {{ $t("t-basic") }}
                              </Link>
                            </li>
                            <li class="nav-item">
                              <Link href="/auth/signup-cover" class="nav-link" data-key="t-cover">
                              {{ $t("t-cover") }}
                              </Link>
                            </li>
                          </ul>
                        </div>
                      </li>

                      <li class="nav-item">

                        <a class="nav-link" href="#sidebarResetPass" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarResetPass" data-key="t-password-reset">
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

                        <a class="nav-link" href="#sidebarcreatepass" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarcreatepass" data-key="t-password-reset">
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

                        <a class="nav-link" href="#sidebarLockScreen" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarLockScreen" data-key="t-lock-screen">
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

                        <a class="nav-link" href="#sidebarLogout" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarLogout" data-key="t-logout">
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

                        <a class="nav-link" href="#sidebarSuccessMsg" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarSuccessMsg" data-key="t-success-message">
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

                        <a class="nav-link" href="#sidebarTwoStep" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarTwoStep" data-key="t-two-step-verification">
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

                        <a class="nav-link" href="#sidebarErrors" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarErrors" data-key="t-errors">
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
                              <Link href="/auth/ofline" class="nav-link" data-key="t-500">
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
                <li class="nav-item">
                  <div class="collapse menu-dropdown" id="sidebarMultilevel">
                    <ul class="nav nav-sm flex-column">
                      <li class="nav-item">

                        <a class="nav-link" data-key="t-level-1.1">

                          {{ $t("t-level-1.1") }}
                        </a>
                      </li>
                      <li class="nav-item">

                        <a class="nav-link" href="#sidebarAccount" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarAccount" data-key="t-level-1.2">
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

                              <a class="nav-link" href="#sidebarCrm" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarCrm" data-key="t-level-2.2">
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
              </simplebar>
            </template>
          </BContainer>
        </div>

        <simplebar id="scrollbar" class="h-100" ref="scrollbar" v-if="rmenu == 'vertical'">
          <Menu></Menu>
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
        <BContainer fluid>
          <slot />
        </BContainer>
      </div>
      <Footer />
    </div>
    <RightBar />
  </div>
</template>