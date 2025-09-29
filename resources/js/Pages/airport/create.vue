<template>
    <Layout>
        <Head title="Airport" />
        <PageHeader :title="$t('create')" :pageTitle="$t('airport')"  pageLink="/airport" />
        <div class="row">
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
                            <div class="mb-3">
                                <label :for="`name`" class="form-label">{{$t("name")}}</label>
                                <input type="text" class="form-control" :placeholder="`Enter Name`"
                                    :id="`name`" v-model="form.name"
                                    required >
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary" >{{$t("save")}}</button>
                            </div>
                            <div class="mb-3">
                                <span v-if="form.errors.coordinates" class="text-danger">{{ form.errors.coordinates }}</span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-10 col-sm-10">
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
                        <div id="map" style="height: 400px;position: relative;z-index: 10;"></div>
                        <div class="col-lg-1 col-md-2 col-sm-2" style="position:absolute;z-index: 50;top:20%;">
                            <div class=" d-flex align-items-center">
                                <div class="card-body">
                                    <BButton @click="changeDrawingMode('grab')" class="align-center"> <i class="bx bxs-hand-up fs-16"></i> </BButton>
                                </div>
                            </div>
                            <div class=" d-flex align-items-center">
                                <div class="card-body">
                                    <BButton @click="changeDrawingMode('draw')" class="align-center"> <i class="bx bx-plus fs-16"></i> </BButton>
                                </div>
                            </div>
                            <div class=" d-flex align-items-center">
                                <div class="card-body">
                                    <BButton @click="removeSelectedPolygon" class="align-center"> <i class="bx bx-x fs-16"></i> </BButton>
                                </div>
                            </div>
                            <div class=" d-flex align-items-center">
                                <div class="card-body">
                                    <BButton @click="removeAllPolygons" class="align-center"> <i class="bx bx-trash fs-16"></i> </BButton>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        <div v-if="successMessage" class="custom-alert alert alert-success alert-border-left fade show" role="alert" id="alertMsg">
        <div class="alert-content">
          <i class="ri-notification-off-line me-3 align-middle"></i>
          <strong>Success</strong> - {{ successMessage }}
          <button type="button" class="btn-close btn-close-success" @click="dismissMessage" aria-label="Close Success Message"></button>
        </div>
      </div>
      <div v-if="alertMessage" class="custom-alert alert alert-danger alert-border-left fade show" role="alert" id="alertMsg">
        <div class="alert-content">
          <i class="ri-notification-off-line me-3 align-middle"></i>
          <strong>Alert</strong> - {{ alertMessage }}
          <button type="button" class="btn-close btn-close-danger" @click="dismissMessage" aria-label="Close Alert Message"></button>
        </div>
      </div>
    </Layout>
</template>


<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import { ref, onMounted } from "vue";
import { useSharedState } from '@/composables/useSharedState'; // Import the composable
import axios from "axios";
import { useI18n } from 'vue-i18n';
import { polygon } from 'leaflet';

export default {
    components: {
        Layout,
        PageHeader,
        Head,
    },
    props: {
        googleMapKey: String, // Define the googleMapKey prop
        successMessage: String,
        serviceLocations:Object,
        default_lat:String,
        default_lng:String,
        alertMessage: String,
        languages: Array,
    },
    setup(props) {
        const { googleMapKey } = props;
        const { t } = useI18n();
        const { languages, fetchData } = useSharedState(); // Destructure the shared state
        const activeTab = ref('English');

        const form = useForm({
            service_location_id: "",
            name:"",


        });
        const successMessage = ref(props.successMessage || '');
        const alertMessage = ref(props.alertMessage || '');
        const serviceLocations = ref([]);
        const search = ref('');
        const suggestions = ref([]);
        const map = ref(null);
        let polygons = [];
        const drawingManager = ref(null);
        const selectedPolygon = ref(null);

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
                
                map.value.setCenter(position);

                if (data.viewport && data.viewport.high && data.viewport.low) {
                    const bounds = new google.maps.LatLngBounds(
                        new google.maps.LatLng(data.viewport.low.latitude, data.viewport.low.longitude),
                        new google.maps.LatLng(data.viewport.high.latitude, data.viewport.high.longitude),
                    );
                    
                    map.value.fitBounds(bounds);
                }else{
                    map.value.setZoom(15);
                }
            })
            .catch((error) => {
            console.error("Error fetching autocomplete results:", error);
            })
        }

        const fetchServiceLocations = async () => {
            const response = await axios.get('list');
            serviceLocations.value = response.data.results;
        };

        const initializeMap = () => {
            map.value = new google.maps.Map(document.getElementById('map'), {
                center: { lat: parseFloat(props.default_lat), lng: parseFloat(props.default_lng) },
                zoom: 10,
            });

            drawingManager.value = new google.maps.drawing.DrawingManager({
                drawingMode: google.maps.drawing.OverlayType.POLYGON,
                drawingControl: false,
                drawingControlOptions: {
                    position: google.maps.ControlPosition.TOP_CENTER,
                    drawingModes: [google.maps.drawing.OverlayType.POLYGON],
                },
                polygonOptions: {
                    fillColor: "#0000FF",
                    fillOpacity: 0.3,
                    strokeWeight: 1,
                    clickable: true,
                    editable: false,
                    zIndex: 1,
                },
            });
            drawingManager.value.setMap(map.value);

            google.maps.event.addListener(drawingManager.value, 'overlaycomplete', (event) => {
                if (event.type === google.maps.drawing.OverlayType.POLYGON) {

                    google.maps.event.addListener(event.overlay, 'click', () => {
                        if (selectedPolygon.value === event.overlay) {
                            return;
                        }
                        selectedPolygon.value = event.overlay;
                        polygons.forEach((poly) => {
                            poly.setOptions({ fillColor: "#0000FF" , editable: false, });
                        });

                        event.overlay.setOptions({ fillColor: "#00FF00" , editable: true, });
                    });

                    polygons.push(event.overlay);
                }
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
                const index = polygons.indexOf(selectedPolygon.value);
                changeDrawingMode('grab');
                if (index > -1) {
                    polygons[index].setMap(null);
                    polygons.splice(index, 1);  // Remove from polygons array
                }
            }
        }
        const removeAllPolygons = () => {
            polygons.forEach(polygon => {
                polygon.setMap(null);
                selectedPolygon.value = null;
            });
            polygons = [];
        }
        const handleSubmit = async () => {
            const errors = validateForm();
            if (Object.keys(errors).length === 0) {
                try {
                    if (polygons.length === 0) {
                        alertMessage.value = t('at_least_one_completed_polygon_is_required');
                        return;
                    }

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

                    const response = await axios.post('store', formData);
                    if (response.status === 201) {
                        successMessage.value = t('airport_created_successfully');
                        form.reset();
                        router.get('/airport');
                    } else {
                        alertMessage.value = t('failed_to_create_airport');
                    }
                } catch (error) {
                    if (error.response && error.response.status === 403) {
                        alertMessage.value = error.response.data.alertMessage;
                        setTimeout(()=>{
                            router.get('/airport');
                        },5000)
                    }else if (error.response && error.response.status === 422) {
                        alertMessage.value = error.response.data.message;
                        form.errors = {
                            coordinates : error.response.data.message,
                        }
                    }else{
                        form.errors = {};
                        alertMessage.value =t('failed_to_create_airport_catch');
                        console.error(t('error_updating_airport'), error);
                    }
                }
            } else {
                form.errors = errors;
            }
        };
        const setActiveTab = (tab) => {
        activeTab.value = tab;
        }
        onMounted(async () => {
        if (Object.keys(languages).length == 0) {
            await fetchData();
        }
        });

        const validateForm = () => {
            const { service_location_id } = form;
            const errors = {};
            if (!service_location_id) {
                errors.service_location_id = t('service_location_is_required');
            }else{
                delete errors.service_location_id;
            }
            if (polygons.length === 0) {
                errors.coordinates = t('at_least_one_completed_polygon_is_required');
            }else{
                delete errors.coordinates;
            }


            return errors;
        };

        onMounted(() => {
            if (!googleMapKey) {
                console.error(t('google_map_api_key_is_null_or_undefined'));
                return;
            }

            // Load Google Maps API script dynamically
            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=${googleMapKey}&libraries=places,drawing`;
            script.onload = () => {
                initializeMap();
                fetchServiceLocations();
            };
            document.head.appendChild(script);
            
        });

        return {
            form,
            successMessage,
            alertMessage,
            handleSubmit,
            serviceLocations,
            search,
            suggestions,
            handleInput,
            selectSuggestion,
            setActiveTab,
            removeAllPolygons,
            removeSelectedPolygon,
            changeDrawingMode,
            activeTab,
            languages,
        };
    },
};
</script>


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
