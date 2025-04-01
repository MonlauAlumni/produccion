<template>
    <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
      <div class="flex items-center">
        <div class="flex items-center">
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
            <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></div>
          </div>
          
          <div class="ml-3">
            <h2 class="font-semibold text-gray-800">
              {{ contactName }}
            </h2>
            <div class="text-xs text-gray-500 flex items-center">
              <i class='bx bxs-circle text-green-500 text-[8px] mr-1'></i> 
              Online
            </div>
          </div>
        </div>
      </div>
      
      <div class="flex items-center">
        <button 
          v-if="conversation.related_job"
          @click="$emit('view-job-offer')"
          class="mr-3 text-sm text-[#193CB8] hover:underline flex items-center"
        >
          <i class='bx bx-briefcase mr-1'></i>
          Ver oferta
        </button>
        
        
      </div>
    </div>
  </template>
  
  <script setup>
  import { computed } from 'vue'
  
  const props = defineProps({
    conversation: {
      type: Object,
      required: true
    }
  })
  
  defineEmits(['view-job-offer'])
  
  const contactName = computed(() => {
    return props.conversation.with_company 
      ? props.conversation.with_company.company_name 
      : `${props.conversation.with_user.name} ${props.conversation.with_user.last_name_1}`
  })
  </script>