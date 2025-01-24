<template>
    <button
    type="submit"
      class="w-full py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200"
      @click="$emit('click')"  
    >
      {{ label }}
    </button>
  </template>
  
  <script>
  export default {
    name: 'ButtonNext',
    props: {
      label: {
        type: String,
        default: 'Siguiente',  
      },
    },
  };
  </script>
  