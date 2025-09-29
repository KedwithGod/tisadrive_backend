<script>
import { Link, Head } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";

export default {
    components: {
        Layout,
        PageHeader,
        Link, Head
    },
};
</script>

<template>
    <Layout>

        <Head title="Starter"/>
        <PageHeader title="Starter" pageTitle="Pages" />
        <BRow>
            <BCol lg="12">
                <BCard no-body id="tasksList">
                    <BCardHeader class="border-0">
                    </BCardHeader>
                    <BCardBody class="border border-dashed border-end-0 border-start-0">
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>

    </Layout>
</template>