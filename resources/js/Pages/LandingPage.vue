<template>
  <div class="landing-page">
    <!-- Navbar con transición suave -->
   <nav class="fixed top-0 z-50 w-full transition-all duration-300"
  :class="[isScrolled ? 'py-3 bg-[#193CB8]/95 shadow-lg backdrop-blur-sm' : 'py-5 md:py-5 bg-transparent']">
  <div class="container mx-auto px-6 flex justify-between items-center">
    <!-- Logo -->
    <a href="/" class="text-white font-bold flex items-center gap-3 group">
      <div class="relative overflow-hidden rounded-lg transition-all duration-300 transform group-hover:scale-105">
        <img src="/public/images/logoMonlau.jpg" alt="Alumni" class="w-12 h-12 object-contain" />
      </div>
      <span class="text-2xl tracking-tight">Alumni</span>
    </a>

    <!-- Botón hamburguesa (solo visible en móvil) -->
    <button class="text-white md:hidden text-3xl focus:outline-none" @click="isMobileMenuOpen = !isMobileMenuOpen">
      <i :class="isMobileMenuOpen ? 'bx bx-x' : 'bx bx-menu'"></i>
    </button>

    <!-- Menú Desktop -->
    <div class="hidden md:flex gap-4 items-center">
      <template v-if="!auth?.user">
        <!-- Menú desplegable -->
        <div class="relative">
          <button @click="isLoginMenuOpen = !isLoginMenuOpen"
            class="bg-white text-[#193CB8] px-5 py-2 rounded-md shadow-md hover:shadow-lg transition-all font-medium flex items-center gap-2"
            aria-haspopup="true" aria-expanded="isLoginMenuOpen" aria-label="Menú de inicio de sesión">
            <i class='bx bx-log-in text-lg'></i>
            <span>Iniciar Sesión</span>
            <i class='bx bx-chevron-down transition-transform' :class="isLoginMenuOpen ? 'rotate-180' : ''"></i>
          </button>

          <!-- Desplegable -->
          <div v-if="isLoginMenuOpen"
            class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg py-2 z-50 animate-fadeIn"
            role="menu" aria-orientation="vertical">
            <a href="/login-monlau"
              class="flex items-center gap-3 px-4 py-3 hover:bg-gray-100 text-gray-800 w-full text-left"
              role="menuitem">
              <div class="bg-blue-100 flex p-2 rounded-full">
                <i class='bx bx-user text-[#193CB8] text-xl'></i>
              </div>
              <div>
                <div class="font-medium">Alumno</div>
                <div class="text-xs text-gray-500">Para estudiantes y graduados</div>
              </div>
            </a>

            <div class="border-t border-gray-200 my-1"></div>

            <a href="/login"
              class="flex items-center gap-3 px-4 py-3 hover:bg-gray-100 text-gray-800 w-full text-left"
              role="menuitem">
              <div class="bg-amber-100 p-2 flex rounded-full">
                <i class='bx bx-building-house text-amber-600 text-xl'></i>
              </div>
              <div>
                <div class="font-medium">Empresa</div>
                <div class="text-xs text-gray-500">Para reclutadores y empresas</div>
              </div>
            </a>
          </div>
        </div>

        <a href="/register-company"
          class="bg-[#193CB8] text-white px-5 py-2 rounded-md shadow-md hover:shadow-lg transition-all font-medium flex items-center gap-2 hover:bg-[#122a8c]">
          <i class='bx bx-user-plus'></i>
          <span>Registrarse</span>
        </a>
      </template>
    </div>
  </div>

  <!-- Menú Móvil -->
  <div v-if="isMobileMenuOpen"
    class="absolute md:hidden w-full bg-white px-6 py-4 shadow-md space-y-3 top-[72px] z-50">
    <template v-if="!auth?.user">
      <a href="/login-monlau"
        class="flex items-center gap-3 text-[#193CB8] hover:underline">
        <i class='bx bx-user'></i>
        Alumno
      </a>
      <a href="/login"
        class="flex items-center gap-3 text-[#193CB8] hover:underline">
        <i class='bx bx-building-house'></i>
        Empresa
      </a>
      <a href="/register-company"
        class="flex items-center gap-3 text-white bg-[#193CB8] px-4 py-2 rounded-md font-medium hover:bg-[#122a8c]">
        <i class='bx bx-user-plus'></i>
        Registrarse
      </a>
    </template>
  </div>
</nav>

    <!-- Hero Section Mejorado -->
    <HeroSection :images="images" />

    <!-- Sección de Características -->
    <FeaturesSection />

    <!-- Sección de Estadísticas -->
    <StatsSection :userCount="userCount.value" :currentStudentCount="currentStudentCount.value"
      :companyCount="companyCount.value" />

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
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const userCount = ref(page.props.userCount);
const companyCount = ref(page.props.companyCount);
const currentStudentCount = ref(page.props.currentStudentCount);

const isMobileMenuOpen = ref(false);
const isLoginMenuOpen = ref(false);
const images = ref([
  '/images/landing/landing-bg1.jpeg',
  '/images/landing/landing-bg2.jpg',
  '/images/landing/landing-bg3.jpg',
  '/images/landing/landing-bg4.jpg',
  '/images/landing/landing-bg5.jpg',



]);

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
  const handleClickOutside = (event) => {
    const loginMenu = document.getElementById('login-menu');
    if (isLoginMenuOpen.value && loginMenu && !loginMenu.contains(event.target)) {
      isLoginMenuOpen.value = false;
    }
  };

  document.addEventListener('click', handleClickOutside);
  // Limpiar el event listener al desmontar
  return () => {
    document.removeEventListener('click', handleClickOutside);

    window.removeEventListener('scroll', handleScroll);
  };

});
</script>

<style>
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fadeIn {
  animation: fadeIn 0.2s ease-out forwards;
}

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

.delay-100 {
  animation-delay: 0.1s;
}

.delay-200 {
  animation-delay: 0.2s;
}

.delay-300 {
  animation-delay: 0.3s;
}

.delay-400 {
  animation-delay: 0.4s;
}

.delay-500 {
  animation-delay: 0.5s;
}
</style>