<template>
  <Layout :auth="auth">
    <div class="max-w-7xl mx-auto">
      <h2 class="text-2xl font-bold text-gray-800 mb-6">Bienvenido a la Plataforma Alumni</h2>
      
      <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 mb-4">
          Este es un ejemplo de contenido principal utilizando el componente de layout.
          El layout integra el header y el sidebar en todas las páginas de la aplicación.
        </p>
        
        <div class="flex flex-col md:flex-row gap-4 mt-6">
          <div class="bg-blue-50 rounded-lg p-4 flex-1">
            <h3 class="text-lg font-semibold text-[#193CB8] mb-2">Próximos Eventos</h3>
            <p class="text-gray-600">Descubre los eventos exclusivos para nuestra comunidad alumni.</p>
          </div>
          
          <div class="bg-blue-50 rounded-lg p-4 flex-1">
            <h3 class="text-lg font-semibold text-[#193CB8] mb-2">Ofertas de Empleo</h3>
            <p class="text-gray-600">Explora oportunidades laborales seleccionadas para ti.</p>
          </div>
          
          <div class="bg-blue-50 rounded-lg p-4 flex-1">
            <h3 class="text-lg font-semibold text-[#193CB8] mb-2">Recursos</h3>
            <p class="text-gray-600">Accede a contenido exclusivo para tu desarrollo profesional.</p>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script>
import Layout from '@/Components/Layout.vue';

export default {
  components: {
    Layout
  },
  data() {
    return {
      auth: {
        user: {
          name: 'María García',
          email: 'maria.garcia@alumni.edu'
        }
      }
    };
  }
};
</script>

