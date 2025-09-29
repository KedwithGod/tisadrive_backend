<script>
import { CountTo } from "vue3-count-to";
import { Autoplay, Navigation } from "swiper/modules";
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";
import Swal from "sweetalert2";
import "swiper/css/autoplay";
import "swiper/css/navigation";
import "swiper/css/pagination";
import LandingHeader from "@/Components/LandingHeader.vue";
import LandingFooter from "@/Components/LandingFooter.vue";
import { Link, router, useForm } from '@inertiajs/vue3';
import FormValidation from "@/Components/FormValidation.vue";
import { ref, watch } from "vue";
import axios from 'axios';

export default {
  props: {
    successMessage: String,
    alertMessage: String,
  },
  setup(props) {
    const form = useForm({
      name: null,
      user_name: null,
      email: null,
      purchase_code: null,
      domain_name: null,
    });

    const validationRules = {
      name: { required: true },
      user_name: { required: true },
      email: { required: true, email: true },
      purchase_code: { required: true },
      domain_name: { required: true },
    };

    const validationRef = ref(null);
    const errors = ref({});
    const successMessage = ref(props.successMessage || '');
    const alertMessage = ref(props.alertMessage || '');
    const dismissMessage = () => {
      successMessage.value = "";
      alertMessage.value = "";
    };
    const isValidCode = ref(false); // Flag to track if the purchase code is valid
    const isValidEmail = ref(false); // Flag to track if the purchase code is valid
    const checkboxChecked = ref(false); // Add this for checkbox tracking
    const checkboxError = ref(false); // Track if checkbox error

    // Regex for purchase code validation
    const purchaseCodeRegex = /^([a-f0-9]{8})-(([a-f0-9]{4})-){3}([a-f0-9]{12})$/i;
    // Regex for email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    // Watcher to validate purchase code while typing
    watch(() => form.purchase_code, (newValue) => {
      if (purchaseCodeRegex.test(newValue) || form.purchase_code == null) {
        isValidCode.value = true; // Set the flag to true for valid code
        errors.value.purchase_code = '';
      } else {
        isValidCode.value = false; // Set the flag to false for invalid code
        errors.value.purchase_code = 'Invalid purchase code format.';
      }
    });

     // Watcher to validate email while typing
     watch(() => form.email, (newValue) => {
      if (emailRegex.test(newValue) || form.email == null) {
        isValidEmail.value = true; // Set the flag to true for valid code
        errors.value.email = '';
      } else {
        isValidEmail.value = false; // Set the flag to false for invalid code
        errors.value.email = 'Invalid email format.';
      }
    });

    // Watch for checkbox changes to clear the error immediately
    watch(checkboxChecked, (newValue) => {
      if (newValue) {
        errors.value.checkbox = "";
        checkboxError.value = false; // Reset checkbox error state
      }
    });

    const handleSubmit = async () => {
     
      if (!checkboxChecked.value) {
        errors.value.checkbox = "You must check this box before install !";
        checkboxError.value = true; // Set checkbox error state
        return;
      } else {
        errors.value.checkbox = "";
        checkboxError.value = false; // Clear checkbox error state
      }
      // errors.value = validationRef.value.validate();
      // if (Object.keys(errors.value).length > 0) {
      //   return;
      // }

      // console.log("Form submitted");
      errors.value = validationRef.value.validate();

      if (Object.keys(errors.value).length == 0) {

        console.log("validation-cleared")
        try {
          // If no validation errors, proceed with form submission
          const formData = new FormData();
          formData.append('name', form.name);
          formData.append('user_name', form.user_name);
          formData.append('email', form.email);
          formData.append('purchase_code', form.purchase_code);
          formData.append('domain_name', form.domain_name);

          let response = await axios.post("verfication-submit", formData);

          console.log(response.data.success);

          if (response.data.success == true) {
            successMessage.value = response.data.message;
            form.reset(); // Reset form after successful submission
            
            Swal.fire({
                title: "Verification Successfull!",
                text: "Reload to Proceed",
                icon: "success",
                confirmButtonColor: "#34c38f",
                confirmButtonText: "Reload",
            }).then(async (result) => {
                if (result.isConfirmed) {
                  window.location.reload();
                }
            });

          } else {

            errors.value.purchase_code = response.data.message;
          }
        } catch (error) {
          console.error("Error handling form submission:", error);
          if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors;
          } else {
            console.error("An unexpected error occurred:", error);
          }
        }
      }
    };
    return {
      form,
      successMessage,
      alertMessage,
      handleSubmit,
      dismissMessage,
      validationRules,
      validationRef,
      errors,
      isValidCode,
      isValidEmail,
      checkboxChecked, // Return this to template
      checkboxError, // Return checkbox error state
    };
  },
  data() {
    return {
      Autoplay, Navigation,
    };
  },
  methods: {},
  components: {
    Swiper,
    SwiperSlide,
    CountTo,
    LandingHeader,
    LandingFooter,
    FormValidation,
    Link,
    router,
  },
};
</script>


<template>
<div class="layout-wrapper landing">
<Header />


<div class="vertical-overlay" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent.show"></div>
<section class="p-3 hero-section" id="hero">
<BContainer>
<BRow class="justify-content-center">
<BCol lg="8" sm="10"><div class="row justify-content-center">
<div class="col-lg-12">
<div class="card">
<div class="card-body">
<div class="text-center">
<div class="row justify-content-center">
    <div class="col-lg-12">
        <h4 class="mt-3 fw-semibold">Restart Purchase verfication</h4>
        <p class="text-muted mt-3">Please complete the form</p>
    </div>
</div>

<div class="row justify-content-center mt-1 mb-0">
    <div class="col-sm-12 col-8">
        <img src="/images/verification-img.png" alt="" class="img-fluid" width="150">
    </div>
</div>
<div class="row align-items-center justify-content-center mt-1 mb-1">
    <div class="col-sm-12 col-12">
        <div class="vstack gap-2">
            <div class="d-flex align-items-center justify-content-center">
                <div class="flex-shrink-0 me-2">
                    <div class="avatar-xs icon-effect">
                        <div class="avatar-title bg-transparent text-success rounded-circle h2">
                            <i class="ri-check-fill"></i>
                        </div>
                    </div>
                </div>
                <div class="flex-grow-0">
                    <p class="mb-0">.env File 777 permission</p>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-center">
                <div class="flex-shrink-0 me-2">
                    <div class="avatar-xs icon-effect">
                        <div class="avatar-title bg-transparent text-success rounded-circle h2">
                            <i class="ri-check-fill"></i>
                        </div>
                    </div>
                </div>
                <div class="flex-grow-0">
                    <p class="mb-0">RouteServiceProvider file 777 permission <!-- Tooltips -->
                      <BButton variant="none" v-b-tooltip.hover title="App/Providers">
                        <i class=" ri-information-line fs-18 text-danger"></i>
                      </BButton>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- <div class="form-check form-check-inline mt-1">
    <input class="form-check-input" type="checkbox" id="inlineCheckbox3" value="option3">
    <label class="form-check-label" for="inlineCheckbox3">Please Check above access are granted.</label>
</div> -->
<!-- Checkbox with conditional class for red border -->
  <div :class="['form-check', 'form-check-inline', 'mt-1']">
    <input :class="[ checkboxError ? 'border border-danger' : '']" class="form-check-input" type="checkbox" id="inlineCheckbox3" v-model="checkboxChecked">
    <label class="form-check-label" for="inlineCheckbox3">Please check above access are granted <span class="text-danger">*</span></label>
    
    <br>
    <span v-if="errors.checkbox" class="text-danger">{{ errors.checkbox }}</span>
  </div>
</div>
<!-- form -->
<div class="row mt-2">
<div class="col-lg-12">
        <div class="card-header align-items-center d-flex">
        </div><!-- end card header -->
        <div class="card-body">
            <div class="live-preview">
                <form @submit.prevent="handleSubmit">
            <FormValidation :form="form" :rules="validationRules" ref="validationRef">
                    <div class="row g-3">
                        <div class="col-lg-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="name" placeholder="Enter your name" v-model="form.name">
                                <label for="name">Name
                                  <span class="text-danger">*</span>
                                </label>
                                <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-floating">
                              <input :class="{ 'border-success': isValidEmail, 'border-danger': errors.email }" type="email" class="form-control" id="email" v-model="form.email"
                                placeholder="Enter your email" >
                                <label for="email">Email
                                  <span class="text-danger">*</span>
                                </label>
                                <span v-for="(error, index) in errors.email"
                                              :key="index"
                                              class="text-danger">{{ error }}</span>
                                              <!-- Success Tick Icon -->
                                        <i v-if="isValidEmail"
                                           class="bx bx-check-circle text-success position-absolute"
                                           style="top: 50%; right: 10px; transform: translateY(-50%);"></i>
                            </div>
                            <!-- <span v-for="(error, index) in errors.email" :key="index" class="text-danger">{{ error }}</span> -->
                        </div>
                        <div class="col-lg-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="user_name" placeholder="Enter your user name" v-model="form.user_name">
                                <label for="username">User name
                                  <span class="text-danger">*</span>
                                </label>
                                <span v-for="(error, index) in errors.user_name" :key="index" class="text-danger">{{ error }}</span>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-floating">
                                <input :class="{ 'border-success': isValidCode, 'border-danger': errors.purchase_code }" type="text" class="form-control" id="purchase_code" v-model="form.purchase_code" placeholder="Enter your purchase code">
                                <label for="purchase_code">Purchase Code
                                  <span class="text-danger">*</span>
                                </label>
                                <span v-for="(error, index) in errors.purchase_code"
                                              :key="index"
                                              class="text-danger">{{ error }}</span>

                                        <!-- Success Tick Icon -->
                                        <i v-if="isValidCode"
                                           class="bx bx-check-circle text-success position-absolute"
                                           style="top: 50%; right: 10px; transform: translateY(-50%);"></i>
                            </div>
                <!-- <span v-for="(error, index) in errors.purchase_code" :key="index" class="text-danger">{{ error }}</span> -->

                        </div>
                        <div class="col-lg-12">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="domain_name" placeholder="Enter your Domain" v-model="form.domain_name">
                                <label for="domain name">Domain
                                  <span class="text-danger">*</span>
                                </label>
                                <span v-for="(error, index) in errors.domain_name" :key="index" class="text-danger">{{ error }}</span>
                            </div>
                        </div>
                        <div class="col-lg-12 flex-shrink-0">
                            <a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-" target="_blank" class="text-reset text-decoration-underline"><b>Where to get Purchase code?</b></a>
                        </div>
                        <div class="col-lg-12">
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Verify</button>
                            </div>
                        </div>
                    </div>
                </FormValidation>

                </form>
            </div> 
        </div>
</div>
</div>
<!-- form end -->
</div>
</div>
<!--end card-->
</div>
<!--end col-->
</div>
</BCol>
</BRow>
</BContainer>
</section>


<!-- Success & Alert Message -->
<div>
      <div v-if="successMessage" class="custom-alert alert alert-success alert-border-left fade show" role="alert"
        id="alertMsg">
        <div class="alert-content">
          <i class="ri-notification-off-line me-3 align-middle"></i>
          <strong>Success</strong> - {{ successMessage }}
          <button type="button" class="btn-close btn-close-success" @click="dismissMessage"
            aria-label="Close Success Message"></button>
        </div>
      </div>

      <div v-if="alertMessage" class="custom-alert alert alert-danger alert-border-left fade show" role="alert"
        id="alertMsg">
        <div class="alert-content">
          <i class="ri-notification-off-line me-3 align-middle"></i>
          <strong>Alert</strong> - {{ alertMessage }}
          <button type="button" class="btn-close btn-close-danger" @click="dismissMessage"
            aria-label="Close Alert Message"></button>
        </div>
      </div>
    </div>
</div>
</template>

