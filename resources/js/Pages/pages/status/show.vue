<template>
    <div>
      <div class="flex mb-4">
        <div class="px-4 py-2 text-blue bg-indigo-600 hover:bg-indigo-700 text-white mt-3 rounded">
          Status Details
        </div>
      </div>
  
      <div class="min-w-full overflow-hidden overflow-x-auto align-middle sm:rounded-md">
        <table class="table table-bordered min-w-full border divide-y divide-gray-200">
          <!-- Table body -->
          <tbody class="bg-white divide-y divide-gray-200 divide-solid">
            <tr class="bg-white">
              <th>Status Name</th>
              <td class="px-6 py-4 text-sm leading-5 text-gray-900 whitespace-no-wrap">{{ status.name }}</td>
            </tr>
            <tr class="bg-white">
              <th>Status order</th>
              <td class="px-6 py-4 text-sm leading-5 text-gray-900 whitespace-no-wrap">{{ status.order }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </template>

<script setup>
import { defineProps } from 'vue';
    defineProps({
        status: Object
    })
</script>