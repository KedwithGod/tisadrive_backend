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
        app_for:String,

    },
    setup(props) {
        const { t } = useI18n();
        const searchTerm = ref("");
        const filter = useForm({
            limit:10
        });


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


        const mobileFromUser = (user) => {
            if(props.app_for && props.app_for == "demo"){
                return "***********";
            }
            return user.mobile_number;
        }

        const filterData = () => {
            modalFilter.value = true;
        };


        const clearFilter = () => {
            filter.reset();
            fetchDatas();
            modalFilter.value = false;
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

        const closeModal = () => {
            modalShow.value = false;
        };
        const deleteData = async (dataId) => {
            try {
                await axios.delete(`/drivers-rating/delete/${dataId}`);
                const index = results.value.findIndex(data => data.id === dataId);
                if (index !== -1) {
                    results.value.splice(index, 1);
                }
                modalShow.value = false;
                Swal.fire(('success'), t('driver_rating_deleted_successfully'), 'success');
            } catch (error) {
                Swal.fire(t('error'), t('failed_to_delete_driver_rating'), 'error');
            }
        };

        const fetchDatas = async (page = 1) => {
            try {
                const params = filter.data();
                if(searchTerm.value.length > 0){
                    params.search = searchTerm.value;
                }
                params.page = page;
                const response = await axios.get(`/drivers-rating/list`, { params });
                results.value = response.data.results;
                paginator.value = response.data.paginator;
                updatePaginatorOptions(paginator.value.total);// Update paginator options dynamically
                modalFilter.value = false;
            } catch (error) {
                console.error(t('error_fetching_drivers_rating'), error);
            }
        };

        const fetchSearch = async (value) => {
            searchTerm.value = value;
            fetchDatas();
        };
        const SerialNumber = (index) => {
            return ((paginator.value.current_page-1)*paginator.value.per_page)+index+1;
        };
        const handlePageChanged = async (page) => {
            localStorage.setItem("drivers-rating/list", page); // Save to localStorage
            fetchDatas(page);
        };

        const viewProfile = async (result) =>  {
            router.get(`/drivers-rating/view-profile/${result.id}`); 
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
            mobileFromUser,
            deleteData,
            dismissMessage,
            SerialNumber,
            fetchSearch,
            searchTerm,
            paginator,
            modalFilter,
            clearFilter,
            fetchDatas,
            filter,
            handlePageChanged,
            viewProfile,
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
         const savedPage = localStorage.getItem("drivers-rating/list");
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

        <Head title="Drivers Rating" />
        <PageHeader :title="$t('driver_rating')" :pageTitle="$t('driver_rating')" />
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
                                    <searchbar @search="fetchSearch"></searchbar>
                                    <!-- <BButton variant="danger" @click="rightOffcanvas = true"><i
                                            class="ri-filter-2-line me-1 align-bottom"></i> Filters</BButton>

                                    <Link href="/drivers-rating/create">
                                    <BButton variant="primary" class="float-end"> <i
                                            class="ri-add-line align-bottom me-1"></i> Add Drivers</BButton>
                                    </Link> -->
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
                                        <th scope="col">{{$t("mobile_number")}}</th>
                                        <th scope="col">{{$t("rating")}}</th>
                                        <th scope="col">{{$t("action")}}</th>
                                    </tr>
                                </thead>
                                <tbody v-if="results.length > 0">
                                    <tr v-for="(result, index) in results" :key="index">
                                        <td> {{ result.name }}</td>
                                        <td> {{ result.transport_type }}</td>
                                        <td>{{ mobileFromUser(result) }}</td>
                                        <td> 
                                            <div>
                                                <i v-for="n in 5" :key="n"
                                                :class="{
                                                'bx bxs-star': n <= Math.floor(result.rating),
                                                'bx bxs-star-half': n === Math.ceil(result.rating) && result.rating % 1 !== 0,
                                                'bx bx-star': n > result.rating
                                                }"
                                                class="align-center text-muted me-2"></i>
                                            </div>    
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <a class="text-reset" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <span class="text-muted fs-18"><i class="mdi mdi-dots-vertical"></i></span>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <Link class="dropdown-item" @click.prevent="viewProfile(result)" v-if="permissions.includes('driver-rating-view')"><i class="  bx bx-show-alt align-center text-muted me-2"></i> 
                                                        {{$t("view")}}</Link> 
                                                </ul>
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
