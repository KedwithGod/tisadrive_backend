<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import { ref } from "vue";
import axios from "axios";
import imageUpload from "@/Components/widgets/imageUpload.vue";

import Multiselect from "@vueform/multiselect";
import FormValidation from "@/Components/FormValidation.vue";
import ImageUpload from '@/Components/ImageUpload.vue';
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
    ImageUpload
  },
  props: {
    successMessage: String,
    alertMessage: String,
    serviceLocations: Object,
    notification: Object,
    validate: Function,
  },
  setup(props) {
    // console.log(props.serviceLocation);
    const { t } = useI18n();
    const form = useForm({
      service_location_id: props.notification ? props.notification.service_location_id || "" :'',
      send_to: props.notification ? props.notification.send_to || "" :'',
      title: props.notification ? props.notification.title || "" :'',
      message: props.notification ? props.notification.body || "" :'',
      image: props.notification ? props.notification.image || "" : "",
    });
    const validationRules = {
      service_location_id: { required: true },
      send_to: { required: true },
      title: { required: true },
      message: { required: true },
    };
    const validationRef = ref(null);
    const errors = ref({});
    const successMessage = ref(props.successMessage || '');
    const alertMessage = ref(props.alertMessage || '');

    const notification = ref(props.notification);

    const dismissMessage = () => {
      successMessage.value = "";
      alertMessage.value = "";
    };


    const onFileChange = (event) => {
        const file = event.target.files[0];
        form.image = file;
        if (file) {
            const reader = new FileReader();
            reader.readAsDataURL(file);
        }
    };
    const imageSrc = ref(props.notification ? props.notification.image : null);

    const handleSubmit = async () => {
      errors.value = validationRef.value.validate();
      if (Object.keys(errors.value).length > 0) {
        return;
      }
      const formData = new FormData();
            for (const key in form) {
                if (key === 'iconFile' && form[key]) {
                    formData.append('image', form[key]);
                } else {
                    formData.append(key, form[key]);
                }
            }
      try {
        let response;
          response = await axios.post('send-push', formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
        });

      if (response.status === 201) {
          successMessage.value = t('push_notifictions_send_successfully');
          form.reset();
          router.get('/push-notifications');
        } else {
          alertMessage.value = t('failed_to_send_push_notifications');
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          errors.value = error.response.data.errors;
        } else {
          console.error(t('error_creating_service_location'), error);
          alertMessage.value = t('failed_to_send_push_notifications_catch');
        }
      }

    };

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
      onFileChange,
      imageSrc,
      notification,
      errors,
      handleImageSelected,
      handleImageRemoved 
    };
  }
};
</script>

<template>
  <Layout>

    <Head title="Push Notifications" />
    <PageHeader :title="$t('create')" :pageTitle="$t('send_notifications')" pageLink="/push-notifications" />
    <BRow>
      <BCol lg="12">
        <BCard no-body id="tasksList">
          <BCardHeader class="border-0"></BCardHeader>
          <BCardBody class="border border-dashed border-end-0 border-start-0">

            <form @submit.prevent="handleSubmit" enctype="multipart/form-data">
              <FormValidation :form="form" :rules="validationRules" ref="validationRef">
                <div class="row">
                  <div class="col-sm-6">
                    <div class="mb-3">
                        <label for="service_location" class="form-label">{{$t("service_location")}}
                          <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="service_location" v-model="form.service_location_id">
                            <option value="" disabled>{{$t("select_service_location")}}</option>
                            <option v-for="location in serviceLocations" :key="location.id" :value="location.id">{{ location.name }}</option>
                        </select>
                        <span v-for="(error, index) in errors.service_location_id" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="send_to" class="form-label">{{$t("send_to")}}
                        <span class="text-danger">*</span>
                      </label>
                      <select id="send_to" class="form-select" name="send_to"  v-model="form.send_to">
                        <option disabled value="">{{$t("select")}}</option>
                        <option  value="driver">{{$t("driver")}}</option>
                        <option  value="user">{{$t("user")}}</option>
                        <option  value="owner">{{$t("owner")}}</option>
                      </select>
                      <span v-for="(error, index) in errors.send_to" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="push_title" class="form-label">{{$t("push_title")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" class="form-control" :placeholder="$t('enter_push_title')" id="push_title" 
                      v-model="form.title"/>
                      <span v-for="(error, index) in errors.title" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>    
                  <div class="col-12">
                    <div class="mb-3">
                      <label for="message" class="form-label">{{$t("message")}}
                        <span class="text-danger">*</span>
                      </label>
                      <textarea type="text" class="form-control" :placeholder="$t('enter_message')" id="message" 
                      v-model="form.message"/>
                      <span v-for="(error, index) in errors.message" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>  
                   <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="select_timezone" class="form-label d-flex">{{$t("notification_banner")}}<span><h5 class="text-muted mt-1 mb-0 fs-13">(320px x 320px)</h5></span></label>
                      <ImageUpload  :imageType="'push-notification'" :initialImageUrl="form.image"   :flexStyle="'0 0 calc(50% - 20px)'" :aspectRatio="'5 / 5'"   
                      @image-selected="(file) => handleImageSelected(file, 'image')" @image-removed="() => handleImageRemoved('image')" >
                      </ImageUpload>
                      <span v-if="errors.image" class="text-danger">{{ errors.image }}</span>
                    </div>  
                  </div>           
                  <div class="col-lg-12">
                    <div class="text-end">
                      <button type="submit" class="btn btn-primary"> {{ notification ? $t('update') : $t('save') }}</button>
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
