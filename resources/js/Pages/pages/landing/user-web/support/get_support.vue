<script>
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import axios from 'axios';
import Layout from "@/Layouts/main.vue";
import { Link, Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed,onMounted } from "vue";
import searchbar from "@/Components/widgets/searchbar.vue";
import UserWebMenu from "@/Components/UserWebMenu.vue";
import Pagination from "@/Components/Pagination.vue";
import { initI18n } from '@/i18n';
import { useI18n } from 'vue-i18n';

export default {
  props: {
    user: Object,
  },
  data() {
    return {
    //     selectedStatus: "", // To hold the selected filter option
    //   filteredRides: [], // Filtered rides based on the selected filter
    //   SearchQuery: '',
      searchTerm: '',
    };
  },
  components: {
    Layout,
    Multiselect,
    flatPickr,
    Head,
    searchbar,
    UserWebMenu,
    Pagination,
    Link,
    useForm,
    router,
  },
  setup(props) {
        const { t } = useI18n();
        const searchTerm = ref('');
        const selectedStatus = ref(""); // Properly define selectedStatus here
        const filter = useForm({
            transport_type: null,
            dispatch_type: null,
            status: null,
        });
        const results = ref([]); // Spread the results to make them reactive
        const paginator = ref({}); // Spread the results to make them reactive




        const fetchSearch = async (value) => {
            searchTerm.value = value;
            fetchDatas();
        };

        const fetchDatas = async (page = 1) => {
            try {
                const params = filter.data();
                params.search = searchTerm.value;
                params.page = page;
                const response = await axios.get(`/get-support/list`, { params });
                results.value = response.data.results;
                paginator.value = response.data.paginator;
            } catch (error) {
                console.error(t('error_fetching_users'), error);
            }
        };

        const handlePageChanged = async (page) => {
            fetchDatas(page);
        };
        
        const rideStatus = (trip) => {
            if(trip.is_cancelled){
                return 'Cancelled';
            }else if(trip.is_completed){
                return 'Completed';
            }else if(trip.is_trip_start){
                return 'On Trip';
            }else if(trip.is_driver_arrived){
                return 'Driver Arrived';
            }else if(trip.is_later && trip.is_driver_started){
                return 'Driver Started';
            }else if(trip.is_driver_started){
                return 'Accepted';
            }else if(!trip.is_later){
                return 'Searching';
            }else{
                return 'Upcoming'
            }
        };

         // Computed property to filter results based on selected status
    const filteredResults = computed(() => {
      if (selectedStatus.value) {
        return results.value.filter(result => {
          if (selectedStatus.value === "_completed") {
            return result.is_completed;
          } else if (selectedStatus.value === "_cancelled") {
            return result.is_cancelled;
          } else if (selectedStatus.value === "_upcoming") {
            return !result.is_completed && !result.is_cancelled; // Upcoming status
          }
          return true; // No filter applied
        });
      }
      return results.value; // Return all results if no filter
    });

    const viewData = async (result) =>  {
        router.get(`/history/view/${result.id}`);
        };
    
    onMounted(async() => {
      await initI18n('en');
    })

    const createTicket = async (titleType) =>  {
        router.get(`/create-ticket`, { titleType: 'general' });
    };

    const viewTicket = async (result) =>  {
        router.get(`/ticket/view/${result.id}`); 

    };
        
        return {
            results,
            searchTerm,
            rideStatus,
            fetchSearch,
            paginator,
            fetchDatas,
            filter,
            handlePageChanged,
            filteredResults, // Return the filtered results computed property
            selectedStatus, // Include selectedStatus in the return object
            viewData,
            createTicket,
            viewTicket
        };
    },
  computed: {
 
  },
  mounted() {
    this.fetchDatas();
  },
  methods: {
//     applyFilter() {
//     this.searchTerm = this.selectedStatus;  // Use selected status as the search term

//     // Fetch filtered data (this will also reset pagination to the first page)
//     this.fetchDatas(1);
//   }

applyFilter() {
    //   if (this.selectedStatus) {
    //     // Apply the filter based on the selected trip status
    //     this.filteredRides = this.requestmodel.data.filter(request => request.trip_status === this.selectedStatus);
    //   } else {
    //     // Show all rides if no filter is selected
    //     this.filteredRides = this.requestmodel.data;
    //   }
    }
  
  },
};
</script>



<template>
    <BCard>
        <Head title="Taxi Ride" />
        <BCardHeader class="border-0">
            <!-- menu Offcanvas -->
            <UserWebMenu :user="user" />
            <!-- menu end -->
        </BCardHeader>

        <BRow>
            <BCol lg="12">
                <BCard no-body id="tasksList">
                    <BCardHeader class="border-0">
                        <h3>{{$t("support_tickets")}}</h3>
                    </BCardHeader>
                    <BCardBody class="border border-dashed border-end-0 border-start-0">    
                        <div class="tab-content  text-muted">
                            <div class="tab-pane active border p-4">
                                <BRow>
                                    <BCol lg="12">
                                <div class="d-flex float-end gap-3 mb-3">
                                    <Link @click.prevent="createTicket(general)">
                                        <BButton variant="primary" class="float-end"> <i
                                                class="ri-add-line align-bottom me-1"></i> 
                                                {{$t("make_ticket")}}
                                        </BButton>
                                    </Link>  
                                </div> 
                            </BCol>
                            </BRow>           
                                <!-- <div data-simplebar style="height: 300px;" class="table-responsive px-3 mx-n3"> -->
                                <div  class="table-responsive px-3 mx-n3">
                                    <table class="table align-middle position-relative table-nowrap">
                                        <thead class="table-active">
                                            <tr>
                                                <th scope="col">{{$t("s_no")}}</th>
                                                <th scope="col">{{$t("ticket_id")}}</th>
                                                <th scope="col">{{$t("support_type")}}</th>
                                                <th scope="col">{{$t("title")}}</th>
                                                <th scope="col">{{ $t("status") }}</th>
                                                <th scope="col">{{ $t("action") }}</th>
                                            </tr>
                                        </thead>
                                        <tbody v-if="results.length > 0">
                                            <tr v-for="(result, index) in results" :key="index">
                                                <td> {{ index+1 }} </td>
                                                <td> {{ result.ticket_id }} </td>
                                                <td>{{ result.support_type}} <span v-if="result.request_id">({{result.request_id}})</span></td>
                                                <td> {{ result.title }} </td>
                                                <td>
                                                    <template v-if="result.status == 1">
                                                        <BBadge variant="success" class="text-uppercase">{{$t("pending")}}</BBadge>
                                                    </template>
                                                    <template v-if="result.status == 2">
                                                        <BBadge variant="warning" class="text-uppercase">{{$t("acknowledge")}}</BBadge>
                                                    </template>
                                                    <template v-if="result.status == 3">
                                                        <BBadge variant="danger" class="text-uppercase">{{$t("closed")}}</BBadge>
                                                    </template>
                                                </td>
                                                <td> 
                                                    <BButton class="btn btn-soft-success btn-sm m-2" data-bs-toggle="tooltip" v-b-tooltip.hover :title="$t('view')" @click.prevent="viewTicket(result)">
                                                        <i class ='ri-account-circle-line bx-xs'></i>
                                                    </BButton> 
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
                            </div>
                        </div>        
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>
        <Pagination :paginator="paginator" @page-changed="handlePageChanged" />
        <BOffcanvas v-model="rightOffcanvas" placement="end" :title="$t('goods_type')" header-class="bg-light"
            body-class="p-0 overflow-hidden" footer-class="border-top p-3 text-center">
            <BFrom action="" class="d-flex flex-column justify-content-end h-100">
                <div class="offcanvas-body">
                    <div class="mb-4">
                        <label for="datepicker-range"
                            class="form-label text-muted text-uppercase fw-semibold mb-3">{{$t("goods_type_for")}}</label>
                        <select class="form-control" data-choices data-choices-search-false goods_type_name="choices-select-status"
                            id="choices-select-status" v-model="filter.goods_types_for">
                            <option value="truck">{{$t("truck")}}</option>
                            <option value="motor_bike">{{$t("motor_bike")}}</option>
                            <option value="both">{{$t("all")}}</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="status-select"
                            class="form-label text-muted text-uppercase fw-semibold mb-3">{{$t("status")}}</label>
                        <BRow class="g-2">
                            <BCol lg="6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="active" v-model="filter.status" value=1 />
                                    <label class="form-check-label" for="active">{{$t("active")}}</label>
                                </div>
                            </BCol>
                            <BCol lg="6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="inactive" v-model="filter.status" value=0 />
                                    <label class="form-check-label" for="inactive">{{$t("inactive")}}</label>
                                </div>
                            </BCol>
                        </BRow>
                    </div>
                    <div>
                    </div>
                </div>
                <!--end offcanvas-body-->
                <div class="offcanvas-footer border-top p-3 text-center hstack gap-2">
                    <BButton variant="light" @click="clearFilter" class="w-100">{{$t("clear_filter")}}</BButton>
                    <BButton type="submit"  @click="filterData" variant="success" class="w-100">
                        {{$t("apply")}}
                    </BButton>
                </div>
                <!--end offcanvas-footer-->
            </BFrom>
        </BOffcanvas>
    </BCard>
</template>
<style>
.address {
    display: inline-block;
    width: 200px;
    white-space: nowrap;
    overflow: hidden !important;
    text-overflow: ellipsis;
}
.filter-container {
  margin-bottom: 1rem;
}
</style>

