<script>
import { Link, Head, useForm } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Swal from "sweetalert2";
import { ref, watch, onMounted } from "vue";
import axios from "axios";
import { mapGetters } from 'vuex';
import { layoutComputed } from "@/state/helpers";
import { useI18n } from 'vue-i18n';

export default {
    components: {
        Layout, PageHeader, Link, Head,
    },
    computed: {
    ...layoutComputed,
    ...mapGetters(['permissions']),
  },
    setup() {
        const form = useForm({
            slug: "",
            name: "",
            description: "",
        });
        const { t } = useI18n();
        const selectedRole = ref(null);
        const roles = ref([]);
        const paginator = ref({});
        const successMessage = ref('');
        const alertMessage = ref('');
        const searchQuery = ref('');
        const perPage = ref(10); // Number of items per page
        const currentPage = ref(1);
        const rows = ref(0);

        const fetchRoles = async () => {
            try {
                const response = await axios.get('/roles/list', {
                    params: {
                        page: currentPage.value,
                        perPage: perPage.value,
                        searchQuery: searchQuery.value,
                    }
                });
                roles.value = response.data.roles.data;
                paginator.value = {
                    currentPage: response.data.roles.current_page,
                    lastPage: response.data.roles.last_page,
                    from: response.data.roles.from,
                    to: response.data.roles.to,
                    total: response.data.roles.total,
                };
                rows.value = response.data.roles.total;
            } catch (error) {
                console.error(t('error_fetching_roles'), error);
            }
        };

        const dismissMessage = () => {
            successMessage.value = "";
            alertMessage.value = "";
        };

        const createRole = () => {
            form.reset();
            selectedRole.value = null;
        };

        const editRole = (role) => {
            selectedRole.value = role;
            form.slug = role.slug;
            form.name = role.name;
            form.description = role.description;
            form.clearErrors();
        };

        const validateForm = () => {
            const { name, description } = form;
            const errors = {};

            const validationRules = {
                name: { test: () => !name, message: 'Role name is required' },
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
                        response = await axios.put(`/roles/${selectedRole.value.id}`, form.data());
                    } else {
                        response = await axios.post("/roles", form.data());
                    }

     
        successMessage.value = response.data.successMessage;
        alertMessage.value = response.data.alertMessage;

        setTimeout(() => {
            successMessage.value = "";
            alertMessage.value = "";
      }, 2000);

                    fetchRoles();
                    form.reset();
                    createRole();

                } catch (error) {
                    console.error(t('error_handling_form_submission'), error);
                    if (error.response && error.response.status === 422) {
                        const errors = error.response.data.errors;
                        form.errors = errors;
                    } else {
                        console.error(t('an_unexpected_error_occurred'), error);
                    }
                }
            } else {
                form.errors = errors;
            }
        };

        const hideError = (field) => {
            form.errors[field] = '';
        };

        const deleteRole = async (roleId) => {
            try {
                await axios.delete(`/roles/${roleId}`);
                Swal.fire('Success', 'Role deleted successfully', 'success');
                fetchRoles();
            } catch (error) {
                console.error(t('error_deleting_role'), error);
                Swal.fire( t('error'), t('failed_to_delete_role'), 'error');
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
                        console.error(t('error_deleting_role'), error);
                        Swal.fire( t('error'), t('failed_to_delete_role'), "error");
                    }
                }
            });
        };

        const slugify = (text) => {
            return text.toString().toLowerCase()
                .replace(/\s+/g, '-')           // Replace spaces with -
                .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
                .replace(/\-\-+/g, '-')         // Replace multiple - with single -
                .replace(/^-+/, '')             // Trim - from start of text
                .replace(/-+$/, '');            // Trim - from end of text
        };

       

        watch(currentPage, () => {
            fetchRoles();
        });

        watch(searchQuery, () => {
            fetchRoles();
        });

        onMounted(() => {
            fetchRoles();
        });

        return {
            form, roles, selectedRole, paginator, successMessage, alertMessage, searchQuery, perPage, currentPage, rows,
            createRole, editRole, handleSubmit, deleteModal, deleteRole, dismissMessage, hideError, fetchRoles,
        };
    },
};
</script>

<template>
    <Layout>

        <Head title="Roles" />
        <PageHeader :title="$t('roles')" :pageTitle="$t('roles')"/>
        <BRow>
            <BCol lg="12">
                <BCard no-body id="tasksList">
                    <BCardHeader class="border-0">
                        <form @submit.prevent="handleSubmit" @keydown="hideError">
                            <BRow>
                                <BCol md="4">
                                    <div class="form-group mt-3">
                                        <label for="name" class="form-label">
                                            {{$t('role_name')}}<span class="text-danger">*</span>
                                        </label>
                                        <input type="text" id="name" v-model="form.name" class="form-control"
                                            :class="{ 'is-invalid': form.errors.name }" @focus="hideError('name')" />
                                        <div v-if="form.errors.name" class="text-danger">{{ form.errors.name }}</div>
                                    </div>
                                </BCol>

                                <BCol md="4">
                                    <div class="form-group mt-3">
                                        <label for="description" class="form-label">
                                            {{$t('description')}}
                                        </label>
                                        <input type="text" id="description" v-model="form.description"
                                            class="form-control" :class="{ 'is-invalid': form.errors.description }"
                                            @focus="hideError('description')" />
                                        <div v-if="form.errors.description" class="text-danger">{{
                                            form.errors.description }}</div>
                                    </div>
                                </BCol>
                            </BRow>
                            <BRow class="g-3">
                                <div class="text-end mt-4">
                                    <button type="submit" class="btn btn-success m-2 float-end" v-if="permissions.includes('create_roles')">
                                        {{ selectedRole ? $t('update') : $t('create') }}
                                    </button>
                                    <button v-if="selectedRole" type="button" @click="createRole"
                                        class="btn btn-secondary m-2 float-end">
                                        {{$t("cancel")}}
                                    </button>
                                </div>
                            </BRow>
                        </form>
                    </BCardHeader>
                </BCard>

                <BCard>
                    <BCardBody class="border border-dashed border-end-0 border-start-0 ">
                        <BCol xxl="4" class="mb-5">
                            <div class="d-flex align-items-center mt-2">
                                <div class="search-box flex-grow-1 mr-1">
                                    <input type="text" class="form-control" :placeholder="$t('search_slug_or_name')"
                                        v-model="searchQuery" @keyup.enter="fetchRoles" />
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                                <!-- <button type="reset" class="btn btn-info mx-1" @click="fetchRoles"
                                    data-bs-toggle="tooltip" v-b-tooltip.hover title="Reset">
                                    <i class="bx bx-reset bx-xs"></i>
                                </button> -->

                            </div>
                        </BCol>
                        <BRow>
                            <BCol md="12">
                                <div class="table-responsive table-card mb-4">
                                    <table class="table table-bordered align-middle table-nowrap mb-0 overflow-auto">
                                        <thead class="table-light">
                                            <tr>
                                                <th>{{$t("s_no")}}</th>
                                                <th>{{$t("name")}}</th>
                                                <th>{{$t("description")}}</th>
                                                <th>{{$t("action")}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(role, index) in roles" :key="role.id">
                                                <td>{{ (currentPage - 1) * perPage + index + 1 }}</td>
                                                <td>{{ role.name }}</td>
                                                <td>{{ role.description ? role.description : '-' }}</td>
                                                <td>
                                                    <div class="d-flex">
                                                        <Link :href="`/permissions/${role.id}`">
                                                        <BButton class="btn btn-soft-success btn-sm m-2"
                                                            data-bs-toggle="tooltip" v-b-tooltip.hover
                                                            :title="$t('permissions')" v-if="permissions.includes('permissions_roles')">
                                                            <i class='bx bxs-user-check bx-xs'></i>
                                                        </BButton>
                                                        </Link>
                                                        <BButton @click.prevent="editRole(role)"
                                                            class="btn btn-soft-warning btn-sm m-2"
                                                            data-bs-toggle="tooltip" v-b-tooltip.hover :title="$t('edit')" v-if="permissions.includes('edit_roles')">
                                                            <i class='bx bxs-edit-alt bx-xs'></i>
                                                        </BButton>
                                                        <BButton class="btn btn-soft-danger btn-sm m-2" size="sm"
                                                            type="button" @click.prevent="deleteModal(role.id)"
                                                            data-bs-toggle="tooltip" v-b-tooltip.hover :title="$t('delete')" v-if="permissions.includes('delete_roles')">
                                                            <i class='bx bx-trash bx-xs'></i>
                                                        </BButton>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </BCol>
                        </BRow>
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>

        <div class="d-flex justify-content-sm-end">
            <b-pagination v-model="currentPage" :total-rows="rows" :per-page="perPage" aria-controls="my-table"
                align="right"></b-pagination>
        </div>

        <!-- Success Message -->
        <div v-if="successMessage" class="custom-alert alert alert-success alert-border-left fade show" language="alert"
            id="alertMsg">
            <div class="alert-content">
                <i class="ri-notification-off-line me-3 align-middle"></i> <strong>Success</strong> - {{
                    successMessage }}
                <button type="button" class="btn-close btn-close-success" @click="dismissMessage"
                    aria-label="Close Success Message"></button>
            </div>
        </div>

        <!-- Alert Message -->
        <div v-if="alertMessage" class="custom-alert alert alert-danger alert-border-left fade show" language="alert"
            id="alertMsg">
            <div class="alert-content">
                <i class="ri-notification-off-line me-3 align-middle"></i> <strong>Alert</strong> - {{ alertMessage
                }}
                <button type="button" class="btn-close btn-close-danger" @click="dismissMessage"
                    aria-label="Close Alert Message"></button>
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
    float: right;
    position: fixed;
    top: 100px;
    right: 80px;
}
</style>
