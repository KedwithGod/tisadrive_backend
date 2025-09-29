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
    landingContact: Object,
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
      hero_title: props.landingContact ? props.landingContact.hero_title || "" : "" ,
      contact_heading: props.landingContact ? props.landingContact.contact_heading || "" : "" ,
      contact_para: props.landingContact ? props.landingContact.contact_para || "" : "" ,
      contact_address_title: props.landingContact ? props.landingContact.contact_address_title || "" : "" ,
      contact_address: props.landingContact ? props.landingContact.contact_address || "" : "" ,
      contact_phone_title: props.landingContact ? props.landingContact.contact_phone_title || "" : "" ,
      contact_phone: props.landingContact ? props.landingContact.contact_phone || "" : "" ,
      contact_mail_title: props.landingContact ? props.landingContact.contact_mail_title || "" : "" ,
      contact_mail: props.landingContact ? props.landingContact.contact_mail || "" : "" ,
      contact_web_title: props.landingContact ? props.landingContact.contact_web_title || "" : "" ,
      contact_web: props.landingContact ? props.landingContact.contact_web || "" : "" ,
      form_name: props.landingContact ? props.landingContact.form_name || "" : "" ,
      form_mail: props.landingContact ? props.landingContact.form_mail || "" : "" ,
      form_subject: props.landingContact ? props.landingContact.form_subject || "" : "" ,
      form_message: props.landingContact ? props.landingContact.form_message || "" : "" ,
      form_btn: props.landingContact ? props.landingContact.form_btn || "" : "" ,
      locale: props.landingContact ? props.landingContact.locale || "" : "",
      language: props.landingContact ? props.landingContact.language || "" : "",
    });


    const validationRules = {
      hero_title: { required: true } ,
      contact_heading: { required: true } ,
      contact_para: { required: true } ,
      contact_address_title: { required: true } ,
      contact_address: { required: true } ,
      contact_phone_title: { required: true } ,
      contact_phone: { required: true } ,
      contact_mail_title: { required: true } ,
      contact_mail: { required: true } ,
      contact_web_title: { required: true } ,
      contact_web: { required: true } ,
      form_name: { required: true } ,
      form_mail: { required: true } ,
      form_subject: { required: true } ,
      form_message: { required: true } ,
      form_btn: { required: true } ,
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
        if (props.landingContact && props.landingContact.id) {
          response = await axios.post(`/landing-contact/update/${props.landingContact.id}`, formData, {
            headers: {
              'Content-Type': 'multipart/form-data',
            },
          });

        } else {
          response = await axios.post('/landing-contact/store', formData, {
            headers: {
              'Content-Type': 'multipart/form-data',
            },
          });
        }

        if (response.status === 201) {
          successMessage.value = t('contact_created_successfully');
          form.reset();
          router.get('/landing-contact');
        } else {
          alertMessage.value = t('failed_to_create_contact');
        }
      } catch (error) {
        console.error(t('error_creating_contact'), error);
        alertMessage.value = t('failed_to_create_contact_catch');
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
        const response = await axios.get('/landing-contact/list');
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
    <Head title="Manage LandingSite-Contact" />
    <PageHeader :title="landingContact ? $t('edit') : $t('create')" :pageTitle="$t('landing_contact')" pageLink="/landing-contact" />
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
                      <select :disabled="app_for === 'demo'" id="language" class="form-select" v-model="form.language"  @change="selectlanguages">
                        <option disabled value="">{{$t("choose_languages")}}</option>
                        <option v-for="languages in languages" :key="languages.id" :disabled="languages.disabled">
                        {{ languages.label }}
                      </option>
                      </select>
                      <span v-for="(error, index) in errors.language" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="card-header alert alert-success">
                      <h4 class="card-title mb-3">{{$t("hero_section")}}</h4>
                  </div>
                  <div class="col-6">
                    <div class="mb-3">
                      <label for="hero_title" class="form-label">{{$t("hero_title")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input :readonly="app_for === 'demo'" type="text" class="form-control" :placeholder="$t('enter_hero_title')" id="hero_title" v-model="form.hero_title" />
                      <span v-for="(error, index) in errors.hero_title" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="mb-3">
                      <label for="contact_heading" class="form-label">{{$t("contact_heading")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input :readonly="app_for === 'demo'" type="text" class="form-control" :placeholder="$t('enter_contact_heading')" id="contact_heading" v-model="form.contact_heading" />
                      <span v-for="(error, index) in errors.contact_heading" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-12 mt-3">
                    <div class="mb-3">
                      <label for="contact_para" class="form-label">{{$t("contact_para")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ckeditor :disabled="app_for === 'demo'" v-model="form.contact_para"  :editor="editor"></ckeditor>
                      <span v-for="(error, index) in errors.contact_para" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>

                  <div class="card-header alert alert-success">
                      <h4 class="card-title mb-3">{{$t("address_section")}}</h4>
                  </div>
                  <div class="col-6">
                    <div class="mb-3">
                      <label for="contact_address_title" class="form-label">{{$t("address_title")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input :readonly="app_for === 'demo'" type="text" class="form-control" :placeholder="$t('enter_address_title')" id="contact_address_title" v-model="form.contact_address_title" />
                      <span v-for="(error, index) in errors.contact_address_title" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="mb-3">
                      <label for="contact_address" class="form-label">{{$t("contact_address")}}
                        <span class="text-danger">*</span>
                      </label>
                      <ckeditor :disabled="app_for === 'demo'" v-model="form.contact_address"  :editor="editor"></ckeditor>
                      <span v-for="(error, index) in errors.contact_address" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="mb-3">
                      <label for="contact_phone_title" class="form-label">{{$t("phone_title")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input :readonly="app_for === 'demo'" type="text" class="form-control" :placeholder="$t('enter_phone_title')" id="contact_phone_title" v-model="form.contact_phone_title" />
                      <span v-for="(error, index) in errors.contact_phone_title" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="mb-3">
                      <label for="contact_phone" class="form-label">{{$t("phone_number")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input :readonly="app_for === 'demo'" type="text" class="form-control" :placeholder="$t('enter_phone')" id="contact_phone" v-model="form.contact_phone" />
                      <span v-for="(error, index) in errors.contact_phone" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="mb-3">
                      <label for="contact_mail_title" class="form-label">{{$t("mail_title")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input :readonly="app_for === 'demo'" type="text" class="form-control" :placeholder="$t('enter_mail')" id="contact_mail_title" v-model="form.contact_mail_title" />
                      <span v-for="(error, index) in errors.contact_mail_title" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="mb-3">
                      <label for="contact_mail" class="form-label">{{$t("mail")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input :readonly="app_for === 'demo'" type="text" class="form-control" :placeholder="$t('enter_mail')" id="contact_mail" v-model="form.contact_mail" />
                      <span v-for="(error, index) in errors.contact_mail" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="mb-3">
                      <label for="contact_web_title" class="form-label">{{$t("web_title")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input :readonly="app_for === 'demo'" type="text" class="form-control" :placeholder="$t('enter_web_title')" id="contact_web_title" v-model="form.contact_web_title" />
                      <span v-for="(error, index) in errors.contact_web_title" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="mb-3">
                      <label for="contact_web" class="form-label">{{$t("web")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input :readonly="app_for === 'demo'" type="url" class="form-control" :placeholder="$t('enter_web_address')" id="contact_web" v-model="form.contact_web" />
                      <span v-for="(error, index) in errors.contact_web" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>

                  <div class="card-header alert alert-success">
                      <h4 class="card-title mb-3">{{$t("form_section")}}</h4>
                  </div>
                  <div class="col-6">
                    <div class="mb-3">
                      <label for="form_name" class="form-label">{{$t("form_name_title")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input :readonly="app_for === 'demo'" type="text" class="form-control" :placeholder="$t('enter_name')" id="form_name" v-model="form.form_name" />
                      <span v-for="(error, index) in errors.form_name" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="mb-3">
                      <label for="form_mail" class="form-label">{{$t("form_mail_title")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input :readonly="app_for === 'demo'" type="text" class="form-control" :placeholder="$t('enter_mail')" id="form_mail" v-model="form.form_mail" />
                      <span v-for="(error, index) in errors.form_mail" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="mb-3">
                      <label for="form_subject" class="form-label">{{$t("form_subject_title")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input :readonly="app_for === 'demo'" type="text" class="form-control" :placeholder="$t('enter_subject')" id="form_subject" v-model="form.form_subject" />
                      <span v-for="(error, index) in errors.form_subject" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="mb-3">
                      <label for="form_message" class="form-label">{{$t("form_message_title")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input :readonly="app_for === 'demo'" type="text" class="form-control" :placeholder="$t('enter_message')" id="form_message" v-model="form.form_message" />
                      <span v-for="(error, index) in errors.form_message" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="mb-3">
                      <label for="form_btn" class="form-label">{{$t("form_button_name")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input :readonly="app_for === 'demo'" type="text" class="form-control" :placeholder="$t('enter_button_name')" id="form_btn" v-model="form.form_btn" />
                      <span v-for="(error, index) in errors.form_btn" :key="index" class="text-danger">
                        {{ error }}
                      </span>
                    </div>
                  </div>
                  
                  <div class="col-lg-12">
                    <div class="text-end">
                      <button type="submit" class="btn btn-primary">{{ landingContact ? $t('update') : $t('create') }}</button>
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
