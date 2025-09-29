<script>
import { Link, Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Swal from "sweetalert2";
import { ref, watch, onMounted } from "vue";
import axios from "axios";
import { debounce } from 'lodash';
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import { integer } from '@vuelidate/validators';
import { useI18n } from 'vue-i18n';

export default {
    components: {
        Layout,
        PageHeader,
        Head,
        Multiselect,
        flatPickr,
        Link,
        Swal
    },
    props: {
        successMessage: String,
        alertMessage: String,
        language_id: integer,
        app_for: String,
        language_name:String,
        results: {
            type: Array,
            required: true,
        },
    },
    setup(props) {
        const searchTerm = ref("");
        const autoTranslateRef = useForm({
            key_value: "",
            index: "",
            group: "",
            current_value: ""
        });
        const { t } = useI18n();
        const languageName = props.language_name;
        const results = ref([]); // Spread the results to make them reactive
        const modalShow = ref(false);
        const form = useForm({
            group: "",
            all_value: "",
            translated_keyword: ""
        });
        const successMessage = ref(props.successMessage || '');
        const alertMessage = ref(props.alertMessage || '');
        const loading = ref(false); // Correctly define loading state
        const showAutoTranslateAll = ref(false);
        const dismissMessage = () => {
            successMessage.value = "";
            alertMessage.value = "";
        };

        const handleGroupChange = async (event) => {
            const selectedGroup = event.target.value;
            fetchDatas(selectedGroup);
        }

        const autoTranslateAll = async () => {
            form.all_value = true;
            loading.value = true; // Set loading to true when the process starts
            try {
                const response = await axios.post(`/languages/auto-translate-all/${props.language_id}`, form.data());
                results.value = response.data.data;
                successMessage.value = t('auto_translated_successfully');
            } catch (error) {
                console.log("error");
                if (error.response.status === 500) {
                    alertMessage.value = error.message;
                }
                console.log(error.response.data.message);
            } finally {
                loading.value = false; // Set loading to false when the process ends
            }
        }

        const autoTranslate = async (key_value, current_value, index) => {
            autoTranslateRef.key_value = key_value;
            autoTranslateRef.index = index;
            autoTranslateRef.group = form.group;
            autoTranslateRef.current_value = current_value;

            try {
                const response = await axios.post(`/languages/auto-translate/${props.language_id}`, autoTranslateRef.data());
                results.value[index].translated_value = response.data.data;
                successMessage.value = t('auto_translated_successfully');
            } catch (error) {
                console.error(t('error_translating_text'), error);
                if (error.response && error.response.status === 500) {
                    alertMessage.value = error.message;

                    Swal.fire({
                        icon: 'error',
                        title: 'Cloud Translation API Error',
                        html: `
                        <p>The Cloud Translation API has not been used in project before or it is disabled.</p>
                        <p>Please enable it by visiting:<br>
                        <a href="https://console.developers.google.com/apis/api/translate.googleapis.com/" target="_blank">
                            Google API Console
                        </a></p>
                        <p>Then retry.</p>
                        `
                    })
                } else {
                    alertMessage.value = t('an_error_occurred_while_translating_the_text') // Generic error message
                }
            }
        };

        const updateData = async (key_value, current_value, index) => {
            autoTranslateRef.key_value = key_value;
            autoTranslateRef.index = index;
            autoTranslateRef.group = form.group;
            autoTranslateRef.current_value = current_value;

            try {
                const response = await axios.post(`/languages/translate/update/${props.language_id}`, autoTranslateRef.data());
                results.value[index].translated_value = response.data.data;
                successMessage.value = t('translation_updated_successfully');
            } catch (error) {
                console.error(t('error_translating_text'), error);
                if (error.response && error.response.status === 500) {
                    alertMessage.value = error.message;
                } else {
                    alertMessage.value = t('an_error_occurred_while_updating_translation'); // Generic error message
                }
            }
        }

        const fetchDatas = async (param = 'pages_names') => {
            try {
                const params = form.data();
                params.group = param;

                const response = await axios.get(`/languages/load-translation/${props.language_id}`, { params });
                results.value = response.data.data;
                showAutoTranslateAll.value=true;
                
            } catch (error) {
                console.error(t('error_fetching_languages'), error);
            }
        };
        const downloadFile = async () => {
            try {
                const params = form.data();
                params.group = form.group;

                const response = await axios.get(`/languages/download-translation/${props.language_id}`, { params, responseType: 'blob' });
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'translations.arb'); // Modify the file name as needed
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } catch (error) {
                console.error(t('error_downloading_file'), error);
            }
        };


        return {
            form,
            results,
            modalShow,
            successMessage,
            alertMessage,
            dismissMessage,
            fetchDatas,
            handleGroupChange,
            autoTranslate,
            autoTranslateRef,
            autoTranslateAll,
            updateData,
            loading, // Make sure to return loading from the setup function
            showAutoTranslateAll,
            downloadFile,
            languageName,
        };
    },
};
</script>


<template>
    <Layout>
        <Head title="Languages" />
        <PageHeader :title="languageName" :pageTitle="$t('languages')" pageLink="/languages"/>
        <BRow>
            <BCol lg="12">
                <BCard no-body id="tasksList">
                    <BCardHeader class="border-0">
                        <BRow class="g-2">
                            <BCol class="col-12 text-center" md="12">
                              <a href="" class="text-success fs-14" data-bs-toggle="modal" data-bs-target="#translate-key">{{$t("how_it_works")}} ?</a>
                            </BCol>
                            <BCol md="3">
                                <label for="select_languages" class="form-label">{{$t("select_group")}}</label>
                                <select id="select_languages" class="form-select" v-model="form.group" @change="handleGroupChange">
                                    <option disabled value="">{{$t("choose_group")}}.</option>
                                    <option value="pages_names">{{$t("pages_names")}}</option>
                                    <option value="view_pages_1">{{$t("view_pages_1")}}</option>
                                    <option value="view_pages_2">{{$t("view_pages_2")}}</option>
                                    <option value="view_pages_3">{{$t("view_pages_3")}}</option>
                                    <option value="error_messages">{{$t("error_messages")}}</option>
                                    <option value="success_messages">{{$t("success_messages")}}</option>
                                    <option value="push_notifications">{{$t("push_notifications")}}</option>
                                    <option value="wallet_remarks">{{$t("wallet_remarks")}}</option>
                                    <option value="user_app">{{$t("user_app")}}</option>
                                    <option value="driver_app">{{$t("driver_app")}}</option>
                                </select>
                            </BCol>
                            
                            <BCol md="auto" class="ms-auto mt-4">
                                <div class="d-flex align-items-center gap-2">
                                <BButton
                                    v-if="form.group === 'user_app' || form.group === 'driver_app'"
                                    variant="success"
                                    class="float-end"
                                    @click="downloadFile"
                                >
                                <i class="ri-download-line align-bottom me-1"></i>
                                {{$t("download_translations")}}
                            </BButton>
                                    <BButton  v-if="showAutoTranslateAll" variant="primary" class="float-end" @click="autoTranslateAll" :disabled="loading || app_for === 'demo'"> 
                                        <i class="ri-add-line align-bottom me-1"></i>
                                        <span v-if="loading">
                                            <i class="ri-loader-4-line align-bottom me-1"></i> {{$t("loading")}}
                                        </span>
                                        <span v-else>
                                            {{$t("auto_translate_all")}}
                                        </span>
                                    </BButton>
                                </div>
                            </BCol>
                        </BRow>
                    </BCardHeader>
                    <BCardBody class="border border-dashed border-end-0 border-start-0">
                        <div class="table-responsive">
                            <table class="table align-middle position-relative table-nowrap">
                                <thead class="table-active">
                                    <tr>
                                        <th scope="col">{{$t("keyword")}}</th>
                                        <th scope="col">{{$t("translated_word")}}</th>
                                        <th scope="col">{{$t("google_auto_translate")}}</th>
                                        <th scope="col">{{$t("action")}}</th>
                                    </tr>
                                </thead>
                                <tbody v-if="results.length > 0">
                                    <tr v-for="(result, index) in results" :key="index">
                                        <td class="length" v-b-tooltip.hover :title=" result.current_value ">{{ result.current_value }}</td>
                                        <td>
                                            <input type="text" class="form-control" :id="'name_' + index" v-model="result.translated_value" />
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <BButton @click.prevent="autoTranslate(result.key_value, result.current_value, index)"
                                                    :disabled="app_for === 'demo' || languageName == 'English'"
                                                    class="" variant="primary">
                                                    <i>{{$t("auto_translate")}}</i>
                                                </BButton>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <BButton @click.prevent="updateData(result.key_value, result.translated_value, index)"
                                                    :disabled="app_for === 'demo'"
                                                    class="" variant="primary">
                                                    <i>{{$t("update")}}</i>
                                                </BButton>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <td colspan="4" class="text-center">
                                            <img src="@assets/images/search-file.gif" alt="Loading..." style="width:100px" />
                                            <h5>{{$t("no_data_found")}}</h5>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>
<!-- Modals -->
<div id="translate-key" class="modal fade modal-lg" tabindex="-1" aria-labelledby="lowLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">To ensure translations work correctly, please follow the instructions below.</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                
            </div>
            <div class="modal-body">
                <div class="d-flex mt-4">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="text-muted mb-0">
                            Update the map key for this page <a href="/map-setting" class="text-success">{{$t("map-settings")}} </a>
                        </p>
                    </div>
                </div>
                <div class="d-flex mt-4">
                    <div class="flex-shrink-0">
                        <i class="ri-checkbox-circle-fill text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="text-muted mb-0">
                            If the map key has already been updated on the page but translations still aren’t working, please ensure the Cloud Translation API is enabled. You can enable it by visiting:
                            <a href="https://console.developers.google.com/apis/api/translate.googleapis.com" target="_blank" class="text-success">https://console.developers.google.com/apis/api/translate.googleapis.com </a> <br>
                            After enabling, please try again.
                        </p>
                    </div>
                </div>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
     
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

            <!-- Alert Message -->
            <div v-if="alertMessage" class="custom-alert alert alert-danger alert-border-left fade show" data="alert"
                id="alertMsg">
                <div class="alert-content">
                    <i class="ri-notification-off-line me-3 align-middle"></i> <strong>Alert</strong> - {{ alertMessage }}
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
.length {
    display: inline-block;
    width: 300px;
    white-space: nowrap;
    overflow: hidden !important;
    text-overflow: ellipsis;
}
</style>
