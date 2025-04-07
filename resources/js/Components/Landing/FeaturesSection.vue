<template>
    <section id="features" class="py-20 bg-white">
      <div class="container mx-auto px-6">
        <div class="text-center mb-16">
          <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">¿Por qué elegir Alumni?</h2>
          <p class="text-lg text-gray-600 max-w-3xl mx-auto">
            Nuestra plataforma ofrece herramientas y recursos diseñados específicamente para conectar a estudiantes, graduados y empresas.
          </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div 
            v-for="(feature, index) in features" 
            :key="index"
            class="bg-gray-50 rounded-xl p-8 shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1"
          >
            <div class="w-14 h-14 bg-[#193CB8]/10 rounded-lg flex items-center justify-center mb-6">
              <i :class="['bx text-3xl text-[#193CB8]', feature.icon]"></i>
            </div>
            <h3 class="text-xl font-semibold mb-3 text-gray-900">{{ feature.title }}</h3>
            <p class="text-gray-600">{{ feature.description }}</p>
          </div>
        </div>
      </div>
    </section>
  </template>
  
  <script setup>
  const features = [
    {
      title: "Ofertas de Empleo Exclusivas",
      description: "Accede a ofertas de trabajo exclusivas de empresas que buscan específicamente perfiles de nuestra comunidad educativa.",
      icon: "bx-briefcase"
    },
    {
      title: "Networking Profesional",
      description: "Conecta con otros alumni, profesionales y empresas para expandir tu red de contactos y descubrir nuevas oportunidades.",
      icon: "bx-network-chart"
    },
    {
      title: "Desarrollo Profesional",
      description: "Accede a recursos, eventos y formaciones para seguir desarrollando tus habilidades y avanzar en tu carrera profesional.",
      icon: "bx-line-chart"
    }
  ];
  </script>