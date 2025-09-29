<template>
  <DispatchMenu />
     <!-- Profile Image and User Info -->
     <div class="position-relative mx-n4 mt-n4">
       <div class="profile-wid-bg profile-setting-img"></div>
     </div>
 
     <BRow>
       <!-- Profile Image Section -->
       <BCol xl="3" xxl="3">
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
               <h5 class="fs-16 mb-1">{{ form.name }}</h5>
             </div>
           </BCardBody>
         </BCard>
       </BCol>
 
       <!-- Form Section -->
       <BCol xl="9" xxl="9">
         <BCard no-body class="mt-xl-n5 mt-xxl-n5">
           <BCardBody class="p-4 pt-2">
             <BTabs nav-class="nav-tabs-custom rounded border-bottom-0">
               <!-- Personal Details Tab -->
               <BTab class="nav-item" :title="$t('personal_details')" active>
                 <form @submit.prevent="submitForm">
                   <BRow class="pt-4">
                     <BCol lg="6">
                       <div class="mb-3">
                         <label for="name" class="form-label">{{ $t("name") }}</label>
                         <input type="text" name="name" class="form-control" id="name" v-model="form.name" :placeholder="$t('enter_your_name')" />
                       </div>
                     </BCol>
                     <BCol lg="6">
                       <div class="mb-3">
                         <label for="email" class="form-label">{{ $t("email") }}</label>
                         <input type="email" name="email" class="form-control" id="email" v-model="form.email" :placeholder="$t('enter_your_email')" />
                       </div>
                     </BCol>
                     <BCol lg="6">
                       <div class="mb-3">
                         <label for="mobile" class="form-label">{{ $t("mobile_number") }}</label>
                         <input type="text" class="form-control" id="mobile" name="mobile" v-model="form.mobile" :placeholder="$t('enter_your_phone_number')" />
                       </div>
                     </BCol>
                     <BCol lg="12">
                       <div class="hstack gap-2 justify-content-end">
                         <BButton type="submit" variant="primary">{{ $t("update") }}</BButton>
                       </div>
                     </BCol>
                   </BRow>
                 </form>
               </BTab>
 
               <!-- Password Tab -->
               <BTab :title="$t('manage_password')">
                 <form @submit.prevent="submitPasswordForm">
                   <BRow class="g-2 pt-4">
                     <BCol lg="6">
                       <div>
                         <label for="newpasswordInput" class="form-label">{{ $t("new_password") }}*</label>
                         <input type="password" v-model="form.password" class="form-control" id="newpasswordInput" :placeholder="$t('enter_new_password')" />
                       </div>
                     </BCol>
                     <BCol lg="6">
                       <div>
                         <label for="confirmpasswordInput" class="form-label">{{ $t("confirm_password") }}*</label>
                         <input type="password" v-model="form.confirm_password" class="form-control" id="confirmpasswordInput" :placeholder="$t('confirm_password')" />
                       </div>
                     </BCol>
                     <BCol lg="12 mt-4">
                       <div class="text-end">
                         <BButton type="submit" variant="success">{{ $t("change_password") }}</BButton>
                       </div>
                     </BCol>
                   </BRow>
                 </form>
               </BTab>
             </BTabs>
           </BCardBody>
         </BCard>
       </BCol>
     </BRow>
 
 </template>
 
 <script>
 import { useForm } from '@inertiajs/vue3';
 import Swal from "sweetalert2";
 import axios from 'axios';
 import Layout from "@/Layouts/main.vue";
 import DispatchMenu from "@/Layouts/dispatchHorizontal.vue"
 
 export default {
   components: {
     Layout,
     DispatchMenu
   },
   data() {
     return {
       profilePhotoUrl: this.$page.props.auth.user.profile_picture, // Use the URL from the model
       profileImageFile: null, // Store the uploaded profile image file
       form: {
         name: this.$page.props.auth.user.name || '',
         email: this.$page.props.auth.user.email || '',
         mobile: this.$page.props.auth.user.mobile || '',
         password: '',
         confirm_password: ''
       },
     };
   },
   methods: {
     onProfileImageChange(event) {
       const file = event.target.files[0];
       if (file) {
         this.profileImageFile = file; // Store the uploaded image file
         const reader = new FileReader();
         reader.onload = (e) => {
           this.profilePhotoUrl = e.target.result; // Preview the image
         };
         reader.readAsDataURL(file);
       }
     },
     submitForm() {
       const formData = new FormData();
 
       // Append the form data fields
       formData.append('name', this.form.name);
       formData.append('email', this.form.email);
       formData.append('mobile', this.form.mobile);
 
       // Append the profile image file if uploaded
       if (this.profileImageFile) {
         formData.append('profile_picture', this.profileImageFile);
       }
 
       axios.post('/update-profile', formData, {
         headers: {
           'Content-Type': 'multipart/form-data',
         },
       })
         .then(response => {
           Swal.fire({
             title: 'Success!',
             text: 'Profile updated successfully.',
             icon: 'success',
           });
         })
         .catch(error => {
           Swal.fire({
             title: 'Error!',
             text: 'There was an error updating your profile.',
             icon: 'error',
           });
         });
     },
     submitPasswordForm() {
       // Check if password matches confirm password
       if (this.form.password !== this.form.confirm_password) {
         Swal.fire({
           title: 'Error!',
           text: 'Passwords do not match!',
           icon: 'error',
         });
         return;
       }
 
       // Submit password update request via Axios
       axios.post('/update-password', {
         password: this.form.password,
         confirm_password: this.form.confirm_password,
       })
       .then(response => {
         Swal.fire({
           title: 'Success!',
           text: 'Password updated successfully!',
           icon: 'success',
         });
         this.form.password = '';
         this.form.confirm_password = '';
       })
       .catch(error => {
         Swal.fire({
           title: 'Error!',
           text: error.response.data.message || 'An error occurred.',
           icon: 'error',
         });
       });
     },
   }
 };
 </script>
 
 
 <style>
 .custom-alert {
     max-width: 600px;
     float: right;
     position: fixed;
     top: 90px;
     right: 20px;
 }
 
 a{
     cursor: pointer;
 }
 .text-danger {
     padding-top: 5px;
 }
 
 </style>
 