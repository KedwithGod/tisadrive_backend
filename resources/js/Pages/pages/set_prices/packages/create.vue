<template>
    <Layout>
      <Head title="Set Prices" />
      <PageHeader :title="zoneTypePackage ? $t('edit') : $t('create')" :pageTitle="$t('set_prices')" pageLink="/set-prices"/>
      <BRow>
        <BCol lg="12">
          <BCard no-body id="tasksList">
            <BCardHeader class="border-0"></BCardHeader>
            <BCardBody class="border border-dashed border-end-0 border-start-0">
              <form @submit.prevent="handleSubmit">
                <FormValidation :form="form" :rules="validationRules" ref="validationRef">
                  <div class="row">
                    <input type="hidden" class="form-control" id="zone_type_price_id" v-model.number="form.zone_type_price_id">
  
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <label for="select_package_type" class="form-label">{{$t("package_type")}}
                          <span class="text-danger">*</span>
                        </label>
                        <select id="package_type" class="form-select" v-model="form.package_type_id">
                          <option disabled value="">{{$t("select_package_type")}}</option>
                          <option v-for="packageType in packageTypes" :key="packageType.id" :value="packageType.id">{{ packageType.name }}</option>
                        </select>
                        <span v-for="(error, index) in errors.package_type_id" :key="index" class="text-danger">{{ error }}</span>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <label for="base_price" class="form-label">{{$t("base_price")}}
                          <span class="text-danger">* </span>
                          <!-- ({{$t("kilo_meter")}}) -->
                        </label>
                        <input type="number" step="any" class="form-control" :placeholder="$t('enter_base_price')" id="base_price" v-model.number="form.base_price">
                        <span v-for="(error, index) in errors.base_price" :key="index" class="text-danger">{{ error }}</span>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <label for="base_distance" class="form-label">{{$t("free_distance")}}
                          <span class="text-danger">*</span>
                          <span v-if = "unit">({{ unit }})</span>
                        </label>
                        <input type="number" step="any" class="form-control" :placeholder="$t('enter_free_distance')" id="base_distance" v-model.number="form.base_distance">
                        <span v-for="(error, index) in errors.base_distance" :key="index" class="text-danger">{{ error }}</span>
                      </div>
                    </div>                    
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <label for="distance_price_per_km" class="form-label">{{$t("distance_price")}}
                          <span class="text-danger">*</span>
                        </label>
                        <input type="number" step="any" class="form-control" :placeholder="$t('enter_price_per_distance')" id="distance_price_per_km" v-model.number="form.distance_price_per_km">
                        <span v-for="(error, index) in errors.distance_price_per_km" :key="index" class="text-danger">{{ error }}</span>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <label for="free_min" class="form-label">{{$t("free_time_in_minute")}}</label>
                        <input type="number" step="any" class="form-control" :placeholder="$t('enter_free_min')" id="free_min" v-model.number="form.free_min
                        ">
                        <span v-for="(error, index) in errors.free_min" :key="index" class="text-danger">{{ error }}</span>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="mb-3">
                        <label for="time_price_per_min" class="form-label">{{$t("time_price")}}
                          <span class="text-danger">*</span>
                        </label>
                        <input type="number" step="any" class="form-control" :placeholder="$t('enter_time_price')" id="time_price_per_min" v-model.number="form.time_price_per_min">
                        <span v-for="(error, index) in errors.time_price_per_min" :key="index" class="text-danger">{{ error }}</span>
                      </div>
                    </div>
                    <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="cancellation_fee" class="form-label">{{$t("cancellation_fee")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="number" class="form-control"  :placeholder="$t('cancellation_fee')"  id="cancellation_fee" v-model.number="form.cancellation_fee">
                      <span v-for="(error, index) in errors.cancellation_fee" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>

                    <div class="col-12 text-end">
                      <button type="submit" class="btn btn-success">{{$t("save")}}</button>
                    </div>
                  </div>
                </FormValidation>
              </form>
              <!-- <div v-if="successMessage" class="alert alert-success alert-dismissible mt-3" role="alert">
                <button type="button" class="btn-close" aria-label="Close" @click="dismissMessage"></button>
                {{ successMessage }}
              </div>
              <div v-if="alertMessage" class="alert alert-danger alert-dismissible mt-3" role="alert">
                <button type="button" class="btn-close" aria-label="Close" @click="dismissMessage"></button>
                {{ alertMessage }}
              </div> -->
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
            </BCardBody>
          </BCard>
        </BCol>
      </BRow>
    </Layout>
  </template>
  
  <script>
  import { Head, useForm, router } from '@inertiajs/vue3';
  import Layout from "@/Layouts/main.vue";
  import PageHeader from "@/Components/page-header.vue";
  import Pagination from "@/Components/Pagination.vue";
  import { ref, onMounted } from "vue";
  import axios from "axios";
  import FormValidation from "@/Components/FormValidation.vue";
  import { useI18n } from 'vue-i18n';

  export default {
    components: {
      Layout,
      PageHeader,
      Head,
      Pagination,
      FormValidation,
    },
    props: {
      successMessage: String,
      alertMessage: String,
      packageTypes: Array,
      zoneTypePrice: Object,
      zoneTypePackage: Object,
      zone_unit:Object,
    },
    setup(props) {
      const { t } = useI18n();
      const form = useForm({
        zone_type_price_id: props.zoneTypePrice?.id || "",
        package_type_id: props.zoneTypePackage?.package_type_id || "",
        base_price: props.zoneTypePackage?.base_price || "",
        distance_price_per_km: props.zoneTypePackage?.distance_price_per_km || "",
        free_min: props.zoneTypePackage?.free_min || "",
        time_price_per_min: props.zoneTypePackage?.time_price_per_min || "",
        base_distance: props.zoneTypePackage?.free_distance || "",
        cancellation_fee: props.zoneTypePackage?.cancellation_fee || "",
      });
  
      const validationRules = {
        package_type_id: { required: true },
        base_price: { required: true },
        distance_price_per_km: { required: true },
        free_min: { required: true },
        time_price_per_min: { required: true },
        base_distance: { required: true },
        cancellation_fee: { required: true },
      };
  
      const validationRef = ref(null);
      const errors = ref({});
      const successMessage = ref(props.successMessage || '');
      const alertMessage = ref(props.alertMessage || '');
      const zone_unit = ref(props.zone_unit);
      const unit = ref();
  
      const handleSubmit = async () => {
        errors.value = validationRef.value.validate(form);
        if (Object.keys(errors.value).length > 0) {
          return;
        }
        try {
          let response;
          if (props.zoneTypePackage && props.zoneTypePackage.id) {
            response = await axios.post(`/set-prices/packages/update/${props.zoneTypePackage.id}`, form);
          } else {
            response = await axios.post('/set-prices/packages/store', form);
          }
          if (response.status === 201) {
            successMessage.value = t('package_price_created_successfully');
            // form.reset();
            router.get(`/set-prices/packages/${props.zoneTypePrice.zone_type_id}`);
          } else {
            alertMessage.value = t('failed_to_create_package_price');
          }
        } catch (error) {
          if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors;
          } else {
            console.error(t('error_creating_package_price'), error);
            alertMessage.value = t('failed_to_create_package_price');
          }
        }
      };
  
      const dismissMessage = () => {
        successMessage.value = "";
        alertMessage.value = "";
      };
        onMounted(() => {

          if (zone_unit.value == 1) {
            unit.value =  t('kilo_meter'); // Unit 1 corresponds to kilometers
          } else if (zone_unit.value == 2) {
            unit.value = t('miles'); // Unit 2 corresponds to miles
          }

      });
  
      return {
        form,
        packageTypes: ref(props.packageTypes || []),
        successMessage,
        alertMessage,
        handleSubmit,
        dismissMessage,
        validationRules,
        validationRef,
        errors,
        unit
      };
    },
  };
  </script>
  
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
  