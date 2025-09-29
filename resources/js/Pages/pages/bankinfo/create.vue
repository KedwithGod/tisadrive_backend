<script>
import { Link, Head, useForm } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Swal from "sweetalert2";
import { ref, reactive } from "vue";
import axios from "axios";
import { debounce } from 'lodash';
import "@vueform/multiselect/themes/default.css";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";

export default {
  setup() {
    const fields = ref([
      { id: Date.now(), input_field_name: "", placeholder: "", isRequired: false, method: "", input_field_type: "" },
    ]);

    const errors = reactive({});

    // Use debounce to prevent rapid clicks from adding fields too quickly
    const addField = debounce(() => {
      fields.value.push({
        id: Date.now(), // Ensures a unique identifier
        input_field_name: "",
        input_field_type: "",
        placeholder: "",
        isRequired: false,
        method: "",
      });
    }, 300); // 300ms debounce delay

    const removeField = (index) => {
      fields.value.splice(index, 1);
    };

    const validateFields = () => {
      let isValid = true;
      errors.value = {};

      fields.value.forEach((field, index) => {
        if (!field.input_field_name) {
          errors.value[`input_field_name_${index}`] = "Field name is required.";
          isValid = false;
        }

        if (!field.input_field_type) {
          errors.value[`input_field_type_${index}`] = "Field type is required.";
          isValid = false;
        }
      });

      return isValid;
    };

    const submitForm = async () => {
      if (!validateFields()) {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "Please fix the errors before submitting the form.",
        });
        return;
      }

      try {
        // Restructure the data to move the "method" key outside the "fields" array
        const requestData = {
          method: fields.value[0].method, // Assuming the "method" is common for all fields
          fields: fields.value.map(({ method, ...field }) => field), // Exclude "method" from each field object
        };

        const response = await axios.post('/driver-bank-info/store', requestData);

        Swal.fire({
          icon: "success",
          title: "Success",
          text: "Fields submitted successfully!",
        });
      } catch (error) {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "There was a problem submitting the form.",
        });
      }
    };

    return {
      fields,
      errors,
      addField,
      removeField,
      submitForm,
    };
  },
  components: {
    Layout,
    PageHeader,
    Head,
    flatPickr,
    Link,
  },
};
</script>



<template>
  <Layout>
    <Head title="Bank Info" />
    <PageHeader :title="$t('bank_infos')" :pageTitle="$t('bank_infos')" pageLink="/driver-bank-info"/>
    <BRow>
      <BCol lg="12">
        <BCard no-body id="tasksList">
          <BCardHeader class="border-0">
            <BRow class="g-2">
              <BCol md="auto" class="ms-auto">
                <div class="d-flex align-items-center gap-2">
                  <!-- Remove the Link component and directly call the addField method -->
                  <BButton variant="primary" @click="addField">
                    <i class="ri-add-line align-bottom me-1"></i> {{$t("add_new_field")}}
                  </BButton>
                </div>
              </BCol>
            </BRow>
          </BCardHeader>

          
          <BCardBody class="border border-dashed border-end-0 border-start-0">
            <div class="row">
              <div class="col-sm-4">
                <div class="mb-3">
                  <label for="method_name" class="form-label">{{ $t('method_name') }}
                    <span class="text-danger">*</span>
                  </label>
                  <input
                    type="text"
                    v-model="fields[0].method"
                    class="form-control"
                    :placeholder="$t('method_name')"
                  />
                </div>
              </div>
            </div>
            <!-- Iterate over the fields array and render each field group -->
            <div v-for="(field, index) in fields" :key="field.id" class="row d-flex align-items-center bg-light rounded mt-4 p-4">
              <div class="col-12">
                <div class="mb-1 d-flex justify-content-end">
                  <div class="form-check form-check-inline">
                    <i
                      class="bx bx-trash text-danger fs-22 btn"
                      @click="removeField(index)"
                    ></i>
                  </div>
                </div>
              </div>             
              <div class="col-sm-3">
                <div class="mb-3">
                  <label for="input_field_type" class="form-label">{{$t("input_field_type")}}</label>
                  <select id="input_field_type" class="form-select" v-model="field.input_field_type">
                    <option disabled value="">{{$t("select")}}</option>
                    <option value="text">{{$t("text")}}</option>
                    <option value="number">{{$t("number")}}</option>
                  </select>
                  <span v-if="errors[`input_field_type_${index}`]" class="text-danger">{{ errors[`input_field_type_${index}`] }}</span>
                </div>
              </div>
              <div class="col-sm-3">
                <div class="mb-3">
                  <label for="input_field_name" class="form-label">{{ $t('input_field_name') }}</label>
                  <input
                    type="text"
                    class="form-control"
                    v-model="field.input_field_name"
                    :placeholder="$t('input_field_name')"
                  />
                  <span v-if="errors[`input_field_name_${index}`]" class="text-danger">{{ errors[`input_field_name_${index}`] }}</span>
                </div>
              </div>
              <div class="col-sm-3">
                <div class="mb-3">
                  <label for="placeholder" class="form-label">{{ $t('placeholder') }}</label>
                  <input
                    type="text"
                    class="form-control"
                    v-model="field.placeholder"
                    :placeholder="$t('enter_your_placeholder')"
                  />
                </div>
              </div>
              <div class="col-sm-3">
                <div class="mt-3">
                  <div class="form-check form-check-inline">
                    <input
                      class="form-check-input"
                      type="checkbox"
                      v-model="field.isRequired"
                    />
                    <label class="form-check-label" for="inlineCheckbox3">{{$t("is_required")}}</label>
                  </div>
                </div>
              </div>
            </div>

            <!-- Submit button to trigger form submission -->
            <BRow  class="g-4">
              <BCol md="auto" class="ms-auto">
                <div class="d-flex align-items-center gap-4">
                  <BButton variant="primary" @click="submitForm">
                    {{$t('submit')}}
                  </BButton>
                </div>
              </BCol>
            </BRow>
          </BCardBody>
        </BCard>
      </BCol>
    </BRow>
  </Layout>
</template>
