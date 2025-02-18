<template>
  <div class="h-screen">
    <Navbar />
    <SideBar />
    <div class="flex flex-col justify-center items-center p-4 gap-4">
      <HomeContainer />
      <HomeContainer />
    </div>
  </div>
</template>

<script setup>
import Navbar from '../Components/Navbar.vue';
import SideBar from '../Components/SideBar.vue';
import HomeContainer from '../Components/HomeContainer.vue';
</script>