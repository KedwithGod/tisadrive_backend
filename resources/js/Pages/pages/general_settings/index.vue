<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import { ref, computed , watch} from "vue";
import axios from "axios";
import Multiselect from "@vueform/multiselect";
import FormValidation from "@/Components/FormValidation.vue";
import logo from "@/Components/widgets/logo.vue";
import favicon from "@/Components/widgets/favicon.vue";
import loginbg from "@/Components/widgets/loginbg.vue";
import { object } from '@amcharts/amcharts5';
import { useI18n } from 'vue-i18n';
import ImageUpload from "@/Components/ImageUpload.vue";
import Swal from "sweetalert2";
import { layoutComputed } from "@/state/helpers";
import { mapGetters } from 'vuex';

export default {
  components: {
    Layout,
    PageHeader,
    Head,
    Pagination,
    Multiselect,
    FormValidation,
    logo,
    favicon,
    loginbg,
    ImageUpload
  },
  computed: {
    ...layoutComputed,
    ...mapGetters(['permissions']),
  },
  props: {
    successMessage: String,
    alertMessage: String,
    app_for: String,
    settings: Object,
    logoURL:object,  
    faviconURL:object,
    loginbgURL:object,
    owner_loginbgURL:object,
    validate: Function, // Define the prop to receive the method
  },
  setup(props) {
    // console.log(props.settings);
    const { t } = useI18n();
    // Initialize the form with default or existing values
    const form = useForm({
      nav_color: props.settings.nav_color || "",
      sidebar_color: props.settings.sidebar_color || "",
      sidebar_text_color: props.settings.sidebar_text_color || "",
      app_name: props.settings.app_name || "",
      currency_code: props.settings.currency_code || "",
      currency_symbol: props.settings.currency_symbol || "",
      contact_us_mobile1: props.settings.contact_us_mobile1 || "",
      contact_us_mobile2: props.settings.contact_us_mobile2 || "",
      contact_us_link: props.settings.contact_us_link || "",
      default_latitude: props.settings.default_latitude || "",
      default_longitude: props.settings.default_longitude || "",
      show_outstation_ride_feature: props.settings.show_outstation_ride_feature === '1',
      show_rental_ride_feature: props.settings.show_rental_ride_feature === '1',
      show_ride_otp_feature: props.settings.show_ride_otp_feature === '1',
      show_ride_later_feature: props.settings.show_ride_later_feature === '1',
      show_ride_without_destination: props.settings.show_ride_without_destination === '1',
      show_incentive_feature_for_driver: props.settings.show_incentive_feature_for_driver === '1',
      // enable_document_auto_approval
      // enable_document_auto_approval: props.settings.enable_document_auto_approval === '1',
      logo:  props.settings.logo || "",
      favicon: props.settings.favicon || "",
      loginbg: props.settings.loginbg || "",
      admin_login: props.settings.admin_login || "",
      owner_login: props.settings.owner_login || "",
      dispatcher_login: props.settings.dispatcher_login || "",
      user_login: props.settings.user_login || "",
      owner_loginbg: props.settings.owner_loginbg || "",
      footer_content1: props.settings.footer_content1 || "",
      footer_content2: props.settings.footer_content2 || "",
      android_user: props.settings.android_user || "",
      android_driver: props.settings.android_driver || "",
      ios_user: props.settings.ios_user || "",
      ios_driver: props.settings.ios_driver || "",
    });

    // Define the URLs for existing images
    const logoURL = computed(() => props.logoURL || '');
    const faviconURL = computed(() => props.faviconURL || '');
    const loginbgURL = computed(() => props.loginbgURL || '');
    const owner_loginbgURL = computed(() => props.owner_loginbgURL || '');

    const validationRules = {};
    const validationRef = ref(null);
    const errors = ref({});
    const successMessage = ref(props.successMessage || '');
    const alertMessage = ref(props.alertMessage || '');

    const dismissMessage = () => {
      successMessage.value = "";
      alertMessage.value = "";
    };

const showModal = ref(false); // Modal state
const modalText = ref(''); // Text for the modal
const currentToggleField = ref(''); // Ensure it's initialized
const newToggleStatus = ref(false); // Ensure it's initialized
const isValidUrl = ref(false);

// Function to update the status when OK is clicked
const enableStatus = async () => {
  try {
    const status = newToggleStatus.value ? 1 : 0;
    await axios.post('/general-settings/update-status', { id: currentToggleField.value, status });
    form[currentToggleField.value] = newToggleStatus.value;
  } catch (error) {
    console.error('Error updating status', error);
  } finally {
    showModal.value = false; // Close the modal
  }
};

const cancelModal = () => {
    // Check if currentToggleField is set
    if (currentToggleField.value) {
        // Reset the form value based on the original props.settings value
        form[currentToggleField.value] = props.settings[currentToggleField.value] === '1'; // Convert to boolean
    } else {
        console.warn('No toggle field set to cancel.');
    }
    showModal.value = false; // Close the modal
};





// Function to confirm the toggle and open the modal
// const confirmToggle = (field, value, placeholder, offPlaceholder) => {
//   console.log('Confirming toggle for field:', field); // Log the field being toggled
//   currentToggleField.value = field; // Set the current toggle field
//   newToggleStatus.value = value; // Set the new status
//   // Check the toggle status and set the modal text accordingly
//   modalText.value = value ? `${placeholder}` : `${offPlaceholder}`;
//   showModal.value = true; // Show the modal
// };

const confirmToggle = async (field, value) => {
      if(props.app_for == "demo"){
        form[field] = !value;
        Swal.fire(t('error'), t('you_are_not_authorised'), 'error');
        return;
      }
      const placeholderText = value ? 'Enable' : 'Enable';
      const offPlaceholderText = value ? 'Disable' : 'Disable';

      try {
        const result = await Swal.fire({
          title: `Are you sure you want to ${value ? 'enable' : 'disable'} this setting?`,
          // text: value ? placeholderText : offPlaceholderText,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, proceed',
          cancelButtonText: 'Cancel'
        });

        if (result.isConfirmed) {
          await axios.post('/general-settings/update-status', { id: field, status: value ? 1 : 0 });
          form[field] = value;
          Swal.fire('Updated!', `Setting has been ${value ? 'enabled' : 'disabled'}.`, 'success');
        } else {
          form[field] = !value;
        }
      } catch (error) {
        console.error('Error updating status', error);
        form[field] = !value; // Reset toggle if error occurs
      }
    };



// Handle file selection and update form data
    const handleFileSelect = (fieldName) => (file) => {
      form[fieldName] = file;
    };


    const urlRegex = /^[a-z]+(-[a-z]+)*$/;

    watch(() => form.admin_login, (newValue) => {
      if (urlRegex.test(newValue)) {
        isValidUrl.value = true; // Set the flag to true for valid code
        errors.value.admin_login = '';
      } else {
        isValidUrl.value = false; // Set the flag to false for invalid code
        errors.value.admin_login = 'Invalid Login URL(valid URL : admin-login (or) admin)';
      }
    });

    watch(() => form.owner_login, (newValue) => {
      if (urlRegex.test(newValue)) {
        isValidUrl.value = true; // Set the flag to true for valid code
        errors.value.owner_login = '';
      } else {
        isValidUrl.value = false; // Set the flag to false for invalid code
        errors.value.owner_login = 'Invalid Login URL.(valid URL : owner-login (or) owner)';
      }
    });
    watch(() => form.dispatcher_login, (newValue) => {
      if (urlRegex.test(newValue)) {
        isValidUrl.value = true; // Set the flag to true for valid code
        errors.value.dispatcher_login = '';
      } else {
        isValidUrl.value = false; // Set the flag to false for invalid code
        errors.value.dispatcher_login = 'Invalid Login URL.(valid URL : owner-login (or) owner)';
      }
    });

    watch(() => form.user_login, (newValue) => {
      if (urlRegex.test(newValue)) {
        isValidUrl.value = true; // Set the flag to true for valid code
        errors.value.user_login = '';
      } else {
        isValidUrl.value = false; // Set the flag to false for invalid code
        errors.value.user_login = 'Invalid Login URL.(valid URL : user-login (or) user)';
      }
    });

    watch(() => form.android_user, (newValue) => {
      if (urlRegex.test(newValue)) {
        isValidUrl.value = true; // Set the flag to true for valid code
        errors.value.android_user = '';
      } else {
        isValidUrl.value = false; // Set the flag to false for invalid code
        errors.value.android_user = 'Invalid Application URL';
      }
    });

    watch(() => form.android_driver, (newValue) => {
      if (urlRegex.test(newValue)) {
        isValidUrl.value = true; // Set the flag to true for valid code
        errors.value.android_driver = '';
      } else {
        isValidUrl.value = false; // Set the flag to false for invalid code
        errors.value.android_driver = 'Invalid Application URL';
      }
    });
    watch(() => form.ios_user, (newValue) => {
      if (urlRegex.test(newValue)) {
        isValidUrl.value = true; // Set the flag to true for valid code
        errors.value.ios_user = '';
      } else {
        isValidUrl.value = false; // Set the flag to false for invalid code
        errors.value.ios_user = 'Invalid Application URL';
      }
    });

    watch(() => form.ios_driver, (newValue) => {
      if (urlRegex.test(newValue)) {
        isValidUrl.value = true; // Set the flag to true for valid code
        errors.value.ios_driver = '';
      } else {
        isValidUrl.value = false; // Set the flag to false for invalid code
        errors.value.ios_driver = 'Invalid Application URL';
      }
    });

    const handleSubmit = async () => {
      if(props.app_for == "demo"){
        Swal.fire(t('error'), t('you_are_not_authorised'), 'error');
        return;
      }
      errors.value = validationRef.value.validate();
      if (Object.keys(errors.value).length > 0) {
        return;
      }

      try {
        const formData = new FormData();

        // Append form fields
        for (const key in form.data()) {
          formData.append(key, form.data()[key]);
        }

        // Append files if they are provided
        if (form.logo) {
          formData.append('logo', form.logo);
        }
        if (form.favicon) {
          formData.append('favicon', form.favicon);
        }
        if (form.loginbg) {
          formData.append('loginbg', form.loginbg);
        }
        if (form.owner_loginbg) {
          formData.append('owner_loginbg', form.owner_loginbg);
        }

        let response = await axios.post(`/general-settings/update`, formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });

// console.log(response.status);
        if (response.status === 200) {
          successMessage.value = t('general_settings_updated_successfully');
          form.reset();
          router.get('/general-settings');
        } else {
          alertMessage.value = t('failed_to_update_general_settings');
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          errors.value = error.response.data.errors;
        } else {
          console.error(t('error_updating_general_settings'), error);
          alertMessage.value =t('failed_to_update_general_settings');
        }
      }
    };

    const handleImageSelected = (file, fieldName) => {
      if(props.app_for == "demo"){
        Swal.fire(t('error'), t('you_are_not_authorised'), 'error');
        return;
      }
      form[fieldName] = file;
    };

    const handleImageRemoved = (fieldName) => {
      if(props.app_for == "demo"){
        Swal.fire(t('error'), t('you_are_not_authorised'), 'error');
        return;
      }
      form[fieldName] = null;
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
      handleFileSelect,
      modalText,
      showModal,
      confirmToggle,
      enableStatus,
      logoURL,
      faviconURL,
      loginbgURL,
      owner_loginbgURL,
      handleImageSelected,
      handleImageRemoved,
      cancelModal
    };
  }
};
</script>


<template>
  <Layout>

    <Head title="General Settings" />
    <PageHeader :title="$t('general-settings')" :pageTitle="$t('general-settings')" />
    <BRow>
        <BCard v-if="app_for === 'demo'" no-body id="tasksList">
          <BCardHeader class="border-0">
            <div class="alert bg-warning border-warning fs-18" role="alert">
              <strong> {{$t('note')}} : <em> {{$t('actions_restricted_due_to_demo_mode')}}</em> </strong>
          </div>
        </BCardHeader>
      </BCard>
      <BCol lg="12">
        <BCard no-body id="tasksList">
          <BCardHeader class="border-0"></BCardHeader>
          <BCardBody class="border border-dashed border-end-0 border-start-0">
            <form @submit.prevent="handleSubmit">
              <FormValidation :form="form" :rules="validationRules" ref="validationRef">
                <div class="row">
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="sidebar_color" class="form-label">{{$t("admin_theme")}}</label>
                      <input type="text" class="form-control" :readonly="app_for === 'demo'" :placeholder="$t('enter_side_bar_color')" id="sidebar_color" v-model="form.sidebar_color" />
                      <span v-for="(error, index) in errors.sidebar_color" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="nav_color" class="form-label">{{$t("landing_website_theme")}}</label>
                      <input type="text" class="form-control" :readonly="app_for === 'demo'" :placeholder="$t('enter_navbar_color')" id="nav_color" v-model="form.nav_color" />
                      <span v-for="(error, index) in errors.nav_color" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="sidebar_text_color" class="form-label">{{$t("side_text_bar_color")}}</label>
                      <input type="text" class="form-control" :readonly="app_for === 'demo'" :placeholder="$t('enter_side_text_bar_color')" id="sidebar_text_color" v-model="form.sidebar_text_color" />
                      <span v-for="(error, index) in errors.sidebar_text_color" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="app_name" class="form-label">{{$t("app_name")}}</label>
                      <input type="text" class="form-control" :readonly="app_for === 'demo'" :placeholder="$t('enter_app_name')" id="app_name" v-model="form.app_name" />
                      <span v-for="(error, index) in errors.app_name" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="currency_code" class="form-label">{{$t("currency_code")}}</label>
                      <input type="text" class="form-control" :readonly="app_for === 'demo'" :placeholder="$t('enter_currency_code')" id="currency_code" v-model="form.currency_code" />
                      <span v-for="(error, index) in errors.currency_code" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="currency_symbol" class="form-label">{{$t("currency_symbol")}}</label>
                      <input type="text" class="form-control" :readonly="app_for === 'demo'" :placeholder="$t('enter_currency_symbol')" id="currency_symbol" v-model="form.currency_symbol" />
                      <span v-for="(error, index) in errors.currency_symbol" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="contact_us_mobile1" class="form-label">{{$t("contact_us_mobile_1")}}</label>
                      <input type="text" class="form-control" :readonly="app_for === 'demo'" :placeholder="$t('enter_contact_us_mobile_1')" id="contact_us_mobile1" v-model="form.contact_us_mobile1" />
                      <span v-for="(error, index) in errors.contact_us_mobile1" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="contact_us_mobile2" class="form-label">{{$t("contact_us_mobile_2")}}</label>
                      <input type="text" class="form-control" :readonly="app_for === 'demo'" :placeholder="$t('enter_contact_us_mobile_2')" id="contact_us_mobile2" v-model="form.contact_us_mobile2" />
                      <span v-for="(error, index) in errors.contact_us_mobile2" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="contact_us_link" class="form-label">{{$t("contact_us_link")}}</label>
                      <input type="text" class="form-control" :readonly="app_for === 'demo'" :placeholder="$t('enter_contact_us_link')" id="contact_us_link" v-model="form.contact_us_link" />
                      <span v-for="(error, index) in errors.contact_us_link" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="default_latitude" class="form-label">{{$t("default_latitude")}}</label>
                      <input type="text" class="form-control" :readonly="app_for === 'demo'" :placeholder="$t('enter_default_latitude')" id="default_latitude" v-model="form.default_latitude" />
                      <span v-for="(error, index) in errors.default_latitude" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="default_longitude" class="form-label">{{$t("default_longitude")}}</label>
                      <input type="text" class="form-control" :readonly="app_for === 'demo'" :placeholder="$t('enter_default_longitude')" id="default_longitude" v-model="form.default_longitude" />
                      <span v-for="(error, index) in errors.default_longitude" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <!-- <div class="col-sm-6">
                    <div class="mb-3">
                      <div class="border rounded">
                        <div class="row">
                          <div class="col">
                            <label class="form-check-label p-2 mt-2" for="show_outstation_ride_feature">{{$t("show_outstation_ride_feature")}} </label>
                          </div>
                          <div class="col">
                            <div class="form-check form-switch form-switch-md float-end p-2" >
                             <input
                                type="checkbox"
                                :placeholder="'If you turned on this, it will Outstation Ride Featire'"
                                :offPlaceholder="'Are You Sure? if You Disable This, it will not Outstation Ride Featire'"
                                class="form-check-input"
                                id="show_outstation_ride_feature"
                                v-model="form.show_outstation_ride_feature"
                                @change.prevent="confirmToggle('show_outstation_ride_feature', form.show_outstation_ride_feature, $event.target.placeholder, $event.target.getAttribute('offPlaceholder'))"
                              />  
                            </div>                            
                          </div>
                        </div>
                      </div>
                      <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error.show_outstation_ride_feature }}</span>
                    </div>
                  </div> -->
                 <!-- <div class="col-sm-6">
                    <div class="mb-3">
                      <div class="border rounded">
                        <div class="row">
                          <div class="col">
                            <label class="form-check-label p-2 mt-2" for="enable_document_auto_approval">{{$t("enable_document_auto_approval")}} </label>
                          </div>
                          <div class="col">
                            <div class="form-check form-switch form-switch-md float-end p-2" >
                             <input
                                type="checkbox"
                                :placeholder="'If you turned on this, it will make Auto Approval Driver Documents'"
                                :offPlaceholder="'Are You Sure? if You Disable This, it will not Auto Approval Driver Documents'"
                                class="form-check-input"
                                id="enable_document_auto_approval"
                                v-model="form.enable_document_auto_approval"
                                @change.prevent="confirmToggle('enable_document_auto_approval', form.enable_document_auto_approval, $event.target.placeholder, $event.target.getAttribute('offPlaceholder'))"
                              />  
                            </div>                            
                          </div>
                        </div>
                      </div>
                      <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error.show_outstation_ride_feature }}</span>
                    </div>
                  </div>  -->

             <div class="col-lg-12">
              <div class="card">
                  <div class="card-header bg-light-subtle">
                  <h5>{{$t("url_setup")}}</h5> 
                  </div>
                <div class="card-body bg-light-subtle" data-bs-toggle="collapse" href="#contactInitiated" role="button" aria-expanded="false" aria-controls="contactInitiated">
                  <div class="row">
                    <div class="row">
                        <div class="col-sm-6">
                          <div class="mb-3">
                              <label for="admin_login" class="form-label">{{$t("admin_login_url")}}</label>
                              <input type="text" :readonly="app_for === 'demo'" :class="{ 'border-success': isValidUrl, 'border-danger': errors.admin_login }" class="form-control" :placeholder="$t('enter_admin_url')" id="admin_login" v-model="form.admin_login" />
                              <span v-for="(error, index) in errors.admin_login" :key="index" class="text-danger">{{ error }}</span>
                          </div>
                          <div class="card-header bg-warning-subtle border border-dashed">
                              <div class="text-center">
                                  <h6 class="mb-0">{{$t("example")}}: <span class="fw-semibold">www.ondemandapp.com/login/<span class="bg-success text-white fw-bold p-1">{{form.admin_login}}</span></span></h6>
                              </div>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="mb-3">
                              <label for="owner_login" class="form-label">{{$t("owner_login_url")}}</label>
                              <input type="text" :readonly="app_for === 'demo'" :class="{ 'border-success': isValidUrl, 'border-danger': errors.owner_login }" class="form-control" :placeholder="$t('enter_owner_url')" id="owner_login" v-model="form.owner_login" />
                              <span v-for="(error, index) in errors.owner_login" :key="index" class="text-danger">{{ error }}</span>
                          </div>
                          <div class="card-header bg-warning-subtle border border-dashed">
                              <div class="text-center">
                                  <h6 class="mb-0">{{$t("example")}}: <span class="fw-semibold">www.ondemandapp.com/login/<span class="bg-success text-white fw-bold p-1">{{form.owner_login}}</span></span></h6>
                              </div>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="mb-3">
                              <label for="dispatcher_login" class="form-label">{{$t("dispatcher_login_url")}}</label>
                              <input type="text" :readonly="app_for === 'demo'" :class="{ 'border-success': isValidUrl, 'border-danger': errors.dispatcher_login }" class="form-control" :placeholder="$t('enter_owner_url')" id="dispatcher_login" v-model="form.dispatcher_login" />
                              <span v-for="(error, index) in errors.dispatcher_login" :key="index" class="text-danger">{{ error }}</span>
                          </div>
                          <div class="card-header bg-warning-subtle border border-dashed">
                              <div class="text-center">
                                  <h6 class="mb-0">{{$t("example")}}: <span class="fw-semibold">www.ondemandapp.com/login/<span class="bg-success text-white fw-bold p-1">{{form.dispatcher_login}}</span></span></h6>
                              </div>
                          </div>
                        </div>
                        <div class="col-sm-6 mt-0">
                          <div class="mb-3">
                              <label for="user_login" class="form-label">{{$t("user_login_url")}}</label>
                              <input type="text" :readonly="app_for === 'demo'" :class="{ 'border-success': isValidUrl, 'border-danger': errors.user_login }" class="form-control" :placeholder="$t('enter_user_url')" id="user_login" v-model="form.user_login" />
                              <span v-for="(error, index) in errors.user_login" :key="index" class="text-danger">{{ error }}</span>
                          </div>
                          <div class="card-header bg-warning-subtle border border-dashed">
                              <div class="text-center">
                                  <h6 class="mb-0">{{$t("example")}}: <span class="fw-semibold">www.ondemandapp.com/login/<span class="bg-success text-white fw-bold p-1">{{form.user_login}}</span></span></h6>
                              </div>
                          </div>
                        </div>
                    </div> 
                  </div> 
                </div> 
              </div> 
            </div> 
            <div class="col-lg-12">
              <div class="card">
                  <div class="card-header bg-light-subtle">
                  <h5>{{$t("url_setup")}}</h5> 
                  </div>
                <div class="card-body bg-light-subtle" data-bs-toggle="collapse" href="#contactInitiated" role="button" aria-expanded="false" aria-controls="contactInitiated">
                  <div class="row">
                    <div class="row">
                        <div class="col-sm-6">
                          <div class="mb-3">
                              <label for="android_user" class="form-label">{{$t("android_user_url")}}</label>
                              <input type="text" :readonly="app_for === 'demo'" :class="{ 'border-success': isValidUrl, 'border-danger': errors.android_user }" class="form-control" :placeholder="$t('enter_admin_url')" id="android_user" v-model="form.android_user" />
                              <span v-for="(error, index) in errors.android_user" :key="index" class="text-danger">{{ error }}</span>
                          </div>
                          <div class="card-header bg-warning-subtle border border-dashed">
                              <div class="text-center">
                                  <h6 class="mb-0">{{$t("example")}}: <span class="fw-semibold"><span class="bg-success text-white fw-bold p-1">{{form.android_user}}</span></span></h6>
                              </div>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="mb-3">
                              <label for="android_driver" class="form-label">{{$t("android_driver_url")}}</label>
                              <input type="text" :readonly="app_for === 'demo'" :class="{ 'border-success': isValidUrl, 'border-danger': errors.android_driver }" class="form-control" :placeholder="$t('enter_owner_url')" id="android_driver" v-model="form.android_driver" />
                              <span v-for="(error, index) in errors.android_driver" :key="index" class="text-danger">{{ error }}</span>
                          </div>
                          <div class="card-header bg-warning-subtle border border-dashed">
                              <div class="text-center">
                                  <h6 class="mb-0">{{$t("example")}}: <span class="fw-semibold"><span class="bg-success text-white fw-bold p-1">{{form.android_driver}}</span></span></h6>
                              </div>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="mb-3">
                              <label for="ios_user" class="form-label">{{$t("ios_user_url")}}</label>
                              <input type="text" :readonly="app_for === 'demo'" :class="{ 'border-success': isValidUrl, 'border-danger': errors.ios_user }" class="form-control" :placeholder="$t('enter_owner_url')" id="ios_user" v-model="form.ios_user" />
                              <span v-for="(error, index) in errors.ios_user" :key="index" class="text-danger">{{ error }}</span>
                          </div>
                          <div class="card-header bg-warning-subtle border border-dashed">
                              <div class="text-center">
                                  <h6 class="mb-0">{{$t("example")}}: <span class="fw-semibold"><span class="bg-success text-white fw-bold p-1">{{form.ios_user}}</span></span></h6>
                              </div>
                          </div>
                        </div>
                        <div class="col-sm-6 mt-0">
                          <div class="mb-3">
                              <label for="ios_driver" class="form-label">{{$t("ios_driver_url")}}</label>
                              <input type="text" :readonly="app_for === 'demo'" :class="{ 'border-success': isValidUrl, 'border-danger': errors.ios_driver }" class="form-control" :placeholder="$t('enter_user_url')" id="ios_driver" v-model="form.ios_driver" />
                              <span v-for="(error, index) in errors.ios_driver" :key="index" class="text-danger">{{ error }}</span>
                          </div>
                          <div class="card-header bg-warning-subtle border border-dashed">
                              <div class="text-center">
                                  <h6 class="mb-0">{{$t("example")}}: <span class="fw-semibold"><span class="bg-success text-white fw-bold p-1">{{form.ios_driver}}</span></span></h6>
                              </div>
                          </div>
                        </div>
                    </div> 
                  </div> 
                </div> 
              </div> 
            </div>
<!-- image section  -->
          <div class="col-lg-12">
              <div class="card">
                <div class="card-header bg-light-subtle">
                 <h5>{{$t("image_section")}}</h5> 
                </div>
                <div class="card-body bg-light-subtle" data-bs-toggle="collapse" href="#contactInitiated" role="button" aria-expanded="false" aria-controls="contactInitiated">
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <label for="login_page_background_image" class="form-label d-flex">{{$t("login_page_background_image")}}
                          <span><h5 class="text-muted mt-1 mb-0 fs-13">(5450px x 3650px)</h5></span>
                        </label>
                        <ImageUpload  :imageType="'system-admin'"  :flexStyle="'0 0 calc(95% - 20px)'" :aspectRatio="'10 / 6'"  :initialImageUrl="form.loginbg"  
                            @image-selected="(file) => handleImageSelected(file, 'loginbg')" @image-removed="() => handleImageRemoved('loginbg')">
                        </ImageUpload>
                        <span v-if="form.errors.loginbg" class="text-danger">{{ form.errors.loginbg }}</span>
                      </div>  
                    </div>
                    <div class="col-sm-6">
                      <div class="row">
                        <div class="col-sm-12">
                          <div class="mb-3">
                            <label for="logo" class="form-label d-flex">{{$t("logo")}}
                              <span><h5 class="text-muted mt-1 mb-0 fs-13">(750px x 100px)</h5></span>
                            </label>
                            <ImageUpload  :imageType="'system-admin'"  :flexStyle="'0 0 calc(95% - 20px)'" :aspectRatio="'11 / 2'"  :initialImageUrl="form.logo"  
                                @image-selected="(file) => handleImageSelected(file, 'logo')" @image-removed="() => handleImageRemoved('logo')">
                            </ImageUpload>
                            <span v-if="form.errors.logo" class="text-danger">{{ form.errors.logo }}</span>
                          </div>  
                        </div> 
                        <div class="col-sm-12">
                          <div class="mb-3">
                            <label for="favicon" class="form-label d-flex">{{$t("favicon")}}
                              <span><h5 class="text-muted mt-1 mb-0 fs-13">(80px x 80px)</h5></span>
                            </label>
                            <ImageUpload class="text-center"  :imageType="'system-admin'"  :flexStyle="'0 0 calc(47% - 100px)'" :aspectRatio="'1 / 1'"  :initialImageUrl="form.favicon"  
                                @image-selected="(file) => handleImageSelected(file, 'favicon')" @image-removed="() => handleImageRemoved('favicon')">
                            </ImageUpload>
                            <span v-if="form.errors.favicon" class="text-danger">{{ form.errors.favicon }}</span>
                          </div>  
                        </div> 
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <label for="owner_login_page_background_image" class="form-label d-flex">{{$t("owner_login_page_background_image")}}
                          <span><h5 class="text-muted mt-1 mb-0 fs-13">(5450px x 3650px)</h5></span>
                        </label>
                        <ImageUpload  :imageType="'system-admin'"  :flexStyle="'0 0 calc(95% - 20px)'" :aspectRatio="'10 / 6'"  :initialImageUrl="form.owner_loginbg"  
                            @image-selected="(file) => handleImageSelected(file, 'owner_loginbg')" @image-removed="() => handleImageRemoved('owner_loginbg')">
                        </ImageUpload>
                        <span v-if="form.errors.owner_loginbg" class="text-danger">{{ form.errors.owner_loginbg }}</span>
                      </div>  
                    </div>
                  </div>
                </div>
            </div>               
          </div>  
<!-- image section end  -->
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <label for="footer_content_1" class="form-label">{{$t("footer_content_1")}}</label>
                        <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_footer_content_1')" id="footer_content1" v-model="form.footer_content1" />
                        <span v-for="(error, index) in errors.footer_content1" :key="index" class="text-danger">{{ error }}</span>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <label for="footer_content_2" class="form-label">{{$t("footer_content_2")}}</label>
                        <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_footer_content_2')" id="footer_content2" v-model="form.footer_content2" />
                        <span v-for="(error, index) in errors.footer_content2" :key="index" class="text-danger">{{ error }}</span>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-12" v-if="permissions.includes('general-settings-view')">
                    <div class="text-end">
                      <button type="submit" class="btn btn-primary"> {{ settings ? $t('update') : $t('save') }}</button>
                    </div>
                  </div>
                </div>  
              </FormValidation>
            </form>
          </BCardBody>
        </BCard>
      </BCol>
    </BRow>
    <div>
<!-- Modal for Confirmation -->
<!-- Modal for Confirmation -->
<div v-if="showModal" id="removeNotificationModal" class="modal fade show d-block" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirmation</h5>
        <button type="button" class="btn-close" @click="showModal = false"></button>
      </div>
      <div class="modal-body">
        <!-- Display the dynamic modal text -->
        <p>{{ modalText }}</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" @click="cancelModal">Cancel</button>
        <button type="button" class="btn btn-primary" @click="enableStatus">OK</button>
      </div>
    </div>
  </div>
</div>

<!-- modal ends -->
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




  </Layout>
</template>

<style>
.custom-alert {
  max-width: 600px;
  float: right;
  position: fixed;
  top: 90px;
  right: 20px;
}
.rtl .custom-alert {
  max-width: 600px;
  float: left;
  top: -300px;
  right: 10px;
}
@media only screen and (max-width: 1024px) {
  .custom-alert {
  max-width: 600px;
  float: right;
  position: fixed;
  top: 90px;
  right: 20px;
}
.rtl .custom-alert {
  max-width: 600px;
  float: left;
  top: -230px;
  right: 10px;
}
}
</style>
