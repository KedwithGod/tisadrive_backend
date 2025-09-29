<script>
import { Link, Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import Swal from "sweetalert2";
import { ref, watch, onMounted } from "vue";
import axios from "axios";
import { debounce } from 'lodash';
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import search from "@/Components/widgets/search.vue";
import searchbar from "@/Components/widgets/searchbar.vue";
import { mapGetters } from 'vuex';
import { layoutComputed } from "@/state/helpers";
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
        Pagination,
        Multiselect,
        flatPickr,
        Link,
        search,
        searchbar,
    },
    props: {
        successMessage: String,
        alertMessage: String,
    },
    setup(props) {
        const { t } = useI18n();
        const searchTerm = ref("");
        const filter = useForm({
            transport_type:null,
            status: null,  // Added status filter
            limit:10
        });
        const results = ref([]);
        const paginator = ref({});
        const modalShow = ref(false);
        const modalFilter = ref(false);
        const deleteItemId = ref(null);
        const paginatorOption = ref({}); // Spread the results to make them reactive

        const successMessage = ref(props.successMessage || '');
        const alertMessage = ref(props.alertMessage || '');

        const dismissMessage = () => {
            successMessage.value = "";
            alertMessage.value = "";
        };
        // const toggleActiveStatus = async (id, status) => {
        //     try {
        //         await axios.post(`/rental-package-types/update-status`, { id, status });
        //         const index = results.value.findIndex(item => item.id === id);
        //         if (index !== -1) {
        //             results.value[index].active = status;
        //         }
        //     } catch (error) {
        //         if (error.response && error.response.status == 403) {
        //             alertMessage.value = error.response.data.alertMessage;
        //             setTimeout(()=>{
        //                 alertMessage.value = "";
        //             },5000)
        //         }
        //         console.error(t('error_updating_status'), error);
        //     }
        // };


        const toggleActiveStatus = async (id, status) => {
            Swal.fire({
                title: t('are_you_sure'),
                text: t('you_are_about_to_change_status'),
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#34c38f",
                cancelButtonColor: "#f46a6a",
                confirmButtonText: t('yes'),
                cancelButtonText: t('cancel')
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        await axios.post(`/rental-package-types/update-status`, { id: id, status: status });
                        const index = results.value.findIndex(item => item.id === id);
                        if (index !== -1) {
                            results.value[index].active = status; // Update the active status locally
                        }
                        Swal.fire(t('changed'), t('status_updated_successfully'), "success");
                    } catch (error) {
                        console.error(t('error_updating_status'), error);
                        Swal.fire(t('error'), t('failed_to_update_status'), "error");
                    }
                }
            });
        };

        const rightOffcanvas = ref(false);
        const filterData = () => {
            fetchDatas();
            modalFilter.value = true;
            rightOffcanvas.value = false;
        };

        const clearFilter = () => {
            filter.reset();
            fetchDatas();
            // modalFilter.value = false;
            rightOffcanvas.value = false;
        };

        const closeModal = () => {
            modalShow.value = false;
        };
        const deleteData = async (dataId) => {
            try {
                await axios.delete(`/rental-package-types/delete/${dataId}`);
                const index = results.value.findIndex(data => data.id === dataId);
                if (index !== -1) {
                    results.value.splice(index, 1);
                }
                modalShow.value = false;
                Swal.fire(t('success'), t('rental_package_deleted_successfully'), 'success');
            } catch (error) {
                if (error.response && error.response.status == 403) {
                    alertMessage.value = error.response.data.alertMessage;
                    setTimeout(()=>{
                        alertMessage.value = "";
                    },5000)
                }
                console.error(t('error_deleting_rental_package'), error);
                Swal.fire(t('error'), t('failed_to_delete_rental_package'), 'error');
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
                        await deleteData(itemId);
                    } catch (error) {
                        console.error(t('error_deleting_data'), error);
                        Swal.fire(t('error'), t('failed_to_delete_the_data'), "error");
                    }
                }
            });
        };

        // watch(searchTerm, debounce(function (value) {
        //     fetchDatas(searchTerm.value);
        // }, 300));

        const fetchDatas = async (page = 1) => {
            try {
                const params = { ...filter.data(), search: searchTerm.value, page };
                if (filter.status !== "") {
                    params.status = filter.status;
                }
                // Debug: Check what params are being sent
                console.log("Params sent:", params);

                const response = await axios.get(`/rental-package-types/list`, { params });
                results.value = response.data.results;
                paginator.value = response.data.paginator;
                updatePaginatorOptions(paginator.value.total);// Update paginator options dynamically

                modalFilter.value = false;



            } catch (error) {
                console.error(t('error_fetching_rental_package_types'), error);
            }
        };
        const handlePageChanged = async (page) => {
            localStorage.setItem("rental-package-types/list", page); // Save to localStorage
            fetchDatas(page);
        };

        const editData = async (result) =>  {
            router.get(`/rental-package-types/edit/${result.id}`); 
        };
        const updatePaginatorOptions = () => {
            paginatorOption.value = [10, 25, 50, 100,200,500]; // Default static options
        };
        // **Handle per-page changes**
        const changeEntriesPerPage = () => {
            fetchDatas(); // Fetch new data
        };

        return {
            results,
            modalShow,
            deleteItemId,
            successMessage,
            alertMessage,
            filterData,
            deleteModal,
            closeModal,
            deleteData,
            dismissMessage,
            searchTerm,
            paginator,
            modalFilter,
            clearFilter,
            fetchDatas,
            filter,
            handlePageChanged,
            editData,
            toggleActiveStatus,
            rightOffcanvas,
            paginatorOption,
            changeEntriesPerPage
        };
    },
    computed: {
    ...layoutComputed,
    ...mapGetters(['permissions']),
  },
    mounted() {
        this.fetchDatas();
        const savedPage = localStorage.getItem("rental-package-types/list");
        if(savedPage){
            this.handlePageChanged(Number(savedPage));
        }
        else{
            this.handlePageChanged(1);
        }
    },
};
</script>

<template>
    <Layout>
        <Head title="Rental Package Types" />
        <PageHeader   :title="$t('rental_package_types')" :pageTitle="$t('rental_package_types')"/>
        <BRow>
            <BCol lg="12">
                <BCard no-body id="tasksList">
                    <BCardHeader class="border-0">
                        <BRow class="g-2">
                            <BCol md="3">
                                <div class="d-flex align-items-center mt-3">
                                    <label class="me-2 text-muted">{{$t("show")}}</label>
                                    <select v-model="filter.limit" @change="changeEntriesPerPage" class="form-select form-select-sm w-auto">
                                    <option v-for="option in paginatorOption" :key="option" :value="option">
                                        {{ option }}
                                    </option>
                                    </select>
                                    <label class="ms-2 text-muted">{{$t("entries")}}</label>
                                </div>
                            </BCol>
                            <BCol md="3">
                                <!-- <div class="search-box">
                                    <input type="text" id="name" class="form-control search"
                                        placeholder="Search Service..." v-model="searchTerm" @keyup.enter="fetchDatas" />
                                    <i class="ri-search-line search-icon"></i>
                                </div> -->
                            </BCol>
                            <BCol md="auto" class="ms-auto">
                                <div class="d-flex align-items-center gap-2">
                                    <!-- <searchbar @search='fetchSearch'></searchbar> -->
                                    <BButton variant="danger" @click="rightOffcanvas = true"><i
                                            class="ri-filter-2-line me-1 align-bottom"></i> {{$t("filters")}}</BButton>

                                    <Link href="/rental-package-types/create" v-if="permissions.includes('add-rental-package')">
                                    <BButton variant="primary" class="float-end"> <i
                                            class="ri-add-line align-bottom me-1"></i>{{$t("add_rental_package_types")}}</BButton>
                                    </Link>
                                </div>
                            </BCol>
                        </BRow>
                    </BCardHeader>
                    <BCardBody class="border border-dashed border-end-0 border-start-0">
                        <div class="table-responsive">
                            <table class="table align-middle position-relative table-nowrap">
                                <thead class="table-active">
                                    <tr>
                                        <th scope="col">{{$t("name")}}</th>
                                        <th scope="col">{{$t("transport_type")}}</th>
                                        <th scope="col">{{$t("status")}}</th>
                                        <th scope="col">{{$t("action")}}</th>
                                    </tr>
                                </thead>
                                <tbody v-if="results.length > 0">
                                    <tr v-for="(result, index) in results" :key="index">
                                        <td>
                                            <div class="d-flex">
                                                <div class="flex-grow-1">{{ result.name }}</div>
                                            </div>
                                        </td>
                                     <td>
                                        <span v-if="result.transport_type === 'taxi'">{{ $t('taxi') }}</span>
                                        <span v-else-if="result.transport_type === 'delivery'">{{ $t('delivery') }}</span>
                                        <span v-else>{{ $t('all') }}</span>
                                        </td>

                                        <!-- <td>{{ result.transport_type }}</td> -->
                                        <td v-if="permissions.includes('toggle-rental-package')">
                                            <div :class="{
                                                    'form-check': true,
                                                    'form-switch': true,
                                                    'form-switch-lg': true,
                                                    'form-switch-success': result.active,
                                                }">
                                                <input class="form-check-input" type="checkbox" role="switch" @click.prevent="toggleActiveStatus(result.id, !result.active)" :id="'status_'+result.id" :checked="result.active">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <BButton @click.prevent="editData(result)" v-if="permissions.includes('edit-rental-package')"
                                                    class="btn btn-soft-warning btn-sm m-2"
                                                    data-bs-toggle="tooltip" v-b-tooltip.hover :title="$t('edit')">
                                                    <i class='bx bxs-edit-alt bx-xs'></i>
                                                </BButton>
                                                <BButton class="btn btn-soft-danger btn-sm m-2" size="sm" v-if="permissions.includes('delete-rental-package')"
                                                    type="button" @click.prevent="deleteModal(result.id)"
                                                    data-bs-toggle="tooltip" v-b-tooltip.hover :title="$t('delete')">
                                                    <i class='bx bx-trash bx-xs'></i>
                                                </BButton>
                                            </div>
                                        </td>                                          
                                    </tr>
                                </tbody>
                    <tbody v-else>
                        <tr>
                            <td colspan="7" class="text-center">
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
                    <i class="ri-notification-off-line me-3 align-middle"></i> <strong>Alert</strong> - {{ alertMessage
                    }}
                    <button type="button" class="btn-close btn-close-danger" @click="dismissMessage"
                        aria-label="Close Alert Message"></button>
                </div>
            </div>
        </div>
        <!-- filter -->
        <!-- Filter Modal -->
       <BOffcanvas v-model="rightOffcanvas" placement="end" :title="$t('rental_package_types_filters')" header-class="bg-light"
        body-class="p-0 overflow-hidden" footer-class="border-top p-3 text-center">
            <BForm >
                <div class="offcanvas-body">
                    <div class="mb-4">
                        <label for="datepicker-range"
                            class="form-label text-muted text-uppercase fw-semibold mb-3">{{$t("transport_type")}}</label>
                        <select class="form-control" data-choices data-choices-search-false name="choices-select-status"
                            id="choices-select-status" v-model="filter.transport_type">
                            <option value="all">{{$t("all")}}</option>
                            <option value="taxi">{{$t("taxi")}}</option>
                            <option value="delivery">{{$t("delivery")}}</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="status-select" class="form-label text-muted text-uppercase fw-semibold mb-3">{{$t("status")}}</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="statusActive" v-model="filter.status" value="1">
                                <label class="form-check-label" for="statusActive">{{$t("active")}}</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="statusInactive" v-model="filter.status" value="0">
                                <label class="form-check-label" for="statusInactive">{{$t("inactive")}}</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="offcanvas-footer border-top p-3 text-center hstack gap-2">
                    <BButton variant="light" class="w-100" @click="clearFilter">{{$t('clear_filter')}}</BButton>
                    <BButton type="submit" variant="success" @click="filterData"  class="w-100">{{$t("apply")}}</BButton>
                </div>
            </BForm>
        </BOffcanvas>
        <!-- Filter End -->

        <!-- Pagination -->
        <Pagination :paginator="paginator" @page-changed="handlePageChanged" />
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
