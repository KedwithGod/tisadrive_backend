<script>
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import axios from 'axios';
import Layout from "@/Layouts/main.vue";
import { Link, Head, useForm, router } from '@inertiajs/vue3';
import PageHeader from "@/Components/page-header.vue";
import Swal from "sweetalert2";
import { ref,onMounted } from "vue";
import { initI18n } from '@/i18n';
import { useI18n } from 'vue-i18n';
import UserWebMenu from "@/Components/UserWebMenu.vue";

export default {
  components: {
    Layout,
    Multiselect,
    flatPickr,
    PageHeader,
    Head,
    Link,
    Swal,
    axios,
    UserWebMenu,
  },

  data() {
    return {
      user: null, // Initialize user data
    };
  },

  // async created() {
  //   const response = await axios.get('/api/users/current'); // Fetch current user data
  //   this.user = response.data; // Assign the fetched user data
  // },

  props: {
    user: Object, // Pass the authenticated user object from the Laravel backend
    successMessage: String,
  },

  setup(props) {
    const { t } = useI18n();

    const form = useForm({
      name: props.user.name || "",
      mobile: props.user.mobile || "",
      email: props.user.email || "",
      gender: props.user.gender || "",
      password: props.user ? props.user.password || "" : "",
      confirm_password: props.user ? props.user.password || "" : "",
      profile_picture: null, // For profile picture upload
    });

    const profilePhotoUrl = ref(
      props.user.profile_picture || '/assets/images/users/user-dummy-img.jpg'
    );

    const errors = ref({});
    const successMessage = ref(props.successMessage || '');

    // Function to handle profile image change
    const onProfileImageChange = (event) => {
      const file = event.target.files[0];
      if (file) {
        form.profile_picture = file; // Add the file to the form data
        // Preview the image before upload
        const reader = new FileReader();
        reader.onload = (e) => {
          profilePhotoUrl.value = e.target.result; // Update the image preview
        };
        reader.readAsDataURL(file);
      }
    };

    // Form validation
    const validateForm = () => {
      const validationErrors = {};
      if (!form.mobile) {
        validationErrors.mobile = t('this_field_is_required');
      }
      return validationErrors;
    };

    // Submit form function
    const handleSubmit = async () => {
      errors.value = validateForm();
      if (Object.keys(errors.value).length > 0) {
        return;
      }

      const formData = new FormData();
      formData.append('name', form.name);
      formData.append('mobile', form.mobile);
      formData.append('email', form.email);
      formData.append('gender', form.gender);
      

      // Append profile_picture only if a file is selected
      if (form.profile_picture) {
        formData.append('profile_picture', form.profile_picture);
      }

      try {
        await form.post(route('user.updateProfile'), {
          data: formData,
          onSuccess: () => {
            Swal.fire({
              icon: 'success',
              title: t('profile_updated_successfully'),
            });           
          },
          onError: (errorResponse) => {
            errors.value = errorResponse;
          },
        });
      } catch (error) {
        console.error(t('an_error_occurred_during_form_submission'), error);
      }
    };

    const handlePasswordSubmit = async () => {

      const formData = new FormData();
      if (form.password !== form.confirm_password) {
        Swal.fire({
          title: 'Error!',
          text: 'Passwords do not match!',
          icon: 'error',
        });
        return;
      }
      formData.append('password', form.password);
      formData.append('confirm_password', form.confirm_password);
      

      // Append profile_picture only if a file is selected
      if (form.profile_picture) {
        formData.append('profile_picture', form.profile_picture);
      }

      try {
        await form.post(route('user.updateProfile'), {
          data: formData,
          onSuccess: () => {
            Swal.fire({
              icon: 'success',
              title: t('password_updated_successfully'),
            });
            form.password = '';
            form.confirm_password = '';
          },
          onError: (errorResponse) => {
            errors.value = errorResponse;
          },
        });
      } catch (error) {
        console.error(t('an_error_occurred_during_form_submission'), error);
      }
    };

    onMounted(async() => {
      await initI18n('en');
    })
    return {
      t,
      form,
      profilePhotoUrl,
      onProfileImageChange,
      handleSubmit,
      errors,
      successMessage,
      handlePasswordSubmit
    };
  },
};
</script>

<template>
  <BCard>
    <Head title="Taxi Ride" />
    <BCardHeader class="border-0">
      <!-- menu Offcanvas -->
      <UserWebMenu :user="user" />
      <!-- menu end -->
    </BCardHeader>

    <BRow>
      <BCol lg="12">
        <BCard no-body id="tasksList">
          <BCardHeader class="border-0">
            <div class="row g-2">
              <div class="col-sm-4"></div>
            </div>
          </BCardHeader>
          <BCardBody class="border border-dashed border-end-0 border-start-0">
            <div class="position-relative mx-n4 mt-n4">
              <div class="profile-wid-bg profile-setting-img">
                <img src="@/assets/images/users/user-dummy-img.jpg" class="profile-wid-img" alt="" />
              </div>
            </div>

            <BRow>
              <BCol xxl="3">
                <BCard no-body class="mt-n5">
                  <BCardBody class="p-4">
                    <div class="text-center">
                      <div class="profile-user position-relative d-inline-block mx-auto mb-4">
                        <img :src="profilePhotoUrl" class="rounded-circle avatar-xl img-thumbnail user-profile-image" alt="user-profile-image" />
                        <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                          <input id="profile-img-file-input" type="file" class="profile-img-file-input" @change="onProfileImageChange" />
                          <label for="profile-img-file-input" class="profile-photo-edit avatar-xs">
                            <span class="avatar-title rounded-circle bg-light text-body">
                              <i class="ri-camera-fill"></i>
                            </span>
                          </label>
                        </div>
                      </div>
                    </div>
                  </BCardBody>
                </BCard>
              </BCol>
              <BCol xxl="9">
                <BCard no-body class="mt-xxl-n5">
                  <BCardBody class="p-4 pt-2">
                    <BTabs nav-class="nav-tabs-custom rounded border-bottom-0">
                      <BTab class="nav-item" :title="$t('personal_details')" active>
                         <form @submit.prevent="handleSubmit">
                            <div v-if="successMessage" class="alert alert-success">{{ successMessage }}</div>
                            <div v-if="Object.keys(errors).length" class="alert alert-danger">
                              <ul>
                                <li v-for="(error, key) in errors" :key="key">{{ error }}</li>
                              </ul>
                            </div>

                            <BRow class="pt-4">
                              <BCol lg="6">
                                <div class="mb-3">
                                  <label for="name" class="form-label">{{$t("name")}}</label>
                                  <input v-model="form.name" type="text" class="form-control" id="name" :placeholder="$t('enter_name')" />
                                </div>
                              </BCol>
                              <BCol lg="6">
                                <div class="mb-3">
                                  <label for="gender">{{ t('gender') }}</label>
                                  <select v-model="form.gender" id="gender" class="form-control">
                                    <option value="" disabled>{{$t("choose_gender")}}</option>
                                    <option value="male">{{$t("male")}}</option>
                                    <option value="female">{{$t("female")}}</option>
                                    <option value="others">{{$t("others")}}</option>
                                  </select>
                                </div>
                              </BCol>
                              <BCol lg="6">
                                <div class="mb-3">
                                  <label for="mobile" class="form-label">{{$t("mobile")}}</label>
                                  <input v-model="form.mobile" type="text" class="form-control" id="mobile" :placeholder="$t('enter_number')" />
                                </div>
                              </BCol>
                              <BCol lg="6">
                                <div class="mb-3">
                                  <label for="email" class="form-label">{{$t("email")}}</label>
                                  <input v-model="form.email" type="email" class="form-control" id="email" :placeholder="$t('enter_email')" />
                                </div>
                              </BCol>                              
                            </BRow>
                            <div class="col-sm-auto ms-auto">
                              <div class="list-grid-nav hstack gap-1 float-end">
                                <button type="submit" class="btn btn-success">{{$t("update")}}</button>
                              </div>
                            </div>
                          </form>
                      </BTab>
                      <BTab class="nav-item" :title="$t('manage_password')">
                         <form @submit.prevent="handlePasswordSubmit">
                            <div v-if="successMessage" class="alert alert-success">{{ successMessage }}</div>
                            <div v-if="Object.keys(errors).length" class="alert alert-danger">
                              <ul>
                                <li v-for="(error, key) in errors" :key="key">{{ error }}</li>
                              </ul>
                            </div>
                            <BRow class="pt-4">
                              <BCol lg="6">
                                <div class="mb-3">
                                  <label for="password" class="form-label">{{$t("password")}}</label>
                                  <input type="password" class="form-control" id="password" v-model="form.password" :placeholder="$t('enter_password')">
                                </div>
                              </BCol>
                              <BCol lg="6">
                                <div class="mb-3">
                                  <label for="confirm_password" class="form-label">{{$t("confirm_password")}}</label>
                                  <input type="password" class="form-control" id="confirm_password" v-model="form.confirm_password" :placeholder="$t('confirm_password')">
                                </div>
                              </BCol>
                              
                            </BRow>
                            <div class="col-sm-auto ms-auto">
                              <div class="list-grid-nav hstack gap-1 float-end">
                                <button type="submit" class="btn btn-success">{{$t("update")}}</button>
                              </div>
                            </div>
                          </form>
                      </BTab>
                    </BTabs>                   
                  </BCardBody>
                </BCard>
              </BCol>
            </BRow>
          </BCardBody>
        </BCard>
      </BCol>
    </BRow>
  </BCard>
</template>

<style scoped>
.profile-photo-container {
  text-align: center;
  margin-bottom: 20px;
}

.profile-photo {
  width: 150px;
  height: 150px;
  border-radius: 50%;
  object-fit: cover;
}

.form-control {
  margin-bottom: 15px;
}

.profile-photo-edit {
  position: absolute;
  bottom: 0;
  right: 0;
}

.avatar-title {
  font-size: 1.5rem;
}

.profile-img-file-input {
  display: none;
}
</style>
