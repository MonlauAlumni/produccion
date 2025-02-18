<template>
  <div class="relative h-screen flex">
    <Navbar />
    <div class="flex-1 flex flex-col mt-24">
      <section class="flex-1 flex flex-col items-center justify-center">
        <header class="text-center">
          <h1 class="text-3xl font-bold">Mi Landing Page</h1>
          <p class="mt-2 text-gray-600">Un breve mensaje de bienvenida</p>
          <button class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Comenzar</button>
        </header>
      </section>
    </div>
  </div>
</template>

<script setup>
import Navbar from '../Components/Navbar.vue';
</script>
