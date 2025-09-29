<script>
    import { Link, Head, useForm } from '@inertiajs/vue3';
    import Layout from "@/Layouts/main.vue";
    import PageHeader from "@/Components/page-header.vue";
    import { ref, watch, onMounted } from "vue";
    import { debounce } from 'lodash';
    import Multiselect from "@vueform/multiselect";
    import "@vueform/multiselect/themes/default.css";
    import "flatpickr/dist/flatpickr.css";
    import L from "leaflet";
    import "leaflet/dist/leaflet.css";
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
        Multiselect,
    },
    props: {
        firebaseSettings: Object,
        service_location: Array, 
        vehicle_type: Array, 
        default_lat:String,
        default_lng:String,
    },
    setup(props) {
        const { t } = useI18n();

        // Reactive data for filters
        const selectedServiceLocations = ref(null);
        const selectedVehicleTypes = ref(null);
        const searchTerm = ref("");
        const filter = useForm({
            all: "",
            locked: "",
        });
        const results = ref([]); 
        const paginator = ref({}); 
        const modalShow = ref(false);
        const currentLat = ref(parseFloat(props.default_lat));
        const currentLng = ref(parseFloat(props.default_lng));
        const modalFilter = ref(false);
        const selectedModes = ref([]);

        const serviceLocations = ref(props.service_location.map(loc => ({ value: loc.id, label: loc.name })));
        const vehicleTypes = ref(props.vehicle_type.map(type => ({ value: type.id, label: type.name })));

        const filterData = () => {
            modalFilter.value = true;
        };

        const clearFilter = () => {
            filter.reset();
            fetchDatas();
            modalFilter.value = false;
        };

        watch([selectedServiceLocations, selectedVehicleTypes, selectedModes], () => {
            fetchNearbyDrivers();
        });

        // Map initialization
        let map;
        let driverMarkers = {};

        const initializeMap = () => {
            map = L.map('map').setView({lat: currentLat.value, lng: currentLng.value}, 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            var firebaseConfig = {
                apiKey: props.firebaseSettings['firebase_api_key'],
                authDomain: props.firebaseSettings['firebase_auth_domain'],
                databaseURL: props.firebaseSettings['firebase_database_url'],
                projectId: props.firebaseSettings['firebase_project_id'],
                storageBucket: props.firebaseSettings['firebase_storage_bucket'],
                messagingSenderId: props.firebaseSettings['firebase_messaging_sender_id'],
                appId: props.firebaseSettings['firebase_app_id'],
            };
            if(!firebase.apps.length){
                firebase.initializeApp(firebaseConfig);
            }
            fetchNearbyDrivers();
        };

        const clearFilters = () => {
            selectedServiceLocations.value = null;
            selectedVehicleTypes.value = null;
            selectedModes.value = [];
            fetchNearbyDrivers();
        }
        const applyFilters = () => {
            console.log(selectedModes.value);
            fetchNearbyDrivers();
        }
        const fetchNearbyDrivers = () => {
            const driversRef = firebase.database().ref('drivers');

            driversRef.on('value', (snapshot) => {
                snapshot.forEach((childSnapshot) => {
                    const driver = childSnapshot.val();
                    const driverLocation = decodeGeohash(driver.g);

                    if (driverLocation) {
                        const driverLatLng = [driverLocation.lat, driverLocation.lon];

                        let active = selectedModes.value.length>0 ? selectedModes.value.includes("online") : true;
                        let onride = selectedModes.value.length>0 ? selectedModes.value.includes("onride") : true;
                        let offline = selectedModes.value.length>0 ? selectedModes.value.includes("offline") : true;

                        let vehicleTypeIconUrl="";
                        if(driver.hasOwnProperty('is_active') && driver.hasOwnProperty('is_available')){
                            if (driver.is_active ==1 && driver.is_available==true && active) {
                                vehicleTypeIconUrl = `/image/map/${driver.vehicle_type_icon}.png`;
                            } else if (driver.is_active == 1 && driver.is_available==false && onride) {
                                vehicleTypeIconUrl = `/image/map/${driver.vehicle_type_icon}.png`;
                            } else {
                                if(offline){
                                    vehicleTypeIconUrl = `/image/map/${driver.vehicle_type_icon}.png`;
                                }
                            }
                        }
                        let matchesServiceLocation = driver.service_location_id ? true : false;
                        if(selectedServiceLocations.value){
                            matchesServiceLocation = selectedServiceLocations.value == driver.service_location_id ?? false;
                        }

                        let matchesVehicleType = Array.isArray(driver.vehicle_types);
                        if(selectedVehicleTypes.value && Array.isArray(driver.vehicle_types) && driver.vehicle_types.length>0){
                            matchesVehicleType = (Array.isArray(driver.vehicle_types) && driver.vehicle_types.some(type => selectedVehicleTypes.value === type)) ?? false;
                        }
                        if (vehicleTypeIconUrl.length > 0 && matchesVehicleType && matchesServiceLocation) {

                            if (driverMarkers[driver.id]) {
                                animateDriverMovement(driverMarkers[driver.id], driverMarkers[driver.id].getLatLng(), driverLatLng);
                                driverMarkers[driver.id].setIcon(L.icon({
                                    iconUrl: vehicleTypeIconUrl,
                                    iconSize: [30, 30],
                                }));
                            } else {
                                const driverMarker = L.marker(driverLatLng, {
                                    icon: L.icon({
                                        iconUrl: vehicleTypeIconUrl,
                                        iconSize: [30, 30],
                                    })
                                }).addTo(map);

                                driverMarker.bindTooltip(
                                    `<strong>${driver.name}</strong><br>Mobile: ${driver.mobile}`,
                                    {
                                        permanent: false,
                                        direction: 'top',
                                        offset: [0, -10],
                                    }
                                );

                                driverMarkers[driver.id] = driverMarker;
                                animateDriverMovement(driverMarker, driverMarker.getLatLng(), driverLatLng);
                            }
                        } else {
                            removeDriverMarker(driver.id);
                        }
                    }
                });
            });

            driversRef.on('child_changed', (childSnapshot) => {
                const driver = childSnapshot.val();
                updateDriverMarker(driver);
            });

            driversRef.on('child_removed', (childSnapshot) => {
                const driver = childSnapshot.val();
                removeDriverMarker(driver.id);
            });
        };

        const animateDriverMovement = (marker, prevLatLng, currentLatLng) => {
            marker.setLatLng(currentLatLng).update();
        };

        const updateDriverMarker = (driver) => {
            const driverLocation = decodeGeohash(driver.g);

            if (driverLocation) {
                const driverLatLng = [driverLocation.lat, driverLocation.lon];
                const vehicleTypeIconUrl = `/image/map/${driver.vehicle_type_icon}.png`;

                if (driverMarkers[driver.id]) {
                    animateDriverMovement(driverMarkers[driver.id], driverMarkers[driver.id].getLatLng(), driverLatLng);
                    driverMarkers[driver.id].setIcon(L.icon({
                        iconUrl: vehicleTypeIconUrl,
                        iconSize: [30, 30],
                    }));
                } else {
                    const driverMarker = L.marker(driverLatLng, {
                        icon: L.icon({
                            iconUrl: vehicleTypeIconUrl,
                            iconSize: [30, 30],
                        })
                    }).addTo(map);
                    driverMarkers[driver.id] = driverMarker;
                }
            } else {
                console.error(`${t('failed_to_decode_geohash')} ${driver.id}`);
            }
        };

        const removeDriverMarker = (driverId) => {
            if (driverMarkers[driverId]) {
                map.removeLayer(driverMarkers[driverId]);
                delete driverMarkers[driverId];
            }
        };

        const decodeGeohash = (geohash) => {
            const BASE32 = '0123456789bcdefghjkmnpqrstuvwxyz';
            const BITS = [16, 8, 4, 2, 1];
            let isEven = true;
            let latMin = -90, latMax = 90;
            let lonMin = -180, lonMax = 180;
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
                return { lat, lon };
            }
            return null;
        };

        onMounted(() => {
            initializeMap();
        });

        return {
            fetchData: debounce(fetchNearbyDrivers, 300),
            results,
            paginator,
            filter,
            searchTerm,
            serviceLocations,
            vehicleTypes,
            selectedServiceLocations,
            selectedVehicleTypes,
            filterData,
            clearFilter,
            clearFilters,
            applyFilters,
            modalShow,
            modalFilter,
            selectedModes
        };
    },
};
</script>
<template>
    <Layout>

        <Head :title="$t('gods-eye')" />
        <PageHeader :title="$t('gods-eye')" :pageTitle="$t('map')" />
        <BRow>
            <BCol lg="12">
                <BCard no-body id="tasksList">

                    <BCardHeader class="border-0">
                            <!-- <h4>Filters</h4> -->
                                <div class="row">
                                    <div class="col-12 col-lg-3">
                                    <div class="mb-3">
                                    <label for="select_payment_type" class="form-label">{{$t("service_location")}}</label>
                                    <Multiselect
                                            v-model="selectedServiceLocations"
                                            :options="serviceLocations"
                                            label="label"
                                            track-by="value"
                                            multiple
                                            close-on-select
                                            :placeholder="$t('select_service_locations')"
                                        />
                                    </div>
                                </div>

                                    <div class="col-12 col-lg-3">
                                    <div class="mb-3">
                                    <label for="select_mode" class="form-label">{{$t("drivers")}}</label>
                                    <Multiselect 
                                        id="select_mode" 
                                        mode="tags" 
                                        :close-on-select="false"
                                        :searchable="true" 
                                        :create-option="false"
                                        v-model="selectedModes"
                                        :options="[
                                            { value: 'offline', label: $t('offline') },
                                            { value: 'online', label: $t('online') },
                                            { value: 'onride', label: $t('onride') },
                                        ]"
                                        :placeholder="$t('select_mode')"
                                    />
                                    </div>
                                </div>

                                <div class="col-12 col-lg-3">
                                    <div class="mb-3">
                                    <label for="vehicle_type" class="form-label">{{$t("vehicle_types")}}</label>
                                    <Multiselect
                                        v-model="selectedVehicleTypes"
                                        :options="vehicleTypes"
                                        label="label"
                                        track-by="value"
                                        multiple
                                        close-on-select
                                        :placeholder="$t('select_vehicle_types')"
                                        id="select_service_location"
                                    />
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6 d-flex gap-1">
                                <BButton type="button" variant="success" class="btn btn-md" @click="applyFilters">{{$t("apply")}}</BButton>
                                <BButton type="button" variant="danger" class="btn btn-md" @click="clearFilters">{{$t("clear")}}</BButton>
                                </div>
                            </div>  
                          
                    </BCardHeader>
                    <BCardBody class="border border-dashed border-end-0 border-start-0">
                        <div id="map" style="height: 400px;">{{$t("map_loading")}}</div>
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>
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
