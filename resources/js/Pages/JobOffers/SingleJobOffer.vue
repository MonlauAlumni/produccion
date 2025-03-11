<script setup>
  import { ref } from 'vue'
  import { router } from '@inertiajs/vue3'
  import Layout from '@/Components/Layout.vue'
  
  const jobOffer = {
    id: 1,
    title: 'Desarrollador Full Stack',
    company: {
      name: 'Tech Solutions',
      logo: 'https://example.com/logo.png',
      industry: 'Tecnología',
      employees: '50-100 empleados',
      website: 'https://techsolutions.com'
    },
    requirements: 'Grado en informática o afines',
    experience: 'Al menos 3 años de experiencia en desarrollo web',
    description: '<p>Buscamos un desarrollador full-stack apasionado por la tecnología para unirse a nuestro equipo y contribuir a proyectos innovadores. Se valoran conocimientos en frameworks como Vue.js y Laravel.</p><p>El candidato ideal tendrá experiencia en desarrollo de aplicaciones web modernas y estará familiarizado con metodologías ágiles de desarrollo.</p><p>Ofrecemos un ambiente de trabajo dinámico, posibilidad de teletrabajo y un plan de carrera personalizado.</p>',
    responsibilities: [
      'Desarrollo de nuevas funcionalidades para aplicaciones web',
      'Mantenimiento y mejora de código existente',
      'Colaboración con el equipo de diseño para implementar interfaces de usuario',
      'Participación en reuniones de planificación y revisión de sprints'
    ],
    benefits: [
      'Horario flexible',
      'Posibilidad de teletrabajo',
      'Formación continua',
      'Seguro médico privado'
    ],
    vacancies: 3,
    salary_min: 2500,
    salary_max: 4000,
    location: 'Madrid, España',
    job_type: 'remote',
    posted_date: '2023-11-15',
    deadline: '2023-12-15'
  }
  
  const skills = ref(['Vue.js', 'Laravel', 'PHP', 'MySQL', 'Git', 'JavaScript', 'HTML/CSS', 'API REST'])
  
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
  
  // Calcular días restantes hasta la fecha límite
  const getDaysRemaining = (dateString) => {
    const deadline = new Date(dateString);
    const today = new Date();
    const diffTime = Math.abs(deadline - today);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays;
  }
  
  const daysRemaining = getDaysRemaining(jobOffer.deadline);
  </script>
  
  <template>
    <Layout>
    <div class="min-h-screen bg-gray-50 flex flex-col">
   
      
      <!-- Header Section with Company Info -->
      <div class="bg-[#193CB8] text-white py-6 border-b-4 border-[#193CB8]/20">
        <div class="max-w-5xl mx-auto px-4">
          <div class="flex flex-col md:flex-row items-start gap-5">
            <!-- Company Logo -->
            <div class="w-20 h-20 bg-white rounded-lg shadow-md flex items-center justify-center p-1 border-2 border-white">
              <img 
                v-if="jobOffer.company.logo" 
                :src="jobOffer.company.logo" 
                :alt="jobOffer.company.name"
                class="w-16 h-16 object-contain rounded"
              />
              <i v-else class='bx bx-building text-4xl text-[#193CB8]'></i>
            </div>
            
            <!-- Job Title and Company Info -->
            <div class="flex-1">
              <div class="flex items-center text-sm mb-1 text-blue-100">
                <i class='bx bx-buildings mr-1'></i>
                {{ jobOffer.company.name }} · {{ jobOffer.company.industry }}
              </div>
              <h1 class="text-2xl font-bold mb-2">{{ jobOffer.title }}</h1>
              <div class="flex flex-wrap gap-x-4 gap-y-2 text-sm mt-2">
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
                  Cierre en {{ daysRemaining }} días
                </div>
              </div>
            </div>
            
            <!-- Apply Button -->
            <div class="mt-4 md:mt-0 flex flex-col items-end">
              <button 
                @click="applyToJob"
                class="bg-white text-[#193CB8] font-semibold px-6 py-2 rounded-lg shadow-md hover:bg-blue-50 flex items-center"
              >
                <i class='bx bx-send mr-2'></i>
                Inscribirme
              </button>
              <div class="text-xs text-blue-100 mt-2">
                <i class='bx bx-user-plus'></i> {{ jobOffer.vacancies }} vacantes disponibles
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Quick Info Bar -->
      <div class="bg-white border-b">
        <div class="max-w-5xl mx-auto px-4 py-3">
          <div class="flex flex-wrap justify-between text-sm">
            <div class="flex items-center mr-6 mb-2 md:mb-0">
              <i class='bx bx-briefcase text-[#193CB8] mr-1'></i>
              <span class="text-gray-500 mr-1">Experiencia:</span>
              <span class="font-medium">{{ jobOffer.experience.split(' ')[0] }}</span>
            </div>
            <div class="flex items-center mr-6 mb-2 md:mb-0">
              <i class='bx bx-book text-[#193CB8] mr-1'></i>
              <span class="text-gray-500 mr-1">Estudios:</span>
              <span class="font-medium">{{ jobOffer.requirements.split(' ')[0] }}</span>
            </div>
            <div class="flex items-center mr-6 mb-2 md:mb-0">
              <i class='bx bx-user-plus text-[#193CB8] mr-1'></i>
              <span class="text-gray-500 mr-1">Vacantes:</span>
              <span class="font-medium">{{ jobOffer.vacancies }}</span>
            </div>
            <div class="flex items-center mb-2 md:mb-0">
              <i class='bx bx-building-house text-[#193CB8] mr-1'></i>
              <span class="text-gray-500 mr-1">Empresa:</span>
              <span class="font-medium">{{ jobOffer.company.employees }}</span>
            </div>
          </div>
        </div>
      </div>
      
      <div class="max-w-5xl mx-auto py-6 px-4">
        <div class="grid md:grid-cols-3 gap-6">
          <!-- Left Column - Main Content -->
          <div class="md:col-span-2 space-y-5">
            <!-- Description Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
              <div class="border-b border-gray-200 p-4 flex items-center">
                <i class='bx bx-detail text-xl text-[#193CB8] mr-2'></i>
                <h2 class="text-lg font-bold text-gray-800">Descripción</h2>
              </div>
              <div class="p-5">
                <div class="prose max-w-none text-gray-700" v-html="jobOffer.description"></div>
              </div>
            </div>
            
            <!-- Responsibilities Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
              <div class="border-b border-gray-200 p-4 flex items-center">
                <i class='bx bx-list-ul text-xl text-[#193CB8] mr-2'></i>
                <h2 class="text-lg font-bold text-gray-800">Responsabilidades</h2>
              </div>
              <div class="p-5">
                <ul class="space-y-2">
                  <li v-for="(responsibility, index) in jobOffer.responsibilities" :key="index" class="flex items-start">
                    <i class='bx bx-check-circle text-[#193CB8] mt-1 mr-2'></i>
                    <span>{{ responsibility }}</span>
                  </li>
                </ul>
              </div>
            </div>
            
            <!-- Requirements Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
              <div class="border-b border-gray-200 p-4 flex items-center">
                <i class='bx bx-list-check text-xl text-[#193CB8] mr-2'></i>
                <h2 class="text-lg font-bold text-gray-800">Requisitos</h2>
              </div>
              <div class="p-5">
                <div class="grid md:grid-cols-2 gap-5 mb-5">
                  <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-[#193CB8]">
                    <h3 class="font-semibold mb-2 flex items-center text-gray-800">
                      <i class='bx bx-book text-[#193CB8] mr-2'></i>
                      Estudios mínimos
                    </h3>
                    <p class="text-gray-700">{{ jobOffer.requirements }}</p>
                  </div>
                  <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-[#193CB8]">
                    <h3 class="font-semibold mb-2 flex items-center text-gray-800">
                      <i class='bx bx-time text-[#193CB8] mr-2'></i>
                      Experiencia mínima
                    </h3>
                    <p class="text-gray-700">{{ jobOffer.experience }}</p>
                  </div>
                </div>
                
                <div>
                  <h3 class="font-semibold mb-3 flex items-center text-gray-800">
                    <i class='bx bx-code-alt text-[#193CB8] mr-2'></i>
                    Conocimientos requeridos
                  </h3>
                  <div class="flex flex-wrap gap-2 mt-2">
                    <span 
                      v-for="skill in skills" 
                      :key="skill"
                      class="px-3 py-1.5 bg-[#193CB8]/10 text-[#193CB8] rounded-md text-sm font-medium flex items-center border border-[#193CB8]/20"
                    >
                      <i class='bx bx-check mr-1'></i>
                      {{ skill }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Benefits Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
              <div class="border-b border-gray-200 p-4 flex items-center">
                <i class='bx bx-gift text-xl text-[#193CB8] mr-2'></i>
                <h2 class="text-lg font-bold text-gray-800">Beneficios</h2>
              </div>
              <div class="p-5">
                <div class="grid grid-cols-2 gap-3">
                  <div v-for="(benefit, index) in jobOffer.benefits" :key="index" class="flex items-center bg-gray-50 p-3 rounded-lg">
                    <i class='bx bx-badge-check text-[#193CB8] mr-2'></i>
                    <span>{{ benefit }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Right Column - Job Details and Company Info -->
          <div class="space-y-5">
            <!-- Action Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
              <div class="border-b border-gray-200 p-4 flex items-center">
                <i class='bx bx-calendar-check text-xl text-[#193CB8] mr-2'></i>
                <h3 class="font-bold text-gray-800">Aplicar a esta oferta</h3>
              </div>
              <div class="p-5">
                <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-100">
                  <div class="text-sm text-gray-500">Fecha límite:</div>
                  <div class="font-medium flex items-center">
                    <i class='bx bx-time-five text-[#193CB8] mr-1'></i>
                    {{ jobOffer.deadline }}
                  </div>
                </div>
                
                <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-100">
                  <div class="text-sm text-gray-500">Vacantes:</div>
                  <div class="font-medium flex items-center">
                    <i class='bx bx-user-plus text-[#193CB8] mr-1'></i>
                    {{ jobOffer.vacancies }}
                  </div>
                </div>
                
                <div class="flex items-center justify-between mb-4">
                  <div class="text-sm text-gray-500">Proceso:</div>
                  <div class="font-medium flex items-center">
                    <i class='bx bx-check-shield text-[#193CB8] mr-1'></i>
                    Rápido
                  </div>
                </div>
                
                <button 
                  @click="applyToJob"
                  class="w-full bg-[#193CB8] text-white font-semibold py-3 px-6 rounded-lg shadow-sm hover:bg-[#142d8c] flex items-center justify-center mt-3"
                >
                  <i class='bx bx-send mr-2'></i>
                  Inscribirme a la oferta
                </button>
                
                <div class="flex items-center justify-center mt-3 text-sm text-gray-500">
                  <i class='bx bx-shield-quarter mr-1'></i> Aplicación segura
                </div>
              </div>
            </div>
            
            <!-- Company Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
              <div class="border-b border-gray-200 p-4 flex items-center">
                <i class='bx bx-building text-xl text-[#193CB8] mr-2'></i>
                <h3 class="font-bold text-gray-800">Sobre la empresa</h3>
              </div>
              <div class="p-5">
                <div class="flex items-center gap-3 mb-4 pb-4 border-b border-gray-100">
                  <div class="w-14 h-14 bg-gray-100 rounded-lg flex items-center justify-center">
                    <img 
                      v-if="jobOffer.company.logo" 
                      :src="jobOffer.company.logo" 
                      :alt="jobOffer.company.name"
                      class="w-10 h-10 object-contain"
                    />
                    <i v-else class='bx bx-building text-2xl text-gray-400'></i>
                  </div>
                  <div>
                    <h4 class="font-semibold">{{ jobOffer.company.name }}</h4>
                    <p class="text-sm text-gray-600">{{ jobOffer.company.industry }}</p>
                  </div>
                </div>
                
                <div class="space-y-3">
                  <div class="flex items-center">
                    <i class='bx bx-group text-[#193CB8] mr-2'></i>
                    <span class="text-sm">{{ jobOffer.company.employees }}</span>
                  </div>
                  <div class="flex items-center">
                    <i class='bx bx-globe text-[#193CB8] mr-2'></i>
                    <a href="#" class="text-sm text-[#193CB8] hover:underline">{{ jobOffer.company.website }}</a>
                  </div>
                  <div class="flex items-center">
                    <i class='bx bx-map text-[#193CB8] mr-2'></i>
                    <span class="text-sm">{{ jobOffer.location }}</span>
                  </div>
                </div>
                
                <a href="#" class="flex items-center justify-center text-[#193CB8] text-sm mt-4 hover:underline">
                  <i class='bx bx-building mr-1'></i> Ver perfil completo
                </a>
              </div>
            </div>
            
            <!-- Similar Jobs Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
              <div class="border-b border-gray-200 p-4 flex items-center">
                <i class='bx bx-list-plus text-xl text-[#193CB8] mr-2'></i>
                <h3 class="font-bold text-gray-800">Ofertas similares</h3>
              </div>
              <div class="p-5">
                <div class="space-y-3">
                  <div class="p-3 border border-gray-100 rounded-lg hover:bg-gray-50">
                    <h4 class="font-medium text-[#193CB8]">Desarrollador Backend</h4>
                    <div class="flex items-center text-sm text-gray-500 mt-1">
                      <i class='bx bx-buildings mr-1'></i>
                      <span>Tech Solutions</span>
                      <span class="mx-2">·</span>
                      <i class='bx bx-map-pin mr-1'></i>
                      <span>Madrid</span>
                    </div>
                  </div>
                  <div class="p-3 border border-gray-100 rounded-lg hover:bg-gray-50">
                    <h4 class="font-medium text-[#193CB8]">Frontend Developer</h4>
                    <div class="flex items-center text-sm text-gray-500 mt-1">
                      <i class='bx bx-buildings mr-1'></i>
                      <span>Digital Agency</span>
                      <span class="mx-2">·</span>
                      <i class='bx bx-map-pin mr-1'></i>
                      <span>Barcelona</span>
                    </div>
                  </div>
                  <div class="p-3 border border-gray-100 rounded-lg hover:bg-gray-50">
                    <h4 class="font-medium text-[#193CB8]">Full Stack Engineer</h4>
                    <div class="flex items-center text-sm text-gray-500 mt-1">
                      <i class='bx bx-buildings mr-1'></i>
                      <span>Startup Inc</span>
                      <span class="mx-2">·</span>
                      <i class='bx bx-map-pin mr-1'></i>
                      <span>Remoto</span>
                    </div>
                  </div>
                </div>
                
                <a href="#" class="flex items-center justify-center text-[#193CB8] text-sm mt-4 hover:underline">
                  <i class='bx bx-search-alt mr-1'></i> Ver más ofertas similares
                </a>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Bottom Action Bar -->
        <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-4 flex flex-col sm:flex-row items-center justify-between">
          <div class="flex items-center mb-3 sm:mb-0">
            <div class="w-10 h-10 bg-[#193CB8]/10 rounded-full flex items-center justify-center text-[#193CB8] mr-3">
              <i class='bx bx-share-alt'></i>
            </div>
            <div>
              <h3 class="font-medium text-gray-800">¿Conoces a alguien que encaje?</h3>
              <p class="text-sm text-gray-500">Comparte esta oferta con tu red profesional</p>
            </div>
          </div>
          <div class="flex gap-2">
            <button class="bg-gray-100 text-gray-700 font-medium py-2 px-4 rounded-lg hover:bg-gray-200 flex items-center">
              <i class='bx bxl-linkedin text-[#0077b5] mr-1'></i> LinkedIn
            </button>
            <button class="bg-gray-100 text-gray-700 font-medium py-2 px-4 rounded-lg hover:bg-gray-200 flex items-center">
              <i class='bx bx-envelope text-[#193CB8] mr-1'></i> Email
            </button>
            <button class="bg-gray-100 text-gray-700 font-medium py-2 px-4 rounded-lg hover:bg-gray-200 flex items-center">
              <i class='bx bx-link text-[#193CB8] mr-1'></i> Copiar
            </button>
          </div>
        </div>
      </div>
    </div>  
    </Layout>
  </template>
  
  <style>
  /* Estilos básicos para el contenido */
  .prose p {
    margin-bottom: 1rem;
    line-height: 1.7;
  }
  </style>