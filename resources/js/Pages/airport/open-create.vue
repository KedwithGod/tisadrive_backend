<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import { ref, onMounted } from "vue";
import { useSharedState } from '@/composables/useSharedState';
import axios from "axios";
import { useI18n } from 'vue-i18n';
import L from 'leaflet'; // Import Leaflet
import 'leaflet-draw'; // Import Leaflet Draw plugin
import { OpenStreetMapProvider, GeoSearchControl } from 'leaflet-geosearch'; // Correct imports
import 'leaflet-draw/dist/leaflet.draw.css';

export default {
    components: {
        Layout,
        PageHeader,
        Head,
    },
    props: {
        successMessage: String,
        alertMessage: String,
        default_lat:String,
        default_lng:String,
        languages: Array,
    },
    setup(props) {
        const { t } = useI18n();
        const { languages, fetchData } = useSharedState();
        const activeTab = ref('English');

        const form = useForm({
            service_location_id: "",
            name:  "",
        });

        const currentLat = ref(parseFloat(props.default_lat));
        const currentLng = ref(parseFloat(props.default_lng));
        const successMessage = ref(props.successMessage || '');
        const alertMessage = ref(props.alertMessage || '');
        const serviceLocations = ref([]);
        let polygons = [];

        const fetchServiceLocations = async () => {
            const response = await axios.get('list');
            serviceLocations.value = response.data.results;
        };

        const initializeMap = () => {
            const map = L.map('map').setView({lat: currentLat.value, lng: currentLng.value}, 10);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            const drawnItems = new L.FeatureGroup();
            map.addLayer(drawnItems);
            // Initialize Leaflet Draw Control
            const drawControl = new L.Control.Draw({
                draw: {
                    polygon: true,
                    polyline: false,
                    rectangle: false,
                    circle: false,
                    marker: false,
                },
                edit: {
                    featureGroup: new L.FeatureGroup().addTo(map),
                },
            });
            map.addControl(drawControl);

            map.on(L.Draw.Event.CREATED, (event) => {
                const layer = event.layer;
                polygons.push(layer);
                drawnItems.addLayer(layer);
                map.addLayer(layer);

            });


            // Handle shape delete
            map.on(L.Draw.Event.DELETED, function (event) {
                const layers = event.layers;
                layers.eachLayer(function (layer) {
                    console.log('Deleted layer:', layer);
                    // Remove layer from your polygons array
                    polygons = polygons.filter(p => p !== layer);
                });
            });

        };

        const handleSubmit = async () => {
            const errors = validateForm();
            if (Object.keys(errors).length === 0) {
                try {
                    if (polygons.length === 0) {
                        alertMessage.value = t('at_least_one_completed_polygon_is_required');
                        return;
                    }

                    const coordinates = polygons.map(polygon =>
                        polygon.getLatLngs()[0].map(latLng => [
                            latLng.lng,
                            latLng.lat
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
                    }else{
                        console.error(t('error_creating_airport'), error);
                        alertMessage.value = t('failed_to_create_airport_catch');
                    }
                }
            } else {
                form.errors = errors;
            }
        };

        const setActiveTab = (tab) => {
            activeTab.value = tab;
        };

        onMounted(async () => {
            if (Object.keys(languages).length == 0) {
                await fetchData();
            }
            initializeMap();
            fetchServiceLocations();
        });

        const validateForm = () => {
            const { service_location_id } = form;
            const errors = {};
            if (!service_location_id) {
                errors.service_location_id = t('service_location_is_required');
            } else {
                delete errors.service_location_id;
            }
            if (polygons.length === 0) {
                errors.coordinates = t('at_least_one_completed_polygon_is_required');
            } else {
                delete errors.coordinates;
            }

            return errors;
        };

        return {
            form,
            successMessage,
            alertMessage,
            handleSubmit,
            serviceLocations,
            setActiveTab,
            activeTab,
            languages,
        };
    },
};
</script>


<template>
    <Layout>
        <Head title="Airport" />
        <PageHeader :title="$t('create')" :pageTitle="$t('airport')" pageLink="/airport" />
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <form @submit.prevent="handleSubmit">
                            <div class="mb-3">
                                <label for="service_location" class="form-label">{{$t("service_location")}}
                                    <span class="text-danger">*</span>
                                </label>
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
                                <button type="submit" class="btn btn-primary">{{$t("save")}}</button>
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
                        <input
                            id="pac-input"
                            class="form-control"
                            type="text"
                            :placeholder="$t('search_for_a_city')"
                            ref="autocompleteInput"
                        />
                        <div id="map" style="height: 400px;"></div>
                    </div>
                </div>
            </div>
        </div>
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
</style>
