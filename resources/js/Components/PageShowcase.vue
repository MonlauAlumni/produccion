<template>
    <section v-for="(section, index) in sections" :key="index" class="page-showcase relative bg-cover bg-center text-black text-center py-16 bg-gray-100">
        <div :class="['container mx-auto max-w-4xl flex items-center gap-4', imageAlignment(index)]">
            <div :class="['w-1/2', imageAlignment(index) === 'flex-row-reverse' ? 'order-2' : 'order-1']">
                <img :src="section.image" :alt="section.title" class="w-full h-[32rem] object-cover mb-4"> <!-- Adjusted height -->
            </div>
            <div class="w-1/2 px-4">
                <h2 class="text-3xl font-bold mb-4">{{ section.title }}</h2>
                <p class="text-lg text-justify">{{ section.description }}</p>
            </div>
        </div>
    </section>
</template>

<script setup>
import { defineProps } from 'vue';

const props = defineProps({
    sections: {
        type: Array,
        required: true
    }
});

const imageAlignment = (index) => {
    return index % 2 === 0 ? 'flex-row' : 'flex-row-reverse';
};
</script>
