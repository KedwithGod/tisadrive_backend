<template>
    <Layout>
        <Head title="Map" />
        <PageHeader title="Map" pageTitle="Map" />
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div id="map" style="height: 600px;"></div>
                    </div>
                </div>
            </div>
    </Layout>
</template>


<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import { onMounted } from 'vue';
import axios from 'axios';
import { useI18n } from 'vue-i18n';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';

export default {
        components: {
        Layout,
        PageHeader,
        Head,
    },
    props: {
        zone: Object,
    },
    setup(props) {
        const { zone } = props;
        const { t } = useI18n();
        let map, currentPolygon;
        let polygons = [];

        const bounds = L.latLngBounds();

        const initializeMap = () => {
            if (zone && zone.coordinates) {
                // Initialize the map
                map = L.map('map').setView([0, 0], 10);

                // Set up OpenStreetMap tile layer
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap',
                }).addTo(map);

                // Add drawing functionality
                const drawnItems = new L.FeatureGroup();
                map.addLayer(drawnItems);

                zone.coordinates.forEach(polygon => {
                   
                const polygonCoordinates = polygon[0].map(point => [point.coordinates[1], point.coordinates[0]]);

                currentPolygon = L.polygon(polygonCoordinates, {
                    color: 'blue',
                    weight: 3,
                    fillOpacity: 0.2,
                }).addTo(drawnItems);

                // Adjust map view to fit the polygon
                bounds.extend(currentPolygon.getBounds());

                polygons.push(currentPolygon);

                // Allow polygon editing
                currentPolygon.on('click', function () {
                    // Remove polygon on click
                    map.removeLayer(currentPolygon);
                }); 
                });

                map.fitBounds(bounds);
            }
        };

        onMounted(() => {
            // Initialize the map when the component is mounted
            initializeMap();
        });

        return {};
    },
};
</script>

<style scoped>
.text-danger {
    padding-top: 5px;
}
</style>
