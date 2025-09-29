<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import { ref, computed } from "vue";
import axios from "axios";
import imageUpload from "@/Components/widgets/imageUpload.vue";

import Multiselect from "@vueform/multiselect";
import FormValidation from "@/Components/FormValidation.vue";
import { useI18n } from 'vue-i18n';
import Swal from "sweetalert2";
import { BCardHeader } from 'bootstrap-vue-next';

export default {
  components: {
    Layout,
    PageHeader,
    Head,
    Pagination,
    Multiselect,
    FormValidation,
    imageUpload,
  },
  props: {
    successMessage: String,
    alertMessage: String,
    app_for: String,
    minimum_tip_add: Object,
    validate: Function,
    settings: Object,
  },
  setup(props) {
    const { t } = useI18n();
    const form = useForm({
      minimum_tip_amount: props.minimum_tip_add?.value ?? "",
      enable_driver_tips_feature: props.settings.enable_driver_tips_feature  ===  '1',
    });

    const validationRef = ref(null);
    const errors = ref({});
    const successMessage = ref(props.successMessage || '');
    const alertMessage = ref(props.alertMessage || '');

    const dismissMessage = () => {
      successMessage.value = "";
      alertMessage.value = "";
    };

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
          await axios.post('/tip-settings/update-status', { id: field, status: value ? 1 : 0 });
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
        response = await axios.post(`/tip-settings/update`, form.data());
        if (response.status === 201) {
          successMessage.value = t('tip_settings_updated_successfully');
          form.reset();
          router.get('/tip-settings');
        } else {
          alertMessage.value = t('failed_to_update_tip_settings');
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          errors.value = error.response.data.errors;
        } else {
          console.error(t('error_updating_tip_settings'), error);
          alertMessage.value = t('failed_to_update_tip_settings');
        }
      }
    };



    const calculatedValue1 = computed(() => {
      const amount = Number(form.minimum_tip_amount) || 0;
      // const multiplier = Number(form.minimum_tip_amount_suggestion_multiply * 0) || 0;
      return (amount);
    });

    const calculatedValue2 = computed(() => {
      const amount = Number(form.minimum_tip_amount) || 0;
      // const multiplier = Number(form.minimum_tip_amount_suggestion_multiply) || 0;
      return ((amount ) * 2);
    });

    const calculatedValue3 = computed(() => {
      const amount = Number(form.minimum_tip_amount) || 0;
      // const multiplier = Number(form.minimum_tip_amount_suggestion_multiply) || 0;
      return ((amount) * 3);
    });


    return {
      form,
      successMessage,
      alertMessage,
      handleSubmit,
      dismissMessage,
      validationRef,
      errors,
      calculatedValue1,
      calculatedValue2,
      calculatedValue3,
      confirmToggle
    };
  }
};
</script>

<template>
  <Layout>
    <Head title="Tip Settings" />
    <PageHeader :title="$t('tip-settings')" :pageTitle="$t('tip-settings')" />
    <BRow>
      <BCard class="p-0">
      <BCardBody class="p-0">
        <div class="col-sm-12">
            <div class="mb-3">
              <div class="border rounded">
                <div class="row">
                  <div class="col">
                    <label class="form-check-label p-2 mt-2" for="enable_driver_tips_feature">{{$t("enable_driver_tips_feature")}}</label>
                  </div>
                  <div class="col">
                    <div class="form-check form-switch form-switch-md float-end p-2">
                      <input
                        type="checkbox"
                        :placeholder="'If Enable this Feature, Drivers can get option to Receive rides for specific routes'"
                        :offPlaceholder="'Drivers can get option get rides from Selected Routes'"
                        class="form-check-input"
                        id="enable_driver_tips_feature"
                        v-model="form.enable_driver_tips_feature"
                        @change.prevent="confirmToggle('enable_driver_tips_feature', form.enable_driver_tips_feature, $event.target.placeholder, $event.target.getAttribute('offPlaceholder'))"
                      />                      
                    </div>                            
                  </div>
                </div>
              </div>
            </div>
          </div> 
      </BCardBody>
    </BCard>
    </BRow>
    <BRow v-if="form.enable_driver_tips_feature == 1">
        <BCard v-if="app_for === 'demo'" no-body id="tasksList">
          <BCardHeader class="border-0">
            <div class="alert bg-warning border-warning fs-18" role="alert">
              <strong> {{$t('note')}} : <em> {{$t('actions_restricted_due_to_demo_mode')}}</em> </strong>
          </div>
        </BCardHeader>
      </BCard>
      <BCol lg="6">
        <BCard no-body id="tasksList">
          <BCardHeader class="border-0"></BCardHeader>
          <BCardBody class="border border-dashed border-end-0 border-start-0">
            <form @submit.prevent="handleSubmit">
              
              <FormValidation :form="form" ref="validationRef">
                <div class="row"> 
                  <div class="col-sm-12">
                    <div class="mb-3">
                      <label for="minimum_tip_amount" class="form-label">{{$t("minimum_tip_amount")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" class="form-control" :placeholder="$t('enter_minimum_tip_amount')" id="minimum_tip_amount" 
                      v-model="form.minimum_tip_amount" :readonly="app_for === 'demo'"/>
                      <span v-for="(error, index) in errors.minimum_tip_amount" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                                          
                  <div class="col-lg-12">
                    <div class="text-end">
                      <button type="submit" class="btn btn-primary"> {{$t("save")}}</button>
                    </div>
                  </div>
                </div>  
              </FormValidation>
            </form>
          </BCardBody>
        </BCard>
      </BCol>
      <BCol lg="6">
        <BCard no-body id="tasksList">
          <BCardHeader class="border-0"><h5>{{$t("mobile_view")}}</h5></BCardHeader>
          <BCardBody class="border border-dashed border-end-0 border-start-0">
            <div class="col-sm-12">
                    <div class="mb-3" style="display: grid;place-items:center;">
                      <!-- <label for="owner_wallet_minimum_amount_to_get_an_order" class="form-label">Mobile View</label> -->
                      
                      <div class="wallet"><div class="overlap">
                        <div class="card cards">
                          <h6 class="text-center">{{$t("trip_fare")}} $550</h6>
                          <h6 class="text-center">{{$t("add_tip_message")}}</h6>
                          <div class="input-group mt-0">
                              <span class="input-group-text" id="basic-addon1">$</span>
                              <!-- <input disabled type="text" class="form-control" id="minimum_amount_added_to_wallet" placeholder="Enter Amount" aria-label="Username" aria-describedby="basic-addon1"
                              v-model="form.minimum_amount_added_to_wallet"> -->
                              <input disabled type="text" class="form-control" id="calculated_value" v-model="calculatedValue1" >
                          </div>
                          <div class="d-flex align-items-center justify-content-center gap-2 mt-2">
                          <div class="avatar-md p-1 py-2 h-auto bg border border-dashed rounded-3">
                              <div class="text-center"><h6 class="mb-0">${{ calculatedValue1 }}</h6></div>
                          </div>
                          <div class="avatar-md p-1 py-2 h-auto bg border border-dashed rounded-3">
                              <div class="text-center"><h6 class="mb-0">${{ calculatedValue2 }}</h6></div>
                          </div>
                          <div class="avatar-md p-1 py-2 h-auto bg border border-dashed rounded-3">
                              <div class="text-center"><h6 class="mb-0">${{ calculatedValue3 }}</h6></div>
                          </div>
                        </div>
                        <div class="hstack gap-2 justify-content-center mt-2">
                            <button type="button" class="btn btn-light" style="border:1px solid #16ad70">{{$t("cancel")}}</button>
                            <button type="button" class="btn" style="background-color: #16ad70;color:white">{{$t("add_tip")}}</button>
                        </div>
                      </div>
                    </div></div>
                    </div>
                  </div>  
          </BCardBody>
          </BCard>
          </BCol>
    </BRow>
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
.wallet{
  width: 300px;
  height: 550px;
  background-image: url(/images/tripSummary.jpg);
  background-position: center;
  background-repeat: no-repeat;
  background-size: contain;
}
.rtl .wallet{
  width: 300px;
  height: 550px;
  background-image: url(/images/tripSummary.jpg);
  background-position: center;
  background-repeat: no-repeat;
  background-size: contain;
}
.overlap{
  position: relative;
  left: 10px;
  padding: 0 15px;
  width: 280px;
  height: 550px;
  background: #6464646b;
  /* opacity: 1; */
}

.rtl .overlap{
  position: relative;
  right: 10px;
  padding: 0 15px;
  width: 280px;
  height: 550px;
  background: #6464646b;
  /* opacity: 1; */
}
.cards{
  position: relative;
    top: 177px;
    left: 7px;
    z-index: 2;
    width: 236px;
    height: 210px;
    padding:20px;
    border-radius: 15px 15px 15px 15px ;
}

.rtl .cards{
  position: relative;
    top: 177px;
    left: 7px;
    z-index: 2;
    width: 236px;
    height: 210px;
    padding:20px;
    border-radius: 15px 15px 15px 15px ;
}
</style>
