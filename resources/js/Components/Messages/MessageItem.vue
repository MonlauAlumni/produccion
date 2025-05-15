<template>
  <div>
    <!-- Fecha -->
    <!-- <div v-if="showDate" class="text-center my-4">
      <span class="px-3 py-1 bg-gray-100 rounded-full text-xs text-gray-600">
        {{ formatDate(message.created_at) }}
      </span>
    </div> -->
    
    <div 
      :class="[
        'flex',
        message.user_id === currentUser.id ? 'justify-end' : 'justify-start'
      ]"
    >
      <!-- Avatar (solo para mensajes recibidos) -->
      <div v-if="message.user_id !== currentUser.id && showAvatar" class="flex-shrink-0 mr-3">
        <img 
          v-if="message.user?.profile?.profile_picture" 
          :src="message.user.profile.profile_picture" 
          alt="User avatar" 
          class="w-8 h-8 rounded-lg object-cover"
        />
        <div v-else class="w-8 h-8 bg-gray-200 rounded-lg flex items-center justify-center text-[#193CB8]">
          <i class='bx bxs-user text-sm'></i>
        </div>
      </div>
      
      <!-- Contenido del mensaje -->
      <div 
        :class="[
          'max-w-[70%] px-4 py-2 rounded-lg',
          message.user_id === currentUser.id 
            ? 'bg-[#193CB8] text-white rounded-tr-none' 
            : 'bg-gray-100 text-gray-800 rounded-tl-none'
        ]"
      >
        <p>{{ message.content }}</p>
        <div 
          :class="[
            'text-xs mt-1 flex items-center justify-end',
            message.user_id === currentUser.id ? 'text-blue-100' : 'text-gray-500'
          ]"
        >
          {{ formatTime(message.created_at) }}
          
          <!-- Indicador de leído (solo para mensajes enviados) -->
          <span v-if="message.user_id === currentUser.id" class="ml-1">
            <i 
              :class="[
                'bx text-sm',
                message.read_at ? 'bx-check-double' : 'bx-check'
              ]"
            ></i>
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  message: {
    type: Object,
    required: true
  },
  showAvatar: {
    type: Boolean,
    default: true
  },
  showDate: {
    type: Boolean,
    default: false
  },
  currentUser: {
    type: Object,
    required: true
  }
});

// Formatear fecha completa
const formatDate = (dateString) => {
  const date = new Date(dateString);
  const now = new Date();
  const yesterday = new Date(now);
  yesterday.setDate(now.getDate() - 1);
  
  if (date.toDateString() === now.toDateString()) {
    return 'Hoy';
  } else if (date.toDateString() === yesterday.toDateString()) {
    return 'Ayer';
  } else {
    return date.toLocaleDateString('es-ES', { 
      day: 'numeric', 
      month: 'long', 
      year: 'numeric' 
    });
  }
};

// Formatear solo la hora
const formatTime = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
};
</script>