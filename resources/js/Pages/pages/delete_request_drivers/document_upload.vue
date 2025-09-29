<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import { ref } from "vue";
import axios from "axios";
import imageUpload from "@/Components/widgets/imageUpload.vue";

import Multiselect from "@vueform/multiselect";
import FormValidation from "@/Components/FormValidation.vue";


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
    countries: Array,
    timeZones: Array,
    serviceLocation: Object,
    validate: Function, // Define the prop to receive the method
  },
  setup(props) {
    console.log(props.serviceLocation);
    const form = useForm({
      country: props.serviceLocation ? props.serviceLocation.country || "" : "",
      name: props.serviceLocation ? props.serviceLocation.name || "" : "",
      currencycode: props.serviceLocation ? props.serviceLocation.currency_code || "" : "",
      currencysymbol: props.serviceLocation ? props.serviceLocation.currency_symbol || "" : "",
      timezone: props.serviceLocation ? props.serviceLocation.timezone || "" : "",
      unit: props.serviceLocation ? props.serviceLocation.unit || "" : "",
    });
    const validationRules = {
      country: { required: true },
      name: { required: true },
      currencycode: { required: true },
      currencysymbol: { required: true },
      timezone: { required: true },
      unit: { required: true }
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
      try {
        let response;
        if (props.serviceLocation && props.serviceLocation.id) {
          response = await axios.post(`/service-locations/update/${props.serviceLocation.id}`, form.data());
        } else {
          response = await axios.post('store', form.data());
        }
        if (response.status === 201) {
          successMessage.value = 'Service location created successfully.';
          form.reset();
          router.get('/service-locations');
        } else {
          alertMessage.value = 'Failed to create service location.';
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          errors.value = error.response.data.errors;
        } else {
          console.error('Error creating service location:', error);
          alertMessage.value = 'Failed to create service location. catch';
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
      errors
    };
  }
};
</script>

<template>
  <Layout>

    <Head title="Document Upload" />
    <PageHeader :title="$t('create')" :pageTitle="$t('document_upload')"  pageLink="/delete-request-drivers"/>
    <BRow>
      <BCol lg="12">
        <BCard no-body id="tasksList">
          <BCardHeader class="border-0"></BCardHeader>
          <BCardBody class="border border-dashed border-end-0 border-start-0">

            <form @submit.prevent="handleSubmit">
              <FormValidation :form="form" :rules="validationRules" ref="validationRef">
                <div class="row">
                  
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="name" class="form-label">{{$t("name")}}</label>
                      <input type="text" class="form-control" :placeholder="$t('enter_driving_license')" id="name" 
                      />
                      <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="identify_number" class="form-label">{{$t("identify_number")}}</label>
                      <input type="text" class="form-control" :placeholder="$t('enter_identify_number')" id="identify_number" 
                      />
                      <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="expiry_date" class="form-label">{{$t("expiry_date")}}</label>
                      <input type="text" class="form-control" placeholder="$t('enter_expiry_date')" id="expiry_date" 
                      />
                      <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="mb-3">
                      <label for="icon" class="form-label d-flex">{{ $t("document") }} 
                        <span><h5 class="text-muted mt-1 mb-0 fs-13">(512px x 512px)</h5></span>
                      </label>
                      <!-- Display existing vehicle image if available -->
                      <div v-if="iconUrl && !form.iconFile">
                        <img :src="iconUrl" alt="Vehicle Image" style="max-width: 100px; max-height: 100px;" />
                      </div>
                      <!-- Image Upload Component -->
                      <imageUpload v-model="form.iconFile"></imageUpload>
                      <span v-if="form.errors.icon" class="text-danger">{{ form.errors.icon }}</span>
                    </div>
                  </div>
                                
                  <div class="col-lg-12">
                    <div class="text-end">
                      <button type="submit" class="btn btn-primary"> {{ serviceLocation ? $t('update') : $t('save') }}</button>
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
