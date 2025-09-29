<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Swal from "sweetalert2";
import { ref, watch } from "vue";
import axios from "axios";
import { debounce } from 'lodash';
import Multiselect from "@vueform/multiselect";
import ImageUpload from "@/Components/ImageUpload.vue";
import FormValidation from "@/Components/FormValidation.vue";
import { useI18n } from 'vue-i18n';

export default {
  components: {
    Layout,
    PageHeader,
    Head,
    FormValidation,
    Multiselect,
    ImageUpload,
  },
  props: {
    successMessage: String,
    alertMessage: String,
    lastLevel: Number,
    level: Object,
    zone_type: Object,
  },
  setup(props) {
    const { t } = useI18n();
    const lastLevel = props.level ? props.level.level : (props.lastLevel ? props.lastLevel + 1 : 1);
    const level = ref(props.level);
    const form = useForm({
      name: props.level ? props.level.name : null,
      is_min_ride_complete: props.level ? props.level.is_min_ride_complete == 1 :false,
      is_min_ride_amount_complete: props.level ? props.level.is_min_ride_amount_complete == 1 :false,
      level: lastLevel,
      reward_type: props.level ? props.level.reward_type : "",
      reward: props.level ? props.level.reward : 0,
      min_ride_amount: props.level ? props.level.min_ride_amount : 0,
      min_ride_count: props.level ? props.level.min_ride_count : 0,
      ride_points: props.level ? props.level.ride_points : 0,
      amount_points: props.level ? props.level.amount_points : 0,
      zone_type_id: props.zone_type ? props.zone_type.id : null,
      image: props.level ? props.level.image : null,
    });
console.log(props.zone_type);
    const successMessage = ref(props.successMessage || '');
    const alertMessage = ref(props.alertMessage || '');

    const dismissMessage = () => {
      successMessage.value = "";
      alertMessage.value = "";
    };

    const validationRules = {
        name: { required: true },
        level: { required: true },
        reward_type: { required: true },
        reward: { required: true },
        min_ride_amount: { required: form.is_min_ride_amount_complete },
        amount_points: { required: form.is_min_ride_amount_complete },
        min_ride_count: { required: form.is_min_ride_complete },
        ride_points: { required: form.is_min_ride_complete },
        image: { required: props.level ? false : true },
    };

    const validationRef = ref(null);
    const errors = ref({});


const handleSubmit = async () => {

    errors.value = validationRef.value.validate();
    if (Object.keys(errors.value).length > 0) {
        return;
    }
    try {
        const formData = new FormData(); // Ensure it's FormData

        // Append other form data
        Object.entries(form.data()).forEach(([key, value]) => {
            if(typeof value == "boolean"){
                formData.append(key, value == true ? 1:0);
            }else{
                formData.append(key, value);
            }
        });
        var url = "/drivers-levelup/store";
        if(props.level && props.level.id){
            url = `/drivers-levelup/update/${props.level.id}`;
        }
        await axios.post(url, formData, {
            headers: {
            'Content-Type': 'multipart/form-data',
            },
        });

        Swal.fire(t('success'), t('form_submitted_successfully'), 'success');
        form.reset();
        router.get(`/drivers-levelup/${props.zone_type.id}`);
    } catch (error) {
        console.error(t('error_submitting_form'), error);
        Swal.fire(t('error'), t('an_error_occurred_while_submitting_the_form'), 'error');
    }
};



    const handleImageSelected = (file, fieldName) => {
      form[fieldName] = file;
    };

    const handleImageRemoved = (fieldName) => {
      form[fieldName] = null;
    };

    return {
      form,
      successMessage,
      alertMessage,
      handleSubmit,
      validationRules,
      validationRef,
      dismissMessage,
      errors,
      level,
      handleImageSelected,
      handleImageRemoved,
    };
  },
  mounted() {
    // Any mounted logic can go here
  },
};
</script>


<template>
    <Layout>
        <Head :title="level ? 'Level Edit' : 'Level Create'" />
        <PageHeader :title="level ? $t('edit') : $t('create')" :pageTitle="$t('level')" pageLink="/set-prices"/>
        <form @submit.prevent="handleSubmit">
        <FormValidation :form="form" :rules="validationRules" ref="validationRef">
            <BRow>
                <BCol lg="12">
                    <BCard no-body id="tasksList">
                        <BCardHeader class="border-0">
                            <h4>{{$t("current_level_info")}}</h4>
                        </BCardHeader>
                        <BCardBody class="border border-dashed border-end-0 border-start-0">
                            <div class="row">
                                <div class="col-6">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="mb-3">
                                                <label for="level" class="form-label">{{$t("level")}}</label>
                                                <input disabled id="level" name="level" class="form-control" v-model="form.level">
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="mb-3">
                                                <label for="name" class="form-label">{{$t("level_name")}}
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control" :placeholder="$t('ex_silver_gold_platinum')" id="name" v-model="form.name">
                                                <span v-for="(error,index) in errors.name" class="text-danger">{{ error }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2"></div>
                                <div class="col-sm-4">
                                    <div class="mb-3">
                                        <label for="icon" class="form-label d-flex">{{$t("level_icon")}} <span><h5 class="text-muted mt-1 mb-0 fs-13">(320px x 320px)</h5></span>
                                            <span class="text-danger">*</span></label>
                                        <ImageUpload :imageType="'drivers-levelup'" :initialImageUrl="form.image"   :flexStyle="'0 0 calc(50% - 20px)'" :aspectRatio="'1 / 1'"   
                                        @image-selected="(file) => handleImageSelected(file, 'image')" @image-removed="() => handleImageRemoved('image')"  id="levelupicon"></ImageUpload>
                                        <span v-for="(error,index) in errors.image" class="text-danger">{{ error }}</span>
                                    </div>
                                </div>
                            </div>
                        </BCardBody>
                    </BCard>
                </BCol>
            </BRow>
            <BRow>
                <BCol lg="12">
                    <BCard no-body id="tasksList">
                        <BCardHeader class="border-0">
                            <h4>{{$t("reward_type")}}</h4>
                        </BCardHeader>
                        <BCardBody class="border border-dashed border-end-0 border-start-0">
                            <div class="row">
                               <div class="col-6">
                                    <div class="mb-3">
                                        <label for="reward_type" class="form-label">{{$t("select_level")}}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select id="reward_type" class="form-select" v-model="form.reward_type">
                                            <option disabled value="">{{$t("choose_level")}}</option>
                                            <option value="no-reward">{{$t("no_reward")}}</option>
                                            <option value="reward-cash">{{$t("reward_credit")}}</option>
                                            <option value="reward-point">{{$t("reward-point")}}</option>
                                        </select>
                                        <span v-for="(error,index) in errors.reward_type" class="text-danger">{{ error }}</span>
                                    </div>
                                </div>

                                <!-- Show reward_cash input if Reward Cash is selected -->
                                <div class="col-sm-6" v-if="form.reward_type === 'reward-cash'">
                                    <div class="mb-3">
                                        <label for="reward_cash" class="form-label">{{$t("reward_credit")}}</label>
                                        <input type="number"  step="any" oninput="this.value = this.value.replace(/[^0-9.]/g, '')"
                                        class="form-control" :placeholder="$t('enter_reward_cash')" id="reward_cash" v-model="form.reward">
                                        <span v-for="(error,index) in errors.reward" class="text-danger">{{ error }}</span>
                                    </div>
                                </div>

                                <!-- Show reward_point input if Reward Point is selected -->
                                <div class="col-sm-6" v-if="form.reward_type === 'reward-point'">
                                    <div class="mb-3">
                                        <label for="reward_point" class="form-label">{{$t("reward_point")}}</label>
                                        <input type="number" step="any" oninput="this.value = this.value.replace(/[^0-9.]/g, '')"
                                         class="form-control" :placeholder="$t('enter_reward_point')" id="reward_point" v-model="form.reward">
                                        <span v-for="(error,index) in errors.reward" class="text-danger">{{ error }}</span>
                                    </div>
                                </div>
                            </div>
                        </BCardBody>
                    </BCard>
                </BCol>
            </BRow>
            <BRow>
                <BCol lg="12">
                    <BCard no-body id="tasksList">
                        <BCardHeader class="border-0">
                            <h4>{{$t("set_target_to_promote_from_this_level")}}</h4>
                        </BCardHeader>
                        <BCardBody class="border border-dashed border-end-0 border-start-0">
                            <div class="row">
                                <div class="col-4">
                                    <div class="mb-3">
                                        <label for="is_min_ride_complete" class="form-label">{{$t("check_to_set")}}</label>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="is_min_ride_complete"
                                                v-model="form.is_min_ride_complete" :checked="form.is_min_ride_complete"
                                            >
                                            <label class="form-check-label" for="is_min_ride_complete">
                                               {{$t("minimum_ride_complete")}}
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Conditionally show min_ride_amount input if checkbox is checked -->
                                <div class="col-sm-4" v-if="form.is_min_ride_complete">
                                    <div class="mb-3">
                                        <label for="min_ride_amount" class="form-label">{{$t("min_ride_count")}}</label>
                                        <input 
                                            type="number" 
                                            class="form-control" 
                                            :placeholder="$t('enter_min_ride_count')" 
                                            id="min_ride_amount" 
                                            v-model="form.min_ride_count"
                                        >
                                        <span v-for="(error,index) in errors.min_ride_count" class="text-danger">{{ error }}</span>
                                    </div>
                                </div>

                                <!-- Conditionally show points input if checkbox is checked -->
                                <div class="col-sm-4" v-if="form.is_min_ride_complete">
                                    <div class="mb-3">
                                        <label for="ride_points" class="form-label">{{$t("reward_point")}}</label>
                                        <input type="number"  step="any" oninput="this.value = this.value.replace(/[^0-9.]/g, '')"
                                        class="form-control" :placeholder="$t('enter_ride_points')" id="ride_points" v-model="form.ride_points"
                                        >
                                        <span v-for="(error,index) in errors.ride_points" class="text-danger">{{ error }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-5">
                                <div class="col-4">
                                    <div class="mb-3">
                                        <label for="reward_type" class="form-label">{{$t("check_to_set")}}</label>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="is_min_ride_amount_complete" name="is_min_ride_amount_complete" v-model="form.is_min_ride_amount_complete">
                                            <label class="form-check-label" for="is_min_ride_amount_complete">
                                                {{$t("minimum_earn_amount")}}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4" v-if="form.is_min_ride_amount_complete">
                                    <div class="mb-3">
                                        <label for="min_ride_amount" class="form-label">{{$t("min_ride_amount")}}</label>
                                        <input type="number"  step="any" oninput="this.value = this.value.replace(/[^0-9.]/g, '')"
                                        class="form-control" :placeholder="$t('enter_min_ride_amount')" id="min_ride_amount" v-model="form.min_ride_amount">
                                        <span v-for="(error,index) in errors.min_ride_amount" class="text-danger">{{ error }}</span>
                                    </div>
                                </div>
                                <div class="col-sm-4" v-if="form.is_min_ride_amount_complete">
                                    <div class="mb-3">
                                        <label for="amount_points" class="form-label">{{$t("reward_point")}}</label>
                                        <input type="number"  step="any" oninput="this.value = this.value.replace(/[^0-9.]/g, '')"
                                        class="form-control" :placeholder="$t('enter_amount_points')" id="amount_points" v-model="form.amount_points">
                                        <span v-for="(error,index) in errors.amount_points" class="text-danger">{{ error }}</span>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary"> {{ level ? $t('update') : $t('save') }}</button>
                                    </div>
                                </div>
                            </div>
                        </BCardBody>
                    </BCard>
                </BCol>
            </BRow>
        </FormValidation>
        </form>
        <div>
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
</style>
