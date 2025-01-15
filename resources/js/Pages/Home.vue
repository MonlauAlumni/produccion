<template>
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
</script>