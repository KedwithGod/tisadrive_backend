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
      promocode: false,
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
    serviceLocations: Array,
    promo: Object,
    users: Object,
    service_location_ids: Array,
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
      service_location_id: props.promo ? props.promo.service_location_id : '',
      transport_type: props.promo ? props.promo.transport_type : '',
      code: props.promo ? props.promo.code : '',
      minimum_trip_amount: props.promo ? props.promo.minimum_trip_amount : '',
      maximum_discount_amount: props.promo ? props.promo.maximum_discount_amount : '',
      discount_percent: props.promo ? props.promo.discount_percent : '',
      uses_per_user: props.promo ? props.promo.uses_per_user : '',
      user_id: props.promo ? props.promo.user_id : [],
      user_specific: props.promo ? props.promo.user_id?.length > 0 : false,
      date: props.promo
        ? [
            convertTimestampToDate(props.promo.from), // Convert from timestamp
            convertTimestampToDate(props.promo.to),   // Convert to timestamp
          ]
        : [],
        
      });

    // Flatpickr configuration
    const rangeDateconfig = ref({
      mode: 'range',
      dateFormat: 'Y-m-d', // Define the date format as 'YYYY-MM-DD'
    });


    const userSearch = ref('');
    const users = ref(props.users ? props.users : []);

    const fetchUsers = async() =>{
      try {
        let response = await axios.get('/promo-code/userList?search='+userSearch.value);
        users.value = response.data.results;
      }catch (errors){
        console.log('Errors'+errors);
      }
    }
    
    watch(userSearch, debounce(function () {
      if(userSearch.value.length > 0){
        fetchUsers();
      }else{
        users.value = [];
      }
    }, 300));

    const validationRules = {
      service_location_id: { required: true },
      transport_type: { required: true },
      code: { required: true },
      minimum_trip_amount: { required: true },
      maximum_discount_amount: { required: true },
      discount_percent: { required: true },
      uses_per_user: { required: true },
      user_specific: { required: false },
      user_id: { required: form.user_specific },
      date: { required: true },
    };

    const validationRef = ref(null);
    const errors = ref({});
    const successMessage = ref(props.successMessage || '');
    const alertMessage = ref(props.alertMessage || '');

    const dismissMessage = () => {
      successMessage.value = "";
      alertMessage.value = "";
    };


    const inputsearch = (value) => {
      userSearch.value = value;
    }

    const handleSubmit = async () => {
      errors.value = validationRef.value.validate();
      if (Object.keys(errors.value).length > 0) {
        return;
      }

      try {
        let response;
        if (props.promo) {
          response = await axios.post(`/promo-code/update/${props.promo.id}`, form.data());
        } else {
          response = await axios.post('store', form.data());
        }
        if (response.status === 201 || response.status === 200) {
          successMessage.value = t('promo_code_saved_successfully');
          form.reset();
          router.get('/promo-code');
        } else {
          alertMessage.value = t('failed_to_save_promo_code');
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          errors.value = error.response.data.errors;
        } else {
          console.error(t('error_saving_promo_code'), error);
          alertMessage.value = t('failed_to_save_promo_code');
        }
      }
    };

    const formattedDate = computed(() => {
    if (form.date && form.date.length === 2) {
        const fromDate = new Date(form.date[0]);
        const toDate = new Date(form.date[1]);
        const fromMonth = fromDate.toLocaleString('default', { month: 'short' });
        const toMonth = toDate.toLocaleString('default', { month: 'short' });
        return `${fromDate.getDate()} ${fromMonth} - ${toDate.getDate()} ${toMonth}`;
      }
      return '';
    });

    onMounted(() => {
      if (props.promo) {
        form.date = [props.promo.from, props.promo.to];
      }
    });



    return {
      form,
      successMessage,
      users,
      inputsearch,
      alertMessage,
      handleSubmit,
      dismissMessage,
      validationRules,
      validationRef,
      serviceLocations: props.serviceLocations,
      errors,
      rangeDateconfig,
    };
  }
};
</script>

<template>
  <Layout>
    <Head title="Promo Code" />
    <PageHeader :title="promo ? $t('edit') : $t('create')":pageTitle="$t('promo_code')" pageLink="/promo-code"/>
    <BRow>
      <BCol lg="12">
        <BCard no-body id="tasksList">
          <BCardHeader class="border-0">
            <BLink @click="promocode = !promocode">
              <h6 class="text-success text-decoration-underline text-decoration-underline-success float-end">{{$t('how_it_works')}}</h6>
            </BLink>
          </BCardHeader>
          <BCardBody class="border border-dashed border-end-0 border-start-0">
            <form @submit.prevent="handleSubmit">
              <FormValidation :form="form" :rules="validationRules" ref="validationRef">
                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="select_service_locations" class="form-label">{{ $t("service_locations") }}
                        <span class="text-danger">*</span>
                      </label>
                      <Multiselect
                        id="select_service_locations"
                        v-model="form.service_location_id"
                        close-on-select
                        :searchable="true"
                        :create-option="false"
                        :options="serviceLocations.map(loc => ({ value: loc.id, label: loc.name }))"
                        :placeholder="$t('select_service_locations')"
                      />
                      <span v-for="(error, index) in errors.service_location_id" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="select_users" class="form-label">{{ $t("users") }}
                        <span class="text-danger">*</span>
                      </label>
                      <Multiselect
                        id="select_users"
                        mode="tags"
                        v-model="form.user_id"
                        :close-on-select="false"
                        :searchable="true"
                        :create-option="false"
                        @search-change="inputsearch"
                        :options="users.map(user => ({ value: user.id, label: user.name }))"
                        :placeholder="$t('select_users')"
                      />
                      <span v-for="(error, index) in errors.user_ids" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-md-6 d-flex">
                    <div class="form-check" style="align-self: center;">
                      <label for="select_all_user">{{ $t("user_specific") }}</label>
                      <input type="checkbox" id="select_all_user" v-model="form.user_specific" 
                      class="form-check-input" :checked="form.user_id? form.user_id.length > 0 : false">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="select_transport_type" class="form-label">{{ $t("transport_type") }}
                        <span class="text-danger">*</span>
                      </label>
                      <select id="transport_type" class="form-select" v-model="form.transport_type">
                        <option disabled value="">{{ $t("select") }}</option>
                        <option value="taxi">{{ $t("taxi") }}</option>
                        <option value="delivery">{{ $t("delivery") }}</option>
                        <option value="both">{{ $t("all") }}</option>
                      </select>
                      <span v-for="(error, index) in errors.transport_type" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="code" class="form-label">{{ $t("code") }}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" class="form-control" :placeholder="$t('enter_code')" id="code" v-model="form.code" />
                      <span v-for="(error, index) in errors.code" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="minimum_trip_amount" class="form-label">{{ $t("minimum_trip_amount") }}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" class="form-control" :placeholder=" $t('enter_minimum_trip_amount')" id="minimum_trip_amount" v-model="form.minimum_trip_amount" />
                      <span v-for="(error, index) in errors.minimum_trip_amount" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="maximum_discount_amount" class="form-label">{{ $t("maximum_discount_amount") }}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" class="form-control" :placeholder="$t('enter_maximum_discount_amount')" id="maximum_discount_amount" v-model="form.maximum_discount_amount" />                   
                      <span v-for="(error, index) in errors.maximum_discount_amount" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="discount_percent" class="form-label">{{ $t("discount_percentage") }}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" class="form-control" :placeholder="$t('enter_discount_percentage')" id="discount_percent" v-model="form.discount_percent" />                   
                      <span v-for="(error, index) in errors.discount_percent" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-4">
                      <label for="datepicker-range" class="form-label">{{ $t("from_to_date") }}
                        <span class="text-danger">*</span>
                      </label>
                      <flat-pickr :placeholder="$t('select_date')" v-model="form.date" :config="rangeDateconfig" class="form-control flatpickr-input" id="demo-datepicker"></flat-pickr>
                      <span v-for="(error, index) in errors.date" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="uses_per_user" class="form-label">{{ $t("how_many_times_the_user_can_use_same_promo_code") }}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" class="form-control" :placeholder="$t('enter_how_many_times_the_user_can_use_same_promo_code')" id="uses_per_user" v-model="form.uses_per_user" />                   
                      <span v-for="(error, index) in errors.uses_per_user" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-lg-12">
                    <div class="text-end">
                      <button type="submit" class="btn btn-primary">{{ promo ? $t('update') : $t('save') }}</button>
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
    <BModal v-model="promocode" hide-footer :title="$t('promo_code')" class="v-modal-custom" size="lg">
      <p class="text-muted mb-0">How Coupon Works:</p>
      <div class="d-flex mt-4">       
        <div class="flex-shrink-0">
            <i class="ri-checkbox-circle-fill text-success"></i>
        </div>
        <div class="flex-grow-1 ms-2">
            <p class="text-muted mb-0">
              The coupon code will work based on its settings, such as the date range, 
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
                @click="promocode = false">
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
