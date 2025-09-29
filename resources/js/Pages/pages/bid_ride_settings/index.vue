<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import { ref } from "vue";
import axios from "axios";
import Multiselect from "@vueform/multiselect";
import FormValidation from "@/Components/FormValidation.vue";
import { useI18n } from 'vue-i18n';
import Swal from "sweetalert2";

export default {
  components: {
    Layout,
    PageHeader,
    Head,
    Pagination,
    Multiselect,
    FormValidation,
  },
  props: {
    successMessage: String,
    app_for: String,
    alertMessage: String,
    settings: Object,
    validate: Function, // Define the prop to receive the method
  },
  setup(props) {
    // console.log(props.settings);
    const { t } = useI18n();
    const form = useForm({
      bidding_low_percentage: props.settings.bidding_low_percentage || "",
      bidding_high_percentage: props.settings.bidding_high_percentage || "",
      bidding_amount_increase_or_decrease: props.settings.bidding_amount_increase_or_decrease || "",
      user_bidding_low_percentage: props.settings.user_bidding_low_percentage || "",
      user_bidding_high_percentage: props.settings.user_bidding_high_percentage || "",
      user_bidding_amount_increase_or_decrease: props.settings.user_bidding_amount_increase_or_decrease || "",
    });

// Create refs to hold the values
    const biddingLowPercentage = ref(form.bidding_low_percentage);
    const biddingHighPercentage = ref(form.bidding_high_percentage);
    const biddingAmountIncreaseOrDecrease = ref(form.bidding_amount_increase_or_decrease);
    const userBiddingLowPercentage = ref(form.user_bidding_low_percentage);
    const userBiddingHighPercentage = ref(form.user_bidding_high_percentage);
    const userBiddingAmountIncreaseOrDecrease = ref(form.user_bidding_amount_increase_or_decrease);

    const validationRules = {
    };
    const validationRef = ref(null);
    const errors = ref({});
    const successMessage = ref(props.successMessage || '');
    const alertMessage = ref(props.alertMessage || '');

    const dismissMessage = () => {
      successMessage.value = "";
      alertMessage.value = "";
    };

    const handleSubmit = async () => {
      if(props.app_for == "demo"){
        Swal.fire(t('error'), t('you_are_not_authorised'), 'error');
        return;
      }
      errors.value = validationRef.value.validate();
      if (Object.keys(errors.value).length > 0) {
        return;
      }
      try {
        let response;
        if (props.settings) {
          response = await axios.post(`/bid-ride-settings/update`, form.data());
        } else {
          alertMessage.value = t('failed_to_update_bid_ride_settings');
        }
        if (response.status === 200) {
          successMessage.value = t('bid_ride_settings_created_successfully');
          form.reset();
          router.get('/bid-ride-settings');
        } else {
          alertMessage.value = t('failed_to_update_bid_ride_settings');
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          errors.value = error.response.data.errors;
        } else {
          console.error(t('error_updating_bid_ride_settings'), error);
          alertMessage.value = t('failed_to_update_bid_ride_settings');
        }
      }

    };




    return {
      form,
      successMessage,
      alertMessage,
      handleSubmit,
      dismissMessage,
      selectedCountry: ref(null),
      selectedTimezone: ref(null),
      validationRules,
      validationRef,
      errors,
      biddingLowPercentage,
      biddingHighPercentage,
      biddingAmountIncreaseOrDecrease,
      userBiddingLowPercentage,
      userBiddingHighPercentage,
      userBiddingAmountIncreaseOrDecrease,
    };
  },
  mounted() {
// Access the refs created in the setup function
    const bidding_amount_increase_or_decrease = parseFloat(this.form.bidding_amount_increase_or_decrease) || 5; // Default step value
    const bidding_high_percentage = parseFloat(this.form.bidding_high_percentage) || 10; // Default high percentage
    const bidding_low_percentage = parseFloat(this.form.bidding_low_percentage) || 10; // Default low percentage   
    const currentValue = 150; 
    const user_bidding_amount_increase_or_decrease = parseFloat(this.form.user_bidding_amount_increase_or_decrease) || 5; // Default step value
    const user_bidding_high_percentage = parseFloat(this.form.user_bidding_high_percentage) || 10; // Default high percentage
    const user_bidding_low_percentage = parseFloat(this.form.user_bidding_low_percentage) || 10; // Default low percentage
    const usercurrentValue = 150; // Starting value

// Calculate max and min based on percentages
    const maxValue = currentValue + (currentValue * (bidding_high_percentage / 100));
    const minValue = currentValue - (currentValue * (bidding_low_percentage / 100));

    const usermaxValue = usercurrentValue + (usercurrentValue * (user_bidding_high_percentage / 100));
    const userminValue = usercurrentValue - (usercurrentValue * (user_bidding_low_percentage / 100));

    document.querySelectorAll(".minus").forEach((item) => {
      let productQuantity = item.closest(".input-step").querySelector(".product-quantity");
      item.addEventListener("click", () => {
        let currentQuantity = parseInt(productQuantity.value);
        
        // Decrease value by step if it doesn't go below the min value 
        if (currentQuantity - bidding_amount_increase_or_decrease >= minValue) {
          productQuantity.value = currentQuantity - bidding_amount_increase_or_decrease;
        } else {
          productQuantity.value = minValue; // Set to minimum if limit reached
        }
      });
    });

    document.querySelectorAll(".plus").forEach((item) => {
      let productQuantity = item.closest(".input-step").querySelector(".product-quantity");
      item.addEventListener("click", () => {
        let currentQuantity = parseInt(productQuantity.value);
        
        // Increase value by step if it doesn't exceed the max value
        if (currentQuantity + bidding_amount_increase_or_decrease <= maxValue) {
          productQuantity.value = currentQuantity + bidding_amount_increase_or_decrease;
        } else {
          productQuantity.value = maxValue; // Set to maximum if limit reached
        }
      });
    });

   
    document.querySelectorAll(".user-plus").forEach((item) => {
      let productQuantity = item.closest(".user").querySelector(".product-quantity-user");
      item.addEventListener("click", () => {
        let currentQuantity = parseInt(productQuantity.value);
        
        // Increase value by step if it doesn't exceed the max value
        if (currentQuantity + user_bidding_amount_increase_or_decrease <= usermaxValue) {
          productQuantity.value = currentQuantity + user_bidding_amount_increase_or_decrease;
        } else {
          productQuantity.value = usermaxValue; // Set to maximum if limit reached
        }
      });
    });

    document.querySelectorAll(".user-minus").forEach((item) => {
      let productQuantity = item.closest(".user").querySelector(".product-quantity-user");
      item.addEventListener("click", () => {
        let currentQuantity = parseInt(productQuantity.value);
        
        // Decrease value by step if it doesn't go below the min value 
        if (currentQuantity - user_bidding_amount_increase_or_decrease >= userminValue) {
          productQuantity.value = currentQuantity - user_bidding_amount_increase_or_decrease;
        } else {
          productQuantity.value = userminValue; // Set to minimum if limit reached
        }
      });
    });
  }

};
</script>

<template>
  <Layout>

    <Head title="Bid Ride Settings" />
    <PageHeader :title="$t('bid-ride-settings')" :pageTitle="$t('bid-ride-settings')" />
    <BRow>
        <BCard v-if="app_for === 'demo'" no-body id="tasksList">
          <BCardHeader class="border-0">
            <div class="alert bg-warning border-warning fs-18" role="alert">
              <strong> {{$t('note')}} : <em> {{$t('actions_restricted_due_to_demo_mode')}}</em> </strong>
          </div>
        </BCardHeader>
      </BCard>
      <BCol lg="6">
        <BCard no-body id="tasksList">
          <BCardHeader class="border-0">
            <h4>{{$t("driver")}}</h4>
          </BCardHeader>
          <BCardBody class="border border-dashed border-end-0 border-start-0">
            <form @submit.prevent="handleSubmit">
              <FormValidation :form="form" :rules="validationRules" ref="validationRef">
                <div class="row"> 
                  <div class="col-sm-12">
                    <div class="mb-3">
                      <label for="bidding_low_percentage" class="form-label">{{$t("bidding_low_percentage")}}(Least Bidding Level) 
                        <a href="" class="text-success" data-bs-toggle="modal" data-bs-target="#low">{{$t("how_it_works")}}</a>
                        <span class="text-danger">*</span>
                      </label>
                    <div class="input-group">
                      <div class="input-group-text">%</div>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_bidding_low_percentage')" id="bidding_low_percentage" 
                     v-model="form.bidding_low_percentage"
                      />
                      <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  </div> 
                  <div class="col-sm-12">
                    <div class="mb-3">
                      <label for="bidding_high_percentage" class="form-label">{{$t("bidding_high_percentage")}}(Highest Bidding Level)
                         <a href="" class="text-success" data-bs-toggle="modal" data-bs-target="#high">{{$t("how_it_works")}}</a>
                         <span class="text-danger">*</span>
                        </label>
                      <div class="input-group">
                        <div class="input-group-text">%</div>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_bidding_high_percentage')" id="bidding_high_percentage" 
                      v-model="form.bidding_high_percentage"
                      />
                      <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  </div> 
                  <div class="col-sm-12">
                    <div class="mb-3">
                      <label for="bidding_amount_increase_or_decrease" class="form-label">{{$t("increase_decrease_percentage_range")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_increase_decrease_percentage_range')" id="bidding_amount_increase_or_decrease" 
                      v-model="form.bidding_amount_increase_or_decrease"
                      />
                      <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>                                                                                                       
                  <div class="col-lg-12">
                    <div class="text-end">
                      <button type="submit" class="btn btn-primary"> {{ settings ? $t('update') : $t('save') }}</button>
                    </div>
                  </div>
                </div>  
              </FormValidation>
            </form>
          </BCardBody>
        </BCard>
      </BCol>
      <BCol lg="6">
        <BCard no-body id="tasksList">
          <BCardHeader class="border-0"></BCardHeader>
          <BCardBody class="border border-dashed border-end-0 border-start-0">
            <div class="col-sm-12">
                    <div class="mb-3" style="display: grid;place-items:center;">
                      <!-- <label for="owner_wallet_minimum_amount_to_get_an_order" class="form-label">Mobile View</label> -->
                      
                  <div class="wallet">
                    <div class="overlap">
                      <div class="card cards">
                        <div class="input-step d-flex align-items-center ">
                          <BButton variant="text-muted" class="minus hov">–{{ form.bidding_amount_increase_or_decrease  }}</BButton>
                          <input type="number" class="product-quantity" value="150" min="0" max="100" readonly />
                          <BButton variant="text-muted" class="plus hov">+{{ form.bidding_amount_increase_or_decrease  }}</BButton>
                        </div>
                        <div class="hstack gap-2 justify-content-center mt-4">
                            <button type="button" class="btn" style="background-color: #16ad70;color:white">Create Request</button>
                        </div>
                      </div>
                    </div>
                  </div>
                    </div>
                  </div>  
          </BCardBody>
        </BCard>
      </BCol>
    </BRow>

    <BRow>
      <BCol lg="6">  
        <BCard no-body id="tasksList">
          <BCardHeader class="border-0"><h4>{{$t("user")}}</h4></BCardHeader>
          <BCardBody class="border border-dashed border-end-0 border-start-0">
            <form @submit.prevent="handleSubmit">
              <FormValidation :form="form" :rules="validationRules" ref="validationRef">
                <div class="row"> 
                  <div class="col-sm-12">
                    <div class="mb-3">
                      <label for="user_bidding_low_percentage" class="form-label">{{$t("bidding_low_percentage")}}(Least Bidding Level) 
                        <a href="" class="text-success" data-bs-toggle="modal" data-bs-target="#low">{{$t("how_it_works")}}</a>
                        <span class="text-danger">*</span>
                      </label>
                    <div class="input-group">
                      <div class="input-group-text">%</div>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_bidding_low_percentage')" id="user_bidding_low_percentage" 
                     v-model="form.user_bidding_low_percentage"
                      />
                      <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  </div> 
                  <div class="col-sm-12">
                    <div class="mb-3">
                      <label for="user_bidding_high_percentage" class="form-label">{{$t("bidding_high_percentage")}}(Highest Bidding Level)
                         <a href="" class="text-success" data-bs-toggle="modal" data-bs-target="#high">{{$t("how_it_works")}}</a>
                         <span class="text-danger">*</span>
                        </label>
                      <div class="input-group">
                        <div class="input-group-text">%</div>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_bidding_high_percentage')" id="user_bidding_high_percentage" 
                      v-model="form.user_bidding_high_percentage"
                      />
                      <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  </div> 
                  <div class="col-sm-12">
                    <div class="mb-3">
                      <label for="user_bidding_amount_increase_or_decrease" class="form-label">{{$t("increase_decrease_percentage_range")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" :readonly="app_for === 'demo'" class="form-control" :placeholder="$t('enter_increase_decrease_percentage_range')" id="user_bidding_amount_increase_or_decrease" 
                      v-model="form.user_bidding_amount_increase_or_decrease"
                      />
                      <span v-for="(error, index) in errors.name" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>                                                                                                       
                  <div class="col-lg-12">
                    <div class="text-end">
                      <button type="submit" class="btn btn-primary"> {{ settings ? $t('update') : $t('save') }}</button>
                    </div>
                  </div>
                </div>  
              </FormValidation>
            </form>
          </BCardBody>
        </BCard>
      </BCol>
      <BCol lg="6">
        <BCard no-body id="tasksList">
          <BCardHeader class="border-0"></BCardHeader>
          <BCardBody class="border border-dashed border-end-0 border-start-0">
            <div class="col-sm-12">
                    <div class="mb-3" style="display: grid;place-items:center;">
                      <!-- <label for="owner_wallet_minimum_amount_to_get_an_order" class="form-label">Mobile View</label> -->
                      
                  <div class="wallet">
                    <div class="overlap">
                      <div class="card cards">
                        <div class="input-step user d-flex align-items-center ">
                          <BButton variant="text-muted" class="user-minus hov">–{{ form.user_bidding_amount_increase_or_decrease  }}</BButton>
                          <input type="number" class="product-quantity-user" value="150" min="0" max="100" readonly />
                          <BButton variant="text-muted" class="user-plus hov">+{{ form.user_bidding_amount_increase_or_decrease  }}</BButton>
                        </div>
                        <div class="hstack gap-2 justify-content-center mt-4">
                            <button type="button" class="btn" style="background-color: #16ad70;color:white">Create Request</button>
                        </div>
                      </div>
                    </div>
                  </div>
                    </div>
                  </div>  
          </BCardBody>
        </BCard>
      </BCol>
    </BRow>
    <div>
      <div v-if="successMessage" class="custom-alert alert alert-success alert-border-left fade show" role="alert"
        id="alertMsg">
        <div class="alert-content">
          <i class="ri-notification-off-line me-3 align-middle"></i>
          <strong>Success</strong> - {{ successMessage }}
          <button type="button" class="btn-close btn-close-success" @click="dismissMessage"
            aria-label="Close Success Message"></button>
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
    </div>
<!-- Low % Modals -->
<div id="low" class="modal fade" tabindex="-1" aria-labelledby="lowLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Least Bidding Amount</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <h5 class="fs-15">
                  Least Bidding Amount Calculation
                </h5>
                <p class="text-muted"> Recommended Price for ride = <strong>$150</strong> </p>
                <p class="text-muted"> {{$t("bidding_low_percentage")}} = <strong>50 %</strong></p>
                <p class="text-muted"> Recommended Price of {{$t("bidding_low_percentage")}} =<strong>150 of 50% = $75</strong> </p>
                <p class="text-muted"> Least Bidding Amount = <strong> Recommended Price for ride - Recommended Price  of {{$t("bidding_low_percentage")}}</strong></p>
                <p class="text-muted"> Least Bidding Amount = <strong> $150 - $75</strong>= <h6>$75</h6></p>
                <p class="text-muted"> Least Bidding Amount = <strong>$75</strong></p>
            </div>
            <!-- <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary ">Save Changes</button>
            </div> -->

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- High % Modals -->
<div id="high" class="modal fade" tabindex="-1" aria-labelledby="highLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Highest Bidding Amount</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <h5 class="fs-15">
                  Highest Bidding Amount Calculation
                </h5>
                <p class="text-muted"> Recommended Price for ride = <strong>$150</strong> </p>
                <p class="text-muted"> {{$t("bidding_high_percentage")}} = <strong>50 %</strong></p>
                <p class="text-muted"> Recommended Price of {{$t("bidding_high_percentage")}} =<strong>150 of 50% = $75</strong> </p>
                <p class="text-muted"> Highest Bidding Amount = <strong> Recommended Price for ride + Recommended Price  of {{$t("bidding_high_percentage")}}</strong></p>
                <p class="text-muted"> Highest Bidding Amount = <strong> $150 + $75</strong>= <h6>$225</h6></p>
                <p class="text-muted"> Highest Bidding Amount = <strong>$225</strong></p>
            </div>
            <!-- <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary ">Save Changes</button>
            </div> -->

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
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
.wallet{
  width: 250px;
  height: 296px;
  background-image: url(/images/bid.png);
  background-position: center 105%;
  background-repeat: no-repeat;
  background-size: cover;
}
.overlap{
  position: relative;
  left: 10px;
  padding: 0 15px;
  width: 280px;
  height: 296px;
  /* margin-top: -1; */
  /* background: #6464646b; */
  /* opacity: 1; */
}
.cards{
  position: relative;
    top: 160px;
    left: -25px;
    z-index: 2;
    width: 250px;
    height: 122px;
    padding:10px;
    border-radius: 15px 15px 0 0 ;
}

.rtl .cards{
  position: relative;
    top: 160px;
    left: 5px;
    z-index: 2;
    width: 250px;
    height: 123px;
    padding:10px;
    border-radius: 15px 15px 0 0 ;
}

.input-step button {
    width: 3.3em;
    font-weight: 300;
    height: 100%;
    line-height: 0.1em;
    font-size: 1em;
    padding: 0.2em !important;
    /* background: #89478b; */
    background: #16ad70;
    color: #ffffff;
    border: none;
    border-radius: var(--vz-border-radius);
    margin: 0 12px;
}

.input-step input {
    width: 6em;
    height: 100%;
    text-align: center;
    border: 0;
    /* background: #89478b2e; */
    background:#16ad705c;
    color: var(--vz-body-color);
    border-radius: var(--vz-border-radius);
}

.hov:hover {
    color: white;
    /* background-color: #cd74d0; */
    background-color:#50c596;
    border-color: var(--vz-btn-hover-border-color);
}

.rtl .input-step button {
    width: 3.3em;
    font-weight: 300;
    height: 100%;
    line-height: 0.1em;
    font-size: 1em;
    padding: 0.2em !important;
    background: #89478b;
    color: #ffffff;
    border: none;
    border-radius: var(--vz-border-radius);
    margin: 0 12px;
}

.rtl .input-step input {
    width: 6em;
    height: 100%;
    text-align: center;
    border: 0;
    background: #89478b2e;
    color: var(--vz-body-color);
    border-radius: var(--vz-border-radius);
}

.rtl .hov:hover {
    color: white;
    background-color: #cd74d0;
    border-color: var(--vz-btn-hover-border-color);
}
</style>
