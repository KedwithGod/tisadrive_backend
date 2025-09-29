<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import axios from 'axios';
import {onMounted} from "vue";
import { initI18n } from '@/i18n';
import { useI18n } from 'vue-i18n';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

// const submit = async () => {
//     form.clearErrors();
//     try {
//         const response = await axios.post('/owner-login', form.data());
//         if (response.data.success) {
//             router.get('/owner-dashboard');
//         }
//     } catch (error) {
//         if (error.response.status === 422) {
//             // Set validation errors in the form
//             form.setError('email', error.response.data.errors.email ? error.response.data.errors.email[0] : '');
//             form.setError('password', error.response.data.errors.password ? error.response.data.errors.password[0] : '');
//         } else {
//             console.error('Unexpected error:', error);
//         }
//     }
// };
const submit = async () => {
    form.clearErrors();
    try {
        const response = await axios.post('/owner-login', form.data());
        if (response.data.success) {
            router.get('/owner-dashboard');
        }
    } catch (error) {
        if (error.response && error.response.status === 422) {
            const errors = error.response.data.errors;
            if (errors.email) {
                form.setError('email', errors.email[0]);
            }
            if (errors.password) {
                form.setError('password', errors.password[0]);
            }
        } else {
            console.error('Unexpected error:', error);
        }
    }
};
</script>
<script>
export default {
    data() {
        return {
            togglePassword: false
        }
    },
    setup() {
      const { t } = useI18n();
      onMounted(async() => {
        await initI18n('en');
      })
      return t;
    },
}
</script>

<template>

    <Head title="Log in" />

    <body id="bodyContainer">
        <div class="bg-overlay"></div>
        <div class="container-fluid ">
            <BRow>
                <BCol lg="5">
                    <div class="form_field text-white">
                        <Bcard>
                            <BCardBody class="BcardBody" >
                                <h1 class="display-6" style="color: white;"><b>Owner Signin</b></h1>
                                <p class="">Welcome back login to your panel.</p>
                                <div class="mt-5">
                                    <form @submit.prevent="submit">

                                        <div class="mb-4">
                                            <InputLabel for="email" value="Email" />
                                            <TextInput id="email-input" v-model="form.email" type="email" class="form-control p-3"
                                                autofocus placeholder="Please enter email" autocomplete="email" required
                                                :class="{ 'is-invalid': form.errors.email }" />
                                            <InputError :message="form.errors.email" />
                                        </div>


                                        <div class="mb-4 ">
                                            <InputLabel for="password" value="Password" />
                                            <div class="position-relative auth-pass-inputgroup mb-3">
                                                <input :type="togglePassword ? 'text' : 'password'"
                                                    class="form-control pe-5 p-3" placeholder="Enter password"
                                                    id="password-input" v-model="form.password" autocomplete="password" required
                                                    :class="{ 'is-invalid': form.errors.password }">
                                                <BButton variant="link"
                                                    class="position-absolute end-0 mt-2 top-0 text-decoration-none text-muted"
                                                    type="button" id="password-addon" @click="togglePassword = !togglePassword">
                                                    <i class="ri-eye-fill align-middle ri-lg"></i>
                                                </BButton>
                                                <InputError :message="form.errors.password" />
                                            </div>
                                        </div>
                                        <div class="float-end " >
                                            <Link v-if="canResetPassword" :href="route('password.request')"
                                                class="text-white">Forgot password?
                                        </Link>
                                        </div>



                                        <div class="form-check form-check-success mt-5">
                                            <Checkbox v-model:checked="form.remember" name="remember" class="form-check-input"
                                                id="auth-remember-check" />
                                            <label class="form-check-label" for="auth-remember-check">Remember
                                                me</label>
                                        </div>

                                        <div class="mt-4">
                                            <BButton variant="success" class="w-100 mt-3" type="submit"
                                                :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                                                style="height: 45px; border-radius: 10px;">Login</BButton>
                                        </div>

                                    </form>
                                </div>
                            
                            </BCardBody>
                        </Bcard>
                    </div>
                </BCol>

            </BRow>
        </div>
    </body>
</template>

<style>
.form_field {
    margin: 185px 20px 0px 80px;
}

#bodyContainer {
    /* background: url('/assets/images/owner-bg.jpg') no-repeat; */
    background: var(--owner_loginbg) no-repeat;
    background-size: cover;
    height: 100vh;
}
#email-input, #password-input{
    background: none;
    color: white;
}
@media only screen and (max-width: 988px) {
    #bodyContainer{
        background: black;
    }

}
</style>
