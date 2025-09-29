<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import { ref, watch, onMounted, computed } from "vue";
import axios from "axios";

import Multiselect from "@vueform/multiselect";
import '@vueform/multiselect/themes/default.css';
import FormValidation from "@/Components/FormValidation.vue";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import searchbar from "@/Components/widgets/searchbar.vue"; 
import { debounce } from 'lodash';
import { useI18n } from 'vue-i18n';

export default {
  data() {
    return {
      subscription_hint: false,
    }
  },
  components: {
    Layout,
    PageHeader,
    Head,
    Multiselect,
    searchbar,
    FormValidation,
    flatPickr
  },
  props: {
    successMessage: String,
    alertMessage: String,
    plan: Object,
    types: Object,
  },
  setup(props) {
    const { t } = useI18n();

    // Helper function to convert timestamp to 'YYYY-MM-DD' format
    const convertTimestampToDate = (timestamp) => {
      if (!timestamp) return '';
      const date = new Date(timestamp * 1000); // Multiply by 1000 if timestamp is in seconds
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, '0'); // Add leading zero
      const day = String(date.getDate()).padStart(2, '0'); // Add leading zero
      return `${year}-${month}-${day}`;
    };

    const form = useForm({
        vehicle_type_id: props.plan ? props.plan.vehicle_type_id : '',
        transport_type: props.plan ? props.plan.transport_type : '',
        name: props.plan ? props.plan.name : '',
        subscription_duration: props.plan ? props.plan.subscription_duration : '',
        amount: props.plan ? props.plan.amount : '',
        description: props.plan ? props.plan.description : '',
      });

    const validationRules = {
      vehicle_type_id: { required: true },
      name: { required: true },
      subscription_duration: { required: true },
      amount: { required: true },
      description: { required: true },
    };

    const validationRef = ref(null);
    const errors = ref({});
    const successMessage = ref(props.successMessage || '');
    const alertMessage = ref(props.alertMessage || '');

    const dismissMessage = () => {
      successMessage.value = "";
      alertMessage.value = "";
    };

    const types = ref(props.types);
    const type_options = computed (() => types.value?.filter(type => type.is_taxi === form.transport_type));


    const handleSubmit = async () => {
      errors.value = validationRef.value.validate();
      if (Object.keys(errors.value).length > 0) {
        return;
      }

      try {
        let response;
        if (props.plan) {
          response = await axios.post(`/subscription/update/${props.plan.id}`, form.data());
        } else {
          response = await axios.post('/subscription/store', form.data());
        }
        if (response.status === 201 || response.status === 200) {
          successMessage.value = t('subscription_saved_successfully');
          form.reset();
          router.get('/subscription');
        } else {
          alertMessage.value = t('failed_to_save_subscription');
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          errors.value = error.response.data.errors;
        } else {
          console.error(t('error_saving_subscription'), error);
          alertMessage.value = t('failed_to_save_subscription');
        }
      }
    };

    onMounted(() => {
    });



    return {
      form,
      successMessage,
      alertMessage,
      handleSubmit,
      dismissMessage,
      validationRules,
      validationRef,
      type_options,
      errors,
    };
  }
};
</script>

<template>
  <Layout>
    <Head title="Subscription" />
    <PageHeader :title="plan ? $t('edit') : $t('create')" :pageTitle="$t('subscription')"  pageLink="/subscription"/>
    <BRow>
      <BCol lg="12">
        <BCard no-body id="tasksList">
          <BCardHeader class="border-0">
            <BLink @click="subscription_hint = !subscription_hint">
              <h6 class="text-success text-decoration-underline text-decoration-underline-success float-end">{{$t('how_it_works')}}</h6>
            </BLink>
          </BCardHeader>
          <BCardBody class="border border-dashed border-end-0 border-start-0">
            <form @submit.prevent="handleSubmit">
              <FormValidation :form="form" :rules="validationRules" ref="validationRef">
                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="select_transport_type" class="form-label">{{ $t("transport_type") }}</label>
                      <select id="transport_type" class="form-select" v-model="form.transport_type">
                        <option disabled value="">{{ $t("select") }}</option>
                        <option value="taxi">{{ $t("taxi") }}</option>
                        <option value="delivery">{{ $t("delivery") }}</option>
                        <option value="both">{{ $t("all") }}</option>
                      </select>
                      <span v-for="(error, index) in errors.transport_type" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="select_types" class="form-label">{{ $t("vehicle_type") }}</label>
                      <Multiselect
                        id="select_vehicle_type"
                        v-model="form.vehicle_type_id"
                        close-on-select
                        :searchable="true"
                        :create-option="false"
                        :options="type_options?.map(loc => ({ value: loc.id, label: loc.name }))"
                        :placeholder="$t('select_vehicle_type')"
                      />
                      <span v-for="(error, index) in errors.user_ids" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="name" class="form-label">{{ $t("name") }}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" class="form-control" :placeholder="$t('enter_name')" id="name" v-model="form.name" />
                      <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="subscription_duration" class="form-label">{{ $t("subscription_duration") }}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="number" class="form-control" :placeholder=" $t('enter_subscription_duration')" id="subscription_duration" v-model="form.subscription_duration" />
                      <span v-for="(error, index) in errors.subscription_duration" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="amount" class="form-label">{{ $t("amount") }}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="number" step="any" class="form-control" :placeholder="$t('enter_amount')" id="amount" v-model="form.amount" />                   
                      <span v-for="(error, index) in errors.amount" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="description" class="form-label">{{ $t("description") }}
                        <span class="text-danger">*</span>
                      </label>
                      <textarea type="text" class="form-control" :placeholder="$t('enter_description')" id="description" v-model="form.description" />                   
                      <span v-for="(error, index) in errors.description" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-lg-12">
                    <div class="text-end">
                      <button type="submit" class="btn btn-primary">{{ plan ? $t('update') : $t('save') }}</button>
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
      <div v-if="successMessage" class="custom-alert alert alert-success alert-border-left fade show" role="alert">
        <div class="alert-content">
          <i class="ri-notification-off-line me-3 align-middle"></i>
          <strong>Success</strong> - {{ successMessage }}
          <button type="button" class="btn-close btn-close-success" @click="dismissMessage" aria-label="Close Success Message"></button>
        </div>
      </div>
      <div v-if="alertMessage" class="custom-alert alert alert-danger alert-border-left fade show" role="alert">
        <div class="alert-content">
          <i class="ri-notification-off-line me-3 align-middle"></i>
          <strong>Alert</strong> - {{ alertMessage }}
          <button type="button" class="btn-close btn-close-danger" @click="dismissMessage" aria-label="Close Alert Message"></button>
        </div>
      </div>
    </div>

    <!-- modal -->
    <BModal v-model="subscription_hint" hide-footer :title="$t('subscription')" class="v-modal-custom" size="lg">
      <p class="text-muted mb-0">How Coupon Works:</p>
      <div class="d-flex mt-4">       
        <div class="flex-shrink-0">
            <i class="ri-checkbox-circle-fill text-success"></i>
        </div>
        <div class="flex-grow-1 ms-2">
            <p class="text-muted mb-0">
              The coupon name will work based on its settings, such as the date range, 
              how many times each user can use it, the total number of uses, 
              maximum discount, and minimum trip amount.
            </p>
        </div>
      </div>
      <div class="d-flex mt-4">       
        <div class="flex-shrink-0">
            <i class="ri-checkbox-circle-fill text-success"></i>
        </div>
        <div class="flex-grow-1 ms-2">
            <p class="text-muted mb-0">
              The driver will still receive the full payment, so they won't be
              affected by rides where a coupon is applied.
            </p>
        </div>
      </div>
      <div class="swiper-pagination dynamic-pagination"></div>
          <div class="modal-footer v-modal-footer">
            <BLink href="javascript:void(0);" class="btn btn-link link-success fw-medium"
                @click="subscription_hint = false">
                <i class="ri-close-line me-1 align-middle"></i> Close
            </BLink>
            <!-- <BButton type="button" variant="primary">Save Changes</BButton> -->
        </div>
    </BModal>
<!-- modal end -->
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
