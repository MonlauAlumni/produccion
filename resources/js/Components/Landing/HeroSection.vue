<template>
    <section class="relative h-screen overflow-hidden">
      <!-- Carrusel de fondo -->
      <div class="absolute inset-0 z-0">
        <div class="relative h-full w-full bg-blue-600">
          <transition-group name="fade">
            <div 
              v-for="(image, index) in images" 
              :key="index" 
              v-show="currentIndex === index"
              class="absolute inset-0 h-full w-full"
            >
              <img 
                :src="image" 
                alt="Background" 
                class="h-full w-full object-cover"
              />
              <div class="absolute inset-0 bg-gradient-to-b from-[#193CB8]/80 to-[#0F172A]/90"></div>
            </div>
          </transition-group>
        </div>
      </div>
      
      <!-- Formas decorativas -->
      <div class="absolute inset-0 z-10 overflow-hidden">
        <div class="absolute -top-20 -left-20 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
        <div class="absolute top-1/3 -right-20 w-80 h-80 bg-[#193CB8]/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-1/4 w-96 h-96 bg-[#193CB8]/10 rounded-full blur-3xl"></div>
      </div>
      
      <!-- Contenido del hero -->
      <div class="relative z-20 h-full container mx-auto px-6 flex flex-col justify-center items-center text-center text-white">
        <h1 class="text-5xl md:text-6xl font-bold mb-6 opacity-0 animate-fadeInUp">
          Conecta con tu futuro profesional
        </h1>
        
        <p class="text-xl md:text-2xl max-w-3xl mx-auto mb-10 text-white/90 opacity-0 animate-fadeInUp delay-200">
          La plataforma que conecta a estudiantes, alumni y empresas para crear oportunidades profesionales y fortalecer la comunidad educativa.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 opacity-0 animate-fadeInUp delay-300">
          <a 
            href="/register" 
            class="px-8 py-3 bg-white text-[#193CB8] rounded-lg font-medium shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1"
          >
            Únete ahora
          </a>
          <a 
            href="#features" 
            class="px-8 py-3 bg-transparent border-2 border-white/30 text-white rounded-lg font-medium hover:bg-white/10 transition-all"
          >
            Descubre más
          </a>
        </div>
        
        <!-- Indicadores de carrusel -->
        <div class="absolute bottom-10 left-0 right-0 flex justify-center gap-2 opacity-0 animate-fadeInUp delay-400">
          <button 
            v-for="(_, index) in images" 
            :key="index"
            @click="setCurrentIndex(index)"
            :class="[
              'w-3 h-3 rounded-full transition-all',
              currentIndex === index ? 'bg-white scale-125' : 'bg-white/40 hover:bg-white/60'
            ]"
          ></button>
        </div>
        
        <!-- Scroll indicator -->
        <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 animate-bounce opacity-0 animate-fadeInUp delay-500">
          <i class='bx bx-chevron-down text-3xl'></i>
        </div>
      </div>
    </section>
  </template>
  
  <script setup>
  import { ref, onMounted, onUnmounted } from 'vue';
  
  const props = defineProps({
    images: {
      type: Array,
      required: true
    }
  });
  
  const currentIndex = ref(0);
  let intervalId = null;
  
  const nextSlide = () => {
    currentIndex.value = (currentIndex.value + 1) % props.images.length;
  };
  
  const setCurrentIndex = (index) => {
    currentIndex.value = index;
  };
  
  onMounted(() => {
    // Iniciar el carrusel automático
    intervalId = setInterval(nextSlide, 5000);
    
    // Iniciar las animaciones
    const animatedElements = document.querySelectorAll('.animate-fadeInUp');
    animatedElements.forEach(el => {
      el.style.opacity = '1';
    });
  });
  
  onUnmounted(() => {
    clearInterval(intervalId);
  });
  </script>
  
  <style scoped>
  .fade-enter-active,
  .fade-leave-active {
    transition: opacity 1.5s ease;
  }
  
  .fade-enter-from,
  .fade-leave-to {
    opacity: 0;
  }
  </style>