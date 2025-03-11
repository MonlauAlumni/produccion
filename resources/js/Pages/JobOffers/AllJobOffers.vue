<script setup>
    import { ref, computed, watch } from 'vue'
    import { router } from '@inertiajs/vue3'
    import Layout from '@/Components/Layout.vue'
    
    // Datos de ejemplo para las ofertas de trabajo
    const jobOffers = ref([
      {
        id: 1,
        title: 'Desarrollador Full Stack',
        company: {
          name: 'Tech Solutions',
          logo: 'https://example.com/logo.png',
          industry: 'Tecnología'
        },
        location: 'Madrid, España',
        job_type: 'remote',
        salary_min: 2500,
        salary_max: 4000,
        posted_date: '2023-11-15',
        skills: ['Vue.js', 'Laravel', 'PHP', 'MySQL', 'Git'],
        experience: '3-5 años',
        vacancies: 3
      },
      {
        id: 2,
        title: 'Frontend Developer',
        company: {
          name: 'Digital Agency',
          logo: 'https://example.com/logo2.png',
          industry: 'Marketing Digital'
        },
        location: 'Barcelona, España',
        job_type: 'hybrid',
        salary_min: 2000,
        salary_max: 3500,
        posted_date: '2023-11-18',
        skills: ['React', 'JavaScript', 'HTML/CSS', 'Tailwind'],
        experience: '2-3 años',
        vacancies: 1
      },
      {
        id: 3,
        title: 'Backend Engineer',
        company: {
          name: 'Fintech Startup',
          logo: 'https://example.com/logo3.png',
          industry: 'Finanzas'
        },
        location: 'Remoto',
        job_type: 'remote',
        salary_min: 3000,
        salary_max: 4500,
        posted_date: '2023-11-20',
        skills: ['Node.js', 'Express', 'MongoDB', 'AWS'],
        experience: '3-5 años',
        vacancies: 2
      },
      {
        id: 4,
        title: 'UX/UI Designer',
        company: {
          name: 'Creative Studio',
          logo: 'https://example.com/logo4.png',
          industry: 'Diseño'
        },
        location: 'Valencia, España',
        job_type: 'onsite',
        salary_min: 2200,
        salary_max: 3200,
        posted_date: '2023-11-22',
        skills: ['Figma', 'Adobe XD', 'Sketch', 'UI Design'],
        experience: '2-4 años',
        vacancies: 1
      },
      {
        id: 5,
        title: 'DevOps Engineer',
        company: {
          name: 'Cloud Services',
          logo: 'https://example.com/logo5.png',
          industry: 'Tecnología'
        },
        location: 'Madrid, España',
        job_type: 'hybrid',
        salary_min: 3500,
        salary_max: 5000,
        posted_date: '2023-11-25',
        skills: ['Docker', 'Kubernetes', 'CI/CD', 'AWS', 'Azure'],
        experience: '4-6 años',
        vacancies: 2
      },
      {
        id: 6,
        title: 'Data Scientist',
        company: {
          name: 'Analytics Corp',
          logo: 'https://example.com/logo6.png',
          industry: 'Análisis de Datos'
        },
        location: 'Barcelona, España',
        job_type: 'hybrid',
        salary_min: 3200,
        salary_max: 4800,
        posted_date: '2023-11-27',
        skills: ['Python', 'R', 'Machine Learning', 'SQL', 'Tableau'],
        experience: '3-5 años',
        vacancies: 1
      },
      {
        id: 7,
        title: 'Product Manager',
        company: {
          name: 'SaaS Platform',
          logo: 'https://example.com/logo7.png',
          industry: 'Software'
        },
        location: 'Remoto',
        job_type: 'remote',
        salary_min: 3800,
        salary_max: 5500,
        posted_date: '2023-11-28',
        skills: ['Product Management', 'Agile', 'Scrum', 'User Research'],
        experience: '5-7 años',
        vacancies: 1
      },
      {
        id: 8,
        title: 'Mobile Developer (iOS)',
        company: {
          name: 'App Factory',
          logo: 'https://example.com/logo8.png',
          industry: 'Desarrollo Móvil'
        },
        location: 'Sevilla, España',
        job_type: 'onsite',
        salary_min: 2800,
        salary_max: 4200,
        posted_date: '2023-11-30',
        skills: ['Swift', 'iOS', 'Xcode', 'UIKit', 'SwiftUI'],
        experience: '3-5 años',
        vacancies: 2
      }
    ])
    
    // Estado de los filtros
    const filters = ref({
      search: '',
      location: [],
      jobType: [],
      experience: [],
      salary: null,
      skills: []
    })
    
    // Opciones para los filtros
    const filterOptions = {
      location: ['Madrid, España', 'Barcelona, España', 'Valencia, España', 'Sevilla, España', 'Remoto'],
      jobType: [
        { value: 'remote', label: 'Remoto' },
        { value: 'onsite', label: 'Presencial' },
        { value: 'hybrid', label: 'Híbrido' }
      ],
      experience: [
        { value: '0-2', label: '0-2 años' },
        { value: '2-5', label: '2-5 años' },
        { value: '5+', label: '5+ años' }
      ],
      salary: [
        { value: '0-2000', label: 'Hasta 2.000€' },
        { value: '2000-3000', label: 'Entre 2.000€ y 3.000€' },
        { value: '3000-4000', label: 'Entre 3.000€ y 4.000€' },
        { value: '4000+', label: 'Más de 4.000€' }
      ],
      skills: ['JavaScript', 'Vue.js', 'React', 'Angular', 'Node.js', 'PHP', 'Laravel', 'Python', 'Java', 'AWS', 'Docker', 'Kubernetes', 'SQL', 'MongoDB', 'HTML/CSS', 'UI/UX', 'Figma', 'Adobe XD']
    }
    
    // Ordenación
    const sortOptions = [
      { value: 'recent', label: 'Más recientes' },
      { value: 'salary-desc', label: 'Mayor salario' },
      { value: 'salary-asc', label: 'Menor salario' },
      { value: 'relevance', label: 'Relevancia' }
    ]
    const sortBy = ref('recent')
    
    // Estado de los filtros móviles
    const mobileFiltersOpen = ref(false)
    
    // Formatear tipo de trabajo
    const formatJobType = (type) => {
      const types = {
        onsite: 'Presencial',
        remote: 'Remoto',
        hybrid: 'Híbrido'
      }
      return types[type] || type
    }
    
    // Formatear rango salarial
    const formatSalaryRange = (min, max) => {
      if (!min && !max) return 'No especificado'
      if (min && max) return `${min}€ - ${max}€`
      return min ? `Desde ${min}€` : `Hasta ${max}€`
    }
    
    // Calcular días desde publicación
    const getDaysSincePosted = (dateString) => {
      const posted = new Date(dateString)
      const today = new Date()
      const diffTime = Math.abs(today - posted)
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
      return diffDays
    }
    
    // Filtrar ofertas
    const filteredJobOffers = computed(() => {
      return jobOffers.value.filter(job => {
        // Filtro por búsqueda
        if (filters.value.search && !job.title.toLowerCase().includes(filters.value.search.toLowerCase()) && 
            !job.company.name.toLowerCase().includes(filters.value.search.toLowerCase())) {
          return false
        }
        
        // Filtro por ubicación
        if (filters.value.location.length > 0 && !filters.value.location.includes(job.location)) {
          return false
        }
        
        // Filtro por tipo de trabajo
        if (filters.value.jobType.length > 0 && !filters.value.jobType.includes(job.job_type)) {
          return false
        }
        
        // Filtro por experiencia
        if (filters.value.experience.length > 0) {
          const jobExp = parseInt(job.experience.split('-')[0])
          let matchesExperience = false
          
          filters.value.experience.forEach(exp => {
            if (exp === '0-2' && jobExp >= 0 && jobExp <= 2) matchesExperience = true
            if (exp === '2-5' && jobExp >= 2 && jobExp <= 5) matchesExperience = true
            if (exp === '5+' && jobExp >= 5) matchesExperience = true
          })
          
          if (!matchesExperience) return false
        }
        
        // Filtro por salario
        if (filters.value.salary) {
          const minSalary = job.salary_min || 0
          
          if (filters.value.salary === '0-2000' && minSalary > 2000) return false
          if (filters.value.salary === '2000-3000' && (minSalary < 2000 || minSalary > 3000)) return false
          if (filters.value.salary === '3000-4000' && (minSalary < 3000 || minSalary > 4000)) return false
          if (filters.value.salary === '4000+' && minSalary < 4000) return false
        }
        
        // Filtro por habilidades
        if (filters.value.skills.length > 0) {
          let hasSkill = false
          filters.value.skills.forEach(skill => {
            if (job.skills.includes(skill)) hasSkill = true
          })
          if (!hasSkill) return false
        }
        
        return true
      })
    })
    
    // Ordenar ofertas
    const sortedJobOffers = computed(() => {
      const sorted = [...filteredJobOffers.value]
      
      if (sortBy.value === 'recent') {
        return sorted.sort((a, b) => new Date(b.posted_date) - new Date(a.posted_date))
      } else if (sortBy.value === 'salary-desc') {
        return sorted.sort((a, b) => (b.salary_max || b.salary_min || 0) - (a.salary_max || a.salary_min || 0))
      } else if (sortBy.value === 'salary-asc') {
        return sorted.sort((a, b) => (a.salary_min || 0) - (b.salary_min || 0))
      }
      
      return sorted
    })
    
    // Número de resultados
    const resultCount = computed(() => filteredJobOffers.value.length)
    
    // Limpiar todos los filtros
    const clearAllFilters = () => {
      filters.value = {
        search: '',
        location: [],
        jobType: [],
        experience: [],
        salary: null,
        skills: []
      }
    }
    
    // Navegar a la página de detalle de oferta
    const viewJobOffer = (id) => {
      router.get(`/ofertas/${id}`)
    }
    
    // Comprobar si hay algún filtro activo
    const hasActiveFilters = computed(() => {
      return filters.value.search !== '' || 
             filters.value.location.length > 0 || 
             filters.value.jobType.length > 0 || 
             filters.value.experience.length > 0 || 
             filters.value.salary !== null || 
             filters.value.skills.length > 0
    })
    
    // Toggle para filtros en móvil
    const toggleMobileFilters = () => {
      mobileFiltersOpen.value = !mobileFiltersOpen.value
    }
    </script>
    
    <template>
        <Layout>
      <div class="min-h-screen bg-gray-50 flex flex-col">
       
        
        <!-- Header Section -->
        <div class="bg-[#193CB8] text-white py-8">
          <div class="max-w-6xl mx-auto px-4">
            <h1 class="text-2xl md:text-3xl font-bold mb-2">Ofertas de Empleo</h1>
            <p class="text-blue-100 max-w-2xl">
              Explora todas las oportunidades laborales disponibles en nuestra plataforma. Utiliza los filtros para encontrar el trabajo perfecto para ti.
            </p>
            
            <!-- Search Bar -->
            <div class="mt-6 relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class='bx bx-search text-gray-400'></i>
              </div>
              <input 
                v-model="filters.search"
                type="text" 
                placeholder="Buscar por título, empresa o palabra clave..." 
                class="w-full bg-white text-black border border-gray-200 rounded-lg pl-10 pr-4 py-2 focus:ring-[#193CB8] focus:border-[#193CB8]"
              >
            </div>
          </div>
        </div>
        
        <!-- Main Content -->
        <div class="w-[60%] mx-auto px-4 py-6">
          <div class="flex flex-col md:flex-row gap-6">
            <!-- Mobile Filters Toggle -->
            <div class="md:hidden mb-4">
              <button 
                @click="toggleMobileFilters"
                class="w-full bg-white border border-gray-200 rounded-lg shadow-sm px-4 py-3 flex items-center justify-between"
              >
                <span class="font-medium flex items-center">
                  <i class='bx bx-filter-alt mr-2 text-[#193CB8]'></i>
                  Filtros
                </span>
                <i class='bx bx-chevron-down' :class="{ 'transform rotate-180': mobileFiltersOpen }"></i>
              </button>
            </div>
            
            <!-- Filters Sidebar -->
            <div 
              :class="[
                'bg-white border border-gray-200 rounded-lg shadow-sm p-4 md:w-72 md:sticky md:top-4 md:h-[calc(100vh-6rem)] md:overflow-auto',
                mobileFiltersOpen ? 'block' : 'hidden md:block'
              ]"
            >
              <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-lg">Filtros</h2>
                <button 
                  v-if="hasActiveFilters"
                  @click="clearAllFilters"
                  class="text-sm text-[#193CB8] hover:underline flex items-center"
                >
                  <i class='bx bx-x mr-1'></i>
                  Limpiar filtros
                </button>
              </div>
              
              <!-- Location Filter -->
              <div class="mb-5 pb-5 border-b border-gray-200">
                <h3 class="font-medium mb-3 flex items-center">
                  <i class='bx bx-map text-[#193CB8] mr-2'></i>
                  Ubicación
                </h3>
                <div class="space-y-2">
                  <div v-for="location in filterOptions.location" :key="location" class="flex items-center">
                    <input 
                      :id="`location-${location}`"
                      type="checkbox"
                      :value="location"
                      v-model="filters.location"
                      class="h-4 w-4 rounded border-gray-300 text-[#193CB8] focus:ring-[#193CB8]"
                    >
                    <label :for="`location-${location}`" class="ml-2 text-sm text-gray-700">
                      {{ location }}
                    </label>
                  </div>
                </div>
              </div>
              
              <!-- Job Type Filter -->
              <div class="mb-5 pb-5 border-b border-gray-200">
                <h3 class="font-medium mb-3 flex items-center">
                  <i class='bx bx-time-five text-[#193CB8] mr-2'></i>
                  Tipo de trabajo
                </h3>
                <div class="space-y-2">
                  <div v-for="type in filterOptions.jobType" :key="type.value" class="flex items-center">
                    <input 
                      :id="`job-type-${type.value}`"
                      type="checkbox"
                      :value="type.value"
                      v-model="filters.jobType"
                      class="h-4 w-4 rounded border-gray-300 text-[#193CB8] focus:ring-[#193CB8]"
                    >
                    <label :for="`job-type-${type.value}`" class="ml-2 text-sm text-gray-700">
                      {{ type.label }}
                    </label>
                  </div>
                </div>
              </div>
              
              <!-- Experience Filter -->
              <div class="mb-5 pb-5 border-b border-gray-200">
                <h3 class="font-medium mb-3 flex items-center">
                  <i class='bx bx-briefcase text-[#193CB8] mr-2'></i>
                  Experiencia
                </h3>
                <div class="space-y-2">
                  <div v-for="exp in filterOptions.experience" :key="exp.value" class="flex items-center">
                    <input 
                      :id="`experience-${exp.value}`"
                      type="checkbox"
                      :value="exp.value"
                      v-model="filters.experience"
                      class="h-4 w-4 rounded border-gray-300 text-[#193CB8] focus:ring-[#193CB8]"
                    >
                    <label :for="`experience-${exp.value}`" class="ml-2 text-sm text-gray-700">
                      {{ exp.label }}
                    </label>
                  </div>
                </div>
              </div>
              
              <!-- Salary Filter -->
              <div class="mb-5 pb-5 border-b border-gray-200">
                <h3 class="font-medium mb-3 flex items-center">
                  <i class='bx bx-money text-[#193CB8] mr-2'></i>
                  Salario
                </h3>
                <div class="space-y-2">
                  <div v-for="salary in filterOptions.salary" :key="salary.value" class="flex items-center">
                    <input 
                      :id="`salary-${salary.value}`"
                      type="radio"
                      :value="salary.value"
                      v-model="filters.salary"
                      class="h-4 w-4 border-gray-300 text-[#193CB8] focus:ring-[#193CB8]"
                    >
                    <label :for="`salary-${salary.value}`" class="ml-2 text-sm text-gray-700">
                      {{ salary.label }}
                    </label>
                  </div>
                </div>
              </div>
              
              <!-- Skills Filter -->
              <div>
                <h3 class="font-medium mb-3 flex items-center">
                  <i class='bx bx-code-alt text-[#193CB8] mr-2'></i>
                  Habilidades
                </h3>
                <div class="flex flex-wrap gap-2">
                  <button
                    v-for="skill in filterOptions.skills"
                    :key="skill"
                    @click="filters.skills.includes(skill) 
                      ? filters.skills = filters.skills.filter(s => s !== skill)
                      : filters.skills.push(skill)"
                    :class="[
                      'px-3 py-1.5 text-xs rounded-md',
                      filters.skills.includes(skill)
                        ? 'bg-[#193CB8] text-white'
                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                    ]"
                  >
                    {{ skill }}
                  </button>
                </div>
              </div>
            </div>
            
            <!-- Results Section -->
            <div class="flex-1">
              <!-- Results Header -->
              <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4 mb-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div>
                  <h2 class="font-bold text-lg">{{ resultCount }} ofertas encontradas</h2>
                  <div v-if="hasActiveFilters" class="text-sm text-gray-500 mt-1">
                    Filtros aplicados: {{ filters.location.length + filters.jobType.length + filters.experience.length + (filters.salary ? 1 : 0) + filters.skills.length }}
                  </div>
                </div>
                
                <div class="flex items-center gap-2 w-full sm:w-auto">
                  <label for="sort-by" class="text-sm text-gray-500 whitespace-nowrap">Ordenar por:</label>
                  <select 
                    id="sort-by"
                    v-model="sortBy"
                    class="flex-1 sm:flex-none bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-md focus:ring-[#193CB8] focus:border-[#193CB8] block p-2"
                  >
                    <option v-for="option in sortOptions" :key="option.value" :value="option.value">
                      {{ option.label }}
                    </option>
                  </select>
                </div>
              </div>
              
              <!-- Job Listings -->
              <div class="space-y-4">
                <div 
                  v-for="job in sortedJobOffers" 
                  :key="job.id"
                  class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden"
                >
                  <div class="p-5">
                    <div class="flex flex-col md:flex-row md:items-start gap-4">
                      <!-- Company Logo -->
                      <div class="w-14 h-14 bg-gray-100 rounded-lg flex items-center justify-center shrink-0">
                        <img 
                          v-if="job.company.logo" 
                          :src="job.company.logo" 
                          :alt="job.company.name"
                          class="w-10 h-10 object-contain"
                        />
                        <i v-else class='bx bx-building text-2xl text-gray-400'></i>
                      </div>
                      
                      <!-- Job Info -->
                      <div class="flex-1">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                          <h3 class="font-bold text-lg text-[#193CB8]">{{ job.title }}</h3>
                          <div class="text-sm text-gray-500">
                            Hace {{ getDaysSincePosted(job.posted_date) }} días
                          </div>
                        </div>
                        
                        <div class="text-gray-700 mt-1">{{ job.company.name }}</div>
                        
                        <div class="flex flex-wrap gap-x-4 gap-y-2 text-sm mt-3">
                          <div class="flex items-center text-gray-600">
                            <i class='bx bx-map-pin mr-1 text-[#193CB8]'></i>
                            {{ job.location }}
                          </div>
                          <div class="flex items-center text-gray-600">
                            <i class='bx bx-time-five mr-1 text-[#193CB8]'></i>
                            {{ formatJobType(job.job_type) }}
                          </div>
                          <div class="flex items-center text-gray-600">
                            <i class='bx bx-money mr-1 text-[#193CB8]'></i>
                            {{ formatSalaryRange(job.salary_min, job.salary_max) }}
                          </div>
                          <div class="flex items-center text-gray-600">
                            <i class='bx bx-briefcase mr-1 text-[#193CB8]'></i>
                            {{ job.experience }}
                          </div>
                        </div>
                        
                        <!-- Skills -->
                        <div class="flex flex-wrap gap-2 mt-3">
                          <span 
                            v-for="(skill, index) in job.skills.slice(0, 4)" 
                            :key="index"
                            class="px-2 py-1 bg-[#193CB8]/10 text-[#193CB8] rounded-md text-xs font-medium"
                          >
                            {{ skill }}
                          </span>
                          <span 
                            v-if="job.skills.length > 4" 
                            class="px-2 py-1 bg-gray-100 text-gray-700 rounded-md text-xs font-medium"
                          >
                            +{{ job.skills.length - 4 }} más
                          </span>
                        </div>
                      </div>
                    </div>
                    
                    <!-- Action Bar -->
                    <div class="flex flex-col sm:flex-row items-center justify-between mt-4 pt-4 border-t border-gray-100">
                      <div class="flex items-center text-sm text-gray-500 mb-3 sm:mb-0">
                        <i class='bx bx-user-plus mr-1 text-[#193CB8]'></i>
                        {{ job.vacancies }} {{ job.vacancies === 1 ? 'vacante' : 'vacantes' }}
                      </div>
                      
                      <div class="flex gap-2 w-full sm:w-auto">
                        <button class="flex-1 sm:flex-none bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 flex items-center justify-center">
                          <i class='bx bx-bookmark mr-1'></i>
                          Guardar
                        </button>
                        <button 
                          @click="viewJobOffer(job.id)"
                          class="flex-1 sm:flex-none bg-[#193CB8] text-white px-4 py-2 rounded-lg hover:bg-[#142d8c] flex items-center justify-center"
                        >
                          Ver oferta
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Empty State -->
                <div v-if="sortedJobOffers.length === 0" class="bg-white border border-gray-200 rounded-lg shadow-sm p-8 text-center">
                  <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class='bx bx-search-alt text-3xl text-gray-400'></i>
                  </div>
                  <h3 class="text-lg font-medium text-gray-800 mb-2">No se encontraron ofertas</h3>
                  <p class="text-gray-500 mb-4">No hay ofertas que coincidan con tus criterios de búsqueda.</p>
                  <button 
                    @click="clearAllFilters"
                    class="text-[#193CB8] font-medium hover:underline flex items-center justify-center mx-auto"
                  >
                    <i class='bx bx-reset mr-1'></i>
                    Limpiar filtros
                  </button>
                </div>
              </div>
              
              <!-- Pagination -->
              <div v-if="sortedJobOffers.length > 0" class="mt-6 flex justify-center">
                <nav class="flex items-center gap-1">
                  <button class="w-10 h-10 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50">
                    <i class='bx bx-chevron-left'></i>
                  </button>
                  <button class="w-10 h-10 rounded-lg bg-[#193CB8] text-white flex items-center justify-center">1</button>
                  <button class="w-10 h-10 rounded-lg border border-gray-200 flex items-center justify-center text-gray-700 hover:bg-gray-50">2</button>
                  <button class="w-10 h-10 rounded-lg border border-gray-200 flex items-center justify-center text-gray-700 hover:bg-gray-50">3</button>
                  <span class="px-2 text-gray-500">...</span>
                  <button class="w-10 h-10 rounded-lg border border-gray-200 flex items-center justify-center text-gray-700 hover:bg-gray-50">8</button>
                  <button class="w-10 h-10 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50">
                    <i class='bx bx-chevron-right'></i>
                  </button>
                </nav>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Layout>
    </template>
    
    <style>
    /* Estilos básicos */
    .prose p {
      margin-bottom: 1rem;
      line-height: 1.7;
    }
    
    /* Scrollbar personalizado para el sidebar de filtros */
    .md\:overflow-auto::-webkit-scrollbar {
      width: 6px;
    }
    
    .md\:overflow-auto::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 10px;
    }
    
    .md\:overflow-auto::-webkit-scrollbar-thumb {
      background: #d1d5db;
      border-radius: 10px;
    }
    
    .md\:overflow-auto::-webkit-scrollbar-thumb:hover {
      background: #9ca3af;
    }
    </style>