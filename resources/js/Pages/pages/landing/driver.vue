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
        landingDriver: Object,
        landingHeader: Object,
    },
    computed: {
    splitRequirements() {
      // Check if landingDriver.data is a string and split it by commas
      if (typeof this.landingDriver.req_lists === 'string') {
        return this.landingDriver.req_lists.split(',');
      }
      // Return an empty array if data is not a string
      return [];
    },
    splitVechileRequirements() {
      // Check if landingDriver.data is a string and split it by commas
      if (typeof this.landingDriver.vechile_req_lists === 'string') {
        return this.landingDriver.vechile_req_lists.split(',');
      }
      // Return an empty array if data is not a string
      return [];
    },
    
    splitDocumentRequirements() {
      // Check if landingDriver.data is a string and split it by commas
      if (typeof this.landingDriver.doc_req_lists === 'string') {
        return this.landingDriver.doc_req_lists.split(',');
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
    components: {
        Swiper,
        SwiperSlide,
        CountTo,
        LandingHeader,
        LandingFooter,
        Link,
        router,
    },
    methods: {
        stripHtmlTags(content) {
            const parser = new DOMParser();
            const parsedContent = parser.parseFromString(content, 'text/html');
            return parsedContent.body.textContent || "";
            },
    },
   
};
</script>

<template>
    <div class="layout-wrapper landing">
        <Header />
        <LandingHeader :headers="landingHeader">
            
        </LandingHeader>

        <section class=" mt-5">
        <div class="auth-page-wrapper pt-5">
        <div class="auth-one-bg-position auth-one-bg" style="background: url('/images/taxi-req.png') no-repeat;background-size: cover;" id="auth-particles">
        <div class="bg-overlay"></div>
        <div class="shape">
            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"
            viewBox="0 0 1440 120">
            <path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
            </svg>
        </div>
        </div>

        <div class="auth-page-content">
        <BContainer>
            <BRow>
            <BCol lg="12">
                <div class="text-center mt-sm-5 pt-4">
                <div class="mb-5 text-white-50">
                    <h1 class="display-5 coming-soon-text">
                        {{ landingDriver.hero_title }}
                    </h1>
                </div>
                </div>
            </BCol>
            </BRow>
        </BContainer>
        </div>
        </div>
        </section>

        <!-- <section class="py-5 bg-primary mt-5">
            <div class="bg-overlay bg-overlay-pattern opacity-50"></div>
            <BContainer>
                <BRow class="align-items-center gy-4">
                    <BCol sm>
                        <div>
                            <h4 class="text-white mb-0 fw-semibold">{{ landingDriver.hero_title }}</h4>
                        </div>
                    </BCol>
                    <BCol sm="auto">
                        <div>
                            
                        </div>
                    </BCol>
                </BRow>
            </BContainer>
        </section> -->
    
        <section class="section" id="wallet">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="text-center mb-5">
                            <h2 class="mb-3 fw-semibold lh-base"> {{ landingDriver.driver_heading_1 }}</h2>
                            <p class="text-muted"> {{ stripHtmlTags(landingDriver.driver_para) }}</p>
                        </div>
                    </div><!-- end col -->
                </div><!-- end row -->

                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="card text-center border shadow-none">
                            <div class="card-body py-5 px-4">
                                <div>
                                    <img :src="landingDriver.driver_img_1_url"  alt="download" width="200" class="mb-3 pb-2">
                                </div>                              
                                <h5> {{ landingDriver.driver_title_1 }}</h5>
                                <p class="text-muted pb-1"> {{ stripHtmlTags(landingDriver.driver_para_1) }}</p>
                            </div>
                        </div>
                    </div><!-- end col -->
                    <div class="col-lg-4">
                        <div class="card text-center border shadow-none">
                            <div class="card-body py-5 px-4">
                                <div>
                                    <img :src="landingDriver.driver_img_2_url"  alt="upload" width="200" class="mb-3 pb-2">
                                </div>                                
                                <h5> {{ landingDriver.driver_title_2 }}</h5>
                                <p class="text-muted pb-1">{{ stripHtmlTags(landingDriver.driver_para_2) }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- end col -->
                    <div class="col-lg-4">
                        <div class="card text-center border shadow-none">
                            <div class="card-body py-5 px-4">
                                <img :src="landingDriver.driver_img_3_url" alt="drive" width="200" class="mb-3 pb-2">
                                <h5> {{ landingDriver.driver_title_3 }}</h5>
                                <p class="text-muted pb-1">{{ stripHtmlTags(landingDriver.driver_para_3) }}</p>
                            </div>
                        </div>
                    </div><!-- end col -->
                </div><!-- end row -->
            </div><!-- end container -->
        </section>


<!-- how it work section -->
        <section class="section bg-light">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="text-center mb-5">
                            <h2 class="mb-3 fw-semibold lh-base"> {{ landingDriver.how_it_work_heading }}</h2>
                        </div>
                    </div><!-- end col -->
                </div><!-- end row -->
                <div class="card">
                    <div class="card-body">
                        <!-- Nav tabs -->
                        <ul class="nav nav-pills animation-nav nav-sm-justified nav-lg-justified gap-2 mb-3" role="tablist">
                            <li class="nav-item waves-effect waves-light" role="presentation">
                                <a class="nav-link active show" data-bs-toggle="tab" href="#animation-1" role="tab" aria-selected="true" >
                                    <h4 class="fw-bold ff-secondary  position-relative" >
                                        <span>1</span>
                                    </h4>{{ landingDriver.how_it_work_title_1 }}
                                </a>
                            </li>
                            <li class="nav-item waves-effect waves-light" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#animation-2" role="tab" aria-selected="false" tabindex="-1">
                                    <h4 class="fw-bold ff-secondary  position-relative">
                                        <span>2</span>
                                    </h4>{{ landingDriver.how_it_work_title_2 }}
                                </a>
                            </li>
                            <li class="nav-item waves-effect waves-light" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#animation-3" role="tab" aria-selected="false" tabindex="-1">
                                    <h4 class="fw-bold ff-secondary  position-relative">
                                        <span>3</span>
                                    </h4>{{ landingDriver.how_it_work_title_3 }}
                                </a>
                            </li>
                            <li class="nav-item waves-effect waves-light" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#animation-4" role="tab" aria-selected="false" tabindex="-1">
                                    <h4 class="fw-bold ff-secondary  position-relative">
                                        <span>4</span>
                                    </h4>{{ landingDriver.how_it_work_title_4 }}
                                </a>
                            </li>
                            <li class="nav-item waves-effect waves-light" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#animation-5" role="tab" aria-selected="false" tabindex="-1">
                                    <h4 class="fw-bold ff-secondary  position-relative">
                                        <span>5</span>
                                    </h4>{{ landingDriver.how_it_work_title_5 }}
                                </a>
                            </li>
                            <li class="nav-item waves-effect waves-light" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#animation-6" role="tab" aria-selected="false" tabindex="-1">
                                    <h4 class="fw-bold ff-secondary  position-relative">
                                        <span>6</span>
                                    </h4>{{ landingDriver.how_it_work_title_6 }}
                                </a>
                            </li>
                            <li class="nav-item waves-effect waves-light" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#animation-7" role="tab" aria-selected="false" tabindex="-1">
                                    <h4 class="fw-bold ff-secondary  position-relative">
                                        <span>7</span>
                                    </h4>{{ landingDriver.how_it_work_title_7 }}
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content text-muted">
                            <div class="tab-pane active show" id="animation-1" role="tabpanel">
                                <section class="section">
                                    <div class="container">
                                        <div class="row align-items-center justify-content-lg-between justify-content-center gy-4">
                                            <div class="col-lg-5 col-sm-7">
                                                <div class="mb-5 mb-lg-0 text-center">
                                                    <img :src="landingDriver.how_it_work_img_1_url" width="400px" alt="" class="img-fluid mx-auto rounded-3">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="text-muted">
                                                    <h4 class="mb-3 lh-base">{{ stripHtmlTags(landingDriver.how_it_work_para_1) }}</h4>
                                                </div>
                                            </div>
                                            <!-- end col -->
                                        </div>
                                        <!-- end row -->
                                    </div>
                                    <!-- end container -->
                                </section>
                            </div>
                            <div class="tab-pane" id="animation-2" role="tabpanel">
                                <section class="section">
                                    <div class="container">
                                        <div class="row align-items-center justify-content-lg-between justify-content-center gy-4">
                                            <div class="col-lg-5 col-sm-7">
                                                <div class="mb-5 mb-lg-0 text-center">
                                                    <img :src="landingDriver.how_it_work_img_2_url" width="400px" alt="" class="img-fluid mx-auto rounded-3">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="text-muted">
                                                    <h4 class="mb-3 lh-base">{{ stripHtmlTags(landingDriver.how_it_work_para_2) }}</h4>
                                                </div>
                                            </div>
                                            <!-- end col -->
                                        </div>
                                        <!-- end row -->
                                    </div>
                                    <!-- end container -->
                                </section>
                            </div>
                            <div class="tab-pane" id="animation-3" role="tabpanel">
                                <section class="section">
                                    <div class="container">
                                        <div class="row align-items-center justify-content-lg-between justify-content-center gy-4">
                                            <div class="col-lg-5 col-sm-7">
                                                <div class="mb-5 mb-lg-0 text-center">
                                                    <img :src="landingDriver.how_it_work_img_3_url" width="400px" alt="" class="img-fluid mx-auto rounded-3">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="text-muted">
                                                    <h4 class="mb-3 lh-base">{{ stripHtmlTags(landingDriver.how_it_work_para_3) }}</h4>
                                                </div>
                                            </div>
                                            <!-- end col -->
                                        </div>
                                        <!-- end row -->
                                    </div>
                                    <!-- end container -->
                                </section>
                            </div>
                            <div class="tab-pane" id="animation-4" role="tabpanel">
                                <section class="section">
                                    <div class="container">
                                        <div class="row align-items-center justify-content-lg-between justify-content-center gy-4">
                                            <div class="col-lg-5 col-sm-7">
                                                <div class="mb-5 mb-lg-0 text-center">
                                                    <img :src="landingDriver.how_it_work_img_4_url" width="400px" alt="" class="img-fluid mx-auto rounded-3">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="text-muted">
                                                    <h4 class="mb-3 lh-base">
                                                        {{ stripHtmlTags(landingDriver.how_it_work_para_4) }}</h4>
                                                </div>
                                            </div>
                                            <!-- end col -->
                                        </div>
                                        <!-- end row -->
                                    </div>
                                    <!-- end container -->
                                </section>
                            </div>
                            <div class="tab-pane" id="animation-5" role="tabpanel">
                                <section class="section">
                                    <div class="container">
                                        <div class="row align-items-center justify-content-lg-between justify-content-center gy-4">
                                            <div class="col-lg-5 col-sm-7">
                                                <div class="mb-5 mb-lg-0 text-center">
                                                    <img :src="landingDriver.how_it_work_img_5_url" width="400px" alt="" class="img-fluid mx-auto rounded-3">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="text-muted">
                                                    <h4 class="mb-3 lh-base">{{ stripHtmlTags(landingDriver.how_it_work_para_5) }}</h4>
                                                </div>
                                            </div>
                                            <!-- end col -->
                                        </div>
                                        <!-- end row -->
                                    </div>
                                    <!-- end container -->
                                </section>
                            </div>
                            <div class="tab-pane" id="animation-6" role="tabpanel">
                                <section class="section">
                                    <div class="container">
                                        <div class="row align-items-center justify-content-lg-between justify-content-center gy-4">
                                            <div class="col-lg-5 col-sm-7">
                                                <div class="mb-5 mb-lg-0 text-center">
                                                    <img :src="landingDriver.how_it_work_img_6_url" width="400px" alt="" class="img-fluid mx-auto rounded-3">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="text-muted">
                                                    <h4 class="mb-3 lh-base">{{ stripHtmlTags(landingDriver.how_it_work_para_6) }}</h4>
                                                </div>
                                            </div>
                                            <!-- end col -->
                                        </div>
                                        <!-- end row -->
                                    </div>
                                    <!-- end container -->
                                </section>
                            </div>
                            <div class="tab-pane" id="animation-7" role="tabpanel">
                                <section class="section">
                                    <div class="container">
                                        <div class="row align-items-center justify-content-lg-between justify-content-center gy-4">
                                            <div class="col-lg-5 col-sm-7">
                                                <div class="mb-5 mb-lg-0 text-center">
                                                    <img :src="landingDriver.how_it_work_img_7_url" width="400px" alt="" class="img-fluid mx-auto rounded-3">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="text-muted">
                                                    <h4 class="mb-3 lh-base">{{ stripHtmlTags(landingDriver.how_it_work_para_7) }}</h4>
                                                </div>
                                            </div>
                                            <!-- end col -->
                                        </div>
                                        <!-- end row -->
                                    </div>
                                    <!-- end container -->
                                </section>
                            </div>
                        </div>
                    </div><!-- end card-body -->
                </div>
            </div><!-- end container -->
        </section>
<!-- how it work section end-->



        <section class="section">
                <div class="container">
                    <div class="row align-items-center justify-content-lg-between justify-content-center gy-4">
                        <div class="col-lg-5 col-sm-7">
                            <div class="about-img-section mb-5 mb-lg-0 text-center">

                                <img :src="landingDriver.req_img_url" alt="about" class="img-fluid mx-auto rounded-3">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="text-muted">
                                <h1 class="mb-3 lh-base">{{ landingDriver.req_heading }} </h1>
                                <h2 class="ff-secondary fs-16 mb-2">{{ landingDriver.req_title}}</h2>

                                <div class="vstack gap-2">
                                    <div class="d-flex align-items-center" v-for="(item, index) in splitRequirements" :key="index">
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
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- end row -->
                </div>
                <!-- end container -->
            </section>

            
            <section class="section">
                <div class="container">
                    <div class="row align-items-center justify-content-lg-between justify-content-center gy-4">
                        <div class="col-lg-6">
                            <div class="text-muted">
                                <h1 class="mb-3 lh-base">{{ landingDriver.vechile_req_title }} </h1>

                                <div class="vstack gap-2">
                                    <div class="d-flex align-items-center" v-for="(item, index) in splitVechileRequirements" :key="index">
                                        <div class="flex-shrink-0 me-2">
                                            <div class="avatar-xs icon-effect">
                                                <div class="avatar-title bg-transparent rounded-circle h2" style="color: var(--landing_header_act_text);">
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
                        </div>
                        <!-- end col -->
                        <div class="col-lg-5 col-sm-7">
                            <div class="about-img-section mb-5 mb-lg-0 text-center">

                                <img :src="landingDriver.vechile_req_img_url" alt="about" class="img-fluid mx-auto rounded-3">
                            </div>
                        </div>
                        
                    </div>
                    <!-- end row -->
                </div>
                <!-- end container -->
            </section>


            <section class="section">
                <div class="container">
                    <div class="row align-items-center justify-content-lg-between justify-content-center gy-4">
                        <div class="col-lg-5 col-sm-7">
                            <div class="about-img-section mb-5 mb-lg-0 text-center">

                                <img :src="landingDriver.doc_req_img_url"alt="about" class="img-fluid mx-auto rounded-3">
                            </div>
                        </div>
                        <div class="col-lg-6 doc-req">
                            <div class="text-muted">
                                <h1 class="mb-3 lh-base">{{ landingDriver.doc_req_title }} </h1>

                                <div class="vstack gap-2">
                                    <div class="d-flex align-items-center" v-for="(item, index) in splitDocumentRequirements" :key="index">
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
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- end row -->
                </div>
                <!-- end container -->
            </section>
       


        <LandingFooter></LandingFooter>
    </div>
</template>
<style>
.icon-effect::before {
    background-color:  rgb(from var(--landing_header_act_text) r g b / 33%);
}
.nav-pills .nav-link.active h4, .nav-pills .show > .nav-link h4{
    color:white;
}
.auth-one-bg .bg-overlay {
    background: linear-gradient(to right, var(--landing_header_act_text), var(--landing_header_act_text));
    opacity: 0.9;
}
.rtl .auth-one-bg .bg-overlay {
    background: linear-gradient(to right, var(--landing_header_act_text), var(--landing_header_act_text));
    opacity: 0.9;
}
.animation-nav li a::before {
    content: "";
    position: absolute;
    top: 0;
    width: 0;
    right: 0;
    height: 100%;
    transition: width 0.4s cubic-bezier(0.51, 0.18, 0, 0.88) 0.1s;
     background-color: var(--landing_header_act_text); 
    z-index: -1;
}
.about-img-section::before {
    content: "";
    position: absolute;
    left: 20px;
    right: -20px;
    top: -20px;
    bottom: 20px;
    border: 1px solid  var(--landing_header_act_text); ;
    z-index: 0;
    border-radius: 0.25rem;
}
.rtl .animation-nav li a::before {
    content: "";
    position: absolute;
    top: 0;
    width: 0;
    right: 0;
    height: 100%;
    transition: width 0.4s cubic-bezier(0.51, 0.18, 0, 0.88) 0.1s;
     background-color: var(--landing_header_act_text); 
    z-index: -1;
}
.rtl .about-img-section::before {
    content: "";
    position: absolute;
    left: 20px;
    right: -20px;
    top: -20px;
    bottom: 20px;
    border: 1px solid  var(--landing_header_act_text); ;
    z-index: 0;
    border-radius: 0.25rem;
}

</style>