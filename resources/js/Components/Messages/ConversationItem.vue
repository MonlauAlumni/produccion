<template>
  <div 
    :class="[
      'p-4 border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-800 cursor-pointer transition-colors',
      isActive ? 'bg-blue-50 dark:bg-blue-900' : ''
    ]"
    @click="$emit('click')"
  >
    <div class="flex items-start gap-3">
      <!-- Avatar -->
      <div class="relative">
        <img 
          v-if="conversation.participant.profile && conversation.participant.profile.profile_picture" 
          :src="conversation.participant.profile.profile_picture" 
          alt="User avatar" 
          class="w-12 h-12 rounded-lg object-cover"
        />
        <div v-else class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center text-[#193CB8] dark:text-blue-200">
          <i class='bx bxs-user text-xl'></i>
        </div>
        
        <!-- Indicador online (simulado) -->
        <div 
          v-if="conversation.participant.is_online"
          class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white dark:border-gray-800"
        ></div>
      </div>
      
      <!-- Información -->
      <div class="flex-1 min-w-0">
        <div class="flex justify-between items-start">
          <h3 class="font-semibold text-gray-800 dark:text-gray-200 truncate">
            {{ conversation.participant.name }} {{ conversation.participant.last_name_1 }}
          </h3>
          <span class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
            {{ formatDate(conversation.updated_at) }}
          </span>
        </div>
        
        <p class="text-sm text-gray-600 dark:text-gray-400 truncate mt-1">
          {{ conversation.last_message || 'Sin mensajes' }}
        </p>
        
        <!-- Etiqueta de trabajo relacionado si existe -->
        <div 
          v-if="conversation.job"
          class="mt-1 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200"
        >
          <i class='bx bx-briefcase text-xs mr-1'></i>
          {{ conversation.job.title }}
        </div>
      </div>
      
      <!-- Contador de no leídos -->
      <div 
        v-if="conversation.unread_count > 0"
        class="min-w-[20px] h-5 rounded-full bg-[#193CB8] dark:bg-blue-400 text-white text-xs flex items-center justify-center px-1.5"
      >
        {{ conversation.unread_count > 99 ? '99+' : conversation.unread_count }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
  conversation: {
    type: Object,
    required: true
  },
  isActive: {
    type: Boolean,
    default: false
  }
});

defineEmits(['click']);

// Formatear fecha relativa
const formatDate = (dateString) => {
  const date = new Date(dateString);
  const now = new Date();
  const diffInDays = Math.floor((now - date) / (1000 * 60 * 60 * 24));
  
  if (diffInDays === 0) {
    // Hoy - mostrar hora
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  } else if (diffInDays === 1) {
    // Ayer
    return 'Ayer';
  } else if (diffInDays < 7) {
    // Esta semana - mostrar día
    const days = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
    return days[date.getDay()];
  } else {
    // Más de una semana - mostrar fecha
    return date.toLocaleDateString();
  }
};
</script>