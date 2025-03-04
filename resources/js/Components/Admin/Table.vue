<script setup>
import { defineProps } from 'vue';

const props = defineProps({
    headers: Array 
});
</script>

<template>
    <div class="overflow-x-auto mt-4 rounded-xl border border-gray-300">
        <table class="min-w-full bg-white">
            <thead>
                <tr class="border-b text-left text-blue-600 font-bold">
                    <th v-for="(header, index) in headers" :key="index" class="py-2 px-4">
                        {{ header }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <slot></slot>
            </tbody>
        </table>
    </div>
</template>
