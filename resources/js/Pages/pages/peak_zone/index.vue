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
        searchbar
    },
    props: {
        successMessage: String,
        alertMessage: String,
        serviceLocations: Array,

        zones: {
            type: Object,
            required: true,
        }


    },
    setup(props) {
        const { t } = useI18n();
        const filter = useForm({
            zone_id: null
        });
        const results = ref([]); // Spread the results to make them reactive
        const paginator = ref({}); // Spread the results to make them reactive
        const successMessage = ref(props.successMessage || '');
        const alertMessage = ref(props.alertMessage || '');
        const serviceLocations = ref(props.serviceLocations || '');
        const modalShow = ref(false);
        const modalFilter = ref(false);
        const deleteItemId = ref(null);

        const dismissMessage = () => {
            successMessage.value = "";
            alertMessage.value = "";
        };

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
                        const response = await axios.post(`/peak_zone/update_status/${id}`, { id: id, active: status });
                        const index = results.value.findIndex(item => item.id === id);
                        if (index !== -1) {
                            results.value[index] = response.data.peakzone; // Update the active status locally
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


        const fetchDatas = async (page = 1) => {
            try {
                const params = filter.data();
                params.page = page;
                const response = await axios.get(`/peak_zone/fetch`, { params });
                results.value = response.data.results;
                paginator.value = response.data.paginator;
                modalFilter.value = false;
                    // Only include status parameter if it's not empty (to show all)
                    if (filter.status !== "") {
                        params.status = filter.status;
                    }

            } catch (error) {
                console.error(t('error_fetching_peak_zone'), error);
            }
        };

        const handlePageChanged = async (page) => {
            fetchDatas(page);
        };

        const mapData = async (result) =>  {
            router.get(`/peak_zone/map/${result.id}`); 
        };
        return {
            results,
            deleteItemId,
            successMessage,
            alertMessage,
            filterData,
            closeModal,
            dismissMessage,
            paginator,
            modalFilter,
            serviceLocations,
            clearFilter,
            fetchDatas,
            filter,
            handlePageChanged,
            mapData,
            toggleActiveStatus,
            rightOffcanvas

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

    <Head :title="$t('peakzone')" />
    <PageHeader :title="$t('zone')" :pageTitle="$t('zone')" />
        <BRow>
            <BCol lg="12">
                <BCard no-body id="tasksList">

                    <BCardHeader class="border-0">
                        <BRow class="g-2">
                            <BCol md="3">
                            </BCol>
                            <BCol md="auto" class="ms-auto">
                                <div class="d-flex align-items-center gap-2">
                                    <BButton variant="danger" @click="rightOffcanvas = true"><i
                                        class="ri-filter-2-line me-1 align-bottom"></i>  {{ $t("filters") }}
                                    </BButton>
                                </div>
                            </BCol>
                        </BRow>
                    </BCardHeader>
                    <BCardBody class="border border-dashed border-end-0 border-start-0">
                        <div class="table">
                            <table class="table align-middle position-relative table-nowrap">
                                <thead class="table-active">
                                    <tr>
                                        <!-- <th scope="col"> {{ $t("s_no") }}</th> -->
                                        <th scope="col"> {{ $t("name") }}</th>
                                        <th scope="col"> {{ $t("start_time") }}</th>
                                        <th scope="col"> {{ $t("status") }}</th>
                                        <th scope="col"  v-if="permissions.includes('peak-zone-map-view')"> {{ $t("action") }}</th>
                                    </tr>
                                </thead>
                                <tbody v-if="results.length > 0">
                                    <tr v-for="(result, index) in results" :key="index">
                                        <!-- <td>{{ index+1 }}</td> -->
                                        <td>
                                            <div class="d-flex">
                                                <div class="flex-grow-1">{{ result.name }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            {{ result.start_time }}
                                        </td>
                                        <td>
                                            <div :class="{
                                                    'form-check': true,
                                                    'form-switch': true,
                                                    'form-switch-lg': true,
                                                    'form-switch-success': result.active,
                                                }">
                                                <input class="form-check-input" type="checkbox" role="switch" 
                                                @click.prevent="toggleActiveStatus(result.id, !result.active)" 
                                                :id="'status_'+result.id" :checked="result.active" :disabled="!permissions.includes('peak-zone-toggle')">
                                            </div>
                                        </td>
                                        <td v-if="permissions.includes('peak-zone-map-view')">
                                            <BButton class="btn btn-soft-info btn-sm m-2" size="sm" type="button" data-bs-toggle="tooltip" v-b-tooltip.hover>
                                            <div class="dropdown">
                                                <a class="text-reset" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <span class="text-muted fs-18"><i class="mdi mdi-dots-vertical"></i></span>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a class="dropdown-item" href="#" @click.prevent="mapData(result)">
                                                        <i class="ri-inbox-archive-line align-center text-muted me-2"></i>{{$t("zone_map_view")}}
                                                    </a>
                                                </div>
                                            </div>
                                            </BButton>
                                        </td>
                                    </tr>
                                </tbody>
                    <tbody v-else>
                        <tr>
                            <td colspan="4" class="text-center">
                                <img src="@assets/images/search-file.gif" alt="Loading..." style="width:100px" />
                                <h5> {{ $t("no_data_found") }}</h5>
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
        <BOffcanvas v-model="rightOffcanvas" placement="end" :title="$t('zone_filters')" header-class="bg-light"
        body-class="p-0 overflow-hidden" footer-class="border-top p-3 text-center">
            <BForm>
                <div class="offcanvas-body">
                   
                    <div class="mb-4">
                        <label for="status-select" class="form-label text-muted text-uppercase fw-semibold mb-3">{{ $t("status") }}</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="statusActive" v-model="filter.status" value="1">
                                <label class="form-check-label" for="statusActive">{{ $t("active") }}</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="statusInactive" v-model="filter.status" value="0">
                                <label class="form-check-label" for="statusInactive">{{ $t("inactive") }}</label>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="offcanvas-footer border-top p-3 text-center hstack gap-2">
                    <BButton variant="light" class="w-100" @click="clearFilter">{{ $t("clear_filter") }}</BButton>
                    <BButton type="submit" variant="success" class="w-100" @click="filterData" >{{ $t("apply") }}</BButton>
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
a{
    cursor: pointer;
}

.text-danger {
    padding-top: 5px;
}

</style>
