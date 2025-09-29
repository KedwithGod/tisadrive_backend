<script>
localStorage.setItem("rightbar_isopen", false);
import { layoutMethods, layoutComputed } from "@/state/helpers";
import simplebar from "simplebar-vue";
import { Link,Head } from '@inertiajs/vue3';
/**
 * Right sidebar component
 */

export default {
  data() {
    return {
      show: false,
      showGradients: false,
      resetLayoutMode: {},
      // toggleValue:false,
      isBoxVisible: false, // Controls the visibility of the sliding box
    };
  },
  beforeCreate() {
    localStorage.setItem("resetValue", JSON.stringify(this.$store.state.layout));
  },
  methods: {
    ...layoutMethods,
    click() {
      this.show = !this.show;
    },
    topFunction() {
      document.body.scrollTop = 0;
      document.documentElement.scrollTop = 0;
    },
    toggleBox() {
      this.isBoxVisible = !this.isBoxVisible; // Toggles the box visibility
    },
    hideBox() {
      this.isBoxVisible = false; // Hides the box when mouse leaves
    },

    resizeWindow() {
      var windowSize = document.documentElement.clientWidth;
      if (windowSize >= 1025) {
        if (document.documentElement.getAttribute("data-layout") === "vertical") {
          document.documentElement.setAttribute("data-sidebar-size", this.$store.state.layout.sidebarSize);
        }
        if (document.documentElement.getAttribute("data-layout") === "semibox") {
          document.documentElement.setAttribute("data-sidebar-size", this.$store.state.layout.sidebarSize);
        }
        if (document.documentElement.getAttribute("data-sidebar-visibility") === "show" && document.querySelector(".hamburger-icon")) {
          document.querySelector(".hamburger-icon").classList.remove("open");
        }
      } else if (windowSize < 1025 && windowSize > 767) {
        document.body.classList.remove("twocolumn-panel");
        if (document.documentElement.getAttribute("data-layout") === "vertical") {
          document.documentElement.setAttribute("data-sidebar-size", "sm");
        }
        if (document.documentElement.getAttribute("data-layout") === "semibox") {
          document.documentElement.setAttribute("data-sidebar-size", "sm");
        }
        if (document.querySelector(".hamburger-icon")) {
          document.querySelector(".hamburger-icon").classList.add("open");
        }
      } else if (windowSize <= 767) {
        document.body.classList.remove("vertical-sidebar-enable");
        document.body.classList.add("twocolumn-panel");
        if (document.documentElement.getAttribute("data-layout") !== "horizontal") {
          document.documentElement.setAttribute("data-sidebar-size", "lg");
        }
        if (document.querySelector(".hamburger-icon")) {
          document.querySelector(".hamburger-icon").classList.add("open");
        }
      }
    },

    resetLayout() {
      let reset = JSON.parse(localStorage.getItem("resetValue"));
      document.documentElement.setAttribute("data-sidebar-size", "lg");
      this.changeMode({ mode: reset.mode });
      this.changeSidebarColor({ sidebarColor: reset.sidebarColor });
      this.changeLayoutType({ layoutType: reset.layoutType });
      this.changeTopbar({ topbar: reset.topbar });
      this.changeLayoutWidth({ layoutWidth: reset.layoutWidth });
      this.changeSidebarSize({ sidebarSize: reset.sidebarSize });
      this.changeSidebarImage({ sidebarImage: reset.sidebarImage });
      this.changeSidebarColor({ sidebarColor: reset.sidebarColor });
      this.changePreloader({ preloader: reset.preloader });
      this.changeSidebarView({ sidebarView: reset.sidebarView });
      this.changeVisibility({ visibility: reset.visibility });
      this.changePosition({ position: reset.position });
    },

    gradiantColor() {
      this.changeSidebarColor({ sidebarColor: "gradient" })
    },

    onSideBarColorClick(color) {
      if (color !== 'gradient') {
        this.showGradients = false
      } else {
        this.showGradients = true
        this.gradiantColor();
      }
    },


    // toggleDirection() {
    //         this.toggleValue = !this.toggleValue; 
    //         this.toggleDirectionSwitch();         
    //     },

    //     toggleDirectionSwitch(){           
    //         const body = document.body;
    //         if (this.toggleValue) {
    //             body.classList.add('rtl');
    //             body.classList.remove('ltr');      
    //         }
    //         else {
    //             body.classList.add('ltr');
    //             body.classList.remove('rtl');
    //         } 
    //         localStorage.setItem('toggleDirection', this.toggleValue);
               
    //     },
    //     initializeToggleDirection() {
    //         const savedToggleValue = localStorage.getItem('toggleDirection');
    //         if (savedToggleValue === "true") {
    //             this.toggleValue = true;
    //         } else {
    //             this.toggleValue = false;
            
    //         this.toggleDirectionSwitch();
    //     }
  },
  mounted() {

    // this.initializeToggleDirection();
    let backtoTop = document.getElementById("back-to-top");

    if (backtoTop) {
      backtoTop = document.getElementById("back-to-top");
      window.onscroll = function () {
        if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
          backtoTop.style.display = "block";
        } else {
          backtoTop.style.display = "none";
        }
      };
    }
    var setpreloader = document.getElementById("preloader");
    if (localStorage.getItem('data-preloader') && localStorage.getItem('data-preloader') == 'enable') {
      document.documentElement.setAttribute("data-preloader", "enable");
      if (setpreloader) {
        setTimeout(function () {
          setpreloader.style.opacity = "0";
          setpreloader.style.visibility = "hidden";
        }, 1000);
      }
    } else {
      document.documentElement.setAttribute("data-preloader", "disable");
      if (setpreloader) {
        setpreloader.style.opacity = "0";
        setpreloader.style.visibility = "hidden";
      }
    }
    if (document.getElementById('collapseBgGradient')) {
      Array.from(document.querySelectorAll("#collapseBgGradient .form-check input")).forEach(function () {
        if (document.querySelector("[data-bs-target='#collapseBgGradient']")) {
          document.querySelector("[data-bs-target='#collapseBgGradient']").addEventListener('click', function () {
            document.getElementById("sidebar-color-gradient").click();
          });
        }
      });
      Array.from(document.querySelectorAll("[name='data-sidebar']")).forEach(function (elem) {
        if (document.querySelector("[data-bs-target='#collapseBgGradient']")) {
          if (document.querySelector("#collapseBgGradient .form-check input:checked")) {
            document.querySelector("[data-bs-target='#collapseBgGradient']").classList.add("active");
          } else {
            document.querySelector("[data-bs-target='#collapseBgGradient']").classList.remove("active");
            document.getElementById('collapseBgGradient').classList.remove('show');
          }

          elem.addEventListener("change", function () {
            if (document.querySelector("#collapseBgGradient .form-check input:checked")) {
              document.querySelector("[data-bs-target='#collapseBgGradient']").classList.add("active");
            } else {
              document.getElementById('collapseBgGradient').classList.remove('show');
              document.querySelector("[data-bs-target='#collapseBgGradient']").classList.remove("active");
            }
          });
        }
      });
    }

    window.addEventListener("resize", this.resizeWindow);
  },
  computed: {
    ...layoutComputed,
    layoutType: {
      get() {
        return this.$store ? this.$store.state.layout.layoutType : {} || {};
      },
      set(layout) {
        localStorage.setItem("rightbar_isopen", true);
        this.changeLayoutType({ layoutType: layout, });
        document.querySelector(".hamburger-icon").classList.remove("open");
      },
    },
    preloader: {
      get() {
        return this.$store ? this.$store.state.layout.preloader : {} || {};
      },
      set(preloader) {
        return this.changePreloader({
          preloader: preloader,
        });
      },
    },
    mode: {
      get() {
        return this.$store ? this.$store.state.layout.mode : {} || {};
      },
      set(mode) {
        if (mode == "dark") {
          this.changeMode({ mode: mode });
          this.changeTopbar({ topbar: "light" });
          this.changeSidebarColor({ sidebarColor: "dark" });
        } else {
          this.changeMode({ mode: mode });
          this.changeTopbar({ topbar: "light" });
          this.changeSidebarColor({ sidebarColor: "light" });
        }
      },
    },
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
    layoutWidth: {
      get() {
        return this.$store ? this.$store.state.layout.layoutWidth : {} || {};
      },
      set(width) {
        if (width == 'boxed') {
          this.changeLayoutWidth({ layoutWidth: width });
          this.changeSidebarSize({ sidebarSize: 'sm-hover' });
        } else {
          this.changeLayoutWidth({ layoutWidth: width });
          this.changeSidebarSize({ sidebarSize: 'lg' });
        }
      },
    },
    position: {
      get() {
        return this.$store ? this.$store.state.layout.position : {} || {};
      },
      set(position) {
        return this.changePosition({
          position: position,
        });
      },
    },
    topbar: {
      get() {
        return this.$store ? this.$store.state.layout.topbar : {} || {};
      },
      set(topbar) {
        this.changeTopbar({
          topbar: topbar,
        });
      },
    },
    sidebarView: {
      get() {
        return this.$store ? this.$store.state.layout.sidebarView : {} || {};
      },
      set(sidebarView) {
        return this.changeSidebarView({
          sidebarView: sidebarView,
        });
      },
    },
    sidebarColor: {
      get() {
        return this.$store ? this.$store.state.layout.sidebarColor : {} || {};
      },
      set(sidebarColor) {
        return this.changeSidebarColor({
          sidebarColor: sidebarColor,
        });
      },
    },
    sidebarImage: {
      get() {
        return this.$store ? this.$store.state.layout.sidebarImage : {} || {};
      },
      set(sidebarImage) {
        return this.changeSidebarImage({
          sidebarImage: sidebarImage,
        });
      },
    },

    visibility: {
      get() {
        return this.$store ? this.$store.state.layout.visibility : {} || {};
      },
      set(visibility) {
        if (visibility == 'hidden') {
          document.querySelector(".hamburger-icon").classList.add("open");
        } else {
          document.querySelector(".hamburger-icon").classList.remove("open");
        }
        this.changeVisibility({ visibility: visibility });
      },
    },
  },

  watch: {
    show: {
      immediate: true,
      deep: true,
      handler(newVal, oldVal) {
        if (newVal !== oldVal) {
          if (!newVal) {
            document.body.removeAttribute("style");
          } 
          // else {
          //   setTimeout(() => {
          //     document.body.setAttribute("style", "overflow: hidden; padding-right:17px");
          //   }, 500)
          // }
        }
      },
    },
    mode: {
      immediate: true,
      deep: true,
      handler(newVal, oldVal) {
        if (newVal !== oldVal) {
          switch (newVal) {
            case "dark":
              document.documentElement.setAttribute("data-bs-theme", "dark");
              break;
            case "light":
              document.documentElement.setAttribute("data-bs-theme", "light");
              break;
          }
        }
      },
    },
    preloader: {
      immediate: true,
      deep: true,
      handler(newVal, oldVal) {
        if (newVal !== oldVal) {
          switch (newVal) {
            case "enable":
              document.documentElement.setAttribute("data-preloader", "enable");
              break;
            case "disable":
              document.documentElement.setAttribute("data-preloader", "disable");
              break;
          }
          localStorage.setItem('data-preloader', newVal);
        }
      },
    },
    layoutType: {
      immediate: true,
      deep: true,
      handler(newVal, oldVal) {
        if (newVal !== oldVal) {
          switch (newVal) {
            case "horizontal":
              document.documentElement.setAttribute("data-layout", "horizontal");
              break;
            case "vertical":
              document.documentElement.setAttribute("data-layout", "vertical");
              break;
            case "twocolumn":
              document.documentElement.setAttribute("data-layout", "twocolumn");
              break;
            case "semibox":
              document.documentElement.setAttribute("data-layout", "semibox");
              break;
          }
        }
      },
    },
    layoutWidth: {
      immediate: true,
      deep: true,
      handler(newVal, oldVal) {
        if (newVal !== oldVal) {
          switch (newVal) {
            case "fluid":
              document.documentElement.setAttribute("data-layout-width", "fluid");
              break;
            case "boxed":
              document.documentElement.setAttribute("data-layout-width", "boxed");
              break;
          }
        }
      },
    },
    position: {
      immediate: true,
      deep: true,
      handler(newVal, oldVal) {
        if (newVal !== oldVal) {
          switch (newVal) {
            case "fixed":
              document.documentElement.setAttribute("data-layout-position", "fixed");
              break;
            case "scrollable":
              document.documentElement.setAttribute("data-layout-position", "scrollable");
              break;
          }
        }
      },
    },
    topbar: {
      immediate: true,
      deep: true,
      handler(newVal, oldVal) {
        if (newVal !== oldVal) {
          switch (newVal) {
            case "light":
              document.documentElement.setAttribute("data-topbar", "light");
              break;
            case "dark":
              document.documentElement.setAttribute("data-topbar", "dark");
              break;
          }
        }
      },
    },
    sidebarSize: {
      immediate: true,
      deep: true,
      handler(newVal, oldVal) {
        if (newVal !== oldVal) {
          switch (newVal) {
            case "lg":
              document.documentElement.setAttribute("data-sidebar-size", "lg");
              break;
            case "sm":
              document.documentElement.setAttribute("data-sidebar-size", "sm");
              break;
            case "md":
              document.documentElement.setAttribute("data-sidebar-size", "md");
              break;
            case "sm-hover":
              document.documentElement.setAttribute("data-sidebar-size", "sm-hover");
              break;
          }
        }
      },
    },
    sidebarView: {
      immediate: true,
      deep: true,
      handler(newVal, oldVal) {
        if (newVal !== oldVal) {
          switch (newVal) {
            case "detached":
              document.documentElement.setAttribute("data-layout-style", "detached");
              break;
            case "default":
              document.documentElement.setAttribute("data-layout-style", "default");
              break;
          }
        }
      },
    },
    sidebarColor: {
      immediate: true,
      deep: true,
      handler(newVal, oldVal) {
        if (newVal !== oldVal) {
          switch (newVal) {
            case "dark":
              document.documentElement.setAttribute("data-sidebar", "dark");
              break;
            case "light":
              document.documentElement.setAttribute("data-sidebar", "light");
              break;
            case "gradient":
              document.documentElement.setAttribute("data-sidebar", "gradient");
              break;
            case "gradient-2":
              document.documentElement.setAttribute("data-sidebar", "gradient-2");
              break;
            case "gradient-3":
              document.documentElement.setAttribute("data-sidebar", "gradient-3");
              break;
            case "gradient-4":
              document.documentElement.setAttribute("data-sidebar", "gradient-4");
              break;
          }
        }
      },
    },
    sidebarImage: {
      immediate: true,
      deep: true,
      handler(newVal, oldVal) {
        if (newVal !== oldVal) {
          switch (newVal) {
            case "img-1":
              document.documentElement.setAttribute("data-sidebar-image", "img-1");
              break;
            case "img-2":
              document.documentElement.setAttribute("data-sidebar-image", "img-2");
              break;
            case "img-3":
              document.documentElement.setAttribute("data-sidebar-image", "img-3");
              break;
            case "img-4":
              document.documentElement.setAttribute("data-sidebar-image", "img-4");
              break;
            case "none":
              document.documentElement.setAttribute("data-sidebar-image", "none");
              break;
          }
        }
      },
    },
    visibility: {
      immediate: true,
      deep: true,
      handler(newVal, oldVal) {
        if (newVal !== oldVal) {
          switch (newVal) {
            case "show":
              document.documentElement.setAttribute("data-sidebar-visibility", "show");
              break;
            case "hidden":
              document.documentElement.setAttribute("data-sidebar-visibility", "hidden");
              break;
          }
        }
      },
    },
  },
  components: { 
    simplebar ,
    Link,
    Head,

  }
};


</script>

<template>
  <div>
    <div id="preloader">
      <div id="status">
        <div class="spinner-border text-primary avatar-sm" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
    </div>

    <BButton variant="danger" @click="topFunction" class="btn-icon" id="back-to-top">
      <i class="ri-arrow-up-line"></i>
    </BButton>

    <!-- <div class="customizer-setting d-none d-md-block" @click="click">
      <div class="btn-info rounded-pill shadow-lg btn btn-icon btn-lg p-2" data-bs-toggle="offcanvas" data-bs-target="#theme-settings-offcanvas" aria-controls="theme-settings-offcanvas" id="mdi-cog">
        <i class="mdi mdi-spin mdi-cog-outline fs-22"></i>
      </div>
    </div> -->
    <BOffcanvas class="border-0" id="theme-settings-offcanvas" header-class="d-flex align-items-center bg-primary bg-gradient p-3" body-class="p-0" z-index="1005" footer-class="offcanvas-footer border-top p-3 text-center" placement="end" v-model="show">
      <template #header>
        <div class="me-2">
          <h5 class="m-0 me-2 text-white">{{$t("theme_customizer")}}</h5>
        </div>
        <button type="button" class="btn-close btn-close-white ms-auto" id="customizerclose-btn" @click="click"></button>
      </template>
      <simplebar class="h-100">
        <div class="p-4">
          <h6 class="mb-0 fw-semibold text-uppercase">{{$t("layout")}}</h6>
          <p class="text-muted">{{$t("choose_your_layout")}}</p>

          <BRow class="gy-3">
            <BCol cols="4">
              <div class="form-check card-radio">
                <input id="customizer-layout01" name="data-layout" type="radio" value="vertical" class="form-check-input" v-model="layoutType" />
                <label class="form-check-label p-0 avatar-md w-100" for="customizer-layout01">
                  <span class="d-flex gap-1 h-100">
                    <span class="flex-shrink-0">
                      <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                        <span class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                        <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                        <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                        <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                      </span>
                    </span>
                    <span class="flex-grow-1">
                      <span class="d-flex h-100 flex-column">
                        <span class="bg-light d-block p-1"></span>
                        <span class="bg-light d-block p-1 mt-auto"></span>
                      </span>
                    </span>
                  </span>
                </label>
              </div>
              <h5 class="fs-13 text-center mt-2">{{$t("vertical")}}</h5>
            </BCol>
            <!-- <BCol cols="4">
              <div class="form-check card-radio">
                <input id="customizer-layout02" name="data-layout" type="radio" value="horizontal" class="form-check-input" v-model="layoutType" />
                <label class="form-check-label p-0 avatar-md w-100" for="customizer-layout02">
                  <span class="d-flex h-100 flex-column gap-1">
                    <span class="bg-light d-flex p-1 gap-1 align-items-center">
                      <span class="d-block p-1 bg-primary-subtle rounded me-1"></span>
                      <span class="d-block p-1 pb-0 px-2 bg-primary-subtle ms-auto"></span>
                      <span class="d-block p-1 pb-0 px-2 bg-primary-subtle"></span>
                    </span>
                    <span class="bg-light d-block p-1"></span>
                    <span class="bg-light d-block p-1 mt-auto"></span>
                  </span>
                </label>
              </div>
              <h5 class="fs-13 text-center mt-2">Horizontal</h5>
            </BCol> -->
            <!-- <BCol cols="4">
              <div class="form-check card-radio">
                <input id="customizer-layout03" name="data-layout" type="radio" value="twocolumn" class="form-check-input" v-model="layoutType" />
                <label class="form-check-label p-0 avatar-md w-100" for="customizer-layout03">
                  <span class="d-flex gap-1 h-100">
                    <span class="flex-shrink-0">
                      <span class="bg-light d-flex h-100 flex-column gap-1">
                        <span class="d-block p-1 bg-primary-subtle mb-2"></span>
                        <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                        <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                        <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                      </span>
                    </span>
                    <span class="flex-shrink-0">
                      <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                        <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                        <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                        <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                        <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                      </span>
                    </span>
                    <span class="flex-grow-1">
                      <span class="d-flex h-100 flex-column">
                        <span class="bg-light d-block p-1"></span>
                        <span class="bg-light d-block p-1 mt-auto"></span>
                      </span>
                    </span>
                  </span>
                </label>
              </div>
              <h5 class="fs-13 text-center mt-2">Two Column</h5>
            </BCol> -->
            <BCol cols="4">
              <div class="form-check card-radio">
                <input id="customizer-layout04" name="data-layout" type="radio" value="semibox" class="form-check-input" v-model="layoutType" />
                <label class="form-check-label p-0 avatar-md w-100" for="customizer-layout04">
                  <span class="d-flex gap-1 h-100">
                    <span class="flex-shrink-0 p-1">
                      <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                        <span class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                        <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                        <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                        <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                      </span>
                    </span>
                    <span class="flex-grow-1">
                      <span class="d-flex h-100 flex-column pt-1 pe-2">
                        <span class="bg-light d-block p-1"></span>
                        <span class="bg-light d-block p-1 mt-auto"></span>
                      </span>
                    </span>
                  </span>
                </label>
              </div>
              <h5 class="fs-13 text-center mt-2">{{$t("semi_box")}}</h5>
            </BCol>
          </BRow>

          <h6 class="mt-4 mb-0 fw-semibold text-uppercase">{{$t("color_scheme")}}</h6>
          <p class="text-muted">{{$t("choose_light_or_dark_scheme")}}</p>

          <div class="colorscheme-cardradio">
            <BRow>
              <BCol cols="4">
                <div class="form-check card-radio">
                  <input class="form-check-input" type="radio" name="data-bs-theme" id="layout-mode-light" value="light" v-model="mode" />
                  <label class="form-check-label p-0 avatar-md w-100" for="layout-mode-light">
                    <span class="d-flex gap-1 h-100">
                      <span class="flex-shrink-0">
                        <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                          <span class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                          <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                          <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                          <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                        </span>
                      </span>
                      <span class="flex-grow-1">
                        <span class="d-flex h-100 flex-column">
                          <span class="bg-light d-block p-1"></span>
                          <span class="bg-light d-block p-1 mt-auto"></span>
                        </span>
                      </span>
                    </span>
                  </label>
                </div>
                <h5 class="fs-13 text-center mt-2">{{$t("light")}}</h5>
              </BCol>

              <BCol cols="4">
                <div class="form-check card-radio dark">
                  <input class="form-check-input" v-model="mode" type="radio" name="data-bs-theme" id="layout-mode-dark" value="dark" />
                  <label class="form-check-label p-0 avatar-md w-100 bg-dark" for="layout-mode-dark">
                    <span class="d-flex gap-1 h-100">
                      <span class="flex-shrink-0">
                        <span class="bg-white bg-opacity-10 d-flex h-100 flex-column gap-1 p-1">
                          <span class="d-block p-1 px-2 bg-white bg-opacity-10 rounded mb-2"></span>
                          <span class="d-block p-1 px-2 pb-0 bg-white bg-opacity-10"></span>
                          <span class="d-block p-1 px-2 pb-0 bg-white bg-opacity-10"></span>
                          <span class="d-block p-1 px-2 pb-0 bg-white bg-opacity-10"></span>
                        </span>
                      </span>
                      <span class="flex-grow-1">
                        <span class="d-flex h-100 flex-column">
                          <span class="bg-white bg-opacity-10 d-block p-1"></span>
                          <span class="bg-white bg-opacity-10 d-block p-1 mt-auto"></span>
                        </span>
                      </span>
                    </span>
                  </label>
                </div>
                <h5 class="fs-13 text-center mt-2">{{$t("dark")}}</h5>
              </BCol>
            </BRow>
          </div>
          <div id="sidebar-visibility" v-if="layoutType == 'semibox'">
            <h6 class="mt-4 mb-0 fw-semibold text-uppercase">{{$t("sidebar_visibility")}}</h6>
            <p class="text-muted">{{$t("choose_show_or_hidden_sidebar")}}</p>

            <BRow>
              <BCol cols="4">
                <div class="form-check card-radio">
                  <input class="form-check-input" type="radio" name="data-sidebar-visibility" id="sidebar-visibility-show" value="show" v-model="visibility" />
                  <label class="form-check-label p-0 avatar-md w-100" for="sidebar-visibility-show">
                    <span class="d-flex gap-1 h-100">
                      <span class="flex-shrink-0 p-1">
                        <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                          <span class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                          <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                          <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                          <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                        </span>
                      </span>
                      <span class="flex-grow-1">
                        <span class="d-flex h-100 flex-column pt-1 pe-2">
                          <span class="bg-light d-block p-1"></span>
                          <span class="bg-light d-block p-1 mt-auto"></span>
                        </span>
                      </span>
                    </span>
                  </label>
                </div>
                <h5 class="fs-13 text-center mt-2">{{$t("show")}}</h5>
              </BCol>
              <BCol cols="4">
                <div class="form-check card-radio">
                  <input class="form-check-input" type="radio" name="data-sidebar-visibility" id="sidebar-visibility-hidden" value="hidden" v-model="visibility" />
                  <label class="form-check-label p-0 avatar-md w-100 px-2" for="sidebar-visibility-hidden">
                    <span class="d-flex gap-1 h-100">
                      <span class="flex-grow-1">
                        <span class="d-flex h-100 flex-column pt-1 px-2">
                          <span class="bg-light d-block p-1"></span>
                          <span class="bg-light d-block p-1 mt-auto"></span>
                        </span>
                      </span>
                    </span>
                  </label>
                </div>
                <h5 class="fs-13 text-center mt-2">{{$t("hidden")}}</h5>
              </BCol>
            </BRow>
          </div>

          <div v-if="layoutType == 'vertical' || layoutType == 'horizontal'" id="layout-width">
            <h6 class="mt-4 mb-0 fw-semibold text-uppercase">{{$t("layout_width")}}</h6>
            <p class="text-muted">{{$t("choose_fluid_or_boxed_layout")}}</p>

            <BRow>
              <BCol cols="4">
                <div class="form-check card-radio">
                  <input class="form-check-input" type="radio" v-model="layoutWidth" name="data-layout-width" id="layout-width-fluid" value="fluid" />
                  <label class="form-check-label p-0 avatar-md w-100" for="layout-width-fluid">
                    <span class="d-flex gap-1 h-100">
                      <span class="flex-shrink-0">
                        <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                          <span class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                          <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                          <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                          <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                        </span>
                      </span>
                      <span class="flex-grow-1">
                        <span class="d-flex h-100 flex-column">
                          <span class="bg-light d-block p-1"></span>
                          <span class="bg-light d-block p-1 mt-auto"></span>
                        </span>
                      </span>
                    </span>
                  </label>
                </div>
                <h5 class="fs-13 text-center mt-2">{{$t("fluid")}}</h5>
              </BCol>
              <BCol cols="4">
                <div class="form-check card-radio">
                  <input class="form-check-input" type="radio" v-model="layoutWidth" name="data-layout-width" id="layout-width-boxed" value="boxed" />
                  <label class="form-check-label p-0 avatar-md w-100 px-2" for="layout-width-boxed">
                    <span class="d-flex gap-1 h-100 border-start border-end">
                      <span class="flex-shrink-0">
                        <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                          <span class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                          <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                          <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                          <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                        </span>
                      </span>
                      <span class="flex-grow-1">
                        <span class="d-flex h-100 flex-column">
                          <span class="bg-light d-block p-1"></span>
                          <span class="bg-light d-block p-1 mt-auto"></span>
                        </span>
                      </span>
                    </span>
                  </label>
                </div>
                <h5 class="fs-13 text-center mt-2">{{$t("boxed")}}</h5>
              </BCol>
            </BRow>
          </div>

          <!-- <div v-if="layoutType != 'twocolumn'" id="layout-position">
            <h6 class="mt-4 mb-0 fw-semibold text-uppercase">
              Layout Position
            </h6>
            <p class="text-muted">
              Choose Fixed or Scrollable Layout Position.
            </p>

            <div class="btn-group radio" role="group">
              <input type="radio" class="btn-check" name="data-layout-position" id="layout-position-fixed" value="fixed" v-model="position" />
              <label class="btn btn-light w-sm" for="layout-position-fixed">Fixed</label>

              <input type="radio" class="btn-check" name="data-layout-position" id="layout-position-scrollable" value="scrollable" v-model="position" />
              <label class="btn btn-light w-sm ms-0" for="layout-position-scrollable">Scrollable</label>
            </div>
          </div> -->
          <h6 class="mt-4 mb-0 fw-semibold text-uppercase">{{$t("topbar_color")}}</h6>
          <p class="text-muted">{{$t("choose_light_or_dark_topbar_color")}}</p>

          <BRow>
            <BCol cols="4">
              <div class="form-check card-radio">
                <input class="form-check-input" type="radio" name="data-topbar" id="topbar-color-light" value="light" v-model="topbar" />
                <label class="form-check-label p-0 avatar-md w-100" for="topbar-color-light">
                  <span class="d-flex gap-1 h-100">
                    <span class="flex-shrink-0">
                      <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                        <span class="
                                                                              d-block
                                                                              p-1
                                                                              px-2
                                                                              bg-primary-subtle
                                                                              rounded
                                                                              mb-2
                                                                            "></span>
                        <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                        <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                        <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                      </span>
                    </span>
                    <span class="flex-grow-1">
                      <span class="d-flex h-100 flex-column">
                        <span class="bg-light d-block p-1"></span>
                        <span class="bg-light d-block p-1 mt-auto"></span>
                      </span>
                    </span>
                  </span>
                </label>
              </div>
              <h5 class="fs-13 text-center mt-2">{{$t("light")}}</h5>
            </BCol>
            <BCol cols="4">
              <div class="form-check card-radio">
                <input class="form-check-input" type="radio" name="data-topbar" id="topbar-color-dark" value="dark" v-model="topbar" />
                <label class="form-check-label p-0 avatar-md w-100" for="topbar-color-dark">
                  <span class="d-flex gap-1 h-100">
                    <span class="flex-shrink-0">
                      <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                        <span class="
                                                                              d-block
                                                                              p-1
                                                                              px-2
                                                                              bg-primary-subtle
                                                                              rounded
                                                                              mb-2
                                                                            "></span>
                        <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                        <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                        <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                      </span>
                    </span>
                    <span class="flex-grow-1">
                      <span class="d-flex h-100 flex-column">
                        <span class="bg-primary d-block p-1"></span>
                        <span class="bg-light d-block p-1 mt-auto"></span>
                      </span>
                    </span>
                  </span>
                </label>
              </div>
              <h5 class="fs-13 text-center mt-2">{{$t("dark")}}</h5>
            </BCol>
          </BRow>
          <div v-if="layoutType == 'vertical' || (layoutType == 'semibox' && visibility == 'show')" id="sidebar-size">
            <h6 class="mt-4 mb-0 fw-semibold text-uppercase">{{$t("sidebar_size")}}</h6>
            <p class="text-muted">{{$t("choose_a_size_of_sidebar")}}</p>

            <BRow>
              <BCol cols="4">
                <div class="form-check sidebar-setting card-radio">
                  <input class="form-check-input" type="radio" name="data-sidebar-size" id="sidebar-size-default" v-model="sidebarSize" value="lg" />
                  <label class="form-check-label p-0 avatar-md w-100" for="sidebar-size-default">
                    <span class="d-flex gap-1 h-100">
                      <span class="flex-shrink-0">
                        <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                          <span class="
                                                                                d-block
                                                                                p-1
                                                                                px-2
                                                                                bg-primary-subtle
                                                                                rounded
                                                                                mb-2
                                                                              "></span>
                          <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                          <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                          <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                        </span>
                      </span>
                      <span class="flex-grow-1">
                        <span class="d-flex h-100 flex-column">
                          <span class="bg-light d-block p-1"></span>
                          <span class="bg-light d-block p-1 mt-auto"></span>
                        </span>
                      </span>
                    </span>
                  </label>
                </div>
                <h5 class="fs-13 text-center mt-2">{{$t("default")}}</h5>
              </BCol>

              <BCol cols="4">
                <div class="form-check sidebar-setting card-radio">
                  <input class="form-check-input" type="radio" name="data-sidebar-size" id="sidebar-size-compact" v-model="sidebarSize" value="md" />
                  <label class="form-check-label p-0 avatar-md w-100" for="sidebar-size-compact">
                    <span class="d-flex gap-1 h-100">
                      <span class="flex-shrink-0">
                        <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                          <span class="d-block p-1 bg-primary-subtle rounded mb-2"></span>
                          <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                          <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                          <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                        </span>
                      </span>
                      <span class="flex-grow-1">
                        <span class="d-flex h-100 flex-column">
                          <span class="bg-light d-block p-1"></span>
                          <span class="bg-light d-block p-1 mt-auto"></span>
                        </span>
                      </span>
                    </span>
                  </label>
                </div>
                <h5 class="fs-13 text-center mt-2">{{$t("compact")}}</h5>
              </BCol>

              <BCol cols="4">
                <div class="form-check sidebar-setting card-radio">
                  <input class="form-check-input" v-model="sidebarSize" type="radio" name="data-sidebar-size" id="sidebar-size-small" value="sm" />
                  <label class="form-check-label p-0 avatar-md w-100" for="sidebar-size-small">
                    <span class="d-flex gap-1 h-100">
                      <span class="flex-shrink-0">
                        <span class="bg-light d-flex h-100 flex-column gap-1">
                          <span class="d-block p-1 bg-primary-subtle mb-2"></span>
                          <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                          <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                          <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                        </span>
                      </span>
                      <span class="flex-grow-1">
                        <span class="d-flex h-100 flex-column">
                          <span class="bg-light d-block p-1"></span>
                          <span class="bg-light d-block p-1 mt-auto"></span>
                        </span>
                      </span>
                    </span>
                  </label>
                </div>
                <h5 class="fs-13 text-center mt-2">{{$t("small")}}</h5>
              </BCol>

              <BCol cols="4">
                <div class="form-check sidebar-setting card-radio">
                  <input class="form-check-input" v-model="sidebarSize" type="radio" name="data-sidebar-size" id="sidebar-size-small-hover" value="sm-hover" />
                  <label class="form-check-label p-0 avatar-md w-100" for="sidebar-size-small-hover">
                    <span class="d-flex gap-1 h-100">
                      <span class="flex-shrink-0">
                        <span class="bg-light d-flex h-100 flex-column gap-1">
                          <span class="d-block p-1 bg-primary-subtle mb-2"></span>
                          <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                          <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                          <span class="d-block p-1 pb-0 bg-primary-subtle"></span>
                        </span>
                      </span>
                      <span class="flex-grow-1">
                        <span class="d-flex h-100 flex-column">
                          <span class="bg-light d-block p-1"></span>
                          <span class="bg-light d-block p-1 mt-auto"></span>
                        </span>
                      </span>
                    </span>
                  </label>
                </div>
                <h5 class="fs-13 text-center mt-2">{{$t("small_hover_view")}}</h5>
              </BCol>
            </BRow>
          </div>

          <div  v-if="layoutType == 'vertical' || layoutType === 'twocolumn'"  id="sidebar-view">
            <h6 class="mt-4 mb-0 fw-semibold text-uppercase">{{$t("sidebar_view")}}</h6>
            <p class="text-muted">{{$t("choose_default_or_detached_sidebar_view")}}</p>

            <BRow>
              <BCol cols="4">
                <div class="form-check sidebar-setting card-radio">
                  <input class="form-check-input" v-model="sidebarView" type="radio" name="data-layout-style" id="sidebar-view-default" value="default" />
                  <label class="form-check-label p-0 avatar-md w-100" for="sidebar-view-default">
                    <span class="d-flex gap-1 h-100">
                      <span class="flex-shrink-0">
                        <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                          <span class="
                                                                                d-block
                                                                                p-1
                                                                                px-2
                                                                                bg-primary-subtle
                                                                                rounded
                                                                                mb-2
                                                                              "></span>
                          <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                          <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                          <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                        </span>
                      </span>
                      <span class="flex-grow-1">
                        <span class="d-flex h-100 flex-column">
                          <span class="bg-light d-block p-1"></span>
                          <span class="bg-light d-block p-1 mt-auto"></span>
                        </span>
                      </span>
                    </span>
                  </label>
                </div>
                <h5 class="fs-13 text-center mt-2">{{$t("default")}}</h5>
              </BCol>
              <BCol cols="4">
                <div class="form-check sidebar-setting card-radio">
                  <input class="form-check-input" v-model="sidebarView" type="radio" name="data-layout-style" id="sidebar-view-detached" value="detached" />
                  <label class="form-check-label p-0 avatar-md w-100" for="sidebar-view-detached">
                    <span class="d-flex h-100 flex-column">
                      <span class="
                                                                            bg-light
                                                                            d-flex
                                                                            p-1
                                                                            gap-1
                                                                            align-items-center
                                                                            px-2
                                                                          ">
                        <span class="d-block p-1 bg-primary-subtle rounded me-1"></span>
                        <span class="
                                                                              d-block
                                                                              p-1
                                                                              pb-0
                                                                              px-2
                                                                              bg-primary-subtle
                                                                              ms-auto
                                                                            "></span>
                        <span class="d-block p-1 pb-0 px-2 bg-primary-subtle"></span>
                      </span>
                      <span class="d-flex gap-1 h-100 p-1 px-2">
                        <span class="flex-shrink-0">
                          <span class="
                                                                                bg-light
                                                                                d-flex
                                                                                h-100
                                                                                flex-column
                                                                                gap-1
                                                                                p-1
                                                                              ">
                            <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                            <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                            <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                          </span>
                        </span>
                      </span>
                      <span class="bg-light d-block p-1 mt-auto px-2"></span>
                    </span>
                  </label>
                </div>
                <h5 class="fs-13 text-center mt-2">{{ $t("detached") }}</h5>
              </BCol>
            </BRow>
          </div>

          <div v-if="layoutType == 'vertical' || layoutType === 'twocolumn' || (layoutType === 'semibox' && visibility === 'show')" id="sidebar-color">
            <h6 class="mt-4 mb-0 fw-semibold text-uppercase">
              {{$t("sidebar_color")}}
            </h6>
            <p class="text-muted">{{$t("choose_light_or_dark_sidebar_color")}}</p>

            <BRow>
              <BCol cols="4">
                <div class="form-check sidebar-setting card-radio">
                  <input class="form-check-input" v-model="sidebarColor" type="radio" name="data-sidebar" id="sidebar-color-light" value="light" @click="onSideBarColorClick('light')" />
                  <label class="form-check-label p-0 avatar-md w-100" for="sidebar-color-light">
                    <span class="d-flex gap-1 h-100">
                      <span class="flex-shrink-0">
                        <span class="bg-white border-end d-flex h-100 flex-column gap-1 p-1">
                          <span class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                          <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                          <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                          <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                        </span>
                      </span>
                      <span class="flex-grow-1">
                        <span class="d-flex h-100 flex-column">
                          <span class="bg-light d-block p-1"></span>
                          <span class="bg-light d-block p-1 mt-auto"></span>
                        </span>
                      </span>
                    </span>
                  </label>
                </div>
                <h5 class="fs-13 text-center mt-2">{{$t("light")}}</h5>
              </BCol>
              <BCol cols="4">
                <div class="form-check sidebar-setting card-radio">
                  <input class="form-check-input" v-model="sidebarColor" type="radio" name="data-sidebar" id="sidebar-color-dark" value="dark" @click="onSideBarColorClick('light')" />
                  <label class="form-check-label p-0 avatar-md w-100" for="sidebar-color-dark">
                    <span class="d-flex gap-1 h-100">
                      <span class="flex-shrink-0">
                        <span class=" bg-primary d-flex h-100 flex-column gap-1 p-1">
                          <span class="d-block p-1 px-2 bg-white bg-opacity-10 rounded mb-2"></span>
                          <span class="d-block p-1 px-2 pb-0 bg-white bg-opacity-10"></span>
                          <span class="d-block p-1 px-2 pb-0 bg-white bg-opacity-10"></span>
                          <span class="d-block p-1 px-2 pb-0 bg-white bg-opacity-10"></span>
                        </span>
                      </span>
                      <span class="flex-grow-1">
                        <span class="d-flex h-100 flex-column">
                          <span class="bg-light d-block p-1"></span>
                          <span class="bg-light d-block p-1 mt-auto"></span>
                        </span>
                      </span>
                    </span>
                  </label>
                </div>
                <h5 class="fs-13 text-center mt-2">{{$t("dark")}}</h5>
              </BCol>
              <!-- <BCol cols="4">
                <button class="btn btn-link avatar-md w-100 p-0 overflow-hidden border collapsed " type="button" v-b-toggle.collapseBgGradient @click="onSideBarColorClick('gradient')">
                  <span class="d-flex gap-1 h-100">
                    <span class="flex-shrink-0">
                      <span class="bg-vertical-gradient d-flex h-100 flex-column gap-1 p-1">
                        <span class="d-block p-1 px-2 bg-white bg-opacity-10 rounded mb-2"></span>
                        <span class="d-block p-1 px-2 pb-0 bg-white bg-opacity-10"></span>
                        <span class="d-block p-1 px-2 pb-0 bg-white bg-opacity-10"></span>
                        <span class="d-block p-1 px-2 pb-0 bg-white bg-opacity-10"></span>
                      </span>
                    </span>
                    <span class="flex-grow-1">
                      <span class="d-flex h-100 flex-column">
                        <span class="bg-light d-block p-1"></span>
                        <span class="bg-light d-block p-1 mt-auto"></span>
                      </span>
                    </span>
                  </span>
                </button>
                <h5 class="fs-13 text-center mt-2">Gradient</h5>
              </BCol> -->
            </BRow>
            <!-- <BCollapse v-if="showGradients" id="collapseBgGradient">
              <div class="d-flex gap-2 flex-wrap img-switch p-2 px-3 bg-light rounded">

                <div class="form-check sidebar-setting card-radio">
                  <input class="form-check-input" type="radio" v-model="sidebarColor" name="data-sidebar" id="sidebar-color-gradient" value="gradient">
                  <label class="form-check-label p-0 avatar-xs rounded-circle" for="sidebar-color-gradient">
                    <span class="avatar-title rounded-circle bg-vertical-gradient"></span>
                  </label>
                </div>
                <div class="form-check sidebar-setting card-radio">
                  <input class="form-check-input" type="radio" v-model="sidebarColor" name="data-sidebar" id="sidebar-color-gradient-2" value="gradient-2">
                  <label class="form-check-label p-0 avatar-xs rounded-circle" for="sidebar-color-gradient-2">
                    <span class="avatar-title rounded-circle bg-vertical-gradient-2"></span>
                  </label>
                </div>
                <div class="form-check sidebar-setting card-radio">
                  <input class="form-check-input" type="radio" v-model="sidebarColor" name="data-sidebar" id="sidebar-color-gradient-3" value="gradient-3">
                  <label class="form-check-label p-0 avatar-xs rounded-circle" for="sidebar-color-gradient-3">
                    <span class="avatar-title rounded-circle bg-vertical-gradient-3"></span>
                  </label>
                </div>
                <div class="form-check sidebar-setting card-radio">
                  <input class="form-check-input" type="radio" v-model="sidebarColor" name="data-sidebar" id="sidebar-color-gradient-4" value="gradient-4">
                  <label class="form-check-label p-0 avatar-xs rounded-circle" for="sidebar-color-gradient-4">
                    <span class="avatar-title rounded-circle bg-vertical-gradient-4"></span>
                  </label>
                </div>
              </div>
            </BCollapse> -->
          </div>

          <!-- <div v-if="layoutType == 'vertical' || layoutType === 'twocolumn' || (layoutType === 'semibox' && visibility === 'show')" id="sidebar-img">
            <h6 class="mt-4 mb-0 fw-semibold text-uppercase">Sidebar Images</h6>
            <p class="text-muted">Choose a image of Sidebar.</p>

            <div class="d-flex gap-2 flex-wrap img-switch">
              <div class="form-check sidebar-setting card-radio">
                <input class="form-check-input" v-model="sidebarImage" type="radio" name="data-sidebar-img" id="sidebarimg-none" value="none" />
                <label class="form-check-label p-0 avatar-sm h-auto" for="sidebarimg-none">
                  <span class="avatar-md w-auto bg-light d-flex align-items-center justify-content-center">
                    <i class="ri-close-fill fs-20"></i>
                  </span>
                </label>
              </div>

              <div class="form-check sidebar-setting card-radio">
                <input class="form-check-input" v-model="sidebarImage" type="radio" name="data-sidebar-img" id="sidebarimg-01" value="img-1" />
                <label class="form-check-label p-0 avatar-sm h-auto" for="sidebarimg-01">
                  <img src="@assets/images/sidebar/img-1.jpg" alt="" class="avatar-md w-auto object-fit-cover">
                </label>
              </div>

              <div class="form-check sidebar-setting card-radio">
                <input class="form-check-input" v-model="sidebarImage" type="radio" name="data-sidebar-img" id="sidebarimg-02" value="img-2" />
                <label class="form-check-label p-0 avatar-sm h-auto" for="sidebarimg-02">
                  <img src="@assets/images/sidebar/img-2.jpg" alt="" class="avatar-md w-auto object-fit-cover">
                </label>
              </div>
              <div class="form-check sidebar-setting card-radio">
                <input class="form-check-input" v-model="sidebarImage" type="radio" name="data-sidebar-img" id="sidebarimg-03" value="img-3" />
                <label class="form-check-label p-0 avatar-sm h-auto" for="sidebarimg-03">
                  <img src="@assets/images/sidebar/img-3.jpg" alt="" class="avatar-md w-auto object-fit-cover">
                </label>
              </div>
              <div class="form-check sidebar-setting card-radio">
                <input class="form-check-input" v-model="sidebarImage" type="radio" name="data-sidebar-img" id="sidebarimg-04" value="img-4" />
                <label class="form-check-label p-0 avatar-sm h-auto" for="sidebarimg-04">
                  <img src="@assets/images/sidebar/img-4.jpg" alt="" class="avatar-md w-auto object-fit-cover">
                </label>
              </div>
            </div>
          </div> -->

          <!-- <div id="preloader-menu">
            <h6 class="mt-4 mb-0 fw-semibold text-uppercase">Preloader</h6>
            <p class="text-muted">Choose a preloader.</p>

            <BRow>
              <BCol cols="4">
                <div class="form-check sidebar-setting card-radio">
                  <input class="form-check-input" v-model="preloader" type="radio" name="data-preloader" id="preloader-view-custom" value="enable">
                  <label class="form-check-label p-0 avatar-md w-100" for="preloader-view-custom">
                    <span class="d-flex gap-1 h-100">
                      <span class="flex-shrink-0">
                        <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                          <span class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                          <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                          <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                          <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                        </span>
                      </span>
                      <span class="flex-grow-1">
                        <span class="d-flex h-100 flex-column">
                          <span class="bg-light d-block p-1"></span>
                          <span class="bg-light d-block p-1 mt-auto"></span>
                        </span>
                      </span>
                    </span>
                    <div id="status" class="d-flex align-items-center justify-content-center">
                      <div class="spinner-border text-primary avatar-xxs m-auto" role="status">
                        <span class="visually-hidden">Loading...</span>
                      </div>
                    </div>
                  </label>
                </div>
                <h5 class="fs-13 text-center mt-2">Enable</h5>
              </BCol>
              <BCol cols="4">
                <div class="form-check sidebar-setting card-radio">
                  <input class="form-check-input" v-model="preloader" type="radio" name="data-preloader" id="preloader-view-none" value="disable">
                  <label class="form-check-label p-0 avatar-md w-100" for="preloader-view-none">
                    <span class="d-flex gap-1 h-100">
                      <span class="flex-shrink-0">
                        <span class="bg-light d-flex h-100 flex-column gap-1 p-1">
                          <span class="d-block p-1 px-2 bg-primary-subtle rounded mb-2"></span>
                          <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                          <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                          <span class="d-block p-1 px-2 pb-0 bg-primary-subtle"></span>
                        </span>
                      </span>
                      <span class="flex-grow-1">
                        <span class="d-flex h-100 flex-column">
                          <span class="bg-light d-block p-1"></span>
                          <span class="bg-light d-block p-1 mt-auto"></span>
                        </span>
                      </span>
                    </span>
                  </label>
                </div>
                <h5 class="fs-13 text-center mt-2">Disable</h5>
              </BCol>
            </BRow>
          </div> -->

        </div>
      </simplebar>
      <template #footer>
        <BRow>
          <BCol cols="6">
            <BButton type="button" variant="light" class="w-100" id="reset-layout" @click="resetLayout"> {{$t("reset")}} </BButton>
          </BCol>
          <BCol cols="6">
            <BButton type="button" variant="primary" class="w-100"> {{$t("preview")}} </BButton>
          </BCol>
        </BRow>
      </template>
    </BOffcanvas>

    <!-- <div class="customizer-setting toggle-setting">
      <div class="form-check form-switch toggle-direction">                    
        <input class="form-check-input" type="checkbox" id="toggleSwitch" @click="toggleDirection" v-model="toggleValue">
        <label class="form-check-label" for="toggleSwitch">
          <span class="switch-label ltr">LTR</span>
          <span class="switch-label rtl">RTL</span>
        </label>
      </div>
    </div> -->

    <div id="app">
    <!-- Right-side Icon -->
      <div class="icon" @mouseenter="toggleBox">
        <div class="btn-success rounded-pill shadow-lg btn btn-icon btn-lg p-2">
          <i class="ri-menu-4-line mdi-spin mdi-cog-outline fs-22"></i>
        </div>
        <!-- <i class=" ri-menu-4-line"></i>  -->
      </div>

      <!-- Sliding Box -->
      <transition name="slide">
        <div v-if="isBoxVisible" class="sliding-box" @mouseleave="hideBox">
          <Link href="/overall-menu" type="button" :style="{ backgroundColor: 'var(--side_menu)', color:'white'}" class="btn bg-gradient waves-effect waves-light">Overall Menu</Link>
        </div>
      </transition>
    </div>
  </div>
</template>

<style lang="scss">
.b-overlay-wrap {
  .b-overlay {
    z-index: 1005 !important;
  }
}
.rtl {
@import "../../scss/config/default/app-rtl";
direction: rtl;
  
.top-navbar .nav-item .active
{
  font-weight: 600;
  color: var(--side_menu);
}
.page-link.active, .active > .page-link {
    z-index: 3;
    color: var(--vz-pagination-active-color);
    background-color: var(--vz-pagination-active-bg);
    border-color: var(--vz-pagination-active-border-color);
}
.form-check{
  position: relative;
    text-align: right !important;
}
}

.ltr {
    direction: ltr;
}
.toggle-direction{
  width:40px;
  height:24px;
  margin-top: -150px;
  margin-right:10px;
  cursor: pointer;
}

#toggleSwitch{
  width: 55px;
  height: 28px; 
}

.switch-label {
  position: absolute;
  top: 65%;
  transform: translateY(-50%);
  font-size: 12px;
  color: #405189;
  font-weight: bold;
  transition: opacity 0.3s;
}

.switch-label.ltr {
  left: 26px;
}

.switch-label.rtl {
  right: 20px;
  color:white;
  opacity: 0; /* Initially hide RTL */
}

.form-check-input:checked + .form-check-label .switch-label.ltr {
  opacity: 0; /* Hide LTR when checked */
}

.form-check-input:checked + .form-check-label .switch-label.rtl {
  opacity: 1; /* Show RTL when checked */
}
/* Icon on the right side of the page */
.ltr .icon {
  position: fixed;
  right: 20px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 24px;
  cursor: pointer;
  z-index: 99;
  // padding: 5px;
  // background-color: #f8f8f8;
  // border: 1px solid #ccc;
  // border-radius: 10px;
}


.rtl .icon {
  position: fixed;
  left: 20px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 24px;
  cursor: pointer;
  z-index: 99;
  // padding: 5px;
  // background-color: #f8f8f8;
  // border: 1px solid #ccc;
  // border-radius: 10px;
}
/* Hidden box by default */
.ltr .sliding-box {
  position: fixed;
  right: 0;
  top: 50%;
  transform: translateY(-50%);
  width: 150px;
  padding: 15px;
  background-color: #f8f8f8;
  border: 1px solid #ccc;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  z-index: 100;
}


.rtl .sliding-box {
  position: fixed;
  left: 0;
  top: 50%;
  transform: translateY(-50%);
  width: 150px;
  padding: 15px;
  background-color: #f8f8f8;
  border: 1px solid #ccc;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  z-index: 100;
}
/* Sliding animation */
.ltr .slide-enter-active,
.ltr .slide-leave-active {
  transition: all 0.3s ease;
}

.ltr .slide-enter {
  transform: translateX(100%); /* Slide in from right */
}

.ltr .slide-leave-to {
  transform: translateX(100%); /* Slide out to right */
}

.rtl .slide-enter-active,
.rtl .slide-leave-active {
  transition: all 0.3s ease;
}

.rtl .slide-enter {
  transform: translateX(-100%);
}

.rtl .slide-leave-to {
  transform: translateX(-100%); 
}
</style>

  