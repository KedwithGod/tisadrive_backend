<script>
import { Link, Head, useForm, router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
export default {
  components: {
    Link
  },
  props: {
  
  },
  setup() {
    const { props } = usePage();
    const firstModal = ref(null);
    let bsModalInstance = null;

    const showModal = () => {
      if (firstModal.value) {
        bsModalInstance = new bootstrap.Modal(firstModal.value);
        bsModalInstance.show();
      }
    };

    const closeModal = () => {
      if (bsModalInstance) {
        bsModalInstance.hide();
      }
    };
    onMounted(() => {
      // Show modal after 5 seconds
       // Trigger modal only if app_for is 'demo'
  if (props.app_for === 'demo') {
      setTimeout(() => {
        showModal();

        // Auto-close modal after another 5 seconds (optional)
        setTimeout(() => {
          closeModal();
        }, 10000); // close after 5 seconds
      }, 1000); // open after 5 seconds
    }
    });

    return {
      firstModal,
      closeModal,
    };
  },
};
</script>



<template>
<!-- Warning Modal -->
<div class="modal fade" id="firstmodal" tabindex="-1" aria-hidden="true" ref="firstModal" :style="{ display: show ? 'block' : 'none' }">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body text-center p-5">
        <img src="@assets/images/warning.gif" class="img-fluid" style="width:55px;height:55px">
        <div class="mt-4 pt-4">
          <h4 class="text-danger fw-bold">Important Notice!</h4>
          <p class="text-muted mt-3">
            ⚠️ Please <strong>do not purchase nulled or pirated source code</strong> from unknown websites. It may contain harmful code, and <strong>we will not provide support</strong> for such copies.
          </p>
          <p class="text-muted">
            For full support and updates, always <strong>purchase from the official source</strong> below.
          </p>
          <a href="https://codecanyon.net/item/restart-perfect-taxi-solution-with-parcel-delivery/55733584" target="_blank" class="btn btn-success mt-3">
            Buy Official Version
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
</template>
