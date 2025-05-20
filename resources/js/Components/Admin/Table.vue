<script setup>
import { defineProps } from 'vue';

const props = defineProps({
    headers: Array 
});
</script>

<template>
    <div class="overflow-x-auto mt-4 rounded-xl border border-gray-300 dark:border-gray-700 shadow-xl">
        <table class="min-w-full bg-white dark:bg-gray-800">
            <thead>
                <tr class="border-b border-gray-300 dark:border-gray-700 text-left text-blue-600 dark:text-blue-400 font-bold">
                    <th v-for="(header, index) in headers" :key="index" class="py-2 px-4">
                        {{ header }}
                    </th>
                </tr>
            </thead>
            <tbody class="text-gray-700 dark:text-gray-300">
                <slot></slot>
            </tbody>
        </table>
    </div>
</template>
