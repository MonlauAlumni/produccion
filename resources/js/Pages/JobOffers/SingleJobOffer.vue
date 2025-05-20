<script setup>
  import { ref, computed } from 'vue'
  import { router, usePage } from '@inertiajs/vue3'
  import Layout from '@/Components/Layout.vue'
  import JobConfirmationModal from '@/Components/JobOffers/JobConfirmationModal.vue'
  import JobEditModal from '@/Components/JobOffers/JobEditModal.vue'
  const props = defineProps({
    jobOffer: {
      type: Object,
      required: true
    },
    isJobOfferOwner: {
      type: Boolean,
      required: true
    },
    skills: {
      type: Array,
      required: true
    }
  })
  const page = usePage()
const userRole = computed(() => page.props.auth.user?.roles?.[0]?.name ?? null)
  const jobOffer = ref(props.jobOffer)
  const showConfirmationModal = ref(false)
  const showEditModal = ref(false)
  
 
  const isJobOfferOwner = ref(props.isJobOfferOwner)
  const formatJobType = (type) => {
    const types = {
      onsite: 'Presencial',
      remote: 'Remoto',
      hybrid: 'Híbrido'
    }
    return types[type] || type
  }
  
  const formatSalaryRange = (min, max) => {
    min = parseInt(min, 10);
    max = parseInt(max, 10);
    
    if (!min && !max) return 'No especificado'
    if (min && max) return  `${min}€ - ${max}€`
    return min ? `Desde ${min}€` : `Hasta ${max}€`
  }
  
  const applyToJob = () => {
    openConfirmationModal()
  }

  const openConfirmationModal = () => {
  showConfirmationModal.value = true
}

const closeConfirmationModal = () => {
  showConfirmationModal.value = false
}
  const openEditModal = () => {
    showEditModal.value = true
  }

  const closeEditModal = () => {
    showEditModal.value = false
  }
  
  const getDaysRemaining = (dateString) => {
    const parts = dateString.split('-');
    if (parts.length !== 3) {
      console.error('Formato de fecha inválido');
      return NaN;
    }
    const [year, month, day] = parts;
    const dayOnly = day.split("T")[0];
    const deadline = new Date(year, month - 1, dayOnly);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const diffTime = deadline - today;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
 
    return diffDays;
  }

  const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  }
  const daysRemaining = getDaysRemaining(props.jobOffer.deadline);
  </script>
  
  <template>
    <Layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex flex-col">
      
      <!-- Header Section with Company Info -->
      <div class="bg-[#193CB8] dark:bg-[#142d8c] text-white py-6 border-b-4 border-[#193CB8]/20">
        <div class="max-w-5xl mx-auto px-4">
          <div class="flex flex-col md:flex-row items-start gap-5">
            <!-- Company Logo -->
            <div class="w-20 h-20 bg-white dark:bg-gray-700 rounded-lg shadow-md flex items-center justify-center border-2 border-white dark:border-gray-600">
              <img 
                v-if="jobOffer.company.profile_picture" 
                :src="jobOffer.company.profile_picture" 
                :alt="jobOffer.company.company_name"
                class="w-19 h-19 rounded-lg"
              />
              <i v-else class='bx bx-building text-4xl text-[#193CB8] dark:text-gray-300'></i>
            </div>
            
            <!-- Job Title and Company Info -->
            <div class="flex-1">
              <div class="flex items-center text-sm mb-1 text-blue-100 dark:text-blue-200">
                <i class='bx bx-buildings mr-1'></i>
                {{ jobOffer.company.company_name }} · {{ jobOffer.company.sector }}
              </div>
              <h1 class="text-2xl font-bold mb-2">{{ jobOffer.title }}</h1>
              <div class="flex flex-wrap gap-x-4 gap-y-2 text-sm mt-2">
                <div class="flex items-center text-gray-200 dark:text-gray-400">
                  <i class='bx bx-map-pin mr-1'></i>
                  {{ jobOffer.location }}
                </div>
                <div class="flex items-center text-gray-200 dark:text-gray-400">
                  <i class='bx bx-time-five mr-1'></i>
                  {{ formatJobType(jobOffer.work_mode) }}
                </div>
                <div class="flex items-center text-gray-200 dark:text-gray-400">
                  <i class='bx bx-money-withdraw mr-1'></i>
                  {{ formatSalaryRange(jobOffer.minimum_salary, jobOffer.maximum_salary) }}
                </div>
                <div class="flex items-center text-gray-200 dark:text-gray-400">
                  <i class='bx bx-calendar mr-1'></i>
                  Cierre en {{ daysRemaining }} días
                </div>
              </div>
            </div>
            
            <!-- Apply Button -->
            <div v-if="userRole === 'alumne'" class="mt-4 md:mt-0 flex flex-col items-end">
              <button 
                @click="applyToJob"
                class="bg-white dark:bg-gray-800 text-[#193CB8] dark:text-blue-200 cursor-pointer font-semibold px-6 py-2 rounded-lg shadow-md hover:bg-blue-50 dark:hover:bg-gray-700 flex items-center"
              >
                <i class='bx bx-send mr-2'></i>
                Inscribirme
              </button>
              
              <div class="text-xs text-blue-100 mt-2">
                <i class='bx bx-user-plus'></i> {{ jobOffer.vacancies }} {{ jobOffer.vacancies === 1 ? 'vacante disponible' : 'vacantes disponibles' }}
              </div>
            </div>
  <div v-if="isJobOfferOwner" class="max-w-5xl mx-auto px-4 py-2 flex justify-end">
    <button 
      @click="openEditModal"
      class="bg-white text-[#193CB8] cursor-pointer font-semibold px-6 py-2 rounded-lg shadow-md hover:bg-blue-50 flex items-center"
    >
      <i class='bx bx-edit mr-2'></i>
      Editar Oferta
    </button>
  </div>
     
          </div>
        </div>
      </div>
      

      
      <div class="max-w-5xl mx-auto py-6 px-4">
        <div class="grid md:grid-cols-3 gap-6">
          <!-- Left Column - Main Content -->
          <div class="md:col-span-2 space-y-5">
            <!-- Description Section -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
              <div class="border-b border-gray-200 dark:border-gray-700 p-4 flex items-center">
                <i class='bx bx-detail text-xl text-[#193CB8] dark:text-blue-200 mr-2'></i>
                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200">Descripción</h2>
              </div>
              <div class="p-5">
                <div class="prose max-w-none text-gray-700 dark:text-gray-300" v-html="jobOffer.description"></div>
              </div>
            </div>
            
            <!-- Responsibilities Section -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
              <div class="border-b border-gray-200 dark:border-gray-700 p-4 flex items-center">
                <i class='bx bx-list-ul text-xl text-[#193CB8] dark:text-blue-200 mr-2'></i>
                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200">Responsabilidades</h2>
              </div>
              <div class="p-5">
                <ul class="space-y-2">
                  <li v-for="(responsibility, index) in jobOffer.responsibilities" :key="index" class="flex items-start">
                    <i class='bx bx-check-circle text-[#193CB8] dark:text-blue-200 mt-1 mr-2'></i>
                    <span class="text-gray-700 dark:text-gray-300">{{ responsibility }}</span>
                  </li>
                </ul>
              </div>
            </div>
            
            <!-- Requirements Section -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
              <div class="border-b border-gray-200 dark:border-gray-700 p-4 flex items-center">
                <i class='bx bx-list-check text-xl text-[#193CB8] dark:text-blue-200 mr-2'></i>
                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200">Requisitos</h2>
              </div>
              <div class="p-5">
                <div class="space-y-4">
                  <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <h3 class="font-semibold flex items-center text-gray-800 dark:text-gray-200 border-b border-gray-200 dark:border-gray-600 pb-2 mb-3">
                      <i class='bx bx-book text-[#193CB8] dark:text-blue-200 mr-2'></i>
                      Estudios mínimos
                    </h3>
                    <div v-html="jobOffer.requirements" class="text-gray-700 dark:text-gray-300"></div>
                  </div>
                  <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <h3 class="font-semibold flex items-center text-gray-800 dark:text-gray-200 border-b border-gray-200 dark:border-gray-600 pb-2 mb-3">
                      <i class='bx bx-time text-[#193CB8] dark:text-blue-200 mr-2'></i>
                      Experiencia mínima
                    </h3>
                    <div v-html="jobOffer.experience" class="text-gray-700 dark:text-gray-300"></div>
                  </div>
                  <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <h3 class="font-semibold flex items-center text-gray-800 dark:text-gray-200 border-b border-gray-200 dark:border-gray-600 pb-2 mb-3">
                      <i class='bx bx-code-alt text-[#193CB8] dark:text-blue-200 mr-2'></i>
                      Conocimientos requeridos
                    </h3>
                    <div class="flex flex-wrap gap-2 mt-3">
                      <span 
                        v-for="skill in jobOffer.skills" 
                        :key="skill"
                        class="px-3 py-1.5 bg-[#193CB8]/10 dark:bg-blue-900 text-[#193CB8] dark:text-blue-200 rounded-md text-sm font-medium flex items-center border border-[#193CB8]/20 dark:border-blue-700"
                      >
                        <i class='bx bx-check mr-1'></i>
                        {{ skill.name }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Benefits Section -->
            <div v-if="jobOffer.benefits" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
              <div class="border-b border-gray-200 dark:border-gray-700 p-4 flex items-center">
                <i class='bx bx-gift text-xl text-[#193CB8] dark:text-blue-200 mr-2'></i>
                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200">Beneficios</h2>
              </div>
              <div class="p-5">
                <div class="grid grid-cols-2 gap-3">
                  <div v-for="(benefit, index) in jobOffer.benefits" :key="index" class="flex items-center bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                    <i class='bx bx-badge-check text-[#193CB8] dark:text-blue-200 mr-2'></i>
                    <span class="text-gray-700 dark:text-gray-300">{{ benefit }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Right Column - Job Details and Company Info -->
          <div class="space-y-5">
            <!-- Action Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
              <div class="border-b border-gray-200 dark:border-gray-700 p-4 flex items-center">
                <i class='bx bx-calendar-check text-xl text-[#193CB8] dark:text-blue-200 mr-2'></i>
                <h3 class="font-bold text-gray-800 dark:text-gray-200">Aplicar a esta oferta</h3>
              </div>
              <div class="p-5">
                <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-100 dark:border-gray-700">
                  <div class="text-sm text-gray-500 dark:text-gray-400">Fecha límite:</div>
                  <div class="font-medium flex items-center text-gray-800 dark:text-gray-200">
                    <i class='bx bx-time-five text-[#193CB8] dark:text-blue-200 mr-1'></i>
                    <p class="text-sm">{{ formatDate(jobOffer.deadline) }}</p>
                  </div>
                </div>
                <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-100 dark:border-gray-700">
                  <div class="text-sm text-gray-500 dark:text-gray-400">Vacantes:</div>
                  <div class="font-medium flex items-center text-gray-800 dark:text-gray-200">
                    <i class='bx bx-user-plus text-[#193CB8] dark:text-blue-200 mr-1'></i>
                    {{ jobOffer.vacancies }}
                  </div>
                </div>
                <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-100 dark:border-gray-700">
                  <div class="text-sm text-gray-500 dark:text-gray-400">Inscritos</div>
                  <div class="font-medium flex items-center text-gray-800 dark:text-gray-200">
                    <i class='bx bx-user-plus text-[#193CB8] dark:text-blue-200 mr-1'></i>
                    {{ jobOffer.applicants.length }}
                  </div>
                </div>
                <div class="flex items-center justify-between mb-4">
                  <div class="text-sm text-gray-500 dark:text-gray-400">Proceso:</div>
                  <div class="font-medium flex items-center text-gray-800 dark:text-gray-200">
                    <i class='bx bx-check-shield text-[#193CB8] dark:text-blue-200 mr-1'></i>
                    En selección
                  </div>
                </div>
                <button 
                  v-if="userRole === 'alumne'"
                  @click="applyToJob"
                  class="w-full bg-[#193CB8] dark:bg-blue-900 cursor-pointer text-white font-semibold py-3 px-6 rounded-lg shadow-sm hover:bg-[#142d8c] dark:hover:bg-blue-800 flex items-center justify-center mt-5"
                >
                  <i class='bx bx-send mr-2'></i>
                  Inscribirme a la oferta
                </button>
              </div>
            </div>
            
            <!-- Company Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
              <div class="border-b border-gray-200 dark:border-gray-700 p-4 flex items-center">
                <i class='bx bx-building text-xl text-[#193CB8] dark:text-blue-200 mr-2'></i>
                <h3 class="font-bold text-gray-800 dark:text-gray-200">Sobre la empresa</h3>
              </div>
              <div class="p-5">
                <div class="flex items-center gap-3 mb-4 pb-4 border-b border-gray-100 dark:border-gray-700">
                  <div class="w-14 h-14 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                    <img 
                      v-if="jobOffer.company.profile_picture" 
                      :src="jobOffer.company.profile_picture" 
                      :alt="jobOffer.company.company_name"
                      class="w-13 h-13 rounded-lg"
                    />
                    <i v-else class='bx bx-building text-2xl text-gray-400 dark:text-gray-500'></i>
                  </div>
                  <div>
                    <h4 class="font-semibold text-gray-800 dark:text-gray-200">{{ jobOffer.company.company_name }}</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ jobOffer.company.sector }}</p>
                  </div>
                </div>
                <div class="space-y-3">
                  <div class="flex items-center text-gray-800 dark:text-gray-200">
                    <i class='bx bx-group text-[#193CB8] dark:text-blue-200 mr-2'></i>
                    <span class="text-sm">{{ jobOffer.company.size }}</span>
                  </div>
                  <div class="flex items-center text-gray-800 dark:text-gray-200">
                    <i class='bx bx-globe text-[#193CB8] dark:text-blue-200 mr-2'></i>
                    <a :href="jobOffer.company.website" class="text-sm text-[#193CB8] dark:text-blue-200 hover:underline">{{ jobOffer.company.website }}</a>
                  </div>
                  <div class="flex items-center text-gray-800 dark:text-gray-200">
                    <i class='bx bx-map text-[#193CB8] dark:text-blue-200 mr-2'></i>
                    <span class="text-sm">{{ jobOffer.company.location ? jobOffer.company.location : 'No especificada' }}</span>
                  </div>
                </div>
                <a @click.prevent="router.get(`/empresa/${jobOffer.company.slang}`)" class="flex cursor-pointer items-center justify-center text-[#193CB8] dark:text-blue-200 text-sm mt-4 hover:underline">
                  <i class='bx bx-building mr-1'></i> Ver perfil completo
                </a>
              </div>
            </div>
            
          
           
          </div>
        </div>
      </div>
    </div>  
    <JobConfirmationModal
    :is-open="showConfirmationModal"
    :job-offer="jobOffer"
    @close="closeConfirmationModal"
  />
  <JobEditModal
    :is-open="showEditModal"
    :job-offer="jobOffer"
    :skills="skills"
    @close="closeEditModal"
  />
  </Layout>
  </template>
  
  <style>
  /* Estilos básicos para el contenido */
  .prose p {
    margin-bottom: 1rem;
    line-height: 1.7;
  }
  </style>
