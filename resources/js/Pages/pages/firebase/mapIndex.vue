<template>
    <Layout>
        <Head title="Map Settings" />
        <PageHeader title="Map Settings" pageTitle="Map Settings" />
        <BRow>
            <BCol lg="12">
                <BCard no-body id="tasksList">
                    <BCardHeader class="border-0"></BCardHeader>
                    <BCardBody class="border border-dashed border-end-0 border-start-0">
                        <form @submit.prevent="handleSubmit" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="google_map_key" class="form-label">Google Map Key</label>
                                        <input type="text" class="form-control" placeholder="Enter map Key" v-model="form.google_map_key" />
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="enable_vase_map" class="form-label">Enable Vase Map</label>
                                        <select id="enable_vase_map" class="form-select" v-model="form.enable_vase_map">
                                            <!-- <option selected disabled value="">Select...</option> -->
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary">{{ map ? 'Update' : 'Save' }}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>

        <div>
            <!-- Success Message -->
            <div v-if="successMessage" class="custom-alert alert alert-success alert-border-left fade show" data="alert" id="alertMsg">
                <div class="alert-content">
                    <i class="ri-notification-off-line me-3 align-middle"></i> <strong>Success</strong> - {{ successMessage }}
                    <button type="button" class="btn-close btn-close-success" @click="dismissMessage" aria-label="Close Success Message"></button>
                </div>
            </div>

            <!-- Alert Message -->
            <div v-if="alertMessage" class="custom-alert alert alert-danger alert-border-left fade show" data="alert" id="alertMsg">
                <div class="alert-content">
                    <i class="ri-notification-off-line me-3 align-middle"></i> <strong>Alert</strong> - {{ alertMessage }}
                    <button type="button" class="btn-close btn-close-danger" @click="dismissMessage" aria-label="Close Alert Message"></button>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script>
import { Link, Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import { ref, onMounted } from "vue";
import axios from "axios";
import { useI18n } from 'vue-i18n';

export default {
    components: {
        Layout,
        PageHeader,
        Head,
    },
    props: {
        successMessage: String,
        alertMessage: String,
        map: {
            type: Object,
            required: true,
        },
        settings: {
            type: Object,
            required: false,
            default: () => ({})
        }
    },

    setup(props) {
        const { t } = useI18n();
        const successMessage = ref(props.successMessage || '');
        const alertMessage = ref(props.alertMessage || '');
        const form = useForm({
            google_map_key: props.settings.google_map_key || '',
            enable_vase_map: props.settings.enable_vase_map || '',
        });

        const dismissMessage = () => {
            successMessage.value = "";
            alertMessage.value = "";
        };

        const handleSubmit = async () => {
            try {
                let formData = new FormData();
   
                formData.append('google_map_key', form.google_map_key);
                formData.append('enable_vase_map', form.enable_vase_map);
                
                let response = await axios.post('/map-settings/update', formData);

                if (response.status === 201) {
                    successMessage.value = t('map_settings_updated_successfully');
                    form.reset();
                    router.get('/map-settings');
                } else {
                    alertMessage.value = t('failed_to_update_map_settings');
                }
            } catch (error) {
                console.error(t('error_updating_map_settings'), error);
                alertMessage.value = t('failed_to_update_map_settings');
            }
        };

        return {
            successMessage,
            alertMessage,
            dismissMessage,
            handleSubmit,
            form,
        };
    },
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
</style>
