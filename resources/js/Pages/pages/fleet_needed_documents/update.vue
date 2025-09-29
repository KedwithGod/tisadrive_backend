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

export default {
    components: {
        Layout,
        PageHeader,
        Head,
        Pagination,
        Multiselect,
    },
    props: {
        successMessage: String,
        alertMessage: String,
    },
    setup(props) {
        const searchTerm = ref("");
        const selectedRole = ref(null);
        const form = useForm({
            name: "",
            description: "",
        });
        const filter = useForm({
            all: "",
            locked: "",
        });
        const roles = ref([]); // Spread the roles to make them reactive
        const paginator = ref({}); // Spread the roles to make them reactive
        const modalShow = ref(false);
        const modalForm = ref(false);
        const modalFilter = ref(false);
        const deleteItemId = ref(null);

        const successMessage = ref(props.successMessage || '');
        const alertMessage = ref(props.alertMessage || '');

        const has_identify_number = ref();

        const dismissMessage = () => {
            successMessage.value = "";
            alertMessage.value = "";
        };

        const createRole = () => {
            form.reset();
            selectedRole.value = null;
            modalForm.value = true;
        };

        const filterRole = () => {
            modalFilter.value = true;
        };

        const editRole = (role) => {
            selectedRole.value = role;
            form.name = role.name;
            form.description = role.description;
            form.clearErrors();
            modalForm.value = true;
        };

        const cancelEdit = () => {
            form.reset();
            modalForm.value = false;
            selectedRole.value = null;
        };

        const clearFilter = () => {
            filter.reset();
            fetchRoles();
            modalFilter.value = false;
        };

        const validateForm = () => {
            const { name, description } = form;
            const errors = {};

            const validationRules = {
                name: {
                    test: () => !name,
                    message: 'Role name is required'
                },
                description: {
                    test: () => !description,
                    message: 'Role description is required'
                },
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
                    if (selectedRole.value) {
                        response = await axios.put(`/roles/update/${selectedRole.value.id}`, form.data());
                    } else {
                        response = await axios.post("/roles", form.data());
                    }

                    if (response.status === 200 || response.status === 201) {
                        successMessage.value = response.data.successMessage;
                        const responseData = response.data.roles;
                        if (selectedRole.value) {
                            const index = roles.value.findIndex(role => role.id === selectedRole.value.id);
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

        watch(searchTerm,debounce(function(value){
            fetchRoles(searchTerm.value);

        },300));

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
            selectedRole,
            successMessage,
            alertMessage,
            createRole,
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
            has_identify_number

        };
    },
    mounted() {
        this.fetchRoles();
    },
};
</script>

<template>
    <Layout>

        <Head title="Owner Needed Documents" />
        <PageHeader title="Update" pageTitle="owner Needed Documents" />
        <BRow>
            <BCol lg="12">
                <BCard no-body id="tasksList">

                    <BCardHeader class="border-0">

                    </BCardHeader>
                    <BCardBody class="border border-dashed border-end-0 border-start-0">
                        <form action="javascript:void(0);">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                    <label for="name" class="form-label">{{$t("name")}}</label>
                                    <input type="text" class="form-control" :placeholder="$t('enter_name')" id="name" v-model="form.name"
                                    />
                                    <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                    <label for="has_expiry_date" class="form-label">{{$t("has_expiry_date")}}</label>
                                    <select id="has_expiry_date" class="form-select"  v-model="form.has_expiry_date">
                                        <option disabled value="">{{$t("select")}}</option>
                                        <option  value="1">{{$t("yes")}}</option>
                                        <option  value="0">{{$t("no")}} </option>
                                    </select>
                                    <span v-for="(error, index) in errors.country" :key="index" class="text-danger">{{ error }}</span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                    <label for="has_identify_number" class="form-label">{{$t("has_identify_number")}}</label>
                                    <select id="has_identify_number" class="form-select"  v-model="form.has_identify_number">
                                        <option disabled value="">{{$t("select")}}</option>
                                        <option  value="1">{{$t("yes")}}</option>
                                        <option  value="0">{{$t("no")}} </option>
                                    </select>
                                    <span v-for="(error, index) in errors.country" :key="index" class="text-danger">{{ error }}</span>
                                    </div>
                                </div>
                                <div class="col-sm-6" v-show="form.has_identify_number == 1">
                                    <div class="mb-3">
                                    <label for="identify_number_key" class="form-label">{{$t("identify_number_key")}}</label>
                                    <input type="text" class="form-control" :required="form.has_identify_number == 1" :placeholder="$t('enter_identify_number_key')" id="identify_number_key" 
                                    v-model="form.identify_number_locale_key"/>
                                    <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </div>
                                </div><!--end col-->
                            </div><!--end row-->
                        </form>
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>

        <div>
            <!-- Success Message -->
            <div v-if="successMessage" class="custom-alert alert alert-success alert-border-left fade show" role="alert"
                id="alertMsg">
                <div class="alert-content">
                    <i class="ri-notification-off-line me-3 align-middle"></i> <strong>Success</strong> - {{
                        successMessage }}
                    <button type="button" class="btn-close btn-close-success" @click="dismissMessage"
                        aria-label="Close Success Message"></button>
                </div>
            </div>

            <!-- Alert Message -->
            <div v-if="alertMessage" class="custom-alert alert alert-danger alert-border-left fade show" role="alert"
                id="alertMsg">
                <div class="alert-content">
                    <i class="ri-notification-off-line me-3 align-middle"></i> <strong>Alert</strong> - {{ alertMessage
                    }}
                    <button type="button" class="btn-close btn-close-danger" @click="dismissMessage"
                        aria-label="Close Alert Message"></button>
                </div>
            </div>
        </div>

        <!-- Create or Edit Form Modal -->
        <BModal v-model="modalForm" hide-footer :title="selectedRole ? 'Update Role' : 'Create Role'"
            class="v-modal-custom">
            <form @submit.prevent="handleSubmit" @keydown="hideError">
                <div class="form-group mt-3">
                    <label for="name" class="form-label">Role Name<span class="text-danger">*</span></label>
                    <input type="text" id="name" v-model="form.name" class="form-control"
                        :class="{ 'is-invalid': form.errors.name }" @focus="hideError('name')" />
                    <div v-if="form.errors.name" class="text-danger">{{ form.errors.name }}</div>
                </div>
                <div class="form-group mt-3">
                    <label for="description" class="form-label">description<span class="text-danger">*</span></label>
                    <input type="text" id="description" v-model="form.description" class="form-control"
                        :class="{ 'is-invalid': form.errors.description }" @focus="hideError('description')" />
                    <div v-if="form.errors.description" class="text-danger">{{ form.errors.description }}</div>
                </div>
                <button type="submit" class="btn btn-success m-2 float-end">{{ selectedRole ? 'Update' : 'Save'
                    }}</button>
                <button v-if="selectedRole" type="button" @click="cancelEdit"
                    class="btn btn-secondary m-2 float-end">Cancel</button>
            </form>
        </BModal>

        <BModal v-model="modalFilter" hide-footer dialog-class="modal-dialog-right" title="Filter"
            class="v-modal-custom " size="sm">
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
