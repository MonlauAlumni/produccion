<template>
  <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 flex justify-between items-center">
    <div class="flex items-center gap-3">
      <!-- Avatar -->
      <div class="relative">
        <img 
          v-if="conversation.participant.profile && conversation.participant.profile.profile_picture" 
          :src="conversation.participant.profile.profile_picture" 
          alt="User avatar" 
          class="w-10 h-10 rounded-lg object-cover"
        />
        <div v-else class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center text-[#193CB8] dark:text-blue-200">
          <i class='bx bxs-user text-xl'></i>
        </div>
        
        <!-- Indicador online -->
        <div 
          v-if="conversation.participant.is_online"
          class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 rounded-full border-2 border-white dark:border-gray-800"
        ></div>
      </div>
      
      <!-- Información -->
      <div>
        <h3 class="font-semibold text-gray-800 dark:text-gray-200">
          {{ conversation.participant.name }} {{ conversation.participant.last_name_1 }}
        </h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">
          {{ conversation.participant.profile?.headline || 'Usuario' }}
        </p>
      </div>
    </div>
    
    <!-- Acciones -->
    <div class="flex items-center gap-2">
      <button 
        v-if="conversation.job"
        @click="showJobDetails = !showJobDetails"
        :class="[
          'px-3 py-1.5 rounded-lg text-sm font-medium flex items-center gap-1',
          showJobDetails 
            ? 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200' 
            : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'
        ]"
      >
        <i class='bx bx-briefcase'></i>
        <span class="hidden sm:inline">Ver oferta</span>
      </button>
    </div>
    
    <!-- Panel de detalles del trabajo (condicional) -->
    <RelatedJobCard 
      v-if="showJobDetails && conversation.job" 
      :job="conversation.job"
      @close="showJobDetails = false"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import RelatedJobCard from './RelatedJobCard.vue';

const props = defineProps({
  conversation: {
    type: Object,
    required: true
  }
});

const showJobDetails = ref(false);
</script>