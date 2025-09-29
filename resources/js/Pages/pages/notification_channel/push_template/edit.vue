<script>
import { Link,Head, useForm,router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import { ref, watch, onMounted } from "vue";
import axios from "axios";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import "flatpickr/dist/flatpickr.css";
import CKEditor from "@ckeditor/ckeditor5-vue";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
import { useSharedState } from '@/composables/useSharedState'; // Import the composable
import ImageUpload from "@/Components/ImageUpload.vue";
import FormValidation from "@/Components/FormValidation.vue";
import { useI18n } from 'vue-i18n';
export default {
  data() {
    return {
      editor: ClassicEditor,
      editorData: "",
      // showBannerImage : true,
      // showButton : true,
      // showFBIcon: true,
      // showInstaIcon: true,
      // showTwitterIcon: true,
      // showLinkedinIcon: true,
      parsedFooter: [],
    };
  },
  components: {
    Layout,
    PageHeader,
    Head,
    Pagination,
    Multiselect,
    FormValidation,
    ImageUpload,
    ckeditor: CKEditor.component,
  },
  props: {
    successMessage: String,
    alertMessage: String,
    notification: {
      type: Object,
      required: true,
    },
    validate: Function,    
    languages: Array,
    app_for: String,
  },
  // setup() {
  //   let files = ref([]);
  //   let dropzoneFile = ref("");
  //   const drop = (e) => {
  //     dropzoneFile.value = e.dataTransfer.files[0];
  //     files.value.push(dropzoneFile.value);
  //   };
  //   const selectedFile = () => {
  //     dropzoneFile.value = document.querySelector(".dropzoneFile").files[0];
  //     files.value.push(dropzoneFile.value);
  //   };
  //   watch(
  //     () => [...files.value],
  //     (currentValue) => {
  //       return currentValue;
  //     }
  //   );
  //   return {
  //     dropzoneFile,
  //     drop,
  //     selectedFile,
  //     v$: useVuelidate(),
  //     files
  //   };
  // },

  

  setup(props) {
    const { t } = useI18n();    
    const { languages, fetchData } = useSharedState(); // Destructure the shared state
    const activeTab = ref('English');

    const form = useForm({
      push_title: Object.keys(props.notification.languageFields).reduce((acc, langCode) => {
        acc[langCode] = props.notification.languageFields[langCode]?.push_title || '';
        return acc;
      }, {}),
      push_body: Object.keys(props.notification.languageFields).reduce((acc, langCode) => {
        acc[langCode] = props.notification.languageFields[langCode]?.push_body || '';
        return acc;
      }, {}),
      
    });
    const validationRules = {};

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
    const setActiveTab = (tab) => {
      activeTab.value = tab;
    };


    const handleSubmit = async () => {
      if(props.app_for == "demo"){
          Swal.fire(t('error'), t('you_are_not_authorised'), 'error');
          return;
      }
      try {
        const formData = new FormData();       

        let response;
        Object.keys(form.push_title).forEach(langCode => {
          formData.append(`push_title[${langCode}]`, form.push_title[langCode]);
        });
        Object.keys(form.push_body).forEach(langCode => {
          formData.append(`push_body[${langCode}]`, form.push_body[langCode]);
        });

        console.log("formdata", form.data());
        
        if (props.notification && props.notification.id) {
          response = await axios.post(`/notification-channel/update-push-template/${props.notification.id}`, formData, {
            headers: {
             'Content-Type': 'multipart/form-data',
            },
          });

        } 
        if (response.status === 201 ) {
          successMessage.value = t('mail_template_created_successfully');
          form.reset();
          router.get('/notification-channel'); 
        } else {
          alertMessage.value = t('failed_to_create_mail_template');
        }
      } catch (error) {
        console.error(t('error_creating_mail_template'), error);
        alertMessage.value = t('failed_to_create_mail_template_catch');
        if (error.response && error.response.data.errors) {
          errors.value = error.response.data.errors;
        }
      }
    };



      // Construct the full URL for the vehicle type icon
      const iconUrl = props.vehicleType ? props.vehicleType.icon :null;

      const handleImageSelected = (file, fieldName) => {
        form[fieldName] = file;
      };

    const handleImageRemoved = (fieldName) => {
      form[fieldName] = null;
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
      validationRules,
      validationRef,
      errors,
      validateIconSize,
      notification: props.notification,
      iconUrl,
      handleImageSelected,
      handleImageRemoved,
      languages,
      setActiveTab,
      activeTab,
      
    };
  },
  
  
  methods: {
        stripHtmlTags(content) {
            const parser = new DOMParser();
            const parsedContent = parser.parseFromString(content, 'text/html');
            return parsedContent.body.textContent || ""
            },
    },
};
</script>

<template>
  <Layout>
    <PageHeader :title="notification ? $t('edit') : $t('create')" :pageTitle="$t('template')"  pageLink="/notification-channel" />
    <BRow>
      <BCol lg="6">
          <BCard no-body>
            <BCardBody>
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
                              <label :for="`name-${language.code}`" class="form-label">{{ $t("push_title") }}
                                <span class="text-danger">*</span>
                              </label>
                              <input type="text" class="form-control" :placeholder="$t('enter_push_title' ,{language: language.label})"
                                :id="`enter_push_title-${language.code}`" v-model="form.push_title[language.code]"
                                :required="language.code === 'en'">
                            </div>
                          </div>
                          <div class="mb-3">
                            <label>{{$t("push_body")}}</label>
                            <ckeditor v-model="form.push_body[language.code]" :id="`name-${language.code}`"  :editor="editor"></ckeditor>
                            <span v-for="(error, index) in errors.push_body" :key="index" class="text-danger">
                              {{ error }}
                            </span>
                          </div>
                            <div class="text-end mb-3">
                              <button type="submit" class="btn btn-primary" :disabled="app_for === 'demo'">{{ notification ? $t('update') : $t('save') }}</button>
                            </div>
                        </div>
                      </div>
                    </div>
                  </BCol>
                </BRow>
                </FormValidation>
              </form>             
            </BCardBody>
          </BCard>
      </BCol>

      <BCol lg="6">
      <BCard>
        <div class="col-12">
            <table class="body-wrap">
                <tbody><tr>
                    <td></td>
                    <td class="container" width="600">
                        <div class="content">
                            <table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope="" itemtype="http://schema.org/ConfirmAction" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; margin: 0; border: none;">
                                <tbody><tr>
                                    <td class="content-wrap">
                                        <meta itemprop="name" content="Confirm Email" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                        <table width="100%" cellpadding="0" cellspacing="0" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                          <tbody>
                                            <tr v-if=" notification && notification.push_title">
                                                <td  class="content-block" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 20px; line-height: 1.5; font-weight: 500; vertical-align: top; margin: 0; padding: 0 0 10px;" valign="top">
                                                    {{notification.push_title}}
                                                </td>
                                            </tr>
                                            <tr v-else>   
                                                <td  class="content-block" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 20px; line-height: 1.5; font-weight: 500; vertical-align: top; margin: 0; padding: 0 0 10px;" valign="top" >
                                                  Register successfully
                                                </td>
                                            </tr> 
                                            <tr v-if="notification && notification.push_body">
                                              <!-- {{ stripHtmlTags(notification.mail_body) }} -->
                                              <div v-html="notification.push_body"></div>
                                            </tr>
                                            <tr v-else>
                                              <p>Register successfully</p>
                                            </tr>  
                                          </tbody>
                                        </table>
                                    </td>
                                  </tr>
                            </tbody></table>                        
                        </div>
                    </td>
                </tr>
            </tbody></table>
            <!-- end table -->
        </div>
      </BCard>
      </BCol>
    </BRow>
  </Layout>
</template>