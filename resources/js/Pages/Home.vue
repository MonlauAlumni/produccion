<template>
  <div class="bg-black text-white p-8">
    <h1 class="text-3xl font-bold mb-4">Bienvenido a la página de inicio</h1>
    <p class="mb-6">¡Esto es un ejemplo de Vue con Inertia!</p>
    
    <!-- Botón sin acción -->
    <ButtonRegister 
      label="BotonUniversal"
      @action="ButtonRegister"
      />
  </div>
</template>

<script>
import ButtonRegister from "@/Components/ButtonRegister.vue"; // Ruta ajustada según tu estructura de proyecto

export default {
  components: {
    ButtonRegister,
  },
  methods: {
    ButtonRegister() {
      console.log("Hiciste clic en el botón con acción");
      alert("Este botón tiene una acción.");
    },
  },
};
</script>
