<script>
import { Head, useForm, router } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import { ref } from "vue";
import axios from "axios";
import FormValidation from "@/Components/FormValidation.vue";
import { useI18n } from 'vue-i18n';

export default {
  components: {
    Layout,
    PageHeader,
    Head,
    FormValidation,
  },
  props: {
    successMessage: String,
    alertMessage: String,
    faq: Object,
  },
  setup(props) {
    const { t } = useI18n();
    const form = useForm({
      question: props.faq ? props.faq.question || "" : "",
      answer: props.faq ? props.faq.answer || "" : "",
      user_type: props.faq ? props.faq.user_type || "" : "",
    });

    const validationRules = {
      question: { required: true },
      answer: { required: true },
      user_type: { required: true },
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
        if (props.faq && props.faq.id) {
          response = await axios.post(`/faq/update/${props.faq.id}`, form.data());
        } else {
          response = await axios.post('/faq/store', form.data());
        }

        if (response.status === 201) {
          successMessage.value = t('faq_saved_successfully');
          form.reset();
          router.get('/faq'); // Use router.push instead of router.get
        } else {
          alertMessage.value = t('failed_to_save_faq');
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          errors.value = error.response.data.errors;
        } else if (error.response && error.response.status === 403) {
          alertMessage.value = error.response.data.alertMessage;
          setTimeout(()=>{
            router.get('/faq');
          },5000)
        } else {
          console.error(t('error_saving_faq'), error);
          alertMessage.value = t('failed_to_save_faq_catch');
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
      errors
    };
  }
};
</script>

<template>
  <Layout>
    <Head title="Faq" />
    <PageHeader :title="faq ? $t('edit') : $t('create')" :pageTitle="$t('faq')" pageLink="/faq" />
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
                      <label for="user_type" class="form-label">{{$t("user_type")}}
                        <span class="text-danger">*</span>
                      </label>
                      <select id="user_type" class="form-select" v-model="form.user_type">
                        <option disabled value="">{{$t("select")}}</option>
                        <option value="user">{{$t("user")}}</option>
                        <option value="driver">{{$t("driver")}}</option>
                        <option value="all">{{$t("all")}}</option>
                      </select>
                      <span v-for="(error, index) in errors.user_type" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="question" class="form-label">{{$t("question")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" class="form-control" :placeholder="$t('enter_question')" id="question" v-model="form.question" />
                      <span v-for="(error, index) in errors.question" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="mb-3">
                      <label for="answer" class="form-label">{{$t("answer")}}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" class="form-control" :placeholder="$t('enter_answer')" id="answer" v-model="form.answer" />
                      <span v-for="(error, index) in errors.answer" :key="index" class="text-danger">{{ error }}</span>
                    </div>
                  </div>
                  <div class="col-lg-12">
                    <div class="text-end">
                      <button type="submit" class="btn btn-primary">{{ faq ? $t('update') : $t('save') }}</button>
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
