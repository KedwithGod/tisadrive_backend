<script>
import { Link, Head, useForm, router } from '@inertiajs/vue3';
import Swal from "sweetalert2";
import { ref, onMounted } from "vue";
import axios from "axios";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import search from "@/Components/widgets/search.vue";
import searchbar from "@/Components/widgets/searchbar.vue";
import  { initI18n } from "@/i18n";
import { useI18n } from 'vue-i18n';
import L from "leaflet";
import 'leaflet-routing-machine';
import "leaflet/dist/leaflet.css";
import polyline from '@mapbox/polyline';


export default {
    data() {
        return {
            rightOffcanvas: false,
        };
    },
    components: {
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

        app_for: String,
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
        const driverMarker = ref(null);
        const currentLat = ref(null);
        const currentLng = ref(null);
        const stopMarker = ref([]);

        const user_mobile = ref("**********");
        const user_email = ref("**********");
        const driver_mobile =ref("**********");
        const driver_email =ref("**********");

        const dismissMessage = () => {
            successMessage.value = "";
            alertMessage.value = "";
        };

        const initializeMap = async () => {
            map.value = L.map('map',{
                zoomControl: true,
                dragging: false,
                scrollWheelZoom: false,
                doubleClickZoom: false,
            }).setView([result.value.pick_lat, result.value.pick_lng], 12);
            
            map.value.dragging.disable();
            map.value.scrollWheelZoom.disable();
            map.value.doubleClickZoom.disable();

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
            if(result.value.poly_line) {
                const decodedCoordinates = polyline.decode(result.value.poly_line);
                
                let routePolyline = L.polyline(decodedCoordinates, { color: 'blue' }).addTo(map.value);
                map.value.fitBounds(routePolyline);
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
        const animateDriverMovement = (marker, toLatLng) => {
    if (!marker || !toLatLng || isNaN(toLatLng.lat) || isNaN(toLatLng.lng)) {
        console.error("Invalid marker or destination coordinates:", marker, toLatLng);
        return;
    }

    const duration = 2000;
    const start = performance.now();
    const easeInOutQuad = (t) => (t < 0.5 ? 2 * t * t : -1 + (4 - 2 * t) * t);

    const fromLat = marker.getLatLng().lat;
    const fromLng = marker.getLatLng().lng;
    const toLat = toLatLng.lat;
    const toLng = toLatLng.lng;

    if (isNaN(fromLat) || isNaN(fromLng)) {
        console.error("Invalid marker starting position:", marker.getLatLng());
        return;
    }

    const animateStep = (time) => {
        const elapsed = time - start;
        const t = Math.min(elapsed / duration, 1);
        const easedT = easeInOutQuad(t);

        const currentLat = fromLat + (toLat - fromLat) * easedT;
        const currentLng = fromLng + (toLng - fromLng) * easedT;

        // Ensure valid LatLng values
        if (isNaN(currentLat) || isNaN(currentLng)) {
            console.error("Calculated invalid LatLng during animation:", { currentLat, currentLng });
            return;
        }

        // Update marker position
        marker.setLatLng([currentLat, currentLng]);

        // Continue animation if not finished
        if (t < 1) {
            requestAnimationFrame(animateStep);
        }
    };

    requestAnimationFrame(animateStep);
};


        const decodeGeohash = (geohash) => {
const BASE32 = '0123456789bcdefghjkmnpqrstuvwxyz';
const BITS = [16, 8, 4, 2, 1];
let isEven = true;
let latMin = -90,
latMax = 90;
let lonMin = -180,
lonMax = 180;
let lat, lon;

if (geohash) {
for (let i = 0; i < geohash.length; i++) {
let c = geohash.charAt(i);
let cd = BASE32.indexOf(c);
for (let j = 0; j < 5; j++) {
let mask = BITS[j];
if (isEven) {
let lonMid = (lonMin + lonMax) / 2;
if (cd & mask) {
lonMin = lonMid;
} else {
lonMax = lonMid;
}
} else {
let latMid = (latMin + latMax) / 2;
if (cd & mask) {
latMin = latMid;
} else {
latMax = latMid;
}
}
isEven = !isEven;
}
}
lat = (latMin + latMax) / 2;
lon = (lonMin + lonMax) / 2;
return { lat: lat, lon: lon };
}
};


onMounted( async ()=> {
    const currentLocale = localStorage.getItem('locale') || 'en';
    await initI18n(currentLocale);
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

            if(result.value.driverDetail.data){

                const driversRef = firebase.database().ref('drivers/driver_' + result.value.driverDetail.data.id);

                driversRef.on('value', (snapshot) => {
                        const driver = snapshot.val();
                        const driverLocation = decodeGeohash(driver.g);

                    if (driverLocation) {
                        const driverLatLng = [driverLocation.lat, driverLocation.lon];
                        currentLat.value = driverLocation.lat;
                        currentLng.value = driverLocation.lon;

                        let vehicleTypeIconUrl = driver.vehicle_type_icon 
                            ? `/image/map/${driver.vehicle_type_icon}.png` 
                            : "";

                        if (vehicleTypeIconUrl.length > 0) {
                            if (driverMarker.value) {
                                // Animate the movement

                                const toLatLng = { lat: driverLocation.lat, lng: driverLocation.lon };
                                animateDriverMovement(driverMarker.value, toLatLng);

                                // Update the icon if needed
                                driverMarker.value.setIcon(
                                    L.icon({
                                        iconUrl: vehicleTypeIconUrl,
                                        iconSize: [30, 30],
                                    })
                                );
                            } else {
                                // Create the marker only if it doesn't exist
                                driverMarker.value = L.marker(driverLatLng, {
                                    icon: L.icon({
                                        iconUrl: vehicleTypeIconUrl,
                                        iconSize: [30, 30],
                                    }),
                                }).addTo(map.value);

                                map.value.setView([currentLat.value, currentLng.value], 15);
                            }
                        } else if (driverMarker.value) {
                            map.value.removeLayer(driverMarker.value);
                            driverMarker.value = null;
                        }
                    }

                });
            }
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
            initializeMap();
            
            
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
            user_mobile,
            user_email,
            driver_mobile,
            driver_email,
            showStops,
            dispatch_type,
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
                                        <h5 class=" mt-5 mb-3 fs-18">{{ rideStatus }}</h5>
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
                                    <div class="text-center" v-if="!result.is_cancelled && !result.is_completed">
                                        <a
                                            v-if="result.driverDetail && result.driverDetail.data?.mobile"
                                            :href="`tel:${result.driverDetail.data.mobile}`"
                                            type="button"
                                            class="btn btn-success btn-label waves-effect waves-light fs-18"
                                        >
                                            <i class="ri-phone-fill label-icon align-middle fs-16 me-2"></i> {{ $t("call") }}
                                        </a>
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
                    <!-- <div class="card-header">
                    <h4 class="mb-4"> {{$t("bill_details")}}</h4></div> -->
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
                                    <td>{{ result.accepted_ride_fare }}</td>
                                </tr>
                            </tbody>
                            <tbody v-else >
                                <tr>
                                    <td> {{$t("base_price")}}</td>
                                    <td> {{$t("for_first")}} {{ result.requestBill.data.base_distance }} {{$t("km")}}</td>
                                    <td>{{ result.requestBill.data.base_price }}</td>
                                </tr>
                                <tr>
                                    <td> {{$t("distance_price")}}</td>
                                    <td> {{ result.requestBill.data.total_distance > result.requestBill.data.base_distance ? result.requestBill.data.total_distance - result.requestBill.data.base_distance:0 }}  {{$t("km")}} * {{result.requested_currency_symbol}} {{ result.requestBill.data.price_per_distance }} / {{$t("km")}}</td>
                                    <td>{{ result.requestBill.data.distance_price }}</td>
                                </tr>
                                <tr>
                                    <td> {{$t("time_price")}}</td>
                                    <td>{{ result.requestBill.data.total_time }}  {{$t("min")}} * {{result.requested_currency_symbol}} {{ result.requestBill.data.price_per_time }} / {{$t("min")}}</td>
                                    <td>{{ result.requestBill.data.time_price }}</td>
                                </tr>
                                <tr v-if="result.requestBill.data.cancellation_fee > 0">
                                    <td> {{$t("cancellation_fee")}}</td>
                                    <td>-</td>
                                    <td>{{ result.requestBill.data.cancellation_fee }}</td>
                                </tr>
                                <tr>
                                    <td> {{$t("service_tax")}}</td>
                                    <td>-</td>
                                    <td>{{ result.requestBill.data.service_tax }}</td>
                                </tr>
                                <tr class="text-danger" v-if="result.requestBill.data.promo_discount > 0">
                                    <td> {{$t("promo_discount")}}</td>
                                    <td>-</td>
                                    <td>{{ result.requestBill.data.promo_discount }}</td>
                                </tr>
                                <tr>
                                    <td> {{$t("admin_commission")}}</td>
                                    <td>-</td>
                                    <td>{{ result.requestBill.data.admin_commision }}</td>
                                </tr>
                                <tr v-if="result.requestBill.data.additional_charges_amount > 0">
                                            <td> {{result.requestBill.data.additional_charges_reason}}</td>
                                            <td>-</td>
                                            <td>{{ result.requestBill.data.additional_charges_amount }}</td>
                                        </tr>
                                        <tr v-if="result.requestBill.data.tips > 0">
                                            <td> {{$t("driver_tips")}}</td>
                                            <td>-</td>
                                            <td>{{result.requested_currency_symbol}}{{ result.requestBill.data.tips }}</td>
                                        </tr>
                                        <tr v-if="result.requestBill.data.waiting_charge > 0">
                                            <td> {{$t("waiting_charge")}}</td>
                                            <td>{{ $t('For') }} {{result.requestBill.data.waiting_charge}} min</td>
                                            <td>{{result.requested_currency_symbol}}{{ result.requestBill.data.waiting_charge }}</td>
                                        </tr>
                                        <tr  v-if="result.requestBill.data.airport_surge_fee > 0 && result.transport_type === 'taxi'">
                                            <td> {{$t("airport_surge_fee")}}</td>
                                            <td>-</td>
                                            <td>{{result.requested_currency_symbol}}{{ result.requestBill.data.airport_surge_fee }}</td>
                                        </tr>
                                        <tr v-if="result.payment_opt">
                                            <td> {{$t("payment_option")}}</td>
                                            <td>-</td>
                                             <td v-if="result.payment_opt == 0">
                                                <BBadge variant="success" class="text-uppercase">{{$t("card")}}</BBadge>
                                             </td>
                                             <td v-if="result.payment_opt == 1">
                                                <BBadge variant="success" class="text-uppercase">{{$t("cash")}}</BBadge>
                                             </td>
                                             <td v-if = "result.payment_opt == 2">
                                                <BBadge variant="success" class="text-uppercase">{{$t("wallet")}}</BBadge>
                                             </td>
                                             <td v-if = "result.payment_opt == 3">
                                                <BBadge variant="success" class="text-uppercase">{{$t("wallet/cash")}}</BBadge>
                                             </td>
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
                </div>
                <div class="col-lg-6">
                <div class="card border mt-3">
                    <!-- <div class="card-header">
                    <h4 class="mb-4"> {{$t("bill_details")}}</h4></div> -->
                    <div class="table-responsive">
                        <table class="table align-middle position-relative table-nowrap">
                            <thead class="table-active">
                                <tr class="fs-16">
                                    <th scope="col"> {{$t("title")}}</th>
                                    <th scope="col"></th>
                                    <th scope="col" > {{$t("price")}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="text-danger">
                                    <td colspan="2"> {{$t("admin_commission")}}</td>
                                    <td>{{ result.requestBill.data.admin_commision }}</td>
                                </tr>
                                <tr class="text-danger">
                                    <td colspan="2"> {{$t("service_tax")}}</td>
                                    <td>{{ result.requestBill.data.service_tax }}</td>
                                </tr>
                                <tr class="text-danger">
                                    <td colspan="2"> {{$t("admin_commission_from_driver")}}</td>
                                    <td>{{ result.requestBill.data.admin_commision_from_driver }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2"> {{$t("driver_commission")}}
                                        ({{$t('included')}} - 
                                            <div v-if="result.requestBill.data.driver_tips > 0">
                                                {{$t('driver_tips')}} -{{result.requested_currency_symbol}}{{ result.requestBill.data.driver_tips }}
                                            </div>
                                            <div  v-if="result.requestBill.data.airport_surge_fee > 0 && result.transport_type === 'taxi'">
                                                {{$t('airport_surge_fee')}} - {{result.requested_currency_symbol}}{{ result.requestBill.data.airport_surge_fee }}
                                            </div>)
                                    </td>
                                    <td>{{result.requested_currency_symbol}}{{ result.requestBill.data.driver_commision }}</td>
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
.leaflet-control-container .leaflet-routing-container-hide {
  display: none;
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
