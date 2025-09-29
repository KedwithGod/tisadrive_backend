<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import { ref, onMounted, watch } from "vue";
import axios from "axios";
import Multiselect from "@vueform/multiselect";
import FormValidation from "@/Components/FormValidation.vue";
import { useSharedState } from '@/composables/useSharedState'; // Import the composable
import { useI18n } from 'vue-i18n';

export default {
  components: {
    Layout,
    PageHeader,
    Head,
    Pagination,
    Multiselect,
    FormValidation
  },
  props: {
    successMessage: String,
    alertMessage: String,
    activeTab: String,
    countries: Array,
    timeZones: Array,
    languages: Array,
    serviceLocation: Object,
    validate: Function, // Define the prop to receive the method
    app_for: String,
  },
  setup(props) {
    const { t } = useI18n();
    const { languages, fetchData } = useSharedState(); // Destructure the shared state
    const activeTab = ref('English');
    const form = useForm({
      country: props.serviceLocation ? props.serviceLocation.country || "" : "",
      currency_code: props.serviceLocation ? props.serviceLocation.currency_code || "" : "",
      currency_symbol: props.serviceLocation ? props.serviceLocation.currency_symbol || "" : "",
      currency_pointer: "ltr",
      timezone: props.serviceLocation ? props.serviceLocation.timezone || "" : "",
      currency_name: props.serviceLocation ? props.serviceLocation.currency_name || "" : "",
      languageFields:  props.serviceLocation ? props.serviceLocation.languageFields || {} : {} // To hold data from the Tab component
    });
    const validationRules = {
      country: { required: true },
      currency_code: { required: true },
      currency_symbol: { required: true },
      timezone: { required: true },
    };
    const validationRef = ref(null);
    const errors = ref({});
    const successMessage = ref(props.successMessage || '');
    const alertMessage = ref(props.alertMessage || '');

    const dismissMessage = () => {
      successMessage.value = "";
      alertMessage.value = "";
    };

    const setActiveTab = (tab) => {
      activeTab.value = tab;
    };

    // Method to handle the change of country selection
    const onCountryChange = (countryId) => {
      const selectedCountry = props.countries.find(country => country.id === countryId);
      if (selectedCountry) {
        form.currency_code = selectedCountry.currency_code;
        form.currency_symbol = selectedCountry.currency_symbol;
        form.currency_name = selectedCountry.currency_name;
      }
    };

    // Watch the country field for changes
    watch(() => form.country, (newCountry) => {
      onCountryChange(newCountry);
    });

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
        if (props.serviceLocation && props.serviceLocation.id) {
          response = await axios.post(`/service-locations/update/${props.serviceLocation.id}`, form.data());
        } else {
          response = await axios.post('store', form.data());
        }
        if (response.status === 201) {
          successMessage.value = t('service_location_created_successfully');
          form.reset();
          router.get('/service-locations');
        } else {
          alertMessage.value = t('failed_to_create_service_location');
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          errors.value = error.response.data.errors;
        } else if (error.response && error.response.status === 403) {
          alertMessage.value = error.response.data.alertMessage;
          setTimeout(()=>{
            router.get('/service-locations');
          },5000)
        } else {
          console.error(t('error_creating_service_location'), error);
          alertMessage.value = t('failed_to_create_service_location_catch');
        }
      }
    };

    onMounted(async () => {
      if (Object.keys(languages).length == 0) {
        await fetchData();
      }
    });

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
      languages,
      setActiveTab,
      activeTab,
      onCountryChange, // Expose the method to the template
    };
  }
};

</script>

<template>
  <Layout>

    <Head title="Service Locations" />
    <PageHeader :title="serviceLocation ? $t('edit') : $t('create')" :pageTitle="$t('service_location')" pageLink="/service-locations"/>
    <BRow>
      <BCol lg="12">
        <BCard no-body id="tasksList">
          <BCardHeader class="border-0">

          </BCardHeader>
          <BCardBody class="border border-dashed border-end-0 border-start-0">

            <form @submit.prevent="handleSubmit">
              <FormValidation :form="form" :rules="validationRules" ref="validationRef">
                <BRow>
                  <BCol lg="12">
                    <div>
                      <ul class="nav nav-tabs nav-tabs-custom nav-success nav-justified" role="tablist">
                        <BRow v-for="language in languages" :key="language.code">
                          <BCol lg="12">
                            <li class="nav-item" role="presentation">
                              <a class="nav-link" @click="setActiveTab(language.label)"
                                :class="{ active: activeTab === language.label }" role="tab" aria-selected="true">
                                {{ language.label }}
                              </a>
                            </li>
                          </BCol>
                        </BRow>
                      </ul>
                      <div class="tab-content text-muted" v-for="language in languages" :key="language.code">
                        <div v-if="activeTab === language.label" class="tab-pane active show" :id="`${language.label}`"
                          role="tabpanel">
                          <div class="col-sm-6 mt-3">
                            <div class="mb-3">
                              <label :for="`name-${language.code}`" class="form-label">{{ $t("name") }}
                                <span class="text-danger">*</span>
                              </label>
                              <input type="text" class="form-control" :placeholder="$t('enter_name_in', {language: `${language.label}`})"
                                :id="`name-${language.code}`" v-model="form.languageFields[language.code]"
                                :required="language.code === 'en'">
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </BCol>
                </BRow>
               <div class="row">
                <div class="col-sm-6">
                  <div class="mb-3">
                    <label for="select_country" class="form-label">{{ $t("select_country") }}
                      <span class="text-danger">*</span>
                    </label>
                    <select id="select_country" class="form-select" v-model="form.country">
                      <option disabled value="">{{ $t("choose_country") }}</option>
                      <option v-for="country in countries" :key="country.id" :value="country.id">
                        {{ country.name }}
                      </option>
                    </select>
                    <span v-for="(error, index) in errors.country" :key="index" class="text-danger">{{ error }}</span>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="mb-3">
                    <label for="currency_code" class="form-label">{{ $t("currency_code") }}
                      <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" :placeholder="$t('enter_currency_code')" id="currency_code"
                      v-model="form.currency_code" />
                    <span v-for="(error, index) in errors.currency_code" :key="index" class="text-danger">{{ error }}</span>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="mb-3">
                    <label for="currency_symbol" class="form-label">{{ $t("currency_symbol") }}
                      <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" :placeholder="$t('enter_currency_symbol')" id="currency_symbol"
                      v-model="form.currency_symbol" />
                    <span v-for="(error, index) in errors.currency_symbol" :key="index" class="text-danger">{{ error }}</span>
                  </div>
                </div>                        
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="select_timezone" class="form-label">{{ $t("select_timezone") }}
                        <span class="text-danger">*</span>
                      </label>
                        <Multiselect
                            id="select_timezone"
                            v-model="form.timezone"
                            :options="timeZones.map(timezone => ({ value: timezone.timezone, label: timezone.timezone }))"
                            :close-on-select="true" 
                            :searchable="true"
                            :placeholder="$t('choose_timezone')"
                            :create-option="false">
                        </Multiselect>
                      <span v-for="(error, index) in errors.timezone" :key="index" class="text-danger">{{ error
                        }}</span>
                    </div>
                  </div>
                <div class="col-sm-6">
                  <div class="mb-3">
                    <input type="hidden" class="form-control" id="currency_name" name="currency_name"
                      v-model="form.currency_name" />
                  </div>
                </div>  
                  <div class="col-lg-12">
                    <div class="text-end">
                      <button type="submit" class="btn btn-primary" :disabled="app_for === 'demo'"> {{ serviceLocation ? $t('update') : $t('save')}}</button>
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

.nav-tabs .nav-link {
  color: #000;
  border: 1px solid transparent;
  transition: background-color 0.3s ease;
}

.nav-tabs .nav-link:hover {
  background-color: #e9ecef;
}

.nav-tabs .nav-link.active {
  color: #28a745;
  border-bottom: 2px solid #28a745;
}
</style>
