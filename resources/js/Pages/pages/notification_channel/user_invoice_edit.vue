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
    const footer = ref(props.notification && props.notification.footer
        ? JSON.parse(props.notification.footer) // Ensure it's parsed to an array
        : [{ 
          footer_fblink: "" ,
          footer_instalink:"",
          footer_twitterlink:"",
          footer_linkedinlink:""
        }]);



    const form = useForm({
      // email_subject:  props.notification ? props.notification.languageFields|| "" : "", 
    email_subject: Object.keys(props.notification.languageFields).reduce((acc, langCode) => {
        acc[langCode] = props.notification.languageFields[langCode]?.email_subject || '';
        return acc;
      }, {}),
      footer_content: Object.keys(props.notification.languageFields).reduce((acc, langCode) => {
        acc[langCode] = props.notification.languageFields[langCode]?.footer_content || '';
        return acc;
      }, {}),
      footer_copyrights: Object.keys(props.notification.languageFields).reduce((acc, langCode) => {
        acc[langCode] = props.notification.languageFields[langCode]?.footer_copyrights || '';
        return acc;
      }, {}),

      logo_img: props.notification ? props.notification.logo_img || "" : "",
      // mail_body: props.notification ? props.notification.mail_body || "" : "",
      mail_body: props.notification && props.notification.languageFields
    ? Object.keys(props.notification.languageFields).reduce((acc, langCode) => {
        acc[langCode] = props.notification.languageFields[langCode]?.mail_body || '';
        return acc;
      }, {})
    : {},

      // button_name: props.notification ? props.notification.button_name || "" : "",

      button_name: props.notification && props.notification.languageFields
    ? Object.keys(props.notification.languageFields).reduce((acc, langCode) => {
        acc[langCode] = props.notification.languageFields[langCode]?.button_name || '';
        return acc;
      }, {})
    : {},
      button_url: props.notification ? props.notification.button_url || "" : "",
      banner_img: props.notification ? props.notification.banner_img || "" : "",
      show_button: props.notification?.show_button == 1,
      show_img: props.notification?.show_img == 1,
      show_fbicon: props.notification?.show_fbicon == 1,
      show_instaicon: props.notification?.show_instaicon == 1,
      show_twittericon: props.notification?.show_twittericon == 1,
      show_linkedinicon: props.notification?.show_linkedinicon == 1,
      
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
        Object.keys(form.email_subject).forEach(langCode => {
          formData.append(`email_subject[${langCode}]`, form.email_subject[langCode]);
        });
        Object.keys(form.button_name).forEach(langCode => {
          formData.append(`button_name[${langCode}]`, form.button_name[langCode]);
        });
        Object.keys(form.footer_content).forEach(langCode => {
          formData.append(`footer_content[${langCode}]`, form.footer_content[langCode]);
        });
        Object.keys(form.footer_copyrights).forEach(langCode => {
          formData.append(`footer_copyrights[${langCode}]`, form.footer_copyrights[langCode]);
        });
        Object.keys(form.mail_body).forEach(langCode => {
          formData.append(`mail_body[${langCode}]`, form.mail_body[langCode]);
        });

        formData.append('banner_img', form.banner_img);
        formData.append('button_url', form.button_url);
         formData.append('logo_img', form.logo_img);
         formData.append('footer_fblink', footer.value.footer_fblink || '');
        formData.append('footer_instalink', footer.value.footer_instalink || '');
        formData.append('footer_twitterlink', footer.value.footer_twitterlink || '');
        formData.append('footer_linkedinlink', footer.value.footer_linkedinlink || '');
         formData.append('show_button', form.show_button ? '1' : '0');
         formData.append('show_img', form.show_img ? '1' : '0');
         formData.append('show_fbicon', form.show_fbicon ? '1' : '0');
         formData.append('show_instaicon', form.show_instaicon ? '1' : '0');
         formData.append('show_twittericon', form.show_twittericon ? '1' : '0');
         formData.append('show_linkedinicon', form.show_linkedinicon ? '1' : '0');
        
        if (props.notification && props.notification.id) {
          response = await axios.post(`/notification-channel/update/${props.notification.id}`, formData, {
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
      footer,
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
    mounted() {
      const storagePath = '/storage/uploads/website/images/';
      this.notification.logo_img = `${storagePath}${this.notification.logo_img}`
      this.notification.banner_img = `${storagePath}${this.notification.banner_img}`
    if (this.notification.footer) {
      try {
        this.parsedFooter = JSON.parse(this.notification.footer);
      } catch (error) {
        console.error(t('error_parsing_footer_content'), error);
      }
    }
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
                          <div class="mb-3 mt-3">
                            <label for="logo" class="form-label d-flex">{{$t("logo")}}
                              <span><h5 class="text-muted mt-1 mb-0 fs-13">(150px x 60px)</h5></span>
                            </label>
                            <ImageUpload    :flexStyle="'0 0 calc(95% - 20px)'" :aspectRatio="'11 / 2'"  :initialImageUrl="form.logo_img"  
                                @image-selected="(file) => handleImageSelected(file, 'logo_img')" @image-removed="() => handleImageRemoved('logo_img')">
                            </ImageUpload>
                            <span v-if="form.errors.logo_img" class="text-danger">{{ form.errors.logo_img }}</span>
                          </div>
                          <div class="col-sm-6 mt-3">
                            <div class="mb-3">
                              <label :for="`name-${language.code}`" class="form-label">{{ $t("email_subject") }}
                                <span class="text-danger">*</span>
                              </label>
                              <input type="text" class="form-control" :placeholder="$t('enter_subject' ,{language: language.label})"
                                :id="`enter_subject-${language.code}`" v-model="form.email_subject[language.code]"
                                :required="language.code === 'en'">
                            </div>
                          </div>
                          <div>
                            <label>{{$t("body_of_the_mail")}}</label>
                            <ckeditor v-model="form.mail_body[language.code]" :id="`name-${language.code}`"  :editor="editor"></ckeditor>
                            <span v-for="(error, index) in errors.mail_body" :key="index" class="text-danger">
                              {{ error }}
                            </span>
                          </div>
                          <div class="form-check form-switch form-switch-right form-switch-md mt-4">
                              <label for="radio-toggle-shocade" class="form-label text-dark">{{$t("button_enable")}}</label>
                              <input class="form-check-input code-switcher" type="checkbox" id="radio-toggle-shocade" v-model="form.show_button">
                          </div>
                          <div class="row mt-3">
                            <div class="col-lg-6">
                              <div class="mb-3">
                                <label class="form-label" for="button_name">{{$t("button_name")}}</label>
                                <input type="text" class="form-control" :id="`button_name-${language.code}`" 
                                  :placeholder="$t('enter_name')" v-model="form.button_name[language.code]" >
                              </div>
                              <span v-for="(error, index) in errors.button_name" :key="index" class="text-danger">{{ error }}</span>
                            </div>
                            <div class="col-lg-6">
                              <div class="mb-3">
                                <label class="form-label" for="button_url">{{$t("button_url")}}</label>
                                <input type="url" class="form-control" id="button_url"
                                  :placeholder="$t('enter_url')" v-model="form.button_url" >
                              </div>
                              <span v-for="(error, index) in errors.button_url" :key="index" class="text-danger">{{ error }}</span>
                            </div>
                           </div> 
                           <div class="form-check form-switch form-switch-right form-switch-md mt-1 mb-3">
                                <label for="radio-toggle-shocade" class="form-label text-dark">{{$t("banner_image_enable")}}</label>
                                <input class="form-check-input code-switcher" type="checkbox" id="radio-toggle-shocade" v-model="form.show_img">
                            </div>
                            <div class="mb-4">
                              <label for="logo" class="form-label d-flex">{{$t("banner_img")}}
                                <span><h5 class="text-muted mt-1 mb-0 fs-13">(600px x 300px)</h5></span>
                              </label>
                              <ImageUpload    :flexStyle="'0 0 calc(95% - 20px)'" :aspectRatio="'2 / 1'"  :initialImageUrl="form.banner_img"  
                                  @image-selected="(file) => handleImageSelected(file, 'banner_img')" @image-removed="() => handleImageRemoved('banner_img')">
                              </ImageUpload>
                              <span v-if="form.errors.banner_img" class="text-danger">{{ form.errors.logo_img }}</span>
                            </div>
                            <div class="p-3 bg-light rounded mb-4 mt-5">
                              <div>
                                <div class="mb-3">
                                <label class="form-label" for="footer_content">{{$t("footer_content")}}</label>
                                <input type="text" class="form-control" id="footer_content" :placeholder="$t('enter_footer')"  v-model="form.footer_content[language.code]" />
                                <span v-for="(error, index) in errors.footer_content" :key="index" class="text-danger">{{ error }}</span>
                              </div>
                              <div class="mb-3">
                                <label class="form-label" for="footer_copyrights">{{$t("copyrights_content")}}</label>
                                <input type="text" class="form-control" id="footer_copyrights" :placeholder="$t('enter_copyrights')" v-model="form.footer_copyrights[language.code]" />
                                <span v-for="(error, index) in errors.footer_copyrights" :key="index" class="text-danger">{{ error }}</span>
                              </div>
                              <div>
                                <div class="input-group mb-3">                
                                  <span class="input-group-text">{{$t("facebook")}}</span>
                                    <div class="input-group-text">
                                      <input class="form-check-input mt-0" type="checkbox" value="" aria-label="Checkbox for following text input" v-model="form.show_fbicon">
                                    </div>
                                    <input type="url" class="form-control" id="footer_fblink" :placeholder="$t('enter_facebook_link')" v-model="footer.footer_fblink">                  
                                </div>
                                  <span v-for="(error, index) in errors.footer_fblink" :key="index" class="text-danger">{{ error }}</span>
                              </div>              
                              <div>
                                <div class="input-group mb-3">                
                                  <span class="input-group-text">{{$t("instagram")}}</span>
                                    <div class="input-group-text">
                                      <input class="form-check-input mt-0" type="checkbox" value="" aria-label="Checkbox for following text input" v-model="form.show_instaicon">
                                    </div>
                                    <input type="url" class="form-control"  id="footer_instalink" :placeholder="$t('enter_instagram_link')" v-model="footer.footer_instalink">                  
                                </div>
                                <span v-for="(error, index) in errors.footer_instalink" :key="index" class="text-danger">{{ error }}</span>
                              </div>              
                              <div>
                                <div class="input-group mb-3">                
                                  <span class="input-group-text">{{$t("twitter")}}</span>
                                    <div class="input-group-text">
                                      <input class="form-check-input mt-0" type="checkbox" value="" aria-label="Checkbox for following text input" v-model="form.show_twittericon">
                                    </div>
                                    <input type="url" class="form-control"  id="footer_twitterlink" :placeholder="$t('enter_twitter_link')" v-model="footer.footer_twitterlink">                  
                                </div>
                                <span v-for="(error, index) in errors.footer_twitterlink" :key="index" class="text-danger">{{ error }}</span>
                              </div>
                              
                              <div>
                                <div class="input-group mb-3">                
                                  <span class="input-group-text">{{$t("linkedin")}}</span>
                                    <div class="input-group-text">
                                      <input class="form-check-input mt-0" type="checkbox" value="" aria-label="Checkbox for following text input" v-model="form.show_linkedinicon">
                                    </div>
                                    <input type="url" class="form-control"  id="footer_linkedinlink" :placeholder="$t('enter_linkedin_link')" v-model="footer.footer_linkedinlink">                  
                                </div>
                                <span v-for="(error, index) in errors.footer_linkedinlink" :key="index" class="text-danger">{{ error }}</span>
                              </div>
                              </div>
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
                                            <tbody><tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                <td class="content-block" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
                                                    <div style="margin-bottom: 15px;" v-if="notification && notification.logo_img" >
                                                        <img :src="notification.logo_img" alt="" height="23" >
                                                    </div>
                                                    <div style="margin-bottom: 15px;" v-else>
                                                        <img src="@assets/images/logo-light.png" alt="" height="23" >
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr v-if=" notification && notification.email_subject">
                                                <td  class="content-block" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 20px; line-height: 1.5; font-weight: 500; vertical-align: top; margin: 0; padding: 0 0 10px;" valign="top">
                                                    {{notification.email_subject}}
                                                </td>
                                            </tr>
                                            <tr v-else>   
                                                <td  class="content-block" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 20px; line-height: 1.5; font-weight: 500; vertical-align: top; margin: 0; padding: 0 0 10px;" valign="top" >
                                                  Hello User
                                                </td>
                                            </tr> 
                                            <tr v-if="notification && notification.mail_body">
                                               <!-- {{ stripHtmlTags(notification.mail_body) }} -->
                                               <div v-html="notification.mail_body"></div>
                                            </tr>
                                            <tr v-else>
                                              <p>Hello {name}</p>  
                                              <p>Thank You for Riding with us</p>
                                              <p>Here is the summary of your recent trip:</p>
                                            </tr>  
                                            
                                            <div style="margin-left: -30px; padding: 0">
                                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                <tbody>
                                                <tr>
                                                <td bgcolor="#EEEEEE" style="padding: 20px 15px 0 15px">
                                                <div align="center">
                                                <table border="0" cellpadding="0" cellspacing="0" width="660">
                                                <tbody>
                                                <tr>
                                                <td bgcolor="#ffffff"
                                                style="padding: 24px 14px 0 14px; border-top-left-radius: 4px; border-top-right-radius: 4px">
                                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                <tbody>
                                                <tr>
                                                <td width="40%" align="left">
                                                <table>
                                                <tbody>
                                                <tr>
                                                <td
                                                style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 14px; color: #707070; font-weight: normal; line-height: 16px">
                                                Jan 06,2025 - 04:04 PM
                                                <br />
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                <td width="60%" align="right">
                                                <table border="0" cellpadding="0" cellspacing="0">
                                                <tbody>
                                                <tr>
                                                <td align="right" style="padding: 0 0 5px 0">
                                                <a href="#"
                                                style="float: right" rel="noreferrer">
                                                    <img src="/storage/uploads/website/images/rest.png" alt="Logo" class="logo"
                                                    style="display: flex; float: none; text-align: center; height: 23px;margin-top:20px;">
                                               
                                                <!-- <img width="76"
                                                style="display: inline; float: none; text-align: center; width: 150px"
                                                src="{{ asset('invoice/logo.jpeg') }}"
                                                alt="" class="CToWUd" /> -->
                                                </a> <br> <br> <br>

                                                <!-- <br> <p>5,test-address, Coimbatore-05.</p> -->
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                <tr>
                                                <td style="color: #ffffff; line-height: 10px">
                                                <img src="/invoice/Invoice_White_line.png"
                                                style="width: 100%" class="CToWUd" />
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                <tr>

                                                <td bgcolor="#ffffff" align="center"
                                                style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 40px; font-weight: bold; color: #000000">
                                                <table>
                                                <tbody>
                                                <tr>
                                                <td style="padding-left: 15px">
                                                <table>
                                                <tbody>
                                                <tr>
                                                <td
                                                style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 40px; font-weight: bold; color: #000000; padding-right: 10px">
                                                
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                <td style="padding-top: 10px; padding-left: 6px"></td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                <tr>
                                                <td bgcolor="#ffffff" align="center"
                                                style="padding-right: 20px; padding-left: 20px; padding-bottom: 2px; font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif; color: #777777">
                                                <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
                                                <tbody>
                                                <tr>
                                                <td align="center" style="
                                                padding-top: 10px;
                                                background-image: url('Invoice_background.png');
                                                background-position: center 100%;
                                                background-repeat: no-repeat;
                                                ">
                                                <span style="
                                                background-color: #ffffff;
                                                padding-top: 0px;
                                                padding-left: 0px;
                                                padding-right: 0px;
                                                font-size: 14px;
                                                font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
                                                color: #707070;
                                                ">
                                                &nbsp;&nbsp;&nbsp; 115.00&nbsp;&nbsp;&nbsp;<br />
                                                REQ_11111115
                                                </span>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                <tr>
                                                <td bgcolor="#ffffff" align="center"
                                                style="font-family: Helvetica, Helvetica Neue, Arial, sans-serif; font-size: 14px; word-spacing: -0.3px; color: #000000; padding-bottom: 0px; padding-top: 17px">
                                                Thanks for ordering with us,{user_name}
                                                </td>
                                                </tr>
                                                <tr>
                                                <td bgcolor="#ffffff" align="center"
                                                style="padding-left: 16px; padding-right: 16px">
                                                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                                <tbody>
                                                <tr>
                                                <td
                                                style="border-bottom: 3px solid #ececec; width: 100%; text-align: center; color: #ffffff">
                                                <img src="/invoice/Invoice_White_line.png"
                                                style="width: 100%" class="CToWUd" />
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </div>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                <table bgcolor="#eeeeee" border="0" cellpadding="0" cellspacing="0" width="100%">
                                                <tbody>
                                                <tr>
                                                <td bgcolor="#EEEEEE" align="center" style="padding: 0 15px 0 15px">
                                                <table border="0" cellpadding="0" cellspacing="0" width="660">
                                                <tbody>
                                                <tr>
                                                <td>
                                                <table cellspacing="0" cellpadding="0" border="0" width="100%">
                                                <tbody>
                                                <tr>
                                                <td bgcolor="#ffffff" valign="top"
                                                style="padding-top: 28px; padding-bottom: 0; padding-left: 0px; padding-right: 0px">
                                                <table cellpadding="0" cellspacing="0" border="0" width="49.5%"
                                                align="left">
                                                <tbody>
                                                <tr>
                                                <td style="padding-left: 14px; padding-right: 14px">
                                                <table cellpadding="0" cellspacing="0" border="0"
                                                width="100%">
                                                <tbody>
                                                <tr>
                                                <td align="center" style="
                                                font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif;
                                                color: #333333;
                                                font-size: 16px;
                                                padding-bottom: 16px;
                                                font-weight: bold;
                                                border-bottom: 1px solid #d7d7d7;
                                                ">
                                                Order Details
                                                </td>
                                                </tr>
                                                <tr>
                                                <td style="padding: 9px 0 0 0">
                                                <table cellspacing="0"
                                                cellpadding="0" border="0"
                                                width="100%">
                                                <tbody>
                                                <tr>
                                                <td valign="top"
                                                style="border-bottom: 1px solid #eeeeee; padding-bottom: 10px; padding-left: 14px">
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="100%"
                                                style="width: 100%"
                                                align="left">
                                                <tbody>
                                                <tr>
                                                <td
                                                align="left">
                                                <table>
                                                <tbody>
                                                <tr>
                                                <td
                                                style="font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif; color: #000000; font-size: 16px; line-height: 18px;font-weight: bold;">

                                                For:{user_name}
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                <tr>
                                                <td
                                                style="color: #ffffff; line-height: 12px !important; font-size: 10px !important">
                                                <img src="/invoice/Invoice_White_line.png"
                                                style="width: 100%"
                                                class="CToWUd" />
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                <tr>
                                                <td style="padding: 0 0 0 0">
                                                <table cellspacing="0"
                                                cellpadding="0" border="0"
                                                width="100%">
                                                <tbody>
                                                <tr>
                                                <td valign="top"
                                                style="border-bottom: 1px solid #eeeeee; padding-bottom: 12px; padding-left: 14px; padding-top: 5px">
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="100%"
                                                style="width: 100%"
                                                align="left">
                                                <tbody>
                                                <tr>
                                                <td align="left"
                                                style="width: 42px; padding-left: 2px"
                                                width="42">
                                                <img width="40"
                                                style="width: 40px"
                                                src="/invoice/Invoice_user_icon_2x.png"
                                                alt=""
                                                class="CToWUd" />
                                                </td>
                                                <td align="left"
                                                style="font-family: Helvetica, Helvetica Neue, Arial, sans-serif; color: #000000; font-size: 14px; padding-left: 16px; padding-top: 8px">
                                                {user_name}
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                <tr>
                                                <td
                                                style="color: #ffffff; line-height: 12px; font-size: 10px">
                                                <img src="/invoice/Invoice_White_line.png"
                                                style="width: 100%"
                                                class="CToWUd" />
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                <tr>
                                                <td style="padding: 0 0 0 0">
                                                <table cellspacing="0"
                                                cellpadding="0" border="0"
                                                width="100%">
                                                <tbody>
                                                <tr>
                                                <td valign="top"
                                                style="border-bottom: 1px solid #eeeeee; padding-bottom: 12px; padding-left: 14px; padding-top: 5px">
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="100%"
                                                style="width: 100%"
                                                align="left">
                                                <tbody>
                                                <tr>
                                                <td align="left"
                                                style="width: 42px; padding-left: 2px"
                                                width="42">
                                                <img width="40"
                                                style="width: 40px"
                                                src="/invoice/Invoice_dash_icon_2x.png"
                                                alt=""
                                                class="CToWUd" />
                                                </td>
                                                <td align="left"
                                                style="font-family: Helvetica, Helvetica Neue, Arial, sans-serif; color: #000000; font-size: 14px; padding-left: 16px; padding-top: 8px">
                                                Total
                                                Kms:{total_distance}
                                                km
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                <tr>
                                                <td
                                                style="color: #ffffff; line-height: 12px; font-size: 10px">
                                                <img src="/invoice/Invoice_White_line.png"
                                                style="width: 100%"
                                                class="CToWUd" />
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                <tr>
                                                <td style="padding: 0 0 0 0">
                                                <table cellspacing="0"
                                                cellpadding="0" border="0"
                                                width="100%">
                                                <tbody>
                                                <tr>
                                                <td valign="top"
                                                style="border-bottom: 1px solid #eeeeee; padding-bottom: 12px; padding-left: 14px; padding-top: 5px">
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="100%"
                                                style="width: 100%"
                                                align="left">
                                                <tbody>
                                                <tr>
                                                <td align="left"
                                                style="width: 42px; padding-left: 2px"
                                                width="42">
                                                <img width="40"
                                                style="width: 40px"
                                                src="/invoice/Invoice_time_icon_2x.png"
                                                alt=""
                                                class="CToWUd" />
                                                </td>
                                                <td align="left"
                                                style="font-family: Helvetica, Helvetica Neue, Arial, sans-serif; color: #000000; font-size: 14px; padding-left: 16px; padding-top: 8px">
                                                Total
                                                Mins:{totla_time}
                                                min
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                <tr>
                                                <td
                                                style="color: #ffffff; line-height: 12px; font-size: 10px">
                                                <img src="/invoice/Invoice_White_line.png"
                                                style="width: 100%"
                                                class="CToWUd" />
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                <tr>
                                                <td style="padding: 0 0 0 0">
                                                <table cellspacing="0"
                                                cellpadding="0" border="0"
                                                width="100%">
                                                <tbody>
                                                <tr>
                                                <td valign="top"
                                                style="border-bottom: 1px solid #eeeeee; padding-bottom: 7px; padding-left: 14px; padding-top: 5px">
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="100%"
                                                style="width: 100%"
                                                align="left">
                                                <tbody>
                                                <tr>
                                                <td align="left"
                                                style="width: 42px; padding-left: 2px"
                                                width="42">
                                                <img width="38"
                                                style="width: 38px"
                                                src="/invoice/Invoice_driver_default.png"
                                                alt="vehicle_type_img"
                                                class="CToWUd" />
                                                </td>
                                                <td align="left"
                                                style="
                                                padding-top: 4px;
                                                font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif;
                                                color: #000000;
                                                font-size: 14px;
                                                padding-left: 16px;
                                                line-height: 16px;
                                                ">
                                                {driver_name}
                                                <br>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                <tr>
                                                <td
                                                style="color: #ffffff; line-height: 8px">
                                                <img src="/invoice/Invoice_White_line.png"
                                                style="width: 100%"
                                                class="CToWUd" />
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                <tr style="text-align: left; display: block; background-color: #ffffff; padding-bottom: 10px; padding-top: 5px"
                                                align="left" bgcolor="#ffffff">
                                                <td style="word-break: break-word; border-collapse: collapse !important; vertical-align: top; text-align: left; display: inline-block; padding: 10px 0 0 14px"
                                                align="left" valign="top">
                                                <table
                                                style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: auto; padding: 0">
                                                <tbody>
                                                <tr style="vertical-align: top; text-align: left; width: 100%; padding-top: 5px"
                                                align="left">
                                                <td style="
                                                word-break: break-word;
                                                border-collapse: collapse !important;
                                                vertical-align: top;
                                                text-align: left;
                                                display: table-cell;
                                                width: 80px !important;
                                                line-height: 16px;
                                                height: auto;
                                                padding: 0 0 0 0;
                                                " align="left" valign="top">
                                                <span
                                                style="font-size: 14px; font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif; font-weight: normal; color: #000000 !important">
                                                <span> <a
                                                style="text-decoration: none !important; color: #000000 !important"
                                                rel="noreferrer"></a>
                                                </span>
                                                {start_time}
                                                </span>
                                                </td>
                                                <td rowspan="2" style="
                                                word-break: break-word;
                                                border-collapse: collapse !important;
                                                vertical-align: top;
                                                text-align: left;
                                                display: table-cell;
                                                width: 17px !important;
                                                padding: 3px 2px 10px 2px;
                                                " align="left" valign="top">
                                                <img width="6"
                                                height="84px"
                                                src="/invoice/Invoice_src_dest.png"
                                                style="outline: none; text-decoration: none; float: left; clear: both; display: block; width: 6px !important; height: 84px; padding-top: 5px"
                                                align="left"
                                                class="CToWUd" />
                                                </td>
                                                <td style="
                                                word-break: break-word;
                                                border-collapse: collapse !important;
                                                vertical-align: top;
                                                text-align: left;
                                                display: table-cell;
                                                width: 197px;
                                                line-height: 16px;
                                                height: 57px;
                                                padding: 0 10px 10px 0;
                                                " align="left" valign="top">
                                                <span style="
                                                font-size: 14px;
                                                font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif;
                                                color: #000000 !important;
                                                line-height: 16px;
                                                text-decoration: none;
                                                ">
                                                {pickup_address}
                                                </span>
                                                </td>
                                                </tr>
                                                <tr style="vertical-align: top; text-align: left; width: 100%; padding: 0"
                                                align="left">
                                                <td style="
                                                word-break: break-word;
                                                border-collapse: collapse !important;
                                                vertical-align: top;
                                                text-align: left;
                                                display: table-cell;
                                                width: 80px !important;
                                                line-height: 16px;
                                                height: auto;
                                                padding: 0 0 0 0;
                                                " align="left" valign="top">
                                                <span
                                                style="font-size: 14px; font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif; font-weight: normal; color: #000000 !important">
                                                <span>
                                                <a style="text-decoration: none !important; color: #000000 !important"
                                                rel="noreferrer"></a>
                                                </span>
                                                {end_time}
                                                </span>
                                                </td>
                                                <td style="
                                                word-break: break-word;
                                                border-collapse: collapse !important;
                                                vertical-align: top;
                                                text-align: left;
                                                display: table-cell;
                                                width: 197px;
                                                line-height: 16px;
                                                height: auto;
                                                padding: 0 0px 0 0;
                                                " align="left" valign="top">
                                                <span style="
                                                font-size: 14px;
                                                font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif;
                                                color: #000000 !important;
                                                line-height: 16px;
                                                text-decoration: none;
                                                ">
                                                {drop_address}
                                                </span>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                <table cellpadding="0" cellspacing="0" border="0" width="49.5%"
                                                align="left">
                                                <tbody>
                                                <tr>
                                                <td style="padding-right: 14px; padding-left: 14px">
                                                <table cellpadding="0" cellspacing="0" border="0"
                                                width="100%">
                                                <tbody>
                                                <tr>
                                                <td align="center" style="
                                                font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif;
                                                color: #333333;
                                                font-size: 16px;
                                                padding-bottom: 16px;
                                                font-weight: bold;
                                                border-bottom: 1px solid #d7d7d7;
                                                ">
                                                Bill Details
                                                </td>
                                                </tr>
                                                <tr>
                                                <td bgcolor="#ffffff">
                                                <table cellspacing="0"
                                                cellpadding="0" border="0"
                                                width="100%">
                                                <tbody>
                                                <tr>
                                                <td
                                                style="color: #ffffff; line-height: 8px !important; padding: 0 0 0 0; font-size: 13px">
                                                <img style="width: 100%"
                                                src="/invoice/Invoice_White_line.png"
                                                class="CToWUd" />
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                <tr>
                                                <td bgcolor="#ffffff">
                                                <table cellspacing="0"
                                                cellpadding="0" border="0"
                                                width="100%">
                                                <tbody>
                                                <tr>
                                                <td border="0"
                                                cellpadding="0"
                                                cellspacing="0"
                                                valign="top"
                                                style="padding-left: 7px; padding-right: 13px; padding-top: 10px; padding-bottom: 2px">
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="80%"
                                                style="width: 80%"
                                                align="left">
                                                <tbody>
                                                <tr>
                                                <td>
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="100%">
                                                <tbody>
                                                <tr>
                                                <td
                                                align="left">
                                                <table>
                                                <tbody>
                                                <tr>
                                                <!-- <td align="left"
                                                style="font-family: Arial, Helvetica, 'Helvetica Neue', sans-serif; color: #707070; font-size: 14px">
                                                Bid
                                                Fare
                                                </td> -->
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                <td
                                                align="left">
                                                <table>
                                                <tbody>
                                                <tr>
                                                <td align="left"
                                                style="font-family: Arial, Helvetica, 'Helvetica Neue', sans-serif; color: #707070; font-size: 14px">
                                                Base
                                                Fare
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="20%"
                                                style="width: 20%"
                                                align="right">
                                                <tbody >
                                                    <tr>
                                                    </tr>
                                                </tbody>
                                                </table>

                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="20%"
                                                style="width: 20%"
                                                align="right">
                                                <tbody>
                                                <tr>
                                                <td
                                                style="padding: 0 0 0 0">
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="100%">
                                                <tbody>
                                                <tr>
                                                <td
                                                align="left">
                                                <table
                                                align="right">
                                                <tbody>
                                                <tr>
                                                <td
                                                style="font-family: Arial, Helvetica, 'Helvetica Neue', sans-serif; color: #707070; font-size: 14px">
                                                5
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                <tr>
                                                <td
                                                style="color: #ffffff; line-height: 8px !important; padding: 0 0 0 0; font-size: 13px">
                                                <img style="width: 100%"
                                                src="/invoice/Invoice_White_line.png"
                                                class="CToWUd" />
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                <tr>
                                                <td bgcolor="#ffffff">
                                                <table cellspacing="0"
                                                cellpadding="0" border="0"
                                                width="100%">
                                                <tbody>
                                                <tr>
                                                <td border="0"
                                                cellpadding="0"
                                                cellspacing="0"
                                                valign="top"
                                                style="padding-left: 7px; padding-right: 13px; padding-bottom: 2px">
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="80%"
                                                style="width: 80%"
                                                align="left">
                                                <tbody>
                                                <tr>
                                                <td
                                                style="padding: 0 0 0 0">
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="100%">
                                                <tbody>
                                                <tr>
                                                <td
                                                align="left">
                                                <table>
                                                <tbody>
                                                <tr>
                                                <td align="left"
                                                style="
                                                font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif;
                                                color: #707070;
                                                font-size: 14px;
                                                font-weight: normal;
                                                line-height: 18px;
                                                ">
                                                Additional
                                                Distance
                                                Fare
                                                for <br>  
                                                KM
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="20%"
                                                style="width: 20%"
                                                align="right">
                                                <tbody>
                                                <tr>
                                                <td
                                                style="padding: 0 0 0 0">
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="100%">
                                                <tbody>
                                                <tr>
                                                <td
                                                align="left">
                                                <table
                                                align="right">
                                                <tbody>
                                                <tr>
                                                <td style="
                                                font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif;
                                                color: #707070;
                                                font-size: 14px;
                                                font-weight: normal;
                                                line-height: 18px;
                                                ">0
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                <tr>
                                                <td
                                                style="color: #ffffff; line-height: 8px !important; padding: 0 0 0 0; font-size: 13px">
                                                <img style="width: 100%"
                                                src="/invoice/Invoice_White_line.png"
                                                class="CToWUd" />
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                <tr>
                                                <td bgcolor="#ffffff">
                                                <table cellspacing="0"
                                                cellpadding="0" border="0"
                                                width="100%">
                                                <tbody>
                                                <tr>
                                                <td border="0"
                                                cellpadding="0"
                                                cellspacing="0"
                                                valign="top"
                                                style="padding-left: 7px; padding-right: 13px; padding-bottom: 2px">
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="80%"
                                                style="width: 80%"
                                                align="left">
                                                <tbody>
                                                <tr>
                                                <td
                                                style="padding: 0 0 0 0">
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="100%">
                                                <tbody>
                                                <tr>
                                                <td
                                                align="left">
                                                <table>
                                                <tbody>
                                                <tr>
                                                <td align="left"
                                                style="
                                                font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif;
                                                color: #707070;
                                                font-size: 14px;
                                                font-weight: normal;
                                                line-height: 18px;
                                                ">
                                                Additional
                                                Time
                                                Fare
                                                for <br>
                                                min
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="20%"
                                                style="width: 20%"
                                                align="right">
                                                <tbody>
                                                <tr>
                                                <td
                                                style="padding: 0 0 0 0">
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="100%">
                                                <tbody>
                                                <tr>
                                                <td
                                                align="left">
                                                <table
                                                align="right">
                                                <tbody>
                                                <tr>
                                                <td style="
                                                font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif;
                                                color: #707070;
                                                font-size: 14px;
                                                font-weight: normal;
                                                line-height: 18px;
                                                ">0
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                <tr>
                                                <td
                                                style="color: #ffffff; line-height: 8px !important; padding: 0 0 0 0; font-size: 13px">
                                                <img style="width: 100%"
                                                src="/invoice/Invoice_White_line.png"
                                                class="CToWUd" />
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>


                                                <!-- Waiting Charge -->

                                                <tr>
                                                <td bgcolor="#ffffff">
                                                <table cellspacing="0"
                                                cellpadding="0" border="0"
                                                width="100%">
                                                <tbody>
                                                <tr>
                                                <td border="0"
                                                cellpadding="0"
                                                cellspacing="0"
                                                valign="top"
                                                style="padding-left: 7px; padding-right: 13px; padding-bottom: 2px">
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="80%"
                                                style="width: 80%"
                                                align="left">
                                                <tbody>
                                                <tr>
                                                <td
                                                style="padding: 0 0 0 0">
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="100%">
                                                <tbody>
                                                <tr>
                                                <td
                                                align="left">
                                                <table>
                                                <tbody>
                                                <tr>
                                                <td align="left"
                                                style="
                                                font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif;
                                                color: #707070;
                                                font-size: 14px;
                                                font-weight: normal;
                                                line-height: 18px;
                                                ">
                                                Waiting Charge
                                                for <br>
                                                min
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="20%"
                                                style="width: 20%"
                                                align="right">
                                                <tbody>
                                                <tr>
                                                <td
                                                style="padding: 0 0 0 0">
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="100%">
                                                <tbody>
                                                <tr>
                                                <td
                                                align="left">
                                                <table
                                                align="right">
                                                <tbody>
                                                <tr>
                                                <td style="
                                                font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif;
                                                color: #707070;
                                                font-size: 14px;
                                                font-weight: normal;
                                                line-height: 18px;
                                                ">0
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                <tr>
                                                <td
                                                style="color: #ffffff; line-height: 8px !important; padding: 0 0 0 0; font-size: 13px">
                                                <img style="width: 100%"
                                                src="/invoice/Invoice_White_line.png"
                                                class="CToWUd" />
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>


                                                <tr>
                                                <td bgcolor="#ffffff">
                                                <table cellspacing="0"
                                                cellpadding="0" border="0"
                                                width="100%">
                                                <tbody>
                                                <tr>
                                                <td border="0"
                                                cellpadding="0"
                                                cellspacing="0"
                                                valign="top"
                                                style="padding-left: 7px; padding-right: 13px; padding-bottom: 2px">
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="80%"
                                                style="width: 80%"
                                                align="left">
                                                <tbody>
                                                <tr>
                                                <td
                                                style="padding: 0 0 0 0">
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="100%">
                                                <tbody>
                                                <tr>
                                                <td
                                                align="left">
                                                <table>
                                                <tbody>
                                                <tr>
                                                <td align="left"
                                                style="
                                                font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif;
                                                color: #707070;
                                                font-size: 14px;
                                                font-weight: normal;
                                                line-height: 18px;
                                                ">
                                                Cancellation
                                                Fee
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="20%"
                                                style="width: 20%"
                                                align="right">
                                                <tbody>
                                                <tr>
                                                <td
                                                style="padding: 0 0 0 0">
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="100%">
                                                <tbody>
                                                <tr>
                                                <td
                                                align="left">
                                                <table
                                                align="right">
                                                <tbody>
                                                <tr>
                                                <td style="
                                                font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif;
                                                color: #707070;
                                                font-size: 14px;
                                                font-weight: normal;
                                                line-height: 18px;
                                                ">0
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>




                                                <tr>
                                                <td border="0"
                                                cellpadding="0"
                                                cellspacing="0"
                                                valign="top"
                                                style="padding-left: 7px; padding-right: 13px; padding-bottom: 2px">
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="80%"
                                                style="width: 80%"
                                                align="left">
                                                <tbody>
                                                <tr>
                                                <td
                                                style="padding: 0 0 0 0">
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="100%">
                                                <tbody>
                                                <tr>
                                                <td
                                                align="left">
                                                <table>
                                                <tbody>
                                                <tr>
                                                <td align="left"
                                                style="
                                                font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif;
                                                color: #707070;
                                                font-size: 14px;
                                                font-weight: normal;
                                                line-height: 18px;
                                                ">
                                                Convenience Fee <br>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="20%"
                                                style="width: 20%"
                                                align="right">
                                                <tbody>
                                                <tr>
                                                <td
                                                style="padding: 0 0 0 0">
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="100%">
                                                <tbody>
                                                <tr>
                                                <td
                                                align="left">
                                                <table
                                                align="right">
                                                <tbody>
                                                <tr>
                                                <td style="
                                                font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif;
                                                color: #707070;
                                                font-size: 14px;
                                                font-weight: normal;
                                                line-height: 18px;
                                                ">0
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>

                                                <!-- Promo Discount -->
                                                <tr>
                                                <td border="0"
                                                cellpadding="0"
                                                cellspacing="0"
                                                valign="top"
                                                style="padding-left: 7px; padding-right: 13px; padding-bottom: 2px">
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="80%"
                                                style="width: 80%"
                                                align="left">
                                                <tbody>
                                                <tr>
                                                <td
                                                style="padding: 0 0 0 0">
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="100%">
                                                <tbody>
                                                <tr>
                                                <td
                                                align="left">
                                                <table>
                                                <tbody>
                                                <tr>
                                                <td align="left"
                                                style="
                                                font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif;
                                                color: #707070;
                                                font-size: 14px;
                                                font-weight: normal;
                                                line-height: 18px;
                                                ">
                                                Promo Discount <br>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="20%"
                                                style="width: 20%"
                                                align="right">
                                                <tbody>
                                                <tr>
                                                <td
                                                style="padding: 0 0 0 0">
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="100%">
                                                <tbody>
                                                <tr>
                                                <td
                                                align="left">
                                                <table
                                                align="right">
                                                <tbody>
                                                <tr>
                                                <td style="
                                                font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif;
                                                color: #707070;
                                                font-size: 14px;
                                                font-weight: normal;
                                                line-height: 18px;
                                                ">0
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>

                                                <!-- Ride fare -->
                                                <tr>
                                                <td
                                                style="color: #ffffff; line-height: 8px !important; padding: 0 0 0 0; font-size: 13px">
                                                <img style="width: 100%"
                                                src="/invoice/Invoice_White_line.png"
                                                class="CToWUd" />
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                <tr>
                                                <td bgcolor="#ffffff">
                                                <table cellspacing="0"
                                                cellpadding="0" border="0"
                                                width="100%">
                                                <tbody>
                                                <tr>
                                                <td border="0"
                                                cellpadding="0"
                                                cellspacing="0"
                                                valign="top"
                                                style="padding-top: 6px; padding-bottom: 6px; padding-left: 7px; padding-right: 13px; background-color: #f3f3f3">
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="70%"
                                                style="width: 70%"
                                                align="left">
                                                <tbody>
                                                <tr>
                                                <td
                                                style="padding: 0 0 0 0">
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="100%">
                                                <tbody>
                                                <tr>
                                                <td
                                                align="left">
                                                <table>
                                                <tbody>
                                                <tr>
                                                <td align="left"
                                                style="
                                                font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif;
                                                color: #000000;
                                                font-size: 14px;
                                                font-weight: normal;
                                                line-height: 18px;
                                                ">
                                                Ride
                                                Fare
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="30%"
                                                style="width: 30%"
                                                align="right">
                                                <tbody>
                                                <tr>
                                                <td
                                                style="padding: 0 0 0 0">
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="100%">
                                                <tbody>
                                                <tr>
                                                <td
                                                align="left">
                                                <table
                                                align="right">
                                                <tbody>
                                                <tr>
                                                <td style="
                                                font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif;
                                                color: #000000;
                                                font-size: 14px;
                                                font-weight: normal;
                                                line-height: 18px;
                                                ">0
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>

                                                <tr>
                                                <td
                                                style="color: #ffffff; line-height: 8px !important; padding: 0 0 0 0; font-size: 13px">
                                                <img style="width: 100%"
                                                src="/invoice/Invoice_White_line.png"
                                                class="CToWUd" />
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>


                                                <tr>
                                                <td bgcolor="#ffffff">
                                                <table cellspacing="0"
                                                cellpadding="0" border="0"
                                                width="100%">
                                                <tbody>
                                                <tr>
                                                <td border="0"
                                                cellpadding="0"
                                                cellspacing="0"
                                                valign="top"
                                                style="padding-left: 7px; padding-right: 13px; padding-bottom: 2px">
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="72%"
                                                style="width: 72%"
                                                align="left">
                                                <tbody>
                                                <tr>
                                                <td
                                                style="padding: 0 0 0 0">
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="100%">
                                                <tbody>
                                                <tr>
                                                <td
                                                align="left">
                                                <table>
                                                <tbody>
                                                <tr>
                                                <td align="left"
                                                style="
                                                font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif;
                                                color: #707070;
                                                font-size: 14px;
                                                font-weight: normal;
                                                line-height: 18px;
                                                ">
                                                Tax 5 %
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="27%"
                                                style="width: 27%"
                                                align="right">
                                                <tbody>
                                                <tr>
                                                <td
                                                style="padding: 0 0 0 0">
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="100%">
                                                <tbody>
                                                <tr>
                                                <td
                                                align="left">
                                                <table
                                                align="right">
                                                <tbody>
                                                <tr>
                                                <td style="
                                                font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif;
                                                color: #707070;
                                                font-size: 14px;
                                                font-weight: normal;
                                                line-height: 18px;
                                                ">5.00
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                <tr>
                                                <td
                                                style="color: #ffffff; line-height: 8px !important; padding: 0 0 0 0; font-size: 13px">
                                                <img style="width: 100%"
                                                src="/invoice/Invoice_White_line.png"
                                                class="CToWUd" />
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>



                                                <tr>
                                                <td bgcolor="#ffffff">
                                                <table cellspacing="0"
                                                cellpadding="0" border="0"
                                                width="100%">
                                                <tbody>
                                                <tr>
                                                <td border="0"
                                                cellpadding="0"
                                                cellspacing="0"
                                                valign="top"
                                                style="padding-top: 6px; padding-bottom: 6px; padding-left: 7px; padding-right: 13px; background-color: #f3f3f3">
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="70%"
                                                style="width: 70%"
                                                align="left">
                                                <tbody>
                                                <tr>
                                                <td
                                                style="padding: 0 0 0 0">
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="100%">

                                                <tbody>
                                                <tr>
                                                <td
                                                align="left">
                                                <table>
                                                <tbody>
                                                <tr>
                                                <td align="left"
                                                style="
                                                font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif;
                                                color: #000000;
                                                font-size: 16px;
                                                font-weight: bold;
                                                line-height: 18px;
                                                ">
                                                Total
                                                Bill
                                                <span
                                                style="font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif; color: #707070; font-size: 13px; font-weight: normal">(rounded
                                                off)</span>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="30%"
                                                style="width: 30%"
                                                align="right">
                                                <tbody>
                                                <tr>
                                                <td
                                                style="padding: 0 0 0 0">
                                                <table
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                                width="100%">
                                                <tbody>
                                                <tr>
                                                <td
                                                align="left">
                                                <table
                                                align="right">
                                                <tbody>
                                                <tr>
                                                <td style="
                                                font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif;
                                                color: #000000;
                                                font-size: 16px;
                                                font-weight: bold;
                                                line-height: 18px;
                                                ">10
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                <tr>
                                                <td
                                                style="color: #ffffff; line-height: 8px !important; padding: 0 0 0 0; font-size: 13px">
                                                <img style="width: 100%"
                                                src="/invoice/Invoice_White_line.png"
                                                class="CToWUd" />
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                <table bgcolor="#eeeeee" border="0" cellpadding="0" cellspacing="0" width="100%">
                                                <tbody>
                                                <tr>
                                                <td bgcolor="#eeeeee" align="center" style="padding: 0 15px 0 15px">
                                                <table border="0" bgcolor="#ffffff" cellpadding="0" cellspacing="0" width="660">
                                                <tbody>
                                                <tr>
                                                <td bgcolor="#ffffff" align="center" style="padding-left: 15px; padding-right: 15px">
                                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                <tbody>
                                                <tr>
                                                <td align="center" style="
                                                font-family: Arial, Helvetica, 'Helvetica Neue', sans-serif;
                                                color: #000000;
                                                font-size: 16px;
                                                font-weight: bold;
                                                padding-top: 10px;
                                                padding-bottom: 10px;
                                                border-bottom: 1px solid #eeeeee;
                                                ">
                                                Payment
                                                </td>
                                                </tr>
                                                <tr>
                                                <td style="color: #ffffff; line-height: 14px">.</td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                <tr>
                                                <td bgcolor="#ffffff" style="padding: 0 15px 0 15px">
                                                <table cellspacing="0" cellpadding="0" border="0" width="100%">
                                                <tbody>
                                                <tr>
                                                <td border="0" cellpadding="0" cellspacing="0" valign="top"
                                                style="padding-bottom: 3px">
                                                <table cellpadding="0" cellspacing="0" border="0" width="50%"
                                                style="width: 50%" align="left">
                                                <tbody>
                                                <tr>
                                                <td style="padding: 0 0 0 0">
                                                <table cellpadding="0" cellspacing="0" border="0"
                                                width="100%">
                                                <tbody>
                                                <tr>
                                                <td align="left"
                                                style="padding-left: 70px">
                                                <table>
                                                <tbody>
                                                <tr>
                                                <td width="30"
                                                style="width: 30px">
                                                <img style="float: none; display: inline; width: 20px"
                                                width="20"
                                                src="/invoice/cash_international.png"
                                                alt=""
                                                class="CToWUd" />
                                                </td>
                                                <td align="left"
                                                style="padding-bottom: 5px; padding-left: 8px; font-family: Arial, Helvetica, 'Helvetica Neue', sans-serif; color: #707070; font-size: 14px">
                                                Paid By Cash
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                <table cellpadding="0" cellspacing="0" border="0" width="27%"
                                                style="width: 27%" align="right">
                                                <tbody>
                                                <tr>
                                                <td style="padding: 0 0 0 0">
                                                <table cellpadding="0" cellspacing="0" border="0"
                                                width="100%">
                                                <tbody>
                                                <tr>
                                                <td align="left"
                                                style="font-family: Arial, Helvetica, 'Helvetica Neue', sans-serif; color: #707070; font-size: 14px; padding-bottom: 5px">
                                               10.00 
                                              </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                <tr>
                                                <td style="color: #ffffff; line-height: 10px">
                                                <img src="/invoice/Invoice_White_line.png"
                                                style="width: 100%" class="CToWUd" />
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                <table bgcolor="#eeeeee" border="0" cellpadding="0" cellspacing="0" width="100%">
                                                <tbody>
                                                <tr>
                                                <td bgcolor="#EEEEEE" align="center" style="padding: 0 15px 20px 15px">
                                                <table border="0" cellpadding="0" cellspacing="0" width="660">
                                                <tbody>
                                                <tr>
                                                <td
                                                style="background-color: #ffffff; padding: 0 15px 0px 15px; border-bottom-left-radius: 4px; border-bottom-right-radius: 4px;">
                                                <table align="left" width="100%" border="0" cellspacing="0" cellpadding="0">
                                                <tbody>
                                                <tr>
                                                <td align="left" style="
                                                font-size: 13px;
                                                font-family: Arial, Helvetica, 'Helvetica Neue', sans-serif;
                                                color: #777777;
                                                padding-top: 16px;
                                                padding-bottom: 10px;
                                                padding-left: 16px;
                                                border-top: 1px solid #eeeeee;
                                                text-align: center;
                                                ">
                                                <!-- <a style="text-decoration: none; color: #5d93bb"
                                                href="/privacy" rel="noreferrer"
                                                target="_blank">
                                                Privacy Policy</a>
                                                |
                                                <a style="text-decoration: none; color: #5d93bb"
                                                href="/terms" rel="noreferrer"
                                                target="_blank">
                                                Terms</a>
                                                <a style="text-decoration: none; color: #000000"
                                                rel="noreferrer"
                                                target="_blank">

                                                | Questions? Email us at 
                                                </a> -->
                                                </td>

                                                </tr>
                                                <tr>
                                                <td style="color: #ffffff; line-height: 9px"><img
                                                src="/invoice/Invoice_White_line.png"
                                                style="width: 100%" class="CToWUd" /></td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                                </div>                                          
                                            <tr v-if="form.show_button">
                                                <td>
                                                    <div style="display: flex;" class="mt-3" v-if="notification && notification.button_name">
                                                      <BLink :href="notification.button_url" target="_blank" class="btn btn-primary">{{ notification.button_name }}</BLink>
                                                    </div>
                                                    <div style="display: flex;" class="mt-3" v-else>
                                                      <BLink href="#" target="_blank" class="btn btn-primary">Log In</BLink>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr v-if="form.show_img">
                                                <td class="content-block">
                                                    <div style="display: flex; align-items: center;" v-if="notification && notification.banner_img">
                                                        <img class="img-fluid mt-3" width="600" height="300" :src="notification.banner_img" alt="">
                                                  
                                                    </div>
                                                    <div style="display: flex; align-items: center;" v-else>
                                                        <img class="img-fluid mt-3" width="600" height="300" src="@assets/images/profile-bg.jpg" alt="">
                                                  
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody></table>
                                    </td>
                                </tr>
                            </tbody></table>

                              <div class="p-3 mb-4 mt-3">
                                <div class="row g-2" >
                                  <p v-if="notification.footer_content" class="text-center">{{ stripHtmlTags(notification.footer_content ) }}</p>
                                  <p v-else>If you have any questions or need assistance, feel free to contact us</p>
                                  <!-- <hr> -->
                                  <div style="text-align: center; margin: 0px auto;">
                              
                                    <!-- <ul style="list-style: none;display: flex; padding-top: 20px;padding-left: 0px; margin-bottom: 20px; font-family: 'Roboto', sans-serif;justify-content:center;font-weight: bold;">
                                        <li>
                                            <a href="#" style="color: #495057;">Subscribe</a>
                                        </li>
                                        <div style="border-right: 2px; border-right-style: solid; border-right-color:#98a6ad;margin-right: 15px;margin-left: 15px;"></div>
                                        <li>
                                            <a href="#" style="color: #495057;">Privacy</a>
                                        </li>
                                        <div style="border-right: 2px; border-right-style: solid; border-right-color:#98a6ad;margin-right: 15px;margin-left: 15px;"></div>
                                        <li>
                                            <a href="#" style="color: #495057;">Web</a>
                                        </li>
                                    </ul> -->
                                    <div class="d-flex align-items-center justify-content-center mt-5">
                                      <div v-if="form.show_fbicon">
                                        <a :href="footer.footer_fblink">
                                          <img class="img-fluid me-2" src="@assets/images/facebook.png" alt="" width="24">
                                        </a>                                       
                                      </div>
                                      <div v-if="form.show_instaicon">
                                        <a :href="footer.footer_instalink">
                                          <img class="img-fluid me-2" src="@assets/images/instagram.png" alt="" width="24">
                                        </a>  
                                      </div>
                                      <div v-if="form.show_twittericon">
                                        <a :href="footer.footer_twitterlink">
                                          <img class="img-fluid me-2" src="@assets/images/twitter.png" alt="" width="24">
                                        </a>
                                      </div>
                                      <div v-if="form.show_linkedinicon">
                                        <a :href="footer.footer_linkedinlink">
                                          <img class="img-fluid me-2" src="@assets/images/linkedin.png" alt="" width="24">
                                        </a>    
                                      </div>
                                    </div>
                                    <div class="mt-4">
                                      <p v-if="notification.footer_copyrights" style="font-family: 'Roboto', sans-serif; font-size: 14px;color: #98a6ad; margin: 0px;">
                                        {{ stripHtmlTags(notification.footer_copyrights) }}
                                      </p>
                                      <p v-else style="font-family: 'Roboto', sans-serif; font-size: 14px;color: #98a6ad; margin: 0px;">
                                        2021 Misoftwares & Rights Reserved
                                      </p>
                                    </div>

                                </div>   
                                </div>
                            </div>                            
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