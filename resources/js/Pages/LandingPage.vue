<template>
    <nav
      class="fixed top-0 z-50 p-4 w-full flex justify-between items-center transition-all duration-300"
      :class="isScrolled ? 'bg-blue-800/80' : 'bg-transparent'"
    >
      <div class="flex gap-4 items-center">
        <a href="/" class="text-white text-3xl font-bold flex items-center gap-4">
          <img href="/" src="/public/images/logo.png" alt="Alumni" class="w-17 h-16">
          Alumni
        </a>
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
  
    <div ref="carouselWrapper">
      <Carousel :images="images" />
    </div>
    <StatsSection />
    <PageShowcase :sections="sections"/>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import Carousel from '../Components/Carousel.vue';
import StatsSection from '../Components/StatsSection.vue';
import PageShowcase from '../Components/PageShowcase.vue';

const images = ref([
    '/images/landing-bg1.jpeg',
    '/images/landing-bg2.jpg'
]);

const sections = ref([
    {
        title: "Blog",
        image: '/images/blog-test.png',
        description: "Descubre las últimas noticias y artículos de interés en nuestro blog. Aquí encontrarás información actualizada sobre diversos temas que te ayudarán a mantenerte informado y al día con las tendencias más recientes."
    },
    {
        title: "Social",
        image: 'images/social-test.png',
        description: "Conéctate con otros miembros de la comunidad y comparte tus experiencias. Nuestra plataforma social te permite interactuar, crear grupos y participar en discusiones que te interesen."
    },
    {
        title: "Calendar",
        image: 'images/calendar-test.png',
        description: "Consulta nuestro calendario de eventos y no te pierdas ninguna actividad. Mantente al tanto de las fechas importantes y planifica tu participación en nuestros eventos y actividades comunitarias."
    }
]);

const isScrolled = ref(false);
const carouselWrapper = ref(null);

const handleScroll = (entries) => {
    const [entry] = entries;
    isScrolled.value = !entry.isIntersecting;
};

onMounted(() => {
    if (carouselWrapper.value) {
        const observer = new IntersectionObserver(handleScroll, {
            root: null,
            threshold: 0.2 // Detecta cuando el 20% del Carousel aún está visible
        });
        observer.observe(carouselWrapper.value);
    }
});
</script>
