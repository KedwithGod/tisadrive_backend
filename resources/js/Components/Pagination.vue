<template>
  <BRow class="g-0 text-center text-sm-start align-items-center mb-4">
    <BCol sm="6">
      <div>
        <p class="mb-sm-0 text-muted">
          {{$t("showing")}} <span class="fw-semibold">{{ paginator.from }}</span> {{$t("to")}}
          <span class="fw-semibold">{{ paginator.to }}</span> {{$t("of")}}
          <span class="fw-semibold text-decoration-underline">{{ paginator.total }}</span>
          {{$t("entries")}}
        </p>
      </div>
    </BCol>
    <BCol sm="6">
      <ul class="pagination pagination-separated justify-content-center justify-content-sm-end mb-sm-0">
        <!-- Previous Button -->
        <li class="page-item" :class="{ disabled: !paginator.prev_page_url }">
          <a 
            v-if="paginator.prev_page_url"
            href="javascript:void(0);" 
            @click.prevent="changePage(paginator.current_page - 1)"
            class="page-link"
            aria-label="Previous">
            <span aria-hidden="true">{{$t("prev")}}</span>
          </a>
          <span v-else class="page-link disabled" aria-disabled="true" aria-label="Previous">
            <span aria-hidden="true">{{$t("prev")}}</span>
          </span>
        </li>

        <!-- Page Numbers -->
        <li 
          class="page-item" 
          v-for="page in visiblePages" 
          :key="page" 
          :class="{ active: paginator.current_page === page }">
          <a 
            v-if="paginator.current_page !== page"
            href="javascript:void(0);" 
            @click.prevent="changePage(page)"
            class="page-link">{{ page }}</a>
          <span v-else class="page-link">{{ page }}</span>
        </li>

        <!-- Next Button -->
        <li class="page-item" :class="{ disabled: !paginator.next_page_url }">
          <a 
            v-if="paginator.next_page_url"
            href="javascript:void(0);" 
            @click.prevent="changePage(paginator.current_page + 1)"
            class="page-link"
            aria-label="Next">
            <span aria-hidden="true">{{$t("next")}}</span>
          </a>
          <span v-else class="page-link disabled" aria-disabled="true" aria-label="Next">
            <span aria-hidden="true">{{$t("next")}}</span>
          </span>
        </li>
      </ul>
    </BCol>
  </BRow>
</template>

<script>
export default {
  props: {
    paginator: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      pageSet: 0, // Tracks the current set of pages being shown
      pagesPerSet: 5 // Show 5 pages at a time
    };
  },
  computed: {
    // Compute the pages to display based on the current page set
    visiblePages() {
      const start = this.pageSet * this.pagesPerSet + 1;
      const end = Math.min(start + this.pagesPerSet - 1, this.paginator.last_page);
      return Array.from({ length: end - start + 1 }, (_, i) => start + i);
    }
  },
  methods: {
    changePage(page) {
      // If we are at the last page of the current set, advance to the next set
      if (page === this.visiblePages[this.visiblePages.length - 1]) {
        this.pageSet += 1;
      }
      // If we are at the first page of the current set and not on the first set, go back to the previous set
      if (page === this.visiblePages[0] && this.pageSet > 0) {
        this.pageSet -= 1;
      }
      this.$emit('page-changed', page);
    }
  },
  watch: {
    // Reset the page set if the paginator changes (useful for server-side pagination)
    'paginator.current_page': function (newPage) {
      this.pageSet = Math.floor((newPage - 1) / this.pagesPerSet);
    }
  }
};
</script>

  