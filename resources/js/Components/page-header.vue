<script>
import { Link, Head, useForm, router } from '@inertiajs/vue3';
export default {
  components: {
    Link
  },
  props: {
    title: {
      type: String,
      default: "",
    },
    pageTitle: {
      type: String,
      default: "",
    },
    pageLink: {
      type: String,
      default: "",
    }
  },
};
</script>



<template>
  <BRow>
    <BCol cols="12">
      <div class="page-title-box d-sm-flex align-items-center justify-content-between">
        <h4 class="mb-sm-0">{{ title }}</h4>
        <div class="page-title-right">
          <ol class="breadcrumb m-0">
            <li class="breadcrumb-item">
              <Link  :href="pageLink"> {{ pageTitle }} </Link>
            </li>
            <li class="breadcrumb-item active">{{ title }}</li>
          </ol>
        </div>
      </div>
    </BCol>
  </BRow>
</template>
