<template>
    <div class="auth-page-wrapper overflow-hidden" style="height: 100vh;">
      <div class="auth-page-content overflow-hidden">
        <BContainerFluid>
          <BRow>
            <BCol lg="12">
              <BRow class="g-0" style="height: 100vh;">
                <BCol lg="4">
                  <div class="p-lg-5 p-4">
                    <div class="mt-4 text-center">
                      <img :src="logo" alt="" height="30" />
                    </div>
                    <div class="text-center mt-5">
                      <h5 class="text-primary">{{$t("enter_your_mobile_number")}}</h5>
                      <p class="text-muted">{{$t("otp_will_be_sent_on_sms")}}</p>
                    </div>
  
                    <div class="mt-4">
                      <!-- Step 1: Enter mobile number -->
                      <form @submit.prevent="validateMobile">
                        <div class="mb-3">
                        <div class="input-group" data-input-flag="">
                          <button
                            class="btn btn-light border"
                            type="button"
                            @click="toggleDropdown"
                          >
                            <img
                              :src="selectedCountry.flag"
                              alt="flag"
                              height="20"
                              class="country-flagimg rounded"
                            />
                            <span class="ms-2 country-codeno">{{ selectedCountry.dial_code }}</span>
                          </button>
                          <input
                            type="text"
                            v-model="phoneNumber"
                            class="form-control"
                            id="number"
                            :placeholder="$t('enter_mobile')"
                            required
                          />
                        </div>

                        <div
                          class="dropdown-menu w-60 mt-0" v-if="showDropdown"
                          :class="{ 'show': showDropdown }" 
                          
                        >
                          <div class="p-2 px-3 pt-1 searchlist-input">
                            <input
                              type="text"
                              class="form-control form-control-sm border search-countryList"
                              :placeholder="$t('search_country_name_or_country_code')"
                              v-model="searchQuery"
                              @click.stop
                            />
                          </div>
                          <ul class="list-unstyled dropdown-menu-list mb-0">
                            <li
                              v-for="country in filteredCountries"
                              :key="country.id"
                            >
                              <a
                                href="javascript:void(0);"
                                class="dropdown-item notify-item language py-2"
                                @click="selectCountry(country)"
                              >
                                <img
                                  :src="country.flag"
                                  alt="flag"
                                  class="me-2 rounded"
                                  height="18"
                                />
                                <span class="align-middle">{{ country.name }} {{ country.dial_code }}</span>
                              </a>
                            </li>
                          </ul>
                        </div>
                      </div>
  
                        <div v-if="!showOTPInput">
                          <div v-if="mobileExists">
                            <!-- Show password field if mobile is already registered -->
                            <div class="mb-3">
                              <label for="password" class="form-label">{{$t("password")}}</label>
                              <input
                                type="password"
                                v-model="password"
                                 :class="{'form-control is-invalid': passwordError, 'form-control': !passwordError}"
                                id="password"
                                :placeholder="$t('enter_password')"
                              />
                              <div v-if="passwordError" class="invalid-feedback">
                                {{ passwordErrorMessage }}
                              </div>
                            </div>
  
                            <div class="mt-4">
                              <BButton variant="primary" class="w-100" @click="loginWithPassword">{{$t("login")}}</BButton>
                            </div>
  
                            <div class="mt-3 text-center">
                              <span>or</span>
                              <BButton variant="link" @click="sendOTP">{{$t("sign_in_with_otp")}}</BButton>
                            </div>
                          </div>
  
                          <div v-else>
                            <!-- If mobile doesn't exist, directly send OTP -->
                            <BButton variant="success" class="w-100" type="submit">{{$t("send_otp_for_registration")}}</BButton>
                          </div>
                        </div>
                        <div v-if="showOTPInput">
                        <!-- Step 2: Enter OTP -->
                        <label for="otp" class="form-label">{{$t("enter_otp")}}</label>
                        <p v-if="app_for == 'demo'" class="text-muted">{{$t("enter_default_otp_123456")}}</p>
                        <div class="d-flex otp-input-container">
                        <input
                        type="number"
                        v-model="otpDigits[0]"
                        class="form-control"
                        :class="{'is-invalid': otpError}"
                        maxlength="1"
                        @input="moveFocus($event, 0)"
                        @keydown="handleKeydown($event, 0)"
                        />
                        <input
                        type="number"
                        v-model="otpDigits[1]"
                        class="form-control"
                        :class="{'is-invalid': otpError}"
                        maxlength="1"
                        @input="moveFocus($event, 1)"
                        @keydown="handleKeydown($event, 1)"

                        />
                        <input
                        type="number"
                        v-model="otpDigits[2]"
                        class="form-control"
                        :class="{'is-invalid': otpError}"
                        maxlength="1"
                        @input="moveFocus($event, 2)"
                        @keydown="handleKeydown($event, 2)"

                        />
                        <input
                        type="number"
                        v-model="otpDigits[3]"
                        class="form-control"
                        :class="{'is-invalid': otpError}"
                        maxlength="1"
                        @input="moveFocus($event, 3)"
                        @keydown="handleKeydown($event, 3)"

                        />
                        <input
                        type="number"
                        v-model="otpDigits[4]"
                        class="form-control"
                        :class="{'is-invalid': otpError}"
                        maxlength="1"
                        @input="moveFocus($event, 4)"
                        @keydown="handleKeydown($event, 4)"

                        />
                        <input
                        type="number"
                        v-model="otpDigits[5]"
                        class="form-control"
                        :class="{'is-invalid': otpError}"
                        maxlength="1"
                        @input="moveFocus($event, 5)"
                        @keydown="handleKeydown($event, 5)"

                        />
                        </div>
                        <div v-if="otpError" class="invalid-feedback d-block">
                        {{ otpErrorMessage }}
                        </div>
                        <BButton variant="primary" class="w-100 mt-3" @click="verifyOTP">{{$t("verify_otp")}}</BButton>
                        </div>
  
                        <div id="recaptcha-container"></div>
                      </form>
                    </div>
                  </div>
                </BCol>
  
                <BCol lg="8">
                  <div
                    class="p-lg-5 p-4 h-100"
                    style="background: url(/images/TAXI.png);background-position: center;background-size: cover;background-repeat: no-repeat;"
                  ></div>
                </BCol>
              </BRow>
            </BCol>
          </BRow>
        </BContainerFluid>
      </div>
    </div>
  </template>
  
  <script>
  import { useForm, router } from '@inertiajs/vue3';
  import { getAuth, RecaptchaVerifier, signInWithPhoneNumber, PhoneAuthProvider, signInWithCredential } from 'firebase/auth';
  import axios from 'axios';
  import { initializeApp } from "firebase/app";
  import { initI18n } from '@/i18n';
  import { useI18n } from 'vue-i18n';
  import { ref, computed , onMounted, onUnmounted } from "vue";

  // Firebase configuration

  let app = null;

  export default {
    data() {
      return {
        phoneNumber: '',
        country: '',
        password: '',
        verificationCode: '',
        mobileExists: false,
        showOTPInput: false,
        verificationId: null,
        otpError: false,
        otpErrorMessage: 'The OTP provided is invalid',
        passwordError: false,
        passwordErrorMessage: '',
        recaptchaVerifier: null,
        otpDigits: ['', '', '', '', '', ''],
        logo: window.logo,
      };
    },
    props: {
      countries: Array,
      default_flag: String,
      default_dial_code: String,
      app_for: String,
      successMessage: String,
      alertMessage: String,
      enable_firebase_otp: Boolean,
      firebaseConfig: Object,
      default_country_id : String,
    },
    setup(props) {

      const { t } = useI18n();
      const filteredCountries = computed(() => {
        return props.countries.filter((country) =>
          country.name.toLowerCase().includes(searchQuery.value.toLowerCase())
        );
      });

      const searchQuery = ref('');
      const showDropdown = ref(false);
      const selectedCountry = ref({
        dial_code: props.default_dial_code || '',
        flag: props.default_flag || '',
        id: props.default_country_id || ''
      });

      const selectCountry = (country) => {
        selectedCountry.value = country;
        showDropdown.value = false;
      };

      const toggleDropdown = () => {
        showDropdown.value = !showDropdown.value;
      };

     

      const handleClickOutside = (event) => {
        if (event.target.closest('.input-group') === null) {
          showDropdown.value = false;
        }
      };

      onMounted(async() => {
        await initI18n('en');
      })
      onMounted(() => {
        const firebaseConfig = props.firebaseConfig;
        if (!firebase.apps.length) {
            app = initializeApp(firebaseConfig);
        }
        document.addEventListener('click', handleClickOutside);
      });

      onUnmounted(() => {
        document.removeEventListener('click', handleClickOutside);
      });

      return {
        t,
        selectCountry,
        selectedCountry,
        filteredCountries,
        searchQuery,
        showDropdown,
        toggleDropdown
      };
    },
    methods: {
      // Call API to check if the mobile is registered
      async validateMobile() {
        try {
          
          const response = await axios.post('/api/v1/user/validate-mobile-for-login', { mobile: this.phoneNumber});
          if (response.data.success && response.data.message === 'mobile_exists') {
            this.mobileExists = true;
          }
          if (this.mobileExists) {
            // If mobile exists, ask for password or OTP
          } else {
            // If mobile does not exist, trigger OTP for registration
            this.sendOTP();
          }
        } catch (error) {
          console.error(this.t('error_validating_mobile'), error);
        }
      },

      // Send OTP
      async sendOTP() {
        if(this.app_for == 'demo') {
          this.showOTPInput = true;
        }
        if (this.enable_firebase_otp) {
          this.sendFirebaseOTP();
        } else {
          try {
            const response = await axios.post('/api/v1/mobile-otp', { mobile: this.phoneNumber,country_code:this.selectedCountry.dial_code  });
            if (response.status == 200) {
              this.showOTPInput = true;
            }
          } catch (error) {
            console.error(this.t('error_sending_otp'), error);
          }
        }
      },

      // Send OTP via Firebase
      async sendFirebaseOTP() {
        const auth = getAuth(app);
        this.recaptchaVerifier = new RecaptchaVerifier(auth, 'recaptcha-container', { size: 'invisible' }, auth);

        try {
          const confirmationResult = await signInWithPhoneNumber(auth, `${this.selectedCountry.dial_code}${this.phoneNumber}`, this.recaptchaVerifier);
          this.verificationId = confirmationResult.verificationId;
          this.showOTPInput = true;
        } catch (error) {
          console.error(this.t('error_sending_firebase_otp'), error);
        }
      },

      handleKeydown(event, index) {
      if (event.key === 'Backspace' || event.key === 'Delete') {
        if (this.otpDigits[index] === '') {
          // Move focus to previous input if current input is empty
          if (index > 0) {
            event.target.previousElementSibling?.focus();
          }
        } else {
          // Clear the current input if not empty
          this.otpDigits[index] = '';
        }
      }
    },
    // Method to move focus to next input
    moveFocus(event, index) {
       // Ensure only one digit is allowed per input
       const value = event.target.value;
      if (/^\d$/.test(value)) {
        this.otpDigits[index] = value; // Set the value if it's a digit
        if (index < 5) {
          event.target.nextElementSibling?.focus(); // Move to the next input
        }
      } else {
        this.otpDigits[index] = ''; // Clear the value if it's not a digit
      }
    },
      // Verify OTP
    async verifyOTP() {
        this.verificationCode = this.otpDigits.join('');
      if (this.verificationCode.length === 6) {
        // Proceed with OTP validation
        // Assuming validation fails, you can set otpError to true
        this.otpError = false; // Set to true if OTP is invalid
      } else {
        this.otpError = true;
      }
      if(this.enable_firebase_otp && this.app_for !== 'demo'){
        await this.verifyFirebaseOTP();
      }else{
      try {
        const response = await axios.post('/api/v1/validate-otp', {
          mobile: this.phoneNumber,
          otp: this.verificationCode,
        });
        if (response.data.success) {
          // Handle successful OTP verification
          this.otpError = false;
          this.otpErrorMessage = '';
          // Proceed with login or registration
          if (this.mobileExists) {
                await this.loginWithOTP();
              } else {
                await this.registerUser();
              }
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          
          console.log("error-validation");

          this.otpError = true;
          this.otpErrorMessage = error.response.data.errors.message;
        } else {
          console.error(this.t('error_verifying_otp'), error);
        }
      }
      }
    },

      // Verify OTP via Firebase
      async verifyFirebaseOTP() {
        this.verificationCode = this.otpDigits.join('');
        const auth = getAuth(app);
        const credential = PhoneAuthProvider.credential(this.verificationId, this.verificationCode);

        try {
          await signInWithCredential(auth, credential);

          if (this.mobileExists) {
            await this.loginWithOTP();
          } else {
            await this.registerUser();
          }
        } catch (error) {
          if (error.response && error.response.status === 422) {
            
            console.log("error-validation");

            this.otpError = true;
            this.otpErrorMessage = error.response.data.errors.message;
          } else {
            console.error(this.t('error_verifying_otp'), error);
          }
        }
      },

      // Login with password
      async loginWithPassword() {
        try {
          const response = await axios.post('/user/login', {
            mobile: this.phoneNumber,
            password: this.password,
          });
  
          if (response.status === 200 && response.data.success) {
            router.get('/create-booking');
          }
        } catch (error) {

          if (error.response && error.response.status === 422) {
          this.passwordError = true;
          this.passwordErrorMessage = error.response.data.errors.mobile[0];
        } else {
          console.error(t('error_verifying_otp'), error);
        }

          console.error(t('error_logging_in'), error);
        }
      },

      // Login with OTP
      async loginWithOTP() {
        try {
          const response = await axios.post('/user/login', { mobile: this.phoneNumber });
          if (response.data.success) {
            router.get('/create-booking');
          }
        } catch (error) {
          console.error(t('error_logging_in_with_otp'), error);
        }
      },

      // Register new user after OTP verification
      async registerUser() {
        try {
          const response = await axios.post('/user/register', { mobile: this.phoneNumber,country:this.selectedCountry.id  });
          if (response.status === 200 && response.data.success) {
            router.get('/create-booking');
          }
        } catch (error) {
          console.error(t('error_registering_user'), error);
        }
      },
    },
  };
</script>

  <style>
  .dropdown-menu.show {
  display: block !important; /* Ensure dropdown menu is shown */
  overflow: auto;
  height: 50vh;
}
.is-invalid {
  border-color: red;
}
.invalid-feedback {
  color: red;
  font-size: 0.875em;
}
.otp-input-container {
  display: flex;
  justify-content: space-between;
}

</style>