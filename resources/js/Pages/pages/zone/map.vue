<script>
import { onMounted } from 'vue';
import axios from 'axios';
import { useI18n } from 'vue-i18n';

export default {
    props: {
        zone: Object,
        googleMapKey: String, // Define the googleMapKey prop
    },
    setup(props) {
        const { zone, googleMapKey } = props;
        const { t } = useI18n();
        let map, drawingManager, currentPolygon;

        let polygons = [];



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
                    editable: false,
                    draggable: false,
                    map: map,
                });
                polygons.push(currentPolygon);

                currentPolygon.getPath().forEach(coord => bounds.extend(coord));
                })
                map.fitBounds(bounds);

                initializeDrawingManager();
            }
        };

        const initializeDrawingManager = () => {
            drawingManager = new google.maps.drawing.DrawingManager({
                drawingMode: null,
                drawingControl: false,
                drawingControlOptions: {
                    position: google.maps.ControlPosition.TOP_CENTER,
                    drawingModes: [google.maps.drawing.OverlayType.POLYGON],
                },
                polygonOptions: {
                    editable: false,
                    draggable: false,
                },
            });

            drawingManager.setMap(map);
        };

        onMounted(() => {
            // Load Google Maps API script dynamically using the googleMapKey prop
            if (!googleMapKey) {
                console.error(t('google_map_api_key_is_null_or_undefined'));
                return;
            }


            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=${googleMapKey}&libraries=places,drawing`;
            script.onload = () => {
                initializeMap();
            };
            script.onerror = () => {
                console.error(t('error_loading_google_maps_api_script'));
            };
            document.head.appendChild(script);
        });

        return {
        };
    },
};
</script>


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


<style scoped>
.text-danger {
    padding-top: 5px;
}
</style>
