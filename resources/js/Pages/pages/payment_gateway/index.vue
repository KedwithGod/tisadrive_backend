<script>
import { Link, Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Swal from "sweetalert2";
import { ref, watch, onMounted } from "vue";
import axios from "axios";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import search from "@/Components/widgets/search.vue";
import searchbar from "@/Components/widgets/searchbar.vue";
import { useI18n } from 'vue-i18n';

export default {
    data() {
        return {
            rightOffcanvas: false,
        };
    },
    components: {
        Layout,
        PageHeader,
        Head,
        Multiselect,
        flatPickr,
        Link,
        search,
        searchbar,

    },
    props: {
        successMessage: String,
        alertMessage: String,
        app_for: String,
        settings: {
            type: Object,
            default: () => ({}),
        },
    },
    setup(props) {
        const { t } = useI18n();
        const searchTerm = ref("");

    const form = useForm({
        enable_paystack: props.settings.enable_paystack ?? false,
        paystack_test_publish_key: props.settings.paystack_test_publish_key ?? '',
        paystack_production_publish_key: props.settings.paystack_production_publish_key ?? '',
        paystack_test_secret_key: props.settings.paystack_test_secret_key ?? '',
        paystack_production_secret_key: props.settings.paystack_production_secret_key ?? '',
        paystack_environment: props.settings.paystack_environment ?? '',

        enable_cashfree: props.settings.enable_cashfree ?? false,
        cash_free_environment: props.settings.cash_free_environment ?? '',
        cash_free_secret_key: props.settings.cash_free_secret_key ?? '',
        cash_free_production_secret_key: props.settings.cash_free_production_secret_key ?? '',
        cash_free_app_id: props.settings.cash_free_app_id ?? '',
        cash_free_production_app_id: props.settings.cash_free_production_app_id ?? '',

        enable_mercadopago: props.settings.enable_mercadopago ?? false,
        mercadopago_environment: props.settings.mercadopago_environment ?? '',
        mercadopago_test_public_key: props.settings.mercadopago_test_public_key ?? '',
        mercadopago_live_public_key: props.settings.mercadopago_live_public_key ?? '',
        mercadopago_test_access_token: props.settings.mercadopago_test_access_token ?? '',
        mercadopago_live_access_token: props.settings.mercadopago_live_access_token ?? '',

        enable_stripe: props.settings.enable_stripe ?? false,
        enable_stripe_authorization: props.settings.enable_stripe_authorization ?? false,
        stripe_environment: props.settings.stripe_environment ?? '',
        stripe_test_secret_key: props.settings.stripe_test_secret_key ?? '',
        stripe_live_secret_key: props.settings.stripe_live_secret_key ?? '',
        stripe_test_publishable_key: props.settings.stripe_test_publishable_key ?? '',
        stripe_live_publishable_key: props.settings.stripe_live_publishable_key ?? '',

        enable_flutterwave: props.settings.enable_flutterwave ?? false,
        flutter_wave_environment: props.settings.flutter_wave_environment ?? '',
        flutter_wave_test_secret_key: props.settings.flutter_wave_test_secret_key ?? '',
        flutter_wave_production_secret_key: props.settings.flutter_wave_production_secret_key ?? '',

        enable_razorpay: props.settings.enable_razorpay ?? false,
        razor_pay_environment: props.settings.razor_pay_environment ?? '',
        razor_pay_test_api_key: props.settings.razor_pay_test_api_key ?? '',
        razor_pay_live_api_key: props.settings.razor_pay_live_api_key ?? '',
        razor_pay_secrect_key: props.settings.razor_pay_secrect_key ?? '',
        razor_pay_test_secrect_key: props.settings.razor_pay_test_secrect_key ?? '',

        enable_khalti: props.settings.enable_khalti ?? false,
        khalti_pay_environment: props.settings.khalti_pay_environment ?? '',
        khalti_pay_test_api_key: props.settings.khalti_pay_test_api_key ?? '',
        khalti_pay_live_api_key: props.settings.khalti_pay_live_api_key ?? '',

        enable_xendit: props.settings.enable_xendit ?? false,
        xendi_pay_environment: props.settings.xendi_pay_environment ?? '',
        xendi_pay_test_api_key: props.settings.xendi_pay_test_api_key ?? '',
        // xendi_pay_test_secrect_key: props.settings.xendi_pay_test_secrect_key ?? '',
        xendit_pay_live_api_key: props.settings.xendit_pay_live_api_key ?? '',
        // xendit_pay_secrect_key: props.settings.xendit_pay_secrect_key ?? '',

        enable_flexpaie: props.settings.enable_flexpaie ?? false,
        flexpaie_environment: props.settings.flexpaie_environment ?? '',
        flexpaie_test_bearer_token: props.settings.flexpaie_test_bearer_token ?? '',
        flexpaie_production_bearer_token: props.settings.flexpaie_production_bearer_token ?? '',
    });

        const successMessage = ref(props.successMessage || '');
        const alertMessage = ref(props.alertMessage || '');

        const dismissMessage = () => {
            successMessage.value = "";
            alertMessage.value = "";
        };

        const handleCheckboxChange = (key) => {
            if(props.app_for == "demo"){
                form[key] = !form[key];
                Swal.fire(t('error'), t('you_are_not_authorised'), 'error');
                return;
            }
               

        };

        const handleSubmit = async () => {
            if(props.app_for == "demo"){
                Swal.fire(t('error'), t('you_are_not_authorised'), 'error');
                return;
            }
            if(!form.enable_stripe){
                form.enable_stripe_authorization = false;
            }
            try {
                // let formData = form.data();
                let formData = new FormData();
                for (let key in form) {
                    if(key.startsWith('enable')){
                        formData.append(key, form[key] ? 1 : 0);
                    }else{
                        formData.append(key, form[key]);
                    }
                }
                let response = await axios.post('/payment-gateway/update', formData);

                if (response.status === 201) {
                    successMessage.value = t('sms_configuration_updated_successfully');
                    form.reset();
                    router.get('/payment-gateway');
                } else {
                    alertMessage.value = t('failed_to_update_sms_configuration');
                }
            } catch (error) {
                console.error(t('error_updating_sms_configuration'), error);
                alertMessage.value = t('failed_to_update_sms_configuration_catch');
            }
        };

        return {
            successMessage,
            alertMessage,
            dismissMessage,
            form,
            handleSubmit,
            handleCheckboxChange,


        };
    },
    mounted() {
    },
};
</script>

<template>
    <Layout>

        <Head title="Payment Gateway" />
        <PageHeader :title="$t('payment_gateway')" :pageTitle="$t('payment_gateway')" />
        <form @submit.prevent="handleSubmit">
        <BRow>
            <BCard v-if="app_for === 'demo'" no-body id="tasksList">
                <BCardHeader class="border-0">
                    <div class="alert bg-warning border-warning fs-18" role="alert">
                        <strong> {{$t('note')}} : <em> {{$t('actions_restricted_due_to_demo_mode')}}</em> </strong>
                    </div>
                </BCardHeader>
            </BCard>
            <BCol lg="12">
                <BCard no-body id="tasksList">
                    <BCardHeader class="border-0"></BCardHeader>
                    <BCardBody class="border border-dashed border-end-0 border-start-0">
                        <BRow class="mt-4">
                            <BCol lg="6">
                                <BCard no-body id="tasksList" class="border">
                                    <BCardHeader class="border-0">
                                        <div class="row border-bottom p-2">
                                            <div class="col-6 ">
                                                <h5 class="mt-1">{{$t("razor_pay")}}</h5>                                                           
                                            </div>
                                            <div class="col-6">
                                                <div class="form-check form-switch form-switch-lg float-end me-3">
                                                    <input v-model="form.enable_razorpay" class="form-check-input" type="checkbox" role="switch" id="enable_razorpay" @change="handleCheckboxChange('enable_razorpay')" />
                                                </div>                                                              
                                            </div>
                                        </div>
                                    </BCardHeader>
                                    <BCardBody >
                                        <div  class="text-center mb-4">
                                            <img src="@assets/images/razor-pay.png"  style="width: 200px;" >
                                        </div> 
                                        <div class="mb-3">
                                            <label for="razor_pay_environment" class="form-label">{{$t("razor_pay_environment")}}</label>
                                            <select :disabled="app_for === 'demo'" id="razor_pay_environment" class="form-select" v-model="form.razor_pay_environment">
                                                <option disabled value="">{{$t("select")}}</option>
                                                <option value="test">{{$t("test")}}</option>
                                                <option value="production">{{$t("production")}}</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="razor_pay_test_api_key" class="form-label">{{$t("razor_pay_test_api_key")}}</label>
                                            <input :type="app_for === 'demo' ? 'password' : 'text'" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_razor_pay_test_api_key')" id="razor_pay_test_api_key" v-model="form.razor_pay_test_api_key" />
                                        </div>
                                        <div class="mb-3">
                                            <label for="razor_pay_test_secrect_key" class="form-label">{{$t("razor_pay_test_secrect_key")}}</label>
                                            <input :type="app_for === 'demo' ? 'password' : 'text'" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_razor_pay_test_secrect_key')" id="razor_pay_test_secrect_key" v-model="form.razor_pay_test_secrect_key" />
                                        </div>
                                        <div class="mb-3">
                                            <label for="razor_pay_live_api_key" class="form-label">{{$t("razor_pay_live_api_key")}}</label>
                                            <input :type="app_for === 'demo' ? 'password' : 'text'" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_razor_pay_live_api_key')" id="razor_pay_live_api_key" v-model="form.razor_pay_live_api_key" />
                                        </div>
                                        <div class="mb-3">
                                            <label for="razor_pay_secrect_key" class="form-label">{{$t("razor_pay_secrect_key")}}</label>
                                            <input :type="app_for === 'demo' ? 'password' : 'text'" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_razor_pay_secrect_key')" id="razor_pay_secrect_key" v-model="form.razor_pay_secrect_key" />
                                        </div>
                                            <div class="col-lg-12">
                                                <div class="text-end">
                                                <button type="submit" class="btn btn-primary"> {{ serviceLocation ? $t('update') : $t('save') }}</button>
                                                </div>
                                            </div>
                                    </BCardBody>    
                                </BCard>        
                            </BCol>
                            <BCol lg="6">
                                <BCard no-body id="tasksList" class="border" >
                                    <BCardHeader class="border-0">
                                        <div class="row border-bottom p-2">
                                            <div class="col-6 ">
                                                <h5 class="mt-1">{{$t("cashfree")}}</h5>                                                           
                                            </div>
                                            <div class="col-6">
                                                <div class="form-check form-switch form-switch-lg float-end me-3">
                                                    <input v-model="form.enable_cashfree" class="form-check-input" type="checkbox" role="switch" id="enable_cashfree" @change="handleCheckboxChange('enable_cashfree')"/>
                                                </div>                                                              
                                            </div>
                                        </div>
                                    </BCardHeader>
                                    <BCardBody>
                                        <div  class="text-center mb-4">
                                            <img src="@assets/images/cashfree.png"  style="width: 200px;" >
                                        </div> 
                                        <div class="mb-3">
                                            <label for="cash_free_environment" class="form-label">{{$t("cashfree_environment")}}</label>
                                            <select :disabled="app_for === 'demo'" id="cash_free_environment" class="form-select" v-model="form.cash_free_environment">
                                                <option disabled value="">{{$t("select")}}</option>
                                                <option value="test">{{$t("test")}}</option>
                                                <option value="production">{{$t("production")}}</option>
                                            </select>
                                        </div>                                        
                                            <div class="mb-3">
                                                <label for="cash_free_secret_key" class="form-label">{{$t("cashfree_test_secret_key")}}</label>
                                                <input :type="app_for === 'demo' ? 'password' : 'text'" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_cashfree_test_secret_key')" id="cash_free_secret_key" v-model="form.cash_free_secret_key" />
                                            </div>
                                            <div class="mb-3">
                                                <label for="cash_free_production_secret_key" class="form-label">{{$t("cashfree_production_secret_key")}}</label>
                                                <input :type="app_for === 'demo' ? 'password' : 'text'" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_cashfree_production_secret_key')" id="cash_free_production_secret_key" v-model="form.cash_free_production_secret_key" />
                                            </div>
                                            <div class="mb-3">
                                                <label for="cash_free_app_id" class="form-label">{{$t("cashfree_test_app_id")}}</label>
                                                <input :type="app_for === 'demo' ? 'password' : 'text'" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_cashfree_test_app_id')" id="cash_free_app_id" v-model="form.cash_free_app_id" />
                                            </div>
                                            <div class="mb-3">
                                                <label for="cash_free_production_app_id" class="form-label">{{$t("cashfree_production_app_id")}}</label>
                                                <input :type="app_for === 'demo' ? 'password' : 'text'" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_cashfree_production_app_id')" id="cash_free_production_app_id" v-model="form.cash_free_production_app_id" />
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="text-end">
                                                <button type="submit" class="btn btn-primary"> {{ serviceLocation ? $t('update') : $t('save') }}</button>
                                                </div>
                                            </div>
                                    </BCardBody>    
                                </BCard>        
                            </BCol>
                        </BRow>                  
                        <BRow class="mt-4" >
                            <BCol lg="6">
                                <BCard no-body id="tasksList" class="border" >
                                    <BCardHeader class=" border-0">
                                        <div class="row border-bottom p-2">
                                            <div class="col-6 ">
                                                <h5 class="mt-1">{{$t("mercadopago")}}</h5>                                                           
                                            </div>
                                            <div class="col-6">
                                                <div class="form-check form-switch form-switch-lg float-end me-3">
                                                    <input v-model="form.enable_mercadopago" class="form-check-input" type="checkbox" role="switch" id="enable_mercadopago" @change="handleCheckboxChange('enable_mercadopago')" />
                                                </div>                                                              
                                            </div>
                                        </div>
                                    </BCardHeader>
                                    <BCardBody>
                                        <div  class="text-center mb-4">
                                            <img src="@assets/images/mercado.png"  style="width: 200px;" >
                                        </div>
                                        <div class="mb-3">
                                            <label for="mercadopago_environment" class="form-label">{{$t("mercadopago_environment")}}</label>
                                            <select :disabled="app_for === 'demo'" id="mercadopago_environment" class="form-select" v-model="form.mercadopago_environment">
                                                <option disabled value="">{{$t("select")}}</option>
                                                <option value="test">{{$t("test")}}</option>
                                                <option value="production">{{$t("production")}}</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                                <label for="mercadopago_test_public_key" class="form-label">{{$t("mercadopago_test_secret_key")}}</label>
                                                <input :type="app_for === 'demo' ? 'password' : 'text'" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_mercadopago_test_secret_key')" id="mercadopago_test_public_key" v-model="form.mercadopago_test_public_key" />
                                            </div>
                                            <div class="mb-3">
                                                <label for="mercadopago_live_public_key" class="form-label">{{$t("mercadopago_production_secret_key")}}</label>
                                                <input :type="app_for === 'demo' ? 'password' : 'text'" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_mercadopago_production_secret_key')" id="mercadopago_live_public_key" v-model="form.mercadopago_live_public_key" />
                                            </div>
                                            <div class="mb-3">
                                                <label for="mercadopago_test_access_token" class="form-label">{{$t("mercadopago_test_access_token")}}</label>
                                                <input :type="app_for === 'demo' ? 'password' : 'text'" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_mercadopago_test_access_token')" id="mercadopago_test_access_token" v-model="form.mercadopago_test_access_token" />
                                            </div>
                                            <div class="mb-3">
                                                <label for="mercadopago_live_access_token" class="form-label">{{$t("mercadopago_production_access_token")}}</label>
                                                <input :type="app_for === 'demo' ? 'password' : 'text'" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_mercadopago_production_access_token')"  id="mercadopago_live_access_token" v-model="form.mercadopago_live_access_token" />
                                            </div>

                                            <div class="col-lg-12">
                                                <div class="text-end">
                                                <button type="submit" class="btn btn-primary"> {{ serviceLocation ? $t('update') : $t('save') }}</button>
                                                </div>
                                            </div>
                                    </BCardBody>    
                                </BCard>        
                            </BCol>
                            <BCol lg="6">
                                <BCard no-body id="tasksList" class="border">
                                    <BCardHeader class="border-0">
                                        <div class="row border-bottom p-2">
                                            <div class="col-6 ">
                                                <h5 class="mt-1">{{$t("stripe")}}</h5>                                                           
                                            </div>
                                            <div class="col-6">
                                                <div class="form-check form-switch form-switch-lg float-end me-3">
                                                    <input v-model="form.enable_stripe" class="form-check-input" type="checkbox" role="switch" id="enable_stripe" @change="handleCheckboxChange('enable_stripe')" />
                                                </div>                                                              
                                            </div>
                                        </div>
                                    </BCardHeader>
                                    <BCardBody >
                                        <div  class="text-center mb-4">
                                            <img src="@assets/images/stripe.png"  style="width: 200px;" >
                                        </div>
                                        <div class="mb-3 row">
                                            <div class="col-6 ">
                                                <label for="enable_stripe_authorization" class="form-label">{{$t("stripe_authorization")}}</label>
                                                <a href="" class="text-success ms-2" data-bs-toggle="modal" data-bs-target="#strip">{{$t("how_it_works")}}</a>  
                                            </div>
                                            <div class="col-6">
                                                <div class="form-check form-switch form-switch-lg float-end me-3">
                                                    <input v-model="form.enable_stripe_authorization" class="form-check-input" type="checkbox" role="switch" id="enable_stripe_authorization" @change="handleCheckboxChange('enable_stripe_authorization')" />
                                                </div>                                                              
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="stripe_environment" class="form-label">{{$t("stripe_environment")}}</label>
                                            <select :disabled="app_for === 'demo'" id="stripe_environment" class="form-select" v-model="form.stripe_environment">
                                                <option disabled value="">{{$t("select")}}</option>
                                                <option value="test">{{$t("test")}}</option>
                                                <option value="production">{{$t("production")}}</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="stripe_test_secret_key" class="form-label">{{$t("stripe_test_secret_key")}}</label>
                                            <input :type="app_for === 'demo' ? 'password' : 'text'" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_stripe_test_secret_key')" id="stripe_test_secret_key" v-model="form.stripe_test_secret_key" />
                                        </div>
                                        <div class="mb-3">
                                            <label for="stripe_live_secret_key" class="form-label">{{$t("stripe_production_secret_key")}}</label>
                                            <input :type="app_for === 'demo' ? 'password' : 'text'" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_stripe_production_secret_key')" id="stripe_live_secret_key" v-model="form.stripe_live_secret_key" />
                                        </div>
                                        <div class="mb-3">
                                            <label for="stripe_test_publishable_key" class="form-label">{{$t("stripe_test_publish_key")}}</label>
                                            <input :type="app_for === 'demo' ? 'password' : 'text'" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_stripe_test_publish_key')" id="stripe_test_publishable_key" v-model="form.stripe_test_publishable_key" />
                                        </div>
                                        <div class="mb-3">
                                            <label for="stripe_live_publishable_key" class="form-label">{{$t("stripe_production_publish_key")}}</label>
                                            <input :type="app_for === 'demo' ? 'password' : 'text'" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_stripe_production_publish_key')" id="stripe_live_publishable_key" v-model="form.stripe_live_publishable_key" />
                                        </div>
                                            <div class="col-lg-12">
                                                <div class="text-end">
                                                <button type="submit" class="btn btn-primary"> {{ serviceLocation ? $t('update') : $t('save') }}</button>
                                                </div>
                                            </div>
                                    </BCardBody>    
                                </BCard>        
                            </BCol>
                        </BRow>


                        <BRow class="mt-4">
                            <BCol lg="6">
                                <BCard no-body id="tasksList" class="border" >
                                    <BCardHeader class= " border-0">
                                        <div class="row border-bottom p-2">
                                            <div class="col-6 ">
                                                <h5 class="mt-1">{{$t("flutter_wave")}}</h5>                                                           
                                            </div>
                                            <div class="col-6">
                                                <div class="form-check form-switch form-switch-lg float-end me-3">
                                                    <input v-model="form.enable_flutterwave" class="form-check-input" type="checkbox" role="switch" id="enable_flutterwave" @change="handleCheckboxChange('enable_flutterwave')" />
                                                </div>                                                              
                                            </div>
                                        </div>
                                    </BCardHeader>
                                    <BCardBody >
                                        <div  class="text-center mb-4">
                                            <img src="@assets/images/flutter-wave.png"  style="width: 200px;" >
                                        </div>
                                        <div class="mb-3">
                                            <label for="flutter_wave_environment" class="form-label">{{$t("flutter_wave_environment")}}</label>
                                            <select :disabled="app_for === 'demo'" id="flutter_wave_environment" class="form-select" v-model="form.flutter_wave_environment">
                                                <option disabled value="">{{$t("select")}}</option>
                                                <option value="test">{{$t("test")}}</option>
                                                <option value="production">{{$t("production")}}</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="flutter_wave_test_secret_key" class="form-label">{{$t("flutter_wave_test_secret_key")}}</label>
                                            <input :type="app_for === 'demo' ? 'password' : 'text'" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_flutter_wave_test_secret_key')" id="flutter_wave_test_secret_key" v-model="form.flutter_wave_test_secret_key" />
                                        </div>
                                        <div class="mb-3">
                                            <label for="flutter_wave_production_secret_key" class="form-label">{{$t("flutter_wave_live_secret_key")}}</label>
                                            <input :type="app_for === 'demo' ? 'password' : 'text'" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_flutter_wave_live_secret_key')" id="flutter_wave_production_secret_key" v-model="form.flutter_wave_production_secret_key" />
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="text-end">
                                            <button type="submit" class="btn btn-primary"> {{ serviceLocation ? $t('update') : $t('save') }}</button>
                                            </div>
                                        </div>
                                    </BCardBody>    
                                </BCard>        
                            </BCol>
                            <BCol lg="6">
                                <BCard no-body id="tasksList" class="border" >
                                    <BCardHeader class="border-0">
                                        <div class="row border-bottom p-2">
                                            <div class="col-6 ">
                                                <h5 class="mt-1">{{$t("khalti")}}</h5>                                                           
                                            </div>
                                            <div class="col-6">
                                                <div class="form-check form-switch form-switch-lg float-end me-3">
                                                    <input v-model="form.enable_khalti" class="form-check-input" type="checkbox" role="switch" id="enable_khalti" @change="handleCheckboxChange('enable_khalti')" />
                                                </div>                                                              
                                            </div>
                                        </div>
                                    </BCardHeader>
                                    <BCardBody >
                                        <div  class="text-center mb-4">
                                            <img src="@assets/images/khalti-pay.png"  style="width: 200px;" >
                                        </div> 
                                        <div class="mb-3">
                                            <label for="khalti_pay_environment" class="form-label">{{$t("khalti_pay_environment")}}</label>
                                            <select :disabled="app_for === 'demo'" id="khalti_pay_environment" class="form-select" v-model="form.khalti_pay_environment">
                                                <option disabled value="">{{$t("select")}}</option>
                                                <option value="test">{{$t("test")}}</option>
                                                <option value="production">{{$t("production")}}</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="khalti_pay_test_api_key" class="form-label">{{$t("khalti_pay_test_secret_key")}}</label>
                                            <input :type="app_for === 'demo' ? 'password' : 'text'" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_khalti_pay_test_secret_key')" id="khalti_pay_test_api_key" v-model="form.khalti_pay_test_api_key" />
                                        </div>
                                        <div class="mb-3">
                                            <label for="khalti_pay_live_api_key" class="form-label">{{$t("khalti_pay_live_secret_key")}}</label>
                                            <input :type="app_for === 'demo' ? 'password' : 'text'" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_khalti_pay_live_secret_key')" id="khalti_pay_live_api_key" v-model="form.khalti_pay_live_api_key" />
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="text-end">
                                            <button type="submit" class="btn btn-primary"> {{ serviceLocation ? $t('update') : $t('save') }}</button>
                                            </div>
                                        </div>
                                    </BCardBody>    
                                </BCard>        
                            </BCol>
                        </BRow>


                        <BRow class="mt-4" >
                            <BCol lg="6">
                                <BCard no-body id="tasksList" class="border" >
                                    <BCardHeader class=" border-0 ">
                                        <div class="row border-bottom p-2">
                                            <div class="col-6 ">
                                                <h5 class="mt-1">{{$t("paystack")}}</h5>                                                           
                                            </div>
                                            <div class="col-6">
                                                <div class="form-check form-switch form-switch-lg float-end me-3">
                                                    <input v-model="form.enable_paystack" class="form-check-input" type="checkbox" role="switch" id="enable_paystack" @change="handleCheckboxChange('enable_paystack')" />
                                                </div>                                                              
                                            </div>
                                        </div>
                                    </BCardHeader>
                                    <BCardBody >
                                        <div  class="text-center mb-4">
                                            <img src="@assets/images/paystack.png"  style="width:200px;" >
                                        </div>                                      
                                        <div class="mb-3">
                                            <label for="paystack_environment" class="form-label">{{$t("paystack_environment")}}</label>
                                            <select :disabled="app_for === 'demo'" id="paystack_environment" class="form-select" v-model="form.paystack_environment">
                                                <option disabled value="">{{$t("select")}}</option>
                                                <option value="test">{{$t("test")}}</option>
                                                <option value="production">{{$t("production")}}</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="paystack_test_secret_key" class="form-label">{{$t("paystack_test_publish_key")}}</label>
                                            <input :type="app_for === 'demo' ? 'password' : 'text'" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_paystack_test_publish_key')" id="paystack_test_secret_key" v-model="form.paystack_test_secret_key" />
                                        </div>
                                        <div class="mb-3">
                                            <label for="paystack_production_secret_key" class="form-label">{{$t("paystack_production_publish_key")}}</label>
                                            <input :type="app_for === 'demo' ? 'password' : 'text'" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_paystack_production_publish_key')" id="paystack_production_secret_key" v-model="form.paystack_production_secret_key" />
                                        </div>
                                        <div class="col-lg-12">
                                                <div class="text-end">
                                                <button type="submit" class="btn btn-primary"> {{ serviceLocation ? $t('update') : $t('save') }}</button>
                                                </div>
                                            </div>
                                    </BCardBody>
                                </BCard>
                            </BCol>
                            <BCol lg="6">
                                <BCard no-body id="tasksList" class="border" >
                                    <BCardHeader class="border-0">
                                        <div class="row border-bottom p-2">
                                            <div class="col-6 ">
                                                <h5 class="mt-1">{{$t("xendit")}}</h5>                                                           
                                            </div>
                                            <div class="col-6">
                                                <div class="form-check form-switch form-switch-lg float-end me-3">
                                                    <input v-model="form.enable_xendit" class="form-check-input" type="checkbox" role="switch" id="enable_xendit" @change="handleCheckboxChange('enable_xendit')" />
                                                </div>                                                              
                                            </div>
                                        </div>
                                    </BCardHeader>
                                    <BCardBody >
                                        <div  class="text-center mb-4">
                                            <img src="@assets/images/xendit-logo.jpg"  style="width: 200px;" >
                                        </div> 
                                        <div class="mb-3">
                                            <label for="xendi_pay_environment" class="form-label">{{$t("xendi_pay_environment")}}</label>
                                            <select :disabled="app_for === 'demo'" id="xendi_pay_environment" class="form-select" v-model="form.xendi_pay_environment">
                                                <option disabled value="">{{$t("select")}}</option>
                                                <option value="test">{{$t("test")}}</option>
                                                <option value="production">{{$t("production")}}</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="xendi_pay_test_api_key" class="form-label">{{$t("xendi_pay_test_api_key")}}</label>
                                            <input :type="app_for === 'demo' ? 'password' : 'text'" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_xendi_pay_test_api_key')" id="xendi_paytest_api_key" v-model="form.xendi_pay_test_api_key" />
                                        </div>
                                        <!-- <div class="mb-3">
                                            <label for="xendi_pay_test_secrect_key" class="form-label">{{$t("xendi_pay_test_secrect_key")}}</label>
                                            <input :type="app_for === 'demo' ? 'password' : 'text'" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_xendi_pay_test_secrect_key')" id="xendi_pay_test_secrect_key" v-model="form.xendi_pay_test_secrect_key" />
                                        </div> -->
                                        <div class="mb-3">
                                            <label for="xendit_pay_live_api_key" class="form-label">{{$t("xendit_pay_live_api_key")}}</label>
                                            <input :type="app_for === 'demo' ? 'password' : 'text'" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_xendit_pay_live_api_key')" id="xendit_pay_live_api_key" v-model="form.xendit_pay_live_api_key" />
                                        </div>
                                        <!-- <div class="mb-3">
                                            <label for="xendit_pay_secrect_key" class="form-label">{{$t("xendit_pay_secrect_key")}}</label>
                                            <input :type="app_for === 'demo' ? 'password' : 'text'" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_xendit_pay_secrect_key')" id="xendit_pay_secrect_key" v-model="form.xendit_pay_secrect_key" />
                                        </div> -->
                                        <div class="col-lg-12">
                                            <div class="text-end">
                                            <button type="submit" class="btn btn-primary"> {{ serviceLocation ? $t('update') : $t('save') }}</button>
                                            </div>
                                        </div>
                                    </BCardBody>    
                                </BCard>        
                            </BCol>
                        </BRow>
                        <BRow class="mt-4">
                            <BCol lg="6">
                                <BCard no-body id="tasksList" class="border" >
                                    <BCardHeader class="border-0">
                                        <div class="row border-bottom p-2">
                                            <div class="col-6 ">
                                                <h5 class="mt-1">{{$t("flexpaie")}}</h5>                                                           
                                            </div>
                                            <div class="col-6">
                                                <div class="form-check form-switch form-switch-lg float-end me-3">
                                                    <input v-model="form.enable_flexpaie" class="form-check-input" type="checkbox" role="switch" id="enable_flexpaie" @change="handleCheckboxChange('enable_flexpaie')" />
                                                </div>                                                              
                                            </div>
                                        </div>
                                    </BCardHeader>
                                    <BCardBody >
                                        <div  class="text-center mb-4">
                                            <img src="@assets/images/flexpaie.png"  style="width: 200px;" >
                                        </div> 
                                        <div class="mb-3">
                                            <label for="flexpaie_environment" class="form-label">{{$t("flexpaie_environment")}}</label>
                                            <select :disabled="app_for === 'demo'" id="flexpaie_environment" class="form-select" v-model="form.flexpaie_environment">
                                                <option disabled value="">{{$t("select")}}</option>
                                                <option value="test">{{$t("test")}}</option>
                                                <option value="production">{{$t("production")}}</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="flexpaie_test_bearer_token" class="form-label">{{$t("flexpaie_test_bearer_token")}}</label>
                                            <input :type="app_for === 'demo' ? 'password' : 'text'" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_flexpaie_test_bearer_token')" id="flexpaie_test_bearer_token" v-model="form.flexpaie_test_bearer_token" />
                                        </div>
                                        <div class="mb-3">
                                            <label for="flexpaie_production_bearer_token" class="form-label">{{$t("flexpaie_production_bearer_token")}}</label>
                                            <input :type="app_for === 'demo' ? 'password' : 'text'" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_flexpaie_production_bearer_token')" id="flexpaie_production_bearer_token" v-model="form.flexpaie_production_bearer_token" />
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="text-end">
                                            <button type="submit" class="btn btn-primary"> {{ serviceLocation ? $t('update') : $t('save') }}</button>
                                            </div>
                                        </div>
                                    </BCardBody>    
                                </BCard>        
                            </BCol>
                        </BRow>
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>
        <div>
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

            <!-- Strip preAuthorization Modals -->
            <div id="strip" class="modal fade" tabindex="-1" aria-labelledby="lowLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="myModalLabel">Stripe PreAuthorization</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                        </div>
                        <div class="modal-body">
                            <p class="text-muted"> For authorization, The card details must be updated by the user</p>
                            <p class="text-muted"> Stripe authorization uses the saved card details to hold the eta amount when the ride is created.</p>
                            <p class="text-muted"> The amount is held until the ride is completed or cancelled</p>
                        </div>
                        <!-- <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary ">Save Changes</button>
                        </div> -->

                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
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
        </div>
        </form>
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
