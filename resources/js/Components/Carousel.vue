<template>
    <section class="hero relative bg-cover bg-center text-white text-center">
        <div class="carousel absolute inset-y-0 inset-x-0 w-full h-full">
            <div class="carousel-inner relative w-full h-full overflow-hidden">
                <div class="carousel-item absolute opacity-0 transition-opacity duration-1000 ease-linear" v-for="(image, index) in images" :key="index">
                    <img :src="image" class="w-full h-full object-cover">
                </div>
                <div class="absolute inset-0 bg-black opacity-50"></div>
            </div>
        </div>
        <header class="absolute inset-y-0 inset-x-0 flex flex-col items-center justify-center">
            <h1 class="text-4xl font-bold">Mi Landing Page</h1>
            <p class="mt-4 text-lg">Un breve mensaje de bienvenida</p>
            <button class="mt-6 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700">Comenzar</button>
        </header>
    </section>
</template>

<script setup>
import { ref, onMounted, defineProps, onUnmounted } from 'vue';

const props = defineProps({
    images: {
        type: Array,
        required: true
    }
});

let currentIndex = ref(0);
let intervalId = null;

const showImage = (index) => {
    const items = document.querySelectorAll('.carousel-item');
    items.forEach((item, i) => {
        item.style.opacity = i === index ? '1' : '0';
    });
};

const next = () => {
    currentIndex.value = (currentIndex.value + 1) % props.images.length;
    showImage(currentIndex.value);
};

onMounted(() => {
    showImage(currentIndex.value);
    intervalId = setInterval(next, 3000); // Change image every 3 seconds
});

onUnmounted(() => {
    clearInterval(intervalId);
});
</script>

<style scoped>
.hero {
    @apply h-screen relative;
}

.carousel {
    @apply absolute inset-y-0 inset-x-0 w-full h-full;
}

.carousel-inner {
    @apply relative w-full h-full;
}

.carousel-item {
    @apply absolute w-full h-full transition-opacity duration-1000 ease-linear;
}
</style>

