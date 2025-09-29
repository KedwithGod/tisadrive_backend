<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import { ref, computed } from "vue";
import axios from "axios";
import Multiselect from "@vueform/multiselect";
import FormValidation from "@/Components/FormValidation.vue";
import logo from "@/Components/widgets/logo.vue";
import favicon from "@/Components/widgets/favicon.vue";
import loginbg from "@/Components/widgets/loginbg.vue";
import { useI18n } from 'vue-i18n';
import ImageUpload from "@/Components/ImageUpload.vue";
import Swal from "sweetalert2";

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
  props: {
    successMessage: String,
    alertMessage: String,
    app_for: String,
    settings: Object,
    countries: Object,
    validate: Function, // Define the prop to receive the method
  },
  setup(props) {
    // console.log(props.settings);
    const { t } = useI18n();
    // Initialize the form with default or existing values 
    const form = useForm({
      enable_vase_map: props.settings.enable_vase_map === '1',
      enable_modules_for_applications: props.settings.enable_modules_for_applications || "",
      default_country_code_for_mobile_app:props.settings.default_country_code_for_mobile_app || "",
      // default_currency_code_for_mobile_app:props.settings.default_currency_code_for_mobile_app || "",
      enable_country_restrict_on_map: props.settings.enable_country_restrict_on_map === '1',
      show_wallet_feature_on_mobile_app: props.settings.show_wallet_feature_on_mobile_app === '1',
      show_wallet_feature_on_mobile_app_driver: props.settings.show_wallet_feature_on_mobile_app_driver === '1',
      show_wallet_feature_on_mobile_app_owner: props.settings.show_wallet_feature_on_mobile_app_owner === '1',
      show_instant_ride_feature_on_mobile_app: props.settings.show_instant_ride_feature_on_mobile_app === '1',
      show_owner_module_feature_on_mobile_app: props.settings.show_owner_module_feature_on_mobile_app === '1',
      show_wallet_money_transfer_feature_on_mobile_app: props.settings.show_wallet_money_transfer_feature_on_mobile_app === '1',
      show_wallet_money_transfer_feature_on_mobile_app_for_driver: props.settings.show_wallet_money_transfer_feature_on_mobile_app_for_driver === '1',
      show_wallet_money_transfer_feature_on_mobile_app_for_owner: props.settings.show_wallet_money_transfer_feature_on_mobile_app_for_owner === '1',
      show_email_otp_feature_on_mobile_app: props.settings.show_email_otp_feature_on_mobile_app === '1',
      // show_bank_info_feature_on_mobile_app: props.settings.show_bank_info_feature_on_mobile_app === '1',
      show_delivery_rental_ride_feature: props.settings.show_delivery_rental_ride_feature === '1',
      show_card_payment_feature: props.settings.show_card_payment_feature === '1',
      show_taxi_rental_ride_feature: props.settings.show_taxi_rental_ride_feature === '1',      
      show_ride_otp_feature: props.settings.show_ride_otp_feature === '1',
      show_delivery_ride_pick_otp_feature: props.settings.show_delivery_ride_pick_otp_feature === '1',
      show_delivery_ride_drop_otp_feature: props.settings.show_delivery_ride_drop_otp_feature === '1',
      show_ride_later_feature: props.settings.show_ride_later_feature === '1',
      show_outstation_ride_feature: props.settings.show_outstation_ride_feature === '1',
      show_delivery_outstation_ride_feature: props.settings.show_delivery_outstation_ride_feature === '1',
      show_ride_without_destination: props.settings.show_ride_without_destination === '1',
      show_incentive_feature_for_driver: props.settings.show_incentive_feature_for_driver === '1',
      enable_shipment_load_feature: props.settings.enable_shipment_load_feature === '1',
      enable_shipment_unload_feature: props.settings.enable_shipment_unload_feature  ===  '1',
      enable_document_auto_approval: props.settings.enable_document_auto_approval === '1',
      enable_digital_signature: props.settings.enable_digital_signature  ===  '1',
      enable_pet_preference_for_user: props.settings.enable_pet_preference_for_user  ===  '1',
      enable_luggage_preference_for_user: props.settings.enable_luggage_preference_for_user  ===  '1',
      // enable_my_route_booking_feature: props.settings.enable_my_route_booking_feature  ===  '1',
      enable_web_booking_feature: props.settings.enable_web_booking_feature  ===  '1',
      enable_document_auto_approval: props.settings.enable_document_auto_approval === '1',
      enable_landing_site: props.settings.enable_landing_site  ===  '1',
      enable_additional_charge_feature: props.settings.enable_additional_charge_feature  ===  '1',
      enable_sub_vehicle_feature: props.settings.enable_sub_vehicle_feature  ===  '1',
      show_driver_level_feature: props.settings.show_driver_level_feature  ===  '1',
      // enable_driver_tips_feature: props.settings.enable_driver_tips_feature  ===  '1',
      enable_driver_profile_disapprove_on_update: props.settings.enable_driver_profile_disapprove_on_update  ===  '1',
      enable_support_ticket_feature: props.settings.enable_support_ticket_feature  ===  '1',
      enable_map_appearance_change_on_mobile_app: props.settings.enable_map_appearance_change_on_mobile_app  ===  '1',
      enable_eta_total_update: props.settings.enable_eta_total_update  ===  '1',
      enable_driver_leaderboard_feature:props.settings.enable_driver_leaderboard_feature === '1',
    });

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


// Function to update the status when OK is clicked
const enableStatus = async () => {
  try {
    const status = newToggleStatus.value ? 1 : 0;
    await axios.post('/customization-settings/update-status', { id: currentToggleField.value, status });
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
          await axios.post('/customization-settings/update-status', { id: field, status: value ? 1 : 0 });
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
        let response;

        if (props.settings) {
          response = await axios.post(`/customization-settings/update`, form.data());
        } else {
          alertMessage.value = t('failed_to_update_customization_settings');
        }
        if (response.status === 200) {
          successMessage.value = t('customization_settings_created_successfully');
          form.reset();
          router.get('/customization-settings');
        } else {
          alertMessage.value = t('failed_to_update_customization_settings');
        }
      }catch (error) {
        if (error.response && error.response.status === 422) {
          errors.value = error.response.data.errors;
        } else {
          console.error(t('error_updating_customization_settings'), error);
          alertMessage.value =t('failed_to_update_customization_settings');
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
      handleImageSelected,
      handleImageRemoved,
      cancelModal
    };
  }
};
</script>


<template>
  <Layout>

    <Head title="Customization Settings" />
    <PageHeader :title="$t('customization-settings')" :pageTitle="$t('customization-settings')" />
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
          <form @submit.prevent="handleSubmit">
            <FormValidation :form="form" :rules="validationRules" ref="validationRef">
            <BCardHeader class="border-0">
              <h5>{{$t("general-settings")}}</h5>
            </BCardHeader>
            <BCardBody class="border border-dashed border-end-0 border-start-0">
                  <div class="row">                   
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <label for="enable_modules_for_applications" class="form-label">{{ $t("enable_modules_for_applications") }}</label>
                        <select id="enable_modules_for_applications":disabled="app_for === 'demo'"  class="form-select" v-model="form.enable_modules_for_applications">
                          <option disabled value="">{{$t("select")}}</option>
                          <option value="taxi">{{$t("taxi")}}</option>
                          <option value="delivery">{{$t("delivery")}}</option>
                          <option value="both">{{$t("all")}}</option>
                        </select>
                        <span v-for="(error, index) in errors.enable_modules_for_applications" :key="index" class="text-danger">{{ error }}</span>
                      </div>
                    </div>
                    <!-- <div class="col-sm-6">
                      <div class="mb-3">
                        <label for="default_currency_code_for_mobile_app" class="form-label">{{$t("default_currency_code_for_mobile_app")}}</label>
                        <input type="text" class="form-control" :readonly="app_for === 'demo'" :placeholder="$t('enter_default_currency_code_for_mobile_app')" id="default_currency_code_for_mobile_app" v-model="form.default_currency_code_for_mobile_app" />
                        <span v-for="(error, index) in errors.default_currency_code_for_mobile_app" :key="index" class="text-danger">{{ error }}</span>
                      </div>
                    </div> -->
                     <!-- Country Code Dropdown -->
        <div class="col-sm-6">
          <div class="mb-3">
            <label for="default_country_code_for_mobile_app" class="form-label">{{$t("default_country_code_for_mobile_app")}}</label>
            <select
              id="default_country_code_for_mobile_app"
              class="form-select"
              v-model="form.default_country_code_for_mobile_app"
            >
              <option disabled value="">Select Country Code</option>
              <option
                v-for="(code, id) in countries"
                :key="id"
                :value="code"
              >
                {{ code }}
              </option>
            </select>
          </div>
        </div>
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <div class="border rounded">
                          <div class="row">
                            <div class="col">
                              <label class="form-check-label p-2 mt-2" for="enable_vase_map">{{$t("show_waze_map")}}</label>
                            </div>
                            <div class="col">
                              <div class="form-check form-switch form-switch-md float-end p-2">
                                <input
                                  type="checkbox"
                                  :placeholder="'If you turned on this. it will display User Application Vase Map'"
                                  :offPlaceholder="'Are You Sure? if You Disable This it will will not display User Application Vase Map'"
                                  class="form-check-input"
                                  id="enable_vase_map"
                                  v-model="form.enable_vase_map"
                                  @change.prevent="confirmToggle('enable_vase_map', form.enable_vase_map, $event.target.placeholder, $event.target.getAttribute('offPlaceholder'))"
                                />                    
                              </div>                            
                            </div>
                          </div>
                        </div>
                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error.enable_vase_map }}</span>
                      </div>
                    </div>  
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <div class="border rounded">
                          <div class="row">
                            <div class="col">
                              <label class="form-check-label p-2 mt-2" for="show_wallet_feature_on_mobile_app">{{$t("show_wallet_feature_on_mobile_app_user")}}</label>
                            </div>
                            <div class="col">
                              <div class="form-check form-switch form-switch-md float-end p-2">
                                <input
                                  type="checkbox"
                                  :placeholder="'If you turned on this. it will display User Application Wallet-related transactions'"
                                  :offPlaceholder="'Are You Sure? if You Disable This it will will not display User Application Wallet-related transactions'"
                                  class="form-check-input"
                                  id="show_wallet_feature_on_mobile_app"
                                  v-model="form.show_wallet_feature_on_mobile_app"
                                  @change.prevent="confirmToggle('show_wallet_feature_on_mobile_app', form.show_wallet_feature_on_mobile_app, $event.target.placeholder, $event.target.getAttribute('offPlaceholder'))"
                                />                    
                              </div>                            
                            </div>
                          </div>
                        </div>
                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error.show_wallet_feature_on_mobile_app }}</span>
                      </div>
                    </div>  
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <div class="border rounded">
                          <div class="row">
                            <div class="col">
                              <label class="form-check-label p-2 mt-2" for="show_wallet_feature_on_mobile_app_driver">{{$t("show_wallet_feature_on_mobile_app_driver")}}</label>
                            </div>
                            <div class="col">
                              <div class="form-check form-switch form-switch-md float-end p-2" >
                                <input
                                  type="checkbox"
                                  :placeholder="'If you turned on this, it will display Driver Application Wallet-related transactions'"
                                  :offPlaceholder="'Are You Sure? if You Disable This, it will not display Driver Application Wallet-related transactions'"
                                  class="form-check-input"
                                  id="show_wallet_feature_on_mobile_app_driver"
                                  v-model="form.show_wallet_feature_on_mobile_app_driver"
                                  @change.prevent="confirmToggle('show_wallet_feature_on_mobile_app_driver', form.show_wallet_feature_on_mobile_app_driver, $event.target.placeholder, $event.target.getAttribute('offPlaceholder'))"
                                />                                 
                              </div>                            
                            </div>
                          </div>
                        </div>
                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error.show_wallet_feature_on_mobile_app_driver }}</span>
                      </div>
                    </div> 
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <div class="border rounded">
                          <div class="row">
                            <div class="col">
                              <label class="form-check-label p-2 mt-2" for="show_wallet_feature_on_mobile_app_owner">{{$t("show_wallet_feature_on_mobile_app_owner")}}</label>
                            </div>
                            <div class="col">
                              <div class="form-check form-switch form-switch-md float-end p-2" >
                                <input
                                  type="checkbox"
                                  :placeholder="'If you turned on this, it will display Owner Application Wallet-related transactions'"
                                  :offPlaceholder="'Are You Sure? if You Disable This, it will not display Owner Application Wallet-related transactions'"
                                  class="form-check-input"
                                  id="show_wallet_feature_on_mobile_app_owner"
                                  v-model="form.show_wallet_feature_on_mobile_app_owner"
                                  @change.prevent="confirmToggle('show_wallet_feature_on_mobile_app_owner', form.show_wallet_feature_on_mobile_app_owner, $event.target.placeholder, $event.target.getAttribute('offPlaceholder'))"
                                />                                 
                              </div>                            
                            </div>
                          </div>
                        </div>
                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error.show_wallet_feature_on_mobile_app_owner }}</span>
                      </div>
                    </div> 
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <div class="border rounded">
                          <div class="row">
                            <div class="col">
                              <label class="form-check-label p-2 mt-2" for="show_instant_ride_feature_on_mobile_app">{{$t("show_instant_ride_feature_on_mobile_app")}} </label>
                            </div>
                            <div class="col">                                                  
                              <div class="form-check form-switch form-switch-md float-end p-2" >
                                <input
                                  type="checkbox"
                                  :placeholder="'If you turned on this, it will display Driver Application Instant Ride Feature'"
                                  :offPlaceholder="'Are You Sure? if You Disable This, it will not display Driver Application  Instant Ride Feature'"
                                  class="form-check-input"
                                  id="show_instant_ride_feature_on_mobile_app"
                                  v-model="form.show_instant_ride_feature_on_mobile_app"
                                  @change.prevent="confirmToggle('show_instant_ride_feature_on_mobile_app', form.show_instant_ride_feature_on_mobile_app, $event.target.placeholder, $event.target.getAttribute('offPlaceholder'))"
                                />                                                           
                                </div>                            
                            </div>
                          </div>
                        </div>
                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error.show_instant_ride_feature_on_mobile_app }}</span>
                      </div>
                    </div>  
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <div class="border rounded">
                          <div class="row">
                            <div class="col">
                              <label class="form-check-label p-2 mt-2" for="show_wallet_money_transfer_feature_on_mobile_app">{{$t("show_wallet_money_transfer_feature_on_mobile_app_for_user")}} </label>
                            </div>
                            <div class="col">
                              <div class="form-check form-switch form-switch-md float-end p-2">
                                <input
                                  type="checkbox"
                                  :placeholder="'If you turned on this, it will show Wallet Money Tansfer Feature in User Application'"
                                  :offPlaceholder="'Are You Sure? if You Disable This, it will not show Wallet Money Tansfer Feature in User Application'"
                                  class="form-check-input"
                                  id="show_wallet_money_transfer_feature_on_mobile_app"
                                  v-model="form.show_wallet_money_transfer_feature_on_mobile_app"
                                  @change.prevent="confirmToggle('show_wallet_money_transfer_feature_on_mobile_app', form.show_wallet_money_transfer_feature_on_mobile_app, $event.target.placeholder, $event.target.getAttribute('offPlaceholder'))"
                                />                               
                              </div>                            
                            </div>
                          </div>
                        </div>
                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error.show_wallet_money_transfer_feature_on_mobile_app }}</span>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <div class="border rounded">
                          <div class="row">
                            <div class="col">
                              <label class="form-check-label p-2 mt-2" for="show_wallet_money_transfer_feature_on_mobile_app_for_driver">{{$t("show_wallet_money_transfer_feature_on_mobile_app_for_driver")}} </label>
                            </div>
                            <div class="col">
                              <div class="form-check form-switch form-switch-md float-end p-2" >
                                <input
                                  type="checkbox"
                                  :placeholder="'If you turned on this, it will show Wallet Money Tansfer Feature in Driver Application'"
                                  :offPlaceholder="'Are You Sure? if You Disable This, it will not show Wallet Money Tansfer Feature in Driver Application'"
                                  class="form-check-input"
                                  id="show_wallet_money_transfer_feature_on_mobile_app_for_driver"
                                  v-model="form.show_wallet_money_transfer_feature_on_mobile_app_for_driver"
                                  @change.prevent="confirmToggle('show_wallet_money_transfer_feature_on_mobile_app_for_driver', form.show_wallet_money_transfer_feature_on_mobile_app_for_driver, $event.target.placeholder, $event.target.getAttribute('offPlaceholder'))"
                                />                             
                              </div>                            
                            </div>
                          </div>
                        </div>
                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error.show_wallet_money_transfer_feature_on_mobile_app_for_driver }}</span>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <div class="border rounded">
                          <div class="row">
                            <div class="col">
                              <label class="form-check-label p-2 mt-2" for="show_wallet_money_transfer_feature_on_mobile_app_for_owner">{{$t("show_wallet_money_transfer_feature_on_mobile_app_for_owner")}} </label>
                            </div>
                            <div class="col">
                              <div class="form-check form-switch form-switch-md float-end p-2" >
                                <input
                                  type="checkbox"
                                  :placeholder="'If you turned on this, it will show Wallet Money Tansfer Feature in Owner Application'"
                                  :offPlaceholder="'Are You Sure? if You Disable This, it will not show Wallet Money Tansfer Feature in Owner Application'"
                                  class="form-check-input"
                                  id="show_wallet_money_transfer_feature_on_mobile_app_for_owner"
                                  v-model="form.show_wallet_money_transfer_feature_on_mobile_app_for_owner"
                                  @change.prevent="confirmToggle('show_wallet_money_transfer_feature_on_mobile_app_for_owner', form.show_wallet_money_transfer_feature_on_mobile_app_for_owner, $event.target.placeholder, $event.target.getAttribute('offPlaceholder'))"
                                />                             
                              </div>                            
                            </div>
                          </div>
                        </div>
                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error.show_wallet_money_transfer_feature_on_mobile_app_for_owner }}</span>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <div class="border rounded">
                          <div class="row">
                            <div class="col">
                              <label class="form-check-label  p-2 mt-2" for="show_taxi_rental_ride_feature">{{$t("show_taxi_rental_ride_feature")}}</label>
                            </div>
                            <div class="col">                  
                              <div class="form-check form-switch form-switch-md  float-end p-2" >
                                <input
                                  type="checkbox"
                                  :placeholder="'If you turned on this, it will show Rental Ride Feature'"
                                  :offPlaceholder="'Are You Sure? if You Disable This, it will not show Rental Ride Feature'"
                                  class="form-check-input"
                                  id="show_taxi_rental_ride_feature"
                                  v-model="form.show_taxi_rental_ride_feature"
                                  @change.prevent="confirmToggle('show_taxi_rental_ride_feature', form.show_taxi_rental_ride_feature, $event.target.placeholder, $event.target.getAttribute('offPlaceholder'))"
                                />                          
                              </div>                            
                            </div>
                          </div>
                        </div> 
                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error.show_taxi_rental_ride_feature }}</span>
                      </div>
                    </div>                        
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <div class="border rounded">
                          <div class="row">
                            <div class="col">
                              <label class="form-check-label  p-2 mt-2" for="show_delivery_rental_ride_feature">{{$t("show_delivery_rental_ride_feature")}}</label>
                            </div>
                            <div class="col">                  
                              <div class="form-check form-switch form-switch-md  float-end p-2" >
                                <input
                                  type="checkbox"
                                  :placeholder="'If you turned on this, it will show Rental Ride Feature'"
                                  :offPlaceholder="'Are You Sure? if You Disable This, it will not show Rental Ride Feature'"
                                  class="form-check-input"
                                  id="show_delivery_rental_ride_feature"
                                  v-model="form.show_delivery_rental_ride_feature"
                                  @change.prevent="confirmToggle('show_delivery_rental_ride_feature', form.show_delivery_rental_ride_feature, $event.target.placeholder, $event.target.getAttribute('offPlaceholder'))"
                                />                          
                              </div>                            
                            </div>
                          </div>
                        </div> 
                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error.show_delivery_rental_ride_feature }}</span>
                      </div>
                    </div> 
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <div class="border rounded">
                          <div class="row">
                            <div class="col">
                              <label class="form-check-label p-2 mt-2" for="show_ride_later_feature">{{$t("show_ride_later_feature")}}</label>
                            </div>
                            <div class="col">
                              <div class="form-check form-switch form-switch-md  float-end p-2" >
                                <input
                                  type="checkbox"
                                  :placeholder="'If you turned on this, it will Show Ride Later Feature'"
                                  :offPlaceholder="'Are You Sure? if You Disable This, it will not Show Ride Later Feature'"
                                  class="form-check-input"
                                  id="show_ride_later_feature"
                                  v-model="form.show_ride_later_feature"
                                  @change.prevent="confirmToggle('show_ride_later_feature', form.show_ride_later_feature, $event.target.placeholder, $event.target.getAttribute('offPlaceholder'))"
                                />   
                              </div>                            
                            </div>
                          </div>
                        </div>
                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error.show_ride_later_feature }}</span>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <div class="border rounded">
                          <div class="row">
                            <div class="col">
                              <label class="form-check-label  p-2 mt-2" for="show_email_otp_feature_on_mobile_app">{{$t("show_email_otp_feature_on_mobile_app")}}</label>
                            </div>
                            <div class="col">
                              <div class="form-check form-switch form-switch-md float-end p-2" >
                                <input
                                  type="checkbox"
                                  :placeholder="'If you turned on this, it will show Email OTP Feature in Application'"
                                  :offPlaceholder="'Are You Sure? if You Disable This, it will not show Email OTP Feature in Application'"
                                  class="form-check-input"
                                  id="show_email_otp_feature_on_mobile_app"
                                  v-model="form.show_email_otp_feature_on_mobile_app"
                                  @change.prevent="confirmToggle('show_email_otp_feature_on_mobile_app', form.show_email_otp_feature_on_mobile_app, $event.target.placeholder, $event.target.getAttribute('offPlaceholder'))"
                                />                             
                              </div>                            
                            </div>
                          </div>
                        </div>
                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error.show_email_otp_feature_on_mobile_app }}</span>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <div class="border rounded">
                          <div class="row">
                            <div class="col">
                              <label class="form-check-label p-2 mt-2" for="show_outstation_ride_feature">{{$t("show_outstation_ride_feature")}}</label>
                            </div>
                            <div class="col">
                              <div class="form-check form-switch form-switch-md  float-end p-2" >
                                <input
                                  type="checkbox"
                                  :placeholder="'If you turned on this, it will Show Ride Later Feature'"
                                  :offPlaceholder="'Are You Sure? if You Disable This, it will not Show Ride Later Feature'"
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
                    </div>
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <div class="border rounded">
                          <div class="row">
                            <div class="col">
                              <label class="form-check-label p-2 mt-2" for="show_delivery_outstation_ride_feature">{{$t("show_delivery_outstation_ride_feature")}}</label>
                            </div>
                            <div class="col">
                              <div class="form-check form-switch form-switch-md  float-end p-2" >
                                <input
                                  type="checkbox"
                                  :placeholder="'If you turned on this, it will Show Ride Later Feature'"
                                  :offPlaceholder="'Are You Sure? if You Disable This, it will not Show Ride Later Feature'"
                                  class="form-check-input"
                                  id="show_delivery_outstation_ride_feature"
                                  v-model="form.show_delivery_outstation_ride_feature"
                                  @change.prevent="confirmToggle('show_delivery_outstation_ride_feature', form.show_delivery_outstation_ride_feature, $event.target.placeholder, $event.target.getAttribute('offPlaceholder'))"
                                />   
                              </div>                            
                            </div>
                          </div>
                        </div>
                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error.show_delivery_outstation_ride_feature }}</span>
                      </div>
                    </div>      
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <div class="border rounded">
                          <div class="row">
                            <div class="col">
                              <label class="form-check-label p-2 mt-2" for="show_incentive_feature_for_driver">{{$t("show_incentive_feature_for_driver")}}</label>  
                            </div>
                            <div class="col">
                              <div class="form-check form-switch form-switch-md float-end p-2" >
                                <input
                                  type="checkbox"
                                  :placeholder="'If you turned on this, it will Show Incentives Feature'"
                                  :offPlaceholder="'Are You Sure? if You Disable This, it will not Show Incentives Feature'"
                                  class="form-check-input"
                                  id="show_incentive_feature_for_driver"
                                  v-model="form.show_incentive_feature_for_driver"
                                  @change.prevent="confirmToggle('show_incentive_feature_for_driver', form.show_incentive_feature_for_driver, $event.target.placeholder, $event.target.getAttribute('offPlaceholder'))"
                                />    
                              </div> 
                            </div>
                          </div>                
                        </div>
                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error.show_incentive_feature_for_driver }}</span>
                      </div>
                    </div> 
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <div class="border rounded">
                          <div class="row">
                            <div class="col">
                              <label class="form-check-label p-2 mt-2" for="show_driver_level_feature">{{$t("show_driver_level_feature")}}</label>
                            </div>
                            <div class="col">
                              <div class="form-check form-switch form-switch-md float-end p-2">
                                <input
                                  type="checkbox"
                                  :placeholder="'If Enable this Feature, Drivers can get option to Receive rides for specific routes'"
                                  :offPlaceholder="'Drivers can get option get rides from Selected Routes'"
                                  class="form-check-input"
                                  id="show_driver_level_feature"
                                  v-model="form.show_driver_level_feature"
                                  @change.prevent="confirmToggle('show_driver_level_feature', form.show_driver_level_feature, $event.target.placeholder, $event.target.getAttribute('offPlaceholder'))"
                                />                      
                              </div>                            
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>  
                    <div class="col-sm-6">
                      <div class="mb-3 mt-0">
                        <div class="border rounded">
                          <div class="row">
                            <div class="col">
                              <label class="form-check-label p-2 mt-2" for="enable_country_restrict_on_map">{{$t("enable_country_restrict_on_map")}}</label>
                            </div>
                            <div class="col">
                              <div class="form-check form-switch form-switch-md float-end p-2">
                                <input
                                  type="checkbox"
                                  :placeholder="'Users can only search for addresses, cities, and points of interest that are located within the restricted country'"
                                  :offPlaceholder="'Users can search for any addresses, cities, and points Accross the World'"
                                  class="form-check-input"
                                  id="enable_country_restrict_on_map"
                                  v-model="form.enable_country_restrict_on_map"
                                  @change.prevent="confirmToggle('enable_country_restrict_on_map', form.enable_country_restrict_on_map, $event.target.placeholder, $event.target.getAttribute('offPlaceholder'))"
                                />
                              </div>
                            </div>
                          </div>
                        </div>
                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error.enable_country_restrict_on_map }}</span>
                      </div>
                    </div> 
                    
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <div class="border rounded">
                          <div class="row">
                            <div class="col">
                              <label class="form-check-label p-2 mt-2" for="show_owner_module_feature_on_mobile_app">{{$t("show_owner_module_feature_on_mobile_app")}} </label>
                            </div>
                            <div class="col">
                              <div class="form-check form-switch form-switch-md float-end p-2" >
                                <input
                                  type="checkbox"
                                  :placeholder="'If you turned on this, it will display Owner Module In Driver Application'"
                                  :offPlaceholder="'Are You Sure? if You Disable This, it will not display Owner Module In Driver Application'"
                                  class="form-check-input"
                                  id="show_owner_module_feature_on_mobile_app"
                                  v-model="form.show_owner_module_feature_on_mobile_app"
                                  @change.prevent="confirmToggle('show_owner_module_feature_on_mobile_app', form.show_owner_module_feature_on_mobile_app, $event.target.placeholder, $event.target.getAttribute('offPlaceholder'))"
                                />   
                              </div>                            
                            </div>
                          </div>
                        </div>
                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error.show_owner_module_feature_on_mobile_app }}</span> 
                      </div>
                    </div>                  
                    <!-- <div class="col-sm-6">
                      <div class="mb-3">
                        <div class="border rounded">
                          <div class="row">
                            <div class="col">
                              <label class="form-check-label p-2 mt-2" for="show_bank_info_feature_on_mobile_app">{{$t("show_bank_info_feature_on_mobile_app")}}</label>
                            </div>
                            <div class="col">
                              <div class="form-check form-switch form-switch-md float-end p-2">
                                <input
                                  type="checkbox"
                                  :placeholder="'If you turned on this, it will show Bank Informations in Driver Application'"
                                  :offPlaceholder="'Are You Sure? if You Disable This, it will not show Bank Informations in Driver Application'"
                                  class="form-check-input"
                                  id="show_bank_info_feature_on_mobile_app"
                                  v-model="form.show_bank_info_feature_on_mobile_app"
                                  @change.prevent="confirmToggle('show_bank_info_feature_on_mobile_app', form.show_bank_info_feature_on_mobile_app, $event.target.placeholder, $event.target.getAttribute('offPlaceholder'))"
                                />   
                              </div>                            
                            </div>
                          </div>
                        </div> 
                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error.show_bank_info_feature_on_mobile_app }}</span> 
                      </div>
                    </div> -->
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <div class="border rounded">
                          <div class="row">
                            <div class="col">
                              <label class="form-check-label p-2 mt-2" for="show_ride_otp_feature">{{$t("show_ride_otp_feature")}}</label>
                            </div>
                            <div class="col">
                              <div class="form-check form-switch form-switch-md float-end p-2" >
                                <input
                                  type="checkbox"
                                  :placeholder="'If you turned on this, it will Ask Ride OTP For Taxi Trips'"
                                  :offPlaceholder="'Are You Sure? if You Disable This, it will not Ask Ride OTP For Taxi Trips'"
                                  class="form-check-input"
                                  id="show_ride_otp_feature"
                                  v-model="form.show_ride_otp_feature"
                                  @change.prevent="confirmToggle('show_ride_otp_feature', form.show_ride_otp_feature, $event.target.placeholder, $event.target.getAttribute('offPlaceholder'))"
                                />  
                              </div>                            
                            </div>
                          </div>
                        </div>
                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error.show_ride_otp_feature }}</span>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <div class="border rounded">
                          <div class="row">
                            <div class="col">
                              <label class="form-check-label p-2 mt-2" for="show_delivery_ride_pick_otp_feature">{{$t("show_delivery_ride_pick_otp_feature")}}</label>
                            </div>
                            <div class="col">
                              <div class="form-check form-switch form-switch-md float-end p-2" >
                                <input
                                  type="checkbox"
                                  :placeholder="'If you turned on this, it will Ask Ride OTP when Loading For Delivery Trips'"
                                  :offPlaceholder="'Are You Sure? if You Disable This, it will not Ask Ride OTP when Loading For Delivery Trips'"
                                  class="form-check-input"
                                  id="show_delivery_ride_pick_otp_feature"
                                  v-model="form.show_delivery_ride_pick_otp_feature"
                                  @change.prevent="confirmToggle('show_delivery_ride_pick_otp_feature', form.show_delivery_ride_pick_otp_feature, $event.target.placeholder, $event.target.getAttribute('offPlaceholder'))"
                                />  
                              </div>                            
                            </div>
                          </div>
                        </div>
                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error.show_delivery_ride_pick_otp_feature }}</span>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <div class="border rounded">
                          <div class="row">
                            <div class="col">
                              <label class="form-check-label p-2 mt-2" for="show_delivery_ride_drop_otp_feature">{{$t("show_delivery_ride_drop_otp_feature")}}</label>
                            </div>
                            <div class="col">
                              <div class="form-check form-switch form-switch-md float-end p-2" >
                                <input
                                  type="checkbox"
                                  :placeholder="'If you turned on this, it will Ask Ride OTP For Trips'"
                                  :offPlaceholder="'Are You Sure? if You Disable This, it will not Ask Ride OTP For delivery Trips'"
                                  class="form-check-input"
                                  id="show_delivery_ride_drop_otp_feature"
                                  v-model="form.show_delivery_ride_drop_otp_feature"
                                  @change.prevent="confirmToggle('show_delivery_ride_drop_otp_feature', form.show_delivery_ride_drop_otp_feature, $event.target.placeholder, $event.target.getAttribute('offPlaceholder'))"
                                />  
                              </div>                            
                            </div>
                          </div>
                        </div>
                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error.show_delivery_ride_drop_otp_feature }}</span>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <div class="border rounded">
                          <div class="row">
                            <div class="col">
                              <label class="form-check-label p-2 mt-2" for="show_ride_without_destination">{{$t("show_ride_without_destination")}}</label>  
                            </div>
                            <div class="col">
                              <div class="form-check form-switch form-switch-md float-end p-2" >
                                <input
                                  type="checkbox"
                                  :placeholder="'If you turned on this, it will Show Ride Without Destination Feature'"
                                  :offPlaceholder="'Are You Sure? if You Disable This, it will not Show Ride Without Destination Feature'"
                                  class="form-check-input"
                                  id="show_ride_without_destination"
                                  v-model="form.show_ride_without_destination"
                                  @change.prevent="confirmToggle('show_ride_without_destination', form.show_ride_without_destination, $event.target.placeholder, $event.target.getAttribute('offPlaceholder'))"
                                />    
                              </div> 
                            </div>
                          </div>                
                        </div>
                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error.show_ride_without_destination }}</span>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <div class="border rounded">
                          <div class="row">
                            <div class="col">
                              <label class="form-check-label p-2 mt-2" for="enable_web_booking_feature">{{$t("enable_web_booking_feature")}}</label>
                            </div>
                            <div class="col">
                              <div class="form-check form-switch form-switch-md float-end p-2">
                                <input
                                  type="checkbox"
                                  :placeholder="'If Enable this Feature, Drivers can get option to Receive rides for specific routes'"
                                  :offPlaceholder="'Drivers can get option get rides from Selected Routes'"
                                  class="form-check-input"
                                  id="enable_web_booking_feature"
                                  v-model="form.enable_web_booking_feature"
                                  @change.prevent="confirmToggle('enable_web_booking_feature', form.enable_web_booking_feature, $event.target.placeholder, $event.target.getAttribute('offPlaceholder'))"
                                />                      
                              </div>                            
                            </div>
                          </div>
                        </div>
                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <div class="border rounded">
                          <div class="row">
                            <div class="col">
                              <label class="form-check-label p-2 mt-2" for="enable_sub_vehicle_feature">{{$t("enable_sub_vehicle_feature")}}</label>
                            </div>
                            <div class="col">
                              <div class="form-check form-switch form-switch-md float-end p-2">
                                <input
                                  type="checkbox"
                                  :placeholder="'If Enable this Feature, Drivers can get option to Receive rides for specific routes'"
                                  :offPlaceholder="'Drivers can get option get rides from Selected Routes'"
                                  class="form-check-input"
                                  id="enable_sub_vehicle_feature"
                                  v-model="form.enable_sub_vehicle_feature"
                                  @change.prevent="confirmToggle('enable_sub_vehicle_feature', form.enable_sub_vehicle_feature, $event.target.placeholder, $event.target.getAttribute('offPlaceholder'))"
                                />                      
                              </div>                            
                            </div>
                          </div>
                        </div>
                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                      </div>
                    </div> 
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <div class="border rounded">
                          <div class="row">
                            <div class="col">
                              <label class="form-check-label p-2 mt-2" for="enable_landing_site">{{$t("enable_landing_site")}}</label>
                            </div>
                            <div class="col">
                              <div class="form-check form-switch form-switch-md float-end p-2">
                                <input
                                  type="checkbox"
                                  :placeholder="'If Enable this Feature, Drivers can get option to Receive rides for specific routes'"
                                  :offPlaceholder="'Drivers can get option get rides from Selected Routes'"
                                  class="form-check-input"
                                  id="enable_landing_site"
                                  v-model="form.enable_landing_site"
                                  @change.prevent="confirmToggle('enable_landing_site', form.enable_landing_site, $event.target.placeholder, $event.target.getAttribute('offPlaceholder'))"
                                />                      
                              </div>                            
                            </div>
                          </div>
                        </div>
                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                      </div>
                    </div> 
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <div class="border rounded">
                          <div class="row">
                            <div class="col">
                              <label class="form-check-label p-2 mt-2" for="enable_additional_charge_feature">{{$t("enable_additional_charge_feature")}}</label>
                            </div>
                            <div class="col">
                              <div class="form-check form-switch form-switch-md float-end p-2">
                                <input
                                  type="checkbox"
                                  :placeholder="'If Enable this Feature, Drivers can get option to Receive rides for specific routes'"
                                  :offPlaceholder="'Drivers can get option get rides from Selected Routes'"
                                  class="form-check-input"
                                  id="enable_additional_charge_feature"
                                  v-model="form.enable_additional_charge_feature"
                                  @change.prevent="confirmToggle('enable_additional_charge_feature', form.enable_additional_charge_feature, $event.target.placeholder, $event.target.getAttribute('offPlaceholder'))"
                                />                      
                              </div>                            
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <div class="border rounded">
                          <div class="row">
                            <div class="col">
                              <label class="form-check-label p-2 mt-2" for="enable_driver_profile_disapprove_on_update">{{$t("enable_driver_profile_disapprove_on_update")}}</label>
                            </div>
                            <div class="col">
                              <div class="form-check form-switch form-switch-md float-end p-2">
                                <input
                                  type="checkbox"
                                  :placeholder="'If Enable this Feature, Drivers can get option to Receive rides for specific routes'"
                                  :offPlaceholder="'Drivers can get option get rides from Selected Routes'"
                                  class="form-check-input"
                                  id="enable_driver_profile_disapprove_on_update"
                                  v-model="form.enable_driver_profile_disapprove_on_update"
                                  @change.prevent="confirmToggle('enable_driver_profile_disapprove_on_update', form.enable_driver_profile_disapprove_on_update, $event.target.placeholder, $event.target.getAttribute('offPlaceholder'))"
                                />                      
                              </div>                            
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <div class="border rounded">
                          <div class="row">
                            <div class="col">
                              <label class="form-check-label p-2 mt-2" for="enable_support_ticket_feature">{{$t("enable_support_ticket_feature")}}</label>
                            </div>
                            <div class="col">
                              <div class="form-check form-switch form-switch-md float-end p-2">
                                <input
                                  type="checkbox"
                                  :placeholder="'If Enable this Feature, Drivers can get option to Receive rides for specific routes'"
                                  :offPlaceholder="'Drivers can get option get rides from Selected Routes'"
                                  class="form-check-input"
                                  id="enable_support_ticket_feature"
                                  v-model="form.enable_support_ticket_feature"
                                  @change.prevent="confirmToggle('enable_support_ticket_feature', form.enable_support_ticket_feature, $event.target.placeholder, $event.target.getAttribute('offPlaceholder'))"
                                />                      
                              </div>                            
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <div class="border rounded">
                          <div class="row">
                            <div class="col">
                              <label class="form-check-label p-2 mt-2" for="enable_map_appearance_change_on_mobile_app">{{$t("enable_map_appearance_change_on_mobile_app")}}</label>
                            </div>
                            <div class="col">
                              <div class="form-check form-switch form-switch-md float-end p-2">
                                <input
                                  type="checkbox"
                                  :placeholder="'If Enable this Feature, Drivers can get option to Receive rides for specific routes'"
                                  :offPlaceholder="'Drivers can get option get rides from Selected Routes'"
                                  class="form-check-input"
                                  id="enable_map_appearance_change_on_mobile_app"
                                  v-model="form.enable_map_appearance_change_on_mobile_app"
                                  @change.prevent="confirmToggle('enable_map_appearance_change_on_mobile_app', form.enable_map_appearance_change_on_mobile_app, $event.target.placeholder, $event.target.getAttribute('offPlaceholder'))"
                                />                      
                              </div>                            
                            </div>
                          </div>
                        </div>
                      </div>
                    </div> 
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <div class="border rounded">
                          <div class="row">
                            <div class="col">
                              <label class="form-check-label p-2 mt-2" for="enable_driver_leaderboard_feature">{{$t("enable_driver_leaderboard_feature")}}</label>
                            </div>
                            <div class="col">
                              <div class="form-check form-switch form-switch-md float-end p-2">
                                <input
                                  type="checkbox"
                                  :placeholder="'If Enable this Feature, Drivers can get option to Receive ride Leaderboard Feature'"
                                  :offPlaceholder="'Drivers can get option to get rides leaderboard feature'"
                                  class="form-check-input"
                                  id="enable_driver_leaderboard_feature"
                                  v-model="form.enable_driver_leaderboard_feature"
                                  @change.prevent="confirmToggle('enable_driver_leaderboard_feature', form.enable_driver_leaderboard_feature, $event.target.placeholder, $event.target.getAttribute('offPlaceholder'))"
                                />                      
                              </div>                            
                            </div>
                          </div>
                        </div>
                      </div>
                    </div> 
                  </div>  
            </BCardBody>
            <BCardFooter>
              <h5>{{$t("transport-ride-settings")}}</h5>  
              <div class="row">
                <div class="col-sm-6 mt-4">
                  <div class="mb-0">
                    <div class="border rounded">
                      <div class="row">
                        <div class="col">
                          <label class="form-check-label p-2 mt-2" for="enable_shipment_load_feature">{{$t("enable_shipment_load_feature")}}</label>
                        </div>
                        <div class="col">
                          <div class="form-check form-switch form-switch-md float-end p-2">
                            <input
                              type="checkbox"
                              :placeholder="'Drivers will be required to take a photo for every shipment load to ensure proper verification'"
                              :offPlaceholder="'Drivers do not need to take a photo for every shipment load, streamlining the process'"
                              class="form-check-input"
                              id="enable_shipment_load_feature"
                              v-model="form.enable_shipment_load_feature"
                              @change.prevent="confirmToggle('enable_shipment_load_feature', form.enable_shipment_load_feature, $event.target.placeholder, $event.target.getAttribute('offPlaceholder'))"
                            />                         
                          </div>                            
                        </div>
                      </div>
                    </div>
                    <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                  </div>
                </div> 
                <div class="col-sm-6 mt-4">
                  <div class="mb-0">
                    <div class="border rounded">
                      <div class="row">
                        <div class="col">
                          <label class="form-check-label p-2 mt-2" for="enable_shipment_unload_feature">{{$t("enable_shipment_unload_feature")}}</label>
                        </div>
                        <div class="col">
                          <div class="form-check form-switch form-switch-md float-end p-2">
                            <input
                              type="checkbox"
                              :placeholder="'Drivers will be required to take a photo for every shipment unload to ensure proper verification'"
                              :offPlaceholder="'Drivers do not need to take a photo for every shipment unload, streamlining the process'"
                              class="form-check-input"
                              id="enable_shipment_unload_feature"
                              v-model="form.enable_shipment_unload_feature"
                              @change.prevent="confirmToggle('enable_shipment_unload_feature', form.enable_shipment_unload_feature, $event.target.placeholder, $event.target.getAttribute('offPlaceholder'))"
                            />                      
                          </div>                            
                        </div>
                      </div>
                    </div>
                    <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                  </div>
                </div> 
                <div class="col-sm-6 mt-4">
                  <div class="mb-0">
                    <div class="border rounded">
                      <div class="row">
                        <div class="col">
                          <label class="form-check-label p-2 mt-2" for="enable_digital_signature">{{$t("enable_digital_signature")}}</label>
                        </div>
                        <div class="col">
                          <div class="form-check form-switch form-switch-md float-end p-2">
                            <input
                              type="checkbox"
                              :placeholder="'Drivers will be required to get Signature for every shipment Delivery'"
                              :offPlaceholder="'Drivers do not need to get Signature for every shipment Delivery'"
                              class="form-check-input"
                              id="enable_digital_signature"
                              v-model="form.enable_digital_signature"
                              @change.prevent="confirmToggle('enable_digital_signature', form.enable_digital_signature, $event.target.placeholder, $event.target.getAttribute('offPlaceholder'))"
                            />                        
                          </div>                            
                        </div>
                      </div>
                    </div>
                    <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                  </div>
                </div>   
                <div class="col-sm-6 mt-4">
                  <div class="mb-0">
                    <div class="border rounded">
                      <div class="row">
                        <div class="col">
                          <label class="form-check-label p-2 mt-2" for="enable_pet_preference_for_user">{{$t("enable_pet_preference_for_user")}}</label>
                        </div>
                        <div class="col">
                          <div class="form-check form-switch form-switch-md float-end p-2">
                            <input
                              type="checkbox"
                              :placeholder="'Users can bring Their pets along for taxi rides, making travel more enjoyable and convenient for pet USers'"
                              :offPlaceholder="'Users will not be able to bring pets along for taxi rides, and alternative transportation options may need to be considered'"
                              class="form-check-input"
                              id="enable_pet_preference_for_user"
                              v-model="form.enable_pet_preference_for_user"
                              @change.prevent="confirmToggle('enable_pet_preference_for_user', form.enable_pet_preference_for_user, $event.target.placeholder, $event.target.getAttribute('offPlaceholder'))"
                            />                    
                          </div>                            
                        </div>
                      </div>
                    </div>
                    <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                  </div>
                </div>
                <div class="col-sm-6 mt-4">
                  <div class="mb-0">
                    <div class="border rounded">
                      <div class="row">
                        <div class="col">
                          <label class="form-check-label p-2 mt-2" for="enable_luggage_preference_for_user">{{$t("enable_luggage_preference_for_user")}}</label>
                        </div>
                        <div class="col">
                          <div class="form-check form-switch form-switch-md float-end p-2">
                            <input
                              type="checkbox"
                              :placeholder="'Luggage preference is enabled. You can specify your luggage needs for taxi rides, ensuring your belongings are comfortably accommodated'"
                              :offPlaceholder="'Luggage preference is disabled. Standard luggage capacity will apply, and you may need to consider alternative transportation options if traveling with larger items'"
                              class="form-check-input"
                              id="enable_luggage_preference_for_user"
                              v-model="form.enable_luggage_preference_for_user"
                              @change.prevent="confirmToggle('enable_luggage_preference_for_user', form.enable_luggage_preference_for_user, $event.target.placeholder, $event.target.getAttribute('offPlaceholder'))"
                            />                      
                          </div>                            
                        </div>
                      </div>
                    </div>
                    <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                  </div>
                </div> 
                <div class="col-sm-6 mt-4">
                  <div class="mb-0">
                    <div class="border rounded">
                      <div class="row">
                        <div class="col">
                          <label class="form-check-label p-2 mt-2" for="enable_eta_total_update">{{$t("enable_eta_total_update")}}</label>
                        </div>
                        <div class="col">
                          <div class="form-check form-switch form-switch-md float-end p-2">
                            <input
                              type="checkbox"
                              class="form-check-input"
                              id="enable_eta_total_update"
                              v-model="form.enable_eta_total_update"
                              @change.prevent="confirmToggle('enable_eta_total_update', form.enable_eta_total_update, $event.target.placeholder, $event.target.getAttribute('offPlaceholder'))"
                            />                      
                          </div>                            
                        </div>
                      </div>
                    </div>
                    <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                  </div>
                </div> 
                <div class="col-lg-12">
                  <div class="text-end">
                    <button type="submit" class="btn btn-primary"> {{ settings ? $t('update') : $t('save') }}</button>
                  </div>
                </div>
              </div>          
            </BCardFooter>          
            </FormValidation>
          </form>
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
