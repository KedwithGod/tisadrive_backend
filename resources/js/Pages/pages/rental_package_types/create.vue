<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import { ref } from "vue";
import axios from "axios";
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
  },
  props: {
    successMessage: String,
    alertMessage: String,
    packageType: Object,
    validate: Function, // Define the prop to receive the method
  },
  setup(props) {
    const { t } = useI18n();
    console.log(props.packageType);
    const form = useForm({
      name: props.packageType ? props.packageType.name || "" : "",
      transport_type: props.packageType ? props.packageType.transport_type || "" : "",
      short_description: props.packageType ? props.packageType.short_description || "" : "",
      description: props.packageType ? props.packageType.description || "" : "",
    });
    const validationRules = {
      name: { required: true },
      transport_type: { required: true },
      short_description: { required: true },
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


    const handleSubmit = async () => {
      errors.value = validationRef.value.validate();
      if (Object.keys(errors.value).length > 0) {
        return;
      }
      try {
        let response;
        if (props.packageType && props.packageType.id) {
          response = await axios.post(`/rental-package-types/update/${props.packageType.id}`, form.data());
        } else {
          response = await axios.post('store', form.data());
        }
        if (response.status === 201) {
          successMessage.value = t('rental_package_created_successfully');
          form.reset();
          router.get('/rental-package-types');
        } else {
          alertMessage.value = t('failed_to_create_rental_package');
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          errors.value = error.response.data.errors;
        } else if (error.response && error.response.status === 403) {
          alertMessage.value = error.response.data.alertMessage;
          setTimeout(()=>{
            router.get('/rental-package-types');
          },5000)
        } else {
          console.error(t('error_creating_rental_Package'), error);
          alertMessage.value = t('failed_to_create_rental_package_catch');
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

    <Head title="Rental Package Types" />
    <PageHeader :title="packageType ? $t('edit') : $t('create')" :pageTitle="$t('rental_package_types')" pageLink="/rental-package-types" />
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
                      <label for="select_transport_type" class="form-label"> {{$t("transport_type")}}
                        <span class="text-danger">*</span>
                      </label>
                      <select id="select_transport_type" class="form-select" v-model="form.transport_type">
                        <option disabled value="">{{$t("select_transport_type")}}</option>
                        <option  value="taxi">{{$t('taxi')}}</option>
                        <option value="delivery">{{$t('delivery')}}</option>
                        <option value="both">{{$t('all')}}</option>
                      </select>
                      <span v-for="(error, index) in errors.transport_type" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="name" class="form-label">{{$t("name")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" class="form-control" :placeholder="$t('enter_name')" id="name" v-model="form.name" />
                      <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="short_description" class="form-label">{{$t("short_description")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" class="form-control" :placeholder="$t('enter_short_description')" id="short_description"
                        v-model="form.short_description" />
                      <span v-for="(error, index) in errors.short_description" :key="index" class="text-danger">{{ error
                        }}</span>
                    </div>
                  </div>                  
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="description" class="form-label"> {{$t("description")}}
                        <span class="text-danger">*</span>
                      </label>
                      <textarea type="text" class="form-control" :placeholder="$t('enter_description')" id="description"
                        v-model="form.description" />
                      <span v-for="(error, index) in errors.description" :key="index" class="text-danger">{{ error
                        }}</span>
                    </div>
                  </div>
                  <div class="col-lg-12">
                    <div class="text-end">
                      <button type="submit" class="btn btn-primary"> {{ packageType ? $t('update') : $t('save') }}</button>
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
