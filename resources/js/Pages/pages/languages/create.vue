<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import { ref } from "vue";
import axios from "axios";
import { useI18n } from 'vue-i18n';
import Multiselect from "@vueform/multiselect";
import FormValidation from "@/Components/FormValidation.vue";

export default {
  components: {
    Layout,
    PageHeader,
    Head,
    Pagination,
    Multiselect,
    FormValidation
  },
  props: {
    successMessage: String,
    alertMessage: String,
    languagesList: Object,
    validate: Function, // Define the prop to receive the method
    
  },
  setup(props) {
    // console.log(props.languages);
    const form = useForm({
      code: props.languagesList ? props.languagesList.code || "" : "",
      direction: props.languagesList ? props.languagesList.direction || "" : "",
    });
    const { t } = useI18n();

    const validationRules = {
      code: { required: true },
      direction: { required: true }
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
      errors.value = validationRef.value.validate();
      if (Object.keys(errors.value).length > 0) {
        return;
      }
       try {
        let response;
        console.log("props.languagesList=="+form.data());
        if (props.languagesList && props.languagesList.id) {
          response = await axios.post(`/languages/update/${props.languagesList.id}`, form.data());
        } else {  
          response = await axios.post('/languages/store', form.data());
        }
        if (response.status === 201) {
          
          successMessage.value = t('language_added_successfully');
          
          // successMessage.value = 'tetetetete';
          
          form.reset();
          router.get('/languages');
        } else {
          alertMessage.value = t('failed_to_create_languages');
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          errors.value = error.response.data.errors;
        } else {
          console.error( t('error_creating_languages'), error);
          alertMessage.value = t('failed_to_create_languages_catch');
        }
      }

    };

    return {
      form,
      successMessage,
      alertMessage,
      handleSubmit,
      dismissMessage,
      validationRules,
      validationRef,
      errors,
      // languages:[],
  languages: [
    {
      "language": "Afrikaans",
      "code": "af"
    },
    {
      "language": "Albanian",
      "code": "sq"
    },
    {
      "language": "Amharic",
      "code": "am"
    },
    {
      "language": "Arabic",
      "code": "ar"
    },
    {
      "language": "Armenian",
      "code": "hy"
    },
    {
      "language": "Azerbaijani",
      "code": "az"
    },
    {
      "language": "Basque",
      "code": "eu"
    },
    {
      "language": "Belarusian",
      "code": "be"
    },
    {
      "language": "Bengali",
      "code": "bn"
    },
    {
      "language": "Bosnian",
      "code": "bs"
    },
    {
      "language": "Bulgarian",
      "code": "bg"
    },
    {
      "language": "Catalan",
      "code": "ca"
    },
    {
      "language": "Cebuano",
      "code": "ceb"
    },
    {
      "language": "Chinese (Simplified)",
      "code": "zh-CN"
    },
    {
      "language": "Chinese (Traditional)",
      "code": "zh-TW"
    },
    {
      "language": "Corsican",
      "code": "co"
    },
    {
      "language": "Croatian",
      "code": "hr"
    },
    {
      "language": "Czech",
      "code": "cs"
    },
    {
      "language": "Danish",
      "code": "da"
    },
    {
      "language": "Dutch",
      "code": "nl"
    },
    {
      "language": "English",
      "code": "en"
    },
    {
      "language": "Esperanto",
      "code": "eo"
    },
    {
      "language": "Estonian",
      "code": "et"
    },
    {
      "language": "Finnish",
      "code": "fi"
    },
    {
      "language": "French",
      "code": "fr"
    },
    {
      "language": "Frisian",
      "code": "fy"
    },
    {
      "language": "Galician",
      "code": "gl"
    },
    {
      "language": "Georgian",
      "code": "ka"
    },
    {
      "language": "German",
      "code": "de"
    },
    {
      "language": "Greek",
      "code": "el"
    },
    {
      "language": "Gujarati",
      "code": "gu"
    },
    {
      "language": "Haitian Creole",
      "code": "ht"
    },
    {
      "language": "Hausa",
      "code": "ha"
    },
    {
      "language": "Hawaiian",
      "code": "haw"
    },
    {
      "language": "Hebrew",
      "code": "iw"
    },
    {
      "language": "Hindi",
      "code": "hi"
    },
    {
      "language": "Hmong",
      "code": "hmn"
    },
    {
      "language": "Hungarian",
      "code": "hu"
    },
    {
      "language": "Icelandic",
      "code": "is"
    },
    {
      "language": "Igbo",
      "code": "ig"
    },
    {
      "language": "Indonesian",
      "code": "id"
    },
    {
      "language": "Irish",
      "code": "ga"
    },
    {
      "language": "Italian",
      "code": "it"
    },
    {
      "language": "Japanese",
      "code": "ja"
    },
    {
      "language": "Javanese",
      "code": "jw"
    },
    {
      "language": "Kannada",
      "code": "kn"
    },
    {
      "language": "Kazakh",
      "code": "kk"
    },
    {
      "language": "Khmer",
      "code": "km"
    },
    {
      "language": "Korean",
      "code": "ko"
    },
    {
      "language": "Kurdish",
      "code": "ku"
    },
    {
      "language": "Kyrgyz",
      "code": "ky"
    },
    {
      "language": "Lao",
      "code": "lo"
    },
    {
      "language": "Latin",
      "code": "la"
    },
    {
      "language": "Latvian",
      "code": "lv"
    },
    {
      "language": "Lithuanian",
      "code": "lt"
    },
    {
      "language": "Luxembourgish",
      "code": "lb"
    },
    {
      "language": "Macedonian",
      "code": "mk"
    },
    {
      "language": "Malagasy",
      "code": "mg"
    },
    {
      "language": "Malay",
      "code": "ms"
    },
    {
      "language": "Malayalam",
      "code": "ml"
    },
    {
      "language": "Maltese",
      "code": "mt"
    },
    {
      "language": "Maori",
      "code": "mi"
    },
    {
      "language": "Marathi",
      "code": "mr"
    },
    {
      "language": "Mongolian",
      "code": "mn"
    },
    {
      "language": "Myanmar (Burmese)",
      "code": "my"
    },
    {
      "language": "Nepali",
      "code": "ne"
    },
    {
      "language": "Norwegian",
      "code": "no"
    },
    {
      "language": "Nyanja (Chichewa)",
      "code": "ny"
    },
    {
      "language": "Pashto",
      "code": "ps"
    },
    {
      "language": "Persian",
      "code": "fa"
    },
    {
      "language": "Polish",
      "code": "pl"
    },
    {
      "language": "Portuguese (Portugal, Brazil)",
      "code": "pt"
    },
    {
      "language": "Punjabi",
      "code": "pa"
    },
    {
      "language": "Romanian",
      "code": "ro"
    },
    {
      "language": "Russian",
      "code": "ru"
    },
    {
      "language": "Samoan",
      "code": "sm"
    },
    {
      "language": "Scots Gaelic",
      "code": "gd"
    },
    {
      "language": "Serbian",
      "code": "sr"
    },
    {
      "language": "Sesotho",
      "code": "st"
    },
    {
      "language": "Shona",
      "code": "sn"
    },
    {
      "language": "Sindhi",
      "code": "sd"
    },
    {
      "language": "Sinhala (Sinhalese)",
      "code": "si"
    },
    {
      "language": "Slovak",
      "code": "sk"
    },
    {
      "language": "Slovenian",
      "code": "sl"
    },
    {
      "language": "Somali",
      "code": "so"
    },
    {
      "language": "Spanish",
      "code": "es"
    },
    {
      "language": "Sundanese",
      "code": "su"
    },
    {
      "language": "Swahili",
      "code": "sw"
    },
    {
      "language": "Swedish",
      "code": "sv"
    },
    {
      "language": "Tagalog (Filipino)",
      "code": "tl"
    },
    {
      "language": "Tajik",
      "code": "tg"
    },
    {
      "language": "Tamil",
      "code": "ta"
    },
    {
      "language": "Telugu",
      "code": "te"
    },
    {
      "language": "Thai",
      "code": "th"
    },
    {
      "language": "Turkish",
      "code": "tr"
    },
    {
      "language": "Ukrainian",
      "code": "uk"
    },
    {
      "language": "Urdu",
      "code": "ur"
    },
    {
      "language": "Uzbek",
      "code": "uz"
    },
    {
      "language": "Vietnamese",
      "code": "vi"
    },
    {
      "language": "Welsh",
      "code": "cy"
    },
    {
      "language": "Xhosa",
      "code": "xh"
    },
    {
      "language": "Yiddish",
      "code": "yi"
    },
    {
      "language": "Yoruba",
      "code": "yo"
    },
    {
      "language": "Zulu",
      "code": "zu"
    }
  ],
  "tts": [
    {
      "language": "Afrikaans (South Africa)",
      "code": "af-ZA"
    },
    {
      "language": "Amharic (Ethiopia)",
      "code": "am-ET"
    },
    {
      "language": "Armenian (Armenia)",
      "code": "hy-AM"
    },
    {
      "language": "Azerbaijani (Azerbaijan)",
      "code": "az-AZ"
    },
    {
      "language": "Indonesian (Indonesia)",
      "code": "id-ID"
    },
    {
      "language": "Malay (Malaysia)",
      "code": "ms-MY"
    },
    {
      "language": "Bengali (Bangladesh)",
      "code": "bn-BD"
    },
    {
      "language": "Bengali (India)",
      "code": "bn-IN"
    },
    {
      "language": "Catalan (Spain)",
      "code": "ca-ES"
    },
    {
      "language": "Czech (Czech Republic)",
      "code": "cs-CZ"
    },
    {
      "language": "Danish (Denmark)",
      "code": "da-DK"
    },
    {
      "language": "German (Germany)",
      "code": "de-DE"
    },
    {
      "language": "English (Australia)",
      "code": "en-AU"
    },
    {
      "language": "English (Canada)",
      "code": "en-CA"
    },
    {
      "language": "English (Ghana)",
      "code": "en-GH"
    },
    {
      "language": "English (United Kingdom)",
      "code": "en-GB"
    },
    {
      "language": "English (India)",
      "code": "en-IN"
    },
    {
      "language": "English (Ireland)",
      "code": "en-IE"
    },
    {
      "language": "English (Kenya)",
      "code": "en-KE"
    },
    {
      "language": "English (New Zealand)",
      "code": "en-NZ"
    },
    {
      "language": "English (Nigeria)",
      "code": "en-NG"
    },
    {
      "language": "English (Philippines)",
      "code": "en-PH"
    },
    {
      "language": "English (Singapore)",
      "code": "en-SG"
    },
    {
      "language": "English (South Africa)",
      "code": "en-ZA"
    },
    {
      "language": "English (Tanzania)",
      "code": "en-TZ"
    },
    {
      "language": "English (United States)",
      "code": "en-US"
    },
    {
      "language": "Spanish (Argentina)",
      "code": "es-AR"
    },
    {
      "language": "Spanish (Bolivia)",
      "code": "es-BO"
    },
    {
      "language": "Spanish (Chile)",
      "code": "es-CL"
    },
    {
      "language": "Spanish (Colombia)",
      "code": "es-CO"
    },
    {
      "language": "Spanish (Costa Rica)",
      "code": "es-CR"
    },
    {
      "language": "Spanish (Ecuador)",
      "code": "es-EC"
    },
    {
      "language": "Spanish (El Salvador)",
      "code": "es-SV"
    },
    {
      "language": "Spanish (Spain)",
      "code": "es-ES"
    },
    {
      "language": "Spanish (United States)",
      "code": "es-US"
    },
    {
      "language": "Spanish (Guatemala)",
      "code": "es-GT"
    },
    {
      "language": "Spanish (Honduras)",
      "code": "es-HN"
    },
    {
      "language": "Spanish (Mexico)",
      "code": "es-MX"
    },
    {
      "language": "Spanish (Nicaragua)",
      "code": "es-NI"
    },
    {
      "language": "Spanish (Panama)",
      "code": "es-PA"
    },
    {
      "language": "Spanish (Paraguay)",
      "code": "es-PY"
    },
    {
      "language": "Spanish (Peru)",
      "code": "es-PE"
    },
    {
      "language": "Spanish (Puerto Rico)",
      "code": "es-PR"
    },
    {
      "language": "Spanish (Dominican Republic)",
      "code": "es-DO"
    },
    {
      "language": "Spanish (Uruguay)",
      "code": "es-UY"
    },
    {
      "language": "Spanish (Venezuela)",
      "code": "es-VE"
    },
    {
      "language": "Basque (Spain)",
      "code": "eu-ES"
    },
    {
      "language": "Filipino (Philippines)",
      "code": "fil-PH"
    },
    {
      "language": "French (Canada)",
      "code": "fr-CA"
    },
    {
      "language": "French (France)",
      "code": "fr-FR"
    },
    {
      "language": "Galician (Spain)",
      "code": "gl-ES"
    },
    {
      "language": "Georgian (Georgia)",
      "code": "ka-GE"
    },
    {
      "language": "Gujarati (India)",
      "code": "gu-IN"
    },
    {
      "language": "Croatian (Croatia)",
      "code": "hr-HR"
    },
    {
      "language": "Zulu (South Africa)",
      "code": "zu-ZA"
    },
    {
      "language": "Icelandic (Iceland)",
      "code": "is-IS"
    },
    {
      "language": "Italian (Italy)",
      "code": "it-IT"
    },
    {
      "language": "Javanese (Indonesia)",
      "code": "jv-ID"
    },
    {
      "language": "Kannada (India)",
      "code": "kn-IN"
    },
    {
      "language": "Khmer (Cambodia)",
      "code": "km-KH"
    },
    {
      "language": "Lao (Laos)",
      "code": "lo-LA"
    },
    {
      "language": "Latvian (Latvia)",
      "code": "lv-LV"
    },
    {
      "language": "Lithuanian (Lithuania)",
      "code": "lt-LT"
    },
    {
      "language": "Hungarian (Hungary)",
      "code": "hu-HU"
    },
    {
      "language": "Malayalam (India)",
      "code": "ml-IN"
    },
    {
      "language": "Marathi (India)",
      "code": "mr-IN"
    },
    {
      "language": "Dutch (Netherlands)",
      "code": "nl-NL"
    },
    {
      "language": "Nepali (Nepal)",
      "code": "ne-NP"
    },
    {
      "language": "Norwegian Bokmål (Norway)",
      "code": "nb-NO"
    },
    {
      "language": "Polish (Poland)",
      "code": "pl-PL"
    },
    {
      "language": "Portuguese (Brazil)",
      "code": "pt-BR"
    },
    {
      "language": "Portuguese (Portugal)",
      "code": "pt-PT"
    },
    {
      "language": "Romanian (Romania)",
      "code": "ro-RO"
    },
    {
      "language": "Sinhala (Sri Lanka)",
      "code": "si-LK"
    },
    {
      "language": "Slovak (Slovakia)",
      "code": "sk-SK"
    },
    {
      "language": "Slovenian (Slovenia)",
      "code": "sl-SI"
    },
    {
      "language": "Sundanese (Indonesia)",
      "code": "su-ID"
    },
    {
      "language": "Swahili (Tanzania)",
      "code": "sw-TZ"
    },
    {
      "language": "Swahili (Kenya)",
      "code": "sw-KE"
    },
    {
      "language": "Finnish (Finland)",
      "code": "fi-FI"
    },
    {
      "language": "Swedish (Sweden)",
      "code": "sv-SE"
    },
    {
      "language": "Tamil (India)",
      "code": "ta-IN"
    },
    {
      "language": "Tamil (Singapore)",
      "code": "ta-SG"
    },
    {
      "language": "Tamil (Sri Lanka)",
      "code": "ta-LK"
    },
    {
      "language": "Tamil (Malaysia)",
      "code": "ta-MY"
    },
    {
      "language": "Telugu (India)",
      "code": "te-IN"
    },
    {
      "language": "Vietnamese (Vietnam)",
      "code": "vi-VN"
    },
    {
      "language": "Turkish (Turkey)",
      "code": "tr-TR"
    },
    {
      "language": "Urdu (Pakistan)",
      "code": "ur-PK"
    },
    {
      "language": "Urdu (India)",
      "code": "ur-IN"
    },
    {
      "language": "Greek (Greece)",
      "code": "el-GR"
    },
    {
      "language": "Bulgarian (Bulgaria)",
      "code": "bg-BG"
    },
    {
      "language": "Russian (Russia)",
      "code": "ru-RU"
    },
    {
      "language": "Serbian (Serbia)",
      "code": "sr-RS"
    },
    {
      "language": "Ukrainian (Ukraine)",
      "code": "uk-UA"
    },
    {
      "language": "Hebrew (Israel)",
      "code": "he-IL"
    },
    {
      "language": "Arabic (Israel)",
      "code": "ar-IL"
    },
    {
      "language": "Arabic (Jordan)",
      "code": "ar-JO"
    },
    {
      "language": "Arabic (United Arab Emirates)",
      "code": "ar-AE"
    },
    {
      "language": "Arabic (Bahrain)",
      "code": "ar-BH"
    },
    {
      "language": "Arabic (Algeria)",
      "code": "ar-DZ"
    },
    {
      "language": "Arabic (Saudi Arabia)",
      "code": "ar-SA"
    },
    {
      "language": "Arabic (Iraq)",
      "code": "ar-IQ"
    },
    {
      "language": "Arabic (Kuwait)",
      "code": "ar-KW"
    },
    {
      "language": "Arabic (Morocco)",
      "code": "ar-MA"
    },
    {
      "language": "Arabic (Tunisia)",
      "code": "ar-TN"
    },
    {
      "language": "Arabic (Oman)",
      "code": "ar-OM"
    },
    {
      "language": "Arabic (State of Palestine)",
      "code": "ar-PS"
    },
    {
      "language": "Arabic (Qatar)",
      "code": "ar-QA"
    },
    {
      "language": "Arabic (Lebanon)",
      "code": "ar-LB"
    },
    {
      "language": "Arabic (Egypt)",
      "code": "ar-EG"
    },
    {
      "language": "Persian (Iran)",
      "code": "fa-IR"
    },
    {
      "language": "Hindi (India)",
      "code": "hi-IN"
    },
    {
      "language": "Thai (Thailand)",
      "code": "th-TH"
    },
    {
      "language": "Korean (South Korea)",
      "code": "ko-KR"
    },
    {
      "language": "Chinese, Mandarin (Traditional, Taiwan)",
      "code": "zh-TW"
    },
    {
      "language": "Chinese, Cantonese (Traditional, Hong Kong)",
      "code": "yue-Hant-HK"
    },
    {
      "language": "Japanese (Japan)",
      "code": "ja-JP"
    },
    {
      "language": "Chinese, Mandarin (Simplified, Hong Kong)",
      "code": "zh-HK"
    },
    {
      "language": "Chinese, Mandarin (Simplified, China)",
      "code": "zh"
    }
  ]

    };
    
  },
  // mounted() {
  //   this.selectLanguages();
  // },
  // methods: {
  //   async selectLanguages() {       
  //     try {
  //        this.loading = true
  //           var url = "/languages/languagesList"
  //       // console.log("calls")
  //       console.log(url)
  //       const response = await axios.get(url);
  //       this.languages = response.data.languages;
  //       console.log( response.data);      
  //     } catch (error) {
  //       console.error("Error fetching languages:", error);
  //     }
  //   }
  // }
  
};
</script>

<template>
  <Layout>

    <Head title="Languages" />
    <PageHeader :title="languagesList ? $t('edit') : $t('create')" :pageTitle="$t('languages')"  pageLink="/languages"/>
    <BRow>
      <BCol lg="12">
        <BCard no-body id="tasksList">
          <BCardHeader class="border-0"></BCardHeader>
          <BCardBody class="border border-dashed border-end-0 border-start-0">

            <form @submit.prevent="handleSubmit">
              <FormValidation :form="form" :rules="validationRules" ref="validationRef">
                <div class="row">
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="select_languages" class="form-label">{{$t("select_languages")}}
                        <span class="text-danger">*</span>
                      </label>
                      <select id="select_languages" class="form-select" v-model="form.code">
                        <option disabled value="">{{$t("choose_languages")}}</option>                        
                        <option v-for="Languagevalue in languages" :value="Languagevalue.code +',' + Languagevalue.language">
                            {{Languagevalue.language}}
                        </option>
                      </select>
                      <span v-for="(error, index) in errors.code" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="select_language_direction" class="form-label">{{$t("select_language_direction")}}
                        <span class="text-danger">*</span>
                      </label>
                      <select id="select_language_direction" class="form-select" v-model="form.direction">
                        <option disabled value="">{{$t("choose_language_direction")}}</option>
                        <option value="ltr">{{$t("ltr")}}</option>
                        <option value="rtl">{{$t("rtl")}}</option>
                      </select>
                      <span v-for="(error, index) in errors.direction" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-lg-12">
                    <div class="text-end">
                      <button type="submit" class="btn btn-primary">{{$t("save")}}</button>
                    </div>
                  </div>              
                </div>
              </FormValidation>
            </form>
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
</style>
	