<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import { ref, watch } from "vue";
import axios from "axios";
import imageUpload from "@/Components/widgets/imageUpload.vue";
import Multiselect from "@vueform/multiselect";
import FormValidation from "@/Components/FormValidation.vue";
import { useI18n } from 'vue-i18n';
import { BCard, BCardBody } from 'bootstrap-vue-next';

export default {
  components: {
    Layout,
    PageHeader,
    Head,
    Pagination,
    Multiselect,
    FormValidation,
    imageUpload,
  },
  props: {
    successMessage: String,
    alertMessage: String,
    referral_commission_amount_for_user: Object,
    referral_commission_amount_for_driver: Object,
    referral_commission_amount_for_user_reffering_a_driver: Object,
    referral_commission_amount_for_driver_reffering_a_user: Object,
    enable_user_referral_earnings: Object,
    enable_driver_referral_earnings: Object,
    app_for: String,
    validate: Function,
  },
  setup(props) {
    const { t } = useI18n();

    const form = useForm({
      enable_user_referral_earnings: props.enable_user_referral_earnings ?? false,
      enable_driver_referral_earnings: props.enable_driver_referral_earnings ?? false,
      referral_commission_amount_for_user: props.referral_commission_amount_for_user?.value ?? "",
      referral_commission_amount_for_user_reffering_a_driver: props.referral_commission_amount_for_user_reffering_a_driver?.value ?? "",
      referral_commission_amount_for_driver: props.referral_commission_amount_for_driver?.value ?? "",
      referral_commission_amount_for_driver_reffering_a_user: props.referral_commission_amount_for_driver_reffering_a_user?.value ?? "",
    });

    const validationRef = ref(null);
    const errors = ref({});
    const successMessage = ref(props.successMessage || '');
    const alertMessage = ref(props.alertMessage || '');

    const dismissMessage = () => {
      successMessage.value = "";
      alertMessage.value = "";
    };

    const handleSubmit = async () => {
      errors.value = validationRef.value.validate();

      if (Object.keys(errors.value).length > 0) {
        return;
      }

      try {
        const response = await axios.post(`/referral-settings/update`, form.data());

        if (response.status === 200) {
          successMessage.value = t('referral_settings_updated_successfully');
          router.get('/referral-settings');
        } else {
          alertMessage.value = t('failed_to_update_referral_settings');
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          errors.value = error.response.data.errors;
        } else {
          console.error(t('error_updating_referral_settings'), error);
          alertMessage.value = t('failed_to_update_referral_settings');
        }
      }
    };

    const handleToggle = async (event) => {
      const isChecked = event.target.checked;
      const dataKey = event.target.getAttribute('data-key');

      form[dataKey] = isChecked; // Update form value based on checkbox state

      try {
        const response = await axios.post(`/referral-settings/toggle`, {
          key: dataKey,
          enabled: isChecked,
        });

        if (response.status === 200) {
          successMessage.value = t('referral_settings_toggled_successfully');
        } else {
          alertMessage.value = t('failed_to_toggle_referral_settings');
        }
      } catch (error) {
        console.error(t('error_toggling_referral_settings'), error);
        alertMessage.value = t('failed_to_toggle_referral_settings');
      }
    };

    // Watch for changes in enable_user_referral_earnings
    watch(() => form.enable_user_referral_earnings, (newValue) => {
      console.log("Updated enable_user_referral_earnings:", newValue);
    });

    return {
      form,
      successMessage,
      alertMessage,
      handleSubmit,
      dismissMessage,
      handleToggle,
      validationRef,
      errors,
    };
  }
};
</script>

<template>
  <Layout>
    <Head title="Referral Settings" />
    <PageHeader :title="$t('referral-settings')" :pageTitle="$t('referral-settings')" />
    <BRow>
      <BCol lg="12">
        <BCard v-if="app_for === 'demo'" no-body id="tasksList">
          <BCardHeader class="border-0">
            <div class="alert bg-warning border-warning fs-18" role="alert">
              <strong> {{$t('note')}} : <em> {{$t('actions_restricted_due_to_demo_mode')}}</em> </strong>
            </div>
          </BCardHeader>
        </BCard>
 <!-- <BCard> -->
         <!-- <BcardBody>
            <div class="row row-cols-xl-5 row-cols-lg-3 row-cols-md-2 row-cols-1 mt-5">
                <div class="col">
                    <div class="card">
                        <div class="card-body text-center">
                            <p class="avatar-md rounded-circle object-fit-cover mt-n5 img-thumbnail border-success mx-auto d-block position-relative"><h2 class="rank">1</h2></p>
                            <a class="mt-5">
                              <img src="assets/images/users/avatar-6.jpg" alt="" class="avatar-md rounded-circle object-fit-cover  img-thumbnail border-light mx-auto d-block">
                                <h5 class="mt-2 mb-1">Arun</h5>
                            </a>
                            <p class="text-muted mb-2">35 Referral</p>
                        </div>
                    </div>
                </div>
               
                <div class="col">
                    <div class="card">
                        <div class="card-body text-center">
                            <p class="avatar-md rounded-circle object-fit-cover mt-n5 img-thumbnail border-success mx-auto d-block position-relative"><h2 class="rank">2</h2></p>
                            <a class="mt-5">
                              <img src="assets/images/users/avatar-6.jpg" alt="" class="avatar-md rounded-circle object-fit-cover  img-thumbnail border-light mx-auto d-block">
                                <h5 class="mt-2 mb-1">Anu</h5>
                            </a>
                            <p class="text-muted mb-2">30 Referral</p>
                        </div>
                    </div>
                </div>
                       
                <div class="col">
                  <div class="card">
                      <div class="card-body text-center">
                          <p class="avatar-md rounded-circle object-fit-cover mt-n5 img-thumbnail border-success mx-auto d-block position-relative"><h2 class="rank">3</h2></p>
                          <a class="mt-5">
                            <img src="assets/images/users/avatar-6.jpg" alt="" class="avatar-md rounded-circle object-fit-cover  img-thumbnail border-light mx-auto d-block">
                              <h5 class="mt-2 mb-1">Indhu</h5>
                          </a>
                          <p class="text-muted mb-2">25 Referral</p>
                      </div>
                  </div>
                </div>
                       
                <div class="col">
                  <div class="card">
                      <div class="card-body text-center">
                          <p class="avatar-md rounded-circle object-fit-cover mt-n5 img-thumbnail border-success mx-auto d-block position-relative"><h2 class="rank">4</h2></p>
                          <a class="mt-5">
                            <img src="assets/images/users/avatar-6.jpg" alt="" class="avatar-md rounded-circle object-fit-cover  img-thumbnail border-light mx-auto d-block">
                              <h5 class="mt-2 mb-1">Nakul</h5>
                          </a>
                          <p class="text-muted mb-2">20 Referral</p>
                      </div>
                  </div>
                </div>
                       
                <div class="col">
                  <div class="card">
                      <div class="card-body text-center">
                          <p class="avatar-md rounded-circle object-fit-cover mt-n5 img-thumbnail border-success mx-auto d-block position-relative"><h2 class="rank">5</h2></p>
                          <a class="mt-5">
                            <img src="assets/images/users/avatar-6.jpg" alt="" class="avatar-md rounded-circle object-fit-cover  img-thumbnail border-light mx-auto d-block">
                              <h5 class="mt-2 mb-1">Sudarsan</h5>
                          </a>
                          <p class="text-muted mb-2">15 Referral</p>
                      </div>
                  </div>
                </div>
                    </div>
          </BcardBody> -->
        <!-- </BCard> -->
        <BCard no-body id="tasksList">
          <BCardBody class="border border-dashed border-end-0 border-start-0">
            <form @submit.prevent="handleSubmit">
              <FormValidation :form="form" :rules="validationRules" ref="validationRef">

                <!-- User Referral Earnings Setup -->
                <section class="row">
                  <div class="accordion custom-accordionwithicon-plus" id="accordionWithplusiconUser">
                    <div class="accordion-item">
                      <div class="accordion-header" id="accordionwithplusExampleUser">
                        <div class="card border rounded">
                          <div class="card-body">
                            <div class="d-flex mt-1">
                              <div>
                                <h5>{{$t("user_referral_earnings_setup")}}</h5>
                                <p class="fs-12">Invite others to use our app with your unique referral code and earn exciting rewards!</p>
                              </div>
                              <div class="ms-auto">
                                <div class="form-check form-switch form-switch-lg mt-2">
                                  <input class="form-check-input" 
                                         type="checkbox" 
                                         :disabled="app_for == 'demo'"
                                         data-bs-toggle="collapse" 
                                         data-bs-target="#showContentUser" 
                                         aria-expanded="true" 
                                         aria-controls="showContentUser" 
                                         @change="handleToggle" 
                                         data-key="enable_user_referral_earnings"
                                         aria-label="Toggle User Referral Earnings Setup"
                                         v-model="form.enable_user_referral_earnings">
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div id="showContentUser" class="accordion-collapse collapse" :class="{ show: form.enable_user_referral_earnings }" aria-labelledby="accordionwithplusExampleUser" data-bs-parent="#accordionWithplusiconUser">
                      <div class="accordion-body">
                        <div class="card border rounded">
                          <div class="card-header accordion-body p-3">
                            <div class="row">
                              <div class="col-lg-6">
                                <h6>{{$t("who_share_the_code_when_user_reffers_user")}}</h6>
                                <p class="fs-12">Offer a reward to Users for each referral when they share their code with Users.</p>
                              </div>
                              <div class="col-lg-6">
                                <div class="border p-4 rounded bg-light">
                                  <label for="referral_commission_amount_for_user" class="form-label">{{$t("earnings_to_each_referral")}}</label>
                                  <input type="text" 
                                         class="form-control" 
                                         placeholder="Enter the Amount" 
                                         id="referral_commission_amount_for_user"  
                                         name="referral_commission_amount_for_user" 
                                         :readonly="app_for == 'demo'"
                                         v-model="form.referral_commission_amount_for_user" 
                                         aria-describedby="userReferralHelp" />
                                  <small id="userReferralHelp" class="form-text text-muted">Enter the amount Users earn for each referral.</small>
                                </div>
                              </div>
                            </div>
                            <div class="border mt-4"></div>
                            <div class="row mt-4">
                              <div class="col-lg-6">
                                <h6>{{$t("who_share_the_code_when_user_reffers_driver")}}</h6>
                                <p class="fs-12">Offer a reward to Users for each referral when they share their code with Drivers.</p>
                              </div>
                              <div class="col-lg-6">
                                <div class="border p-4 rounded bg-light">
                                  <label for="referral_commission_amount_for_user_reffering_a_driver" class="form-label">{{$t("bonus_in_wallet")}}</label>
                                  <input type="text" 
                                         class="form-control" 
                                         placeholder="Enter the Amount" 
                                         :readonly="app_for == 'demo'"
                                         name="referral_commission_amount_for_user_reffering_a_driver" 
                                         id="referral_commission_amount_for_user_reffering_a_driver" 
                                         v-model="form.referral_commission_amount_for_user_reffering_a_driver" 
                                         aria-describedby="walletBonusHelp" />
                                  <small id="walletBonusHelp" class="form-text text-muted">Enter the amount Users earn for each referral.</small>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </section>

                <!-- Driver Referral Earnings Setup -->
                <section class="row mt-5">
                  <div class="accordion custom-accordionwithicon-plus" id="accordionWithplusiconDriver">
                    <div class="accordion-item">
                      <div class="accordion-header" id="accordionwithplusExampleDriver">
                        <div class="card border rounded">
                          <div class="card-body">
                            <div class="d-flex mt-1">
                              <div>
                                <h5>{{$t("driver_referral_earnings_setup")}}</h5>
                                <p class="fs-12">Invite drivers to use our app with your unique referral code and earn exciting rewards!</p>
                              </div>
                              <div class="ms-auto">
                                <div class="form-check form-switch form-switch-lg mt-2">
                                  <input class="form-check-input" 
                                         type="checkbox" 
                                         data-bs-toggle="collapse" 
                                         data-bs-target="#showContentDriver" 
                                         aria-expanded="true" 
                                         :disabled="app_for == 'demo'"
                                         aria-controls="showContentDriver" 
                                         @change="handleToggle" 
                                         data-key="enable_driver_referral_earnings"
                                         aria-label="Toggle Driver Referral Earnings Setup"
                                         v-model="form.enable_driver_referral_earnings">
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div id="showContentDriver" class="accordion-collapse collapse" :class="{ show: form.enable_driver_referral_earnings }" aria-labelledby="accordionwithplusExampleDriver" data-bs-parent="#accordionWithplusiconDriver">
                      <div class="accordion-body">
                        <div class="card border rounded">
                          <div class="card-header accordion-body p-3">
                            <div class="row">
                              <div class="col-lg-6">
                                <h6>{{$t("who_share_the_code_when_driver_reffers_user")}}</h6>
                                <p class="fs-12">Offer a reward to Drivers for each referral when they share their code with Users.</p>
                              </div>
                              <div class="col-lg-6">
                                <div class="border p-4 rounded bg-light">
                                  <label for="referral_commission_amount_for_driver_reffering_a_user" class="form-label">{{$t("earnings_to_each_referral")}}</label>
                                  <input type="text" 
                                         class="form-control" 
                                         placeholder="Enter the Amount" 
                                         id="referral_commission_amount_for_driver_reffering_a_user"  
                                         name="referral_commission_amount_for_driver_reffering_a_user" 
                                         :readonly="app_for == 'demo'"
                                         v-model="form.referral_commission_amount_for_driver_reffering_a_user" 
                                         aria-describedby="driverReferralHelp" />
                                  <small id="driverReferralHelp" class="form-text text-muted">Enter the amount Drivers earn for each referral.</small>
                                </div>
                              </div>
                            </div>
                            <div class="border mt-4"></div>
                            <div class="row mt-4">
                              <div class="col-lg-6">
                                <h6>{{$t("who_share_the_code_when_driver_reffers_driver")}}</h6>
                                <p class="fs-12">Offer a reward to Drivers for each referral when they share their code with Drivers.</p>
                              </div>
                              <div class="col-lg-6">
                                <div class="border p-4 rounded bg-light">
                                  <label for="referral_commission_amount_for_driver" class="form-label">{{$t("bonus_in_wallet")}}</label>
                                  <input type="text" 
                                         class="form-control" 
                                         placeholder="Enter the Amount" 
                                         name="referral_commission_amount_for_driver" 
                                         id="referral_commission_amount_for_driver" 
                                         :readonly="app_for == 'demo'"
                                         v-model="form.referral_commission_amount_for_driver" 
                                         aria-describedby="driverBonusHelp" />
                                  <small id="driverBonusHelp" class="form-text text-muted">Enter the amount Drivers earn for each referral.</small>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </section>

                <!-- Submit Button -->
                <div v-if="app_for!== 'demo'" class="mt-4">
                  <button type="submit" class="btn btn-primary">{{$t("update_referral_settings")}}</button>
                </div>
              </FormValidation>
            </form>
            <div v-if="successMessage" class="alert alert-success mt-3" role="alert">
              {{ successMessage }}
              <button type="button" class="btn-close" aria-label="Close" @click="dismissMessage"></button>
            </div>
            <div v-if="alertMessage" class="alert alert-danger mt-3" role="alert">
              {{ alertMessage }}
              <button type="button" class="btn-close" aria-label="Close" @click="dismissMessage"></button>
            </div>
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
.accordion-button:not(.collapsed)
{
  background-color: #f3f6f9 !important;
}

.custom-accordionwithicon-plus .accordion-button:not(.collapsed)::after{
  content: "" !important; 
}

.custom-accordionwithicon-plus .accordion-button::after{
  content: "" !important;
}
.rank{
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  font-weight: 800;
}
</style>
