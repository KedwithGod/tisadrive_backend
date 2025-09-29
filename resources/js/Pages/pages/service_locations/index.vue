<script>
import { Link, Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import Swal from "sweetalert2";
import { ref  } from "vue";
import axios from "axios";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
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
        searchbar,

    },
    props: {
        successMessage: String,
        alertMessage: String,
        countries: Array,
        timeZones: Array,
        app_for: String,
        serviceLocations: {
            type: Object,
            required: true,
        }


    },
    setup(props) {
        const { t } = useI18n();
        const searchTerm = ref("");
        const filter = useForm({
            unit: null,
            country: null,
            timezone: null,
            status: null,
            limit:10
        });
        const results = ref([]); // Spread the results to make them reactive
        const paginator = ref({}); // Spread the results to make them reactive
        const modalShow = ref(false);
        const modalFilter = ref(false);
        const deleteItemId = ref(null);
        const timeZones = ref(props.timeZones);
        const serviceLocations = ref(props.serviceLocations);
        const paginatorOption = ref({}); // Spread the results to make them reactive

        const successMessage = ref(props.successMessage || '');
        const alertMessage = ref(props.alertMessage || '');

        const dismissMessage = () => {
            successMessage.value = "";
            alertMessage.value = "";
        };



        const filterData = () => {
            modalFilter.value = true;
        };


        const clearFilter = () => {
            filter.reset();
            fetchDatas();
            modalFilter.value = false;
        };


        const closeModal = () => {
            modalShow.value = false;
        };
        const deleteData = async (dataId) => {
            try {
                await axios.delete(`/service-locations/delete/${dataId}`);
                const index = results.value.findIndex(data => data.id === dataId);
                if (index !== -1) {
                    results.value.splice(index, 1);
                }
                modalShow.value = false;
                Swal.fire(t('success'), t('service_location_deleted_successfully'), 'success');
            } catch (error) {
                if (error.response && error.response.status == 403) {
                    alertMessage.value = error.response.data.alertMessage;
                    setTimeout(()=>{
                        alertMessage.value = "";
                    },5000)
                }
                Swal.fire(t('error'), t('failed_to_delete_service_location'), 'error');
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

        const fetchSearch = async (value) => {
            searchTerm.value = value;
            fetchDatas();
        };
        // watch(searchTerm, debounce(function (value) {
        //     fetchDatas(searchTerm.value);

        // }, 300));

        const fetchDatas = async (page = 1) => {
            try {
                const params = filter.data();
                params.search = searchTerm.value;
                params.page = page;
                const response = await axios.get(`/service-locations/list`, { params });
                results.value = response.data.results;
                paginator.value = response.data.paginator;

                updatePaginatorOptions(paginator.value.total);// Update paginator options dynamically
                modalFilter.value = false;
            } catch (error) {
                console.error(t('error_fetching_service_locations'), error);
            }
        };

        const toggleModel = async (result) => {
            try {
                const response = await axios.post(`/service-locations/toggle/${result.id}`);
                result.active = response.data.serviceLocation.active;
                Swal.fire(t('success'), t('service_location_status_updated_successfully'), 'success');
            } catch (error) {
                if (error.response && error.response.status == 403) {
                    alertMessage.value = error.response.data.alertMessage;
                    setTimeout(()=>{
                        alertMessage.value = "";
                    },5000)
                }
                console.error(t('error_updating_status'), error);
                Swal.fire(t('error'), t('failed_to_update_the_status'), "error");
            }
        };
        const handlePageChanged = async (page) => {
            localStorage.setItem("service-locations/list", page); // Save to localStorage
            fetchDatas(page);
        };

        const editData = async (result) =>  {
            router.get(`/service-locations/edit/${result.id}`); 
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
            fetchSearch,
            modalFilter,
            toggleModel,
            clearFilter,
            serviceLocations,
            timeZones,
            fetchDatas,
            filter,
            handlePageChanged,
            editData,
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
        const savedPage = localStorage.getItem("service-locations/list");
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

        <Head title="Service Locations" />
        <PageHeader :title="$t('service_location')" :pageTitle="$t('service_location')" />
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
                            <BCol md="auto" class="ms-auto">
                                <div class="d-flex align-items-center gap-2">
                                    <searchbar @search="fetchSearch"></searchbar>
                                     
                                    <Link :href="app_for === 'demo' ? '#'  : '/service-locations/create'" v-if="permissions.includes('add_service_Location')"> 
                                        <BButton variant="primary" class="float-end" :disabled="app_for === 'demo'">
                                            <i class="ri-add-line align-bottom me-1"></i>{{ $t("add_service_location") }}
                                        </BButton>
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
                                        <th scope="col">{{ $t("name") }}</th>
                                        <th scope="col">{{ $t("timezone") }}</th>
                                        <th scope="col" v-if="permissions.includes('toggle_service_Location')">{{ $t("status") }}</th>
                                        <th scope="col">{{ $t("action") }}</th>
                                    </tr>
                                </thead>
                                <tbody v-if="results.length > 0">
                                    <tr v-for="(result, index) in results" :key="index">
                                        <td>
                                            <div class="d-flex">
                                                <div class="flex-grow-1">{{ result.name }}</div>
                                            </div>
                                        </td>
                                        <td>{{ result.timezone }}</td>
                                        <td>
                                            <div :class="{
                                                    'form-check': true,
                                                    'form-switch': true,
                                                    'form-switch-lg': true,
                                                    'form-switch-success': result.active,
                                                }">
                                                <input class="form-check-input" type="checkbox" role="switch" @click.prevent="toggleModel(result)" :id="'status_'+result.id" :checked="result.active">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <BButton @click.prevent="editData(result)" v-if="permissions.includes('edit_service_Location')"
                                                    class="btn btn-soft-warning btn-sm m-2"
                                                    data-bs-toggle="tooltip" v-b-tooltip.hover :title="$t('edit')">
                                                    <i class='bx bxs-edit-alt bx-xs'></i>
                                                </BButton>
                                             <!--   <BButton class="btn btn-soft-danger btn-sm m-2" size="sm" v-if="permissions.includes('delete_service_Location')"
                                                    type="button" @click.prevent="deleteModal(result.id)"
                                                    data-bs-toggle="tooltip" v-b-tooltip.hover :title="$t('delete')">
                                                    <i class='bx bx-trash bx-xs'></i>
                                                </BButton>  -->
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <td colspan="4" class="text-center">
                                            <img src="@assets/images/search-file.gif" alt="Loading..." style="width:100px" />
                                            <h5>{{ $t("no_data_found") }}</h5>
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
        <!-- <BOffcanvas v-model="rightOffcanvas" placement="end" title="Service Locations" header-class="bg-light"
            body-class="p-0 overflow-hidden" footer-class="border-top p-3 text-center">
            <BFrom action="" class="d-flex flex-column justify-content-end h-100">
                <div class="offcanvas-body">
                    <div class="mb-4">
                        <label for="datepicker-range"
                            class="form-label text-muted text-uppercase fw-semibold mb-3">Distance Unit</label>
                        <select class="form-control" data-choices data-choices-search-false name="choices-select-status"
                            id="choices-select-status" v-model="filter.unit">
                            <option value='kilo_meter'>Kilo Meters</option>
                            <option value='miles'>Miles</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="select_timezone" class="form-label">Select Timezone</label>
                        <select id="select_timezone" class="form-select" v-model="filter.timezone">
                            <option disabled value="">Choose Timezone...</option>
                            <option v-for="timezone in timeZones" :key="timezone.id" :value="timezone.id">
                            {{ timezone.name }}
                            </option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="select_country" class="form-label">Select Country</label>
                        <select id="select_country" class="form-select" v-model="filter.country">
                            <option disabled value="">Choose Country...</option>
                            <option v-for="country in countries" :key="country.id" :value="country.id">
                            {{ country.name }}
                            </option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="status-select"
                            class="form-label text-muted text-uppercase fw-semibold mb-3">Status</label>
                        <BRow class="g-2">
                            <BCol lg="6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="inlineCheckbox1"
                                        value=1 v-model="filter.status" />
                                    <label class="form-check-label" for="inlineCheckbox1">Active</label>
                                </div>
                            </BCol>
                            <BCol lg="6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="inlineCheckbox2"
                                        value=0 v-model="filter.status" />
                                    <label class="form-check-label" for="inlineCheckbox2">Inactive</label>
                                </div>
                            </BCol>
                        </BRow>
                    </div>
                    <div>
                    </div>
                </div>
                <div class="offcanvas-footer border-top p-3 text-center hstack gap-2">
                    <BButton variant="light" @click="clearFilter" class="w-100">Clear Filter</BButton>
                    <BButton type="submit" @click="fetchDatas" variant="success" class="w-100">
                        Apply
                    </BButton>
                </div>
            </BFrom>
        </BOffcanvas> -->
        <!--end offcanvas-->
        <!-- filter end -->

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
a{
    cursor: pointer;
}

</style>
