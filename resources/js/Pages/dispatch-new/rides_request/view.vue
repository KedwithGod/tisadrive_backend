<script>
import { Link, Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/dispatchmain.vue";
import PageHeader from "@/Components/page-header.vue";
import Swal from "sweetalert2";
import { ref, onMounted } from "vue";
import axios from "axios";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import search from "@/Components/widgets/search.vue";
import searchbar from "@/Components/widgets/searchbar.vue";
import { useI18n } from 'vue-i18n';
import googleMap from '@/Components/googleMap.vue';


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
        Multiselect,
        flatPickr,
        Link,
        search,
        searchbar,
        googleMap,

    },
    props: {
        successMessage: String,
        alertMessage: String,

        serviceLocations: {
            type: Object,
        },
        googleMapKey: String,
        app_for: String,
        rejected_drivers: Object,
        firebaseConfig: Object,
        baseUrl: String,

        request: Object,
    },
    setup(props) {
        const { t } = useI18n();
        const result = ref(props.request);
        const rejected_drivers = ref(props.rejected_drivers);
        const modalShow = ref(false);

        const proof = ref(props.request.requestProofs.data);
        const stops = ref(props.request.requestStops.data);
        const successMessage = ref(props.successMessage || '');
        const alertMessage = ref(props.alertMessage || '');

        const user_mobile = ref("**********");
        const user_email = ref("**********");
        const driver_mobile =ref("**********");
        const driver_email =ref("**********");

        const dismissMessage = () => {
            successMessage.value = "";
            alertMessage.value = "";
        };

        const closeModal = () => {
            modalShow.value = false;
        };
        const deleteData = async (dataId) => {
            try {
                const response = await axios.get(`/rides-request/cancel/${dataId}`);
                result.value = response.data.request;
                modalShow.value = false;
                Swal.fire(t('success'), t('trip_cancelled_successfully'), 'success');
            } catch (error) {
                Swal.fire(t('error'), t('failed_to_cancel_trip'), 'error');
            }
        };
        const rideStatus = (trip) => {
            if (trip.is_cancelled) {
                return { text: t('cancelled'), colorClass: 'text-danger' }; // Red
            } else if (trip.is_completed) {
                return { text: t('completed'), colorClass: 'text-success' }; // Green
            } else if (trip.is_trip_start) {
                return { text: t('on_trip'), colorClass: 'text-primary' }; // Blue
            } else if (trip.is_driver_arrived) {
                return { text: t('driver_arrived'), colorClass: 'text-warning' }; // Yellow
            } else if (trip.is_later && trip.is_driver_started) {
                return { text: t('driver_started'), colorClass: 'text-info' }; // Light Blue
            } else if (trip.driver_id) {
                return { text: t('accepted'), colorClass: 'text-secondary' }; // Grey
            } else if (!trip.is_later) {
                return { text: t('searching'), colorClass: 'text-muted' }; // Light Grey
            } else {
                return { text: t('upcoming'), colorClass: 'text-dark' }; // Black
            }
        };
        const cancelledBy = (cancel) =>{
            if(cancel.cancel_method == 0){
                return t('automatic');
            }else if(cancel.cancel_method == 1){
                return t('cancelled_by_user');
            }else if(cancel.cancel_method == 2){
                return t('cancelled_by_driver');
            }else{
                return t('cancelled_by_dispatcher')
            }
        };
        const formatDateTime = () => {
            const now = new Date();
            const options = { 
                weekday: 'short', 
                day: 'numeric', 
                month: 'short', 
                year: 'numeric' 
            };
            return now.toLocaleDateString('en-US', options);
        }

        const deleteModal = async (itemId) => {
            Swal.fire({
                title: "Are you sure?",
                text: "You want to be cancel this ride!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#34c38f",
                cancelButtonColor: "#f46a6a",
                confirmButtonText: "Yes, Cancel it!",
                cancelButtonText: "Close",
            }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const response = await axios.get(`/dispatcher/rides_request/cancel/${itemId}`);
                if (response.data.success) {
                    // Update local status immediately
                    const updatedRequest = response.data.request;
                    result.value = updatedRequest;
                    Swal.fire(t('success'), t('trip_cancelled_successfully'), 'success');
                }
            } catch (error) {
                console.error(t('error_deleting_data'), error);
                Swal.fire(t('error'), t('failed_to_delete_the_data'), "error");
            }
        }
    });
        };

        onMounted( async ()=> {
            if (!result.value.is_cancelled && !result.value.is_completed) {
        try {
            const firebaseConfig = props.firebaseConfig;
            if (!firebase.apps.length) {
                firebase.initializeApp(firebaseConfig);
            }
            const database = firebase.database();
            const tripRef = database.ref(`requests/${result.value.id}`);
            tripRef.on('value', (snapshot) => {
                const val = snapshot.val();
                if (val) {
                    if (val.hasOwnProperty('is_completed')) {
                        result.value.is_completed = true;
                    }
                    if (val.accept !== 1) {
                        result.value.driver_id = null;
                    }
                    if (result.value.is_later && val.hasOwnProperty('modified_by_driver')) {
                        result.value.is_driver_started = 1;
                    }
                    if (val.trip_arrived == 1) {
                        result.value.is_driver_arrived = true;
                        if (!result.value.converted_arrived_at) {
                            result.value.converted_arrived_at = formatDateTime();
                        }
                    }
                    if (val.trip_start == 1) {
                        result.value.is_trip_start = true;
                        if (!result.value.converted_trip_start_time) {
                            result.value.converted_trip_start_time = formatDateTime();
                        }
                    }
                    if (val.is_cancelled || val.is_cancel) {
                        result.value.is_cancelled = true;
                        setTimeout(() => {
                            window.location.reload();
                         }, 2000);
                        
                    }
                }
            });
        } catch (error) {
            console.error('Error initializing Firebase or fetching settings:', error);
        }
    }

        });
        
        if(props.app_for != "demo"){
            if(result.value.userDetail){
                user_mobile.value = result.value.userDetail.data?.mobile;
                user_email.value = result.value.userDetail.data?.email;
            }
            if(result.value.driverDetail){
                driver_mobile.value = result.value.driverDetail.data?.mobile;
                driver_email.value = result.value.driverDetail.data?.email;
            }
        }
        const dispatch_type = ref(t('normal'));

        if(result.value.is_bid_ride){
            dispatch_type.value = t('bidding');
        }
        if(result.value.is_rental){
            dispatch_type.value = t('rental');
        }
        if(result.value.is_out_station){
            if(result.value.is_round_trip){
                dispatch_type.value = t('round_trip_outstation_trip');
            }else{
                dispatch_type.value = t('one_way_outstation_trip');
            }
        }

        return {
            result,
            modalShow,
            successMessage,
            alertMessage,
            rejected_drivers,
            deleteModal,
            closeModal,
            deleteData,
            stops,
            proof,
            rideStatus,
            user_mobile,
            user_email,
            driver_mobile,
            dispatch_type,
            driver_email,
            dismissMessage,
            cancelledBy
        };
    },
};
</script>

<template>
    <Layout>

        <Head title="View Details" />
        <PageHeader :title="$t('view_details')" :pageTitle="$t('view_details')" pageLink="/dispatcher/rides_request"/>
        <BRow>
            <BCol lg="12">
                <BCard no-body id="tasksList">
                    <BCardHeader>
                        <h4 class="mb-1"> {{$t("map_view")}}</h4>
                    </BCardHeader>
                    <BCardBody>
                        <div class="row">  
                            <div class="mt-3"></div> 
                            <div class="col-sm-12">

                                <div class="card">
                                    <div class="card-body">
                                        <div style="height: 400px;">
                                            <googleMap
                                                :baseUrl="baseUrl"
                                                :pick_location="{lat:result.pick_lat, lng:result.pick_lng}"
                                                :drop_location="{lat:result.drop_lat, lng:result.drop_lng}"
                                                :stops="result.stops"
                                                :polyline="result.poly_line"
                                                :map_key="googleMapKey"
                                                :libraries="['marker','geometry']"
                                            >
                                                {{$t("map_loading")}}
                                            </googleMap>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-4 col-md-6">
                                <div class="card card-animate border border-dashed border-primary">
                                    <div class="card-body text-center">
                                        <h4 class="mb-3 flex-grow-1 text-start text-muted badge bg-secondary-subtle fs-18">{{$t("pickup_details")}}</h4>
                                        <div class="flex-grow-1">
                                                <img src="@assets/images/pickup.gif" class="img-fluid" style="width:55px;height:55px">
                                        </div>
                                        <h5 class="mb-3 fs-18">{{$t("location")}}:</h5>
                                        <h6 class="fs-14 mb-4">{{ result.pick_address }}</h6>
                                    </div>
                                </div>
                            </div>
                            <div v-if="stops.length>0" class="col-xxl-4 col-md-6">
                                <div class="card card-animate border border-dashed border-primary">
                                    <div class="card-body text-center">
                                        <h4 class="mb-3 flex-grow-1 text-start text-muted badge bg-secondary-subtle fs-18">{{$t("stop_details")}}</h4>
                                        <div class="flex-grow-1">
                                                <img src="@assets/images/pickup.gif" class="img-fluid" style="width:55px;height:55px">
                                        </div>
                                        <h5 class="mb-3 fs-18">{{$t("location")}}:</h5>
                                        <h6 class="fs-14 mb-4" v-for="(stop, index) in stops">{{ stop.address }}</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-4 col-md-6" v-if = "result.drop_address">
                                <div class="card card-animate border border-dashed border-primary">
                                    <div class="card-body text-center">
                                        <h4 class="mb-3 flex-grow-1 text-start text-muted badge bg-secondary-subtle fs-18">{{$t("drop_details")}}</h4>
                                        <div class="flex-grow-1">
                                                <img src="@assets/images/drop.gif" class="img-fluid" style="width:55px;height:55px">
                                        </div>
                                        <h5 class="mb-3 fs-18">{{$t("location")}}:</h5>
                                        <h6 class="fs-14 mb-4">{{ result.drop_address }}</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-4 col-md-6">
                                <div class="card card-animate border border-dashed border-primary">
                                    <div class="card-body text-center">
                                        <h4 class="mb-3 flex-grow-1 text-start text-muted badge bg-secondary-subtle fs-18">{{$t("trip_status")}} {{ result.is_bid_ride ? $t('bidding') : '' }}</h4>
                                        <div class="flex-grow-1 mt-2">
                                                <img src="@assets/images/stat.gif" class="img-fluid" style="width:55px;height:55px">
                                        </div>
                                        <h5 class=" mt-5 mb-3 fs-18" :class="rideStatus(result).colorClass">{{ rideStatus(result).text }}</h5>
                                        <h5 v-if="result.is_cancelled" class="fs-14 text-danger">( {{ cancelledBy(result) }} )</h5>
                                        <BButton class="btn btn-danger btn-md" v-if="!result.is_cancelled&&!result.is_completed" type="button" @click.prevent="deleteModal(result.id)">
                                                <i class=" bx bx-show-alt align-center text-muted me-2"></i>  {{$t("cancel")}}
                                        </Bbutton>
                                    </div>
                                </div>
                            </div>
                        
                        </div>
                        
                     </BCardBody>
                </BCard>
            </BCol>
        </BRow>

<!-- request details -->
<BRow>
    <BCol lg="12">
        <BCard no-body id="tasksList">
            <BCardHeader>
                 <h4 class="mb-1"> {{$t("request_details")}}</h4>
            </BCardHeader>
            <BCardBody>
                <div class="row">
                    <div class="col-xxl-4 col-md-6">
                        <div class="card card-animate border border-dashed border-primary">
                            <div class="card-body">
                                <h4 class="mb-3 flex-grow-1 text-start text-muted badge bg-secondary-subtle fs-18">{{$t("request")}}</h4>
                                <div class="flex-grow-1 mt-2 text-center">
                                        <img src="@assets/images/mark.gif" class="img-fluid" style="width:55px;height:55px">
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="flex-shrink-0">
                                        <p class="text-muted mb-0">{{$t("zone")}}:</p>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <h6 class="mb-0">{{ result.zone_name ?? '-' }}</h6>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="flex-shrink-0">
                                        <p class="text-muted mb-0">{{$t("transport_type")}}:</p>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <h6 class="mb-0">
                                        <span v-if="result.transport_type === 'taxi'">{{ $t('taxi') }}</span>
                                        <span v-else-if="result.transport_type === 'delivery'">{{ $t('delivery') }}</span>
                                        <span v-else>{{ $t('all') }}</span>
                                        </h6>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="flex-shrink-0">
                                        <p class="text-muted mb-0">{{$t("vehicle_type")}}:</p>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <h6 class="mb-0">{{ result.vehicle_type_name ?? '-' }}</h6>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="flex-shrink-0">
                                        <p class="text-muted mb-0">{{$t("trip_time")}}:</p>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <h6 class="mb-0">{{ result.is_later ? result.converted_trip_start_time :result.converted_created_at }}</h6>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-2" v-if="result.rental_package_name !== '-'">
                                    <div class="flex-shrink-0">
                                        <p class="text-muted mb-0">{{$t("rental_pack")}}:</p>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <h6 class="mb-0">{{ result.rental_package_name }}</h6>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="flex-shrink-0">
                                        <p class="text-muted mb-0">{{$t("ride_type")}}:</p>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <h6 class="mb-0">{{ dispatch_type }}</h6>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-2" v-if="result.is_round_trip">
                                    <div class="flex-shrink-0">
                                        <p class="text-muted mb-0">{{$t("return_time")}}:</p>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <h6 class="mb-0">{{ result.return_time }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-4 col-md-6">
                        <div class="card card-animate border border-dashed border-primary">
                            <div class="card-body">
                                <h4 class="mb-3 flex-grow-1 text-start text-muted badge bg-secondary-subtle fs-18">{{$t("user_details")}}</h4>
                                <div class="flex-grow-1 mt-2 text-center">
                                        <img src="@assets/images/user.gif" class="img-fluid" style="width:55px;height:55px">
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="flex-shrink-0">
                                        <p class="text-muted mb-0">{{$t("name")}}:</p>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <h6 class="mb-0">{{ result.userDetail ? result.userDetail.data?.name : '-' }}</h6>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="flex-shrink-0">
                                        <p class="text-muted mb-0">{{$t("email")}}:</p>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <h6 class="mb-0">{{ user_email }}</h6>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="flex-shrink-0">
                                        <p class="text-muted mb-0">{{$t("mobile")}}:</p>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <h6 class="mb-0">{{ user_mobile }}</h6>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="flex-shrink-0">
                                        <p class="text-muted mb-0">{{$t("rating")}}:</p>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <h6 class="mb-0">{{ result.userDetail ? result.userDetail.data?.rating : 0 }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-4 col-md-6" v-if="result.driverDetail">
                        <div class="card card-animate border border-dashed border-primary">
                            <div class="card-body">
                                <h4 class="mb-3 flex-grow-1 text-start text-muted badge bg-secondary-subtle fs-18">{{$t("driver_details")}}</h4>
                                <div class="flex-grow-1 mt-2 text-center">
                                        <img src="@assets/images/driver.gif" class="img-fluid" style="width:55px;height:55px">
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="flex-shrink-0">
                                        <p class="text-muted mb-0">{{$t("name")}}:</p>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <h6 class="mb-0">{{ result.driverDetail ? result.driverDetail.data?.name : '-' }}</h6>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="flex-shrink-0">
                                        <p class="text-muted mb-0">{{$t("email")}}:</p>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <h6 class="mb-0">{{ driver_email }}</h6>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="flex-shrink-0">
                                        <p class="text-muted mb-0">{{$t("mobile")}}:</p>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <h6 class="mb-0">{{ driver_mobile }}</h6>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="flex-shrink-0">
                                        <p class="text-muted mb-0">{{$t("rating")}}:</p>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <h6 class="mb-0">{{ result.driverDetail ? result.driverDetail.data?.rating : 0 }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </BCardBody>
        </BCard>
    </BCol>
</BRow>
<!-- request details end -->

<!-- bill details begin -->
        <BRow  v-if="result.requestBill">
            <BCol lg="col-12">
                <BCard>
                    <BCardHeader>
                        <h4 class="mb-1"> {{$t("bill_details")}}</h4>
                    </BCardHeader>
                    <BCardBody>
                        <BRow>
                        <div class="col-lg-6">
                        <div class="card border mt-3">
                            <div class="table-responsive">
                                <table class="table align-middle position-relative table-nowrap">
                                    <thead class="table-active">
                                        <tr class="fs-16">
                                            <th scope="col"> {{$t("title")}}</th>
                                            <th scope="col"> {{$t("description")}}</th>
                                            <th scope="col" > {{$t("price")}}</th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="result.accepted_ride_fare > 0" >
                                        <tr>
                                            <td> {{$t("bid_fare")}}</td>
                                            <td> -</td>
                                            <td>{{result.requested_currency_symbol}}{{ result.accepted_ride_fare }}</td>
                                        </tr>
                                        <tr v-if="result.additional_charges_amount > 0">
                                            <td> {{result.additional_charges_reason}}</td>
                                            <td>-</td>
                                            <td>{{ result.additional_charges_amount }}</td>
                                        </tr>
                                    </tbody>
                                    <tbody v-else >
                                        <tr>
                                            <td> {{$t("base_price")}}</td>
                                            <td> {{$t("for_first")}} {{ result.requestBill.data.base_distance }} {{$t("km")}}</td>
                                            <td>{{result.requested_currency_symbol}}{{ result.requestBill.data.base_price }}</td>
                                        </tr>
                                        <tr>
                                            <td> {{$t("distance_price")}}</td>
                                            <td> {{ result.requestBill.data.total_distance > result.requestBill.data.base_distance ? result.requestBill.data.total_distance - result.requestBill.data.base_distance:0 }}  {{$t("km")}} * {{result.requested_currency_symbol}} {{ result.requestBill.data.price_per_distance }} / {{$t("km")}}</td>
                                            <td>{{result.requested_currency_symbol}}{{ result.requestBill.data.distance_price }}</td>
                                        </tr>
                                        <tr>
                                            <td> {{$t("time_price")}}</td>
                                            <td>{{ result.requestBill.data.total_time }}  {{$t("min")}} * {{result.requested_currency_symbol}} {{ result.requestBill.data.price_per_time }} / {{$t("min")}}</td>
                                            <td>{{result.requested_currency_symbol}}{{ result.requestBill.data.time_price }}</td>
                                        </tr>
                                        <tr v-if="result.requestBill.data.cancellation_fee > 0">
                                            <td> {{$t("cancellation_fee")}}</td>
                                            <td>-</td>
                                            <td>{{result.requested_currency_symbol}}{{ result.requestBill.data.cancellation_fee }}</td>
                                        </tr>
                                        <tr>
                                            <td> {{$t("service_tax")}}</td>
                                            <td>-</td>
                                            <td>{{result.requested_currency_symbol}}{{ result.requestBill.data.service_tax }}</td>
                                        </tr>
                                        <tr class="text-danger" v-if="result.requestBill.data.promo_discount > 0">
                                            <td> {{$t("promo_discount")}}</td>
                                            <td>-</td>
                                            <td>{{result.requested_currency_symbol}}{{ result.requestBill.data.promo_discount }}</td>
                                        </tr>
                                        <tr>
                                            <td> {{$t("admin_commission")}}</td>
                                            <td>-</td>
                                            <td>{{result.requested_currency_symbol}}{{ result.requestBill.data.admin_commision }}</td>
                                        </tr>
                                        <tr v-if="result.additional_charges_amount > 0">
                                            <td> {{result.additional_charges_reason}}</td>
                                            <td>-</td>
                                            <td>{{ result.additional_charges_amount }}</td>
                                        </tr>
                                    </tbody>
                                    <tfoot class="bg-body bg-success-subtle">
                                        <tr>
                                            <td colspan="2" class="text-success"> <h4 class="text-success">{{$t("total")}}</h4></td>
                                            <td class="text-success"><h4 class="text-success">{{result.requested_currency_symbol}}{{ result.requestBill.data.total_amount }}</h4> </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        </div>
                        <div class="col-lg-6">
                        <div class="card border mt-3">
                            <div class="table-responsive">
                                <table class="table align-middle position-relative table-nowrap">
                                    <thead class="table-active">
                                        <tr class="fs-16">
                                            <th scope="col"> {{$t("title")}}</th>
                                            <th scope="col"> </th>
                                            <th scope="col" > {{$t("price")}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="text-danger">
                                            <td colspan="2"> {{$t("admin_commission")}}</td>
                                            <td>{{result.requested_currency_symbol}}{{ result.requestBill.data.admin_commision }}</td>
                                        </tr>
                                        <tr class="text-danger">
                                            <td colspan="2"> {{$t("service_tax")}}</td>
                                            <td>{{result.requested_currency_symbol}}{{ result.requestBill.data.service_tax }}</td>
                                        </tr>
                                        <tr class="text-danger">
                                            <td colspan="2"> {{$t("admin_commission_from_driver")}}</td>
                                            <td>{{result.requested_currency_symbol}}{{ result.requestBill.data.admin_commision_from_driver }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"> {{$t("driver_commission")}}</td>
                                            <td>{{result.requested_currency_symbol}}{{ result.requestBill.data.driver_commision }}</td>
                                        </tr>
                                    </tbody>
                                    <tfoot class="bg-body bg-success-subtle">
                                        <tr>
                                            <td colspan="2" class="text-success"> <h4 class="text-success">{{$t("total")}}</h4></td>
                                            <td class="text-success"><h4 class="text-success">{{result.requested_currency_symbol}}{{ result.requestBill.data.total_amount }}</h4> </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        </div>
                    </BRow>
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>
 <!-- bill details end  -->

<!-- timeline -->
        <div class="card">
            <BCardHeader>
                 <h4 class="mb-1"> {{ $t("request_timeline") }}</h4>
            </BCardHeader>
            <div class="card-body">
                <div class="profile-timeline">
                    <div class="accordion accordion-flush" id="accordionFlushExample">
                        <div class="accordion-item border-0">
                            <div class="accordion-header" id="headingOne">
                                <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#" aria-expanded="true" aria-controls="collapseOne">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 border border-4 border-success rounded-circle">
                                            <img :src=" result.userDetail ? result.userDetail.data?.profile_picture : '/assets/images/default-profile-picture.png'" alt="" class="avatar-md rounded-circle">
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="fs-15 mb-0 fw-semibold text-primary">{{ $t("ride_created") }} -<span class="fw-normal text-muted">{{result.converted_created_at}}</span></h6>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div v-if="!result.driver_id && !result.is_cancelled" id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                <div class="accordion-body ms-5 ps-5 pt-0">
                                    <h6 class="mb-1 text-warning" v-if="result.is_later && !result.onSearch"> {{$t("yet_to_search")}}</h6>
                                    <h6 class="mb-1 text-success" v-else-if="!result.driverDetail && !result.is_cancelled && !result.is_completed"> {{$t("searching")}}</h6>
                                </div>
                            </div>
                        </div>
                        <div v-for="(driver, index) in rejected_drivers" class="accordion-item border-0">
                            <div class="accordion-header" id="headingTwo">
                                <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#" aria-expanded="false" aria-controls="collapseTwo">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 border border-4 border-danger rounded-circle">
                                            <img :src="driver.profile_picture" alt="" class="avatar-md rounded-circle">
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="fs-15 mb-1 fw-semibold text-danger">{{$t("rejected")}} - <span class="fw-normal">{{ driver.converted_created_at }}</span></h6>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div id="collapseTwo" class="accordion-collapse collapse show" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                <div class="accordion-body ms-5 ps-5 pt-0">
                                    <h5 class="mb-1">{{ driver.driver_name}}</h5>
                                    <span><span class="badge bg-light text-body fs-14 fw-large">{{ driver.driver_rating }}<i class="mdi mdi-star text-warning ms-1"></i></span></span>
                                </div>
                            </div>
                        </div>
                        <div v-if="result.driverDetail" class="accordion-item border-0">
                            <div class="accordion-header" id="headingThree">
                                <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#" aria-expanded="false" aria-controls="collapseThree">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 border border-4 border-success rounded-circle">
                                            <img :src="result.driverDetail?.data?.profile_picture" alt="" class="avatar-md rounded-circle">
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="fs-15 mb-1 fw-semibold text-success">{{$t("accepted")}} - <span class="fw-normal">{{result.converted_accepted_at}}</span></h6>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div id="collapseThree" class="accordion-collapse collapse show" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                <div class="accordion-body ms-5 ps-5 pt-0">
                                    <h5 class="mb-1"> {{ result.driverDetail.data?.name }}</h5>
                                    <span><span class="badge bg-light text-body fs-14 fw-large">{{result.driverDetail.data?.rating}}<i class="mdi mdi-star text-warning ms-1"></i></span></span>
                                </div>
                            </div>
                        </div>
                        <div v-if="result.is_driver_arrived && !result.instant_ride" class="accordion-item border-0">
                            <div class="accordion-header" id="headingThree">
                                <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#" aria-expanded="false" aria-controls="collapseThree">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 border border-4 border-warning rounded-circle">
                                            <img :src="result.driverDetail?.data?.profile_picture" alt="" class="avatar-md rounded-circle">
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="fs-15 mb-1 fw-semibold text-warning">{{$t("arrived")}} - <span class="fw-normal">{{result.converted_arrived_at}}</span></h6>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div id="collapseThree" class="accordion-collapse collapse show" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                <div class="accordion-body ms-5 ps-5 pt-0">
                                    <h5 class="mb-1"> {{ result.driverDetail?.data?.name }}</h5>
                                </div>
                            </div>
                            <div v-if="!result.is_trip_start" id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                <div class="accordion-body ms-5 ps-5 pt-0">
                                    <h6 class="mb-1 text-success">{{$t("waiting")}}</h6>
                                </div>
                            </div>
                        </div>
                        <div v-if="result.is_trip_start" class="accordion-item border-0">
                            <div class="accordion-header" id="headingThree">
                                <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#" aria-expanded="false" aria-controls="collapseThree">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 border border-4 border-success rounded-circle">
                                            <img :src="result.driverDetail?.data?.profile_picture" alt="" class="avatar-md rounded-circle">
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="fs-15 mb-1 fw-semibold text-success">{{ result.transport_type =='delivery' ? $t("tripLoaded") : $t("tripStarted")}} - <span class="fw-normal">{{ result.converted_trip_start_time}}</span></h6>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div id="collapseThree" class="accordion-collapse collapse show" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                <div class="accordion-body ms-5 ps-5 pt-0">
                                    <h5 class="mb-1"> {{ result.driverDetail?.data?.name }}</h5>
                                </div>
                            </div>
                        </div>
                        <div v-if="result.is_completed" class="accordion-item border-0">
                            <div class="accordion-header" id="headingFour">
                                <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseFour" aria-expanded="false">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 border border-4 border-success rounded-circle">
                                            <img :src="result.driverDetail?.data?.profile_picture" alt="" class="avatar-md rounded-circle">
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="fs-14 mb-0 fw-semibold text-success">{{ result.transport_type =='delivery' ? $t("tripUnloaded") : $t("ride_completed")}} -<span class="fw-normal text-muted">{{result.converted_completed_at}}</span></h6>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div v-if="result.is_cancelled" class="accordion-item border-0">
                            <div class="accordion-header" id="headingFour">
                                <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseFour" aria-expanded="false">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 border border-4 border-danger rounded-circle">
                                            <img :src="result.userDetail?.data?.profile_picture" alt="" class="avatar-md rounded-circle">
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="fs-14 mb-0 fw-semibold text-danger">{{$t("ride_cancelled")}} -<span class="fw-normal text-muted">{{ result.converted_cancelled_at}}</span></h6>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!--end accordion-->
                </div>
            </div>
        </div>
<!-- end timeline -->

<!-- Proof -->
 <div class="card" v-if="proof.length >0" >
    <BCardHeader>
            <h4 class="mb-1"> {{ $t("request_proof") }}</h4>
    </BCardHeader>
    <div class="card-body">
        <div class="row">
        <div class="col-lg-4" v-for="(doc, index) in proof">
            <img :src="doc.proof_image" style="height:100%; width:100%" />
        </div>
        </div>
    </div>
 </div>
<!-- end Proof -->

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
.profile-timeline .accordion-item::before {
    content: "";
    border-left: 2px dashed var(--vz-border-color);
    position: absolute;
    height: 100%;
    left: 46px;
}
</style>
