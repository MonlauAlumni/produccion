<template> 
    <button 
      @click="$emit('action')" 
      class="bgwhite text-black border-2 border-black px-7 py-1 rounded-2xl text-lg font-medium hover:bg-slate-200  transition duration-300"
    >
      {{ label }}
    </button>
  </template>
  
  <script>
  export default {
    name: "ButtonAcces",
    props: {
      label: {
        type: String,
        default: "Botón", 
      },
    },
  };
  </script>
  