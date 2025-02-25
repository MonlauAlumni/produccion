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
      job_type: 'remote'
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
    </script>
    
  
    
<template>
    <div class="min-h-screen bg-gray-50 flex flex-col">
        <NavBar />
    <div class="max-w-4xl mx-auto py-8 px-4">
      <!-- Header Section -->
      <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-start justify-between">
          <div class="flex gap-4">
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
              <h1 class="text-2xl font-bold text-gray-900">{{ jobOffer.title }}</h1>
              <p class="text-gray-600">{{ jobOffer.company.name }}</p>
            </div>
          </div>
          <button 
            @click="applyToJob"
            class="bg-blue-600 cursor-pointer hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors"
          >
            Inscribirme a la oferta
          </button>
        </div>
      </div>
  
      <!-- Requirements Section -->
      <div class="bg-gray-50 rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Requisitos</h2>
        
        <div class="grid md:grid-cols-2 gap-6 mb-6">
          <div>
            <h3 class="font-medium mb-2">Estudios mínimos</h3>
            <p class="text-gray-600">{{ jobOffer.requirements }}</p>
          </div>
          <div>
            <h3 class="font-medium mb-2">Experiencia mínima</h3>
            <p class="text-gray-600">{{ jobOffer.experience }}</p>
          </div>
        </div>
  
        <div class="mb-6">
          <h3 class="font-medium mb-2">Conocimientos básicos</h3>
          <div class="flex flex-wrap gap-2">
            <span 
              v-for="skill in skills" 
              :key="skill"
              class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm"
            >
              {{ skill }}
            </span>
          </div>
        </div>
      </div>
  
      <!-- Description Section -->
      <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Descripción</h2>
        <div class="prose max-w-none text-gray-600" v-html="jobOffer.description"></div>
      </div>
  
      <!-- Job Details Section -->
      <div class="grid gap-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
          <h3 class="font-medium mb-4">Detalles del puesto</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Left Side (3 items) -->
            <div class="flex flex-col space-y-4">
              <div class="flex items-center gap-2">
                <i class='bx bx-building-house text-blue-600'></i>
                <span><strong>Industria:</strong> {{ jobOffer.company.industry }}</span>
              </div>
              <div class="flex items-center gap-2">
                <i class='bx bx-briefcase text-blue-600'></i>
                <span><strong>Puesto:</strong> {{ jobOffer.title }}</span>
              </div>
              <div class="flex items-center gap-2">
                <i class='bx bx-user-plus text-blue-600'></i>
                <span><strong>Vacantes:</strong> {{ jobOffer.vacancies || 'No especificado' }}</span>
              </div>
            </div>
          
            <!-- Right Side (3 items) -->
            <div class="flex flex-col space-y-4">
              <div class="flex items-center gap-2">
                <i class='bx bx-money text-blue-600'></i>
                <span><strong>Salario:</strong> {{ formatSalaryRange(jobOffer.salary_min, jobOffer.salary_max) }}</span>
              </div>
              <div class="flex items-center gap-2">
                <i class='bx bx-map text-blue-600'></i>
                <span><strong>Ubicación:</strong> {{ jobOffer.location }}</span>
              </div>
              <div class="flex items-center gap-2">
                <i class='bx bx-time text-blue-600'></i>
                <span><strong>Modalidad:</strong> {{ formatJobType(jobOffer.job_type) }}</span>
              </div>
            </div>
          </div>
          
        </div>
      </div>
    </div>
    </div>  
  </template>
  
