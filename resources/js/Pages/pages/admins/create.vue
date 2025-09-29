<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import { ref, computed } from "vue";
import axios from "axios";
import imageUpload from "@/Components/widgets/imageUpload.vue";
import Multiselect from "@vueform/multiselect";
import FormValidation from "@/Components/FormValidation.vue";
import { useI18n } from 'vue-i18n';
import ImageUpload from '@/Components/ImageUpload.vue';

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
    app_for: String,
    countries: Array,
    roles: Array,
    role: Array,
    admin: Array,
    serviceLocations: Array,
    validate: Function,
    category: Array,
  },
  setup(props) {
    const { t } = useI18n();
    // const category = ref(props.category);
    const form = useForm({
      country: props.admin ? props.admin.country || "" : "",
      service_location_id: props.admin ? props.admin.service_location_id || "" : "",
      role: props.admin ? props.role.slug || "" : "",
      mobile: props.admin ? props.admin.mobile || "" : "",
      name: props.admin ? props.admin.first_name || "" : "",
      email: props.admin ? props.admin.email || "" : "",
      address: props.admin ? props.admin.address || "" : "",
      state: props.admin ? props.admin.state || "" : "",
      city: props.admin ? props.admin.city || "" : "",
      pincode: props.admin ? props.admin.pincode || "" : "",
      password: props.admin ? props.admin.password || "" : "",
      confirm_password: props.admin ? props.admin.password || "" : "",
      // profilePictureFile: null,
      profile_picture: props.admin ? props.admin.profile_picture || "" : "",
      // category_type: props.admin ? props.admin.category_type.split(',') || [] : [],
      // category_type: props.admin ? props.admin.category_type.split(',') || [] : [],

    });

    const isDispatcher = computed(() => {
        return form.role === 'dispatcher' || form.role === 'super-admin';
    });
    const validationRules = {
      name: { required: true },
      email: { required: true },
      address: { required: true },
      role: { required: true },
      state: { required: true },
      city: { required: true },
      country: { required: true },
      service_location_id: { required: !isDispatcher },
      confirm_password: { required: !props.admin, sameAs: "password" },
      pincode: { required: true },
      // profile_picture : {required: true}
      // category_type: { required: true },
    };

    if(props.admin && props.app_for == 'demo') {
        form.mobile = "**********";
        form.email = "**********";
    }
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

      const formData = new FormData();
            for (const key in form) {
                if (key === 'iconFile' && form[key]) {
                    formData.append('profile_picture', form[key]);
                } else {
                    formData.append(key, form[key]);
                }
            }

      try {
        let response;
        if (props.admin && props.admin.id) {
          response = await axios.post(`/admins/update/${props.admin.id}`, formData);
        } else {
          response = await axios.post('store', formData);
        }
        if (response.status === 201) {
          successMessage.value = t('admin_created_successfully');
          form.reset();
          router.get('/admins');
        } else {
          alertMessage.value = t('failed_to_create_admin');
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          errors.value = error.response.data.errors;
        } else {
          console.error(t('error_creating_admin'), error);
          alertMessage.value = t('failed_to_create_admin');
        }
      }
    };

    const onFileChange = (event) => {
      const file = event.target.files[0];
      form.profilePictureFile = file;
      if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
          profilePictureUrl.value = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    };

    const profilePictureUrl = ref('');

    const imageSrc = computed(() => {
      return form.profilePictureFile ? profilePictureUrl.value : (props.admin?.profile_picture ? props.admin.profile_picture : '/default-profile.jpeg');
    });

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
      isDispatcher,
      onFileChange,
      imageSrc,
      profilePictureUrl,
      handleImageSelected,
      handleImageRemoved ,
      // category,
    };
  },
  methods: {
    validateEmail() {
      if (!this.form.email) {
        this.errors.email = "Email is required";
      } else if (!this.isValidEmail(this.form.email)) {
        this.errors.email = "Enter a valid email address";
      } else {
        this.errors.email = "";
      }
    },
    isValidEmail(email) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return emailRegex.test(email);
    },
  },
};
</script>


<template>
  <Layout>

    <Head title="Admin" />
    <PageHeader :title="admin? $t('edit') : $t('create')" :pageTitle="$t('admins')" pageLink="/admins"/>
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
                      <label for="role" class="form-label">{{$t("select_role")}}
                        <span class="text-danger">*</span>
                      </label>
                      <select id="role" class="form-select" v-model="form.role">
                        <option disabled value="">{{$t("select")}}</option>
                        <option v-for="role in roles" :key="role.slug" :value="role.slug">
                          {{ role.name }}
                        </option>
                      </select>
                      <span v-if="errors.role" class="text-danger">{{ errors.role }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="service_location_id" class="form-label">{{$t("select_area")}}
                        <span v-if="!isDispatcher" class="text-danger">*</span>
                      </label>
                      <select id="service_location_id" class="form-select" v-model="form.service_location_id">
                        <option disabled value="">{{$t("select")}}</option>
                        <option v-for="location in serviceLocations" :key="location.id" :value="location.id">
                          {{ location.name }}
                        </option>
                      </select>
                      <span v-if="errors.service_location_id" class="text-danger">{{ errors.service_location_id }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="name" class="form-label">{{$t("name")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" class="form-control" name="name" :placeholder="$t('enter_name')" id="name"  v-model="form.name"
                      />
                      <span v-if="errors.name" class="text-danger">{{ errors.name }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="address" class="form-label">{{$t("address")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" class="form-control" :placeholder="$t('enter_address')" name="address" id="address"  v-model="form.address"
                      />
                      <span v-if="errors.address" class="text-danger">{{ errors.address }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="mobile" class="form-label">{{$t("mobile_number")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" name="mobile" class="form-control" :placeholder="$t('enter_mobile_number')" id="mobile"  v-model="form.mobile"
                      />
                      <span v-if="errors.mobile" class="text-danger">{{ errors.mobile }}</span>
                    </div>
                  </div> 
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="email" class="form-label">{{$t("email")}}
                        <span class="text-danger">*</span>  
                      </label>
                      <input type="email" name="email" class="form-control" :placeholder="$t('enter_email')" id="email"  v-model="form.email" @blur="validateEmail"
                      />
                      <span v-if="errors.email" class="text-danger">{{ errors.email }}</span>
                    </div>
                  </div>
                  <div v-if="!admin" class="col-sm-6">
                    <div class="mb-3">
                      <label for="password" class="form-label">{{$t("password")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="password"  name="password" class="form-control" :placeholder="$t('enter_password')" id="password"  v-model="form.password"
                      />
                      <span v-if="errors.password" class="text-danger">{{ errors.password }}</span>
                    </div>
                  </div> 
                  <div v-if="!admin" class="col-sm-6">
                    <div class="mb-3">
                      <label for="confirm_password" class="form-label">{{$t("confirm_password")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="password"  name="confirm_password" class="form-control" :placeholder="$t('confirm_password')" id="confirm_password"  v-model="form.confirm_password" 
                      />
                      <span v-if="errors.confirm_password" class="text-danger">{{ errors.confirm_password }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="country" class="form-label">{{$t("select_country")}}
                        <span class="text-danger">*</span>
                      </label>
                      <select id="country"  name="country" class="form-select" v-model="form.country">
                        <option disabled value="">{{$t("select")}}</option>
                        <option v-for="country in countries" :key="country.id" :value="country.id">
                          {{ country.name }}
                        </option>
                      </select>
                      <span v-if="errors.country" class="text-danger">{{ errors.country }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="state" class="form-label">{{$t("state")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" name="state" class="form-control" :placeholder="$t('enter_state')" id="state"  v-model="form.state"
                      />
                      <span v-if="errors.state" class="text-danger">{{ errors.state }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="city" class="form-label">{{$t("city")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" name="city" class="form-control" :placeholder="$t('enter_city')" id="city"  v-model="form.city"
                      />
                      <span v-if="errors.city" class="text-danger">{{ errors.city }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="pincode" class="form-label">{{$t("postal_code")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="number"  name="pincode"  class="form-control" :placeholder="$t('enter_postal_code')" id="pincode"  v-model="form.pincode"
                      />
                      <span v-if="errors.pincode" class="text-danger">{{ errors.pincode }}</span>
                    </div>
                  </div>
                  <!-- <div class="col-sm-6">
                  <div class="mb-3 mt-3">
                          <label for="select_categories" class="form-label">{{$t("categories")}}
                          </label>
                          <Multiselect
                          mode="tags" 
                          id="select_categoires"
                          v-model="form.category_type"
                          :options="category.map(categories => ({value:categories.category,label:categories.category}))"
                          multiple
                          :close-on-select="false"
                          :searchable="true"
                          :create-option="false"
                          :placeholder="$t('select_categoires')"
                          />
                          <span v-for="(error, index) in errors.category_type" :key="index" class="text-danger">{{ error }}</span>
                      </div>
                  </div> -->
                  <!-- <div class="col-sm-6"></div> -->
                  <!-- <div v-if="imageSrc">
                    <img :src="imageSrc" alt="driver Profile" style="max-width: 100px; max-height: 100px;" />
                  </div> -->
                  <div class="row">
                  <div class="col-sm-6">
                    <!-- <image-upload id="profilePictureInput" @change="onFileChange" v-model="form.profilePictureFile" :src="profilePictureUrl"></image-upload> -->
                    <ImageUpload  :imageType="'user-profile'" :initialImageUrl="form.profile_picture"   :flexStyle="'0 0 calc(50% - 20px)'" :aspectRatio="'5 / 5'"   
                      @image-selected="(file) => handleImageSelected(file, 'profile_picture')" @image-removed="() => handleImageRemoved('profile_picture')"  id="profilePictureInput" @change="onFileChange">
                      </ImageUpload>
                    <span v-if="errors.profilePictureFile" class="text-danger">{{ errors.profilePictureFile }}</span>
                  </div>  
                </div>        
                  <div class="col-lg-12">
                    <div class="text-end">
                      <button type="submit" class="btn btn-primary"> {{ admin ? $t('update') : $t('save') }}</button>
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
