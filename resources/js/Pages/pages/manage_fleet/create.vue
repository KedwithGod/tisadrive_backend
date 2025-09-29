<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import { ref } from "vue";
import axios from "axios";
import imageUpload from "@/Components/widgets/imageUpload.vue";
import animationData from "@/Components/widgets/lupuorrc.json";
import Lottie from "@/Components/widgets/lottie.vue";

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
    lottie: Lottie,
  },
  props: {
    successMessage: String,
    alertMessage: String,
    countries: Array,
    timeZones: Array,
    owners: Array,  // Ensure 'owners' is an array
    types: Object,
    fleet: Object,
    validate: Function, // Define the prop to receive the method
    selectedOwner: Object, // New prop for the selected owner
  },
  setup(props) {
    const { t } = useI18n();
    const form = useForm({
        owner_id: props.selectedOwner ? props.selectedOwner.id || "" : "", // Use selectedOwner to set the initial value
      custom_make: props.fleet ? props.fleet.car_make_name || "" : "",
      custom_model: props.fleet ? props.fleet.car_model_name || "" : "",
      vehicle_type: props.fleet ? props.fleet.vehicle_type || "" : "",
      license_number: props.fleet ? props.fleet.license_number || "" : "",
      car_color: props.fleet ? props.fleet.car_color || "" : "",
    });
    const validationRules = {
      owner_id: { required: true },
      custom_make: { required: true },
      custom_model: { required: true },
      vehicle_type: { required: true },
      license_number: { required: true },
      car_color: { required: true },
    };
    const validationRef = ref(null);
    const errors = ref({});
    const successMessage = ref(props.successMessage || '');
    const alertMessage = ref(props.alertMessage || '');

    const dismissMessage = () => {
      successMessage.value = "";
      alertMessage.value = "";
    };

    const owners = ref(props.owners || []);
    const types = ref(props.types || []);
    const fleet = ref(props.fleet);

    const handleSubmit = async () => {
      errors.value = validationRef.value.validate();
      if (Object.keys(errors.value).length > 0) {
        return;
      }
      try {
        let response;
        if (props.fleet && props.fleet.id) {
          response = await axios.post(`/manage-fleet/update/${props.fleet.id}`, form.data());
        } else {
          response = await axios.post('/manage-fleet/store', form.data());
        }
        if (response.status === 201) {
          successMessage.value = t('fleet_created_successfully');
          form.reset();
          router.get('/manage-fleet');
        } else {
          alertMessage.value = t('failed_to_create_fleet');
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          errors.value = error.response.data.errors;
        } else {
          console.error(t('error_creating_fleet'), error);
          alertMessage.value = t('failed_to_create_fleet_catch');
        }
      }

    };

    return {
      form,
      successMessage,
      alertMessage,
      handleSubmit,
      dismissMessage,
      selectedCountry: ref(null),
      selectedTimezone: ref(null),
      validationRules,
      validationRef,
      errors,
      types,
      owners,
      fleet,
    };
  },
};
</script>

<template>
  <Layout>

    <Head title="Manage Fleet" />
    <PageHeader :title="fleet ? $t('edit') : $t('create')" :pageTitle="$t('manage_fleet')" pageLink="/manage-fleet"/>
    <BRow>
      <BCol lg="12">
        <BCard no-body id="tasksList">
          <BCardHeader class="border-0"></BCardHeader>
          <BCardBody class="border border-dashed border-end-0 border-start-0 form-steps">
            
            <form  @submit.prevent="handleSubmit" class="form-steps"   >
              <FormValidation :form="form" :rules="validationRules" ref="validationRef"> 
                <div class="row">
                  <div class="col-sm-6">
                      <div class="mb-3">
                          <label for="owner_id" class="form-label">{{$t("owner")}}
                              <span class="text-danger">*</span>
                          </label>
                          <select id="owner_id" class="form-control" v-model="form.owner_id">
                              <option value="" disabled>{{$t("select_an_owner")}}</option>
                              <option v-for="owner in owners" :key="owner.user_id" :value="owner.user_id">{{ owner.company_name }}</option>
                          </select>
                          <span v-for="(error, index) in errors.owner_id" :key="index" class="text-danger">{{ error }}</span>
                      </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="select_type" class="form-label">{{$t("select_type")}}
                        <span class="text-danger">*</span>
                      </label>
                      <select id="select_type" class="form-select" v-model="form.vehicle_type">
                        <option v-for="(type,index) in types" :key="index" :value="type.id">{{ type.name }}</option>
                      </select>
                      <span v-for="(error, index) in errors.vehicle_type" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="car_brand" class="form-label">{{$t("car_brand")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input id="car_brand" class="form-control" :placeholder="$t('enter_car_make')" v-model="form.custom_make">
                      <span v-for="(error, index) in errors.custom_make" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="car_model" class="form-label">{{$t("car_model")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input id="car_model" class="form-control" :placeholder="$t('enter_car_model')" v-model="form.custom_model">
                      <span v-for="(error, index) in errors.custom_model" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="license_plate_number" class="form-label">{{$t("license_plate_number")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" class="form-control" :placeholder="$t('enter_license_plate_number')" id="license_plate_number" v-model="form.license_number"/>
                      <span v-for="(error, index) in errors.license_number" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="car_color" class="form-label">{{$t("car_color")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" class="form-control" :placeholder="$t('enter_car_color')" id="car_color" v-model="form.car_color"/>
                      <span v-for="(error, index) in errors.car_color" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-lg-12">
                    <div class="text-end">
                      <button type="submit" class="btn btn-primary"> {{ fleet ? $t('update') : $t('save') }}</button>
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
