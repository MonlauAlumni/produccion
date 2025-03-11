<script setup>
import { defineProps } from 'vue';

const props = defineProps({
    action: {
        type: Function,
        required: true
    }
});

const handleClick = () => {
    props.action();
};
</script>

<template>
    <button @click="handleClick"
        class="bg-gradient-to-r from-[#193CB8] to-[#2748c6] px-4 py-2 rounded hover:bg-blue-600 cursor-pointer text-white transition duration-300">
        <slot></slot>
    </button>
</template>