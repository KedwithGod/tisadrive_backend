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
    driver_wallet: Object,
    app_for: String,
    minimum_wallet: Object,
    owner_wallet: Object,
    minimum_wallet_add: Object,
    validate: Function,
  },
  setup(props) {
    const { t } = useI18n();
    const form = useForm({
      minimum_wallet_amount_for_transfer: props.minimum_wallet?.value ?? "",
      driver_wallet_minimum_amount_to_get_an_order: props.driver_wallet?.value ?? "",
      owner_wallet_minimum_amount_to_get_an_order: props.owner_wallet?.value ?? "",
      minimum_amount_added_to_wallet: props.minimum_wallet_add?.value ?? "",
    });

    const validationRef = ref(null);
    const errors = ref({});
    const successMessage = ref(props.successMessage || '');
    const alertMessage = ref(props.alertMessage || '');

    const dismissMessage = () => {
      successMessage.value = "";
      alertMessage.value = "";
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
        response = await axios.post(`/wallet-settings/update`, form.data());
        if (response.status === 201) {
          successMessage.value = t('wallet_settings_updated_successfully');
          form.reset();
          router.get('/wallet-settings');
        } else {
          alertMessage.value = t('failed_to_update_wallet_settings');
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          errors.value = error.response.data.errors;
        } else {
          console.error(t('error_updating_wallet_settings'), error);
          alertMessage.value = t('failed_to_update_wallet_settings');
        }
      }
    };



    const calculatedValue1 = computed(() => {
      const amount = Number(form.minimum_amount_added_to_wallet) || 0;
      // const multiplier = Number(form.minimum_amount_added_to_wallet_suggestion_multiply) || 0;
      return (amount);
    });

    const calculatedValue2 = computed(() => {
      const amount = Number(form.minimum_amount_added_to_wallet) || 0;
      // const multiplier = Number(form.minimum_amount_added_to_wallet_suggestion_multiply) || 0;
      return ((amount) * 2);
    });

    const calculatedValue3 = computed(() => {
      const amount = Number(form.minimum_amount_added_to_wallet) || 0;
      // const multiplier = Number(form.minimum_amount_added_to_wallet_suggestion_multiply) || 0;
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
    };
  }
};
</script>

<template>
  <Layout>
    <Head title="Wallet Settings" />
    <PageHeader :title="$t('wallet-settings')" :pageTitle="$t('wallet-settings')" />
    <BRow>
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
                      <label for="minimum_wallet_amount_for_transfer" class="form-label">{{$t("minimum_wallet_amount_for_transfer")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" class="form-control" :placeholder="$t('enter_minimum_wallet_amount_for_transfer')" id="minimum_wallet_amount_for_transfer" 
                      v-model="form.minimum_wallet_amount_for_transfer" :readonly="app_for === 'demo'"/>
                      <span v-for="(error, index) in errors.minimum_wallet_amount_for_transfer" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-12">
                    <div class="mb-3">
                      <label for="driver_wallet_minimum_amount_to_get_an_order" class="form-label">{{$t("driver_wallet_minimum_amount_to_get_an_order")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" class="form-control" :placeholder="$t('enter_driver_wallet_minimum_amount_to_get_an_order')" id="driver_wallet_minimum_amount_to_get_an_order" 
                      v-model="form.driver_wallet_minimum_amount_to_get_an_order" :readonly="app_for === 'demo'"/>
                      <span v-for="(error, index) in errors.driver_wallet_minimum_amount_to_get_an_order" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-12">
                    <div class="mb-3">
                      <label for="owner_wallet_minimum_amount_to_get_an_order" class="form-label">{{$t("owner_wallet_minimum_amount_to_get_an_order")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" class="form-control" :placeholder="$t('enter_owner_wallet_minimum_amount_to_get_an_order')" id="owner_wallet_minimum_amount_to_get_an_order" 
                      v-model="form.owner_wallet_minimum_amount_to_get_an_order" :readonly="app_for === 'demo'"/>
                      <span v-for="(error, index) in errors.owner_wallet_minimum_amount_to_get_an_order" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>  
                  <div class="col-sm-12">
                    <div class="mb-3">
                      <label for="minimum_amount_added_to_wallet" class="form-label">{{$t("minimum_amount_added_to_wallet")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" class="form-control" :placeholder="$t('enter_minimum_amount_added_to_wallet')" id="minimum_amount_added_to_wallet" 
                      v-model="form.minimum_amount_added_to_wallet" :readonly="app_for === 'demo'"/>
                      <span v-for="(error, index) in errors.minimum_amount_added_to_wallet" :key="index" class="text-danger">{{ error }}</span>
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
                          <!-- <input type="text" class="form-control" placeholder="" id="minimum_wallet_amount_for_transfer" 
                          v-model="form.minimum_wallet_amount_for_transfer" /> -->
                          <div class="input-group mt-3">
                              <span class="input-group-text" id="basic-addon1">$</span>
                              <!-- <input disabled type="text" class="form-control" id="minimum_amount_added_to_wallet" placeholder="Enter Amount" aria-label="Username" aria-describedby="basic-addon1"
                              v-model="form.minimum_amount_added_to_wallet"> -->
                              <input disabled type="text" class="form-control" id="calculated_value" v-model="calculatedValue1" >
                          </div>
                          <div class="d-flex align-items-center justify-content-center gap-2 mt-3">
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
                        <div class="hstack gap-2 justify-content-center mt-4">
                            <button type="button" class="btn btn-light" style="border:1px solid #16ad70">{{$t("cancel")}}</button>
                            <button type="button" class="btn" style="background-color: #16ad70;color:white">{{$t("add_money")}}</button>
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
  background-image: url(/images/wallet-settings.jpeg);
  background-position: center;
  background-repeat: no-repeat;
  background-size: contain;
}
.rtl .wallet{
  width: 300px;
  height: 550px;
  background-image: url(/images/wallet-settings.jpeg);
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
    top: 316px;
    left: 0px;
    z-index: 2;
    width: 250px;
    height: 200px;
    padding:10px;
    border-radius: 15px 15px 0 0 ;
}

.rtl .cards{
  position: relative;
    top: 316px;
    left: 0px;
    z-index: 2;
    width: 250px;
    height: 200px;
    padding:10px;
    border-radius: 15px 15px 0 0 ;
}
</style>
