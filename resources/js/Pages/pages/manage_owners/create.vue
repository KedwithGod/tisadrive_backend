<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import { ref,computed,reactive } from "vue";
import axios from "axios";
import imageUpload from "@/Components/widgets/imageUpload.vue";
import Lottie from "@/Components/widgets/lottie.vue";
import Multiselect from "@vueform/multiselect";
import FormValidation from "@/Components/FormValidation.vue";
import { useI18n } from 'vue-i18n';


export default {
  components: {
    Layout,
    PageHeader,
    Head,
    Multiselect,
    FormValidation,
    imageUpload,
    lottie: Lottie,
  },
  props: {
    successMessage: String,
    alertMessage: String,
    app_for: String,
    countries: Array,
    default_dial_code: String,
    default_flag: String,
    default_country_id: Number,
    owner: Object,
    serviceLocation: Object,
    validate: Function, // Define the prop to receive the method
  },
  setup(props) {
    const { t } = useI18n();
    const serviceLocation = props.serviceLocation;
    const form = useForm({
      company_name: props.owner ? props.owner.company_name : null,
      name: props.owner ? props.owner.name : null,
      email: props.owner ? props.owner.email : null,
      mobile: props.owner ? props.owner.mobile : null,
      password: null,
      confirm_password: null,
      transport_type: props.owner ? props.owner.transport_type : "",
      service_location_id: props.owner ? props.owner.service_location_id : serviceLocation.id,
      country: props.owner ? props.owner.user.country || props.default_country_id : props.default_country_id, // Assuming country is an object with an ID
    });
    const searchQuery = ref('');
    const selectedCountry = ref({
        dial_code: props.default_dial_code || '',
        flag: props.default_flag || ''
    });
    const filteredCountries = computed(() => {
        return props.countries.filter(country =>
            country.name.toLowerCase().includes(searchQuery.value.toLowerCase())
        );
    });

    const selectCountry = (country) => {
        if (country) {
            selectedCountry.value = country;
            form.country = country.id; // Set form.country to the selected country's id
        }
    };
    const validationRules = {
      company_name: { required: true },
      name: { required: true },
      mobile: { required: true, regex: /^\d+$/ },
      email: { required: true, email: true },
      password: { required: !props.owner },
      confirm_password: { required: !props.owner, sameAs: "password" },
      transport_type: { required: true },
      service_location_id: { required: true },
    };
    const validationRef = ref(null);
    const errors = ref({});
    const successMessage = ref(props.successMessage || '');
    const alertMessage = ref(props.alertMessage || '');
    const isButtonDisabled = ref(false);

    const dismissMessage = () => {
      successMessage.value = "";
      alertMessage.value = "";
    };

    if(props.app_for == "demo"){
        form.mobile = "**********";
        form.email = "**********";
    }
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(today.getDate() + 1);
    const rangeDateconfig = {
      mode: "range",
      minDate: tomorrow, // Set minimum date to tomorrow
      dateFormat: 'Y-m-d', // Adjust the date format as needed
    };
    const checkEmail = async () => {
      try {
        if (form.email && (!props.owner || form.email !== props.owner.email)) {
          const emailCheckResponse = await axios.get(`/manage-owners/check-email/${form.email}${props.owner ? `/${props.owner.id}` : ''}`);
            if (emailCheckResponse.data.exists) {
                return true ;
            }else{
              return false;
            }
        }
        return false;
      }catch (error) {
        console.error(t('error_handling_form_submission'), error);
        if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors;
        } else {
            console.error(t('an_unexpected_error_occurred'), error);
        }
        return true;
      }
    }
    const checkMobile = async () => {
      try {
        if (form.mobile && (!props.owner || form.mobile !== props.owner.mobile)) {
          const mobileCheckResponse = await axios.get(`/manage-owners/check-mobile/${form.mobile}${props.owner ? `/${props.owner.id}` : ''}`);
            if (mobileCheckResponse.data.exists) {
                return true;
            }else{
              return false;
            }
        }
        return false;
      }catch (error) {
        console.error(t('error_handling_form_submission'), error);
        if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors;
        } else {
            console.error(t('an_unexpected_error_occurred'), error);
        }
        return true;
      }
    }
    const handleSubmit = async () => {
      
      const [mobileExists, emailExists] = await Promise.all([checkMobile(), checkEmail()]);
      errors.value = {};
      if (mobileExists) {
        errors.value.mobile = t('mobile_number_already_exists');
      }else{
        delete errors.value.mobile;
      }
      if (emailExists) {
        errors.value.email = t('email_already_exists');
      } else {
        delete errors.value.email;
      }
      if (form.password && form.password !== form.confirm_password) {
        Object.assign(errors, {password:[t('password_mismatch')]});
      } else {
        delete errors.value.password;
      }
      const validationErrors = validationRef.value.validate();
      errors.value = { ...errors.value, ...validationErrors };

      if (errors.value && Object.keys(errors.value).length > 0) {
        return;
      }
      try {
        let response;        
        isButtonDisabled.value = true;
        if (props.owner && props.owner.id) {
          response = await axios.post(`/manage-owners/update/${props.owner.id}`, form.data());
        } else {
          response = await axios.post('/manage-owners/store', form.data());
        }
        if (response.status === 201) {
          successMessage.value = t('owner_created_successfully');
          form.reset();
          router.get('/manage-owners');
        } else {
          alertMessage.value = t('failed_to_create_owner');
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          errors.value = error.response.data.errors;
        } else {
          console.error(t('error_creating_owner'), error);
          alertMessage.value =t('failed_to_create_owner_catch');
        }
      }

    };
    return {
      form,
      successMessage,
      alertMessage,
      handleSubmit,
      selectCountry,
      filteredCountries,
      selectedCountry,
      searchQuery,
      dismissMessage,
      validationRules,
      validationRef,
      errors,
      isButtonDisabled
    };
  },
};


</script>

<template>
  <Layout>

    <Head title="Manage Owners" />
    <PageHeader :title="owner ? $t('edit') : $t('create')" :pageTitle="$t('manage_owners')" pageLink="/manage-owners" />
    <BRow>
      <BCol lg="12">
        <BCard no-body id="tasksList">
          <BCardHeader class="border-0"></BCardHeader>
          <BCardBody class="border border-dashed border-end-0 border-start-0 form-steps">
            
            <form  @submit.prevent="handleSubmit" class="form-steps"   >

  <input type="text" name="fakeusernameremembered" style="display:none;">
  <input type="password" name="fakepasswordremembered" style="display:none;">
            <FormValidation :form="form" :rules="validationRules" ref="validationRef">
                  <div>
                    <div class="mb-4">
                      <div>
                        <h5 class="mb-1">{{$t("basic_information")}}</h5>
                        <p class="text-muted">{{$t("fill_all_information_as_below")}}</p>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-sm-6">
                        <div class="mb-3">
                          <label for="company_name" class="form-label">{{$t("company_name")}}
                            <span class="text-danger">*</span>
                          </label>
                          <input type="text" class="form-control" :placeholder="$t('enter_company_name')" id="company_name" v-model="form.company_name"/>
                          <span v-for="(error, index) in errors.company_name" :key="index" class="text-danger">{{ error }}</span>
                        </div>
                      </div>
                      <div class="col-sm-6">
                        <div class="mb-3">
                          <label for="name" class="form-label">{{$t("name")}}
                            <span class="text-danger">*</span>
                          </label>
                          <input type="text" class="form-control" :placeholder="$t('enter_name')" id="name" v-model="form.name"/>
                          <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                        </div>
                      </div>
                      <div class="col-sm-6">
                          <div>
                              <label class="form-label">{{$t("mobile")}}
                                  <span class="text-danger">*</span>
                              </label>
                              <div class="input-group" data-input-flag="">
                                  <button class="btn btn-light border" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                      <img :src="selectedCountry.flag" alt="flag" height="20" class="country-flagimg rounded">
                                      <span class="ms-2 country-codeno">{{ selectedCountry.dial_code }}</span>
                                  </button>
                                  <input type="text" class="form-control rounded-end flag-input" v-model="form.mobile" :placeholder="$t('enter_number')">
                                  <div class="dropdown-menu w-100">
                                      <div class="p-2 px-3 pt-1 searchlist-input">
                                          <input type="text" class="form-control form-control-sm border search-countryList" :placeholder="$t('search_country_name_or_country_code')" v-model="searchQuery">
                                      </div>
                                      <ul class="list-unstyled dropdown-menu-list mb-0">
                                          <li v-for="country in filteredCountries" :key="country.id">
                                              <a href="javascript:void(0);" class="dropdown-item notify-item language py-2" @click="selectCountry(country)">
                                                  <img :src="country.flag" alt="flag" class="me-2 rounded" height="18">
                                                  <span class="align-middle">{{ country.name }} {{ country.dial_code }}</span>
                                              </a>
                                          </li>
                                      </ul>
                                  </div>
                              </div>
                              <span v-if="errors.mobile" class="text-danger">{{ errors.mobile }}</span>
                          </div>
                      </div>
                      <div class="col-sm-6">
                        <div class="mb-3">
                          <label for="email" class="form-label">{{$t("email")}}
                            <span class="text-danger">*</span>
                          </label>
                          <input type="email" class="form-control" :placeholder="$t('enter_email')" id="email" v-model="form.email"/>
                          <span v-for="(error, index) in errors.email" :key="index" class="text-danger">{{ error }}</span>
                        </div>
                      </div>
                      <div v-if="!owner" class="col-sm-6">
                        <div class="mb-3">
                          <label for="password" class="form-label">{{$t("password")}}
                            <span v-if="!owner" class="text-danger">*</span>
                          </label>
                          <input type="password" class="form-control" :placeholder="$t('enter_password')" id="password" v-model="form.password"/>
                          <span v-for="(error, index) in errors.password" :key="index" class="text-danger">{{ error }}</span>
                        </div>
                      </div>
                      <div v-if="!owner" class="col-sm-6">
                        <div class="mb-3">
                          <label for="confirm_password" class="form-label">{{$t("confirm_password")}}
                            <span v-if="!owner" class="text-danger">*</span>  
                          </label>
                          <input type="password" class="form-control" :placeholder="$t('enter_confirm_password')"
                                id="confirm_password" v-model="form.confirm_password"/>
                          <span v-for="(error, index) in errors.confirm_password" :key="index" class="text-danger">{{ error }}</span>
                        </div>
                      </div>
                      <div class="col-sm-6">
                        <div class="mb-3">
                          <label for="select_area" class="form-label">{{$t("select_area")}}</label>
                          <select class="form-control" id="select_area" v-model="form.service_location_id">
                            <option value="" disabled>{{$t("select_area")}}</option>
                            <!-- Assuming serviceLocations is an array of objects with 'id' and 'name' properties -->
                            <option v-for="location in serviceLocation" :key="location.id" :value="location.id">
                              {{ location.name }}
                            </option>
                          </select>
                          <span v-for="(error, index) in errors.service_location_id" :key="index" class="text-danger">{{ error }}</span>
                        </div>
                      </div>
                      <div class="col-sm-6">
                        <div class="mb-3">
                          <label for="select_transport_type" class="form-label">{{$t("select_transport_type")}}
                            <span class="text-danger">*</span>
                          </label>
                          <select id="select_transport_type" class="form-select" v-model="form.transport_type">
                            <option disabled value="">{{$t("select_transport_type")}}</option>
                            <option  value="taxi">{{$t("taxi")}}</option>
                            <option  value="delivery">{{$t("delivery")}}</option>
                            <option  value="both">{{$t("both")}}</option>
                          </select>
                          <span v-for="(error, index) in errors.transport_type" :key="index" class="text-danger">{{ error }}</span>
                        </div>
                      </div>
                      <div class="d-flex align-items-end gap-3 mt-4">
                        <button type="submit" class="btn btn-success btn-label right ms-auto" :disabled="isButtonDisabled">
                          {{$t("submit")}} <i class="ri-check-fill label-icon align-middle fs-16 ms-2"></i>
                        </button>
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
