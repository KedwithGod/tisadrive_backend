<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import { ref, computed, onMounted} from "vue";
import axios from "axios";
import Multiselect from "@vueform/multiselect";
import FormValidation from "@/Components/FormValidation.vue";
import imageUpload from "@/Components/widgets/imageUpload.vue";
import ImageUp from "@/Components/ImageUp.vue";
import CKEditor from "@ckeditor/ckeditor5-vue";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
import { useSharedState } from '@/composables/useSharedState';
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
    ImageUp,
    ckeditor: CKEditor.component,
  },
  data() {
    return {
      editor: ClassicEditor,
      editorData: "",
    };
  },
  props: {
    successMessage: String,
    alertMessage: String,
    landingQuickLink: Object,
    app_for: String,
    validate: Function,
    languages: {
      type: Array,
      required: true
    },
  },
  setup(props) {
    const { t } = useI18n();
    const { languages } = useSharedState();
    const storedLanguages = ref([]);
    const form = useForm({
      privacy_title: props.landingQuickLink ? props.landingQuickLink.privacy_title || "" : "" ,
      privacy: props.landingQuickLink ? props.landingQuickLink.privacy || "" : "" ,
      terms_title: props.landingQuickLink ? props.landingQuickLink.terms_title || "" : "" ,
      terms: props.landingQuickLink ? props.landingQuickLink.terms || "" : "" ,
      compliance_title: props.landingQuickLink ? props.landingQuickLink.compliance_title || "" : "" ,
      compliance: props.landingQuickLink ? props.landingQuickLink.compliance || "" : "" ,
      dmv_title: props.landingQuickLink ? props.landingQuickLink.dmv_title || "" : "" ,
      dmv: props.landingQuickLink ? props.landingQuickLink.dmv || "" : "" ,
      locale: props.landingQuickLink ? props.landingQuickLink.locale || "" : "",
      language: props.landingQuickLink ? props.landingQuickLink.language || "" : "",
    });


    const validationRules = {
      privacy_title: { required: true } ,
      privacy: { required: true },
      terms_title: { required: true },
      terms: { required: true },
      compliance_title: { required: true },
      compliance: { required: true },
      dmv_title: { required: true },
      dmv: { required: true }, 
      locale: { required: true }, 
      language: { required: true }, 
      
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

    const validationRef = ref(null);
    const errors = ref({});
    const successMessage = ref(props.successMessage || '');
    const alertMessage = ref(props.alertMessage || '');

    const dismissMessage = () => {
      successMessage.value = "";
      alertMessage.value = "";
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
            formData.append('icon', form[key]);
          } else {
            formData.append(key, form[key]);
          }
        }

        let response;
        if (props.landingQuickLink && props.landingQuickLink.id) {
          response = await axios.post(`/landing-quicklink/update/${props.landingQuickLink.id}`, formData, {
            headers: {
              'Content-Type': 'multipart/form-data',
            },
          });

        } else {
          response = await axios.post('/landing-quicklink/store', formData, {
            headers: {
              'Content-Type': 'multipart/form-data',
            },
          });
        }

        if (response.status === 201) {
          successMessage.value = t('landing_quickLinks_created_successfully');
          form.reset();
          router.get('/landing-quicklink');
        } else {
          alertMessage.value = t('failed_to_create_landing_quickLinks');
        }
      } catch (error) {
        console.error(t('error_creating_landing_quickLinks'), error);
        alertMessage.value = t('failed_to_create_landing_quickLinks_catch');
        if (error.response && error.response.data.errors) {
          errors.value = error.response.data.errors;
        }
      }
    };
    onMounted(async () => {
      if (Object.keys(languages).length == 0) {
        await fetchData();
      }
      fetchStoredLanguages();
    });


    const fetchStoredLanguages = async () => {     
      try {
        const response = await axios.get('/landing-quicklink/list');
        storedLanguages.value = response.data.results.map(lang => ({language: lang.language }) );

        const commonLanguages = storedLanguages.value.filter(storedLang =>
            languages.value.some(lang => lang.label === storedLang.language)
        );

          languages.value = languages.value.map(lang => ({
          ...lang,  // All existing properties of lang
          disabled: commonLanguages.some(commonLang => commonLang.language === lang.label),
        }));
      } catch (error) {
        console.error(t('error_fetching_stored_languages'), error);
      }
    };
      // Construct the full URL for the vehicle type icon
      const iconUrl = props.vehicleType ? props.vehicleType.icon :null;

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

    const selectlanguages = async () =>{
      const selectedLanguage = languages.value.find(
        (lang) => lang.label === form.language
      );
        form.language = selectedLanguage.label;
        form.locale = selectedLanguage.code;
        form.direction = selectedLanguage.direction;
    }

    return {
      form,
      successMessage,
      alertMessage,
      handleSubmit,
      dismissMessage,
      validationRules,
      validationRef,
      errors,
      validateIconSize,
      landingHeader: props.landingHeader,
      iconUrl,
      handleImageSelected,
      handleImageRemoved,
      languages,
      selectlanguages,
    };
  }
};
</script>

<template>
  <Layout>
    <Head title="Manage LandingSite-QuickLinks" />
    <PageHeader :title="landingQuickLink ? $t('edit') : $t('create')" :pageTitle="$t('landing_quicklinks')"  pageLink="/landing-quicklink"/>
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
            <div>
              <div v-if="successMessage" class="custom-alert alert alert-success alert-border-left fade show" role="alert" id="alertMsg">
                <div class="alert-content">
                  <i class="ri-notification-off-line me-3 align-middle"></i>
                  <strong>Success</strong> - {{ successMessage }}
                  <button type="button" class="btn-close btn-close-success" @click="dismissMessage" aria-label="Close Success Message"></button>
                </div>
              </div>

              <div v-if="alertMessage" class="custom-alert alert alert-danger alert-border-left fade show" role="alert" id="alertMsg">
                <div class="alert-content">
                  <i class="ri-notification-off-line me-3 align-middle"></i>
                  <strong>Alert</strong> - {{ alertMessage }}
                  <button type="button" class="btn-close btn-close-danger" @click="dismissMessage" aria-label="Close Alert Message"></button>
                </div>
              </div>
            </div>
          </BCardHeader>
          <BCardBody class="border border-dashed border-end-0 border-start-0">
            <form @submit.prevent="handleSubmit">
              <FormValidation :form="form" :rules="validationRules" ref="validationRef">
                <div class="row">
                  <div class="col-6 mt-3">
                    <div class="mb-3">
                      <label for="language" class="form-label">{{$t("languages")}}
                        <span class="text-danger">*</span>
                      </label>
                      <select :disabled="app_for === 'demo'" id="language" class="form-select" v-model="form.language" @change="selectlanguages">
                        <option disabled value="">{{$t("choose_languages")}}</option>
                        <option v-for="languages in languages" :key="languages.id" :disabled="languages.disabled">
                        {{ languages.label }}
                      </option>
                      </select>
                      <span v-for="(error, index) in errors.language" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="card-header alert alert-success">
                      <h4 class="card-title mb-3">{{$t("privacy_policy_section")}}</h4>
                  </div>
                  <div class="col-12">
                    <div class="mb-3">
                      <label for="privacy_title" class="form-label">{{$t("privacy_policy_title")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input :readonly="app_for === 'demo'" type="text" class="form-control" :placeholder="$t('enter_privacy_title')" id="privacy_title" v-model="form.privacy_title" />
                      <span v-for="(error, index) in errors.privacy_title" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-12 mt-3">
                    <div class="mb-3">
                      <label for="privacy" class="form-label">{{$t("privacy_policy")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ckeditor :disabled="app_for === 'demo'" v-model="form.privacy"  :editor="editor"></ckeditor>
                      <span v-for="(error, index) in errors.privacy" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>

                  <div class="card-header alert alert-success">
                      <h4 class="card-title mb-3">{{$t("terms_and_conditions_section")}}</h4>
                  </div>
                  <div class="col-12">
                    <div class="mb-3">
                      <label for="terms_title" class="form-label">{{$t("terms_and_conditions_title")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input :readonly="app_for === 'demo'" type="text" class="form-control" :placeholder="$t('enter_terms_title')" id="terms_title" v-model="form.terms_title" />
                      <span v-for="(error, index) in errors.terms_title" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-12 mt-3">
                    <div class="mb-3">
                      <label for="terms" class="form-label">{{$t("terms_and_conditions")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ckeditor :disabled="app_for === 'demo'" v-model="form.terms"  :editor="editor"></ckeditor>
                      <span v-for="(error, index) in errors.terms" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>

                  <div class="card-header alert alert-success">
                      <h4 class="card-title mb-3">{{$t("compliance_section")}}</h4>
                  </div>
                  <div class="col-12">
                    <div class="mb-3">
                      <label for="compliance_title" class="form-label">{{$t("compliance_title")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input :readonly="app_for === 'demo'" type="text" class="form-control" :placeholder="$t('enter_compliance_title')" id="compliance_title" v-model="form.compliance_title" />
                      <span v-for="(error, index) in errors.compliance_title" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-12 mt-3">
                    <div class="mb-3">
                      <label for="compliance" class="form-label">{{$t("compliance")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ckeditor :disabled="app_for === 'demo'" v-model="form.compliance"  :editor="editor"></ckeditor>
                      <span v-for="(error, index) in errors.compliance" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>

                  <div class="card-header alert alert-success">
                      <h4 class="card-title mb-3">{{$t("dmv_section")}}</h4>
                  </div>
                  <div class="col-12">
                    <div class="mb-3">
                      <label for="dmv_title" class="form-label">{{$t("dmv_title")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input :readonly="app_for === 'demo'" type="text" class="form-control" :placeholder="$t('enter_dmv_title')" id="dmv_title" v-model="form.dmv_title" />
                      <span v-for="(error, index) in errors.dmv_title" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-12 mt-3">
                    <div class="mb-3">
                      <label for="dmv" class="form-label">{{$t("dmv")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ckeditor :disabled="app_for === 'demo'" v-model="form.dmv"  :editor="editor"></ckeditor>
                      <span v-for="(error, index) in errors.dmv" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  
                  <div class="col-lg-12">
                    <div class="text-end">
                      <button type="submit" class="btn btn-primary">{{ landingQuickLink ? 'Update' : 'Create' }}</button>
                    </div>
                  </div>
                </div>
              </FormValidation>
            </form>
          </BCardBody>
        </BCard>
      </BCol>
    </BRow>
  </Layout>
</template>
