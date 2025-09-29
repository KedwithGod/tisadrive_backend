<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import { ref } from "vue";
import axios from "axios";
import imageUpload from "@/Components/widgets/imageUpload.vue";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
// import animationData from "@/components/widgets/lupuorrc.json";
// import Lottie from "@/components/widgets/lottie.vue";

import Multiselect from "@vueform/multiselect";
import FormValidation from "@/Components/FormValidation.vue";
import { useI18n } from 'vue-i18n';

export default {
  components: {
    Layout,
    PageHeader,
    Head,
    Pagination,
    flatPickr,
    Multiselect,
    FormValidation,
    imageUpload,
    // lottie: Lottie
  },
  props: {
    successMessage: String,
    alertMessage: String,
    countries: Array,
    timeZones: Array,
    driver: Object,
    neededDocuments: Object,
    documents: Object,
    validate: Function, // Define the prop to receive the method
  },
  setup(props) {
    const { t } = useI18n();
    const driver = ref(props.driver || '');
    const neededDocuments = ref(props.neededDocuments || '');
    const documents = ref(props.documents || '');
    const form = useForm({
        iconFile: neededDocuments.value.map(() => null),
        expiry_date: neededDocuments.value.map((doc,index) => doc.has_expiry_date ? ( props.documents?.[index] ? props.documents?.[index]?.expiry_date : null) : undefined),
        identify_number: neededDocuments.value.map((doc,index) => doc.has_identify_number ? ( props.documents?.[index] ? props.documents?.[index]?.identify_number : null) : undefined),
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

    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(today.getDate() + 1);
    const rangeDateconfig = {
      minDate: tomorrow, // Set minimum date to tomorrow
      dateFormat: 'Y-m-d', // Adjust the date format as needed
    };
    const iconUrl = ref([]);
    const imageSrc = (index) => {
      return props.documents ? (props.documents[index] ? props.documents[index].image : null) : null;
    };
    
    const onFileChange = (event,index) => {
        const file = event.target.files[0];
        form.iconFile[index] = file;
        if (file) {
            const reader = new FileReader();
            reader.readAsDataURL(file);
        }
    };
    const handleSubmit = async () => {
      const formData = new FormData();

      neededDocuments.value.forEach((doc, index) => {
          if (form.iconFile[index]) {
              formData.append(`iconFile_${index+1}`, form.iconFile[index]);
          }
          if (form.expiry_date[index] !== undefined) {
              formData.append(`expiry_date[${index}]`, form.expiry_date[index]);
          }
          if (form.identify_number[index] !== undefined) {
              formData.append(`identify_number[${index}]`, form.identify_number[index]);
          }
      });
      try {
        let response;
        response = await axios.post(`/fleet-drivers/document/upload/${props.driver.id}`, formData,{
          headers: {'Content-Type': 'multipart/form-data'}
        });
        if (response.status === 201) {
          successMessage.value = t('driver_document_uploaded_successfully');
          form.reset();
          router.get('/fleet-drivers');
        } else {
          alertMessage.value = $t('failed_to_upload_driver_document');
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          errors.value = error.response.data.errors;
        } else {
          console.error(t('error_upload_driver_document'), error);
          alertMessage.value = t('failed_to_upload_driver_document_catch');
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
      rangeDateconfig,
      imageSrc,
      iconUrl,
      onFileChange,
    };
  },

 
};


</script>

<template>
  <Layout>

    <Head title="Edit Document" />
    <PageHeader title="Create" pageTitle="Edit Document" pageLink="/fleet-drivers"/>
    <BRow>
      <BCol lg="12">
        <BCard no-body id="tasksList">
          <BCardHeader class="border-0"></BCardHeader>
          <BCardBody class="border border-dashed border-end-0 border-start-0 form-steps">
            <form  @submit.prevent="handleSubmit"  class="form-steps" enctype="multipart/form-data">
            <FormValidation :form="form" :rules="validationRules" ref="validationRef">  
              <div class="row" v-for="(needed_doc,index) in neededDocuments">
                <h4> {{ needed_doc.name }}</h4>
                <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="document" class="form-label d-flex">{{$t("documents")}}
                        <!-- <span><h5 class="text-muted mt-1 mb-0 fs-13">(512px x 512px)</h5></span> -->
                      </label>
                      <!-- Image Upload Component -->
                      <imageUpload  @change="onFileChange($event,index)" v-model="form.iconFile[index]" :src="iconUrl"></imageUpload>
                      <span v-if="form.errors.icon" class="text-danger">{{ errors.iconFile[index] }}</span>
                    </div>
                </div>
                <div class="col-sm-6">
                  <div class="mb-3" v-if="needed_doc.has_expiry_date">
                    <label :for="`expiry_date_${index}`" class="form-label">{{$t("expiry_date")}}</label>
                    <flat-pickr :placeholder="$t('select_expiry_date')" v-model="form.expiry_date[index]" :config="rangeDateconfig"
                              class="form-control flatpickr-input" :id="`expiry_date_${index}`"></flat-pickr>
                    <span v-for="(error, index) in errors.expiry_date?.[index]" :key="index" class="text-danger">{{ error }}</span>
                  </div>
                  <div class="mb-3" v-if="needed_doc.has_identify_number">
                    <label :for="`${needed_doc.identify_number_locale_key}_${index}`" class="form-label"> {{ needed_doc.identify_number_locale_key }}</label>
                    <input type="text" class="form-control"  v-model="form.identify_number[index]" :placeholder="`Enter ${needed_doc.identify_number_locale_key}`" :id="`${needed_doc.identify_number_locale_key}_${index}`"/>
                    <span v-for="(error, index) in errors.identify_number?.[index]" :key="index" class="text-danger">{{ error }}</span>
                  </div>
                  <div class="mb-3">
                      <div v-if="imageSrc(index)">
                          <img :src="imageSrc(index)" alt="Document Image" style="max-width: 400px; max-height: 400px;" />
                      </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-12">
                <div class="text-end">
                  <button type="submit" class="btn btn-primary"> {{$t("upload")}}</button>
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
