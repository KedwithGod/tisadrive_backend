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
    requestmodel: {
      type: Object,
      required: true
    },
    currency : Array,
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
                const response = await axios.get(`/webuser/list`, { params });
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
     <h3>{{$t("history")}}</h3>
</BCardHeader>
<BCardBody class="border border-dashed border-end-0 border-start-0">
    <div>
    <!-- Filter Dropdown -->
<!-- Filter Dropdown -->
<!-- <div class="filter-container">
          <label for="trip-status">Filter by Trip Status:</label>
          <select v-model="selectedStatus" id="trip-status" @change="applyFilter">
            <option value="">All</option>
            <option value="_completed">Completed</option>
            <option value="_cancelled">Cancelled</option>
            <option value="_upcoming">Upcoming</option>
          </select>
        </div> -->
</div>
   
    <div class="tab-content  text-muted">
        <div class="tab-pane active border p-4">
            <BRow>
                <BCol lg="12">
            <div class="d-flex float-end gap-3 mb-3">
                <!-- <h5 class=" fw-bold  mb-3">{{ $t("rides") }}</h5> -->
                <div class="">
                    <!-- <searchbar @search="fetchSearch"></searchbar> -->
                </div> 
            <select class="form-select" v-model="selectedStatus" id="trip-status" @change="applyFilter">
                <option value="">{{$t("all")}}</option>
                <option value="_completed">{{$t("completed")}}</option>
                <option value="_cancelled">{{$t("cancelled")}}</option>
                <option value="_upcoming">{{$t("upcoming")}}</option>
            </select>
                    
                              
            </div> 
        </BCol>
        </BRow>           
            <!-- <div data-simplebar style="height: 300px;" class="table-responsive px-3 mx-n3"> -->
            <div  class="table-responsive px-3 mx-n3">
                <table class="table align-middle position-relative table-nowrap">
                    <thead class="table-active">
                        <tr>
                            <th scope="col">{{$t("s_no")}}</th>
                            <th scope="col">{{$t("request_id")}}</th>
                            <th scope="col">{{$t("pickup_location")}}</th>
                            <th scope="col">{{ $t("drop_location") }}</th>
                            <th scope="col">{{ $t("transport_type") }}</th>
                            <th scope="col">{{ $t("trip_status") }}</th>
                            <th scope="col">{{ $t("amount") }}</th>
                            <th scope="col">{{ $t("action") }}</th>
                        </tr>
                    </thead>
                    <tbody v-if="filteredResults.length > 0">
                        <tr v-for="(result, index) in filteredResults" :key="index">
                            <td>{{ index+1 }}</td>
                            <td>{{ result.request_number }}</td>
                            <td >
                                <div class="address">
                                    {{ result.pick_address }}
                                </div>
                                <div>
                                    <!-- {{ new Date(result.trip_start_time).toLocaleTimeString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true }) }} -->
                                    {{ result.converted_created_at }}
                                </div>
                            </td>
                            <td >
                                <div class="address">
                                    {{ result.drop_address }}
                                </div>
                                <div>
                                    <!-- {{ new Date(result.completed_at).toLocaleTimeString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true }) }} -->
                                    {{result.converted_completed_at}}
                                </div>
                            </td>
                            <td>
                                <div class="d-flex">
                                    <img :src="result.vehicle_type_image" alt="" class="avatar-sm rounded-circle me-2"/>
                                    <div>
                                        <span>{{ result.transport_type }}</span>
                                        <!-- <p>{{ new Date(result.arrived_at).toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true }) }}</p> -->
                                         <p>{{ result.converted_created_at }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <BBadge :class="{
                                    'text-uppercase': true,
                                    'text-bg-success': rideStatus(result) === 'Completed' || rideStatus(result) === 'Accepted' || rideStatus(result) === 'Driver Started',
                                    'text-bg-danger': rideStatus(result) === 'Cancelled',
                                    'text-bg-info': rideStatus(result) === 'On Trip',
                                    'text-bg-warning': rideStatus(result) === 'Upcoming' || rideStatus(result) === 'Driver Arrived' || rideStatus(result) === 'Searching',
                                }">{{ rideStatus(result) }} </BBadge>
                            </td>
                            <td>
                                {{currency}} {{ result.request_eta_amount }}
                            </td>
                            <td>
                                <button @click.prevent="viewData(result)" type="button" class="btn btn-primary btn-sm">{{ $t("view") }}</button>
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

