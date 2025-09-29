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
        landingAbouts: Object,
        landingHeader: Object,
    },
    mounted() {
    if (this.landingAbouts.team_members) {
      try {
        this.parsedTeamMembers = JSON.parse(this.landingAbouts.team_members);
        const storagePath = 'storage/uploads/website/images/';

        // Update each team member's image URL
        this.parsedTeamMembers.forEach(member => {
            member.team_member_image = `${storagePath}${member.team_member_image}`;
        });
      } catch (error) {
        console.error("Error parsing team_members:", error);
      }
    }

    if (this.landingAbouts.testimonial_content) {
      try {
        this.parsedTestimonial = JSON.parse(this.landingAbouts.testimonial_content);
      } catch (error) {
        console.error("Error parsing testimonial_content:", error);
      }
    }
  },
    computed: {        
        aboutusLists() {
      // Check if landingAbouts.data is a string and split it by commas
      if (typeof this.landingAbouts.about_lists === 'string') {
        return this.landingAbouts.about_lists.split(',');
      }
      // Return an empty array if data is not a string
      return [];
    },
  },
    data() {
        return {
            Autoplay, Navigation, Pagination,
            parsedTeamMembers: [],
            parsedTestimonial: []
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
                                            {{ landingAbouts.hero_title }}
                                        </h1>
                                    </div>
                                </div>
                            </BCol>
                        </BRow>
                    </BContainer>
                </div>
            </div>
        </section>

        <section class="section">
            <BContainer>
                <BRow class="align-items-center">
                    <BCol lg="6" sm="7" cols="10" class="mx-auto">
                        <div>
                            <img :src="landingAbouts.about_img_url" alt="" class="img-fluid">
                        </div>
                    </BCol>
                    <BCol lg="6" class="aboutcompany">
                        <div class="text-muted ps-lg-5">
                            <h5 class="fs-12 text-uppercase" style="color: var(--landing_header_act_text);"> {{ landingAbouts.about_heading }}</h5>
                            <h4 class="mb-3"> {{ landingAbouts.about_title }}</h4>
                            <p class="mb-4">{{ stripHtmlTags(landingAbouts.about_para) }}</p>

                            <div class="vstack gap-2">
                                    <div class="d-flex align-items-center" v-for="(item, index) in aboutusLists" :key="index">
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
        <section  class="section bg-light">
            <BContainer>
                <BRow>
                    <BCol lg="6" class="ceo-section" >
                        <div class="text-muted">
                            <div class="d-flex">
                                <img :src="landingAbouts.ceo_img_url" alt="ceo_img" class="rounded-circle ceo-img"/>
                                <div class=" mt-3 ms-4">
                                <h3>{{ landingAbouts.ceo_name }}</h3>
                                <h5>{{ landingAbouts.ceo_title }}</h5>  
                                <img :src="landingAbouts.signature_url" alt="ceo_sign" class="avatar-lg " />                                   
                            </div> 
                            </div>                            
                        </div>                        
                    </BCol>
                    <BCol lg="5">
                        <div class="mt-3">                             
                            <p>
                                {{ stripHtmlTags(landingAbouts.ceo_para) }}
                            </p>                            
                        </div>
                    </BCOl>
                </BRow>
            </BContainer>
        </section>

        <section class="section">
            <BContainer>
                <BRow class="justify-content-center">
                    <BCol lg="8">
                        <div class="text-center mb-5">
                            <h3 class="mb-3 fw-semibold">{{ landingAbouts.vision_mision_heading }}</h3>
                        </div>
                    </BCol>
                </BRow>
                <BRow class="text-center">
                    <BCol lg="6">
                        <div class="process-card mt-4">
                            <div class="process-arrow-img d-none d-lg-block">
                                <!-- <img src="@/assets/images/landing/process-arrow-img.png" alt="" class="img-fluid"> -->
                            </div>
                            <div class="avatar-sm icon-effect mx-auto mb-4">
                                <div class="avatar-title bg-transparent rounded-circle h1" style="color: var(--landing_header_act_text);">
                                    <i class="ri-user-follow-line"></i>
                                </div>
                            </div>

                            <h5>{{ landingAbouts.vision_title }}</h5>
                            <p class="text-muted ff-secondary">
                                {{ stripHtmlTags(landingAbouts.vision_para) }}
                            </p>
                        </div>
                    </BCol>
                    <BCol lg="6">
                        <div class="process-card mt-4">
                            <div class="process-arrow-img d-none d-lg-block">
                                <!-- <img src="@/assets/images/landing/process-arrow-img.png" alt="" class="img-fluid"> -->
                            </div>
                            <div class="avatar-sm icon-effect mx-auto mb-4">
                                <div class="avatar-title bg-transparent rounded-circle h1" style="color: var(--landing_header_act_text);">
                                    <i class="ri-user-follow-line"></i>
                                </div>
                            </div>

                            <h5>{{ landingAbouts.mission_title }}</h5>
                            <p class="text-muted ff-secondary">
                                {{ stripHtmlTags(landingAbouts.mission_para) }}
                            </p>
                        </div>
                    </BCol>
                </BRow>
            </BContainer>
        </section>

      
         <!-- start review -->
         <section class="section bg-primary" id="reviews">
            <div class="bg-overlay bg-overlay-pattern"></div>
            <BContainer>
                <BRow class="justify-content-center">
                    <BCol lg="10" >
                        <div class="text-center"  >
                            <div>
                                <i class="ri-double-quotes-l text-success display-3"></i>
                            </div>
                            <h3 class="text-success mb-5">{{(landingAbouts.testimonial_heading)}}</h3>

                            <div class="client-review-swiper rounded">
                                <swiper class="navigation-swiper rounded testimonial" :loop="true"
                                    :modules="[Autoplay,Navigation, Pagination]"
                                    :autoplay="{ delay: 2500, disableOnInteraction: false }"
                                    :navigation="{ nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' }"
                                    :pagination="{ clickable: true, el: '.swiper-pagination' }">
                                    <swiper-slide v-for="(testimonial_content, index) in parsedTestimonial" :key="index" >
                                        <div class="swiper-slide">
                                            <BRow class="justify-content-center">
                                                <BCol cols="10">
                                                    <div class="text-white-50">
                                                        <p class="fs-20 ff-secondary mb-4">" {{ stripHtmlTags(testimonial_content.testimonial_para ) }} "</p>

                                                        <div>
                                                            <h5 class="text-white">{{testimonial_content.testimonial_title_1 }} </h5>
                                                            <p>- {{testimonial_content.testimonial_title_2 }} </p>
                                                        </div>
                                                    </div>
                                                </BCol>
                                            </BRow>
                                        </div>
                                    </swiper-slide>
                                    <div class="swiper-button-next bg-white rounded-circle"></div>
                                    <div class="swiper-button-prev bg-white rounded-circle"></div>
                                    <!-- <div class="swiper-pagination position-relative mt-2"></div> -->
                                </swiper>
                            </div>
                        </div>
                    </BCol>
                </BRow>
            </BContainer>
        </section>
        <!-- end review -->


        <section class="section bg-light">
            <BContainer>
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="text-center mb-5">
                            <h3 class="mb-3 fw-semibold">{{ landingAbouts.team_title }}</h3>
                            <p class="text-muted mb-4 ff-secondary"> {{ stripHtmlTags(landingAbouts.team_para) }}</p>
                        </div>
                    </div>
                </div>
                <div class="teamMemeber-swiper rounded">
                    <swiper class="navigation-swiper rounded teamMember" :loop="true"
                        :modules="[Autoplay, Navigation, Pagination]"
                        :autoplay="{ delay: 5500, disableOnInteraction: false }"
                        :navigation="{ nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' }"
                        :pagination="{ clickable: true, el: '.swiper-pagination' }"   
                        :breakpoints="{
                            320: { // Mobile screens
                            slidesPerView: 1,
                            spaceBetween: 10
                            },
                            640: { // Small tablets
                            slidesPerView: 2,
                            spaceBetween: 15
                            },
                            768: { // Tablets
                            slidesPerView: 3,
                            spaceBetween: 20
                            },
                            1024: { // Desktops and larger screens
                            slidesPerView: 4,
                            spaceBetween: 10
                            }
                        }">
                        <swiper-slide  v-for="(teamMember, index) in parsedTeamMembers" :key="index">
                            <div class="swiper-slide">
                                <BRow class="justify-content-center">
                                    <BCol lg="10">
                                        <div class="card">
                                            <div class="card-body text-center p-4">
                                            <div class=" mx-auto mb-4 position-relative">
                                                <img :src="teamMember.team_member_image" alt="Team member image" class=" avatar-xl img-fluid rounded-circle" />
                                            </div>
                                            <h5 class="mb-1">{{ teamMember.team_member_name }}</h5>
                                            <p class="text-muted mb-0 ff-secondary">{{ teamMember.team_member_posision }}</p>
                                            </div>
                                        </div>
                                    </BCol>
                                </BRow>
                            </div>
                        </swiper-slide>
                        <div class="swiper-button-next bg-white rounded-circle"></div>
                        <div class="swiper-button-prev bg-white rounded-circle"></div>
                        <!-- <div class="swiper-pagination position-relative mt-2"></div> -->
                    </swiper>
                </div>
            </BContainer>
        </section>
       


        <LandingFooter></LandingFooter>
    </div>
</template>
<style>
.testimonial .swiper-pagination-bullet{
    background-color:#fff !important;
}
.ceo-img{
    width: 250px;
    height: 250px;
}

.auth-one-bg .bg-overlay {
    background: linear-gradient(to right, var(--landing_header_act_text), var(--landing_header_act_text));
    opacity: 0.9;
}
.rtl .auth-one-bg .bg-overlay {
    background: linear-gradient(to right, var(--landing_header_act_text), var(--landing_header_act_text));
    opacity: 0.9;
}

@media only screen and (max-width: 425px) {
.ceo-img{
    width: 200px;
    height: 200px;
}
}
</style>