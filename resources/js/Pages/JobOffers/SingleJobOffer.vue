<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import NavBar from '@/Components/Navbar.vue'

const jobOffer = {
  id: 1,
  title: 'Desarrollador Full Stack',
  company: {
    name: 'Tech Solutions',
    logo: 'https://example.com/logo.png',
    industry: 'Tecnología'
  },
  requirements: 'Grado en informática o afines',
  experience: 'Al menos 3 años de experiencia en desarrollo web',
  description: '<p>Buscamos un desarrollador full-stack apasionado por la tecnología para unirse a nuestro equipo y contribuir a proyectos innovadores. Se valoran conocimientos en frameworks como Vue.js y Laravel.</p>',
  vacancies: 3,
  salary_min: 2500,
  salary_max: 4000,
  location: 'Madrid, España',
  job_type: 'remote',
  posted_date: '2023-11-15'
}

const skills = ref(['Vue.js', 'Laravel', 'PHP', 'MySQL', 'Git'])

const formatJobType = (type) => {
  const types = {
    onsite: 'Presencial',
    remote: 'Remoto',
    hybrid: 'Híbrido'
  }
  return types[type] || type
}

const formatSalaryRange = (min, max) => {
  if (!min && !max) return 'No especificado'
  if (min && max) return `${min}€ - ${max}€`
  return min ? `Desde ${min}€` : `Hasta ${max}€`
}

const applyToJob = () => {
  router.post(route('job-applications.store'), {
    job_offer_id: jobOffer.id
  })
}

// Calcular días desde publicación
const getDaysSincePosted = (dateString) => {
  const posted = new Date(dateString);
  const today = new Date();
  const diffTime = Math.abs(today - posted);
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  return diffDays;
}

const daysSincePosted = getDaysSincePosted(jobOffer.posted_date);
</script>

<template>
  <div class="min-h-screen bg-gray-50 flex flex-col">
    <NavBar />
    
    <!-- Hero Section with Company Info -->
    <div class="bg-gradient-to-r from-[#193CB8] to-[#2d50c7] text-white py-8">
      <div class="max-w-5xl mx-auto px-4">
        <div class="flex flex-col md:flex-row items-start gap-6">
          <!-- Company Logo -->
          <div class="w-20 h-20 bg-white rounded-xl shadow-lg flex items-center justify-center p-1 border-4 border-white">
            <img 
              v-if="jobOffer.company.logo" 
              :src="jobOffer.company.logo" 
              :alt="jobOffer.company.name"
              class="w-16 h-16 object-contain rounded-lg"
            />
            <i v-else class='bx bx-building text-4xl text-[#193CB8]'></i>
          </div>
          
          <!-- Job Title and Company Info -->
          <div class="flex-1">
            <div class="flex items-center text-sm mb-1 text-blue-100">
              <i class='bx bx-buildings mr-1'></i>
              {{ jobOffer.company.name }} · {{ jobOffer.company.industry }}
            </div>
            <h1 class="text-3xl font-bold mb-2">{{ jobOffer.title }}</h1>
            <div class="flex flex-wrap gap-4 text-sm mt-3">
              <div class="flex items-center">
                <i class='bx bx-map-pin mr-1'></i>
                {{ jobOffer.location }}
              </div>
              <div class="flex items-center">
                <i class='bx bx-time-five mr-1'></i>
                {{ formatJobType(jobOffer.job_type) }}
              </div>
              <div class="flex items-center">
                <i class='bx bx-money-withdraw mr-1'></i>
                {{ formatSalaryRange(jobOffer.salary_min, jobOffer.salary_max) }}
              </div>
              <div class="flex items-center">
                <i class='bx bx-calendar mr-1'></i>
                Publicado hace {{ daysSincePosted }} días
              </div>
            </div>
          </div>
          
          <!-- Apply Button -->
          <div class="mt-4 md:mt-0">
            <button 
              @click="applyToJob"
              class="bg-white text-[#193CB8] font-semibold px-6 py-3 rounded-lg shadow-lg hover:bg-blue-50 transition-all duration-300 flex items-center"
            >
              <i class='bx bx-send mr-2'></i>
              Inscribirme
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <div class="max-w-5xl mx-auto py-8 px-4">
      <div class="grid md:grid-cols-3 gap-6">
        <!-- Left Column - Main Content -->
        <div class="md:col-span-2 space-y-6">
          <!-- Description Section -->
          <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-[#193CB8]/10 to-white p-4 border-b">
              <h2 class="text-xl font-bold text-[#193CB8] flex items-center">
                <i class='bx bx-detail mr-2'></i>
                Descripción
              </h2>
            </div>
            <div class="p-6">
              <div class="prose max-w-none text-gray-700" v-html="jobOffer.description"></div>
            </div>
          </div>
          
          <!-- Requirements Section -->
          <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-[#193CB8]/10 to-white p-4 border-b">
              <h2 class="text-xl font-bold text-[#193CB8] flex items-center">
                <i class='bx bx-list-check mr-2'></i>
                Requisitos
              </h2>
            </div>
            <div class="p-6">
              <div class="grid md:grid-cols-2 gap-6 mb-6">
                <div class="bg-gray-50 p-5 rounded-lg border-l-4 border-[#193CB8] hover:shadow-md transition-shadow">
                  <h3 class="font-semibold mb-2 flex items-center text-[#193CB8]">
                    <i class='bx bx-book text-xl mr-2'></i>
                    Estudios mínimos
                  </h3>
                  <p class="text-gray-700">{{ jobOffer.requirements }}</p>
                </div>
                <div class="bg-gray-50 p-5 rounded-lg border-l-4 border-[#193CB8] hover:shadow-md transition-shadow">
                  <h3 class="font-semibold mb-2 flex items-center text-[#193CB8]">
                    <i class='bx bx-time text-xl mr-2'></i>
                    Experiencia mínima
                  </h3>
                  <p class="text-gray-700">{{ jobOffer.experience }}</p>
                </div>
              </div>
              
              <div>
                <h3 class="font-semibold mb-3 flex items-center text-[#193CB8]">
                  <i class='bx bx-code-alt text-xl mr-2'></i>
                  Conocimientos requeridos
                </h3>
                <div class="flex flex-wrap gap-2 mt-3">
                  <span 
                    v-for="skill in skills" 
                    :key="skill"
                    class="px-4 py-2 bg-gradient-to-r from-[#193CB8]/10 to-[#193CB8]/5 text-[#193CB8] rounded-full text-sm font-medium flex items-center shadow-sm hover:shadow-md transition-shadow"
                  >
                    <i class='bx bx-check-shield mr-1'></i>
                    {{ skill }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Right Column - Job Details -->
        <div class="space-y-6">
          <!-- Job Details Card -->
          <div class="bg-white rounded-xl shadow-md overflow-hidden sticky top-4">
            <div class="bg-gradient-to-r from-[#193CB8]/10 to-white p-4 border-b">
              <h3 class="font-bold text-[#193CB8] flex items-center">
                <i class='bx bx-info-circle mr-2'></i>
                Detalles del puesto
              </h3>
            </div>
            <div class="p-5 space-y-4">
              <div class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="w-10 h-10 rounded-full bg-[#193CB8]/10 flex items-center justify-center text-[#193CB8]">
                  <i class='bx bx-building-house text-xl'></i>
                </div>
                <div>
                  <div class="text-sm text-gray-500">Industria</div>
                  <div class="font-medium">{{ jobOffer.company.industry }}</div>
                </div>
              </div>
              
              <div class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="w-10 h-10 rounded-full bg-[#193CB8]/10 flex items-center justify-center text-[#193CB8]">
                  <i class='bx bx-briefcase text-xl'></i>
                </div>
                <div>
                  <div class="text-sm text-gray-500">Puesto</div>
                  <div class="font-medium">{{ jobOffer.title }}</div>
                </div>
              </div>
              
              <div class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="w-10 h-10 rounded-full bg-[#193CB8]/10 flex items-center justify-center text-[#193CB8]">
                  <i class='bx bx-user-plus text-xl'></i>
                </div>
                <div>
                  <div class="text-sm text-gray-500">Vacantes</div>
                  <div class="font-medium">{{ jobOffer.vacancies || 'No especificado' }}</div>
                </div>
              </div>
              
              <div class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="w-10 h-10 rounded-full bg-[#193CB8]/10 flex items-center justify-center text-[#193CB8]">
                  <i class='bx bx-money text-xl'></i>
                </div>
                <div>
                  <div class="text-sm text-gray-500">Salario</div>
                  <div class="font-medium">{{ formatSalaryRange(jobOffer.salary_min, jobOffer.salary_max) }}</div>
                </div>
              </div>
              
              <div class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="w-10 h-10 rounded-full bg-[#193CB8]/10 flex items-center justify-center text-[#193CB8]">
                  <i class='bx bx-map text-xl'></i>
                </div>
                <div>
                  <div class="text-sm text-gray-500">Ubicación</div>
                  <div class="font-medium">{{ jobOffer.location }}</div>
                </div>
              </div>
              
              <div class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="w-10 h-10 rounded-full bg-[#193CB8]/10 flex items-center justify-center text-[#193CB8]">
                  <i class='bx bx-time text-xl'></i>
                </div>
                <div>
                  <div class="text-sm text-gray-500">Modalidad</div>
                  <div class="font-medium">{{ formatJobType(jobOffer.job_type) }}</div>
                </div>
              </div>
              
              <div class="pt-4">
                <button 
                  @click="applyToJob"
                  class="w-full bg-gradient-to-r from-[#193CB8] to-[#2d50c7] text-white font-semibold py-3 px-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 flex items-center justify-center"
                >
                  <i class='bx bx-send mr-2'></i>
                  Inscribirme a la oferta
                </button>
              </div>
              
              <div class="text-center text-sm text-gray-500 pt-2">
                <i class='bx bx-shield-quarter'></i> Tu solicitud será procesada de forma segura
              </div>
            </div>
          </div>
          
          <!-- Company Card -->
          <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-[#193CB8]/10 to-white p-4 border-b">
              <h3 class="font-bold text-[#193CB8] flex items-center">
                <i class='bx bx-building mr-2'></i>
                Sobre la empresa
              </h3>
            </div>
            <div class="p-5 flex items-center gap-4">
              <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center">
                <img 
                  v-if="jobOffer.company.logo" 
                  :src="jobOffer.company.logo" 
                  :alt="jobOffer.company.name"
                  class="w-12 h-12 object-contain"
                />
                <i v-else class='bx bx-building text-3xl text-gray-400'></i>
              </div>
              <div>
                <h4 class="font-semibold text-lg">{{ jobOffer.company.name }}</h4>
                <p class="text-gray-600">{{ jobOffer.company.industry }}</p>
                <a href="#" class="text-[#193CB8] text-sm flex items-center mt-1 hover:underline">
                  <i class='bx bx-link-external mr-1'></i> Ver perfil de empresa
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Bottom Action Bar -->
      <div class="mt-8 bg-white rounded-xl shadow-md p-6 flex flex-col md:flex-row items-center justify-between">
        <div>
          <h3 class="font-bold text-lg text-[#193CB8]">¿Te interesa esta oferta?</h3>
          <p class="text-gray-600">Inscríbete ahora y forma parte del proceso de selección</p>
        </div>
        <div class="mt-4 md:mt-0 flex gap-3">
          <button class="bg-gray-100 text-gray-700 font-medium py-2 px-4 rounded-lg hover:bg-gray-200 transition-colors flex items-center">
            <i class='bx bx-bookmark mr-1'></i> Guardar
          </button>
          <button 
            @click="applyToJob"
            class="bg-gradient-to-r from-[#193CB8] to-[#2d50c7] text-white font-semibold py-2 px-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 flex items-center"
          >
            <i class='bx bx-send mr-2'></i>
            Inscribirme
          </button>
        </div>
      </div>
    </div>
  </div>  
</template>

<style>
/* Estilos adicionales para mejorar la apariencia */
.prose p {
  margin-bottom: 1rem;
  line-height: 1.7;
}

/* Animación sutil para botones */
button {
  transform: translateY(0);
  transition: transform 0.2s ease;
}

button:hover {
  transform: translateY(-2px);
}

/* Efecto de hover para las tarjetas */
.bg-white.rounded-xl {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.bg-white.rounded-xl:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}
</style>