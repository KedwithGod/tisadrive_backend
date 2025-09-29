<script>
import { Head, useForm } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import Swal from "sweetalert2";
import { ref, watch } from "vue";
import axios from "axios";
import { debounce } from 'lodash';
import Multiselect from "@vueform/multiselect";
import imageUpload from "@/Components/widgets/imageUpload.vue";
import tab from "@/Components/widgets/tab.vue";

export default {
    components: {
        Layout,
        PageHeader,
        Head,
        Pagination,
        Multiselect,
        imageUpload,
        tab,
    },
    props: {
        successMessage: String,
        alertMessage: String,
        countries: Array,
        timeZones: Array,
    },
    setup(props) {
        const searchTerm = ref("");
        const selectedCountry = ref(null);
        const form = useForm({
            country: "",
            name: "",
            currencycode: "",
            currencysymbol: "",
            timezone: "",
            unit:"",
        });
        const filter = useForm({
            all: "",
            locked: "",
        });
        const roles = ref([]);
        const paginator = ref({});
        const modalShow = ref(false);
        const modalForm = ref(false);
        const modalFilter = ref(false);
        const deleteItemId = ref(null);

        const successMessage = ref(props.successMessage || '');
        const alertMessage = ref(props.alertMessage || '');

        const dismissMessage = () => {
            successMessage.value = "";
            alertMessage.value = "";
        };

        const filterRole = () => {
            modalFilter.value = true;
        };

        const editRole = (role) => {
            selectedCountry.value = role;
            form.country = role.country;
            form.name = role.name;
            form.currencycode = role.currencycode;
            form.currencysymbol = role.currencysymbol;
            form.timezone = role.timezone;
            form.clearErrors();
            modalForm.value = true;
        };

        const cancelEdit = () => {
            form.reset();
            modalForm.value = false;
            selectedCountry.value = null;
        };

        const clearFilter = () => {
            filter.reset();
            fetchRoles();
            modalFilter.value = false;
        };

        const validateForm = () => {
            const { country, name, currencycode, currencysymbol, timezone, unit } = form;
            const errors = {};

            const validationRules = {
                country: {
                    test: () => !country,
                    message: 'Country is required'
                },
                name: {
                    test: () => !name,
                    message: 'Name is required'
                },
                currencycode: {
                    test: () => !currencycode,
                    message: 'Currency code is required'
                },
                currencysymbol: {
                    test: () => !currencysymbol,
                    message: 'Currency symbol is required'
                },
                timezone: {
                    test: () => !timezone,
                    message: 'Time Zone is required'
                },
                unit: {
                    test: () => !unit,
                    message: 'Unit is required'
                }              
            };

            Object.keys(validationRules).forEach(fieldName => {
                if (validationRules[fieldName].test()) {
                    errors[fieldName] = validationRules[fieldName].message;
                }
            });

            return errors;
        };

        const handleSubmit = async () => {
        const errors = validateForm();
        if (Object.keys(errors).length === 0) {
            try {
                let response;
                if (selectedCountry.value) {
                    response = await axios.put(`/roles/update/${selectedCountry.value.id}`, form.data());
                } else {
                    response = await axios.post("/roles", form.data());
                }

                if (response.status === 200 || response.status === 201) {
                    successMessage.value = response.data.successMessage;
                    const responseData = response.data.roles;
                    if (selectedCountry.value) {
                        const index = roles.value.findIndex(role => role.id === selectedCountry.value.id);
                        if (index !== -1) {
                            roles.value[index] = responseData;
                        }
                    } else {
                        roles.value.push(responseData);
                    }
                    modalForm.value = false;
                    form.reset();
                } else {
                    console.error('Unexpected response status:', response.status);
                }

            } catch (error) {
                console.error("Error handling form submission:", error);
                if (error.response && error.response.status === 422) {
                    const errors = error.response.data.errors;
                    form.errors = errors;
                } else {
                    console.error("An unexpected error occurred:", error);
                }
            }
        } else {
            form.errors = errors;
        }
    };

        const hideError = (field) => {
            form.errors[field] = '';
        };

        const closeModal = () => {
            modalShow.value = false;
        };

        const deleteRole = async (roleId) => {
            try {
                await axios.delete(`/roles/delete/${roleId}`);
                const index = roles.value.findIndex(role => role.id === roleId);
                if (index !== -1) {
                    roles.value.splice(index, 1);
                }
                modalShow.value = false;
                Swal.fire('Success', 'Role deleted successfully', 'success');
            } catch (error) {
                console.error("Error deleting role:", error);
                Swal.fire('Error', 'Failed to delete role', 'error');
            }
        };

        const deleteModal = async (itemId) => {
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#34c38f",
                cancelButtonColor: "#f46a6a",
                confirmButtonText: "Yes, delete it!",
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        await deleteRole(itemId);
                    } catch (error) {
                        console.error("Error deleting role:", error);
                        Swal.fire("Error", "Failed to delete the role", "error");
                    }
                }
            });
        };

        watch(searchTerm, debounce(function (value) {
            fetchRoles(searchTerm.value);
        }, 300));

        const fetchRoles = async (page = 1) => {
            try {
                const params = filter.data();
                params.search = searchTerm.value;
                params.page = page;
                const response = await axios.get(`/roles1/list`, { params });
                roles.value = response.data.roles;
                paginator.value = response.data.paginator;
                modalFilter.value = false;
            } catch (error) {
                console.error("Error fetching roles:", error);
            }
        };

        const handlePageChanged = async (page) => {
            fetchRoles(page);
        };

        return {
            form,
            roles,
            modalShow,
            modalForm,
            deleteItemId,
            selectedCountry,
            successMessage,
            alertMessage,
            filterRole,
            editRole,
            cancelEdit,
            handleSubmit,
            deleteModal,
            closeModal,
            deleteRole,
            dismissMessage,
            hideError,
            searchTerm,
            paginator,
            modalFilter,
            clearFilter,
            fetchRoles,
            filter,
            handlePageChanged,
            selectedCountry: ref(null),
            selectedTimezone: ref(null),
        };
    },
    mounted() {
        this.fetchRoles();
    },
};
</script>

<template>
    <Layout>
        <Head title="Vehicle Make" />
        <PageHeader title="Create" pageTitle="Vehicle Make" />
        <BRow>
            <BCol lg="12">
                <BCard no-body id="tasksList">
                    <BCardHeader class="border-0">
                        <tab></tab>
                    </BCardHeader>
                    <BCardBody class="border border-dashed border-end-0 border-start-0">
                        <form @submit.prevent="handleSubmit">
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="select_country" class="form-label">Transport Type</label>
                                        <select id="select_country" class="form-select">
                                            <option disabled value="">Choose Type...</option>
                                            <option value="">Taxi</option>
                                            <option value="">Delivery</option>
                                            <option value="">Both</option>
                                        </select>
                                        <span v-if="form.errors.country" class="text-danger">{{ form.errors.country }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Vehicle Make Name</label>
                                        <input type="text" class="form-control" placeholder="Enter Name" id="name" v-model="form.name">
                                        <span v-if="form.errors.name" class="text-danger">{{ form.errors.name }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="select_timezone" class="form-label">Vehicle Make For</label>
                                        <select id="select_timezone" class="form-select" >
                                            <option disabled value="">Choose Make...</option>
                                            <option value="">Taxi</option>
                                            <option value="">Bike</option>
                                            <option value="">Truck</option>
                                        </select>
                                        <span v-if="form.errors.timezone" class="text-danger">{{ form.errors.timezone }}</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="select_timezone" class="form-label d-flex">Vehicle Image <span><h5 class="text-muted mt-1 mb-0 fs-13">(320px x 320px)</h5></span></label>
                                        <imageUpload></imageUpload>
                                        <span v-if="form.errors.timezone" class="text-danger">{{ form.errors.timezone }}</span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>
        <div>
            <div v-if="successMessage" class="custom-alert alert alert-success alert-border-left fade show" role="alert" id="alertMsg">
                <div class="alert-content">
                    <i class="ri-notification-off-line me-3 align-middle"></i>
                    <strong>Success</strong> - {{ successMessage }}
                    <button type="button" class="btn-close btn-close-success" @click="dismissMessage" aria-label="Close Success Message"></button>
                </div>
            </div>

            <div v-if="alertMessage" class="custom-alert alert alert-danger alert-border-left fade show" role="alert" id="alertMsg">
                <div class="alert-content">
                    <i class="ri-notification-off-line me-3 align-middle"></i>
                    <strong>Alert</strong> - {{ alertMessage }}
                    <button type="button" class="btn-close btn-close-danger" @click="dismissMessage" aria-label="Close Alert Message"></button>
                </div>
            </div>
        </div>

        <BModal v-model="modalFilter" hide-footer dialog-class="modal-dialog-right" title="Filter" class="v-modal-custom" size="sm">
            <form>
                <div class="input-group">
                    <select class="form-select mb-3" aria-label="Default select example" v-model="filter.all">
                        <option selected>Select Status</option>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>

                    <select class="form-select mb-3" aria-label="Default select example" v-model="filter.locked">
                        <option selected>Select Status</option>
                        <option value="0">Inactive</option>
                        <option value="1">Active</option>
                    </select>
                </div>
                <BButton variant="primary" class="float-end" @click="fetchRoles"> Apply</BButton>
                <BButton variant="outline-primary" class="float-end mx-2" @click="clearFilter">Clear</BButton>
            </form>
        </BModal>
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

.text-danger {
    padding-top: 5px;
}
</style>
