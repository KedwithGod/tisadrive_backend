<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import { ref } from "vue";
import axios from "axios";
import imageUpload from "@/Components/widgets/imageUpload.vue";
import Swal from "sweetalert2";

import Multiselect from "@vueform/multiselect";
import FormValidation from "@/Components/FormValidation.vue";
import { useI18n } from 'vue-i18n';
import { BCardBody, BCardHeader } from 'bootstrap-vue-next';


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
    app_for: String,
    settings: Object,
    validate: Function, // Define the prop to receive the method
  },
  setup(props) {
    console.log(props.settings);
    const { t } = useI18n();
    const form = useForm({
      map_type: props.settings ? props.settings.map_type || "" : "",
      // google_sheet_id: props.settings ? props.settings.google_sheet_id || "" : "",
      google_map_key: props.settings ? props.settings.google_map_key || "" : "",
      // google_map_key_for_distance_matrix: props.settings ? props.settings.google_map_key_for_distance_matrix || "" : "",
    });
    const validationRules = {
      map_type: { required: true },
    };
    const validationRef = ref(null);
    const errors = ref({});
    const successMessage = ref(props.successMessage || '');
    const alertMessage = ref(props.alertMessage || '');

    const map_type = ref();

    const dismissMessage = () => {
      successMessage.value = "";
      alertMessage.value = "";
    };


    const handleSubmit = async () => {
      if(props.app_for == "demo"){
        Swal.fire(t('error'), t('you_are_not_authorised'), 'error');
        return;
      }
      errors.value = validationRef.value.validate();
      if (Object.keys(errors.value).length > 0) {
        return;
      }
      try {
        let response;
        let formData = new FormData();
          formData.append('map_type', form.map_type);
          formData.append('enable_vase_map', form.enable_vase_map);
          formData.append('google_map_key', form.google_map_key);
          // formData.append('google_map_key_for_distance_matrix', form.google_map_key_for_distance_matrix);
          // formData.append('google_sheet_id', form.google_sheet_id);
          response = await axios.post(`/map-setting/update`, formData);
        if (response.status === 201) {
          successMessage.value = t('map_settings_created_successfully');
          form.reset();
          router.get('/map-setting');
        } else {
          alertMessage.value = t('failed_to_create_map_settings');
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          errors.value = error.response.data.errors;
        } else {
          console.error(t('error_creating_map_settings'), error);
          alertMessage.value = t('failed_to_create_map_settings_catch');
        }
      }

    };

    return {
      form,
      successMessage,
      alertMessage,
      handleSubmit,
      dismissMessage,
      selectedMapType: ref(null),
      validationRules,
      validationRef,
      errors,
      map_type,
    };
  },
  mounted() {
        const googlemapkey = document.getElementById('google_map_key');
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === 'type' && googlemapkey.type !== 'password') {
                  googlemapkey.type = 'password'; // Reset to password type
                }
            });
        });
        observer.observe(googlemapkey, { attributes: true });
    }
};
</script>

<template>
  <Layout>

    <Head title="Map Settings" />
    <PageHeader :title="$t('map_settings')" :pageTitle="$t('map_settings')" />
    <BRow>
        <BCard v-if="app_for === 'demo'" no-body id="tasksList">
          <BCardHeader class="border-0">
            <div class="alert bg-warning border-warning fs-18" role="alert">
              <strong> {{$t('note')}} : <em> {{$t('actions_restricted_due_to_demo_mode')}}</em> </strong>
          </div>
        </BCardHeader>
      </BCard>
      <BCol lg="12"> 
        <form @submit.prevent="handleSubmit">
          <FormValidation :form="form" :rules="validationRules" ref="validationRef">
            <BCard no-body id="tasksList">
              <BCardHeader >                
                <h4  class="border-0">{{$t("choose_map_type")}}</h4>
              </BCardHeader>
              <BCardBody >
                    <div class="row">
                      <div class="col-lg-6">
                        <div class="mb-5">
                          <div class="card rounded border border-2 shadow-lg" style="width: 19rem;">
                            <label class="google_map">
                              <img class="rounded p-2" alt="google_map" src="@assets/images/google-map.jpg" width="100%" height="250px">
                              <h5 class="text-center mt-3">{{$t("google_map")}}</h5>
                              <div class="form-check form-check-success" style="margin-left: 140px;">
                                <input type="radio" :disabled="app_for === 'demo'" name="map_type" value="google_map" class="form-check-input" v-model="form.map_type">
                              </div>
                            </label>
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <div class="mb-5">
                          <div class="card rounded border border-2 shadow-lg" style="width: 19rem;">
                            <label class="open-street-map">
                              <img class="rounded p-2" alt="open-street-map" src="@assets/images/open-street-map.png" width="100%" height="250px">
                              <h5 class="text-center mt-3">{{$t("open_Street")}}</h5>
                              <div class="form-check form-check-success" style="margin-left: 140px;">
                                <input type="radio" :disabled="app_for === 'demo'" name="map_type" value="open_street_map" class="form-check-input" v-model="form.map_type">
                              </div>
                            </label>
                          </div>
                        </div>
                      </div>
                      <!-- <div class="col-lg-12">
                        <div class="text-center">
                          <button type="submit" class="btn btn-primary">{{ settings ? $t('update') : $t('save') }}</button>
                        </div>
                      </div> -->
                    </div>
              </BCardBody>
            </BCard>  
            <BCard>
              <BCardHeader>
                <h4  class="border-0">{{$t("google_map_apis")}}</h4>
              </BCardHeader>
              <BCardBody>                
                <div class="row">
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="google_map_key" class="form-label">{{$t("google_map_key_for_web_apps")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="password":readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_google_map_key_for_web_apps')" id="google_map_key" v-model="form.google_map_key" />
                      <!-- <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span> -->
                    </div> 
                  </div>
                  <!-- <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="google_map_key_for_distance_matrix" class="form-label">{{$t("google_map_key_for_distance_matrix")}}</label>
                      <input type="text" class="form-control" :placeholder="$t('enter_google_map_key_for_distance_matrix')" id="google_map_key_for_distance_matrix"  v-model="form.google_map_key_for_distance_matrix" />
                      <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="google_sheet_id" class="form-label">{{$t("google_sheet_id")}}</label>
                      <input type="text" class="form-control" :placeholder="$t('enter_google_sheet_id')" id="google_sheet_id" v-model="form.google_sheet_id" 
                      />
                      <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div> -->
                  <div class="col-lg-12">
                    <div class="text-end">
                      <button type="submit" class="btn btn-primary">{{ settings ? $t('update') : $t('save') }}</button>
                    </div>
                  </div>
                </div>             
              </BCardBody>
            </BCard>       
          </FormValidation>
        </form> 
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

[type=radio] {
  padding: 10px;
}

.card{
  margin-left: auto;
  margin-right: auto;
  }

.rtl .form-check .form-check-input {
    float: none;
}


</style>
