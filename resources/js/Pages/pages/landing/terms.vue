<script>
import { CountTo } from "vue3-count-to";

import { Autoplay, Navigation, Pagination } from "swiper/modules";
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";
import "swiper/css/autoplay";
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import LandingHeader from "@/Components/LandingHeader.vue";
import LandingFooter from "@/Components/LandingFooter.vue";

export default {
  props: {
        landingQuickLink: Object,
        landingHeader: Object,
    },
    data() {
        return {
            Autoplay, Navigation, Pagination,
            landingQuickLinks: {
              terms: '', // This will hold the HTML content from your database
            },
        };
    },
    created() {
    this.fetchTermContent();
  },
  methods: {
      fetchTermContent() {
        const params = new URLSearchParams();
        params.append('locale', this.$page.props.landingHeader.locale);

        axios.get('/api/terms-content', { params }).then(response => {
            this.landingQuickLinks.terms = response.data.terms;
        }).catch(error => {
            console.error("Error fetching terms content:", error);
        });
      },
      changeLocale(event) {
        const localeId = event.target.value;
        this.selectedLocale = this.locales[localeId];

        // Update URL without reloading the page
        const params = new URLSearchParams(window.location.search);
        params.set('locale', this.selectedLocale);
        window.history.replaceState({}, '', `${window.location.pathname}?${params}`);

        // Fetch the content for the new locale
        this.fetchTermContent();
    }
    },
    components: {
        Swiper,
        SwiperSlide,
        CountTo,
        LandingHeader,
        LandingFooter,
    },
   
};
</script>

<template>
    <div class="layout-wrapper landing">
        <Header />
        <LandingHeader :headers="landingHeader">
            
          </LandingHeader>

<section class="py-5 mt-5">
    <BContainer>
<PageHeader title="Terms & Conditions" pageTitle="Pages" />

<BRow class="justify-content-center">
  <BCol col lg="10">
    <BCard no-body>
      <div class="bg-warning-subtle position-relative">
        <BCardBody class="p-5">
          <div class="text-center">
            <h3 style="color: var(--landing_footer_text);">{{ landingQuickLink.terms_title }}</h3>
          </div>
        </BCardBody>
        <div class="shape">
          <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"
            xmlns:svgjs="http://svgjs.com/svgjs" width="1440" height="60" preserveAspectRatio="none"
            viewBox="0 0 1440 60">
            <g mask="url(&quot;#SvgjsMask1001&quot;)" fill="none">
              <path d="M 0,4 C 144,13 432,48 720,49 C 1008,50 1296,17 1440,9L1440 60L0 60z"
                style="fill: var(--vz-secondary-bg);"></path>
            </g>
            <defs>
              <mask id="SvgjsMask1001">
                <rect width="1440" height="60" fill="#ffffff"></rect>
              </mask>
            </defs>
          </svg>
        </div>
      </div>
      <BCardBody class="p-4">
        <div class="d-flex">
          <div class="flex-shrink-0 me-3">
            <CheckCircleIcon class="text-success icon-dual-success icon-xs" />
          </div>
          <div class="flex-grow-1">
            <div v-html="landingQuickLinks.terms"></div>
        </div>
    </div>


      </BCardBody>
    </BCard>
  </BCol>
</BRow>
</BContainer>
</section>

        <LandingFooter></LandingFooter>
    </div>
</template>

<style>
.bg-warning-subtle {
    background-color: var(--landing_header_act_text) !important; 
    
}
.rtl .bg-warning-subtle {
    background-color: var(--landing_header_act_text) !important; 
    
}
</style>