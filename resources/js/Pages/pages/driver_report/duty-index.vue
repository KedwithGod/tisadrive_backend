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
    serviceLocations: Array,
  },
  setup(props) {
    const { t } = useI18n();
    const form = useForm({
      date: "",
      select_date_option: "",
      service_location_id: "",
      driver_id: "", 
      file_format: '',
    });

    const drivers = ref([]);
    const validationRules = {
      driver_id: { required: true },
      select_date_option: { required: true },
      file_format: { required: true },
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

    const handleServiceLocationChange = async () => {
      if (!form.service_location_id) {
        drivers.value = [];
        return;
      }

      try {
        const response = await axios.get('/report/getDrivers', {
          params: { service_location_id: form.service_location_id }
        });

        if (response.data && response.status === 200) {
          if (!response.data.drivers.length) {
            alertMessage.value = t('no_drivers_found');
          }
          drivers.value = response.data.drivers.length > 0 ? response.data.drivers : [];
        } else {
          drivers.value = [];
          alertMessage.value = t('no_drivers_found');
        }
      } catch (error) {
        console.error(t('error_fetching_drivers'), error);
        alertMessage.value = t('failed_to_fetch_drivers');
      }
    };

    const fromDate = ref('');
    const toDate = ref('');

    // Watcher to update date range based on selected date option
    watch(() => form.select_date_option, (newOption) => {
      const today = new Date();
      let startDate, endDate;

      switch (newOption) {
        case 'today':
          startDate = endDate = today;
          break;
        case 'this_week':
          startDate = new Date(today.setDate(today.getDate() - today.getDay()));
          endDate = new Date(today.setDate(today.getDate() + 6 - today.getDay()));
          break;
        case 'this_month':
          startDate = new Date(today.getFullYear(), today.getMonth(), 1);
          endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
          break;
        case 'this_year':
          startDate = new Date(today.getFullYear(), 0, 1);
          endDate = new Date(today.getFullYear(), 11, 31);
          break;
        case 'date':
          startDate = endDate = null; // Date picker will be used
          break;
        default:
          startDate = endDate = null;
      }

      fromDate.value = startDate ? startDate.toISOString().split('T')[0] : '';
      toDate.value = endDate ? endDate.toISOString().split('T')[0] : '';
    });

//handle submit function
    const handleSubmit = async () => {
      errors.value = validationRef.value.validate();
      
      // Check if select_date_option is 'date'
      if (form.select_date_option === 'date') {
        console.log("date update");
        console.log(form.date); // e.g., "01 Oct, 2024 to 07 Oct, 2024"

        // Split the string into start and end dates
        const dateRange = form.date.split(" to ");
        
        // Check if we got two dates
        if (dateRange.length === 2) {
          const fromDateString = dateRange[0].trim(); // First date
          const toDateString = dateRange[1].trim();   // Second date

          // Convert to Date objects
          const fromDateObj = new Date(fromDateString);
          const toDateObj = new Date(toDateString);

          // Format as Y-m-d
          fromDate.value = fromDateObj.toISOString().split('T')[0]; // Convert to Y-m-d
          toDate.value = toDateObj.toISOString().split('T')[0];     // Convert to Y-m-d

          console.log('Formatted fromDate:', fromDate.value);
          console.log('Formatted toDate:', toDate.value);
        } else {
          console.error('Invalid date range format');
          return; // Exit if the format is invalid
        }
      }

      // Check for validation errors after processing dates
      if (Object.keys(errors.value).length > 0) {
        return;
      }
      loading.value = true;
      try {
        const params = {
          driver_id: form.driver_id,
          from_date: fromDate.value, // Use the formatted from date
          to_date: toDate.value,     // Use the formatted to date
          file_format: form.file_format,
        };
        console.log('API params:', params); // Log the parameters for debugging
        const response = await axios.post('/report/driver-duty-report-download', params, {
          responseType: 'blob',
        });

        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `driver_duty_report.${form.file_format}`);
        document.body.appendChild(link);
        link.click();
        link.remove();

        successMessage.value = t('report_downloaded_successfully');
        form.reset();
        router.get('/report/driver-duty-report');
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
    mode: "range", // Ensure this is set for range selection
    dateFormat: "Y-m-d", // Change this if you need a different format, e.g., "d M, Y"
    maxDate: "today",
    parseDate: (dateString) => new Date(dateString), // Ensures dates are parsed as Date objects
  };


    return {
      form,
      drivers,
      successMessage,
      alertMessage,
      handleSubmit,
      dismissMessage,
      validationRules,
      validationRef,
      errors,
      formattedDate,
      rangeDateconfig,
      handleServiceLocationChange,
      fromDate,
      toDate,
      loading,
    };
  }
};
</script>



<template>
  <Layout>

    <Head title="Driver Duties Report" />
    <PageHeader :title="$t('driver_duty_report')" :pageTitle="$t('driver_duty_report')" />
    <BRow>
      <BCol lg="12">
        <BCard no-body id="tasksList">
          <BCardHeader class="border-0"></BCardHeader>
          <BCardBody class="border border-dashed border-end-0 border-start-0">

            <form @submit.prevent="handleSubmit">
              <FormValidation :form="form" :rules="validationRules" ref="validationRef">
                <div class="row">
                  <!-- Service Location -->
                  <div class="col-sm-6">
                    <label for="service_location_id" class="form-label">{{ $t('service_location') }}
                    <span class="text-danger">*</span>
                    </label>
                      <select v-model="form.service_location_id" class="form-select" @change="handleServiceLocationChange">
                        <option value="">{{ $t('select') }}</option>
                        <option v-for="location in serviceLocations" :key="location.id" :value="location.id">
                          {{ location.name }}
                        </option>
                      </select>
                    <span v-for="error in errors.service_location_id" class="text-danger">{{ error }}</span>
                  </div>

                  <!-- Driver -->
                  <div class="col-sm-6">
                    <label for="driver_id" class="form-label">{{ $t('driver') }}
                    <span class="text-danger">*</span>
                    </label>

                    <Multiselect
                        id="driver_id"
                        v-model="form.driver_id"
                        :options="drivers.map(driver => ({ value: driver.id, label: driver.name }))"
                        :close-on-select="true" 
                        :searchable="true"
                        :placeholder="$t('select')"
                        :create-option="false">
                    </Multiselect>
                    <span v-for="error in errors.driver_id" class="text-danger">{{ error }}</span>
                  </div>

                   <!-- Date Option Select -->
                  <div class="col-sm-6 mt-4">
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
                  <div class="col-sm-6 mt-4">
                    <div class="mb-3">
                      <label for="file_format" class="form-label">{{$t("file_format")}}</label>
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
