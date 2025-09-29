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

        <Head title="Manage Owners" />
        <PageHeader title="Update" pageTitle="Manage Owners" />
        <BRow>
            <BCol lg="12">
                <BCard no-body id="tasksList">

                    <BCardHeader class="border-0">

                    </BCardHeader>
                    <BCardBody class="border border-dashed border-end-0 border-start-0">
                        <form action="javascript:void(0);">
                            <div id="custom-progress-bar" class="progress-nav mb-4">
                                <div class="progress" style="height: 1px;">
                                <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>

                                <ul class="nav nav-pills progress-bar-tab custom-nav" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link rounded-pill active" data-progressbar="custom-progress-bar" 
                                    id="pills-gen-info-tab" data-bs-toggle="pill" data-bs-target="#pills-gen-info"
                                    type="button" role="tab" aria-controls="pills-gen-info" aria-selected="true">1</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link rounded-pill" data-progressbar="custom-progress-bar" 
                                    id="pills-info-desc-tab" data-bs-toggle="pill" data-bs-target="#pills-info-desc" 
                                    type="button" role="tab" aria-controls="pills-info-desc" aria-selected="false">2</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link rounded-pill" data-progressbar="custom-progress-bar"
                                    id="pills-success-tab" data-bs-toggle="pill" data-bs-target="#pills-success" 
                                    type="button" role="tab" aria-controls="pills-success" aria-selected="false">3</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link rounded-pill" data-progressbar="custom-progress-bar" 
                                    id="pills-document-tab" data-bs-toggle="pill" data-bs-target="#pills-document" 
                                    type="button" role="tab" aria-controls="pills-document" aria-selected="false">4</button>
                                </li>
                                </ul>
                            </div>

                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="pills-gen-info" role="tabpanel" aria-labelledby="pills-gen-info-tab">
                                <div>
                                    <div class="mb-4">
                                    <div>
                                        <h5 class="mb-1">Basic Information</h5>
                                        <p class="text-muted">Fill all Information as below</p>
                                    </div>
                                    </div>
                                    <div class="row">
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                        <label for="company_name" class="form-label">Company Name</label>
                                        <input type="text" class="form-control" placeholder="Enter Company Name" id="company_name" />
                                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                        <label for="owner_name" class="form-label">Owner Name</label>
                                        <input type="text" class="form-control" placeholder="Enter Owner Name" id="owner_name"/>
                                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="text" class="form-control" placeholder="Enter Email" id="email"/>
                                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="text" class="form-control" placeholder="Enter Password" id="password"/>
                                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                        <label for="confirm_password" class="form-label">confirm Password</label>
                                        <input type="text" class="form-control" placeholder="Enter confirm Password" id="confirm_password" />
                                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                        <label for="address" class="form-label">Address</label>
                                        <input type="text" class="form-control" placeholder="Enter Address" id="address"/>
                                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                        <label for="postal_code" class="form-label">Postal Code</label>
                                        <input type="text" class="form-control" placeholder="Enter Postal Code" id="postal_code"/>
                                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                        <label for="city" class="form-label">City</label>
                                        <input type="text" class="form-control" placeholder="Enter City" id="city"/>
                                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                        <label for="no_of_vehicle" class="form-label">No of Vehicle</label>
                                        <input type="text" class="form-control" placeholder="Enter No of Vehicle" id="no_of_vehicle"/>
                                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                        <label for="tax_identification_number" class="form-label">TAX Identification Number</label>
                                        <input type="text" class="form-control" placeholder="Enter TAX Identification Number" id="tax_identification_number" />
                                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                        <label for="select_area" class="form-label">Select Area</label>
                                        <input type="text" class="form-control" placeholder="Enter Select Area" id="select_area" />
                                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                        <label for="select_transport_type" class="form-label">Select Transport Type</label>
                                        <select id="select_transport_type" class="form-select" >
                                            <option disabled value="">Select</option>
                                            <option  value="taxi">Taxi</option>
                                            <option  value="delivery">Delivery</option>
                                            <option  value="both">Both</option>
                                        </select>
                                        <span v-for="(error, index) in errors.country" :key="index" class="text-danger">{{ error }}</span>
                                        </div>
                                    </div>
                                    </div>                                                    
                                </div>
                                <div class="d-flex align-items-start gap-3 mt-4">
                                    <button type="button" class="btn btn-success btn-label right ms-auto nexttab nexttab" data-nexttab="pills-info-desc-tab">
                                    <i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>
                                    Next
                                    </button>
                                </div>
                                </div>
                                

                                <div class="tab-pane fade" id="pills-info-desc" role="tabpanel" aria-labelledby="pills-info-desc-tab">
                                <div>
                                    <div class="mb-4">
                                    <div>
                                        <h5 class="mb-1">Contact Person Details</h5>
                                        <p class="text-muted">Fill all Information as below</p>
                                    </div>
                                    </div>
                                    <div class="row">
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" class="form-control" placeholder="Enter Name" id="name"/>
                                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                        <label for="surname" class="form-label">Surname</label>
                                        <input type="text" class="form-control" placeholder="Enter Surname" id="surname"/>
                                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                        <label for="mobile" class="form-label">Mobile</label>
                                        <input type="text" class="form-control" placeholder="Enter Mobile" id="mobile"/>
                                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                        <label for="Phone" class="form-label">Phone</label>
                                        <input type="text" class="form-control" placeholder="Enter Phone" id="phone"/>
                                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                                        </div>
                                    </div>
                                    </div> 
                                    <div class="d-flex align-items-start gap-3 mt-4">
                                    <button type="button" class="btn btn-link text-decoration-none btn-label previestab" data-previous="pills-gen-info-tab"><i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i> Previous</button>
                                    <button type="button" class="btn btn-success btn-label right ms-auto nexttab nexttab" data-nexttab="pills-success-tab"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Next</button>
                                    </div>                  
                                </div>                  
                                </div>
                                
                                <div class="tab-pane fade" id="pills-success" role="tabpanel" aria-labelledby="pills-success-tab">
                                <div>
                                    <div class="mb-4">
                                    <div>
                                        <h5 class="mb-1">Bank Details</h5>
                                        <p class="text-muted">Fill all Information as below</p>
                                    </div>
                                    </div>
                                    <div class="row">
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                        <label for="ifsc" class="form-label">IFSC</label>
                                        <input type="text" class="form-control" placeholder="Enter IFSC" id="ifsc"/>
                                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                        <label for="bank_name" class="form-label">Bank Name</label>
                                        <input type="text" class="form-control" placeholder="Enter Bank Name" id="bank_name"/>
                                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                        <label for="account_no" class="form-label">Account No</label>
                                        <input type="text" class="form-control" placeholder="Enter Account No" id="account_no"/>
                                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                                        </div>
                                    </div>                      
                                    </div>
                                </div>
                                <div class="d-flex align-items-start gap-3 mt-4">
                                    <button type="button" class="btn btn-link text-decoration-none btn-label previestab" data-previous="pills-gen-info-tab"><i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i> Previous</button>
                                    <button type="button" class="btn btn-success btn-label right ms-auto nexttab nexttab" data-nexttab="pills-success-tab"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Next</button>
                                    </div> 
                                </div>
                                
                                <div class="tab-pane fade" id="pills-document" role="tabpanel" aria-labelledby="pills-document-tab">
                                <div>
                                    <div class="mb-4">
                                    <div>
                                        <h5 class="mb-1">Document Details</h5>
                                        <p class="text-muted">Fill all Information as below</p>
                                    </div>
                                    </div>
                                    <div class="row">
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                        <label for="driving_license" class="form-label">Driving License</label>
                                        <input type="text" class="form-control" placeholder="Enter Driving License" id="driving_license"/>
                                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                        <label for="document" class="form-label">Document</label>
                                        <input type="file" class="form-control" id="document"/>
                                        <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                                        </div>
                                    </div>                      
                                    </div>
                                </div>
                                <div class="d-flex align-items-start gap-3 mt-4">
                                    <button type="button" class="btn btn-link text-decoration-none btn-label previestab" data-previous="pills-success-tab"><i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i> Previous</button>
                                    <button type="button" class="btn btn-success btn-label right ms-auto nexttab nexttab" data-nexttab="pills-success-tab"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Submit</button>
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
