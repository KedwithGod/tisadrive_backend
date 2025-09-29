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
            SearchQuery: '',
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
        app_for: String,


    },
    setup(props) {
        const searchTerm = ref("");
        const filter = useForm({
            all: "",
            locked: "",
            limit:10
        });
        const { t } = useI18n();
        const results = ref([]); // Spread the results to make them reactive
        const paginator = ref({}); // Spread the results to make them reactive
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
                await axios.delete(`/languages/delete/${dataId}`);
                const index = results.value.findIndex(data => data.id === dataId);
                if (index !== -1) {
                    results.value.splice(index, 1);
                }
                modalShow.value = false;
                Swal.fire(t('success'), t('language_deleted_successfully') , 'success');
            } catch (error) {
                Swal.fire(t('error'),  t('failed_to_delete_language') , 'error');
            }
        };

        const changeLocale = async (language)=>{

            Swal.fire({
                title: "Are you sure want to change the Default Language?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#34c38f",
                cancelButtonColor: "#f46a6a",
                confirmButtonText: "Yes, change it!",
            }).then(async (result) => {
                if (result.value) {
                try {
                    const response = await axios.post(`/languages/default-set/${language}`,{lang : language});
                    if(response.status == 201){
                        fetchDatas()
                        Swal.fire({
                          text: t('language_updated_successfully'),
                          icon: 'success',
                          showConfirmButton: false,
                          timer: 5000,
                        });
                    }
                }catch(error){
                    
                    alertMessage.value = t('failed_to_update_language');
                    setTimeout(() => {
                        alertMessage.value = '';
                    }, 5000);
                    Swal.fire(t('error'),  t('failed_to_update_language') , 'error');
                }
                     
                }
            });

        };
        const changeStatus = async (locationId, status_type)=>{

            Swal.fire({
                title: "Are you sure want to change the " + status_type + "?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#34c38f",
                cancelButtonColor: "#f46a6a",
                confirmButtonText: "Yes, change it!",
            }).then(async (result) => {
                if (result.value) {
                    const response = await updateStatus(locationId, status_type);
                        const statusCheckbox = document.getElementById("statusCheck-" + locationId);
                        statusCheckbox.checked = response.check_status ? true : false;
                        Swal.fire({
                          title: 'Status Changed!',
                          text: 'Language status has been changed.',
                          icon: 'success',
                          showConfirmButton: false,
                          timer: 1000,
                        });
                     
                }
            });

        };

        const updateStatus = async (locationId, status_type) => {
            try {
                const response = await axios.put(`/languages/status/${locationId}`, { "status_type": status_type });
                return response.data;
            } catch (error) {
                alertMessage.value = t('error_changing_status');
                setTimeout(() => {
                    alertMessage.value = '';
                }, 5000);
                console.error(t('error_changing_status'), error);
                throw error;
            }
        }

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

        const fetchSearch = async (value) => {
            searchTerm.value = value;
            fetchDatas();
        };

        const fetchDatas = async (page = 1) => {
            try {
                const params = filter.data();
                params.search = searchTerm.value;
                params.page = page;
                const response = await axios.get(`/languages/list`, { params });
                results.value = response.data.results;
                paginator.value = response.data.paginator;
                updatePaginatorOptions(paginator.value.total);// Update paginator options dynamically
                modalFilter.value = false;
            } catch (error) {
                console.error(t('error_fetching_languages'), error);
            }
        };

        const handlePageChanged = async (page) => {
            fetchDatas(page);
        };

        const editData = async (result) =>  {
            router.get(`/languages/browse/${result.id}`); 
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
            changeStatus,
            updateStatus,
            fetchSearch,
            changeLocale,
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
    },
};
</script>

<template>
    <Layout>

        <Head title="Languages" />
        <PageHeader :title="$t('languages')" :pageTitle="$t('languages')"  />
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
                            </BCol>
                            <BCol md="auto" class="ms-auto">
                                <div class="d-flex align-items-center gap-2">
                                    <searchbar @search="fetchSearch"></searchbar>

                                    <Link href="/languages/create" v-if="permissions.includes('add_languages')">
                                    <BButton variant="primary" :disabled="app_for == 'demo'" class="float-end"> <i
                                            class="ri-add-line align-bottom me-1"></i> {{$t("add_new_languages")}}</BButton>
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
                                        <th scope="col">{{$t("default")}}</th>
                                        <th scope="col">{{$t("code")}}</th>
                                        <th scope="col">{{$t("status")}}</th>
                                        <th scope="col">{{$t("action")}}</th>
                                    </tr>
                                </thead>
                                <tbody v-if="results.length > 0">
                                    <tr v-for="(result, index) in results" :key="index">
                                        <td>{{ result.name }}</td>
                                        <td>
                                            <button type="button" @click.prevent="changeLocale(result.id)" :class="{
                                                'btn':true,
                                                'btn-outline-primary': !result.default_status,
                                                'btn-primary': result.default_status,
                                                'custom-toggle':true,
                                                'active': result.default_status,
                                            }" data-bs-toggle="button">
                                                <div> {{ result.default_status ? $t('default') : $t('set_as_default') }} </div>
                                            </button>
                                        </td>
                                        <td>{{ result.code }}</td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" language="switch" :id="'statusCheck-' + result.id" :checked="result.active" 
                                                @click.prevent="changeStatus(result.id,'status')"
                                                :disabled="result.code === 'en'"
                                                >
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <BButton @click.prevent="editData(result)"
                                                class="btn btn-soft-info btn-sm m-2"
                                                            data-bs-toggle="tooltip" v-b-tooltip.hover
                                                            :title="$t('translation')" v-if="permissions.includes('browse_languages')">
                                                    <i class='bx bx-world bx-xs'></i>
                                                </BButton>
                                                <BButton class="btn btn-soft-danger btn-sm m-2" size="sm" type="button" data-bs-toggle="tooltip" v-b-tooltip.hover :title="$t('delete')"
                                                    id="sa-warning" @click.prevent="deleteModal(result.id)" 
                                                    v-if="permissions.includes('delete_languages') && results.length > 1 && app_for !== 'demo' && result.code != 'en'">
                                                    <i class='bx bx-trash'></i>
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

.rtl .form-check {
    position: relative;
    text-align: right !important;
}

</style>
