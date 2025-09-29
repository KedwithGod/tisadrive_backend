<script>
import { CountTo } from "vue3-count-to";
import { Link, router } from '@inertiajs/vue3';
import { Autoplay, Navigation, Pagination } from "swiper/modules";
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";
import "swiper/css/autoplay";
import 'swiper/css/navigation';
import 'swiper/css/pagination';

export default {
    data() {
        return {
            Autoplay, Navigation, Pagination,
            headers: this.$page.props.landingHeader,
            header: window.headers, // Access global headers data
            locales: this.$page.props.locales,
            selectedLocale: this.$page.props.landingHeader.locale,  
            // toggleValues:false,
        };
    },
    components: {
        Swiper,
        SwiperSlide,
        CountTo,
        Link,
        router
    },
    methods: {

        stripHtmlTags(content) {
            const parser = new DOMParser();
            const parsedContent = parser.parseFromString(content, 'text/html');
            return parsedContent.body.textContent || "";
            },

topFunction() {
    document.body.scrollTop = 0;
    document.documentElement.scrollTop = 0;
},

scrollToSection(sectionId) {
    const element = document.getElementById(sectionId);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth' });
    }
},
// toggleDirection() {
//             this.toggleValues = !this.toggleValues; 
//             this.toggleDirectionSwitch();         
//         },

//         toggleDirectionSwitch(){           
//             const body = document.body;
//             if (this.toggleValues) {
//                 body.classList.add('rtl');
//                 body.classList.remove('ltr');      
//             }
//             else {
//                 body.classList.add('ltr');
//                 body.classList.remove('rtl');
//             } 
//             localStorage.setItem('directiontoggleValue', this.toggleValues);
               
//         },
//         initializeToggleDirection() {
//             const savedToggleValue = localStorage.getItem('directiontoggleValue');
//             if (savedToggleValue === "true") {
//                 this.toggleValues = true;
//             } else {
//                 this.toggleValues = false;
//             }
//             this.toggleDirectionSwitch();
//         },       
        
        startTour() {
            const driver = window.driver.js.driver;

            const path = window.location.pathname;
            if(path === '/'){
                const driverObj = new driver({
                    showProgress: true,  
                    steps: [
                        { element: '.selectLanguages', 
                            popover: { title: 'Language', description: 'Select Languages', side: "left", align: 'start' } 
                        },
                        { element: '.download-app', 
                            popover: { title: 'App Download', description: 'Download our app using App store and play store', side: "top", align: 'start'  } 
                        },
                        { element: '.advantages', 
                            popover: { title: 'Advantage', description: 'Advantages of using Our App',side: "right", align: 'end'  } 
                        },
                        { element: '.digitalservice', 
                            popover: { title: 'Digital Service', description: 'A complete solution for your Taxi Service.', side: "left", align: 'start'  } 
                        },
                        { element: '.aboutcompany', 
                            popover: { title: 'About', description: 'About Our Company', side: "left", align: 'start' } 
                        },
                        { element: '.whyusTagxi', 
                            popover: { title: 'Why Us?', description: 'Why Drive with Tagxi', side: "top", align: 'start' } 
                        },
                        { element: '.servicelocation', 
                            popover: { title: 'Service Location', description: 'About Our Service Location', side: "left", align: 'end' }
                        },                   
                    ]
                });
                driverObj.drive();
            }
            else if(path === '/driver'){
                const driverObj = new driver({
                    showProgress: true,  
                    steps: [
                        { element: '.selectLanguages', 
                            popover: { title: 'Language', description: 'Select Languages', side: "left", align: 'start' } 
                        },                  
                    ]
                });
            driverObj.drive();
            }
            

            
           
        },
},
unmounted() {
window.removeEventListener('scroll', this.setActiveSection);
},
mounted() {
    // this.initializeToggleDirection();
    
window.addEventListener('scroll', this.setActiveSection);
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


window.addEventListener('scroll', function (ev) {
    ev.preventDefault();
    var navbar = document.getElementById("navbar");
    if (navbar) {
        if (document.body.scrollTop >= 50 || document.documentElement.scrollTop >= 50) {
            navbar.classList.add("is-sticky");
        } else {
            navbar.classList.remove("is-sticky");
        }
    }
});

// document.querySelector('.currentyear').innerHTML = new Date().getFullYear() + " © Mi";
},

computed: {
    
    footerLogoUrl() {
        const selectedHeader = this.header.find(header => header.locale === this.selectedLocale);
        return selectedHeader ? selectedHeader.footer_logo_url : '';
    }
  }

};
</script>

<template>
                <footer class="custom-footer py-5 position-relative" style="background-color: var(--landing_footer_bg);color:var(--landing_footer_text);">
            <BContainer>
                <BRow>
                    <BCol lg="4" class="mt-4">
                        <div>
                            <div>
                                <img  :src="footerLogoUrl" alt="logo light" width="150">
                            </div>
                            <div class="mt-4 fs-13" style="color:var(--landing_footer_text);">
                                <p>{{ stripHtmlTags(headers.footer_para) }}</p>
                            </div>
                        </div>
                    </BCol>

                    <BCol lg="7" class="ms-lg-auto">
                        <BRow>
                            <BCol sm="4" class="mt-4">
                                <h5 class=" mb-0" style="color:var(--landing_footer_text);">{{ headers.quick_links }}</h5>
                                <div class="text-muted mt-3">
                                    <ul class="list-unstyled ff-secondary footer-list">
                                        <li>
                                            <Link style="color:var(--landing_footer_text);" href="/compliance">{{ headers.compliance }}</Link>
                                        </li>
                                        <li>
                                            <Link style="color:var(--landing_footer_text);" href="/privacy">{{ headers.privacy }}</Link>
                                        </li>
                                        <li>
                                            <Link style="color:var(--landing_footer_text);" href="/terms">{{ headers.terms }}</Link>
                                        </li>
                                        <li>
                                            <Link style="color:var(--landing_footer_text);" href="/dmv">{{ headers.dmv }}</Link>
                                        </li>
                                    </ul>
                                </div>
                            </BCol>
                            <BCol sm="4" class="mt-4">
                                <h5 class="mb-0" style="color:var(--landing_footer_text);">{{ headers.user_app }}</h5>
                                <div class="text-muted mt-3">
                                    <ul class="list-unstyled ff-secondary footer-list">
                                        <li>
                                            <BLink :href="headers.user_play_link" target="_blank" style="color:var(--landing_footer_text);">
                                                <i class="ri-google-play-fill align-middle mx-1"></i>{{ headers.user_play }}</BLink>
                                        </li>
                                        <li>
                                            <BLink :href="headers.user_apple_link" target="_blank" style="color:var(--landing_footer_text);">
                                                <i class="ri-apple-fill align-middle mx-1"></i>{{ headers.user_apple }}</BLink>
                                        </li>
                                    </ul>
                                </div>
                            </BCol>
                            <BCol sm="4" class="mt-4">
                                <h5 class="mb-0" style="color:var(--landing_footer_text);">{{ headers.driver_app }}</h5>
                                <div class="text-muted mt-3">
                                    <ul class="list-unstyled ff-secondary footer-list">
                                        <li>
                                            <BLink :href="headers.driver_play_link" target="_blank" style="color:var(--landing_footer_text);">
                                                <i class="ri-google-play-fill align-middle mx-1"></i>{{ headers.driver_play }}</BLink>
                                        </li>
                                        <li>
                                            <BLink :href="headers.driver_apple_link" target="_blank" style="color:var(--landing_footer_text);">
                                                <i class="ri-apple-fill align-middle mx-1"></i>{{ headers.driver_apple }}</BLink>
                                        </li>
                                    </ul>
                                </div>
                            </BCol>
                        </BRow>
                    </BCol>
                </BRow>

                <BRow class="text-center text-sm-start align-items-center mt-5">
                    <BCol sm="6">

                        <div>
                            <p class="copy-rights mb-0 currentyear" style="color:var(--landing_footer_text);">{{ headers.copy_rights }}</p>
                        </div>
                    </BCol>
                    <BCol sm="6">
                        <div class="text-sm-end mt-3 mt-sm-0">
                            <ul class="list-inline mb-0 footer-social-link">
                                <li class="list-inline-item">
                                    <BLink :href= "headers.fb_link" target="_blank" class="avatar-xs d-block">
                                        <div class="avatar-title rounded-circle">
                                            <i class="ri-facebook-fill"></i>
                                        </div>
                                    </BLink>
                                </li>
                                <li class="list-inline-item">
                                    <BLink :href="headers.linkdin_link" target="_blank" class="avatar-xs d-block">
                                        <div class="avatar-title rounded-circle">
                                            <i class="ri-linkedin-fill"></i>
                                        </div>
                                    </BLink>
                                </li>
                                <li class="list-inline-item">
                                    <BLink :href="headers.x_link" target="_blank" class="avatar-xs d-block">
                                        <div class="avatar-title rounded-circle">
                                            <i class="ri-twitter-line"></i>
                                        </div>
                                    </BLink>
                                </li>
                                <li class="list-inline-item">
                                    <BLink :href="headers.insta_link" target="_blank" class="avatar-xs d-block">
                                        <div class="avatar-title rounded-circle">
                                            <i class="ri-instagram-line"></i>
                                        </div>
                                    </BLink>
                                </li>
                            </ul>
                        </div>
                    </BCol>
                </BRow>
            </BContainer>
        </footer>

        <!-- <div class="customizer-setting toggle-setting">
            <div class="form-check form-switch toggle-direction">                    
                <input class="form-check-input" type="checkbox" id="toggleSwitch" @click="toggleDirection" v-model="toggleValues">
                <label class="form-check-label" for="toggleSwitch">
                <span class="switch-label ltr">LTR</span>
                <span class="switch-label rtl">RTL</span>
                </label>
            </div>
        </div> -->

        <!-- <div class="customizer-setting toggle-setting"> 
            <button @click="startTour" class="btn btn-primary" style="margin-top: -335px;">Start Tour</button>
        </div> -->
                    
        <BButton variant="danger" @click="topFunction" class="btn-icon" id="back-to-top">
            <i class="ri-arrow-up-line"></i>
        </BButton>
</template>

<style lang="scss">
.rtl {
@import "../../scss/config/default/app-rtl";
direction: rtl;
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

.footer-social-link .avatar-title {
    color: var(--landing_footer_text);
    background-color: var(--landing_header_act_text); 
    transition: all 0.3s ease;
}
.footer-social-link .avatar-title:hover {
    color: #fff;
    background-color: #405189;
}

</style>