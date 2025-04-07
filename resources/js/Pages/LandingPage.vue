<template>
  <div class="landing-page">
    <!-- Navbar con transición suave -->
    <nav 
      class="fixed top-0 z-50 w-full transition-all duration-300"
      :class="[
        isScrolled ? 'py-3 bg-[#193CB8]/95 shadow-lg backdrop-blur-sm' : 'py-5 bg-transparent',
      ]"
    >
      <div class="container mx-auto px-6 flex justify-between items-center">
        <a href="/" class="text-white font-bold flex items-center gap-3 group">
          <div class="relative overflow-hidden rounded-lg transition-all duration-300 transform group-hover:scale-105">
            <img src="/public/images/logoMonlau.jpg" alt="Alumni" class="w-12 h-12 object-contain" />
          </div>
          <span class="text-2xl tracking-tight">Alumni</span>
        </a>
        
        <div class="flex gap-4 items-center">
          <a 
            v-if="!auth?.user" 
            href="/login-monlau" 
            class="text-white hover:text-white/90 transition-all px-4 py-2 rounded-md hover:bg-white/10"
          >
            <span class="flex items-center gap-2">
              <i class='bx bx-log-in'></i>
              <span>Iniciar Sesión</span>
            </span>
          </a>
          
          <a 
            v-if="!auth?.user" 
            href="/register" 
            class="bg-white text-[#193CB8] px-5 py-2 rounded-md shadow-md hover:shadow-lg transition-all font-medium flex items-center gap-2 hover:bg-gray-50"
          >
            <i class='bx bx-user-plus'></i>
            <span>Registrarse</span>
          </a>
        </div>
      </div>
    </nav>

    <!-- Hero Section Mejorado -->
    <HeroSection :images="images" />
    
    <!-- Sección de Características -->
    <FeaturesSection />
    
    <!-- Sección de Estadísticas -->
    <StatsSection />
    
    <!-- Sección de Showcase -->
    <ShowcaseSection :sections="sections" />
    

    

    
    <!-- Footer -->
    <Footer />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import HeroSection from '@/Components/Landing/HeroSection.vue';
import FeaturesSection from '../Components/Landing/FeaturesSection.vue';
import StatsSection from '../Components/Landing/StatsSection.vue';
import ShowcaseSection from '../Components/Landing/ShowcaseSection.vue';
import Footer from '../Components/Footer.vue';

// Imágenes para el carrusel
const images = ref([
  '/images/landing/landing-bg1.jpeg',
  '/images/landing/landing-bg2.jpg'
]);

// Secciones para el showcase
const sections = ref([
  {
    title: "Ofertas",
    image: '/images/landing/ofertas.png',
    description: "Accede a oportunidades laborales exclusivas para alumnos de Monlau. Explora ofertas de empresas seleccionadas especialmente para impulsar tu desarrollo profesional desde el primer día.",
    icon: 'bx-briefcase'
  },
  {
    title: "Alumni Connect",
    image: '/images/landing/connect.png',
    description: "Conéctate con otros miembros de la comunidad y comparte tus experiencias. Nuestra plataforma social te permite interactuar, crear grupos y participar en discusiones que te interesen.",
    icon: 'bx-group'
  },
  {
    title: "Calendario",
    image: '/images/landing/calendar.png',
    description: "Consulta nuestro calendario de eventos y no te pierdas ninguna actividad. Mantente al tanto de las fechas importantes y planifica tu participación en nuestros eventos y actividades comunitarias.",
    icon: 'bx-calendar'
  }
]);

// Estado para controlar el cambio de estilo del navbar al hacer scroll
const isScrolled = ref(false);

// Detectar scroll para cambiar el estilo del navbar
onMounted(() => {
  const handleScroll = () => {
    isScrolled.value = window.scrollY > 50;
  };
  
  window.addEventListener('scroll', handleScroll);
  
  // Limpiar el event listener al desmontar
  return () => {
    window.removeEventListener('scroll', handleScroll);
  };
});
</script>

<style>

.landing-page {
  --primary-color: #193CB8;
  --primary-light: #2a4fd6;
  --primary-dark: #122a8c;
  --accent-color: #FF6B35;
  --text-light: #F8F9FA;
  --text-dark: #212529;
  --bg-light: #FFFFFF;
  --bg-dark: #0F172A;
  --bg-gray: #F1F5F9;
  
  font-family: 'Inter', sans-serif;
  color: var(--text-dark);
}

/* Animaciones globales */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fadeInUp {
  animation: fadeInUp 0.6s ease-out forwards;
}

.delay-100 { animation-delay: 0.1s; }
.delay-200 { animation-delay: 0.2s; }
.delay-300 { animation-delay: 0.3s; }
.delay-400 { animation-delay: 0.4s; }
.delay-500 { animation-delay: 0.5s; }
</style>