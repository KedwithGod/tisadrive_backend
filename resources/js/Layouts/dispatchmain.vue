<script>
import { layoutMethods,layoutComputed } from "@/state/helpers";

import DispatchHorizontal from "./dispatchHorizontal.vue";

export default {
    components: {
        DispatchHorizontal,
    },
    methods: {
        ...layoutMethods
    },
    created: () => {
        document.documentElement.setAttribute("data-layout", "horizontal");
    },
    data() {
        return {};
    },
    computed: {
        ...layoutComputed,
    },
    mounted() {
        // document.querySelector("html").setAttribute('dir', 'rtl');
    }
};
</script>

<template>
    <div>
        <DispatchHorizontal :layout="'horizontal'">
            <slot />
        </DispatchHorizontal>

    </div>
</template>
