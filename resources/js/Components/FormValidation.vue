<template>
  <slot :validate="validate" :errors="errors"></slot>
</template>

<script>
export default {
  name: "FormValidation",
  props: {
    form: Object,
    rules: Object
  },
  data() {
    return {
      errors: {}
    };
  },
  methods: {
    validate() {
      this.errors = {};
      for (const field in this.rules) {
        // Check if the field is required and only mark it as invalid if it's empty, null, or undefined (not 0)
        if (this.rules[field].required && (this.form[field] === null || this.form[field] === undefined || this.form[field] === "")) {
          if (!this.errors[field]) {
            this.errors[field] = [];
          }
          this.errors[field].push(`required`);
        }

        // Check if the field should be numeric and if it contains a non-numeric value
        if (this.rules[field].numeric && isNaN(this.form[field])) {
          if (!this.errors[field]) {
            this.errors[field] = [];
          }
          this.errors[field].push(`${field} must be numeric`);
        }
      }
      return this.errors;
    }
  }
};
</script>
