<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import { ref, computed, watch,onMounted } from "vue";
import axios from "axios";
import Multiselect from "@vueform/multiselect";
import { useI18n } from 'vue-i18n';
import FormValidation from "@/Components/FormValidation.vue";
import { useSharedState } from '@/composables/useSharedState'; // Import the composable
import UserWebMenu from "@/Components/UserWebMenu.vue";
import ImageUp from "@/Components/ImageUp.vue";
import MultiUpload from "@/Components/MultiUpload.vue";
import { BCard, BCardBody, BCardFooter, BCardHeader } from 'bootstrap-vue-next';
import { debounce } from 'lodash';


export default {
    components: {
        Layout,
        PageHeader,
        Head,
        Multiselect,
        FormValidation,
        UserWebMenu,
        ImageUp,
        MultiUpload
    },
    props: {
        support_ticket: Object,
        successMessage: String,
        alertMessage: String,
        app_for: String,
        title: Array,
        languages: Array,
        titleType:String,
        user: Object,
        service_location: Array,
        request_id:Array
    },

    setup(props) {
        const { t } = useI18n();
        const { languages, fetchData } = useSharedState(); // Destructure the shared state
        const activeTab = ref('English');
        const errors = ref({});
        const validationRef = ref(null);
        const title = ref(props.title);
        const serviceLocation = ref(props.service_location);
        const requestID = ref(props.request_id);

        const form = useForm({
            title_id:  props.support_ticket ? props.support_ticket.title_id || {} : {},
            description:  props.support_ticket ? props.support_ticket.description || "" : "",
            service_location_id:  props.support_ticket ? props.support_ticket.service_location_id || "" : "",
            files: [],
        });


        const successMessage = ref(props.successMessage || '');
        const alertMessage = ref(props.alertMessage || '');

        const dismissMessage = () => {
            successMessage.value = "";
            alertMessage.value = "";
        };

        const validationRules = {
            title_id: { required: true },
            description: { required: true },
            service_location_id: { required: true},
        };
        // Construct the full URL for the vehicle type icon
        const searchQuery = ref('');


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

            try {
                let response;
                console.log("data",form.data());

                const formData = new FormData()
  
                // form.files.forEach((file) => {
                // formData.append("files[]", file);
                // });
                // console.log([...formData]); // Check if files are added


                formData.append('title_id', form.title_id);
                formData.append('description', form.description);
                formData.append('service_location_id', form.service_location_id);

                const rawFiles = form.files.map(proxyFile => proxyFile.file || proxyFile); 

                rawFiles.forEach((file, index) => {
                    formData.append("files[]", file);
                })
                response = await axios.post('/ticket/store', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                });

                if (response.status === 201) {
                successMessage.value = t('ticket_created_successfully');
                form.reset();
                router.get('/get-support');
                } else {
                alertMessage.value = t('failed_to_create_ticket');
                }
            } catch (error) {
                if (error.response && error.response.status === 422) {
                errors.value = error.response.data.errors;
                } else {
                console.error(t('error_creating_ticket'), error);
                alertMessage.value = t('failed_to_create_ticket_catch');
                }
            }
         };

         const setActiveTab = (tab) => {
        activeTab.value = tab;
        }
        onMounted(async () => {
        if (Object.keys(languages).length == 0) {
            await fetchData();
        }
        });

        const filterTitle = () =>{

            if (props.titleType === 'request') {
                title.value = props.title.filter(t => t.title_type === "request");
            } 
            if (props.titleType === 'general') {
                title.value = props.title.filter(t => t.title_type === "general");
            }
        }

        const handleFileUpload = (event) => {
            form.files = [...event.target.files];
        }

        const handleFilesSelected = (files) => {
            console.log("Selected files:", files); // Check before updating

            form.files = [...form.files, ...files]; // Update form files reactively
            console.log("files",form.files);
        };
        const  removeFile = (index) =>{
         form.files.splice(index, 1);
        }
        const ticketRedirect = () =>{
            if(props.titleType === 'request') {
                router.get(`/history/view/${requestID.value}`);
            }
            if (props.titleType === 'general') {
                router.get('/get-support');
            }

        }

        return {
            form,
            successMessage,
            alertMessage,
            errors,
            handleSubmit,
            validationRef,
            validationRules,
            searchQuery,
            setActiveTab,
            activeTab,
            languages,
            title,
            filterTitle,
            handleFileUpload,
            handleFilesSelected,
            removeFile,
            serviceLocation,
            ticketRedirect
        };
    },
    mounted() {
        this.filterTitle();
    }

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
        <BCardBody>
            <!-- <h3 class="text-uppercase">{{$t("create_ticket")}}</h3> -->
            <div class="d-flex align-items-center justify-content-between">
                <h3 class="text-uppercase">{{$t("create_ticket")}}</h3>
                <BLink @click="ticketRedirect(id)" class="btn btn-primary float-end">{{ $t("back") }}</BLink>
            </div>
        </BCardBody>
    
        <div class="row">
            <div class="col-sm-8">
                <BCard no-body id="tasksList" class="border">
                    <BCardBody class="border border-dashed border-end-0 border-start-0">
                        <form @submit.prevent="handleSubmit">
                            <FormValidation :form="form" :rules="validationRules" ref="validationRef">
                                <div class="row">
                                <div class="col-sm-12">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">{{$t("title")}}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select id="title_id" class="form-control" v-model="form.title_id">
                                            <option value="" disabled>{{$t("select_title")}}</option>
                                            <option v-for="titles in title" :key="titles.title" :value="titles.id">{{ titles.title }}</option>
                                        </select>
                                        <span v-for="(error, index) in errors.title_id" :key="index" class="text-danger">{{ error }}</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="service_location_id" class="form-label">{{$t("select_area")}}
                                        <span class="text-danger">*</span>
                                        </label>
                                        <select id="service_location_id" class="form-select" v-model="form.service_location_id">
                                        <option disabled value="">{{$t("select")}}</option>
                                        <option v-for="location in serviceLocation" :key="location.id" :value="location.id">
                                            {{ location.name }}
                                        </option>
                                        </select>
                                        <span v-for="(error, index) in errors.service_location_id" :key="index" class="text-danger">{{ error }}</span>
                                    </div>
                                    </div>
                                <div class="col-sm-12">
                                    <div class="mb-3">
                                    <label for="description" class="form-label">{{$t("description")}}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control" id="description" rows="3" :placeholder="$t('enter_description')" v-model="form.description"></textarea>
                                    <span v-for="(error, index) in errors.description" :key="index" class="text-danger">{{ error }}</span>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="mb-3">
                                        <label for="attachament" class="form-label">{{$t("attachament")}} </label>
                                        <!-- <input type="file" multiple @change="handleFileUpload" class="form-control" /> -->
                                        <MultiUpload @files-selected="handleFilesSelected"></MultiUpload> 
                                    </div>
                                </div>                                                                
                                <div class="col-lg-12">
                                    <div class="text-end">
                                    <button type="submit" class="btn btn-primary">{{ support_ticket ? $t('update') : $t('save') }}</button>
                                    </div>
                                </div>
                                </div>
                            </FormValidation>
                            </form>
                    </BCardBody>
                </BCard>
            </div>
            <!-- <div class="col-sm-4">
                <BCard no-body id="tasksList" class="border border-animate">
                    <BCardHeader>
                        <h4 class="text-center text-uppercase">{{ $t("supporting_time") }}</h4>
                    </BCardHeader>
                    <BCardBody class="border border-dashed border-end-0 border-start-0">
                        <div class="border p-3 rounded border-1 mb-2">
                            <h5 class="border-bottom border-primary m-1 text-center border-bottom-dashed text-uppercase border-2">
                                Monday - Friday
                                <span class="dot-line"></span>
                            </h5>
                            <div class="d-flex">
                                <div class="ms-4 fs-16">11.00AM</div>
                                <div class="ms-auto me-4 fs-16">5.00PM</div>
                            </div>                           
                        </div>
                        <div class="border p-1 rounded border-1 mb-2">
                            <h5 class="border-bottom border-primary pb-2 m-1 text-center border-bottom-dashed text-uppercase border-2">
                                Saturday-Sunday
                                <span class="dot-line1"></span>
                            </h5>
                            <div class="text-center fs-14 mt-2">Closed</div>                         
                        </div> 
                    </BCardBody>
                    <BCardFooter>
                        <h4 class="text-uppercase text-center">Support Ticket Guidelines</h4>
                        <div class="mt-4">
                            <div class="fs-16">Response Time</div>
                            <p class="mt-3 fs-14">
                                Our support team is available six days a week, from 11:00 AM to 5:00 PM (IST, GMT+5:30).
                                We aim to respond to each ticket as quickly as possible. However, response times may be
                                delayed by one to two business days during weekends or government holidays.
                            </p>
                        </div> 
                        
                        <div class="mt-4">
                            <div class="fs-16">Important Notice</div>
                            <ul class="mt-3 fs-14">
                               <li>Ticket response time may take up to two business days.</li>
                               <li>If a ticket remains unaddressed for more than two business days or is unrelated to our support scope, it may be locked.</li>
                               <li>Duplicate tickets for the same issue may also result in ticket locking.</li>
                            </ul>
                            <div class="fs-14">Thank you for your patience and cooperation.</div>
                        </div> 
                    </BCardFooter>
                </BCard>
            </div> -->
        </div>
    </BCard>
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
.dot-line {
    width: 86%;
    height: 2px;
    display: block;
    background-color: var(--success);
    position: relative;
    margin: 0 auto 10px auto;
    transform: translateY(-2px);
}
.dot-line {
    width: 86%;
    height: 3px;
    display: block;
    background-color: var(--side_menu);
    position: relative;
    transform: translateY(-2px);
    top:14px;
}
.dot-line::after {
    content: '';
    position: absolute;
    display: block;
    width: 10px;
    height: 10px;
    background-color: var(--side_menu);
    border-radius: 50%;
    right: 0;
    top: -4px;
}
.dot-line::before {
    content: '';
    position: absolute;
    display: block;
    width: 10px;
    height: 10px;
    background-color: var(--side_menu);
    border-radius: 50%;
    left: 0;
    top: -4px;
}

.dot-line1 {
    width: 86%;
    height: 3px;
    display: block;
    position: relative;
    transform: translateY(-2px);
    top:14px;
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
    