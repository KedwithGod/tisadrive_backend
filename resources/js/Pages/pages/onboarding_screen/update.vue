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
        Link
    },
    props: {
        successMessage: String,
        alertMessage: String,
    },
    setup(props) {
        const searchTerm = ref("");
        const selectedRole = ref(null);
        const form = useForm({
            title: "",
            description: "",
            onboarding_image : "",
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
            form.title = role.title;
            form.description = role.description;
            form.onboarding_image = role.onboarding_image;
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
                        response = await axios.put(`/onboarding-screen/update/${selectedRole.value.id}`, form.data());
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
                const response = await axios.get(`/onboarding-screen/list`, { params });
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
            handlePageChanged

        };
    },
    mounted() {
        this.fetchRoles();
    },
};
</script>

<template>
    <Layout>

        <Head title="Onboarding Screen" />
        <PageHeader title="Update" pageTitle="Onboarding Screen" />
        <BRow>
            <BCol lg="6">
                <BCard no-body id="tasksList">

                    <BCardHeader class="border-0">

                    </BCardHeader>
                    <BCardBody class="border border-dashed border-end-0 border-start-0">
                        <form action="javascript:void(0);">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Onboarding Title</label>
                                        <input type="text" class="form-control" placeholder="Enter Onboarding Title" id="title" v-model="form.title">
                                        <span v-if="form.errors.name" class="text-danger">{{ form.errors.title }}</span>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Onboarding Description</label>
                                        <textarea type="text" class="form-control" placeholder="Enter Onboarding Description" id="description" v-model="form.description"></textarea>
                                        <span v-if="form.errors.description" class="text-danger">{{ form.errors.description }}</span>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                  <div class="mb-3">
                                    <label for="onboarding_image" class="form-label">Invoice Logo (450px * 200px - for better resolution)</label>
                                    <ImageUp  :initialImageUrl="form.onboarding_image" @image-selected="(file) => handleImageSelected(file, 'onboarding_image')" @image-removed="() => handleImageRemoved('onboarding_image')"></ImageUp>
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
