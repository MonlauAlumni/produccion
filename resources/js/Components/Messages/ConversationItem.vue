<template>
    <button
      :class="[
        'w-full text-left px-4 py-3 border-b border-gray-100 hover:bg-gray-50 transition-colors',
        isActive ? 'bg-blue-50' : ''
      ]"
    >
      <div class="flex items-start gap-3">
        <div class="relative">
          <div v-if="conversation.with_company">
            <img 
              v-if="conversation.with_company.profile_picture" 
              :src="conversation.with_company.profile_picture" 
              alt="Company logo" 
              class="w-10 h-10 rounded-lg object-cover"
            />
            <div v-else class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center text-[#193CB8]">
              <i class='bx bxs-building text-xl'></i>
            </div>
          </div>
          <div v-else>
            <img 
              v-if="conversation.with_user.profile && conversation.with_user.profile.profile_picture" 
              :src="conversation.with_user.profile.profile_picture" 
              alt="User avatar" 
              class="w-10 h-10 rounded-lg object-cover"
            />
            <div v-else class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center text-[#193CB8]">
              <i class='bx bxs-user text-xl'></i>
            </div>
          </div>
          <div v-if="conversation.unread_count > 0" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
            {{ conversation.unread_count }}
          </div>
        </div>
        
        <div class="flex-1 min-w-0">
          <div class="flex justify-between items-start">
            <h3 class="font-semibold text-gray-800 truncate">
              {{ contactName }}
            </h3>
            <span class="text-xs text-gray-500 whitespace-nowrap ml-2">
              {{ formatDate(conversation.last_message.created_at) }}
            </span>
          </div>
          
          <p class="text-sm text-gray-600 truncate mt-1">
            {{ conversation.last_message.content }}
          </p>
          
          <div v-if="conversation.related_job" class="mt-1 flex items-center">
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-[#193CB8]/10 text-[#193CB8]">
              <i class='bx bx-briefcase text-xs mr-1'></i>
              {{ conversation.related_job.title }}
            </span>
          </div>
        </div>
      </div>
    </button>
  </template>
  
  <script setup>
  import { computed } from 'vue'
  
  const props = defineProps({
    conversation: {
      type: Object,
      required: true
    },
    isActive: {
      type: Boolean,
      default: false
    }
  })
  
  const contactName = computed(() => {
    return props.conversation.with_company 
      ? props.conversation.with_company.company_name 
      : `${props.conversation.with_user.name} ${props.conversation.with_user.last_name_1}`
  })
  
  const formatDate = (dateString) => {
    const date = new Date(dateString)
    const now = new Date()
    const yesterday = new Date(now)
    yesterday.setDate(yesterday.getDate() - 1)
    
    if (date.toDateString() === now.toDateString()) {
      return formatTime(dateString)
    } else if (date.toDateString() === yesterday.toDateString()) {
      return 'Ayer'
    } else {
      return date.toLocaleDateString('es-ES', { day: 'numeric', month: 'short' })
    }
  }
  
  const formatTime = (dateString) => {
    const date = new Date(dateString)
    return date.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' })
  }
  </script>