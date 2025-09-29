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
            languageFields:  {},
            unit: "",
            maximum_outstation_distance: "",
            maximum_distance: "",
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
                        successMessage.value = t('zone_created_successfully');
                        form.reset();
                        router.get('/zones');
                    } else {
                        alertMessage.value = t('failed_to_create_zone');
                    }
                } catch (error) {
                    if (error.response && error.response.status === 403) {
                        alertMessage.value = error.response.data.alertMessage;
                        setTimeout(()=>{
                            router.get('/zones');
                        },5000)
                    }else{
                        console.error(t('error_creating_zone'), error);
                        alertMessage.value = t('failed_to_create_zone_catch');
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
                errors.unit = t('unit_is_required');
            } else {
                delete errors.unit;
            }
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
        <Head title="Zones" />
        <PageHeader :title="$t('create')" :pageTitle="$t('zone')" pageLink="/zones" />
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
                                    <label :for="`name-${language.code}`" class="form-label">{{$t("name")}}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" :placeholder="`Enter Name in ${language.label}`"
                                        :id="`name-${language.code}`" v-model="form.languageFields[language.code]"
                                        :required="language.code === 'en'">
                                    </div>
                                </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="select_unit" class="form-label">{{$t("select_unit")}}
                                    <span class="text-danger">*</span>
                                </label>
                                <select id="select_unit" class="form-select" v-model="form.unit">
                                    <option disabled value="">{{$t("choose_unit")}}</option>
                                    <option value=1>{{$t("kilo_meter")}}</option>
                                    <option value=2>{{$t("miles")}}</option>
                                </select>
                                <span v-if="form.errors.unit" class="text-danger">{{ form.errors.unit }}</span>
                            </div>
                            <div class="mb-3">
                                <label for="maximum_distance" class="form-label">{{$t("maximum_distance")}}
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="number" step="any" class="form-control" :placeholder="$t('enter_maximum_distance')" id="maximum_distance" v-model.number="form.maximum_distance">
                                <span v-if="form.errors.enter_maximum_distance" class="text-danger">{{ form.errors.enter_maximum_distance }}</span>
                            </div>
                            <div class="mb-3">
                                <label for="maximum_outstation_distance" class="form-label">{{$t("maximum_outstation_distance")}}
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="number" step="any" class="form-control" :placeholder="$t('enter_maximum_outstation_distance')" id="maximum_outstation_distance" v-model.number="form.maximum_outstation_distance">
                                <span v-if="form.errors.maximum_outstation_distance" class="text-danger">{{ form.errors.maximum_outstation_distance }}</span>
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
