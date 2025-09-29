<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import { ref,computed } from "vue";
import axios from "axios";
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
  },
  props: {
    successMessage: String,
    app_for: String,
    alertMessage: String,
    settings: Object,
    validate: Function, // Define the prop to receive the method
  },
  setup(props) {
    // console.log(props.settings);
    const { t } = useI18n();
    const form = useForm({
      peak_zone_radius: props.settings.peak_zone_radius || 0,
      peak_zone_duration: props.settings.peak_zone_duration || 0,
      peak_zone_history_duration: props.settings.peak_zone_history_duration || 0,
      peak_zone_ride_count: props.settings.peak_zone_ride_count || 0,
      distance_price_percentage: props.settings.distance_price_percentage || 0,
    });
    const enable_peak_zone_feature = ref(props.settings.enable_peak_zone_feature == 1);


    const validationRules = {
    };
    const validationRef = ref(null);
    const errors = ref({});
    const successMessage = ref(props.successMessage || '');
    const alertMessage = ref(props.alertMessage || '');

    const dismissMessage = () => {
      successMessage.value = "";
      alertMessage.value = "";
    };
    const handleUpdate = async ()=> {
        try {
            await axios.post(`/peakzone-setting/update`,{enable_peak_zone_feature : enable_peak_zone_feature.value? 0: 1});
            enable_peak_zone_feature.value = !enable_peak_zone_feature.value;
            Swal.fire(t('success'), t('peak_zone_settings_updated_successfully'), 'success');
        } catch (error) {
            Swal.fire(t('error'), t('failed_to_update_peak_zone_settings'), 'error');
        }
    }

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
        if (props.settings) {
          response = await axios.post(`/peakzone-setting/update`, form.data());
        } else {
          alertMessage.value = t('failed_to_update_peak_zone_settings');
        }
        if (response.status === 200) {
          successMessage.value = t('peak_zone_settings_updated_successfully');
          form.reset();
          router.get('/peakzone-setting');
        } else {
          alertMessage.value = t('failed_to_update_peak_zone_settings');
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          errors.value = error.response.data.errors;
        } else {
          console.error(t('failed_to_update_peak_zone_settings'), error);
          alertMessage.value = t('failed_to_update_peak_zone_settings');
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
      enable_peak_zone_feature,
      handleUpdate,
      errors,
    };
  },
};
</script>

<template>
  <Layout>

    <Head :title="$t('peakzone-setting')" />
    <PageHeader :title="$t('peakzone-setting')" :pageTitle="$t('peakzone-setting')" />
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
          <BCardHeader class="border-0">
            <h4>{{$t("peakzone-setting")}}</h4>
          </BCardHeader>
          <BCardBody class="border border-dashed border-end-0 border-start-0">
            <form @submit.prevent="handleSubmit">
              <FormValidation :form="form" :rules="validationRules" ref="validationRef">
                <div class="row"> 
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <div class="border rounded">
                        <div class="row">
                          <div class="col">
                              <label class="form-check-label p-2 mt-2" for="enable_peak_zone_feature">{{$t("enable_peak_zone_feature")}}</label>
                          </div>
                          <div class="col">
                              <div class="form-check form-switch form-switch-md float-end p-2">
                              <input
                                  :disabled="app_for == 'demo'"
                                  type="checkbox"
                                  v-model="enable_peak_zone_feature"
                                  class="form-check-input"
                                  id="enable_peak_zone_feature"
                                  @click.prevent="handleUpdate"
                              />
                              </div>
                          </div>
                        </div>
                      </div>
                    </div> 
                  </div>
                </div>
                <div class="row" v-if="enable_peak_zone_feature">
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="peak_zone_ride_count" class="form-label">{{$t("peak_zone_ride_count")}}
                        <span class="text-danger">*</span>
                      </label>
                      <div class="input-group">
                        <input type="number" :readonly="app_for === 'demo'" class="form-control" 
                          :placeholder="$t('enter_peak_zone_ride_count')" id="peak_zone_ride_count"
                           v-model="form.peak_zone_ride_count"/>
                        <span v-for="(error, index) in errors.peak_zone_ride_count" :key="index" class="text-danger">{{ error }}</span>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="peak_zone_radius" class="form-label">{{$t("peak_zone_radius")}}
                        <span class="text-danger">*</span>
                      </label>
                    <div class="input-group">
                      <input type="number" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_peak_zone_radius')" id="peak_zone_radius" 
                     v-model="form.peak_zone_radius"
                      />
                      <span v-for="(error, index) in errors.peak_zone_ride_count" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  </div> 
                </div> 
                <div class="row" v-if="enable_peak_zone_feature">
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="peak_zone_history_duration" class="form-label">{{$t("peak_zone_history_duration")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="number" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_peak_zone_history_duration')" id="peak_zone_history_duration" 
                      v-model="form.peak_zone_history_duration"
                      />
                      <span v-for="(error, index) in errors.peak_zone_history_duration" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="peak_zone_duration" class="form-label">{{$t("peak_zone_duration")}}
                         <span class="text-danger">*</span>
                        </label>
                      <div class="input-group">
                      <input type="number" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_peak_zone_duration')" id="peak_zone_duration" 
                      v-model="form.peak_zone_duration"
                      />
                      <span v-for="(error, index) in errors.peak_zone_duration" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  </div> 
                  <div class="row" v-if="enable_peak_zone_feature">

                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="distance_price_percentage" class="form-label">{{$t("distance_price_percentage")}}
                        <span class="text-danger">*</span>
                      </label>
                    <div class="input-group">
                      <input type="number" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_distance_price_percentage')" id="distance_price_percentage" 
                     v-model="form.distance_price_percentage"
                      />
                      <span v-for="(error, index) in errors.distance_price_percentage" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  </div> 
                  </div>
                  <div class="col-lg-12">
                    <div class="text-end">
                      <button type="submit" class="btn btn-primary"> {{ settings ? $t('update') : $t('save') }}</button>
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
/* Overlay container for the second image */
.overlay-icon-container {
  position: absolute;
  top: 50%;
  left: 25%;
  transform: translate(-50%, -50%);
  width: 60px; /* Adjust size of overlay image */
  height: 60px; /* Adjust size of overlay image */
  display: flex;
  justify-content: center;
  align-items: center;
  pointer-events: none; /* Ensures the image won't interfere with clicks */
}
.rtl .custom-alert {
  max-width: 600px;
  float: left;
  top: -230px;
  right: 10px;
}
}
.cards{
  position: relative;
    top: 160px;
    left: -25px;
    z-index: 2;
    width: 250px;
    height: 123px;
    padding:10px;
    border-radius: 15px 15px 0 0 ;
}

.rtl .cards{
  position: relative;
    top: 160px;
    left: 5px;
    z-index: 2;
    width: 250px;
    height: 123px;
    padding:10px;
    border-radius: 15px 15px 0 0 ;
}
/* Container for the card with relative positioning */
.position-relative{
  position: relative;
}

/* First image styling */
.map-view-image {
  width: 500px;
  height: auto;
  display: block;
  /* Makes the first image responsive */
}

/* Overlay container for the second image */
.overlay-icon-container {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 60px; /* Adjust size of overlay image */
  height: 60px; /* Adjust size of overlay image */
  display: flex;
  justify-content: center;
  align-items: center;
  pointer-events: none; /* Ensures the image won't interfere with clicks */
}

/* Second image styling (icon) */
.icon-image {
  max-width: 80%;
  height: auto;
  object-fit: cover; /* Ensures the image retains its aspect ratio */
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .overlay-icon-container {
    width: 75px; /* Adjust size for smaller screens */
    height: 75px;
  }
}

@media (max-width: 576px) {
  .overlay-icon-container {
    width: 50px; /* Further adjust size for mobile */
    height: 50px;
  }
}
</style>
