<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Pagination from "@/Components/Pagination.vue";
import { ref,onMounted } from "vue";
import axios from "axios";
import { useSharedState } from '@/composables/useSharedState'; // Import the composable
import Multiselect from "@vueform/multiselect";
import FormValidation from "@/Components/FormValidation.vue";
import { useI18n } from 'vue-i18n';

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
    activeTab: String,
    languages: Array,
    successMessage: String,
    alertMessage: String,
    goodsType: Object,
    validate: Function,
  },
  setup(props) {
    const { t } = useI18n();
    const { languages, fetchData } = useSharedState(); // Destructure the shared state
    const activeTab = ref('English');
    const form = useForm({
      languageFields:  props.goodsType ? props.goodsType.languageFields || {} : {}, // To hold data from the Tab component
      // goods_type_name: props.goodsType ? props.goodsType.goods_type_name || "" : "",
      goods_types_for: props.goodsType ? props.goodsType.goods_types_for || "" : "",
    });

    const validationRules = {
      // goods_type_name: { required: true },
      goods_types_for: { required: true },
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
        if (props.goodsType && props.goodsType.id) {
          response = await axios.post(`/goods-type/update/${props.goodsType.id}`, form.data());
        } else {
          response = await axios.post('store', form.data());
        }
        if (response.status === 201) {
          successMessage.value = t('goods_type_created_successfully');
          form.reset();
          router.get('/goods-type');
        } else {
          alertMessage.value = t('failed_to_create_goods_type');
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          errors.value = error.response.data.errors;
        } else if (error.response && error.response.status === 403) {
          alertMessage.value = error.response.data.alertMessage;
          setTimeout(()=>{
            router.get('/goods-type');
          },5000)
        } else {
          console.error(t('error_creating_goods_type'), error);
          alertMessage.value = t('failed_to_create_goods_type');
        }
      }
    };

    const setActiveTab = (tab) => {
      activeTab.value = tab;
    }
    onMounted(async () => {
      if (Object.keys(languages).length == 0) {
        await fetchData();
      }
    });

    return {
      form,
      successMessage,
      alertMessage,
      handleSubmit,
      dismissMessage,
      validationRules,
      setActiveTab,
      activeTab,
      languages,
      validationRef,
      errors
    };
  }
};

</script>

<template>
  <Layout>
    <Head title="Goods Type" />
    <PageHeader :title="goodsType ? $t('edit') : $t('create')" :pageTitle="$t('goods_type')" pageLink="/goods-type"/>
    <BRow>
      <BCol lg="12">
        <BCard no-body id="tasksList">
          <BCardHeader class="border-0"></BCardHeader>
          <BCardBody class="border border-dashed border-end-0 border-start-0">
            <form @submit.prevent="handleSubmit">
              <FormValidation :form="form" :rules="validationRules" ref="validationRef">
                <div class="row">
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
                          <input type="text" class="form-control" :placeholder="$t('enter_name_in', {language: `${language.label}`})"
                            :id="`name-${language.code}`" v-model="form.languageFields[language.code]"
                            :required="language.code === 'en'">
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="goods_types_for" class="form-label">{{$t("goods_type_for")}}
                        <span class="text-danger">*</span>
                      </label>
                      <select id="goods_types_for" class="form-select" v-model="form.goods_types_for">
                        <option disabled value="">{{$t("select")}}</option>
                        <option value="truck">{{$t("truck")}}</option>
                        <option value="motor_bike">{{$t("motor_bike")}}</option>
                        <option value="both">{{$t("all")}}</option>
                      </select>
                      <span v-for="(error, index) in errors.goods_types_for" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-lg-12">
                    <div class="text-end">
                      <button type="submit" class="btn btn-primary">{{ goodsType ? $t('update') : $t('save') }}</button>
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
