<template>
<<<<<<< HEAD
  <div class="h-screen">
    <Navbar />
    <div class="flex flex-col justify-center items-center p-4 gap-4">
      <HomeContainer />
      <HomeContainer />
    </div>
  </div>

</template>

<script setup>
import Navbar from '../Components/Navbar.vue';
import HomeContainer from '../Components/HomeContainer.vue';
=======
  <div class="bg-black text-white p-8">
    <h1 class="text-3xl font-bold mb-4">Bienvenido a la página de inicio</h1>
    <p class="mb-6">¡Esto es un ejemplo de Vue con Inertia!</p>
   </div>
   
  <div class="bg-white text-black p-8">
    <!-- Botón sin acción -->
    <ButtonRegister 
      label="BotonUniversal"
      @action="ButtonRegister"
      />
  </div>
    
  <div class="bg-white text-black p-8">
    <!-- Botón sin acción -->
    <ButtonAcces 
      label="Continuar con Google"
      @action="ButtonAcces"
      />
  </div>
</template>

<script>
import ButtonRegister from "@/Components/ButtonRegister.vue"; 
import ButtonAcces from "@/Components/ButtonAcces.vue";


export default {
  components: {
    ButtonRegister,
    ButtonAcces,
  },
  methods: {
    ButtonRegister() {
      console.log("Hiciste clic en el botón con acción");
      alert("Este botón tiene una acción.");
    },
    ButtonAcces() {
      console.log("Hiciste clic en el botón con acción");
      alert("Este botón tiene una acción.");
    },
  },
};
>>>>>>> Ponga
</script>
