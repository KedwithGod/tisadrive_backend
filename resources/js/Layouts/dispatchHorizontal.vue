<script>
import { Link } from '@inertiajs/vue3';
import NavBar from "@/Components/dispatch-nav-bar.vue";
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
    Footer,
    Link
  },
};
</script>

<template>
  <div>
    <div id="layout-wrapper">
      <NavBar />
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
    </div>
  </div>
</template>