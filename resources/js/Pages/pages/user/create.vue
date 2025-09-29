<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import { ref, computed, watch } from "vue";
import axios from "axios";
import Multiselect from "@vueform/multiselect";
import imageUpload from "@/Components/widgets/vehicletypeIcon.vue";
import { useI18n } from 'vue-i18n';
import ImageUpload from '@/Components/ImageUpload.vue';
import FormValidation from "@/Components/FormValidation.vue";

export default {
    components: {
        Layout,
        PageHeader,
        Head,
        Multiselect,
        imageUpload,
        ImageUpload,
        FormValidation
    },
    props: {
        user: Object,
        successMessage: String,
        alertMessage: String,
        app_for: String,
        countries: Array,
        default_dial_code: String,
        default_flag: String,
        default_country_id: Number, // Ensure this is provided from the parent component or initialized
    },

    setup(props) {
        const { t } = useI18n();
        const selectedCountry = ref({
            dial_code: props.default_dial_code || '',
            flag: props.default_flag || ''
        });

        const errors = ref({});
        const validationRef = ref(null);
        const isButtonDisabled = ref(false);

        const form = useForm({
            name: props.user ? props.user.name || "" : "",
            mobile: props.user ? props.user.mobile || "" : "",
            email: props.user ? props.user.email || "" : "",
            gender: props.user ? props.user.gender || "" : "",
            password: props.user ? props.user.password || "" : "",
            confirm_password: props.user ? props.user.password || "" : "",
            profilePictureFile: null,
            profile_picture: props.user ? props.user.profile_picture || "" : "",
            country: props.user ? props.user.country.id || "" : null, // Assuming country is an object with an ID
        });


        const successMessage = ref(props.successMessage || '');
        const alertMessage = ref(props.alertMessage || '');

        const dismissMessage = () => {
            successMessage.value = "";
            alertMessage.value = "";
        };

        const validationRules = {
            name: { required: true },
            mobile: { required: true },
            gender: { required: true },
            email: { required: true, email: true },
            // password: { required: true },
            // confirm_password: { required: true, sameAs: "password" },
            // profile_picture: { required: true },
        };
        // Construct the full URL for the vehicle type icon
        const searchQuery = ref('');

        const filteredCountries = computed(() => {
            return props.countries.filter(country =>
                country.name.toLowerCase().includes(searchQuery.value.toLowerCase())
            );
        });


        if(props.app_for == "demo"){
            form.mobile = "**********";
            form.email = "**********";
        }
        const validateprofilePictureSize = () => {
            const fileInput = document.getElementById('profilePictureInput');
            if (fileInput && fileInput.files && fileInput.files.length > 0) {
                const file = fileInput.files[0];
                const img = new Image();
                img.onload = function () {
                    if (img.width !== 512 || img.height !== 512) {
                        alert('Profile picture must be 512x512 pixels.');
                        fileInput.value = '';
                    }
                };
                img.src = URL.createObjectURL(file);
            }
        };

        form.country = props.default_country_id; // Set form.country to default country id

        const selectCountry = (country) => {
            if (country) {
                selectedCountry.value = country;
                form.country = country.id; // Set form.country to the selected country's id
            }
        };

        const validateForm = () => {
            const errors = {};
            for (const key in validationRules) {
                if (validationRules[key].required && !form[key]) {
                    errors[key] = t('this_field_is_required');
                    // errors[key] = 'This field is required';
                }
            }
            return errors;
        };

        const handleSubmit = async () => {
    // Validate form fields locally first
    errors.value = validateForm();

    // Check if validation errors exist locally
    if (Object.keys(errors.value).length === 0) {
        try {
            // Check if mobile number already exists
            if (form.mobile && (!props.user || form.mobile !== props.user.mobile)) {
                const mobileCheckResponse = await axios.get(`/users/check-mobile/${form.mobile}${props.user ? `/${props.user.id}` : ''}`);
                if (mobileCheckResponse.data.exists) {
                    errors.value.mobile = t('mobile_number_already_exists');
                    // errors.value.mobile = 'Mobile number already exists.';
                    return; // Exit early if mobile number exists
                }
            }

            // Check if email already exists
            if (form.email && (!props.user || form.email !== props.user.email)) {
                const emailCheckResponse = await axios.get(`/users/check-email/${form.email}${props.user ? `/${props.user.id}` : ''}`);
                if (emailCheckResponse.data.exists) {
                    errors.value.email = t('email_already_exists');
                    // errors.value.email = 'Email already exists.';
                    return; // Exit early if email exists
                }
            }

            // If no validation errors, proceed with form submission
            const formData = new FormData();
            for (const key in form) {
                if (key === 'profilePictureFile' && form[key]) {
                    formData.append('profile_picture', form[key]);
                } else {
                    formData.append(key, form[key]);
                }
            }

            // Always append country ID to formData
            formData.append('country', form.country);
            isButtonDisabled.value = true;

            let response;
            if (props.user) {
                response = await axios.post(`/users/update/${props.user.id}`, formData);
            } else {
                response = await axios.post("store", formData);
            }

            if (response.status === 200 || response.status === 201) {
                successMessage.value = response.data.successMessage;
                form.reset(); // Reset form after successful submission
                router.get('/users'); // Redirect or navigate to another route
            } else {
                console.error(t('unexpected_response_status'), response.status);
            }

        } catch (error) {
            console.error(t('error_handling_form_submission'), error);
            if (error.response && error.response.status === 422) {
                errors.value = error.response.data.errors;
            } else {
                console.error(t('an_unexpected_error_occurred'), error);
            }
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
            return form.profilePictureFile ? profilePictureUrl.value : (props.user.profile_picture ? props.user.profile_picture : '/default-profile.jpeg');
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
            errors,
            handleSubmit,
            validationRef,
            validationRules,
            selectCountry,
            selectedCountry,
            searchQuery,
            filteredCountries,
            onFileChange,
            imageSrc,
            profilePictureUrl,
            handleImageSelected,
            handleImageRemoved,
            isButtonDisabled
        };
    }
};

</script>

<template>
    <Layout>
        <Head title="User" />
        <PageHeader :title="user ? $t('edit') : $t('create')" :pageTitle="$t('user')" pageLink="/users" />
        <BRow>
            <BCol lg="12">
                <BCard no-body id="tasksList">
                    <BCardHeader class="border-0">
                        <tab></tab>                        
                        <!-- Success Message -->
            <div v-if="successMessage" class="custom-alert alert alert-success alert-border-left fade show" data="alert"
                id="alertMsg">
                <div class="alert-content">
                    <i class="ri-notification-off-line me-3 align-middle"></i> <strong>Success</strong> - {{
                        successMessage }}
                    <button type="button" class="btn-close btn-close-success" @click="dismissMessage"
                        aria-label="Close Success Message"></button>
                </div>
            </div>

            <!-- Alert Message -->
            <div v-if="alertMessage" class="custom-alert alert alert-danger alert-border-left fade show" data="alert"
                id="alertMsg">
                <div class="alert-content">
                    <i class="ri-notification-off-line me-3 align-middle"></i> <strong>Alert</strong> - {{ alertMessage
                    }}
                    <button type="button" class="btn-close btn-close-danger" @click="dismissMessage"
                        aria-label="Close Alert Message"></button>
                </div>
            </div>
                    </BCardHeader>
                    <BCardBody class="border border-dashed border-end-0 border-start-0">
                        <form @submit.prevent="handleSubmit" enctype="multipart/form-data">
                            <FormValidation :form="form" :rules="validationRules" ref="validationRef">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">{{$t("name")}}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control" :placeholder="$t('enter_name')" id="name" v-model="form.name">
                                            <span v-if="errors.name" class="text-danger">{{ errors.name }}</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label for="select_gender" class="form-label">{{$t("select_gender")}}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select id="gender" class="form-select" v-model="form.gender">
                                                <option selected disabled value="">{{$t("choose_gender")}}</option>
                                                <option value="male">{{$t("male")}}</option>
                                                <option value="female">{{$t("female")}}</option>
                                                <option value="others">{{$t("others")}}</option>
                                            </select>
                                            <span v-if="errors.gender" class="text-danger">{{ errors.gender }}</span>
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
                                            <input type="email" class="form-control" id="email" v-model="form.email" :placeholder="$t('enter_email')">
                                            <span v-if="errors.email" class="text-danger">{{ errors.email }}</span>
                                        </div>
                                    </div>
                                    <div v-if="!user" class="col-sm-6">
                                        <div class="mb-3">
                                            <label for="password" class="form-label">{{$t("password")}}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="password" class="form-control" id="password" v-model="form.password" :placeholder="$t('enter_password')">
                                            <span v-if="errors.password" class="text-danger">{{ errors.password }}</span>
                                        </div>
                                    </div>
                                    <div v-if="!user" class="col-sm-6">
                                        <div class="mb-3">
                                            <label for="confirm_password" class="form-label">{{$t("confirm_password")}}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="password" class="form-control" id="confirm_password" v-model="form.confirm_password" :placeholder="$t('confirm_password')">
                                            <span v-if="errors.confirm_password" class="text-danger">{{ errors.confirm_password }}</span>
                                        </div>
                                    </div>
                                    <!-- Display User Profile if available or show default image -->
                                    <!-- Image Upload Component -->
                                    <div class="col-sm-6">
                                        <label for="profile_picture" class="form-label">{{$t("profile_picture")}}</label>
                                        <!-- <imageUpload id="profilePictureInput" @change="onFileChange" v-model="form.profilePictureFile" :src="profilePictureUrl"></imageUpload> -->
                                        <ImageUpload  :imageType="'user-profile'" :initialImageUrl="form.profile_picture"   :flexStyle="'0 0 calc(50% - 20px)'" :aspectRatio="'5 / 5'"   
                                        @image-selected="(file) => handleImageSelected(file, 'profile_picture')" @image-removed="() => handleImageRemoved('profile_picture')"   @change="onFileChange">
                                        </ImageUpload>
                                        <span v-if="errors.profile_picture" class="text-danger">{{ errors.profile_picture }}</span>
                                    </div>
                                    <div class="col-sm-12 text-end">
                                        <button type="submit" class="btn btn-primary" :disabled="isButtonDisabled">{{$t("submit")}}</button>
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
    