
<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import { ref, onMounted,computed } from "vue";
import axios from "axios";
import { useSharedState } from '@/composables/useSharedState'; // Import the composable
import { useI18n } from 'vue-i18n';

export default {
    components: {
        Layout,
        PageHeader,
        Head,
    },
    props: {
        zone: Object, // Passed from controller
        googleMapKey: String, // Define the googleMapKey prop
        successMessage: String,
        languages: Array,
        alertMessage: String,
        app_for: String,
    },
    setup(props) {
        const { zone, googleMapKey } = props;
        const { t } = useI18n();
        const { languages, fetchData } = useSharedState(); // Destructure the shared state
        const activeTab = ref('English');

        const successMessage = ref(props.successMessage || '');
        const alertMessage = ref(props.alertMessage || '');

        const form = useForm({
            service_location_id: zone.service_location_id,
            languageFields:  zone ? zone.languageFields || {} : {}, // To hold data from the Tab component
            // name: zone.name,
            unit: zone.unit,
            coordinates: zone.coordinates || [], // Initialize coordinates from zone data
            maximum_outstation_distance: zone.maximum_outstation_distance ?? "",
            maximum_distance: zone.maximum_distance ?? "",
        });
        const serviceLocations = ref([]);
        let map, currentPolygon;
        let polygons = [];
        const drawingManager = ref(null);
        const selectedPolygon = ref(null);

        const fetchServiceLocations = async () => {
            const response = await axios.get('/zones/list');
            serviceLocations.value = response.data.results;
        };


        const search = ref('');
        const suggestions = ref([]);
        const handleInput = () =>{
            
            if (search.value.length < 3) {
                suggestions.value = [];
                }else{
                setTimeout(() => {
                    fetchAutocompleteResults(search.value);
                }, 300);
            }
        }
        const fetchAutocompleteResults = (search) => {

            const apiUrl = `https://places.googleapis.com/v1/places:autocomplete`;
                const headers = {
                    "Content-Type": "application/json",
                    "X-Goog-Api-Key": props.googleMapKey,
                    "X-Goog-FieldMask": "suggestions.placePrediction.placeId,suggestions.placePrediction.place,suggestions.placePrediction.text",
                };
                const requestData = {
                    input: search,
                };

                fetch(apiUrl, {
                    method: "POST",
                    headers: headers,
                    body: JSON.stringify(requestData),
                })
                    .then((response) => response.json())
                    .then((data) => {
                        if(data.suggestions?.length>0) {
                            suggestions.value = data.suggestions.filter(suggestion => suggestion.placePrediction).map(suggestion => ({
                                placeId: suggestion.placePrediction.placeId,
                                formattedAddress: suggestion.placePrediction.text.text,
                            }));
                        }
                    })
                    .catch((error) => {
                    console.error("Error fetching autocomplete results:", error);
                    })
        }
        const selectSuggestion = async(suggestion) => {

            const headers = {
                    "X-Goog-Api-Key": props.googleMapKey,
                    "X-Goog-FieldMask": "viewport,location",
                };
            fetch(`https://places.googleapis.com/v1/places/${suggestion.placeId}?fields=viewport,location`, {
                headers: headers,
            })
            .then((response) => response.json())
            .then((data) => {
                search.value = '';
                suggestions.value = [];

                const position = new google.maps.LatLng(data.location.latitude, data.location.longitude );
                
                map.setCenter(position);

                if (data.viewport && data.viewport.high && data.viewport.low) {
                    const bounds = new google.maps.LatLngBounds(
                        new google.maps.LatLng(data.viewport.low.latitude, data.viewport.low.longitude),
                        new google.maps.LatLng(data.viewport.high.latitude, data.viewport.high.longitude),
                    );
                    
                    map.fitBounds(bounds);
                }else{
                    map.setZoom(15);
                }
            })
            .catch((error) => {
            console.error("Error fetching autocomplete results:", error);
            })
        }
        const initializeMap = () => {
            if (zone && zone.coordinates) {
                map = new google.maps.Map(document.getElementById('map'), {
                    center: { lat: 0, lng: 0 },
                    zoom: 10,
                });

                // Adjust map center and zoom to fit the polygon
                const bounds = new google.maps.LatLngBounds();
                zone.coordinates.forEach((polygon) => {

                const polygonCoordinates = polygon[0].map(point => ({
                    lat: point.coordinates[1], // Latitude
                    lng: point.coordinates[0], // Longitude
                }))


                currentPolygon = new google.maps.Polygon({
                    paths: polygonCoordinates,
                    fillColor: "#0000FF",
                    fillOpacity: 0.3,
                    strokeWeight: 1,
                    clickable: true,
                    editable: false,
                    zIndex: 1,
                    map: map,
                });
                polygons.push(currentPolygon);
                attachClickListener(currentPolygon);

                currentPolygon.getPath().forEach(coord => bounds.extend(coord));
                })


                map.fitBounds(bounds);

                initializeDrawingManager();
            }

        };
        
        const attachClickListener = (polygon) => {
            google.maps.event.addListener(polygon, 'click', () => {
                if (selectedPolygon.value === polygon) return;

                // Reset styles for all polygons
                polygons.forEach((poly) => {
                    poly.setOptions({ fillColor: "#0000FF", editable: false });
                });

                // Highlight and select the clicked polygon
                polygon.setOptions({ fillColor: "#00FF00", editable: true });
                selectedPolygon.value = polygon;
            });
        };

        const changeDrawingMode = (option) => {
            if(drawingManager.value){
                let mode = option == 'draw' ? google.maps.drawing.OverlayType.POLYGON : null;
                drawingManager.value.setDrawingMode(mode);
            }
        }

        const removeSelectedPolygon = () => {
            if(selectedPolygon.value){
                const index = getPolygonIndex(selectedPolygon.value);
                if (index > -1) {
                    polygons[index].setMap(null);
                    polygons.splice(index, 1);
                }
                selectedPolygon.value = null;
                changeDrawingMode('grab');

            }
        }
        const removeAllPolygons = () => {
            polygons.forEach(polygon => {
                polygon.setMap(null);
                selectedPolygon.value = null;
            });
            polygons = [];
        }
        const arePathsEqual = (poly1, poly2) => {
            const path1 = poly1.getPath().getArray().map(coord => ({
                lat: coord.lat(),
                lng: coord.lng(),
            }));
            const path2 = poly2.getPath().getArray().map(coord => ({
                lat: coord.lat(),
                lng: coord.lng(),
            }));
            return JSON.stringify(path1) === JSON.stringify(path2);
        };
        const getPolygonIndex = (targetPolygon) => {
            for (let i = 0; i < polygons.length; i++) {
                if (arePathsEqual(targetPolygon, polygons[i])) {
                    return i;
                }
            }
            return -1; // Not found
        };
        const initializeDrawingManager = () => {
            drawingManager.value = new google.maps.drawing.DrawingManager({
                drawingMode: null,
                drawingControl: false,
                polygonOptions: {
                    fillColor: "#db1d53",
                    fillOpacity: 0.5,
                    strokeWeight: 1,
                    clickable: true,
                    editable: false,
                    zIndex: 1,
                },
            });

            drawingManager.value.setMap(map);

            google.maps.event.addListener(drawingManager.value, 'overlaycomplete', function(event) {
                if (event.type === google.maps.drawing.OverlayType.POLYGON) {
                    
                    polygons.push(event.overlay);

                    attachClickListener(event.overlay);
                }
            });
        };

        const handleSubmit = async () => {
            if(props.app_for == "demo"){
                Swal.fire(t('error'), t('you_are_not_authorised'), 'error');
                return;
            }
            const errors = validateForm();
            if (Object.keys(errors).length === 0) {
                try {
                    if (polygons.length === 0) {
                        alertMessage.value = t('at_least_one_completed_polygon_is_required');
                        return;
                    }
                    // if (currentPolygon) {
                    //     const newCoordinates = currentPolygon.getPath().getArray().map(coord => ({
                    //         lat: coord.lat(), // Correctly access lat and lng methods
                    //         lng: coord.lng(),
                    //     }));
                    //     form.coordinates = [[newCoordinates]]; // Update form coordinates
                    // }

                    const coordinates = polygons.map(polygon =>
                        polygon.getPath().getArray().map(latLng => [
                            latLng.lng(),
                            latLng.lat()
                        ])
                    );
                    const formData = {
                        ...form.data(),
                        coordinates: JSON.stringify(coordinates)
                    };

                    const response = await axios.post(`/zones/update/${zone.id}`, formData);

                    if (response.status === 200) {
                        successMessage.value = t('zone_created_successfully');
                        form.reset();
                        router.get('/zones');
                    } else {
                        console.error(t('failed_to_update_zone'));
                        // Handle failure
                    }
                } catch (error) {
                    if (error.response && error.response.status === 403) {
                        alertMessage.value = error.response.data.alertMessage;
                        setTimeout(()=>{
                            router.get('/zones');
                        },5000)
                    }else if (error.response && error.response.status === 422) {
                        alertMessage.value = error.response.data.message;
                        form.errors = {
                            coordinates : error.response.data.message,
                        }
                    }else{
                        form.errors = {};
                        alertMessage.value =t('failed_to_create_zone_catch');
                        console.error(t('error_updating_zone'), error);
                    }
                    // Handle error
                }
            } else {
                form.errors = errors;
            }
        };


        const validateForm = () => {
            const { service_location_id, unit,maximum_outstation_distance,maximum_distance } = form;
            const errors = {};
            if (!maximum_outstation_distance) {
                errors.maximum_outstation_distance = t('required');
            } else {
                delete errors.unit;
            }
            if (!maximum_distance) {
                errors.maximum_distance = t('required');
            } else {
                delete errors.unit;
            }
            if (!unit) {
                errors.unit = 'Unit is required';
            } else {
                delete errors.unit;
            }
            if (!service_location_id) {
                errors.service_location_id = t('service_location_is_required');
            }else{
                delete errors.service_location_id;
            }

            return errors;
        };


        const setActiveTab = (tab) => {
        activeTab.value = tab;
        }
        onMounted(async () => {
            let mapInitialized = false;
            if (!googleMapKey) {
                console.error(t('google_map_api_key_is_null_or_undefined'));
                return;
            }
            if (Object.keys(languages).length == 0) {
                await fetchData();
            }

            if (!document.querySelector(`script[src="https://maps.googleapis.com/maps/api/js?key=${googleMapKey}&libraries=places,drawing"]`)) {
                const script = document.createElement('script');
                script.src = `https://maps.googleapis.com/maps/api/js?key=${googleMapKey}&libraries=places,drawing`;
                script.onload = () => {
                    if (!mapInitialized) {
                        initializeMap();
                        fetchServiceLocations();
                        mapInitialized = true;
                    }
                };
                document.head.appendChild(script);
            } else if (!mapInitialized) {
                initializeMap();
                fetchServiceLocations();
                mapInitialized = true;
            }
            
        });

        return {
            form,
            serviceLocations,
            handleSubmit,
            serviceLocations,
            setActiveTab,
            serviceLocations,
            search,
            suggestions,
            handleInput,
            selectSuggestion,
            alertMessage,
            languages,
            removeAllPolygons,
            removeSelectedPolygon,
            changeDrawingMode,
            activeTab,
        };
    },
    computed: {
    selectedUnitLabel() {
        if (this.form.unit == 1) return this.$t("kilo_meter")
        if (this.form.unit == 2) return this.$t("miles")
        return ""
    }
},
};
</script>

<template>
    <Layout>
        <Head title="Edit Zone" />
        <PageHeader :title="$t('edit')" :pageTitle="$t('zone')" pageLink="/zones"/>
        <div v-if="zone" class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <form @submit.prevent="handleSubmit">
                            <div class="mb-3">
                                <label for="service_location" class="form-label">{{$t("service_location")}}</label>
                                <select class="form-select" id="service_location" v-model="form.service_location_id">
                                    <option value="" disabled>{{$t('select_service_location')}}</option>
                                    <option v-for="location in serviceLocations" :key="location.id" :value="location.id">{{ location.name }}</option>
                                </select>
                                <span v-if="form.errors.service_location_id" class="text-danger">{{ form.errors.service_location_id }}</span>
                            </div>
                            <ul class="nav nav-tabs nav-tabs-custom nav-success nav-justified" role="tablist">
                                <BRow v-for="language in languages" :key="language.code">
                                <BCol lg="12">
                                    <li class="nav-item" role="presentation">
                                    <a class="nav-link" @click="setActiveTab(language.label)"
                                        :class="{ active: activeTab === language.label }" role="tab" aria-selected="true">
                                        {{ language.label }}
                                    </a>
                                    </li>
                                </BCol>
                                </BRow>
                            </ul>
                            <div class="tab-content text-muted" v-for="language in languages" :key="language.code">
                                <div v-if="activeTab === language.label" class="tab-pane active show" :id="`${language.label}`"
                                role="tabpanel">
                                <div class="col-sm-6 mt-3">
                                    <div class="mb-3">
                                    <label :for="`name-${language.code}`" class="form-label">{{$t("name")}}</label>
                                    <input type="text" class="form-control" :placeholder="`Enter Name in ${language.label}`"
                                        :id="`name-${language.code}`" v-model="form.languageFields[language.code]"
                                        :required="language.code === 'en'">
                                    </div>
                                </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="select_unit" class="form-label">{{$t("select_unit")}}</label>
                                <select id="select_unit" class="form-select" v-model="form.unit">
                                    <option disabled value="">{{$t("choose_unit")}}</option>
                                    <option value=1>{{$t("kilo_meter")}}</option>
                                    <option value=2>{{$t("miles")}}</option>
                                </select>
                                <span v-if="form.errors.unit" class="text-danger">{{ form.errors.unit }}</span>
                            </div>                                                      
                            <div class="mb-3">
                                <label for="maximum_distance" class="form-label">{{$t("maximum_distance")}}
                                    <span class="text-danger">({{ selectedUnitLabel }}*)</span>
                                </label>
                                <input type="number" step="any" class="form-control" :placeholder="$t('enter_maximum_distance')" id="maximum_distance" v-model.number="form.maximum_distance">
                                <span v-if="form.errors.enter_maximum_distance" class="text-danger">{{ form.errors.enter_maximum_distance }}</span>
                            </div>
                            <div class="mb-3">
                                <label for="maximum_outstation_distance" class="form-label">{{$t("maximum_outstation_distance")}}
                                    <span class="text-danger">({{ selectedUnitLabel }}*)</span>
                                </label>
                                <input type="number" step="any" class="form-control" :placeholder="$t('enter_maximum_outstation_distance')" id="maximum_outstation_distance" v-model.number="form.maximum_outstation_distance">
                                <span v-if="form.errors.maximum_outstation_distance" class="text-danger">{{ form.errors.maximum_outstation_distance }}</span>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary" :disabled="app_for === 'demo'">{{$t("update")}}</button>
                            </div>
                            <div class="mb-3">
                                <span v-if="form.errors.coordinates" class="text-danger">{{ form.errors.coordinates }}</span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="autocomplete-container">
                        <div class="input-group">
                            <input
                            type="text"
                            class="form-control"
                            v-model="search"
                            :placeholder="$t('search_for_a_city')"
                            id="pac-input"
                            autocomplete="off"
                            @input="handleInput"
                            />
                        </div>
                        <div v-if="suggestions.length > 0" class="autocomplete-results">
                            <div
                            v-for="suggestion in suggestions"
                            :key="suggestion.placeId"
                            class="autocomplete-item"
                            @click="selectSuggestion(suggestion)"
                            >
                            {{ suggestion.formattedAddress }}
                            </div>
                        </div>
                        </div>
                        <div id="map" style="height: 400px;"></div>
                        <div class="col-lg-1 col-md-2 col-sm-2" style="position:absolute;z-index: 50;top:20%;">
                            <div class=" d-flex align-items-center">
                                <div class="card-body">
                                    <BButton @click="changeDrawingMode('grab')" class="align-center btn-dark border border-light" data-bs-toggle="tooltip" data-bs-placement="right" title="Select"> <i class="bx bxs-hand-up fs-16"></i> </BButton>
                                </div>
                            </div>
                            <div class=" d-flex align-items-center">
                                <div class="card-body">
                                    <BButton @click="changeDrawingMode('draw')" class="align-center btn-dark border border-light" data-bs-toggle="tooltip" data-bs-placement="right" title="Add"> <i class="bx bx-plus fs-16"></i> </BButton>
                                </div>
                            </div>
                            <div class=" d-flex align-items-center">
                                <div class="card-body">
                                    <BButton @click="removeSelectedPolygon" class="align-center btn-dark border border-light" data-bs-toggle="tooltip" data-bs-placement="right" title="Remove selected polygon"> <i class="bx bx-x fs-16"></i> </BButton>
                                </div>
                            </div>
                            <div class=" d-flex align-items-center">
                                <div class="card-body">
                                    <BButton @click="removeAllPolygons" class="align-center btn-dark border border-light" data-bs-toggle="tooltip" data-bs-placement="right" title="Remove all"> <i class="bx bx-trash fs-16"></i> </BButton>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="alert alert-warning f-18" role="alert">
                            <strong> Avoid make new polygons that overlap with existing zones.</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else>{{$t("lodaing")}}</div>

        <div v-if="alertMessage" class="custom-alert alert alert-danger alert-border-left fade show" role="alert"
            id="alertMsg">
            <div class="alert-content">
            <i class="ri-notification-off-line me-3 align-middle"></i>
            <strong>Alert</strong> - {{ alertMessage }}
            <button type="button" class="btn-close btn-close-danger" @click="dismissMessage"
                aria-label="Close Alert Message"></button>
            </div>
        </div>
    </Layout>
</template>


<style scoped>
.text-danger {
    padding-top: 5px;
}
.autocomplete-container {
  position: relative;
}

.autocomplete-results {
  border: 1px solid #ccc;
  max-height: 200px;
  overflow-y: auto;
  position: absolute;
  width: 100%;
  background-color: white;
  z-index: 1000;
}

.autocomplete-item {
  padding: 5px;
  cursor: pointer;
}

.autocomplete-item:hover {
  background-color: #f0f0f0;
}
</style>
