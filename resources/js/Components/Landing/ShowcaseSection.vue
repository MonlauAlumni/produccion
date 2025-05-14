<template>
    <section class="py-20 bg-gray-50">
      <div class="container mx-auto px-6">
        <div class="text-center mb-16">
          <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Explora nuestras funcionalidades</h2>
          <p class="text-lg text-gray-600 max-w-3xl mx-auto">
            Descubre todo lo que Alumni puede ofrecerte para impulsar tu desarrollo profesional y personal.
          </p>
        </div>
        
        <div class="space-y-24">
          <div 
            v-for="(section, index) in sections" 
            :key="index"
            class="showcase-item"
          >
            <div 
              :class="[
                'flex flex-col md:flex-row items-center gap-12',
                index % 2 === 1 ? 'md:flex-row-reverse' : ''
              ]"
            >
              <!-- Imagen con efecto -->
              <div class="w-full md:w-1/2 relative">
                <div class="relative rounded-xl overflow-hidden shadow-xl group">
                  <img 
                    :src="section.image" 
                    :alt="section.title" 
                    class="w-full h-auto object-cover transition-transform duration-700 group-hover:scale-105"
                  />
                  <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-70"></div>
                  <div class="absolute bottom-0 left-0 right-0 p-6">
                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 bg-[#193CB8] rounded-lg flex items-center justify-center">
                        <i :class="['bx text-xl text-white', section.icon]"></i>
                      </div>
                      <h3 class="text-2xl font-bold text-white">{{ section.title }}</h3>
                    </div>
                  </div>
                </div>
                
                <!-- Elemento decorativo -->
                <div 
                  :class="[
                    'absolute -z-10 w-full h-full rounded-xl',
                    index % 2 === 0 ? '-right-5 -bottom-5' : '-left-5 -bottom-5',
                    index === 0 ? 'bg-[#193CB8]/10' : index === 1 ? 'bg-[#FF6B35]/10' : 'bg-[#193CB8]/10'
                  ]"
                ></div>
              </div>
              
              <!-- Contenido -->
              <div class="w-full md:w-1/2">
                <h3 class="text-2xl md:text-3xl font-bold mb-4 text-gray-900">{{ section.title }}</h3>
                <p class="text-lg text-gray-600 mb-6 leading-relaxed">{{ section.description }}</p>
               
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </template>
  
  <script setup>
  defineProps({
    sections: {
      type: Array,
      required: true
    }
  });
  </script>
  
  <style scoped>
  .showcase-item {
    opacity: 0;
    transform: translateY(30px);
    transition: opacity 0.8s ease, transform 0.8s ease;
  }
  
  .showcase-item.visible {
    opacity: 1;
    transform: translateY(0);
  }
  </style>
  
  <script>
  export default {
    mounted() {
      // Animación al hacer scroll
      const observerOptions = {
        threshold: 0.2
      };
      
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('visible');
          }
        });
      }, observerOptions);
      
      const showcaseItems = document.querySelectorAll('.showcase-item');
      showcaseItems.forEach(item => {
        observer.observe(item);
      });
    }
  }
  </script>