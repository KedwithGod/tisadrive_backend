<script>
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import axios from 'axios';
import Layout from "@/Layouts/main.vue";
import { Link, Head, useForm, router} from '@inertiajs/vue3';
import { ref, computed ,onMounted} from "vue";
import searchbar from "@/Components/widgets/searchbar.vue";
import UserWebMenu from "@/Components/UserWebMenu.vue";
import Pagination from "@/Components/Pagination.vue";
import { useI18n } from 'vue-i18n';
import  { initI18n } from "@/i18n";
import Swal from "sweetalert2";

export default {
  props: {
    successMessage: String,
        alertMessage: String,

        serviceLocations: {
            type: Object,
            required: true,
        },
        googleMapKey: String,
        pick_icon: String,
        drop_icon: String,
        stop_icon: String,
        rejected_drivers: Object,
        firebaseConfig: Object,

        request: Object,
        user: Object,
        supportTicket:Boolean
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
        const result = ref(props.request);
        const rejected_drivers = ref(props.rejected_drivers);
        const modalShow = ref(false);
        const stops = ref(props.request ? props.request.requestStops.data :  []);
        const paymentGateways = ref([]);

        const successMessage = ref(props.successMessage || '');
        const alertMessage = ref(props.alertMessage || '');

        const dismissMessage = () => {
            successMessage.value = "";
            alertMessage.value = "";
        };

        const calculateZoomLevel = (bounds) => {
            const GLOBE_WIDTH = 256; // a constant in Google's map projection
            const pixelWidth = document.getElementById('map').offsetWidth;
            const maxZoom = 21;

            const west = bounds.getSouthWest().lng();
            const east = bounds.getNorthEast().lng();

            let angle = east - west;
            if (angle < 0) {
                angle += 360;
            }

            return Math.floor(Math.log(pixelWidth * 360 / angle / GLOBE_WIDTH) / Math.LN2);
        };
        

        const { googleMapKey } = props;

        const initializeMap = () => {
            const pick_icon = props.pick_icon;
            const drop_icon = props.drop_icon;
            const trip = result.value;


            // initialize Positions
            const pick_position = new google.maps.LatLng(trip.pick_lat, trip.pick_lng);
            const drop_position = new google.maps.LatLng(trip.drop_lat, trip.drop_lng);
            const stops = trip.stops;
            const stop_positions = [];
            if( stops){
                stops.forEach((stop) => {
                    const stop_location = new google.maps.LatLng(stop.latitude, stop.longitude)
                    stop_positions.push(stop_location);
                })
            }
            // initialize bondable locations
            const bounds = new google.maps.LatLngBounds();
            bounds.extend(pick_position);
            bounds.extend(drop_position);
            stop_positions.forEach((stop_location) => {
                bounds.extend(stop_location);
            })
            const map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: trip.pick_lat, lng: trip.pick_lng },
                zoom: calculateZoomLevel(bounds),
            });

            const pickMarker = new google.maps.Marker({
                position: pick_position,
                icon: pick_icon,
                map: map
            });

            onMounted( async ()=> {
            try{
                const firebaseConfig = props.firebaseConfig;
                if (!firebase.apps.length) {
                    firebase.initializeApp(firebaseConfig);
                }
                const database = firebase.database();
                const tripRef = database.ref(`requests/${result.value.id}`);
                tripRef.on('value',function(snapshot){
                    const val =  snapshot.val();
                    if(val.is_completed){
                        result.value.is_completed = true;
                    }
                    if(val.accept !== 1){
                        result.value.driver_id = null;
                    }
                    if(val.driver_id){
                        result.value.driver_id = val.driver_id;
                    }
                    if(val.hasOwnProperty('modified_by_driver')){
                        result.value.is_driver_started = 1;
                    }
                    if(val.trip_arrived == 1){
                        result.value.is_driver_arrived = true;
                    }
                    if(val.trip_start == 1){
                        result.value.is_trip_start = true;
                    }
                    if(val.is_completed){
                        result.value.is_completed = true;
                    }
                    if(val.is_cancelled || val.is_cancel){
                        result.value.is_cancelled = true;
                    }
                })
            } catch (error) {
                console.error(t('error_initializing_firebase_or_fetching_settings'), error);
            }
        });

        const dropMarker = new google.maps.Marker({
                position: drop_position,
                icon: drop_icon,
                map: map
            });
            stop_positions.forEach((stop_location) => {
                const stopMarker = new google.maps.Marker({
                    position: stop_location,
                    icon: drop_icon,
                    map: map
                });
            })
            map.fitBounds(bounds);
            if(trip.poly_line) {

                const decodedPath = google.maps.geometry.encoding.decodePath(trip.poly_line);

                // Adjust Map According the locations
                if(decodedPath.length > 0){
                    const flightPath = new google.maps.Polyline({
                        path: decodedPath,
                        geodesic: true,
                        strokeColor: '#0066FF',
                        strokeOpacity: 4.0,
                        strokeWeight: 5
                    });
                    flightPath.setMap(map);
                }

            }
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
        const rideStatus = ref('');
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
                        await deleteData(itemId);
                    } catch (error) {
                        console.error(t('error_deleting_data'), error);
                        Swal.fire(t('error'), t('failed_to_delete_the_data'), "error");
                    }
                }
            });
        };

        onMounted( async ()=> {

            const currentLocale = localStorage.getItem('locale') || 'en';
            await initI18n(currentLocale);
            console.log(result.value);
            // if (result.value.is_completed && !result.value.is_paid && result.value.payment_type_string == "card") {
            //     paymentGateways.value = result.value.payment_gateways.filter((item) => item.enabled);
            // }
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
                        rideStatus.value = t("ride_completed");
                        setTimeout(() => {
                            window.location.reload();
                         }, 2000);
                    }
                    if (val.accept !== 1) {
                        result.value.driver_id = null;
                        rideStatus.value = t("searching");
                    }
                    if (val.driver_id && result.value.driver_id) {
                        window.location.reload();
                    }
                    if (result.value.is_later && val.hasOwnProperty('modified_by_driver')) {
                        rideStatus.value = t("driver_started");
                        result.value.is_driver_started = 1;
                    }
                    if (val.trip_arrived == 1) {
                        rideStatus.value = t("driver_arrived");
                        result.value.is_driver_arrived = true;
                        if (!result.value.converted_arrived_at) {
                            result.value.converted_arrived_at = formatDateTime();
                        }
                    }
                    if (val.trip_start == 1) {
                        rideStatus.value = t("on_trip");
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
    }else{
        
        if(result.value.is_cancelled){
            rideStatus.value = t("ride_cancelled");
        }else if(result.value.is_completed){
            rideStatus.value = t("ride_completed");
        }else if(result.value.is_result.value_start){
            rideStatus.value = t("on_trip");
        }else if(result.value.is_driver_arrived){
            rideStatus.value = t("driver_arrived");
        }else if(result.value.is_later && result.value.is_driver_started){
            rideStatus.value = t("driver_started");
        }else if(result.value.driver_id){
            rideStatus.value = t("accepted");
        }else if(!result.value.is_later){
            rideStatus.value = t("searching");
        }else{
        rideStatus.value = t("upcoming");
        }
    }
        });
        onMounted(() => {
            if (!googleMapKey) {
                console.error(t('google_map_api_key_is_null_or_undefined'));
                return;
            }

            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=${googleMapKey}&libraries=geometry`;
            script.onload = () => {
                initializeMap();
            };
            document.head.appendChild(script);
            
        });
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
        };

        const createTicket = async () =>  {
            router.get(`/create-ticket`, {id :result.value.id, titleType: 'request'});
        };
        return {
            result,
            modalShow,
            successMessage,
            alertMessage,
            paymentGateways,
            rejected_drivers,
            deleteModal,
            closeModal,
            stops,
            dispatch_type,
            deleteData,
            rideStatus,
            dismissMessage,
            createTicket
        };
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
                            <h3>{{$t("view_details")}}</h3>
                            <Link @click.prevent="createTicket(result)" v-if = "result.is_completed">
                                <BButton variant="primary" class="float-end " v-if="supportTicket == 1"> <i
                                        class="ri-add-line align-bottom me-1"></i> 
                                        {{$t("make_ticket")}}
                                </BButton>
                            </Link>
                        </BCardHeader>
                        <BCardBody class="border border-dashed border-end-0 border-start-0">  
                            <BRow>
                                <BCol lg="12">
                                    <!-- <BCard no-body id="tasksList">
                                        <BCardBody> -->
                                            <div class="row">  
                                                <div class="mt-3"></div> 
                                                <div class="col-sm-12">
                                                    <!-- <h5> {{$t("map_view")}}</h5> -->

                                                    <div class="card">
                                                        <div class="card-body">
                                                            <div id="map" style="height: 400px;"></div>
                                                        </div>

                                                        <div class="d-flex m-auto">
                                                            <div class="text-center p-3 fs-18"><span class="badge bg-primary-subtle text-primary p-3">{{$t("request_id")}} : <strong>{{ result.request_number ?? '-' }}</strong></span></div>
                                                            <div class="text-center p-3 fs-18"><span class="badge bg-success-subtle text-success p-3">{{$t("ride_otp")}} : <strong>{{ result.ride_otp ?? '-' }}</strong></span></div>
                                                        </div>
                                                        <!-- <Link @click.prevent="createTicket(result)">
                                                            <BButton variant="primary" class="float-end"> <i
                                                                    class="ri-add-line align-bottom me-1"></i> 
                                                                    {{$t("make_ticket")}}
                                                            </BButton>
                                                        </Link> -->
                                                        <!-- <div class="text-center p-3 fs-18"><span class=" badge bg-success-subtle text-success p-3">{{$t("ride_otp")}} : <strong>{{ result.ride_otp ?? '-' }}</strong></span></div> -->                                                        
                                                    </div>

                                                   <!-- {{$t("ride_otp")}}     {{ result.ride_otp ?? '-' }} -->

                                                </div>
                                            </div>
<!-- 
                                        </BCardBody>
                                    </BCard> -->
                                </BCol>
                            </BRow>
                        </BCardBody>
                    </BCard>
                </BCol>
            </BRow>

<!-- details cards  -->
<BRow>
    <BCol lg="4" v-if="result.driverDetail">
    <BCard class="card-animate border border-dashed border-primary">
        <BCardBody>
            <div class="d-flex align-items-center">
                <div class="avatar-md flex-shrink-0">
                    <span class="avatar-title bg-light rounded-circle">
                        <img :src="result.driverDetail?.data?.profile_picture" class="img-fluid avatar-md rounded-circle" alt="">
                    </span>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="fs-14 mb-1">{{ result.driverDetail ? result.driverDetail.data?.name : '-' }}</h6>
                    <p class="text-muted fs-12 mb-0">
                        <i class="mdi mdi-circle-medium text-success fs-15 align-middle"></i> {{ result.driverDetail ? result.driverDetail.data?.mobile : '-' }}
                    </p>
                    <p class="text-muted fs-12 mb-0">
                        <i class="mdi mdi-circle-medium text-success fs-15 align-middle"></i> {{ result.driverDetail ? result.driverDetail.data?.email : '-' }}
                    </p>
                </div>
            </div>
    
        </BCardBody>
    </BCard>
    </BCol>
    <BCol lg="4">
    <BCard class="card-animate border border-dashed border-primary">
        <BCardBody>
            <div class="d-flex align-items-center">
                <div class="avatar-xs flex-shrink-0">
                    <span class="avatar-title bg-light rounded-circle">
                        <i class="ri-map-pin-fill icon-dual-success fs-18"></i>
                    </span>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="fs-14 mb-1">{{$t("pickup_location")}}</h6>
                    <p class="text-muted fs-14 mb-2 mt-2">
                    {{ result.pick_address }}
                    </p>
                </div>
            </div>
    
        </BCardBody>
    </BCard>
    </BCol>
    <BCol lg="4">
    <BCard class="card-animate border border-dashed border-primary" v-if = "result.drop_address">
        <BCardBody>
            <div class="d-flex align-items-center">
                <div class="avatar-xs flex-shrink-0">
                    <span class="avatar-title bg-light rounded-circle">
                        <i class="ri-map-pin-fill icon-dual-danger fs-18"></i>
                    </span>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="fs-14 mb-1">{{$t("drop_location")}}</h6>
                    <p class="text-muted fs-14 mb-2 mt-2">
                    {{ result.drop_address }}
                    </p>
                </div>
            </div>
    
        </BCardBody>
    </BCard>
    </BCol>
    <BCol lg="6">
    <BCard class="card-animate border border-dashed border-primary">
        <BCardBody>
            <div class="d-flex align-items-center">
                <div class="avatar-xs flex-shrink-0">
                    <span class="avatar-title bg-light rounded-circle">
                        <i class="ri-route-fill icon-dual-primary fs-18"></i>
                    </span>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="fs-14 mb-1">{{$t("trip_status")}}{{ result.is_bid_ride ? $t('bidding') : '' }}</h6>
                    <span class="text-muted fs-14 mb-2 mt-2">
                        <div>
                            <p class="text-dark">{{ rideStatus }}</p>
                            <BButton class="btn btn-danger btn-md" v-if="!result.is_cancelled&& !result.is_trip_start" type="button" @click.prevent="deleteModal(result.id)">
                                <i class=" bx bx-show-alt align-center text-muted me-2"></i>  {{$t("cancel")}}
                            </Bbutton>
                            <BButton data-bs-toggle="modal" data-bs-target="#pay" class="btn btn-success btn-md" v-if="paymentGateways.length>0" type="button">
                                 {{$t("pay")}}
                            </Bbutton>
                        </div>
                    </span>
                </div>
                <div class="flex-grow-1 ms-3" v-if="result.rental_package_name !== '-'">
                    <h6 class="fs-14 mb-1">{{$t("rental_pack")}}</h6>
                    <span class="text-muted fs-14 mb-2 mt-2">
                        <div>
                            <p class="text-dark">{{ result.rental_package_name }}</p>
                        </div>
                    </span>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="fs-14 mb-1">{{$t("ride_type")}}</h6>
                    <span class="text-muted fs-14 mb-2 mt-2">
                        <div>
                            <p class="text-dark">{{ dispatch_type }}</p>
                        </div>
                    </span>
                   <p v-if="!result.is_completed">{{$t("tripStarted")}} : {{result.converted_trip_start_time}}</p> 
                    <h6 v-if="result.is_round_trip" class="fs-14 mb-1">{{$t("return_time")}}</h6>
                    <span v-if="result.is_round_trip" class="text-muted fs-14 mb-2 mt-2">
                        <div>
                            <p class="text-dark">{{ result.return_time }}</p>
                        </div>
                    </span>
                </div>
                <div class="flex-shrink-0 text-end">
                    <h6 class="mb-1 text-success">{{ result.vehicle_type_name ?? '-' }}</h6>
                    <p class="text-muted fs-13 mb-0">
                        <span v-if="result.transport_type === 'taxi'">{{ $t('taxi') }} {{ result.is_bid_ride ? $t('bidding') : '' }}</span>
                        <span v-else-if="result.transport_type === 'delivery'">{{ $t('delivery') }} {{ result.is_bid_ride ? $t('bidding') : '' }}</span>
                        <span v-else>{{ $t('all') }}</span>
                    </p>
                </div>
            </div>
            
    </BCardBody>
    </BCard>
    </BCol>
    <BCol lg="4" v-if="stops.length>0">
    <BCard class="card-animate border border-dashed border-primary">
        <BCardBody>
            <div class="d-flex align-items-center">
                <div class="avatar-xs flex-shrink-0">
                    <span class="avatar-title bg-light rounded-circle">
                        <i class="ri-map-pin-fill icon-dual-danger fs-18"></i>
                    </span>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="fs-14 mb-1">{{$t("location")}}</h6>
                    <p class="text-muted fs-14 mb-2 mt-2" v-for="(stop, index) in stops">
                    {{ stop.address }}
                    </p>
                </div>
            </div>
    
        </BCardBody>
    </BCard>
    </BCol>
</BRow>
<BRow>
    <!-- details end -->

    <!-- <div class="text-center ">
        <a :href="`tel:{{result.driverDetail ? result.driverDetail.data?.mobile : '-'}}`"  type="button" class="btn btn-success btn-label waves-effect waves-light fs-18"><i class="ri-phone-fill label-icon align-middle fs-16 me-2"></i> {{$t("call")}}</a>
    </div> -->
    <div class="text-center">
    <a
        v-if="result.driverDetail && result.driverDetail.data?.mobile"
        :href="`tel:${result.driverDetail.data.mobile}`"
        type="button"
        class="btn btn-success btn-label waves-effect waves-light fs-18"
    >
        <i class="ri-phone-fill label-icon align-middle fs-16 me-2"></i> {{ $t("call") }}
    </a>
    <span v-else class="text-muted"></span>
</div>

    <!-- bill  -->
<BCol lg="12" v-if="result.requestBill">
<BCard>
    <BCardBody>
        <h4 class="text-center">{{ $t("fare_breakup") }}</h4>
        <div class="border-top border-top-dashed mt-2">
        <table class="table table-borderless table-nowrap align-middle mb-0 m-auto" style="width:250px">
            <tbody>
                <tr>
                    <td>{{$t("base_price")}}</td>
                    <td class="text-end">{{result.requested_currency_symbol}} {{ result.requestBill.data.base_price }}</td>
                </tr>
                <tr>
                    <td>{{$t("distance_price")}}</td>
                    <td class="text-end">{{result.requested_currency_symbol}} {{ result.requestBill.data.distance_price }}</td>
                </tr>
                <tr>
                    <td>{{$t("time_price")}}</td>
                    <td class="text-end">{{result.requested_currency_symbol}} {{ result.requestBill.data.time_price }}</td>
                </tr>
                <tr v-if="result.requestBill.data.cancellation_fee > 0" >
                    <td>{{$t("cancellation_fee")}}</td>
                    <td class="text-end">{{result.requested_currency_symbol}} {{ result.requestBill.data.cancellation_fee }}</td>
                </tr>
                <tr>
                    <td>{{$t("service_tax")}}</td>
                    <td class="text-end">{{result.requested_currency_symbol}} {{ result.requestBill.data.service_tax }}</td>
                </tr>
                <tr>
                    <td>{{$t("convenience_fee")}}</td>
                    <td class="text-end">{{result.requested_currency_symbol}} {{ result.requestBill.data.admin_commision }}</td>
                </tr>
                <tr v-if="result.requestBill.data.promo_discount > 0" >
                    <td>{{$t("promo_discount")}}</td>
                    <td class="text-end">{{result.requested_currency_symbol}} {{ result.requestBill.data.promo_discount }}</td>
                </tr>                
                <tr v-if="result.requestBill.data.tips > 0">
                    <td> {{$t("driver_tips")}}</td>
                    <td  class="text-end">{{result.requested_currency_symbol}}{{ result.requestBill.data.tips }}</td>
                </tr>
                <tr v-if="result.requestBill.data.waiting_charge > 0" >
                    <td>{{$t("waiting_charge")}}({{ $t('For') }} {{result.requestBill.data.waiting_charge}} min)</td>
                    <td class="text-end">{{result.requested_currency_symbol}} {{ result.requestBill.data.waiting_charge }}</td>
                </tr>
                <tr  v-if="result.requestBill.data.airport_surge_fee > 0 && result.transport_type === 'taxi'">
                    <td> {{$t("airport_surge_fee")}}</td>
                    <td class="text-end">{{result.requested_currency_symbol}}{{ result.requestBill.data.airport_surge_fee }}</td>
                </tr>
                <tr v-if="result.payment_opt" >
                    <td>{{$t("payment_option")}}</td>
                    <td v-if="result.payment_opt == 0" class="text-end">
                    <BBadge variant="success" class="text-uppercase">{{$t("card")}}</BBadge>
                    </td>
                    <td v-if="result.payment_opt == 1" class="text-end">
                    <BBadge variant="success" class="text-uppercase">{{$t("cash")}}</BBadge>
                    </td>
                    <td v-if = "result.payment_opt == 2" class="text-end">
                    <BBadge variant="success" class="text-uppercase">{{$t("wallet")}}</BBadge>
                    </td>
                    <td v-if = "result.payment_opt == 3" class="text-end">
                    <BBadge variant="success" class="text-uppercase">{{$t("wallet/cash")}}</BBadge>
                    </td>
                </tr>
                <tr class="border-top border-top-dashed fs-15">
                    <th scope="row">{{$t("total")}}</th>
                    <th class="text-end">{{result.requested_currency_symbol}} {{ result.requestBill.data.total_amount }}</th>
                </tr>
            </tbody>
        </table>
        <!--end table-->
    </div>
    </BCardBody>
</BCard>
</BCol>
</BRow>

                   
    </BCard>

<!-- payment list modal -->
<div id="pay" class="modal fade" tabindex="-1" aria-labelledby="payLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="payLabel">{{$t("payment_option")}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
               <BRow style="max-height: 400px; overflow-y: auto;">
                <BCol v-for="(gateway, index) in paymentGateways" lg="12">
                    <BLink :href="gateway['url']+`?user_id=${user.id}&request_id=${result.id}&amount=${result.requestBill.data.total_amount}`" class="d-flex">
                        <img :src="gateway['image']" :alt="gateway['gateway']" width="200" class="img-fluid m-auto">
                    </BLink>
                </BCol>
                </BRow>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
 <!-- end -->
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
.profile-timeline .accordion-item::before {
    content: "";
    border-left: 2px dashed var(--vz-border-color);
    position: absolute;
    height: 100%;
    left: 46px;
}
</style>

