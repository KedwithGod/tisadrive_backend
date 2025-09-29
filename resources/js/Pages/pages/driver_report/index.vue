<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import { ref, computed, watch } from "vue";
import axios from "axios";
import imageUpload from "@/Components/widgets/imageUpload.vue";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import Multiselect from "@vueform/multiselect";
import FormValidation from "@/Components/FormValidation.vue";
import '@vueform/multiselect/themes/default.css';
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
    flatPickr
  },
  props: {
    successMessage: String,
    alertMessage: String,
    validate: Function,
    vehicleTypes: Array,
  },
  setup(props) {
  const { t } = useI18n();
  const form = useForm({
    date: "",
    select_status: "",
    select_date_option: "",
    select_transport_type: "",
    file_format: '',
    vehicle_types: [], // Ensure this is an array
  });

    const vehicleTypes = ref([]);
    const validationRules = {
      select_status: { required: true },
      select_date_option: { required: true },
      file_format: { required: true }, // Validate format selection
    };
    const validationRef = ref(null);
    const errors = ref({});
    const successMessage = ref(props.successMessage || '');
    const alertMessage = ref(props.alertMessage || '');

    const dismissMessage = () => {
      successMessage.value = "";
      alertMessage.value = "";
    };
    const loading = ref(false); // Correctly define loading state

const handleTransportTypeChange = async () => {
  if (!form.select_transport_type) {
    vehicleTypes.value = []; // Clear vehicle types if no transport type selected
    return;
  }

  try {
    const response = await axios.get(`/report/getVehicleTypes`, {
      params: { transport_type: form.select_transport_type }
    });
    console.log("Vehicle Types Response:", response.data); // Log the response data
    vehicleTypes.value = response.data; // Populate with the new data
  } catch (error) {
    console.error(t('error_fetching_vehicle_types'), error);
    alertMessage.value = t('failed_to_fetch_vehicle_types');
  }
};



    watch(
      () => form.select_transport_type,
      handleTransportTypeChange
    );

const handleSubmit = async () => {
  errors.value = validationRef.value.validate(); // Trigger validation
  if (Object.keys(errors.value).length > 0) {
    return; // Exit if there are validation errors
  }
  loading.value = true;
  try {
    console.log("Form Data:", form.data()); // Log form data
    const response = await axios.post('/report/driver-report-download', form.data(), {
      responseType: 'blob',
    });

    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `driver_report.${form.file_format}`);
    document.body.appendChild(link);
    link.click();
    link.remove();

    successMessage.value = t('report_downloaded_successfully');
    form.reset();
    router.get('/report/driver-report');
  } catch (error) {
    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors;
    } else {
      console.error(t('error_downloading_report'), error);
      alertMessage.value = t('failed_to_download_report');
    }
  }
  finally {
                loading.value = false; // Set loading to false when the process ends
            }
};


    const formattedDate = computed(() => {
      if (form.date && form.date.length === 2) {
        const fromDate = new Date(form.date[0]);
        const toDate = new Date(form.date[1]);
        const fromMonth = fromDate.toLocaleString('default', { month: 'short' });
        const toMonth = toDate.toLocaleString('default', { month: 'short' });
        return `${fromDate.getDate()} ${fromMonth} - ${toDate.getDate()} ${toMonth}`;
      }
      return '';
    });

    const rangeDateconfig = {
      mode: "range",
      dateFormat: "d M, Y",
      maxDate: "today",
    };

    return {
      form,
      vehicleTypes,
      successMessage,
      alertMessage,
      handleSubmit,
      dismissMessage,
      validationRules,
      validationRef,
      errors,
      formattedDate,
      rangeDateconfig,
      loading,
    };
  }
};
</script>


<template>
  <Layout>

    <Head title="Driver Report" />
    <PageHeader :title="$t('driver_report')" :pageTitle="$t('driver_report')" />
    <BRow>
      <BCol lg="12">
        <BCard no-body id="tasksList">
          <BCardHeader class="border-0"></BCardHeader>
          <BCardBody class="border border-dashed border-end-0 border-start-0">

            <form @submit.prevent="handleSubmit">
              <FormValidation :form="form" :rules="validationRules" ref="validationRef">
                <div class="row">
                  <!-- Transport Type -->
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="select_transport_type" class="form-label">{{$t("select_transport_type")}}
                      </label>
                      <select id="select_transport_type" class="form-select" v-model="form.select_transport_type">
                        <option selected disabled value="">{{$t("select")}}</option>
                        <option value="taxi">{{$t("taxi")}}</option>
                        <option value="delivery">{{$t("delivery")}}</option>
                        <option value="both">{{$t("both")}}</option>
                      </select>
                      <span v-for="(error, index) in errors.select_transport_type" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>

                  <!-- Vehicle Type -->
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="select_vehicle_type" class="form-label">{{$t("select_vehicle_type")}}</label>
                        <Multiselect
                          id="select_vehicle_type"
                          mode="tags"
                          v-model="form.vehicle_types"
                          :close-on-select="false"
                          :searchable="true"
                         :create-option="false"
                         :options="vehicleTypes.map(type => ({ value: type.id, label: type.name }))"
                          :placeholder="$t('select_vehicle_type')"
                          :multiple="true"
                        />
                      <span v-for="(error, index) in errors.vehicle_types" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="select_status" class="form-label">{{$t("select_approval_status")}}
                      <span class="text-danger">*</span>
                      </label>
                        <select id="select_status" class="form-select" v-model="form.select_status">
                          <option selected disabled value="">{{$t("select")}}</option>
                          <option value="1">{{$t("approved")}}</option>
                          <option value="0">{{$t("disapproved")}}</option>
                        </select>
                      <span v-for="(error, index) in errors.select_status" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                   <!-- Date Option Select -->
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="select_date_option" class="form-label">{{$t("date_option")}}
                        <span class="text-danger">*</span>
                      </label>
                      <select id="select_date_option" class="form-select" v-model="form.select_date_option">
                        <option disabled value="">{{$t("select")}}</option>
                        <option value="today">{{$t("today")}}</option>
                        <option value="this_week">{{$t("this_week")}}</option>
                        <option value="this_month">{{$t("this_month")}}</option>
                        <option value="this_year">{{$t("this_year")}}</option>
                        <option value="date">{{$t("date")}}</option>
                      </select>
                      <span v-for="(error, index) in errors.select_date_option" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>

                  <!-- Date Range Picker -->
                  <div class="col-sm-6" v-if="form.select_date_option === 'date'">
                    <div class="mb-4">
                      <label for="datepicker-range" class="form-label">{{$t("from_to_date")}}</label>
                      <flat-pickr 
                        :placeholder="$t('select_date')" 
                        v-model="form.date" 
                        :config="rangeDateconfig" 
                        class="form-control flatpickr-input" 
                        id="datepicker-range">
                      </flat-pickr>
                      <span v-for="(error, index) in errors.date" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>

                  <!-- File Format Select -->
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="file_format" class="form-label">{{$t("file_format")}}
                      <span class="text-danger">*</span>
                      </label>
                      <select id="file_format" class="form-select" v-model="form.file_format">
                        <option disabled selected value="">{{$t("select_file_format")}}</option>
                        <option value="xlsx">{{$t("excel")}}</option>
                        <!-- <option value="pdf">PDF (.pdf)</option> -->
                        <option value="csv">{{$t("csv")}}</option>
                      </select>
                      <span v-for="(error, index) in errors.file_format" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>

                  <div class="col-lg-12">
                    <div class="text-end">
                      <button type="submit" class="btn btn-primary" :disabled="loading">{{$t("download")}}</button>
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
