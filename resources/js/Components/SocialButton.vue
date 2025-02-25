<template>
    <button
      class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
    >
      <component :is="icon" class="mr-2 h-4 w-4" />
      <slot></slot>
    </button>
  </template>
  
  <script setup>

  
  const props = defineProps({
    icon: {
      type: String,
      required: true
    }
  })
  
  const icon = {
    Github: 'github',
    Linkedin: 'linkedin',
    MessageCircle: 'message-circle'
  }[props.icon]
  </script>