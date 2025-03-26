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

const getDaysRemaining = (dateString) => {
  const eventDate = new Date(dateString);
  const today = new Date();
  
  if (eventDate < today) {
    return 'Finalizado';
  } else if (eventDate.toDateString() === today.toDateString()) {
    return 'Hoy';
  } else {
    return `${Math.ceil((eventDate - today) / (1000 * 60 * 60 * 24))} días`;
  }
};

const getStatusClass = (dateString) => {
  const eventDate = new Date(dateString);
  const today = new Date();
  
  if (eventDate < today) {
    return 'bg-gray-100 text-gray-700';
  } else if (eventDate.toDateString() === today.toDateString()) {
    return 'bg-green-100 text-green-700';
  } else {
    return 'bg-blue-100 text-blue-700';
  }
};
</script>

<template>
  <div @click="router.visit(`/eventos/${event.slug}`)"
    class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-all cursor-pointer">
    <div class="relative h-48 bg-gray-100">
      <img v-if="event.photos && event.photos.length > 0" :src="event.photos[0].photo_path" :alt="event.title"
        class="w-full h-full object-cover" />
      <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
        <i class='bx bx-calendar-event text-5xl'></i>
      </div>

      <div class="absolute top-3 right-3">
        <span :class="['px-2 py-1 rounded-full text-xs font-medium', getStatusClass(event.event_date)]">
          {{ getDaysRemaining(event.event_date) }}
        </span>
      </div>

      <div v-if="event.is_private" class="absolute top-3 left-3">
        <span class="px-2 py-1 bg-gray-800/70 text-white rounded-full text-xs font-medium flex items-center">
          <i class='bx bx-lock-alt mr-1'></i> Privado
        </span>
      </div>
    </div>

    <div class="p-5">
      <h3 class="text-xl font-bold text-gray-800 mb-2 line-clamp-1">{{ event.title }}</h3>

      <div class="flex flex-wrap items-center text-gray-500 text-sm gap-y-1 gap-x-3 mb-3">
        <span class="flex items-center">
          <i class='bx bx-calendar mr-1'></i>
          {{ formatDate(event.event_date, {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
          }) }}
        </span>
        <span class="flex items-center">
          <i class='bx bx-time mr-1'></i>
          {{ formatTime(event.event_date) }}
        </span>
      </div>

      <div class="flex items-center text-gray-500 text-sm mb-3">
        <i class='bx bx-map mr-1'></i> {{ event.location }}
      </div>

      <p class="text-gray-600 line-clamp-2 mb-4">{{ event.description }}</p>

      <div class="flex items-center justify-between">
        <div class="flex items-center">
          <div class="flex -space-x-2">
            <div v-for="(attendee, index) in event.attendees?.slice(0, 3)" :key="index"
              class="w-8 h-8 rounded-full border-2 border-white overflow-hidden">
              <img v-if="attendee.user.profile && attendee.user.profile.profile_picture"
                :src="attendee.user.profile.profile_picture" :alt="attendee.user.name"
                class="w-full h-full object-cover" />
              <div v-else class="w-full h-full bg-gray-200 flex items-center justify-center text-[#193CB8]">
                <i class='bx bxs-user'></i>
              </div>
            </div>
            <div v-if="event.attendees_count > 3"
              class="w-8 h-8 rounded-full border-2 border-white bg-gray-100 flex items-center justify-center text-xs text-gray-600">
              +{{ event.attendees_count - 3 }}
            </div>
          </div>
          <span class="ml-2 text-sm text-gray-500">
            {{ event.attendees_count }} {{ event.attendees_count === 1 ? 'asistente' : 'asistentes' }}
          </span>
        </div>

        <div v-if="event.group" class="flex items-center text-sm text-gray-500">
          <i class='bx bx-group mr-1'></i> {{ event.group.name }}
        </div>
      </div>
    </div>
  </div>
</template>
