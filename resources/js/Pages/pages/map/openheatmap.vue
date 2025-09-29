<script>
import { ref, watch, onMounted } from "vue";
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Slider from "@vueform/slider";
import L from "leaflet";
import "leaflet/dist/leaflet.css";
import { useI18n } from 'vue-i18n';
import "leaflet.heat";


export default {
    components: {
        Layout,
        PageHeader,
        Slider,
    },
    props: {
        default_lat:String,
        default_lng:String,
        requestData: {
            type: Array,
            default: () => [],
        },
    },
    setup(props) {
        const map = ref(null);
        const heatmapLayer = ref(null);
        const selectedGradient = ref('Default');
        const opacity = ref(50);
        const currentLat = ref(props.default_lat ?? 0);
        const currentLng = ref(props.default_lng ?? 0);
        const radius = ref(20);

        const gradients = ref([
            {
                name: "Default",
                colors: ['#00ffff', '#00bfff', '#007fff', '#003fff', '#0000ff', '#7f00ff', '#ff00ff', '#ff007f', '#ff0000'],
            },
            {
                name: "Warm",
                colors: ['#ffff00', '#ffbf00', '#ff7f00', '#ff3f00', '#ff0000', '#bf0000', '#7f0000'],
            },
            {
                name: "Cool",
                colors: ['#00ffff', '#00bfff', '#007fff', '#003fff', '#0000ff'],
            }
        ]);

        const updateHeatmap = () => {
            if (heatmapLayer.value) {
                const heatmapOptions = {
                    radius: radius.value,
                    opacity: opacity.value/100,
                    gradient: createGradient(),
                };
                heatmapLayer.value.setOptions(heatmapOptions);
            }
        };

        const createGradient = () => {

            const gradientColors = gradients.value.find(g => g.name === selectedGradient.value).colors;
            const opcaities = gradientColors.reduce((acc,item,index) => {
                const key = ((index+1)/gradientColors.length).toFixed(2);
                acc[key] = item;
                return acc;
            },{})
            return opcaities;
        };

        const initializeHeatmap = () => {
            map.value = L.map('map').setView([currentLat.value, currentLng.value], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map.value);

            if (props.requestData.length) {
                const heatmapData = props.requestData.map(item => [item.pick_lat, item.pick_lng, 1]);

                heatmapLayer.value = L.heatLayer(heatmapData, {
                    radius: radius.value,
                    opacity: opacity.value/100,
                    gradient: createGradient(),
                }).addTo(map.value);
            }
        };

        watch([opacity, radius, selectedGradient], ()=>{
            updateHeatmap();
        })

        onMounted(() => {
            initializeHeatmap();
        });

        return {
            map,
            heatmapLayer,
            gradients,
            selectedGradient,
            opacity,
            radius,
        };
    },
};
</script>

<template>
    <Layout>
        <Head title="Heat Map" />
        <PageHeader :title="$t('heat_map')" :pageTitle="$t('map')"/>
        <BRow>
            <BCol lg="12">
                <BCard no-body id="tasksList">
                    <BCardBody class="border border-dashed border-end-0 border-start-0">
                        <div id="map" style="height: 400px;">{{$t("map_loading")}}</div>
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>
        <div class="row">  
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">{{$t("visibility")}}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 mb-5">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="gradient" class="form-label fs-14">{{$t("gradient")}}</label>
                                        <select class="form-select" v-model="selectedGradient">
                                            <option v-for="gradient in gradients" :key="gradient.name" :value="gradient.name">
                                                {{ gradient.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6 mb-3">
                                <div>
                                    <h5 class="fs-14 mb-3">{{$t("opacity")}}</h5>
                                    <Slider :type="range" v-model="opacity" data-slider-size="lg"/>
                                </div>
                            </div>
                            <!-- end col -->
                            <div class="col-xl-6 col-lg-6 mb-3">
                                <div class="mt-4 mt-lg-0">
                                    <h5 class="fs-14 mb-3">{{$t("radius")}}</h5>
                                    <Slider :type="range" v-model="radius" data-slider-size="lg"/>
                                </div>
                            </div>
                            <!-- end col -->
                        </div>
                        <!-- end row -->
                    </div>
                    <!-- end card body -->
                </div>
                <!-- end card -->
            </div>
            <!-- end col -->
        </div>
    </Layout>
</template>

<style>
#map {
    height: 400px;
    margin-bottom: 10px;
}

.controls {
    display: flex;
    flex-direction: column;
    margin-top: 10px;
}

.controls label {
    margin-top: 5px;
}

.controls input[type="range"],
.controls select {
    width: 100%;
    margin-bottom: 10px;
}
</style>



