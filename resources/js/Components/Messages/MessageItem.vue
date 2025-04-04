<template>
    <div 
      :class="[
        'flex',
        message.is_from_me ? 'justify-end' : 'justify-start'
      ]"
    >
      <div 
        :class="[
          'max-w-[70%] rounded-lg p-3 shadow-sm',
          message.is_from_me 
            ? 'bg-[#193CB8] text-white rounded-br-none' 
            : 'bg-white text-gray-800 rounded-bl-none'
        ]"
      >
        <div class="flex flex-col">
          <p class="whitespace-pre-wrap break-words">{{ message.content }}</p>
          <span 
            :class="[
              'text-xs mt-1 self-end',
              message.is_from_me ? 'text-blue-100' : 'text-gray-500'
            ]"
          >
            {{ formatTime(message.created_at) }}
            <i v-if="message.is_from_me" :class="[
              'bx ml-1',
              message.read_at ? 'bx-check-double' : 'bx-check'
            ]"></i>
          </span>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  defineProps({
    message: {
      type: Object,
      required: true
    }
  })
  
  const formatTime = (dateString) => {
    const date = new Date(dateString)
    return date.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' })
  }
  </script>
  
  <style scoped>
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
  }
  
  .flex {
    animation: fadeIn 0.3s ease-out;
  }
  </style>