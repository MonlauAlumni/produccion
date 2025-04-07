<template>
    <section class="py-20 bg-gradient-to-r from-[#193CB8] to-[#2a4fd6] text-white relative overflow-hidden">
      <!-- Formas decorativas -->
      <div class="absolute inset-0 z-0">
        <div class="absolute top-0 left-1/4 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-1/4 w-80 h-80 bg-white/5 rounded-full blur-3xl"></div>
      </div>
      
      <div class="container mx-auto px-6 relative z-10">
        <div class="text-center mb-16">
          <h2 class="text-3xl md:text-4xl font-bold mb-4">Nuestra comunidad en números</h2>
          <p class="text-lg text-white/80 max-w-3xl mx-auto">
            Descubre el impacto que estamos generando en la comunidad educativa y profesional.
          </p>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
          <div 
            v-for="(stat, index) in stats" 
            :key="index"
            class="text-center p-6 rounded-xl bg-white/10 backdrop-blur-sm"
          >
            <div class="flex flex-col items-center">
              <div class="text-5xl md:text-6xl font-bold mb-2">
                <span ref="countRefs" class="counter" :data-target="stat.value">0</span>
                <span v-if="stat.suffix">{{ stat.suffix }}</span>
              </div>
              <p class="text-lg text-white/90">{{ stat.label }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  
  const stats = [
    { value: 750, label: "Usuarios registrados", suffix: "+" },
    { value: 200, label: "Alumnos en formación", suffix: "" },
    { value: 600, label: "Empresas colaboradoras", suffix: "+" },
    { value: 10, label: "Años de experiencia", suffix: "" }
  ];
  
  const countRefs = ref([]);
  
  onMounted(() => {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const counters = document.querySelectorAll('.counter');
          
          counters.forEach(counter => {
            const target = parseInt(counter.getAttribute('data-target'));
            const duration = 2000; // ms
            const step = target / (duration / 16); // 60fps
            
            let current = 0;
            const updateCounter = () => {
              current += step;
              if (current < target) {
                counter.textContent = Math.floor(current);
                requestAnimationFrame(updateCounter);
              } else {
                counter.textContent = target;
              }
            };
            
            updateCounter();
          });
          
          // Desconectar el observer después de la animación
          observer.disconnect();
        }
      });
    }, { threshold: 0.5 });
    
    // Observar la sección
    const section = document.querySelector('section');
    if (section) {
      observer.observe(section);
    }
  });
  </script>