<script>
import { Link,Head, useForm,router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import dropZone from "@/Components/widgets/dropZone.vue";
import searchbar from "@/Components/widgets/searchbar.vue";
import imageUpload from "@/Components/widgets/imageUpload.vue";
import Swal from "sweetalert2";
import { ref, watch } from "vue";
import axios from "axios";
import { debounce } from 'lodash';
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import CKEditor from "@ckeditor/ckeditor5-vue";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
import useVuelidate from "@vuelidate/core";
import ImageUpload from "@/Components/ImageUpload.vue";
import FormValidation from "@/Components/FormValidation.vue";
import { useI18n } from 'vue-i18n';
export default {
  data() {
    return {
      editor: ClassicEditor,
      editorData: "",
      showBannerImage : true,
      showButton : true,
      showFBIcon: true,
      showInstaIcon: true,
      showTwitterIcon: true,
      showLinkedinIcon: true,
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
    emails: {
      type: Object,
      required: true,
    },
    validate: Function,
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
    const footer = ref(props.emails && props.emails.footer
        ? JSON.parse(props.emails.footer) // Ensure it's parsed to an array
        : [{ 
          footer_content: "", 
          footer_copyrights: "",
          footer_fblink: "" ,
          footer_instalink:"",
          footer_twitterlink:"",
          footer_linkedinlink:""
        }]);

    const form = useForm({
      email_subject: props.emails ? props.emails.email_subject || "" : "",
      logo_img: props.emails ? props.emails.logo_img || "" : "",
      mail_body: props.emails ? props.emails.mail_body || "" : "",
      button_name: props.emails ? props.emails.button_name || "" : "",
      button_url: props.emails ? props.emails.button_url || "" : "",
      banner_img: props.emails ? props.emails.banner_img || "" : "",
      
    });

    const validationRules = {
      email_subject: { required: true }  ,
      logo_img: { required: true } ,
      mail_body : { required: true }  ,
      button_name : { required: true }  ,
      button_url  : { required: true } ,
      banner_img : { required: true }  ,
      // footer_content : { required: true } ,
      // footer_copyrights : { required: true } ,
      // footer_fblink : { required: true } ,
      // footer_instalink : { required: true } ,
      // footer_linkedinlink : { required: true } ,
      // footer_twitterlink : { required: true } ,

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
      errors.value = validationRef.value.validate();
      if (Object.keys(errors.value).length > 0) {
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
        Object.keys(footer).forEach((key, index) => {
          formData.append(`footer[${index}][footer_content]`, footer[key].footer_content);
          formData.append(`footer[${index}][footer_copyrights]`, footer[key].footer_copyrights);
          formData.append(`footer[${index}][footer_fblink]`, footer[key].footer_fblink);
          formData.append(`footer[${index}][footer_instalink]`, footer[key].footer_instalink);
          formData.append(`footer[${index}][footer_twitterlink]`, footer[key].footer_twitterlink);
          formData.append(`footer[${index}][footer_linkedinlink]`, footer[key].footer_linkedinlink);
        });

      console.log(footer.value);
        if (props.emails && props.emails.id) {
          response = await axios.post(`/mail-template/update/${props.emails.id}`, formData, {
            headers: {
              'Content-Type': 'multipart/form-data',
            },
          });

        } else {
          response = await axios.post('/mail-template/store', formData, {
            headers: {
              'Content-Type': 'multipart/form-data',
            },
          });
        }

        if (response.status === 201 ) {
          successMessage.value = t('mail_template_created_successfully');
          form.reset();
          router.get('/mail-template'); 
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
      emails: props.emails,
      iconUrl,
      handleImageSelected,
      handleImageRemoved,
      footer
      
    };
  },
  
  
  methods: {
        stripHtmlTags(content) {
            const parser = new DOMParser();
            const parsedContent = parser.parseFromString(content, 'text/html');
            return parsedContent.body.textContent || "";
            },
    },
    mounted() {
      const storagePath = '/storage/uploads/website/images/';
      this.emails.logo_img = `${storagePath}${this.emails.logo_img}`
      this.emails.banner_img = `${storagePath}${this.emails.banner_img}`
    if (this.emails.footer) {
      try {
        this.parsedFooter = JSON.parse(this.emails.footer);
      } catch (error) {
        console.error(t('error_parsing_footer_content'), error);
      }
    }
  },
};
</script>

<template>
  <Layout>
    <PageHeader :title="emails ? $t('edit') : $t('create')" :pageTitle="$t('template')"  pageLink="/mail-template" />
    <BRow>
      <BCol lg="6">
          <BCard no-body>
            <BCardBody>
              <form @submit.prevent="handleSubmit">
                <FormValidation :form="form" :rules="validationRules" ref="validationRef">
                  <div class="mb-3">
                <label class="form-label" for="subject">{{$t("email_subject")}}</label>
                <input type="text" class="form-control" id="email_subject" :placeholder="$t('enter_subject')"  v-model="form.email_subject" />
                <span v-for="(error, index) in errors.email_subject" :key="index" class="text-danger">{{ error }}</span>
              </div>
              <div class="mb-3">
                <label for="logo" class="form-label d-flex">{{$t("logo")}}
                  <span><h5 class="text-muted mt-1 mb-0 fs-13">(150px x 60px)</h5></span>
                </label>
                <ImageUpload    :flexStyle="'0 0 calc(95% - 20px)'" :aspectRatio="'11 / 2'"  :initialImageUrl="form.logo_img"  
                    @image-selected="(file) => handleImageSelected(file, 'logo_img')" @image-removed="() => handleImageRemoved('logo_img')">
                </ImageUpload>
                <span v-if="form.errors.logo_img" class="text-danger">{{ form.errors.logo_img }}</span>
              </div>
              <div>
                <label>{{$t("body_of_the_mail")}}</label>
                <ckeditor v-model="form.mail_body"  :editor="editor"></ckeditor>
                <span v-for="(error, index) in errors.mail_body" :key="index" class="text-danger">
                  {{ error }}
                </span>
              </div>
              <div class="form-check form-switch form-switch-right form-switch-md mt-4">
                  <label for="radio-toggle-shocade" class="form-label text-dark">{{$t("button_enable")}}</label>
                  <input class="form-check-input code-switcher" type="checkbox" id="radio-toggle-shocade" v-model="showButton">
              </div>
              <div class="row mt-3">
                    <div class="col-lg-6">
                      <div class="mb-3">
                        <label class="form-label" for="button_name">{{$t("button_name")}}</label>
                        <input type="text" class="form-control" id="button_name"
                          :placeholder="$t('enter_name')" v-model="form.button_name" >
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
                  <input class="form-check-input code-switcher" type="checkbox" id="radio-toggle-shocade" v-model="showBannerImage">
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
                  <input type="text" class="form-control" id="footer_content" :placeholder="$t('enter_footer')"  v-model="footer.footer_content" />
                  <span v-for="(error, index) in errors.footer_content" :key="index" class="text-danger">{{ error }}</span>
                </div>
                <div class="mb-3">
                  <label class="form-label" for="footer_copyrights">{{$t("copyrights_content")}}</label>
                  <input type="text" class="form-control" id="footer_copyrights" :placeholder="$t('enter_copyrights')" v-model="footer.footer_copyrights" />
                  <span v-for="(error, index) in errors.footer_copyrights" :key="index" class="text-danger">{{ error }}</span>
                </div>
                <!-- Checkbox Input -->
                <div>
                  <div class="input-group mb-3">                
                    <span class="input-group-text">{{$t("facebook")}}</span>
                      <div class="input-group-text">
                        <input class="form-check-input mt-0" type="checkbox" value="" aria-label="Checkbox for following text input" v-model="showFBIcon">
                      </div>
                      <input type="url" class="form-control" id="footer_fblink" :placeholder="$t('enter_facebook_link')" v-model="footer.footer_fblink">                  
                  </div>
                    <span v-for="(error, index) in errors.footer_fblink" :key="index" class="text-danger">{{ error }}</span>
                </div>              
                <div>
                  <div class="input-group mb-3">                
                    <span class="input-group-text">{{$t("instagram")}}</span>
                      <div class="input-group-text">
                        <input class="form-check-input mt-0" type="checkbox" value="" aria-label="Checkbox for following text input" v-model="showInstaIcon">
                      </div>
                      <input type="url" class="form-control"  id="footer_instalink" :placeholder="$t('enter_instagram_link')" v-model="footer.footer_instalink">                  
                  </div>
                  <span v-for="(error, index) in errors.footer_instalink" :key="index" class="text-danger">{{ error }}</span>
                </div>              
                <div>
                  <div class="input-group mb-3">                
                    <span class="input-group-text">{{$t("twitter")}}</span>
                      <div class="input-group-text">
                        <input class="form-check-input mt-0" type="checkbox" value="" aria-label="Checkbox for following text input" v-model="showTwitterIcon">
                      </div>
                      <input type="url" class="form-control"  id="footer_twitterlink" :placeholder="$t('enter_twitter_link')" v-model="footer.footer_twitterlink">                  
                  </div>
                  <span v-for="(error, index) in errors.footer_twitterlink" :key="index" class="text-danger">{{ error }}</span>
                </div>
                
                <div>
                  <div class="input-group mb-3">                
                    <span class="input-group-text">{{$t("linkedin")}}</span>
                      <div class="input-group-text">
                        <input class="form-check-input mt-0" type="checkbox" value="" aria-label="Checkbox for following text input" v-model="showLinkedinIcon">
                      </div>
                      <input type="url" class="form-control"  id="footer_linkedinlink" :placeholder="$t('enter_linkedin_link')" v-model="footer.footer_linkedinlink">                  
                  </div>
                  <span v-for="(error, index) in errors.footer_linkedinlink" :key="index" class="text-danger">{{ error }}</span>
                </div>
                </div>
              </div>
              <div class="text-end mb-3">
                <button type="submit" class="btn btn-primary">{{ emails ? $t('update') : $t('save') }}</button>
              </div>
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
                                                    <div style="margin-bottom: 15px;" v-if="emails && emails.logo_img" >
                                                        <img :src="emails.logo_img" alt="" height="23" >
                                                    </div>
                                                    <div style="margin-bottom: 15px;" v-else>
                                                        <img src="@assets/images/logo-light.png" alt="" height="23" >
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr v-if=" emails && emails.email_subject">
                                                <td  class="content-block" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 20px; line-height: 1.5; font-weight: 500; vertical-align: top; margin: 0; padding: 0 0 10px;" valign="top">
                                                    {{emails.email_subject}}
                                                </td>
                                            </tr>
                                            <tr v-else>   
                                                <td  class="content-block" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 20px; line-height: 1.5; font-weight: 500; vertical-align: top; margin: 0; padding: 0 0 10px;" valign="top" >
                                                  Hello User
                                                </td>
                                            </tr> 
                                            <tr v-if="emails && emails.mail_body">
                                               {{ stripHtmlTags(emails.mail_body) }}
                                            </tr>
                                            <tr v-else>
                                              <p>Thank you for joining MI Softwares! We are thrilled to have you as a part of our community.</p>   
                                              <p>Our mission is to mobility. We hope that you will find our products\/services to be useful and enjoyable.</p>         
                                              <p>To get started, please take a few moments to explore our website and familiarize yourself with our offerings. If you have any questions or concerns, our customer support team is always here to help.</p>         
                                              <p>We look forward to working with you and providing you with a top-notch experience.</p>         
                                              <p>Best regards, </p>         
                                              <p>MI Softwares</p>
                                            </tr>

                                            <tr v-if="showBannerImage">
                                                <td class="content-block">
                                                    <div style="display: flex; align-items: center;" v-if="emails && emails.banner_img">
                                                        <img class="img-fluid mt-3" width="600" height="300" :src="emails.banner_img" alt="">
                                                  
                                                    </div>
                                                    <div style="display: flex; align-items: center;" v-else>
                                                        <img class="img-fluid mt-3" width="600" height="300" src="@assets/images/profile-bg.jpg" alt="">
                                                  
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr v-if="showButton">
                                                <td>
                                                    <div style="display: flex; align-items: center;" class="mt-5" v-if="emails && emails.button_name">
                                                      <BLink :href="emails.button_url" target="_blank" class="btn btn-primary ms-auto me-auto">{{ emails.button_name }}</BLink>
                                                    </div>
                                                    <div style="display: flex; align-items: center;" class="mt-5" v-else>
                                                      <BLink href="#" target="_blank" class="btn btn-primary ms-auto me-auto">Button</BLink>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody></table>
                                    </td>
                                </tr>
                            </tbody></table>

                            <div class="p-3 bg-light rounded mb-4 mt-5">
                                <div class="row g-2" >
                                  <p v-if="footer.footer_content">{{ stripHtmlTags(footer.footer_content ) }}</p>
                                  <p v-else>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore.</p>
                                  <div style="text-align: center; margin: 0px auto;">
                              
                                    <ul style="list-style: none;display: flex; justify-content: space-evenly; padding-top: 25px;padding-left: 0px; margin-bottom: 20px; font-family: 'Roboto', sans-serif;">
                                        <li>
                                            <a href="#" style="color: #495057;">Subscribe</a>
                                        </li>
                                        <li>
                                            <a href="#" style="color: #495057;">Privacy</a>
                                        </li>
                                        <li>
                                            <a href="#" style="color: #495057;">Web</a>
                                        </li>
                                    </ul>
                                    <div class="row d-flex align-items-center justify-content-center mt-5">
                                      <div class="col" v-if="showFBIcon">
                                        <a :href="footer.footer_fblink">
                                          <img class="img-fluid" src="@assets/images/facebook.png" alt="" width="40">
                                        </a>                                       
                                      </div>
                                      <div class="col" v-if="showInstaIcon">
                                        <a :href="footer.footer_instalink">
                                          <img class="img-fluid" src="@assets/images/instagram.png" alt="" width="40">
                                        </a>  
                                      </div>
                                      <div class="col" v-if="showTwitterIcon">
                                        <a :href="footer.footer_twitterlink">
                                          <img class="img-fluid" src="@assets/images/twitter.png" alt="" width="40">
                                        </a>
                                      </div>
                                      <div class="col" v-if="showLinkedinIcon">
                                        <a :href="footer.footer_linkedinlink">
                                          <img class="img-fluid" src="@assets/images/linkedin.png" alt="" width="40">
                                        </a>    
                                      </div>
                                    </div>
                                    <div class="mt-4">
                                      <p v-if="footer.footer_copyrights" style="font-family: 'Roboto', sans-serif; font-size: 14px;color: #98a6ad; margin: 0px;">
                                        {{ stripHtmlTags(footer.footer_copyrights) }}
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