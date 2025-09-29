<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import { ref, onMounted } from "vue";
import axios from "axios";
import imageUpload from "@/Components/widgets/imageUpload.vue";
import { useSharedState } from '@/composables/useSharedState'; // Import the composable
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
    languages: Array,
    timeZones: Array,
    complaintTitle: Object,
    validate: Function, // Define the prop to receive the method
  },
  setup(props) {
    const { t } = useI18n();
    const { languages, fetchData } = useSharedState(); // Destructure the shared state
    const activeTab = ref('English');
    const form = useForm({
      user_type: props.complaintTitle ? props.complaintTitle.user_type || "" : "",
      languageFields:  props.complaintTitle ? props.complaintTitle.languageFields || {} : {}, // To hold data from the Tab component
      // title: props.complaintTitle ? props.complaintTitle.title || "" : "",
      complaint_type: props.complaintTitle ? props.complaintTitle.complaint_type || "" : "",
    });
    const validationRules = {
      user_type: { required: true },
      complaint_type: { required: true },
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
        if (props.complaintTitle && props.complaintTitle.id) {
          response = await axios.post(`/complaint-title/update/${props.complaintTitle.id}`, form.data());
        } else {
          response = await axios.post('store', form.data());
        }
        if (response.status === 201) {
          successMessage.value = t('complaint_title_created_successfully');
          form.reset();
          router.get('/complaint-title');
        } else {
          alertMessage.value = t('failed_to_create_complaint_title');
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          errors.value = error.response.data.errors;
        } else {
          console.error(t('error_creating_complaint_titles'), error);
          alertMessage.value = t('failed_to_create_complaint_title_Catch');
        }
      }

    };


    const setActiveTab = (tab) => {
      activeTab.value = tab;
    }
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
      setActiveTab,
      activeTab,
      languages,
      errors
    };
  }
};
</script>

<template>
  <Layout>

    <Head title="Complaint Title" />
    <PageHeader :title="complaintTitle ? $t('edit') : $t('create')" :pageTitle="$t('complaint_title')" pageLink="/complaint-title"/>
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
                      <label for="user_type" class="form-label">{{$t("user_type")}}
                        <span class="text-danger">*</span>
                      </label>
                      <select id="select__user_type" class="form-select" v-model="form.user_type">
                        <option disabled value="">{{$t("select")}}</option>
                        <option  value="user">{{$t("user")}}</option>
                        <option  value="driver">{{$t("driver")}}</option>
                        <option  value="owner">{{$t("owner")}}</option>
                      </select>
                      <span v-for="(error, index) in errors.user_type" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="select_complaint_title_type" class="form-label">{{$t("complaint_title_type")}}
                        <span class="text-danger">*</span>
                      </label>
                      <select id="select_complaint_title_type" class="form-select" v-model="form.complaint_type">
                        <option disabled value="">{{$t("select")}}</option>
                        <option  value="request_help">{{$t("request_help")}}</option>
                        <option  value="general">{{$t("general")}}</option>
                      </select>
                      <span v-for="(error, index) in errors.complaint_type" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
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
                          <label :for="`name-${language.code}`" class="form-label">{{$t("name")}}
                            <span class="text-danger">*</span>
                          </label>
                          <input type="text" class="form-control" :placeholder="$t('enter_name_in', {language: `${language.label}`})"
                            :id="`name-${language.code}`" v-model="form.languageFields[language.code]"
                            :required="language.code === 'en'">
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-12">
                    <div class="text-end">
                      <button type="submit" class="btn btn-primary"> {{ complaintTitle ? $t('update') : $t('save') }}</button>
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
