<script setup>
import { defineProps } from 'vue';
import { router } from '@inertiajs/vue3';
import EventCard from '@/Components/Social/EventCard.vue';
import EventFeaturedCard from '@/Components/Social/EventFeaturedCard.vue';

const props = defineProps({
  events: {
    type: Array,
    default: () => []
  }
});
</script>

<template>
  <div class="py-8">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Eventos</h2>
      <button @click="router.get('/eventos/nuevo')"
        class="px-4 py-2 bg-[#193CB8] text-white rounded-lg hover:bg-[#142d8c] transition-colors flex items-center dark:bg-[#142d8c] dark:hover:bg-[#193CB8]">
        <i class='bx bx-plus-circle mr-2'></i> Crear evento
      </button>
    </div>

    <div v-if="events && events.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <EventCard v-for="event in events" :key="event.id" :event="event" />
    </div>

    <div v-else class="bg-white dark:bg-gray-900 rounded-xl shadow-sm p-8 text-center">
      <div class="w-20 h-20 bg-[#193CB8]/10 dark:bg-[#193CB8]/20 rounded-full flex items-center justify-center mx-auto mb-4">
        <i class='bx bx-calendar-event text-4xl text-[#193CB8] dark:text-[#8fa7ff]'></i>
      </div>
      <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-2">No hay eventos disponibles</h3>
      <p class="text-gray-600 dark:text-gray-300 max-w-md mx-auto mb-6">
        Aún no hay eventos programados. ¡Sé el primero en crear un evento para la comunidad Alumni!
      </p>
      <button @click="router.get('/eventos/nuevo')"
        class="px-6 py-3 bg-[#193CB8] text-white rounded-lg hover:bg-[#142d8c] transition-colors inline-flex items-center dark:bg-[#142d8c] dark:hover:bg-[#193CB8]">
        <i class='bx bx-plus-circle mr-2'></i> Crear un evento
      </button>
    </div>

    <div v-if="events && events.length > 0" class="mt-8">
      <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4">Eventos destacados</h3>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <EventFeaturedCard v-for="event in events.slice(0, 2)" :key="event.id" :event="event" />
      </div>
    </div>
  </div>
</template>

