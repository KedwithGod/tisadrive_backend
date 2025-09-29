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
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from "vue";
import axios from "axios";
import FormValidation from "@/Components/FormValidation.vue";


export default {
    props: {
        landingContact: Object,
        landingHeader: Object,
        successMessage: String,
        alertMessage: String,
    },
    components: {
        Swiper,
        SwiperSlide,
        CountTo,
        LandingHeader,
        LandingFooter,
        FormValidation,
    },
    data() {
    return {
        // enablerecaptcha: window.enablerecaptcha || 0,   // Default to false if not defined
    };
  },
    setup(props) {
    const form = useForm({
        name: props.landingContact ? props.landingContact.name || "" : "",
        mail: props.landingContact ? props.landingContact.mail || "" : "",
        subject: props.landingContact ? props.landingContact.subject || "" : "",
        comments: props.landingContact ? props.landingContact.comments || "" : "",
    });

    const validationRules = {
        name: { required: true },
        mail: { required: true },
        subject: { required: true },
        comments: { required: true },
    };

    const validationRef = ref(null);
    const errors = ref({});
    const successMessage = ref(props.successMessage || '');
    const alertMessage = ref(props.alertMessage || '');


    const dismissMessage = () => {
      successMessage.value = "";
      alertMessage.value = "";
    };

    

    const handleSubmit = async () => {
      errors.value = validationRef.value.validate();
      if (Object.keys(errors.value).length > 0) {
        return;       
      }
        if (enablerecaptcha == 1) {
            const recaptchaResponse = grecaptcha.getResponse();
            console.log("recaptchaResponse", recaptchaResponse);
            if (!recaptchaResponse) {
            alertMessage.value = 'Failed to get reCAPTCHA.';
            return;
            }

            // Add reCAPTCHA response to the request payload
            form.data().recaptchaResponse = recaptchaResponse;
        }
     
      try {
        let response;
        const requestData = { ...form.data(),
                            //   recaptchaResponse, // Add the reCAPTCHA response to the request payload
                             };
        response = await axios.post('/landing-contact/contactmessage',requestData);

        if (response.status === 201) {
          successMessage.value = 'Message saved successfully.';
          form.reset();
          router.get('/contact'); // Use router.push instead of router.get
        } else {
          alertMessage.value = 'Failed to save Message.';
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          errors.value = error.response.data.errors;
        } else {
          console.error('Error saving Message:', error);
          alertMessage.value = 'Failed to save Message.';
        }
      }
    };

    return {
        Autoplay,
        Navigation,
        Pagination, 
        form,
        successMessage,
        alertMessage,
        handleSubmit,
        dismissMessage,
        validationRules,
        validationRef,
        errors,
        recaptchaKey: window.recaptchaKey,    
        enablerecaptcha: window.enablerecaptcha    
    };
  },


    methods: {
        stripHtmlTags(content) {
            const parser = new DOMParser();
            const parsedContent = parser.parseFromString(content, 'text/html');
            return parsedContent.body.textContent || "";
        },

        loadRecaptcha() {
        const script = document.createElement('script');
        script.src = 'https://www.google.com/recaptcha/api.js';
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);
        },
        myRecaptchaMethod(response) {
            this.recaptchaToken = response;
            // console.log('Captcha completed:', this.recaptchaToken);
        }
    },

    mounted() {
        window.myRecaptchaMethod = this.myRecaptchaMethod;
        this.loadRecaptcha();
    }


};
</script>

<template>
    <div class="layout-wrapper landing">
        <Header />
        <LandingHeader :headers="landingHeader">
            
        </LandingHeader>


        <section class=" mt-5">
        <div class="auth-page-wrapper pt-5">
        <div class="auth-one-bg-position auth-one-bg" style="background: url('/images/2.jpg') no-repeat;background-size: cover;" id="auth-particles">
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
                        {{ landingContact.hero_title }}
                    </h1>
                </div>
                </div>
            </BCol>
            </BRow>
        </BContainer>
        </div>
        </div>
    </section>
    

        <section class="section" id="contact">
            <BContainer>
                <BRow class="justify-content-center">
                    <BCol lg="8">
                        <div class="text-center mb-5">
                            <h3 class="mb-3 fw-semibold">{{ landingContact.contact_heading }}</h3>
                            <p class="text-muted mb-4 ff-secondary">{{ stripHtmlTags(landingContact.contact_para) }}</p>
                        </div>
                    </BCol>
                </BRow>

                <BRow class="gy-4">
                    <BCol lg="4">
                        <div>
                            <div class="mt-4">
                                <h5 class="fs-13 text-muted text-uppercase"><i class="ri-building-4-line mx-1"></i>{{ landingContact.contact_address_title }}:</h5>
                                <div class="ff-secondary fw-semibold">{{ stripHtmlTags(landingContact.contact_address) }}</div>
                            </div>
                            <div class="mt-4">
                                <h5 class="fs-13 text-muted text-uppercase"><i class="ri-phone-line mx-1"></i>{{ landingContact.contact_phone_title }}:</h5>
                                <div class="ff-secondary fw-semibold">{{ landingContact.contact_phone }}</div>
                            </div>
                            <div class="mt-4">
                                <h5 class="fs-13 text-muted text-uppercase"><i class="ri-mail-line mx-1"></i>{{ landingContact.contact_mail_title }}:</h5>
                                <div class="ff-secondary fw-semibold">{{ landingContact.contact_mail }}</div>
                            </div>
                            <div class="mt-4">
                                <h5 class="fs-13 text-muted text-uppercase"><i class="ri-global-line mx-1"></i>{{ landingContact.contact_web_title }}:</h5>
                                <div class="ff-secondary fw-semibold"><BLink :href="landingContact.contact_web" target="_blank">{{ landingContact.contact_web }}</BLink></div>
                            </div>
                        </div>
                    </BCol>
                    <BCol lg="8">
                        <div>
                            <form @submit.prevent="handleSubmit">
                                <FormValidation :form="form" :rules="validationRules" ref="validationRef">
                                    <BRow>
                                        <BCol lg="6">
                                            <div class="mb-4">
                                                <label for="name" class="form-label fs-13">{{ landingContact.form_name }}</label>
                                                <input name="name" id="name" type="text"
                                                    class="form-control bg-light border-light" :placeholder="landingContact.form_name" v-model="form.name">
                                                    <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                                            </div>
                                        </BCol>
                                        <BCol lg="6">
                                            <div class="mb-4">
                                                <label for="email" class="form-label fs-13">{{ landingContact.form_mail }}</label>
                                                <input name="email" id="email" type="email"
                                                    class="form-control bg-light border-light" :placeholder="landingContact.form_mail" v-model="form.mail">
                                                    <span v-for="(error, index) in errors.mail" :key="index" class="text-danger">{{ error }}</span>
                                            </div>
                                        </BCol>
                                    </BRow>
                                    <BRow>
                                        <BCol lg="12">
                                            <div class="mb-4">
                                                <label for="subject" class="form-label fs-13">{{ landingContact.form_subject }}</label>
                                                <input type="text" class="form-control bg-light border-light" id="subject"
                                                    name="subject" :placeholder="landingContact.form_subject" v-model="form.subject" />
                                                    <span v-for="(error, index) in errors.subject" :key="index" class="text-danger">{{ error }}</span>
                                            </div>
                                        </BCol>
                                    </BRow>
                                    <BRow>
                                        <BCol lg="12">
                                            <div class="mb-3">
                                                <label for="comments" class="form-label fs-13">{{ landingContact.form_message }}</label>
                                                <textarea name="comments" id="comments" rows="3"
                                                    class="form-control bg-light border-light"
                                                    :placeholder="landingContact.form_message" v-model="form.comments"></textarea>
                                                    <span v-for="(error, index) in errors.comments" :key="index" class="text-danger">{{ error }}</span>
                                            </div>
                                        </BCol>
                                    </BRow>
                                    <BRow>
                                        <BCol lg="12" v-if="enablerecaptcha == 1">
                                            <div class="g-recaptcha" :data-sitekey="recaptchaKey" data-callback="myRecaptchaMethod"></div>
                                        </BCol>
                                    </BRow>
                                    <BRow>
                                        <BCol lg="12" class="text-end">
                                            <Button type="submit" id="submit" name="send" class="submitBnt btn" style="background-color: var(--landing_header_act_text);color:var(--landing_footer_text)" >{{ landingContact.form_btn }}</Button>
                                        </BCol>
                                    </BRow>
                                </FormValidation>    
                            </form>
                        </div>
                    </BCol>
                </BRow>  
            </BContainer>
        </section>
        <div>
            <div v-if="successMessage" class="custom-alert alert alert-success alert-border-left fade show" role="alert" id="alertMsg">
                <div class="alert-content">
                    <i class="ri-notification-off-line me-3 align-middle"></i>
                    <strong>Success</strong> - {{ successMessage }}
                    <button type="button" class="btn-close btn-close-success" @click="dismissMessage" aria-label="Close Success Message"></button>
                </div>
            </div>
            <div v-if="alertMessage" class="custom-alert alert alert-danger alert-border-left fade show" role="alert" id="alertMsg">
                <div class="alert-content">
                    <i class="ri-notification-off-line me-3 align-middle"></i>
                    <strong>Alert</strong> - {{ alertMessage }}
                    <button type="button" class="btn-close btn-close-danger" @click="dismissMessage" aria-label="Close Alert Message"></button>
                </div>
            </div>
        </div>


        <LandingFooter></LandingFooter>
    </div>
</template>



<style>
.custom-alert {
  max-width: 600px;
  float: right;
  position: fixed;
  top: 150px;
  right: 20px;
}

.text-danger {
  padding-top: 5px;
}
.auth-one-bg .bg-overlay {
    background: linear-gradient(to right, var(--landing_header_act_text), var(--landing_header_act_text));
    opacity: 0.9;
}
.rtl .auth-one-bg .bg-overlay {
    background: linear-gradient(to right, var(--landing_header_act_text), var(--landing_header_act_text));
    opacity: 0.9;
}
</style>