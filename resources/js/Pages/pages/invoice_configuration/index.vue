<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import { ref } from "vue";
import axios from "axios";
import imageUpload from "@/Components/widgets/imageUpload.vue";
import ImageUp from "@/Components/ImageUp.vue";
import Multiselect from "@vueform/multiselect";
import FormValidation from "@/Components/FormValidation.vue";
import { useI18n } from 'vue-i18n';
import Swal from "sweetalert2";


export default {
  components: {
    Layout,
    PageHeader,
    Head,
    Pagination,
    Multiselect,
    FormValidation,
    imageUpload,
    ImageUp
  },
  props: {
    successMessage: String,
    alertMessage: String,
    invoice_configuration :{
            type: Object,
            required: true,
        },
    app_for: String,
    validate: Function, // Define the prop to receive the method
  },
  setup(props) {
    const { t } = useI18n();
    console.log(props.invoice_configuration);
    const form = useForm({
      privacy_policy_link: props.invoice_configuration ? props.invoice_configuration.privacy_policy_link || "" : "",
      terms_and_conditions_link: props.invoice_configuration ? props.invoice_configuration.terms_and_conditions_link || "" : "",
      invoice_email: props.invoice_configuration ? props.invoice_configuration.invoice_email || "" : "",
      invoice_logo: props.invoice_configuration ? props.invoice_configuration.invoice_logo || "" : "",
      // icon: null
    });
    const validationRules = {
      terms_and_conditions_link: { required: true },
      terms_condition_link: { required: true },
      invoice_email: { required: true },
      invoice_logo:{ required: true },
      // icon: { required: props.invoice_configuration ? false : true }
    };
    const validationRef = ref(null);
    const errors = ref({});
    const successMessage = ref(props.successMessage || '');
    const alertMessage = ref(props.alertMessage || '');

    const dismissMessage = () => {
      successMessage.value = "";
      alertMessage.value = "";
    };

    const validateIconSize = () => {
      const fileInput = document.getElementById('iconInput');
      if (fileInput.files.length > 0) {
        const file = fileInput.files[0];
        const img = new Image();
        img.onload = function () {
          if (img.width !== 512 || img.height !== 512) {
            alert('Icon must be 512x512 pixels.');
            fileInput.value = '';
          }
        };
        img.src = URL.createObjectURL(file);
      }
    };
    const handleSubmit = async () => {
      if(props.app_for == "demo"){
          Swal.fire(t('error'), t('you_are_not_authorised'), 'error');
          return;
      }
      try {
        const formData = new FormData();
        for (const key in form) {
          if (key === 'iconFile' && form[key]) {
            formData.append('invoice_logo', form[key]);
          } else {
            formData.append(key, form[key]);
          }
        }

        let response;
        // if (props.invoice_configuration && props.invoice_configuration.id) {
          response = await axios.post(`/invoice-configuration/update`, formData);

        // } else {
          // response = await axios.post('/invoice-configuration/store', formData);
        // }

        if (response.status === 201) {
          successMessage.value = t('invoice_configuration_updated_successfully');
          form.reset();
          router.get('/invoice-configuration');
        } else {
          alertMessage.value = t('failed_to_update_invoice_configuration');
        }
      } catch (error) {
        console.error(t('error_creating_invoice_configuration'), error);
        alertMessage.value = t('failed_to_create_invoice_configuration_catch');
        if (error.response && error.response.data.errors) {
          errors.value = error.response.data.errors;
        }
      }
    };


      // Construct the full URL for the vehicle type icon
    const iconUrl = props.invoice_configuration ? props.invoice_configuration.icon :null;

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
      iconUrl,
      validateIconSize,
      handleImageSelected,
      handleImageRemoved,
    };
  }
};
</script>

<template>
  <Layout>

    <Head title="Invoice Configuration" />
    <PageHeader :title="$t('invoice-configuration')" :pageTitle="$t('invoice-configuration')" />
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
                      <label for="privacy_policy_link" class="form-label">{{$t("privacy_policy")}}
                        <span class="text-danger">*</span>
                      </label>
                      <textarea type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_privacy_policy')" id="privacy_policy_link" v-model="form.privacy_policy_link"
                      />
                      <span v-for="(error, index) in errors.privacy_policy_link" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div> 
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="terms_and_conditions_link" class="form-label">{{$t("terms_and_conditions_link")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="url" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_terms_and_condition_link')" id="terms_and_conditions_link" v-model="form.terms_and_conditions_link"  />
                      <span v-for="(error, index) in errors.terms_and_conditions_link" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>             
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="invoice_email" class="form-label">{{$t("invoice_email")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="email" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_invoice_email')" id="invoice_email" v-model="form.invoice_email" />
                      <span v-for="(error, index) in errors.invoice_email" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>

                  <!-- <div class="col-12">
                    <div class="mb-3">
                      <label for="invoice_logo" class="form-label d-flex">Invoice Logo Image 
                        <span><h5 class="text-muted mt-1 mb-0 fs-13">(512px x 512px)</h5></span>
                      </label>
                      Display existing vehicle image if available
                      <div v-if="iconUrl && !form.invoice_logo">
                        <img :src="iconUrl" alt="Invoice Logo Image" style="max-width: 100px; max-height: 100px;" />
                      </div>
                      Image Upload Component
                      <imageUpload v-model="form.invoice_logo"></imageUpload>
                      <span v-for="(error, index) in errors.invoice_logo" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div> -->
                  <div class="col-sm-6 mt-3">
                    <div class="mb-3">
                      <label for="invoice_logo" class="form-label">{{$t("invoice_logo")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ImageUp :imageType="'invoice'" :flexStyle="'0 0 calc(60% - 10px)'"
                      :aspectRatio="'18 / 5'"  :initialImageUrl="form.invoice_logo" @image-selected="(file) => handleImageSelected(file, 'invoice_logo')" @image-removed="() => handleImageRemoved('invoice_logo')"></ImageUp>
                    </div>
                  </div>
                  <div class="col-lg-12">
                    <div class="text-end">
                      <button type="submit" class="btn btn-primary"> {{invoice_configuration  ? $t('update') : $t('save') }}</button>
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
