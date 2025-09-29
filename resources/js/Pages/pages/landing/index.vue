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
import { Link, router } from '@inertiajs/vue3';



export default {
    
    props: {
        landingHome: Object,
        landingHeader: Object,
    },
    computed: {
    splitServices() {
      // Check if landingHome.data is a string and split it by commas
      if (typeof this.landingHome.services === 'string') {
        return this.landingHome.services.split(',');
      }
      // Return an empty array if data is not a string
      return [];
    },
    splitAbout() {
      // Check if landingHome.data is a string and split it by commas
      if (typeof this.landingHome.about_lists === 'string') {
        return this.landingHome.about_lists.split(',');
      }
      // Return an empty array if data is not a string
      return [];
    },
  },
    data() {
        return {
            Autoplay, Navigation, Pagination,
        };
    },
    methods: {
        stripHtmlTags(content) {
            const parser = new DOMParser();
            const parsedContent = parser.parseFromString(content, 'text/html');
            return parsedContent.body.textContent || "";
            },
    },

    components: {
        Swiper,
        SwiperSlide,
        CountTo,
        LandingHeader,
        LandingFooter,
        Link,
        router,
    },
};
</script>

<template>
    <div class="layout-wrapper landing">
        <Header />
        <LandingHeader :headers="landingHeader">
            
        </LandingHeader>
        
        <div class="vertical-overlay" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent.show"></div>
        <section class="section pb-0 hero-section" id="hero">
            <div class="bg-overlay bg-overlay-pattern"></div>
            <BContainer class="download-app">
                <BRow class="justify-content-center">
                    <BCol lg="8" sm="10">
                        <div class="text-center mt-lg-5 pt-5">
                            <h1 class="display-6 fw-semibold mb-3 lh-base">{{ landingHome.hero_title }}</h1>
                            <div class="d-flex align-items-center justify-content-center gap-4 mb-5">
                            <div class="d-block">
                            <h2  style="color: var(--landing_header_act_text);">{{ landingHeader.user_app }}</h2>

                            <div class="d-flex gap-2 justify-content-center mt-4">
                                <BLink :href="landingHome.hero_user_link_android" target="_blank" class="btn btn-primary">{{ landingHeader.user_play }} <i
                                        class="ri-google-play-fill align-middle ms-1"></i></BLink>
                                <BLink :href="landingHome.hero_user_link_apple" target="_blank" class="btn btn-dark">{{ landingHeader.user_apple }} <i
                                        class="ri-apple-fill align-middle ms-1"></i></BLink>
                            </div>
                            </div>
                        <div class="d-block">
                            <h2 style="color: var(--landing_header_act_text);">{{ landingHeader.driver_app }}</h2>

                            <div class="d-flex gap-2 justify-content-center mt-4">
                                <BLink :href="landingHome.hero_driver_link_android" target="_blank" class="btn btn-primary">{{ landingHeader.driver_play }} <i
                                        class="ri-google-play-fill align-middle ms-1"></i></BLink>
                                <BLink :href="landingHome.hero_driver_link_apple" target="_blank" class="btn btn-dark">{{ landingHeader.driver_apple }} <i
                                        class="ri-apple-fill align-middle ms-1"></i></BLink>
                            </div>
                        </div>
                            </div>
                        </div>
                    </BCol>
                </BRow>
            </BContainer>
        </section>

       

        <section class="section" id="services">
            <BContainer>
                <BRow class="justify-content-center">
                    <BCol lg="8">
                        <div class="text-center mb-5">
                            <h1 class="mb-3 ff-secondary fw-semibold lh-base">{{ landingHome.feature_heading }}</h1>
                            <p class="text-muted">{{ stripHtmlTags(landingHome.feature_para) }}</p>
                        </div>
                    </BCol>
                </BRow>

                <BRow class="g-3 advantages">
                    <BCol lg="6">
                        <div class="d-flex p-3">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar-sm icon-effect">
                                    <div class="avatar-title bg-transparent  rounded-circle" style="color: var(--landing_header_act_text);">
                                        <i class="ri-pencil-ruler-2-line fs-36"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-2">
                                <h5 class="fs-18">{{ landingHome.feature_sub_heading_1 }}</h5>
                                <p class="text-muted my-3 ff-secondary">{{ stripHtmlTags(landingHome.feature_sub_para_1) }}</p>
                               
                            </div>
                        </div>
                    </BCol>
                    <BCol lg="6">
                        <div class="d-flex p-3">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar-sm icon-effect">
                                    <div class="avatar-title bg-transparent  rounded-circle" style="color: var(--landing_header_act_text);">
                                        <i class="ri-palette-line fs-36"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="fs-18">{{ landingHome.feature_sub_heading_2 }}</h5>
                                <p class="text-muted my-3 ff-secondary">{{ stripHtmlTags(landingHome.feature_sub_para_2) }}</p>
                                
                            </div>
                        </div>
                    </BCol>
                    <BCol lg="6">
                        <div class="d-flex p-3">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar-sm icon-effect">
                                    <div class="avatar-title bg-transparent  rounded-circle" style="color: var(--landing_header_act_text);">
                                        <i class="ri-lightbulb-flash-line fs-36"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="fs-18">{{ landingHome.feature_sub_heading_3 }}</h5>
                                <p class="text-muted my-3 ff-secondary">{{ stripHtmlTags(landingHome.feature_sub_para_3) }}</p>
                                
                            </div>
                        </div>
                    </BCol>
                    <BCol lg="6">
                        <div class="d-flex p-3">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar-sm icon-effect">
                                    <div class="avatar-title bg-transparent  rounded-circle" style="color: var(--landing_header_act_text);">
                                        <i class="ri-customer-service-line fs-36"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="fs-18">{{ landingHome.feature_sub_heading_4 }}</h5>
                                <p class="text-muted my-3 ff-secondary">{{ stripHtmlTags(landingHome.feature_sub_para_4) }}</p>
                               
                            </div>
                        </div>
                    </BCol>
                </BRow>
            </BContainer>
        </section>



        <section class="section">
            <BContainer>
                <BRow class="align-items-center gy-4">
                    <BCol lg="6" order="2" order-lg="1" class="digitalservice">
                        <div class="text-muted">
                            <h5 class="fs-12 text-uppercase" style="color: var(--landing_header_act_text);">{{ landingHome.service_heading_1 }}</h5>
                            <h4 class="mb-3">{{ landingHome.service_heading_2 }}</h4>
                            <p class="mb-4 ff-secondary">{{ stripHtmlTags(landingHome.service_para) }}</p>

                            <BRow>
                                <BCol sm="12">
                                    <div class="vstack gap-2">
                                        <div class="d-flex align-items-center" v-for="(item, index) in splitServices" :key="index">
                                            <div class="flex-shrink-0 me-2">
                                                <div class="avatar-xs icon-effect">
                                                    <div class="avatar-title bg-transparent rounded-circle h2" style="color: var(--landing_header_act_text);">
                                                        <i class="ri-check-fill"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h5 class="fs-14 mb-0">{{ stripHtmlTags(item) }}</h5>
                                            </div>
                                        </div>
                                    </div>
                                </BCol>
                            </BRow>
                        </div>
                    </BCol>
                    <BCol lg="6" sm="7" cols="10" order="1" order-lg="2" class="ms-auto">
                        <div>
                            <img :src="landingHome.service_img_url" alt="" class="img-fluid">
                        </div>
                    </BCol>
                </BRow>

                <BRow class="align-items-center mt-5 pt-lg-5 gy-4">
                    <BCol lg="6" sm="7" cols="10" class="mx-auto">
                        <div>
                            <img :src="landingHome.about_img_url" alt="" class="img-fluid">
                        </div>
                    </BCol>
                    <BCol lg="6" class="aboutcompany">
                        <div class="text-muted ps-lg-5">
                            <h5 class="fs-12 text-uppercase" style="color: var(--landing_header_act_text);">{{ landingHome.about_title_1 }}</h5>
                            <h4 class="mb-3">{{ landingHome.about_title_2 }}</h4>
                            <p class="mb-4">{{ stripHtmlTags(landingHome.about_para) }}</p>

                            <div class="vstack gap-2">
                                <div class="d-flex align-items-center" v-for="(item, index) in splitAbout" :key="index">
                                    <div class="flex-shrink-0 me-2">
                                        <div class="avatar-xs icon-effect">
                                            <div class="avatar-title bg-transparent  rounded-circle h2" style="color: var(--landing_header_act_text);">
                                                <i class="ri-check-fill"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="mb-0">{{ stripHtmlTags(item) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </BCol>
                </BRow>
            </BContainer>
        </section>

        <section class="section bg-light" id="team">
            <BContainer>
                <BRow>
                    <BCol lg="4" sm="6">
                        <BCard n-body>
                            <BCardBody class="text-center p-4">
                                <div class="avatar-xxl mx-auto mb-4 position-relative">
                                    <img :src="landingHome.box_img_1_url" alt="" class="img-fluid rounded-circle">
                                </div>
                                <h5 class="mb-1">{{ stripHtmlTags(landingHome.box_para_1) }}</h5>
                            </BCardBody>
                        </BCard>
                    </BCol>
                    <BCol lg="4" sm="6">
                        <BCard n-body>
                            <BCardBody class="text-center p-4">
                                <div class="avatar-xxl mx-auto mb-4 position-relative">
                                    <img :src="landingHome.box_img_2_url" alt="" class="img-fluid rounded-circle">
                                </div>
                                <h5 class="mb-1">{{ stripHtmlTags(landingHome.box_para_2) }}</h5>
                            </BCardBody>
                        </BCard>
                    </BCol>
                    <BCol lg="4" sm="6">
                        <BCard n-body>
                            <BCardBody class="text-center p-4">
                                <div class="avatar-xxl mx-auto mb-4 position-relative">
                                    <img :src="landingHome.box_img_3_url" alt="" class="img-fluid rounded-circle">
                                </div>
                                <h5 class="mb-1">{{ stripHtmlTags(landingHome.box_para_3) }}</h5>
                            </BCardBody>
                        </BCard>
                    </BCol>
                </BRow>
            </BContainer>
        </section>


        <section class="section">
            <BContainer>
                <BRow class="justify-content-center">
                    <BCol lg="8">
                        <div class="text-center mb-5">
                            <h3 class="mb-3 fw-semibold">{{ landingHome.drive_heading }}</h3>
                        </div>
                    </BCol>
                </BRow>

                <BRow class="text-center whyusTagxi">
                    <BCol lg="4">
                        <div class="process-card mt-4">
                            <div class="process-arrow-img d-none d-lg-block">
                                <!-- <img src="@/assets/images/landing/process-arrow-img.png" alt="" class="img-fluid"> -->
                            </div>
                            <div class="avatar-sm icon-effect mx-auto mb-4">
                                <div class="avatar-title bg-transparent rounded-circle h1" style="color: var(--landing_header_act_text);">
                                    <i class="ri-quill-pen-line"></i>
                                </div>
                            </div>

                            <h5>{{ landingHome.drive_title_1 }}</h5>
                            <p class="text-muted ff-secondary">{{ stripHtmlTags(landingHome.drive_para_1) }}</p>
                        </div>
                    </BCol>
                    <BCol lg="4">
                        <div class="process-card mt-4">
                            <div class="process-arrow-img d-none d-lg-block">
                                <!-- <img src="@/assets/images/landing/process-arrow-img.png" alt="" class="img-fluid"> -->
                            </div>
                            <div class="avatar-sm icon-effect mx-auto mb-4">
                                <div class="avatar-title bg-transparent  rounded-circle h1" style="color: var(--landing_header_act_text);">
                                    <i class="ri-user-follow-line"></i>
                                </div>
                            </div>

                            <h5>{{ landingHome.drive_title_2 }}</h5>
                            <p class="text-muted ff-secondary">{{ stripHtmlTags(landingHome.drive_para_2) }}</p>
                        </div>
                    </BCol>
                    <BCol lg="4">
                        <div class="process-card mt-4">
                            <div class="avatar-sm icon-effect mx-auto mb-4">
                                <div class="avatar-title bg-transparent  rounded-circle h1" style="color: var(--landing_header_act_text);">
                                    <i class="ri-book-mark-line"></i>
                                </div>
                            </div>

                            <h5>{{ landingHome.drive_title_3 }}</h5>
                            <p class="text-muted ff-secondary">{{ stripHtmlTags(landingHome.drive_para_3) }}</p>
                        </div>
                    </BCol>
                </BRow>
            </BContainer>
        </section>



        <section class="section bg-light py-5" id="features">
            <BContainer>
                <BRow class="align-items-center gy-4">
                    <BCol lg="6" sm="7" class="mx-auto">
                        <div>
                            <img :src="landingHome.service_area_img_url" alt="location" class="img-fluid mx-auto">
                        </div>
                    </BCol>
                    <BCol lg="6" class="servicelocation">
                        <div class="text-muted">
                            <div class="avatar-sm icon-effect mb-4">
                                <div class="avatar-title bg-transparent rounded-circle h1" style="color: var(--landing_header_act_text);">
                                    <i class="ri-collage-line fs-36"></i>
                                </div>
                            </div>
                            <h3 class="mb-3 fs-20">{{ landingHome.service_area_title }}</h3>
                            <p class="mb-4 ff-secondary fs-16">{{ stripHtmlTags(landingHome.service_area_para) }}</p>

                        </div>
                    </BCol>
                </BRow>
            </BContainer>
        </section>

<LandingFooter></LandingFooter>
    </div>
</template>

<style>
.icon-effect::before {
    background-color:  rgb(from var(--landing_header_act_text) r g b / 33%);
}
</style>