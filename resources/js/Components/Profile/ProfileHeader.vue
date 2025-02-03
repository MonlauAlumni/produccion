<template>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
      <div class="p-6">
        <div class="flex flex-col items-center md:flex-row md:items-start md:space-x-6">
          <div class="h-24 w-24 rounded-full bg-gray-200 overflow-hidden">
            <img src="" alt="Profile" class="h-full w-full object-cover" />
          </div>
          <div class="mt-4 text-center md:mt-0 md:text-left">
            <h1 class="text-2xl font-bold">{{ user.name }} {{ user.last_name_1 }} {{ user.last_name_2 }}</h1>
            <p class="mt-1">Exalumno de Desarrollo de Aplicaciones Web</p>
            <div class="mt-4 flex items-center justify-center space-x-2 md:justify-start">
              <SocialButton icon="Github">GitHub</SocialButton>
              <SocialButton icon="Linkedin">LinkedIn</SocialButton>
              <SocialButton icon="MessageCircle">Mensaje</SocialButton>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import SocialButton from '../SocialButton.vue'

  defineProps({
  user: Object,
  profile: Object,
});

  </script>