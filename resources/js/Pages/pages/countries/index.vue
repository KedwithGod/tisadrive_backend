<script>
import { Link, Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import Swal from "sweetalert2";
import { ref, watch } from "vue";
import axios from "axios";
import { debounce } from 'lodash';
import searchbar from "@/Components/widgets/searchbar.vue";
import { mapGetters } from 'vuex';
import { layoutComputed } from "@/state/helpers";
import { useI18n } from 'vue-i18n';

export default {
    components: {
        Layout,
        Link,
        PageHeader,
        Head,
        searchbar,
        Pagination,
    },
    props: {
        successMessage: String,
        alertMessage: String,
    },
    setup(props) {
        const { t } = useI18n();
        const searchTerm = ref("");
        const filter = useForm({
            limit:10,
        });
        const items = ref([]); // Spread the items to make them reactive
        const paginator = ref({}); // Spread the items to make them reactive
        const modalShow = ref(false);
        const modalFilter = ref(false);
        const paginatorOption = ref({}); // Spread the results to make them reactive

        const successMessage = ref(props.successMessage || '');
        const alertMessage = ref(props.alertMessage || '');

        const dismissMessage = () => {
            successMessage.value = "";
            alertMessage.value = "";
        };


        const filterItem = () => {
            modalFilter.value = true;
        };

       
        
        const clearFilter = () => {
            filter.reset();
            fetchItems();
            modalFilter.value = false;
        };


        const closeModal = () => {
            modalShow.value = false;
        };
      
        const fetchSearch = async (value) => {
            searchTerm.value = value;
            fetchItems();
        };

        const editData = async (result) =>  {
            router.get(`/country/${result.id}`); 
        };

        const toggleModel = async (result) => {
            try {
                const response = await axios.post(`/country/toggle_status/${result.id}`);
                result.active = response.data.country.active;
                Swal.fire(t('success'), t('country_status_updated_successfully'), 'success');
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

        const fetchItems = async (page = 1) => {
            try {
                const params = filter.data();
                params.search = searchTerm.value;
                params.page = page;
                const response = await axios.get(`/country/list`, { params });
                items.value = response.data.items;
                paginator.value = response.data.paginator;
                modalFilter.value = false;
                updatePaginatorOptions(paginator.value.total);// Update paginator options dynamically
            } catch (error) {
                // @TODO 
                // return exceptions
                console.error("Error fetching items:", error);
            }
        };

        const updatePaginatorOptions = () => {
            paginatorOption.value = [10, 25, 50, 100,200,500]; // Default static options
        };

        // **Handle per-page changes**
        const changeEntriesPerPage = () => {
            fetchItems(); // Fetch new data
        };

        const handlePageChanged = async (page) => {
            localStorage.setItem("country/list", page); // Save to localStorage

            fetchItems(page);
        };

        return {
            items,
            modalShow,
            successMessage,
            alertMessage,
            filterItem,
            closeModal,
            dismissMessage,
            searchTerm,
            fetchSearch,
            paginator,
            editData,
            modalFilter,
            clearFilter,
            toggleModel,
            fetchItems,
            filter,
            paginatorOption,
            changeEntriesPerPage,
            handlePageChanged

        };
    },
    computed: {
        ...layoutComputed,
        ...mapGetters(['permissions']),
    },
    mounted() {
        this.fetchItems();
        const savedPage = localStorage.getItem("country/list");
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

        <Head title="items" />
        <PageHeader title="countries" pageTitle="Masters" />

        
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
                                     
                                    <Link href="/country/create'" v-if="permissions.includes('add-country')"> 
                                        <BButton variant="primary" class="float-end" >
                                            <i class="ri-add-line align-bottom me-1"></i>{{ $t("add_country") }}
                                        </BButton>
                                    </Link>
                                </div>
                            </BCol>
                        </BRow>
                    </BCardHeader>
                    <BCardBody class="border border-dashed border-end-0 border-start-0">
                        <table class="table table-bordered align-middle table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col"> {{ $t('country_name') }} </th>
                                    <th scope="col">{{$t("icon")}}</th>
                                    <th scope="col" v-if="permissions.includes('toggle_service_Location')">{{ $t("status") }}</th>
                                    <th scope="col"> {{ $t('action') }} </th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-for="country in items" :key="country.id">
                                    <tr>
                                        <td>{{ country.name }}</td>
                                        <td> <img :src="country.flag"  alt="" class="avatar-xs rounded-circle me-2"></td>
                                        <td  v-if="permissions.includes('toggle_service_Location')">
                                            <div :class="{
                                                    'form-check': true,
                                                    'form-switch': true,
                                                    'form-switch-lg': true,
                                                    'form-switch-success': country.active,
                                                }">
                                                <input class="form-check-input" type="checkbox" role="switch" @click.prevent="toggleModel(country)" :id="'status_'+country.id" :checked="country.active">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <BButton @click.prevent="editData(country)" v-if="permissions.includes('edit-country')"
                                                    class="btn btn-soft-warning btn-sm m-2"
                                                    data-bs-toggle="tooltip" v-b-tooltip.hover :title="$t('edit')">
                                                    <i class='bx bxs-edit-alt bx-xs'></i>
                                                </BButton>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>

      
        <BModal v-model="modalFilter" hide-footer dialog-class="modal-dialog-right" title="Filter"
            class="v-modal-custom " size="sm">
            <form >
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
                <BButton variant="primary" class="float-end" @click="fetchItems"> Apply</BButton>
                <BButton variant="outline-primary" class="float-end mx-2" @click="clearFilter">Clear</BButton>
                
            </form>
        </BModal>

        <!-- Pagination -->
        <Pagination :paginator=paginator @page-changed="handlePageChanged"/>
        
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
