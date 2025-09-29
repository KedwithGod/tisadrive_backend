<script>
import { Link, Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
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
import L from "leaflet";
import 'leaflet-routing-machine';
import "leaflet/dist/leaflet.css";


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

    },
    props: {
        successMessage: String,
        alertMessage: String,

        pick_icon: String,
        drop_icon: String,
        stop_icon: String,
        firebaseConfig: Object,
        request: Object,
    },
    setup(props) {
        const { t } = useI18n();
        const result = ref(props.request);
        const modalShow = ref(false);
        const showStops = ref(false);
        const stops = ref(props.request ? props.request.requestStops.data :  []);
        const rejected_drivers = ref(props.request ? props.request.rejectedDrivers.data :  []);
        const successMessage = ref(props.successMessage || '');
        const alertMessage = ref(props.alertMessage || '');
        const pickupMarker = ref(null);
        const dropMarker = ref(null);
        const stopMarker = ref([]);

        const dismissMessage = () => {
            successMessage.value = "";
            alertMessage.value = "";
        };

        const initializeMap = async () => {
            map.value = L.map('map',{
                zoomControl: false,
                dragging: false,
                scrollWheelZoom: false,
                doubleClickZoom: false,
                touchZoom: false
            }).setView([result.value.pick_lat, result.value.pick_lng], 12);
            
            map.value.dragging.disable();
            map.value.scrollWheelZoom.disable();
            map.value.doubleClickZoom.disable();
            map.value.touchZoom.disable();

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
            }).addTo(map.value);

        
            const pickupIcon = L.icon({
                iconUrl: '/image/map/pickup.png',
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32]
            });

            const dropIcon = L.icon({
                iconUrl: '/image/map/drop.png',
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32]
            });

            pickupMarker.value = L.marker([result.value.pick_lat, result.value.pick_lng], { icon: pickupIcon, draggable:false } ).addTo(map.value);
            dropMarker.value = L.marker([result.value.drop_lat, result.value.drop_lng], { icon:  dropIcon, draggable:false } ).addTo(map.value);

            stops.value.forEach( (stop,index)=> {
                const stopIcon = L.icon({
                    iconUrl: '/image/map/'+index+'.png',
                    iconSize: [32, 32],
                    iconAnchor: [16, 32],
                    popupAnchor: [0, -32]
                });
                const marker = L.marker([stop.latitude, stop.longitude], { icon:  stopIcon, draggable:false } ).addTo(map.value);
                stopMarker.value[index] = marker;
            })
            
            L.Routing.control({
                waypoints: [
                    pickupMarker.value.getLatLng(),
                    ...stops.value.map(stop => L.latLng(stop.latitude, stop.longitude)),
                    dropMarker.value.getLatLng(),
                ],
                routeWhileDragging: false,
                createMarker: function() { return null; },
                addWaypoints: false,
                router: L.Routing.osrmv1({
                    serviceUrl: 'https://router.project-osrm.org/route/v1',
                    timeout: 30000
                }),
                lineOptions: {
                styles: [{ color: 'blue', weight: 4 }]
                },

            }).addTo(map.value);
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
            }else if(trip.driver_id){
                return 'Accepted';
            }else if(!trip.is_later){
                return 'Searching';
            }else{
                return 'Upcoming'
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
                text: "You won't be able to revert this!",
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
            if(!result.value.is_cancelled && !result.value.is_completed){
                try{
                    const firebaseConfig = props.firebaseConfig;
                    if (!firebase.apps.length) {
                        firebase.initializeApp(firebaseConfig);
                    }
                    const database = firebase.database();
                    const tripRef = database.ref(`requests/${result.value.id}`);
                    tripRef.on('value',function(snapshot){
                        const val =  snapshot.val();
                        if(val.hasOwnProperty('is_completed')){
                            result.value.is_completed = true;
                        }
                        if(val.accept !== 1){
                            result.value.driver_id = null;
                        }
                        if(result.value.is_later && val.hasOwnProperty('modified_by_driver')){
                            result.value.is_driver_started = 1;
                        }
                        if(val.trip_arrived == 1){
                            result.value.is_driver_arrived = true;
                            if(!result.value.converted_arrived_at){
                                result.value.converted_arrived_at = formatDateTime();
                            }
                        }
                        if(val.trip_start == 1){
                            result.value.is_trip_start = true;
                            if(!result.value.converted_trip_start_time){
                                result.value.converted_trip_start_time = formatDateTime();
                            }
                        }
                        if(val.hasOwnProperty('is_cancelled') || val.hasOwnProperty('is_cancel')){
                            result.value.is_cancelled = true;
                            window.location.reload();
                        }
                    })
                } catch (error) {
                    console.error('Error initializing Firebase or fetching settings:', error);
                }
            }
        });
        onMounted(() => {
            initializeMap();
            
            
        });

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
            showStops,
            rideStatus,
            dismissMessage,
        };
    },
};
</script>

<template>
    <Layout>
        <Head title="View Details" />
        <PageHeader :title="$t('view_details')" :pageTitle="$t('view_details')" pageLink="/rides-request"/>
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
                                <!-- <h5> {{$t("map_view")}}</h5> -->

                                <div class="card">
                                    <div class="card-body">
                                        <div id="map" style="height: 400px;"></div>
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
                                        <h6 class="fs-14 mb-4" v-for="(stop, index) in stops">{{ result.stop_address }}</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-4 col-md-6">
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
                                        <h5 class=" mt-5 mb-3 fs-18">{{ rideStatus(result) }}</h5>
                                        <BButton class="btn btn-danger btn-md" v-if="!result.is_cancelled&&!result.is_completed" type="button" @click.prevent="deleteModal(result.id)">
                                                <i class=" bx bx-show-alt align-center text-muted me-2"></i>  {{$t("cancel")}}
                                        </Bbutton>
                                    </div>
                                </div>
                            </div>
                        
                            <!-- <div class="col-sm-4 mt-5">
                                <div class="card border">
                                    <div class="card-header bg-body">
                                        <h5> {{$t("pickup_details")}}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div>
                                            <h5> {{$t("location")}}:</h5>
                                            <p>{{ result.pick_address }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="stops.length>0" class="card border">
                                    <div class="card-header bg-body">
                                        <h5> {{$t("stop_details")}}</h5>
                                    </div>
                                    <div class="card-body">
                                        <h5> {{$t("location")}}:</h5>
                                        <div v-for="(stop, index) in stops">    
                                            <p>{{ stop.address }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card border">
                                    <div class="card-header bg-body">
                                        <h5> {{$t("drop_details")}}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div>
                                            <h5> {{$t("location")}}:</h5>
                                            <p>{{ result.drop_address }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card border">
                                    <div class="card-header bg-body">
                                        <h5> {{$t("trip_status")}} {{ result.is_bid_ride ? $t('bidding') : '' }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div>
                                            <p>{{ rideStatus(result) }}</p>
                                            <BButton class="btn btn-danger btn-md" v-if="!result.is_cancelled&&!result.is_completed" type="button" @click.prevent="deleteModal(result.id)">
                                                <i class=" bx bx-show-alt align-center text-muted me-2"></i>  {{$t("cancel")}}
                                            </Bbutton>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                        </div>
                        
                            <!-- <div class="row">
                                <div class="mt-5"></div>
                                <div class="col-sm-4">
                                    <div class="card border">
                                        <div class="card-header bg-body">
                                            <h5> {{$t("request")}}</h5>
                                        </div>
                                        <div class="card-body">
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
                                                    <span v-if="result.transport_type === 'taxi'">{{ $t('taxi') }} {{ result.is_bid_ride ? $t('bidding') : '' }}</span>
                                                    <span v-else-if="result.transport_type === 'delivery'">{{ $t('delivery') }} {{ result.is_bid_ride ? $t('bidding') : '' }}</span>
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
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="card border">
                                        <div class="card-header bg-body">
                                            <h5> {{$t("user_details")}}</h5>
                                        </div>
                                        <div class="card-body">
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

                                <div class="col-sm-4">
                                    <div class="card border">
                                        <div class="card-header bg-body">
                                            <h5> {{$t("driver_details")}}</h5>
                                        </div>
                                        <div class="card-body">
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
                            </div> -->
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
                                            <span v-if="result.transport_type === 'taxi'">{{ $t('taxi') }} {{ result.is_bid_ride ? $t('bidding') : '' }}</span>
                                            <span v-else-if="result.transport_type === 'delivery'">{{ $t('delivery') }} {{ result.is_bid_ride ? $t('bidding') : '' }}</span>
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
                        <div class="col-xxl-4 col-md-6">
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
                        <!-- <div class="col-sm-4">
                            <div class="card border">
                                <div class="card-header bg-body">
                                    <h5> {{$t("request")}}</h5>
                                </div>
                                <div class="card-body">
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
                                            <span v-if="result.transport_type === 'taxi'">{{ $t('taxi') }} {{ result.is_bid_ride ? $t('bidding') : '' }}</span>
                                            <span v-else-if="result.transport_type === 'delivery'">{{ $t('delivery') }} {{ result.is_bid_ride ? $t('bidding') : '' }}</span>
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
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="card border">
                                <div class="card-header bg-body">
                                    <h5> {{$t("user_details")}}</h5>
                                </div>
                                <div class="card-body">
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

                        <div class="col-sm-4">
                            <div class="card border">
                                <div class="card-header bg-body">
                                    <h5> {{$t("driver_details")}}</h5>
                                </div>
                                <div class="card-body">
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
                        </div> -->
                    </div>
                </BCardBody>
            </BCard>
        </BCol>
    </BRow>
<!-- request details end -->

<!-- bill details begin -->
<BRow>
    <BCol lg="col-12">
        <BCard>
            <BCardHeader>
                <h4 class="mb-1"> {{$t("bill_details")}}</h4>
            </BCardHeader>
            <BCardBody>
                <div class="card p-3 border mt-3" v-if="result.requestBill">
                    <!-- <div class="card-header">
                    <h4 class="mb-4"> {{$t("bill_details")}}</h4></div> -->
                    <div class="table-responsive">
                        <table class="table align-middle position-relative table-nowrap">
                            <thead class="table-active">
                                <tr class="fs-16">
                                    <th scope="col"> {{$t("title")}}</th>
                                    <th scope="col" > {{$t("description")}}</th>
                                    <th scope="col" > {{$t("price")}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td> {{$t("base_price")}}</td>
                                    <td> {{$t("for_first")}} {{ result.requestBill.data.base_distance }} {{$t("km")}}</td>
                                    <td>{{ result.requestBill.data.base_price }}</td>
                                </tr>
                                <tr>
                                    <td> {{$t("distance_price")}}</td>
                                    <td> {{ result.requestBill.data.total_distance }}  {{$t("km")}} * {{result.requested_currency_symbol}} {{ result.requestBill.data.price_per_distance }} / {{$t("km")}}</td>
                                    <td>{{ result.requestBill.data.distance_price }}</td>
                                </tr>
                                <tr>
                                    <td> {{$t("time_price")}}</td>
                                    <td>{{ result.requestBill.data.total_time }}  {{$t("min")}} * {{result.requested_currency_symbol}} {{ result.requestBill.data.price_per_time }} / {{$t("min")}}</td>
                                    <td>{{ result.requestBill.data.time_price }}</td>
                                </tr>
                                <tr>
                                    <td> {{$t("cancellation_fee")}}</td>
                                    <td>-</td>
                                    <td>{{ result.requestBill.data.cancellation_fee }}</td>
                                </tr>
                                <tr>
                                    <td> {{$t("service_tax")}}</td>
                                    <td>-</td>
                                    <td>{{ result.requestBill.data.service_tax }}</td>
                                </tr>
                                <tr>
                                    <td> {{$t("promo_distance")}}</td>
                                    <td>-</td>
                                    <td>{{ result.requestBill.data.promo_discount }}</td>
                                </tr>
                                <tr>
                                    <td> {{$t("admin_commission")}}</td>
                                    <td>-</td>
                                    <td>{{ result.requestBill.data.admin_commision }}</td>
                                </tr>
                                <tr>
                                    <td> {{$t("admin_commission_for_driver")}}</td>
                                    <td>-</td>
                                    <td>{{ result.requestBill.data.admin_commision_from_driver }}</td>
                                </tr>
                                <tr>
                                    <td> {{$t("driver_commission")}}</td>
                                    <td>-</td>
                                    <td class="text-danger">{{ result.requestBill.data.driver_commision }}</td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-body bg-success-subtle">
                                <tr>
                                    <td colspan="2" class="text-success"> <h4 class="text-success">{{$t("total")}}</h4></td>
                                    <td class="text-success"><h4 class="text-success">{{ result.requestBill.data.total_amount }}</h4> </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div v-else>
                <div class="d-flex">
                    <td colspan="12" class="text-center m-auto">
                        <img src="@assets/images/search-file.gif" alt="Loading..." style="width:100px" />
                        <h5>{{$t("no_data_found")}}</h5>
                    </td>
                </div>
                </div>
                <div class=" d-flex justify-content-center gap-2"  v-if="result.ride_fare">
                    <!-- <button class="btn btn-primary" type="button">Download Customer Invoice</button>
                    <button class="btn btn-primary" type="button">Download Driver Invoice</button>-->
                </div>
                
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
    <!-- <div class="card-header">
        <h5 class="card-title mb-0">{{ $t("request_timeline") }}</h5>
    </div> -->
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
                            <h6 class="mb-1 text-success"> {{$t("searching")}}</h6>
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
                <div v-if="result.is_driver_arrived" class="accordion-item border-0">
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
                            <span><span class="badge bg-light text-body fs-14 fw-large">{{result.driverDetail?.data?.rating}}<i class="mdi mdi-star text-warning ms-1"></i></span></span>
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
                                    <h6 class="fs-15 mb-1 fw-semibold text-success">{{$t("tripStarted")}} - <span class="fw-normal">{{ result.converted_trip_start_time}}</span></h6>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div id="collapseThree" class="accordion-collapse collapse show" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                        <div class="accordion-body ms-5 ps-5 pt-0">
                            <h5 class="mb-1"> {{ result.driverDetail?.data?.name }}</h5>
                            <span><span class="badge bg-light text-body fs-14 fw-large">{{result.driverDetail?.data?.rating}}<i class="mdi mdi-star text-warning ms-1"></i></span></span>
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
                                    <h6 class="fs-14 mb-0 fw-semibold text-success">{{$t("ride_completed")}} -<span class="fw-normal text-muted">{{result.converted_completed_at}}</span></h6>
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
