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
import ImageUpload from '@/Components/ImageUpload.vue';

export default {
  components: {
    Layout,
    PageHeader,
    Head,
    Pagination,
    Multiselect,
    FormValidation,
    imageUpload,
    ImageUpload
  },
  props: {
    successMessage: String,
    alertMessage: String,
    app_for: String,
    countries: Array,
    roles: Array,
    role: Array,
    admin: Array,
    serviceLocations: Array,
    validate: Function,
  },
  setup(props) {
    const { t } = useI18n();
    const form = useForm({
      password: props.admin ? props.admin.password || "" : "",
      confirm_password: props.admin ? props.admin.password || "" : "",
    });

    const validationRules = {
      password: { required: true },
      confirm_password: { required: !props.admin, sameAs: "password" },
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
        if (props.admin && props.admin.id) {
          response = await axios.post(`/admins/password/update/${props.admin.id}`, form.data());
        } else {
          response = await axios.post('store', formData);
        }
        if (response.status === 201) {
          successMessage.value = t('admin_created_successfully');
          form.reset();
          router.get('/admins');
        } else {
          alertMessage.value = t('failed_to_create_admin');
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          errors.value = error.response.data.errors;
        } else {
          console.error(t('error_creating_admin'), error);
          alertMessage.value = t('failed_to_create_admin');
        }
      }
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
    };
  }
};
</script>


<template>
  <Layout>

    <Head title="Admin" />
    <PageHeader :title="admin? $t('edit') : $t('create')" :pageTitle="$t('admins')" pageLink="/admins"/>
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
                      <label for="password" class="form-label">{{$t("password")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="password"  name="password" class="form-control" :placeholder="$t('enter_password')" id="password"  v-model="form.password"
                      />
                      <span v-if="errors.password" class="text-danger">{{ errors.password }}</span>
                    </div>
                  </div> 
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="confirm_password" class="form-label">{{$t("confirm_password")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="password"  name="confirm_password" class="form-control" :placeholder="$t('confirm_password')" id="confirm_password"  v-model="form.confirm_password" 
                      />
                      <span v-if="errors.confirm_password"  class="text-danger">{{ errors.confirm_password }}</span>
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