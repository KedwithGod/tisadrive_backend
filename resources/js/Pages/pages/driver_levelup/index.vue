<script>
import { Link,Head, useForm } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import dropZone from "@/Components/widgets/dropZone.vue";
import search from "@/Components/widgets/search.vue";
import searchbar from "@/Components/widgets/searchbar.vue";
import imageUpload from "@/Components/widgets/imageUpload.vue";
import Swal from "sweetalert2";
import { ref, watch } from "vue";
import axios from "axios";
import { debounce, result } from 'lodash';
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import { mapGetters } from 'vuex';
import { layoutComputed } from "@/state/helpers";
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
        Head,
        Pagination,
        Multiselect,
        flatPickr,
        Link,
        dropZone,
        imageUpload,
        search,
        searchbar,
    },
    props: {
      levels:Object,
      show_driver_level_feature:Boolean,
      reward_point_value: Number,
      minimun_reward_point: Number,
      currency_symbol:CharacterData,
      app_for: String,
      zone_type: Object,
    }
    ,
    setup(props) {
        const { t } = useI18n();
        const searchTerm = ref("");
        const form = useForm({
            zone_type_id:props.zone_type.id
        });
        const paginator = ref({}); // Spread the roles to make them reactive
        const modalShow = ref(false);
        const modalForm = ref(false);
        const modalFilter = ref(false);
        const deleteItemId = ref(null);

        const successMessage = ref(props.successMessage || '');
        const alertMessage = ref(props.alertMessage || '');

        const levels = ref(props.levels || false);
        const show_driver_level_feature = ref(props.show_driver_level_feature);
        const reward_point_value = ref(props.reward_point_value);
        const minimun_reward_point = ref(props.minimun_reward_point);
        const currency_symbol = ref(props.currency_symbol);
        let currency_value = ref(1);
        let reward_value =ref( reward_point_value.value);

        const hideError = (field) => {
            form.errors[field] = '';
        };

        const deleteData = async (dataId) => {
            try {
                await axios.delete(`/drivers-levelup/delete/${dataId}`);
                const index = levels.value.findIndex(data => data.id === dataId);
                if (index !== -1) {
                    levels.value.splice(index, 1);
                }
                modalShow.value = false;
                Swal.fire(t('success'), t('delete_drivers_level_deleted_successfully'), 'success');
            } catch (error) {
                Swal.fire(t('error'), t('failed_to_delete_drivers_level'), 'error');
            }
        };
        const handleSettingChange = async () => {

        }
        const handleRewardValueChange = async () => {
            try {
                reward_point_value.value = reward_point_value.value > 0 ? reward_point_value.value : 0;
                minimun_reward_point.value = minimun_reward_point.value > 0 ? minimun_reward_point.value : 0;
                await axios.post(`/drivers-levelup/settingsUpdate`,{id: 'reward_point_value',status: reward_point_value.value ?? 0});
                await axios.post(`/drivers-levelup/settingsUpdate`,{id: 'minimun_reward_point',status: minimun_reward_point.value ?? 0});
                Swal.fire(t('success'), t('show_driver_level_feature_updated_successfully'), 'success');
                updateRewardValue();
            } catch (error) {
                Swal.fire(t('error'), t('failed_to_update_show_driver_level_feature'), 'error');
            }
        };
        const handleLevelUpUpdate = async () => {
            try {
                await axios.post(`/drivers-levelup/settingsUpdate`,{id:'show_driver_level_feature',status: show_driver_level_feature.value? 0: 1});
                show_driver_level_feature.value = !show_driver_level_feature.value;
                Swal.fire(t('success'), t('show_driver_level_feature_updated_successfully'), 'success');
            } catch (error) {
                Swal.fire(t('error'), t('failed_to_update_show_driver_level_feature'), 'error');
            }
        };

        const deleteModal = async (itemId) => {
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#34c38f",
                cancelButtonColor: "#f46a6a",
                confirmButtonText: "Yes, delete it!",
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

        const clearFilter = () => {
            filter.reset();
            fetchDatas();
            modalFilter.value = false;
        };
        const fetchDatas = async (page = 1) => {
            try {
                const params = form.data();
                params.search = searchTerm.value;
                params.page = page;
                const response = await axios.get(`/drivers-levelup/list`, { params });
                levels.value = response.data.results;
                paginator.value = response.data.paginator;
                modalFilter.value = false;
            } catch (error) {
                console.error(t('error_fetching_goods_type'), error);
            }
        };

        const SerialNumber = (index) => {
            return ((paginator.value.current_page-1)*paginator.value.per_page)+index+1;
        };

        const handlePageChanged = async (page) => {
            fetchDatas(page);
        };

        const updateRewardValue = () => {
            reward_value.value = (parseFloat(currency_value.value) * reward_point_value.value).toFixed(2);
        };

        const updateCurrencyValue = () => {
            currency_value.value = (parseFloat(reward_value.value) / reward_point_value.value).toFixed(2);
        };

        return {
            form,
            modalShow,
            modalForm,
            deleteItemId,
            successMessage,
            alertMessage,
            hideError,
            fetchDatas,
            clearFilter,
            deleteModal,
            searchTerm,
            SerialNumber,
            paginator,
            levels,
            handleSettingChange,
            minimun_reward_point,
            modalFilter,
            show_driver_level_feature,
            handleLevelUpUpdate,
            currency_symbol,
            currency_value,
            reward_value,
            updateRewardValue,
            updateCurrencyValue,
            handlePageChanged,
            reward_point_value,
            handleRewardValueChange,

        };
    },
    mounted() {
        this.fetchDatas();
    },
    computed: {
    ...layoutComputed,
    ...mapGetters(['permissions']),
  },
    
};
</script>

<template>
    <Layout>

        <Head :title="$t('driver_levelup')" />
        <PageHeader :title="$t('driver_levelup')" :pageTitle="$t('driver_levelup')" pageLink="/set-prices" />
        <BRow>
            <BCol lg="12">
                <BCard v-if="app_for === 'demo'" no-body id="tasksList">
                  <BCardHeader class="border-0">
                    <div class="alert bg-warning border-warning fs-18" role="alert">
                      <strong> {{$t('note')}} : <em> {{$t('actions_restricted_due_to_demo_mode')}}</em> </strong>
                    </div>
                  </BCardHeader>
                </BCard>
                <BCard no-body id="">

                    <BCardHeader class="border-0">
                        <BRow>
                            <BCol lg="12">
                                <div class="row">                                   
                                    <div class="col-sm-3">
                                        <div class="card border border-dashed border-primary">
                                            <div class="card-body text-center">
                                                <h4 class="mb-3 flex-grow-1 text-start text-muted badge bg-secondary-subtle fs-18">{{$t("zone")}}</h4>
                                                <h5>{{ zone_type.zone_name }}</h5>
                                            </div>
                                        </div>
                                    </div>  
                                    <div class="col-sm-3">
                                        <div class="card border border-dashed border-primary">
                                            <div class="card-body text-center">
                                                <h4 class="mb-3 flex-grow-1 text-start text-muted badge bg-secondary-subtle fs-18">{{$t("vehicle_type")}}</h4>
                                                <h5>{{ zone_type.vehicle_type_name }}</h5>
                                            </div>
                                        </div>
                                    </div>                                 
                                </div>
                            </BCol>
                        </BRow>
                    </BCardHeader>
                    </BCard>
                    <BCard v-if="permissions.includes('change-reward-value')">
                    <BCardHeader class="border-0">
                        
                        <BRow class="g-2">
                            <div class="col-sm-6" v-if="permissions.includes('add-drivers-levelup')">
                                <div class="mb-3">
                                    <label class="form-check-label p-2 mt-2" for="reward_point_value">{{$t("reward_point_value") }} 
                                    <a href="" class="text-success" data-bs-toggle="modal" data-bs-target="#reward_point_conversion">{{$t("how_it_works")}}</a>
                                    </label>
                                    <input 
                                    type="number" 
                                    :readonly="app_for == 'demo'"
                                    class="form-control" 
                                    :placeholder="$t('enter_reward_point_value')" 
                                    id="reward_point_value" 
                                    @change="handleRewardValueChange"
                                    v-model="reward_point_value" 
                                    />
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-check-label p-2 mt-2" for="minimun_reward_point">{{$t("minimun_reward_point") }} 
                                    </label>
                                    <input 
                                    type="number" 
                                    class="form-control" 
                                    :readonly="app_for == 'demo'"
                                    :placeholder="$t('enter_minimun_reward_point')" 
                                    id="minimun_reward_point" 
                                    @change="handleRewardValueChange"
                                    v-model="minimun_reward_point" 
                                    />
                                </div>
                                <BCol md="auto" v-if="app_for!== 'demo'" class="ms-auto">
                                    <div class="gap-2">
                                        <BButton variant="primary"  
                                            @click="handleRewardValueChange"
                                            class="float-end"
                                        > {{ $t('save') }}</BButton>
                                    </div>
                                </BCol>
                            </div>

                            <BCol md="auto" v-if="app_for!== 'demo'" class="ms-auto">
                                
                                <div class="d-flex align-items-center gap-2">

                                
                                <Link :href="`/drivers-levelup/create/${zone_type.id ?? ''}`" v-if="permissions.includes('add-drivers-levelup') && show_driver_level_feature">
                                    <BButton variant="primary" class="float-end"> <i
                                            class="ri-add-line align-bottom me-1"></i> {{ $t('add_level') }}</BButton>
                                    </Link>
                                </div>
                            </BCol>
                        </BRow>
                    </BCardHeader>
                    <BCardBody class="border border-dashed border-end-0 border-start-0" v-if="show_driver_level_feature">
                        <div class="todo-task" id="todo-task">
                        <div class="table-responsive">
                            <table class="table align-middle position-relative table-nowrap">
                                <thead class="table-active">
                                    <tr>
                                        <th scope="col">{{ $t('level') }}</th>
                                        <th scope="col">{{ $t('level_name') }}</th>
                                        <th scope="col">{{ $t('target_to_proceed') }}</th>
                                        <th scope="col">{{ $t('reward') }}</th>
                                        <th v-if="app_for!== 'demo'" scope="col">{{ $t('action') }}</th>
                                    </tr>
                                </thead>

                                <tbody v-if="levels && levels.length > 0">
                                    <tr v-for="(level,index) in levels">
                                        <!-- <td> {{ SerialNumber(index) }}</td> -->
                                        <td>
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="flex-grow-1 ms-2">
                                                    <h6> {{  level.level }}</h6>
                                                </div>
                                            </div>
                                           
                                        </td>
                                        <td>
                                          <div class="d-flex align-items-center">
                                            <img :src="level.image ?? '/assets/images/users/avatar-1.jpg'" alt="@assets/images/users/avatar-1.jpg" class="avatar-xs rounded-circle me-2"><span class="text-muted">{{level.name}}</span>
                                          </div>
                                        </td>
                                        <td>
                                            <div v-if="level.is_min_ride_complete" class="d-flex align-items-center mb-2">
                                                <div class="flex-shrink-0">
                                                    <p class="text-muted mb-0">{{$t("ride_completed")}}:</p>
                                                </div>
                                                <div class="flex-grow-1 ms-2">
                                                    <h6 class="mb-0"> {{ level.min_ride_count }} ({{ level.ride_points }})</h6>
                                                </div>
                                            </div>
                                            <div v-if="level.is_min_ride_amount_complete" class="d-flex align-items-center mb-2">
                                                <div class="flex-shrink-0">
                                                    <p class="text-muted mb-0">{{$t("min_ride_amount")}}:</p>
                                                </div>
                                                <div class="flex-grow-1 ms-2">
                                                    <h6 class="mb-0"> {{ level.min_ride_amount }} ({{ level.amount_points }})</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="flex-shrink-0">
                                                    <p class="text-muted mb-0"> {{ $t(level.reward_type) }}</p>
                                                </div>
                                                <div class="flex-grow-1 ms-2">
                                                    <h6 class="mb-0"> {{  level.reward }}</h6>
                                                </div>
                                            </div>
                                           
                                        </td>
                                        <td v-if="app_for!== 'demo'">
                                            <div class="hstack gap-2">
                                                <Link :href="`/drivers-levelup/edit/${level.id}`"
                                                    class="btn btn-soft-info btn-sm m-2">
                                                    <i class='bx bxs-edit-alt'></i>
                                                </Link>
                                                <BButton class="btn btn-soft-danger btn-sm m-2" size="sm" type="button"
                                                    id="sa-warning" v-if="level.level>1" @click.prevent="deleteModal(level.id)">
                                                    <i class='bx bx-trash'></i>
                                                </BButton>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            <img src="@assets/images/search-file.gif" alt="Loading..." style="width:100px" />
                                            <h5> {{ $t("no_data_found") }}</h5>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>
        <div>
            <!-- Success Message -->
            <div v-if="successMessage" class="custom-alert alert alert-success alert-border-left fade show" role="alert"
                id="alertMsg">
                <div class="alert-content">
                    <i class="ri-notification-off-line me-3 align-middle"></i> <strong>Success</strong> - {{
                        successMessage }}
                    <button type="button" class="btn-close btn-close-success" @click="dismissMessage"
                        aria-label="Close Success Message"></button>
                </div>
            </div>

            <!-- Alert Message -->
            <div v-if="alertMessage" class="custom-alert alert alert-danger alert-border-left fade show" role="alert"
                id="alertMsg">
                <div class="alert-content">
                    <i class="ri-notification-off-line me-3 align-middle"></i> <strong>Alert</strong> - {{ alertMessage
                    }}
                    <button type="button" class="btn-close btn-close-danger" @click="dismissMessage"
                        aria-label="Close Alert Message"></button>
                </div>
            </div>
        </div>


        <div id="reward_point_conversion" class="modal fade" tabindex="-1" aria-labelledby="lowLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                    </div>
                    <div class="modal-body">
                        <h5 class="fs-15">
                            LoyaltyPoint Conversion Calculation
                        </h5>
                        <p class="text-muted"> {{$t("currency_in_currency")}} ({{ currency_symbol }})
                            <input 
                                type="number" 
                                class="form-control" 
                                id="currency_value" 
                                @input="updateRewardValue"
                                v-model="currency_value" 
                            />
                        </p>
                        <p class="text-muted"> {{$t("currency_in_rewards")}}
                            <input 
                                type="number" 
                                class="form-control" 
                                id="reward_value" 
                                @input="updateCurrencyValue"
                                v-model="reward_value" 
                            />
                        </p>
                        <p class="text-muted"> {{$t("currency_in_currency")}} <strong>{{ currency_symbol }} 1 </strong> = <strong> {{ reward_point_value }} </strong> {{ $t("reward_points") }}</p>
                    </div>

                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <!-- Pagination -->
        <Pagination :paginator=paginator v-if="show_driver_level_feature" @page-changed="handlePageChanged"/>
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

</style>
