<template>
  <nav class="fixed top-0 z-50 p-4 w-full flex justify-between items-center transition-all duration-300"
    :class="isScrolled ? 'bg-blue-800/80' : 'bg-transparent'">
    <div class="flex gap-4 items-center">
      <a href="/" class="text-white text-3xl font-bold flex items-center gap-4">
        <img href="/" src="/public/images/logo.png" alt="Alumni" class="w-17 h-16">
        Alumni
      </a>
    </div>
    <ul class="flex gap-4 items-center">
      <li v-if="!auth?.user">
        <a href="/login-monlau" class="text-white hover:text-white/90 transition-colors font-medium flex items-center">
          <i class='bx bx-log-in mr-1'></i> Iniciar Sesión
        </a>
      </li>
      <li v-if="!auth?.user">
        <a href="/register" class="bg-white text-black px-4 py-2 rounded-md shadow-sm hover:bg-blue-50 transition-colors font-medium flex items-center">
          <i class='bx bx-user-plus mr-1'></i> Registrarse
        </a>
      </li>
    </ul>
  </nav>

  <div ref="carouselWrapper">
    <Carousel :images="images" />
  </div>
  <StatsSection />
  <PageShowcase :sections="sections" />
  <Footer />
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import Carousel from '../Components/Carousel.vue';
import StatsSection from '../Components/StatsSection.vue';
import PageShowcase from '../Components/PageShowcase.vue';
import Footer from '../Components/Footer.vue';

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
