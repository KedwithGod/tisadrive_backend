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
import ImageUpload from '@/Components/ImageUpload.vue';

export default {
  components: {
    Layout,
    PageHeader,
    ImageUpload,
    Head,
    Pagination,
    Multiselect,
    FormValidation
  },
  props: {
    successMessage: String,
    alertMessage: String,
    country:Object,
  },
  setup(props) {
    const { t } = useI18n();
    const form = useForm({
      name: props.country ? props.country.name || "" : "",
      dial_code: props.country ? props.country.dial_code || "" : "",
      code: props.country ? props.country.code || "" : "",
      flag: props.country ? props.country.flag || "" : "",
      currency_code: props.country ? props.country.currency_code || "" : "",
      dial_min_length: props.country ? props.country.dial_min_length || "" : "",
      dial_max_length: props.country ? props.country.dial_max_length || "" : "",
      currency_symbol: props.country ? props.country.currency_symbol || "" : "",
      currency_name: props.country ? props.country.currency_name || "" : "",
    });
    const validationRules = {
      name: { required: true },
      dial_code: { required: true },
      flag: { required: true },
      code: { required: true },
      currency_code: { required: true },
      currency_symbol: { required: true },
      dial_min_length: { required: true },
      dial_max_length: { required: true },
    };
    const validationRef = ref(null);
    const errors = ref({});
    const successMessage = ref(props.successMessage || '');
    const alertMessage = ref(props.alertMessage || '');

    const dismissMessage = () => {
      successMessage.value = "";
      alertMessage.value = "";
    };


    const handleImageSelected = (file, fieldName) => {
        form[fieldName] = file;
      };

      const handleImageRemoved = (fieldName) => {
        form[fieldName] = null;
      };

    const handleSubmit = async () => {
      errors.value = validationRef.value.validate();
      if (Object.keys(errors.value).length > 0) {
        return;
      }
      try {
        let response;
        if (props.country && props.country.id) {
          response = await axios.post(`/country/update/${props.country.id}`, form.data(),{
            headers: {
              'Content-Type': 'multipart/form-data',
            },
          });
        } else {
          response = await axios.post('/country/store', form.data(),{
            headers: {
              'Content-Type': 'multipart/form-data',
            },
          });
        }
        if (response.status === 201) {
          successMessage.value = t('country_created_successfully');
          form.reset();
          router.get('/country');
        } else {
          alertMessage.value = t('failed_to_create_country');
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          errors.value = error.response.data.errors;
        } else if (error.response && error.response.status === 403) {
          alertMessage.value = error.response.data.alertMessage;
          setTimeout(()=>{
            router.get('/country');
          },5000)
        } else {
          console.error(t('error_creating_country'), error);
          alertMessage.value = t('failed_to_create_country');
        }
      }
    };


    return {
      form,
      successMessage,
      alertMessage,
      handleSubmit,
      handleImageSelected,
      handleImageRemoved,
      dismissMessage,
      selectedCountry: ref(null),
      selectedTimezone: ref(null),
      validationRules,
      validationRef,
      errors,
    };
  }
};

</script>

<template>
  <Layout>

    <Head :title="$t('country')" />
    <PageHeader :title="country ? $t('edit') : $t('create')" :pageTitle="$t('country')" pageLink="/country"/>
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
                    </div>
                  </BCol>
                </BRow>
                <div class="row">
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="name" class="form-label">{{ $t("name") }}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" class="form-control" :placeholder="$t('enter_name')" id="name"
                        v-model="form.name" />
                      <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="dial_code" class="form-label">{{ $t("dial_code") }}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" class="form-control" :placeholder="$t('enter_dial_code')" id="dial_code"
                        v-model="form.dial_code" />
                      <span v-for="(error, index) in errors.dial_code" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div> 
                </div>

                <div class="row">
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="code" class="form-label">{{ $t("code") }}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" class="form-control" :placeholder="$t('enter_code')" id="code"
                        v-model="form.code" />
                      <span v-for="(error, index) in errors.code" :key="index" class="text-danger">{{ error }}</span>
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
                </div>
               
                <div class="row">
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
                      <label for="currency_name" class="form-label">{{ $t("currency_name") }}
                      </label>
                      <input type="text" class="form-control" :placeholder="$t('enter_currency_name')" id="currency_name"
                        v-model="form.currency_name" />
                      <span v-for="(error, index) in errors.currency_name" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div> 
                </div>

                <div class="row">
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="dial_min_length" class="form-label">{{ $t("dial_min_length") }}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="number" class="form-control" :placeholder="$t('enter_dial_min_length')" id="dial_min_length"
                        v-model="form.dial_min_length" />
                      <span v-for="(error, index) in errors.dial_min_length" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div> 
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="dial_max_length" class="form-label">{{ $t("dial_max_length") }}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="number" class="form-control" :placeholder="$t('enter_dial_max_length')" id="dial_max_length"
                        v-model="form.dial_max_length" />
                      <span v-for="(error, index) in errors.dial_max_length" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div> 
                </div>
                
                <div class="row">
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="flag" class="form-label d-flex">{{$t("flag")}} 
                        <span class="text-danger">*</span>
                      </label>
                      <ImageUpload  :imageType="'types'" :initialImageUrl="form.flag"   :flexStyle="'0 0 calc(20% - 20px)'" :aspectRatio="'5 / 5'"   
                        @image-selected="(file) => handleImageSelected(file, 'flag')" @image-removed="() => handleImageRemoved('flag')"   @change="onFileChange">
                      </ImageUpload>
                      <span v-for="(error, index) in errors.flag" :key="index" class="text-danger">
                        {{ error }}
                      </span>    
                    </div>
                  </div>
                  <div class="col-lg-12">
                    <div class="text-end">
                      <button type="submit" class="btn btn-primary"> {{ country ? $t('update') : $t('save')}}</button>
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
