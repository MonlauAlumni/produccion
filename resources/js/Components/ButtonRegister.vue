<template> 
    <button 
      @click="$emit('action')" 
      class="bg-blue-500 text-white px-20 py-1 rounded-lg text-lg font-medium hover:bg-blue-800 transition duration-300"
    >
      {{ label }}
    </button>
  </template>
  
  <script>
  export default {
    name: "ButtonRegister",
    props: {
      label: {
        type: String,
        default: "Botón", 
      },
    },
  };
  </script>
  