<script setup>
import { defineProps } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    event: {
        type: Object,
        required: true
    }
});

const formatDate = (dateString, options) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', options);
};

const formatTime = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleTimeString('es-ES', {
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<template>
    <div @click="router.visit(`/eventos/${event.slug}`)"
        class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-all cursor-pointer flex">
        <div class="w-1/3 bg-gray-100">
            <img v-if="event.photos && event.photos.length > 0" :src="event.photos[0].photo_path" :alt="event.title"
                class="w-full h-full object-cover" />
            <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                <i class='bx bx-calendar-event text-3xl'></i>
            </div>
        </div>

        <div class="p-4 flex-1">
            <h4 class="font-bold text-gray-800 mb-1 line-clamp-1">{{ event.title }}</h4>

            <div class="flex items-center text-gray-500 text-xs mb-1">
                <i class='bx bx-calendar mr-1'></i>
                {{ formatDate(event.event_date, {
                    month: 'short',
                    day: 'numeric'
                }) }}
                <span class="mx-1">·</span>
                <i class='bx bx-time mr-1'></i>
                {{ formatTime(event.event_date) }}
            </div>

            <div class="flex items-center text-gray-500 text-xs mb-2">
                <i class='bx bx-map mr-1'></i> {{ event.location }}
            </div>

            <p class="text-gray-600 text-sm line-clamp-2">{{ event.description }}</p>
        </div>
    </div>
</template>
