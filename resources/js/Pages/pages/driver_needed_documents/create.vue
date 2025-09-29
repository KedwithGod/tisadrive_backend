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
    countries: Array,
    timeZones: Array,
    driverNeededDocument: Object,
    validate: Function, // Define the prop to receive the method
  },
  setup(props) {
    // console.log(props.driverNeededDocument);
    const { t } = useI18n();
    const form = useForm({
      name: props.driverNeededDocument?.name || "",
      account_type: props.driverNeededDocument?.account_type || "",
      has_expiry_date: props.driverNeededDocument?.has_expiry_date?.toString() || "",
      has_identify_number: props.driverNeededDocument?.has_identify_number?.toString() || "",
      identify_number_locale_key: props.driverNeededDocument?.identify_number_locale_key || "",
      image_type: props.driverNeededDocument?.image_type || "", // new image_type field
      document_name_front: props.driverNeededDocument?.document_name_front || "",
      document_name_back: props.driverNeededDocument?.document_name_back || "",
      is_editable: props.driverNeededDocument?.is_editable == 1, // Convert '1' or 1 to true, otherwise false
      is_required: props.driverNeededDocument?.is_required == 1, // Convert '1' or 1 to true, otherwise false
    });

    const validationRules = ref({
      name: { required: true },
      account_type: { required: true },
      has_expiry_date: { required: true },
      has_identify_number: { required: true },
      identify_number_locale_key: { required: (form.has_identify_number === 'yes') },
      image_type: { required: true }, // validation rule for image_type
    });

  
    const validationRef = ref(null);
    const errors = ref({});
    const successMessage = ref(props.successMessage || '');
    const alertMessage = ref(props.alertMessage || '');


    const has_identify_number = ref();

    const dismissMessage = () => {
      successMessage.value = "";
      alertMessage.value = "";
    };


    const handleSubmit = async () => {
      errors.value = validationRef.value.validate();
      if(form.image_type == 'front_and_back') {
        if(!form.document_name_back) {
          errors.value.document_name_back = [t('required')]
        }else{
          delete errors.value.document_name_back;
        }
        if(!form.document_name_front) {
          errors.value.document_name_front = [t('required')]
        }else{
          delete errors.value.document_name_front;
        }
      }
      if (Object.keys(errors.value).length > 0) {
        return;
      }
      try {
        let response;
        if (props.driverNeededDocument && props.driverNeededDocument.id) {
          response = await axios.post(`/driver-needed-documents/update/${props.driverNeededDocument.id}`, form.data());
        } else {
          response = await axios.post('store', form.data());
        }
        if (response.status === 201) {
          successMessage.value = t('driver-needed_docs_created_successfully');
          form.reset();
          router.get('/driver-needed-documents');
        } else {
          alertMessage.value = t('failed_to_create_driver_needed_docs');
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          errors.value = error.response.data.errors;
        } else {
          console.error(t('error_creating_driver_needed_docs'), error);
          alertMessage.value = t('failed_to_create_driver_needed_docs_catch');
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
      has_identify_number
    };
  }
};
</script>

<template>
  <Layout>

    <Head title="Driver Needed Documents" />
    <PageHeader :title="driverNeededDocument ? $t('edit') : $t('create')" :pageTitle="$t('driver_needed_documents')" pageLink="/driver-needed-documents"/>
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
                      <label for="name" class="form-label">{{$t("document_name")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" class="form-control" :placeholder="$t('enter_name')" id="name" v-model="form.name" />
                      <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="account_type" class="form-label">{{$t("account_type")}}
                        <span class="text-danger">*</span>
                      </label>
                      <select id="account_type" class="form-select" v-model="form.account_type">
                        <option disabled value="">{{$t("select")}}</option>
                        <option value="individual">{{$t("individual")}}</option>
                        <option value="fleet_driver">{{$t("fleet_drivers")}}</option>
                        <option value="both">{{$t("both")}}</option>
                      </select>
                      <span v-for="(error, index) in errors.account_type" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="has_expiry_date" class="form-label">{{$t("has_expiry_date")}}
                        <span class="text-danger">*</span>
                      </label>
                      <select id="has_expiry_date" class="form-select" v-model="form.has_expiry_date">
                        <option disabled value="">{{$t("select")}}</option>
                        <option value="1">{{$t("yes")}}</option>
                        <option value="0">{{$t("no")}}</option>
                      </select>
                      <span v-for="(error, index) in errors.has_expiry_date" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="image_type" class="form-label">{{$t("image_type")}}
                        <span class="text-danger">*</span>
                      </label>
                      <select id="image_type" class="form-select" v-model="form.image_type">
                        <option disabled selected value="">{{$t("select")}}</option>
                        <option value="front">{{$t("front")}}</option>
                        <option value="front_and_back">{{$t("front_and_backs")}}</option>
                      </select>
                      <span v-for="(error, index) in errors.image_type" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>                  
                  <div class="col-sm-6" v-if="form.image_type == 'front_and_back'">
                    <div class="mb-3">
                      <label for="document_name_front" class="form-label">{{$t("document_name_front")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" class="form-control" :placeholder="$t('enter_name')" id="name" v-model="form.document_name_front" />
                      <span v-for="(error, index) in errors.document_name_front" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6" v-if="form.image_type == 'front_and_back'">
                    <div class="mb-3">
                      <label for="document_name_back" class="form-label">{{$t("document_name_back")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" class="form-control" :placeholder="$t('enter_name')" id="name" v-model="form.document_name_back" />
                      <span v-for="(error, index) in errors.document_name_back" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="has_identify_number" class="form-label">{{$t("has_identify_number")}}
                        <span class="text-danger">*</span>
                      </label>
                      <select id="has_identify_number" class="form-select" v-model="form.has_identify_number">
                        <option disabled value="">{{$t("select")}}</option>
                        <option value="1">{{$t("yes")}}</option>
                        <option value="0">{{$t("no")}}</option>
                      </select>
                      <span v-for="(error, index) in errors.has_identify_number" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6" v-show="form.has_identify_number === '1'">
                    <div class="mb-3">
                      <label for="identify_number_locale_key" class="form-label">{{$t("identify_number_key")}}</label>
                      <input type="text" class="form-control" :placeholder="$t('enter_identify_number_key')" id="identify_number_locale_key" v-model="form.identify_number_locale_key" />
                      <span v-for="(error, index) in errors.identify_number_locale_key" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                <!-- New is_editable checkbox field -->
                  <div class="col-sm-2">
                    <div class="mt-3">
                      <div class="form-check form-check-inline">
                        <input
                          class="form-check-input"
                          type="checkbox"
                          id="is_editable"
                          v-model="form.is_editable"
                        />
                        <label class="form-check-label" for="is_editable">{{$t("is_editable")}}</label>
                      </div>
                    </div>
                  </div>
                  <!-- New is_required checkbox field -->
                  <div class="col-sm-2">
                    <div class="mt-3">
                      <div class="form-check form-check-inline">
                        <input
                          class="form-check-input"
                          type="checkbox"
                          id="is_required"
                          v-model="form.is_required"
                        />
                        <label class="form-check-label" for="is_required">{{$t("is_required")}}</label>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-12">
                    <div class="text-end">
                      <button type="submit" class="btn btn-primary">{{ driverNeededDocument ? $t('update') : $t('save') }}</button>
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
