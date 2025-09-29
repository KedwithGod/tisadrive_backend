<template>
    <div class="flex mb-4 place-content-end">
        <div class="px-4 py-2 bg-indigo-600 cursor-pointer hover:bg-indigo-700 text-white">
            <Link href="/status/create" class="text-sm font-medium">Add new Status</Link>
        </div>
    </div>
    <div class="min-w-full overflow-hidden overflow-x-auto align-middle sm:rounded-md">
        <table class="table table-bordered min-w-full border divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-6 py-3 bg-gray-50">
                        <span
                            class="text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase">Name</span>
                    </th>
                    <th class="px-6 py-3 bg-gray-50">
                        <span
                            class="text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase">Options</span>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 divide-solid">
                <template v-for="item in status" :key="item.id">
                    <tr class="bg-white">
                        <td class="px-6 py-4 text-sm leading-5 text-gray-900 whitespace-no-wrap">{{ item.name }}</td>
                        <!-- <td class="px-6 py-4 text-sm leading-5 text-gray-900 whitespace-no-wrap">{{ item.order }}</td> -->
                        <td class="px-6 py-4 text-sm leading-5 text-center text-grey-900 whitespace-no-wrap">
                            <Link :href="`/status/show/${item.id}`"
                                class="px-4 py-2 rounded-md border border-transparent bg-indigo-600 cursor-pointer hover:bg-indigo-700 text-white">
                            Show</Link>

                            <!-- <router-link :to="{ name: 'status.edit', params: { id: item.id } }"
                                class="px-4 py-2 rounded-md border border-transparent bg-indigo-600 cursor-pointer hover:bg-indigo-700 text-white">Edit</router-link>

                            <button @click="deleteStatus(item.id)"
                                class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out bg-gray-800 border border-transparent rounded-md hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 ml-2">
                                Delete</button>  -->
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</template>


<script setup>
import { defineProps } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';

defineProps({
    status: Object
});
</script>