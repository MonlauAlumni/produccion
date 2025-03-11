<script setup>
  import { ref, onMounted, computed } from 'vue'
  import { router } from '@inertiajs/vue3'
  import Layout from '@/Components/Layout.vue'
  import JobCard from '@/Pages/JobOffers/JobCard.vue'
  
  // Estado para el feed infinito
  const jobOffers = ref([])
  const isLoading = ref(false)
  const page = ref(1)
  const hasMorePages = ref(true)
  const showScrollTopButton = ref(false)
  
  // Estado para interacciones
  const savedJobs = ref(new Set())
  const likedJobs = ref(new Set())
  const viewedJobs = ref(new Set())
  
  // Estado para filtros rápidos
  const activeFilter = ref('recommended')
  
  // Categorías destacadas
  const featuredCategories = [
    { id: 'tech', name: 'Tecnología', icon: 'bx-code-alt', color: '#193CB8' },
    { id: 'marketing', name: 'Marketing', icon: 'bx-line-chart', color: '#E44D26' },
    { id: 'design', name: 'Diseño', icon: 'bx-palette', color: '#FF7C43' },
    { id: 'finance', name: 'Finanzas', icon: 'bx-money', color: '#28A745' },
    { id: 'health', name: 'Salud', icon: 'bx-plus-medical', color: '#17A2B8' },
    { id: 'education', name: 'Educación', icon: 'bx-book-open', color: '#6F42C1' }
  ]
  
  // Datos de ejemplo para las ofertas de trabajo (simulando API)
  const fetchJobOffers = async (pageNum) => {
    isLoading.value = true
    
    // Simulamos una llamada a API con un retraso
    await new Promise(resolve => setTimeout(resolve, 800))
    
    // Generamos datos de ejemplo
    const newOffers = Array(10).fill().map((_, index) => {
      const id = (pageNum - 1) * 10 + index + 1
      const randomCompany = getRandomCompany()
      const randomLocation = getRandomLocation()
      const randomSalary = getRandomSalary()
      const randomSkills = getRandomSkills()
      const randomJobType = getRandomJobType()
      const randomDaysAgo = Math.floor(Math.random() * 14) + 1
      const randomApplicants = Math.floor(Math.random() * 100) + 5
      const randomViews = randomApplicants * (Math.floor(Math.random() * 10) + 5)
      
      return {
        id,
        title: getRandomJobTitle(),
        company: randomCompany,
        location: randomLocation,
        job_type: randomJobType.value,
        job_type_label: randomJobType.label,
        salary_min: randomSalary.min,
        salary_max: randomSalary.max,
        posted_date: getDateDaysAgo(randomDaysAgo),
        skills: randomSkills,
        experience: getRandomExperience(),
        description: getRandomDescription(),
        applicants: randomApplicants,
        views: randomViews,
        match_percentage: Math.floor(Math.random() * 31) + 70, // 70-100%
        is_promoted: Math.random() > 0.8,
        is_urgent: Math.random() > 0.9,
        is_remote_friendly: randomJobType.value === 'remote' || Math.random() > 0.7
      }
    })
    
    // Simulamos que no hay más páginas después de la 5
    if (pageNum >= 5) {
      hasMorePages.value = false
    }
    
    jobOffers.value = [...jobOffers.value, ...newOffers]
    isLoading.value = false
  }
  
  // Funciones para generar datos aleatorios
  const getRandomJobTitle = () => {
    const titles = [
      'Desarrollador Full Stack',
      'Frontend Developer',
      'Backend Engineer',
      'UX/UI Designer',
      'DevOps Engineer',
      'Data Scientist',
      'Product Manager',
      'Mobile Developer',
      'QA Engineer',
      'Project Manager',
      'Marketing Specialist',
      'Content Manager',
      'SEO Specialist',
      'Growth Hacker',
      'Customer Success Manager'
    ]
    return titles[Math.floor(Math.random() * titles.length)]
  }
  
  const getRandomCompany = () => {
    const companies = [
      { name: 'Tech Solutions', logo: null, industry: 'Tecnología' },
      { name: 'Digital Agency', logo: null, industry: 'Marketing Digital' },
      { name: 'Fintech Startup', logo: null, industry: 'Finanzas' },
      { name: 'Creative Studio', logo: null, industry: 'Diseño' },
      { name: 'Cloud Services', logo: null, industry: 'Tecnología' },
      { name: 'Analytics Corp', logo: null, industry: 'Análisis de Datos' },
      { name: 'SaaS Platform', logo: null, industry: 'Software' },
      { name: 'App Factory', logo: null, industry: 'Desarrollo Móvil' },
      { name: 'Health Tech', logo: null, industry: 'Salud' },
      { name: 'Edu Platform', logo: null, industry: 'Educación' }
    ]
    return companies[Math.floor(Math.random() * companies.length)]
  }
  
  const getRandomLocation = () => {
    const locations = [
      'Madrid, España',
      'Barcelona, España',
      'Valencia, España',
      'Sevilla, España',
      'Bilbao, España',
      'Málaga, España',
      'Zaragoza, España',
      'Remoto, España',
      'Remoto, Internacional'
    ]
    return locations[Math.floor(Math.random() * locations.length)]
  }
  
  const getRandomJobType = () => {
    const types = [
      { value: 'remote', label: 'Remoto' },
      { value: 'onsite', label: 'Presencial' },
      { value: 'hybrid', label: 'Híbrido' }
    ]
    return types[Math.floor(Math.random() * types.length)]
  }
  
  const getRandomSalary = () => {
    const bases = [25000, 30000, 35000, 40000, 45000, 50000, 60000]
    const base = bases[Math.floor(Math.random() * bases.length)]
    return {
      min: base,
      max: base + Math.floor(Math.random() * 20000) + 5000
    }
  }
  
  const getRandomSkills = () => {
    const allSkills = [
      'JavaScript', 'Vue.js', 'React', 'Angular', 'Node.js', 'PHP', 'Laravel', 
      'Python', 'Java', 'AWS', 'Docker', 'Kubernetes', 'SQL', 'MongoDB', 
      'HTML/CSS', 'UI/UX', 'Figma', 'Adobe XD', 'TypeScript', 'GraphQL',
      'Git', 'CI/CD', 'Agile', 'Scrum', 'REST API', 'Redux', 'Sass',
      'Tailwind CSS', 'Bootstrap', 'WordPress', 'SEO', 'Content Marketing',
      'Social Media', 'Google Analytics', 'Adobe Creative Suite'
    ]
    
    // Seleccionar entre 3 y 6 habilidades aleatorias
    const numSkills = Math.floor(Math.random() * 4) + 3
    const skills = new Set()
    
    while (skills.size < numSkills) {
      const randomIndex = Math.floor(Math.random() * allSkills.length)
      skills.add(allSkills[randomIndex])
    }
    
    return Array.from(skills)
  }
  
  const getRandomExperience = () => {
    const experiences = ['0-1 años', '1-2 años', '2-3 años', '3-5 años', '5+ años']
    return experiences[Math.floor(Math.random() * experiences.length)]
  }
  
  const getRandomDescription = () => {
    const descriptions = [
      'Buscamos un profesional apasionado para unirse a nuestro equipo en crecimiento.',
      'Únete a nuestra empresa líder en el sector y desarrolla tu carrera profesional.',
      'Forma parte de un equipo innovador con proyectos desafiantes e interesantes.',
      'Oportunidad para crecer en una empresa con cultura de aprendizaje continuo.',
      'Posición clave en una startup en fase de expansión con grandes oportunidades.'
    ]
    return descriptions[Math.floor(Math.random() * descriptions.length)]
  }
  
  const getDateDaysAgo = (days) => {
    const date = new Date()
    date.setDate(date.getDate() - days)
    return date.toISOString().split('T')[0]
  }
  
  // Manejar scroll infinito
  const handleScroll = () => {
    const scrollPosition = window.scrollY + window.innerHeight
    const pageHeight = document.documentElement.scrollHeight
    
    // Mostrar/ocultar botón para volver arriba
    showScrollTopButton.value = window.scrollY > 500
    
    // Cargar más ofertas cuando estamos cerca del final
    if (scrollPosition > pageHeight - 500 && !isLoading.value && hasMorePages.value) {
      page.value++
      fetchJobOffers(page.value)
    }
  }
  
  // Guardar oferta
  const toggleSaveJob = (jobId) => {
    if (savedJobs.value.has(jobId)) {
      savedJobs.value.delete(jobId)
    } else {
      savedJobs.value.add(jobId)
      
      // Animación de confeti o feedback visual (simulado)
      // En una implementación real, se usaría una librería de confeti o animaciones
    }
  }
  
  // Like a una oferta
  const toggleLikeJob = (jobId) => {
    if (likedJobs.value.has(jobId)) {
      likedJobs.value.delete(jobId)
    } else {
      likedJobs.value.add(jobId)
    }
  }
  
  // Ver detalle de oferta
  const viewJobOffer = (jobId) => {
    viewedJobs.value.add(jobId)
    router.get(`/ofertas/${jobId}`)
  }
  
  // Aplicar a oferta
  const applyToJob = (jobId) => {
    // Aquí iría la lógica para aplicar a la oferta
    // Por ahora solo marcamos como vista
    viewedJobs.value.add(jobId)
  }
  
  // Compartir oferta
  const shareJob = (jobId) => {
    // Simulación de compartir
    alert(`Compartiendo oferta ${jobId}`)
  }
  
  // Volver al inicio de la página
  const scrollToTop = () => {
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    })
  }
  
  // Cambiar filtro activo
  const setFilter = (filter) => {
    activeFilter.value = filter
    
    // En una implementación real, esto recargaría las ofertas
    // Por ahora, simulamos un refresco
    jobOffers.value = []
    page.value = 1
    hasMorePages.value = true
    fetchJobOffers(page.value)
  }
  
  // Comprobar si un trabajo está guardado
  const isJobSaved = (jobId) => {
    return savedJobs.value.has(jobId)
  }
  
  // Comprobar si un trabajo tiene like
  const isJobLiked = (jobId) => {
    return likedJobs.value.has(jobId)
  }
  
  // Inicializar
  onMounted(() => {
    fetchJobOffers(page.value)
    window.addEventListener('scroll', handleScroll)
  })
  </script>
  
  <template>
    <Layout>
      <div class="min-h-screen bg-gray-50 flex flex-col">
        <!-- Categories Section -->
        <div class="bg-white border-b shadow-sm sticky top-0 z-10">
          <div class="max-w-6xl mx-auto px-4">
            <div class="flex items-center justify-between py-3">
              <!-- Quick Filters -->
              <div class="flex items-center space-x-1 overflow-x-auto hide-scrollbar">
                <button 
                  @click="setFilter('recommended')"
                  :class="[
                    'px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-colors',
                    activeFilter === 'recommended' 
                      ? 'bg-[#193CB8] text-white' 
                      : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                  ]"
                >
                  <i class='bx bx-star mr-1'></i>
                  Recomendadas
                </button>
                <button 
                  @click="setFilter('recent')"
                  :class="[
                    'px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-colors',
                    activeFilter === 'recent' 
                      ? 'bg-[#193CB8] text-white' 
                      : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                  ]"
                >
                  <i class='bx bx-time mr-1'></i>
                  Recientes
                </button>
                <button 
                  @click="setFilter('saved')"
                  :class="[
                    'px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-colors',
                    activeFilter === 'saved' 
                      ? 'bg-[#193CB8] text-white' 
                      : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                  ]"
                >
                  <i class='bx bx-bookmark mr-1'></i>
                  Guardadas
                </button>
                <button 
                  @click="setFilter('remote')"
                  :class="[
                    'px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-colors',
                    activeFilter === 'remote' 
                      ? 'bg-[#193CB8] text-white' 
                      : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                  ]"
                >
                  <i class='bx bx-laptop mr-1'></i>
                  Remoto
                </button>
              </div>
              
              <!-- View Toggle (podría implementarse para cambiar la vista) -->
              <div class="hidden md:flex items-center space-x-2">
                <button class="p-2 rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200">
                  <i class='bx bx-list-ul'></i>
                </button>
                <button class="p-2 rounded-full bg-[#193CB8] text-white">
                  <i class='bx bx-grid-alt'></i>
                </button>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Main Feed -->
        <div class="flex-1 py-6">
          <div class="max-w-3xl mx-auto px-4">
            <!-- Feed Items -->
            <div class="space-y-6">
              <JobCard 
                v-for="job in jobOffers" 
                :key="job.id"
                :jobOffer="job"
                :isLiked="likedJobs.has(job.id)"
                :isSaved="savedJobs.has(job.id)"
                class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden job-card"
                :class="{ 'border-[#193CB8] border-2': job.is_promoted }"
                @view="viewJobOffer"
                @apply="applyToJob"
                @save="toggleSaveJob"
                @like="toggleLikeJob"
                @share="shareJob"
              />
              
              <!-- Loading Indicator -->
              <div v-if="isLoading" class="flex justify-center py-4">
                <div class="animate-pulse flex space-x-2">
                  <div class="w-2 h-2 bg-[#193CB8] rounded-full"></div>
                  <div class="w-2 h-2 bg-[#193CB8] rounded-full"></div>
                  <div class="w-2 h-2 bg-[#193CB8] rounded-full"></div>
                </div>
              </div>
              
              <!-- End of Results -->
              <div v-if="!hasMorePages && !isLoading" class="text-center py-8">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                  <i class='bx bx-check-circle text-3xl text-[#193CB8]'></i>
                </div>
                <h3 class="text-lg font-medium text-gray-800 mb-2">¡Has visto todas las ofertas!</h3>
                <p class="text-gray-500 mb-4">Vuelve más tarde para descubrir nuevas oportunidades</p>
                <button 
                  @click="scrollToTop"
                  class="text-[#193CB8] font-medium hover:underline flex items-center justify-center mx-auto"
                >
                  <i class='bx bx-chevron-up mr-1'></i>
                  Volver arriba
                </button>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Scroll to Top Button -->
        <button 
          v-show="showScrollTopButton"
          @click="scrollToTop"
          class="fixed bottom-6 right-6 w-12 h-12 rounded-full bg-[#193CB8] text-white shadow-lg flex items-center justify-center hover:bg-[#142d8c] transition-all duration-300 z-50 animate-fade-in"
        >
          <i class='bx bx-chevron-up text-xl'></i>
        </button>
        
        <!-- Floating Action Button (Mobile) -->
        <div class="md:hidden fixed bottom-6 left-1/2 transform -translate-x-1/2 z-50">
          <button 
            @click="router.get('/ofertas/crear')"
            class="bg-[#193CB8] text-white px-6 py-3 rounded-full shadow-lg flex items-center justify-center hover:bg-[#142d8c] transition-all duration-300"
          >
            <i class='bx bx-plus-circle mr-2'></i>
            Publicar oferta
          </button>
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
  
  /* Ocultar scrollbar pero mantener funcionalidad */
  .hide-scrollbar {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
  }
  
  .hide-scrollbar::-webkit-scrollbar {
    display: none;  /* Chrome, Safari and Opera */
  }
  
  /* Animaciones para hacer la experiencia más adictiva */
  .job-card {
    transform: translateY(0);
    transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
  }
  
  .job-card:hover {
    transform: translateY(-3px);
  }
  
  /* Animación para el botón de scroll to top */
  @keyframes fade-in {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
  }
  
  .animate-fade-in {
    animation: fade-in 0.3s ease-out;
  }
  
  /* Animación para los elementos que aparecen al hacer scroll */
  @keyframes slide-up {
    from {
      opacity: 0; 
      transform: translateY(20px);
    }
    to { 
      opacity: 1; 
      transform: translateY(0);
    }
  }
  
  .job-card {
    animation: slide-up 0.5s ease-out;
  }
  </style>