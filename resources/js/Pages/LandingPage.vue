<template>
        <nav class="bg-none fixed top-0 z-100 p-4 w-full flex justify-between items-center">

            <div class="flex gap-4 items-center">
                <a href="/" class="text-white text-3xl font-bold flex items-center gap-4"> 
                    <img href="/" src="/public/images/logo.png" alt="Alumni" class="w-17 h-16">
                    Alumni</a>
            </div>
            <ul class="flex gap-4 items-center">
                <li v-if="!$page.props.auth.user">
                    <a href="/login" class="text-white text-lg font-semibold">Iniciar Sesión</a>
                </li>
                <li v-if="!$page.props.auth.user">
                    <a href="/register"
                        class="text-white text-lg p-2 border-solid border-2 border-white rounded-3xl font-semibold">Registrarse</a>
                </li>
            </ul>

    </nav>
    <Carousel :images="images" />
</template>

<script setup>
import { ref } from 'vue';
import Navbar from '../Components/Navbar.vue';
import Carousel from '../Components/Carousel.vue';

const images = ref([
    '/images/landing1.jpeg',
    '/images/landing2.jpg',
    '/images/landing1.jpeg',
    'https://www.lavoz.com.ar/resizer/v2/I2A6DJW5AZHQ3MAHZZKM3HCEFM.jpg?quality=75&smart=true&auth=b0e4977d318601217fee6508c6b7c012b5450d17512f29296c9ff83f87760f00&width=980&height=640'
]);
</script>
