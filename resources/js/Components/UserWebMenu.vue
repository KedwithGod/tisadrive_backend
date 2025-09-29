<script>
import { CountTo } from "vue3-count-to";
import { Link } from '@inertiajs/vue3';
import { Autoplay, Navigation, Pagination } from "swiper/modules";
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";
import "swiper/css/autoplay";
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import Swal from "sweetalert2";

export default {

    props: {
    user: {
      type: Object,
      required: true,
    },
  },
    
    data() {
        return {
            Autoplay, Navigation, Pagination,
            supportTicket:window.supportTicket
            
        };
    },
    components: {
        Swiper,
        SwiperSlide,
        CountTo,
        Link,
        axios,
        router
    },
    // methods: {
    //     logout() {
    //   // Call the backend logout route
    //   axios.post(route('logout')).then(() => {
    //     router.visit('/login'); // Redirect to user-login page
    //   }).catch(error => {
    //     console.error('Logout failed:', error);
    //   });
    // }

    // },
    methods: {
  logout() {
    Swal.fire({
      title: this.$t("are_you_sure"), // Translatable title
      text: this.$t("you_want_to_be_logout"), // Translatable message
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#34c38f",
      cancelButtonColor: "#f46a6a",
      confirmButtonText: this.$t("yes"), // "Yes, Logout"
      cancelButtonText: this.$t("cancel"), // "Cancel"
    }).then((result) => {
      if (result.isConfirmed) {
        axios.post(route("logout"))
          .then(() => {
            router.visit("/login"); // Redirect to login page
          })
          .catch((error) => {
            console.error("Logout failed:", error);
          });
      }
    });
  }
},

    mounted(){
     
    },
    created() {
       
        
    },
};
</script>

<template>
   <!-- menu Offcanvas -->
<div class="d-flex flex-wrap gap-2 p-0">
    <BLink class="btn p-0" data-bs-toggle="offcanvas" data-bs-target="#offcanvasScrolling" aria-controls="offcanvasScrolling"><i class="ri-bar-chart-horizontal-line fs-22"></i></BLink>
</div>    

<div class="offcanvas offcanvas-start" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1" id="offcanvasScrolling" aria-labelledby="offcanvasScrollingLabel">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title" id="offcanvasScrollingLabel">{{$t("menu")}}</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="d-flex justify-content-center mb-2">
        <img :src="user?.profile_picture" class="img-fluid avatar-xl rounded-circle m-auto" width="100" alt="user-profile">
        </div>
        <div data-simplebar style="max-height: 215px;"> 
    <ul class="">
      <li class="list-group-item py-3">
            <BLink href="/create-booking">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 avatar-xs">
                            <div class="avatar-title bg-light text-dark rounded">
                                <i class=" ri-tv-2-line"></i>
                            </div>
                        </div>
                        <div class="flex-shrink-0 ms-2">
                            <h6 class="fs-14 mb-0">{{$t("create_booking")}}</h6>
                        </div>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <span class="text-dark"><i class="ri-arrow-right-s-line fs-18"></i></span>
                </div>
            </div>
        </BLink>
        </li>
      <li class="list-group-item py-3">
            <BLink href="/profile">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 avatar-xs">
                            <div class="avatar-title bg-light text-dark rounded">
                                <i class="ri-user-fill"></i>
                            </div>
                        </div>
                        <div class="flex-shrink-0 ms-2">
                            <h6 class="fs-14 mb-0">{{$t("profile")}}</h6>
                        </div>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <span class="text-dark"><i class="ri-arrow-right-s-line fs-18"></i></span>
                </div>
            </div>
        </BLink>
        </li>
        <li class="list-group-item py-3">
          <BLink href="/history">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 avatar-xs">
                            <div class="avatar-title bg-light text-dark rounded">
                                <i class="ri-history-line"></i>
                            </div>
                        </div>
                        <div class="flex-shrink-0 ms-2">
                            <h6 class="fs-14 mb-0">{{$t("history")}}</h6>
                        </div>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <span class="text-dark"><i class="ri-arrow-right-s-line fs-18"></i></span>
                </div>
            </div>
          </BLink>
        </li>
        <li class="list-group-item py-3" v-if="supportTicket == 1">
          <BLink href="/get-support">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 avatar-xs">
                            <div class="avatar-title bg-light text-dark rounded">
                                <i class="bx bx-support"></i>
                            </div>
                        </div>
                        <div class="flex-shrink-0 ms-2">
                            <h6 class="fs-14 mb-0">{{$t("get_support")}}</h6>
                        </div>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <span class="text-dark"><i class="ri-arrow-right-s-line fs-18"></i></span>
                </div>
            </div>
          </BLink>
        </li>
        <li class="list-group-item py-3">
          <BLink @click="logout">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 avatar-xs">
                            <div class="avatar-title bg-light text-dark rounded">
                                <i class=" ri-shut-down-line"></i>
                            </div>
                        </div>
                        <div class="flex-shrink-0 ms-2">
                            <h6 class="fs-14 mb-0">{{$t("logout")}}</h6>
                        </div>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <!-- <span class="text-dark"><i class="ri-arrow-right-s-line fs-18"></i></span> -->
                </div>
            </div>
          </BLink>
        </li>
    </ul>
</div>
    </div>
</div>
<!-- menu end -->
</template>

