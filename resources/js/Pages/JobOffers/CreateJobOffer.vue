<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import Layout from '@/Components/Layout.vue'

const form = ref({
  title: '',
  description: '',
  location: '',
  requirements: '',
  experience: '',
  responsibilities: [],
  benefits: [],
  min_experience: '',
  min_studies: '',
  vacancies: '',
  work_mode: '',
  minimum_salary: '',
  maximum_salary: '',
  skills: [],
  deadline: '',
  file: null,
  job_type: '',
})

const skills = ref(['Vue.js', 'Laravel', 'PHP', 'MySQL', 'Git', 'JavaScript', 'HTML', 'CSS', 'React', 'Node.js'])
const newSkill = ref('')
const fileName = ref('')

// For responsibilities and benefits
const newResponsibility = ref('')
const newBenefit = ref('')

const formProgress = computed(() => {
  let filled = 0;
  let total = 9; // Campos obligatorios (increased from 7 to include new required fields)

  if (form.value.title) filled++;
  if (form.value.description) filled++;
  if (form.value.requirements) filled++;
  if (form.value.min_experience) filled++;
  if (form.value.min_studies) filled++;
  if (form.value.vacancies) filled++;
  if (form.value.work_mode) filled++;
  if (form.value.skills.length > 0) filled++;
  if (form.value.deadline) filled++;

  return Math.round((filled / total) * 100);
})

const toggleSkill = (skill) => {
  const index = form.value.skills.indexOf(skill)
  if (index === -1) {
    form.value.skills.push(skill)
  } else {
    form.value.skills.splice(index, 1)
  }
}

const addSkill = () => {
  if (newSkill.value.trim() !== '') {
    skills.value.push(newSkill.value.trim())
    form.value.skills.push(newSkill.value.trim())
    newSkill.value = ''
  }
}

const addResponsibility = () => {
  if (newResponsibility.value.trim() !== '') {
    form.value.responsibilities.push(newResponsibility.value.trim())
    newResponsibility.value = ''
  }
}

const removeResponsibility = (index) => {
  form.value.responsibilities.splice(index, 1)
}

const addBenefit = () => {
  if (newBenefit.value.trim() !== '') {
    form.value.benefits.push(newBenefit.value.trim())
    newBenefit.value = ''
  }
}

const removeBenefit = (index) => {
  form.value.benefits.splice(index, 1)
}

const handleFileUpload = (event) => {
  form.value.file = event.target.files[0]
  fileName.value = event.target.files[0]?.name || ''
}

const submit = () => {
  router.post('/ofertas/crear', form.value, {
    forceFormData: true
  })
}

const activeSection = ref('basic') // basic, details, requirements, benefits, finish

const nextSection = () => {
  if (activeSection.value === 'basic') activeSection.value = 'details'
  else if (activeSection.value === 'details') activeSection.value = 'requirements'
  else if (activeSection.value === 'requirements') activeSection.value = 'benefits'
  else if (activeSection.value === 'benefits') activeSection.value = 'finish'
}

const prevSection = () => {
  if (activeSection.value === 'details') activeSection.value = 'basic'
  else if (activeSection.value === 'requirements') activeSection.value = 'details'
  else if (activeSection.value === 'benefits') activeSection.value = 'requirements'
  else if (activeSection.value === 'finish') activeSection.value = 'benefits'
}
</script>

<template>
  <Layout>
    <div class="min-h-screen bg-gray-50 flex flex-col">

      <!-- Header Section -->
      <div class="bg-[#193CB8] flex text-white py-8">
        <div class="mx-auto min-w-5xl  px-4">
          <h1 class="text-3xl font-bold flex items-center">
            <i class='bx bx-plus-circle mr-3 text-4xl'></i>
            Crear Nueva Oferta de Empleo
          </h1>
          <p class="mt-2 text-blue-100 max-w-2xl">
            Publica una nueva oferta de trabajo para encontrar a los mejores talentos para tu empresa. Completa todos
            los campos para maximizar la visibilidad.
          </p>

          <!-- Progress Bar -->
          <div class="mt-6 bg-white/20 rounded-full h-2.5 w-full max-w-2xl">
            <div class="bg-white h-2.5 rounded-full transition-all duration-500" :style="`width: ${formProgress}%`">
            </div>
          </div>
          <div class="text-sm text-blue-100 mt-1">Progreso: {{ formProgress }}%</div>
        </div>
      </div>

      <div class="min-w-5xl max-w-5xl mx-auto py-8 px-4">
        <!-- Form Steps Navigation -->
        <div class="bg-white rounded-xl shadow-md mb-6 overflow-hidden">
          <div class="flex border-b">
            <button @click="activeSection = 'basic'" :class="[
              'flex-1 py-4 px-4 text-center font-medium transition-colors flex items-center justify-center gap-2',
              activeSection === 'basic'
                ? 'bg-[#193CB8] text-white'
                : 'text-gray-600 hover:bg-gray-50'
            ]">
              <i class='bx bx-info-circle'></i>
              Información Básica
            </button>
            <button @click="activeSection = 'details'" :class="[
              'flex-1 py-4 px-4 text-center font-medium transition-colors flex items-center justify-center gap-2',
              activeSection === 'details'
                ? 'bg-[#193CB8] text-white'
                : 'text-gray-600 hover:bg-gray-50'
            ]">
              <i class='bx bx-detail'></i>
              Detalles del Puesto
            </button>
            <button @click="activeSection = 'requirements'" :class="[
              'flex-1 py-4 px-4 text-center font-medium transition-colors flex items-center justify-center gap-2',
              activeSection === 'requirements'
                ? 'bg-[#193CB8] text-white'
                : 'text-gray-600 hover:bg-gray-50'
            ]">
              <i class='bx bx-list-check'></i>
              Requisitos
            </button>
            <button @click="activeSection = 'benefits'" :class="[
              'flex-1 py-4 px-4 text-center font-medium transition-colors flex items-center justify-center gap-2',
              activeSection === 'benefits'
                ? 'bg-[#193CB8] text-white'
                : 'text-gray-600 hover:bg-gray-50'
            ]">
              <i class='bx bx-gift'></i>
              Beneficios
            </button>
            <button @click="activeSection = 'finish'" :class="[
              'flex-1 py-4 px-4 text-center font-medium transition-colors flex items-center justify-center gap-2',
              activeSection === 'finish'
                ? 'bg-[#193CB8] text-white'
                : 'text-gray-600 hover:bg-gray-50'
            ]">
              <i class='bx bx-check-circle'></i>
              Finalizar
            </button>
          </div>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
          <!-- Basic Information Section -->
          <div v-if="activeSection === 'basic'" class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-[#193CB8]/10 to-white p-4 border-b">
              <h2 class="text-xl font-bold text-[#193CB8] flex items-center">
                <i class='bx bx-info-circle mr-2'></i>
                Información Básica
              </h2>
            </div>

            <div class="p-6 space-y-6">
              <!-- Title -->
              <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                  Título de la oferta <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class='bx bx-heading text-gray-400'></i>
                  </div>
                  <input id="title" v-model="form.title" type="text" placeholder="Ej: Desarrollador Full Stack Senior"
                    required
                    class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-[#193CB8] focus:border-[#193CB8] bg-white">
                </div>
                <p class="mt-1 text-sm text-gray-500">
                  Un título claro y conciso aumentará la visibilidad de tu oferta
                </p>
              </div>

              <!-- Description -->
              <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                  Descripción de la oferta <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <textarea id="description" v-model="form.description"
                    placeholder="Describe detalladamente el puesto, responsabilidades y lo que ofrece tu empresa..."
                    required rows="6"
                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-[#193CB8] focus:border-[#193CB8] bg-white"></textarea>
                </div>
                <p class="mt-1 text-sm text-gray-500">
                  Una descripción detallada ayuda a los candidatos a entender mejor la posición
                </p>
              </div>

              <div>
                <label for="localidad" class="block text-sm font-medium text-gray-700 mb-1">
                  Localidad <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class='bx bx-map text-gray-400'></i>
                  </div>
                  <input id="localidad" v-model="form.location" type="text" placeholder="Ej: Madrid" required
                    class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-[#193CB8] focus:border-[#193CB8] bg-white">
                </div>
                <p class="mt-1 text-sm text-gray-500">
                  Indica la ciudad o localidad donde se desarrollará el trabajo
                </p>
              </div>

              <!-- Deadline -->
              <div>
                <label for="deadline" class="block text-sm font-medium text-gray-700 mb-1">
                  Fecha límite de aplicación <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class='bx bx-calendar text-gray-400'></i>
                  </div>
                  <input id="deadline" v-model="form.deadline" type="date" required
                    class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-[#193CB8] focus:border-[#193CB8] bg-white">
                </div>
                <p class="mt-1 text-sm text-gray-500">
                  Establece una fecha límite para recibir aplicaciones
                </p>
              </div>

              <div class="flex justify-end pt-4">
                <button type="button" @click="nextSection"
                  class="bg-gradient-to-r from-[#193CB8] to-[#2d50c7] text-white font-medium py-2 px-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 flex items-center">
                  Siguiente
                  <i class='bx bx-right-arrow-alt ml-2'></i>
                </button>
              </div>
            </div>
          </div>

          <!-- Details Section -->
          <div v-if="activeSection === 'details'" class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-[#193CB8]/10 to-white p-4 border-b">
              <h2 class="text-xl font-bold text-[#193CB8] flex items-center">
                <i class='bx bx-detail mr-2'></i>
                Detalles del Puesto
              </h2>
            </div>

            <div class="p-6 space-y-6">
              <!-- Vacancies and Work Mode -->
              <div class="grid md:grid-cols-2 gap-6">
                <div>
                  <label for="vacancies" class="block text-sm font-medium text-gray-700 mb-1">
                    Número de Vacantes <span class="text-red-500">*</span>
                  </label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <i class='bx bx-user-plus text-gray-400'></i>
                    </div>
                    <input id="vacancies" v-model="form.vacancies" type="number" placeholder="Ej: 3" required
                      class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-[#193CB8] focus:border-[#193CB8] bg-white">
                  </div>
                </div>
                <div>
                  <label for="work_mode" class="block text-sm font-medium text-gray-700 mb-1">
                    Modalidad de Trabajo <span class="text-red-500">*</span>
                  </label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <i class='bx bx-time-five text-gray-400'></i>
                    </div>
                    <select id="work_mode" v-model="form.work_mode" required
                      class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-[#193CB8] focus:border-[#193CB8] bg-white appearance-none">
                      <option value="">Selecciona la modalidad</option>
                      <option value="onsite">Presencial</option>
                      <option value="remote">Remoto</option>
                      <option value="hybrid">Híbrido</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                      <i class='bx bx-chevron-down text-gray-400'></i>
                    </div>
                  </div>
                </div>
                <div class="relative">
                  <label for="job_type" class="block text-sm font-medium text-gray-700 mb-1">
                    Tipo de Contrato <span class="text-red-500">*</span>
                  </label>
                  <select id="job_type" v-model="form.job_type" required
                    class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-[#193CB8] focus:border-[#193CB8] bg-white appearance-none">
                    <option value="">Selecciona el tipo de contrato</option>
                    <option value="part_time">Media Jornada</option>
                    <option value="full_time">Jornada Completa</option>
                    <option value="internship">Prácticas</option>
                  </select>
                  <div class="absolute bottom-4 right-0 flex items-center pr-3 pointer-events-none">
                    <i class="bx bx-chevron-down text-gray-400"></i>
                  </div>
                </div>



                <div v-if="form.job_type === 'internship'" class="mt-6">
                  <label class="inline-flex items-center">
                    <input type="checkbox" v-model="form.dual" class="form-checkbox h-5 w-5 text-blue-600">
                    <span class="ml-2 text-gray-700">Dual</span>
                  </label>
                  <p class="mt-2 text-sm text-gray-500">
                    Marque esta opción si se ofrece la posibilidad de hacer Dual.
                  </p>
                </div>
              </div>

              <!-- Salary -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Salario
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label for="minimum_salary" class="block text-sm font-medium text-gray-700 mb-1">
                      Salario Mínimo
                    </label>
                    <div class="relative">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500">€</span>
                      </div>
                      <input id="minimum_salary" v-model="form.minimum_salary" type="number" placeholder="Ej: 25000"
                        class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-[#193CB8] focus:border-[#193CB8] bg-white">
                    </div>
                  </div>
                  <div>
                    <label for="maximum_salary" class="block text-sm font-medium text-gray-700 mb-1">
                      Salario Máximo
                    </label>
                    <div class="relative">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500">€</span>
                      </div>
                      <input id="maximum_salary" v-model="form.maximum_salary" type="number" placeholder="Ej: 35000"
                        class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-[#193CB8] focus:border-[#193CB8] bg-white">
                    </div>
                  </div>
                </div>
                <p class="mt-1 text-sm text-gray-500">
                  Deja ambos campos vacíos si no deseas especificar el salario
                </p>
              </div>

              <!-- Responsibilities -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">
                  Responsabilidades
                </label>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-4">
                  <div class="space-y-2 mb-4">
                    <div v-for="(responsibility, index) in form.responsibilities" :key="index"
                      class="flex items-center bg-white p-3 rounded-lg border border-gray-200">
                      <span class="flex-1">{{ responsibility }}</span>
                      <button type="button" @click="removeResponsibility(index)"
                        class="text-red-500 hover:text-red-700">
                        <i class='bx bx-trash'></i>
                      </button>
                    </div>
                    <div v-if="form.responsibilities.length === 0" class="text-gray-500 text-center py-2">
                      No hay responsabilidades añadidas
                    </div>
                  </div>
                  <div class="flex">
                    <input v-model="newResponsibility" @keyup.enter="addResponsibility" type="text"
                      placeholder="Añadir responsabilidad..."
                      class="flex-1 px-4 py-3 border border-gray-300 rounded-l-lg shadow-sm focus:outline-none focus:ring-[#193CB8] focus:border-[#193CB8] bg-white">
                    <button type="button" @click="addResponsibility"
                      class="bg-gradient-to-r from-[#193CB8] to-[#2d50c7] text-white px-4 py-3 rounded-r-lg hover:shadow-md transition-all duration-300">
                      <i class='bx bx-plus'></i>
                    </button>
                  </div>
                </div>
              </div>

              <div class="flex justify-between pt-4">
                <button type="button" @click="prevSection"
                  class="bg-gray-100 text-gray-700 font-medium py-2 px-6 rounded-lg hover:bg-gray-200 transition-colors flex items-center">
                  <i class='bx bx-left-arrow-alt mr-2'></i>
                  Anterior
                </button>
                <button type="button" @click="nextSection"
                  class="bg-gradient-to-r from-[#193CB8] to-[#2d50c7] text-white font-medium py-2 px-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 flex items-center">
                  Siguiente
                  <i class='bx bx-right-arrow-alt ml-2'></i>
                </button>
              </div>
            </div>
          </div>

          <!-- Requirements Section -->
          <div v-if="activeSection === 'requirements'" class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-[#193CB8]/10 to-white p-4 border-b">
              <h2 class="text-xl font-bold text-[#193CB8] flex items-center">
                <i class='bx bx-list-check mr-2'></i>
                Requisitos
              </h2>
            </div>

            <div class="p-6 space-y-6">
              <!-- Experience and Studies -->
              <div class="grid md:grid-cols-2 gap-6">
                <div>
                  <label for="min_experience" class="block text-sm font-medium text-gray-700 mb-1">
                    Experiencia Mínima <span class="text-red-500">*</span>
                  </label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <i class='bx bx-time text-gray-400'></i>
                    </div>
                    <select id="min_experience" v-model="form.min_experience" required
                      class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-[#193CB8] focus:border-[#193CB8] bg-white appearance-none">
                      <option value="">Selecciona la experiencia mínima</option>
                      <option value="0">Sin Experiencia</option>
                      <option value="1">1-2 años</option>
                      <option value="3">3-4 años</option>
                      <option value="5">5 o más años</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                      <i class='bx bx-chevron-down text-gray-400'></i>
                    </div>
                  </div>
                </div>
                <div>
                  <label for="min_studies" class="block text-sm font-medium text-gray-700 mb-1">
                    Estudios Mínimos <span class="text-red-500">*</span>
                  </label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <i class='bx bx-book text-gray-400'></i>
                    </div>
                    <select id="min_studies" v-model="form.min_studies" required
                      class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-[#193CB8] focus:border-[#193CB8] bg-white appearance-none">
                      <option value="">Selecciona los estudios mínimos</option>
                      <option value="high-school">Grado Superior</option>
                      <option value="bachelor">Grado Medio</option>
                      <option value="master">Grado Universitario</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                      <i class='bx bx-chevron-down text-gray-400'></i>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Detailed Requirements -->
              <div>
                <label for="requirements" class="block text-sm font-medium text-gray-700 mb-1">
                  Requisitos detallados <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <textarea id="requirements" v-model="form.requirements"
                    placeholder="Describe los requisitos específicos para el puesto..." required rows="4"
                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-[#193CB8] focus:border-[#193CB8] bg-white"></textarea>
                </div>
                <p class="mt-1 text-sm text-gray-500">
                  Detalla los requisitos específicos que deben cumplir los candidatos
                </p>
              </div>

              <!-- Experience Description -->
              <div>
                <label for="experience" class="block text-sm font-medium text-gray-700 mb-1">
                  Descripción de la experiencia <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <textarea id="experience" v-model="form.experience"
                    placeholder="Describe la experiencia necesaria para el puesto..." required rows="4"
                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-[#193CB8] focus:border-[#193CB8] bg-white"></textarea>
                </div>
                <p class="mt-1 text-sm text-gray-500">
                  Detalla el tipo de experiencia que debe tener el candidato ideal
                </p>
              </div>

              <!-- Skills -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">
                  Habilidades requeridas <span class="text-red-500">*</span>
                </label>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-4">
                  <div class="flex flex-wrap gap-2 mb-4">
                    <span v-for="skill in skills" :key="skill" :class="[
                      'px-4 py-2 rounded-full text-sm cursor-pointer transition-all duration-200 flex items-center',
                      form.skills.includes(skill)
                        ? 'bg-gradient-to-r from-[#193CB8] to-[#2d50c7] text-white shadow-md'
                        : 'bg-white text-gray-700 border border-gray-300 hover:border-[#193CB8] hover:text-[#193CB8]'
                    ]" @click="toggleSkill(skill)">
                      <i :class="[
                        'mr-1',
                        form.skills.includes(skill) ? 'bx bx-check' : 'bx bx-plus'
                      ]"></i>
                      {{ skill }}
                    </span>
                  </div>
                  <div class="flex">
                    <input v-model="newSkill" @keyup.enter="addSkill" type="text"
                      placeholder="Añadir nueva habilidad..."
                      class="flex-1 px-4 py-3 border border-gray-300 rounded-l-lg shadow-sm focus:outline-none focus:ring-[#193CB8] focus:border-[#193CB8] bg-white">
                    <button type="button" @click="addSkill"
                      class="bg-gradient-to-r from-[#193CB8] to-[#2d50c7] text-white px-4 py-3 rounded-r-lg hover:shadow-md transition-all duration-300">
                      <i class='bx bx-plus'></i>
                    </button>
                  </div>
                  <p class="mt-2 text-sm text-gray-500">
                    Selecciona todas las habilidades relevantes o añade nuevas
                  </p>
                </div>
              </div>

              <div class="flex justify-between pt-4">
                <button type="button" @click="prevSection"
                  class="bg-gray-100 text-gray-700 font-medium py-2 px-6 rounded-lg hover:bg-gray-200 transition-colors flex items-center">
                  <i class='bx bx-left-arrow-alt mr-2'></i>
                  Anterior
                </button>
                <button type="button" @click="nextSection"
                  class="bg-gradient-to-r from-[#193CB8] to-[#2d50c7] text-white font-medium py-2 px-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 flex items-center">
                  Siguiente
                  <i class='bx bx-right-arrow-alt ml-2'></i>
                </button>
              </div>
            </div>
          </div>

          <!-- Benefits Section -->
          <div v-if="activeSection === 'benefits'" class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-[#193CB8]/10 to-white p-4 border-b">
              <h2 class="text-xl font-bold text-[#193CB8] flex items-center">
                <i class='bx bx-gift mr-2'></i>
                Beneficios
              </h2>
            </div>

            <div class="p-6 space-y-6">
              <!-- Benefits List -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">
                  Beneficios para el empleado
                </label>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-4">
                  <div class="space-y-2 mb-4">
                    <div v-for="(benefit, index) in form.benefits" :key="index"
                      class="flex items-center bg-white p-3 rounded-lg border border-gray-200">
                      <span class="flex-1">{{ benefit }}</span>
                      <button type="button" @click="removeBenefit(index)" class="text-red-500 hover:text-red-700">
                        <i class='bx bx-trash'></i>
                      </button>
                    </div>
                    <div v-if="form.benefits.length === 0" class="text-gray-500 text-center py-2">
                      No hay beneficios añadidos
                    </div>
                  </div>
                  <div class="flex">
                    <input v-model="newBenefit" @keyup.enter="addBenefit" type="text" placeholder="Añadir beneficio..."
                      class="flex-1 px-4 py-3 border border-gray-300 rounded-l-lg shadow-sm focus:outline-none focus:ring-[#193CB8] focus:border-[#193CB8] bg-white">
                    <button type="button" @click="addBenefit"
                      class="bg-gradient-to-r from-[#193CB8] to-[#2d50c7] text-white px-4 py-3 rounded-r-lg hover:shadow-md transition-all duration-300">
                      <i class='bx bx-plus'></i>
                    </button>
                  </div>
                  <p class="mt-2 text-sm text-gray-500">
                    Añade los beneficios que ofrece tu empresa (seguro médico, teletrabajo, etc.)
                  </p>
                </div>
              </div>

              <div class="flex justify-between pt-4">
                <button type="button" @click="prevSection"
                  class="bg-gray-100 text-gray-700 font-medium py-2 px-6 rounded-lg hover:bg-gray-200 transition-colors flex items-center">
                  <i class='bx bx-left-arrow-alt mr-2'></i>
                  Anterior
                </button>
                <button type="button" @click="nextSection"
                  class="bg-gradient-to-r from-[#193CB8] to-[#2d50c7] text-white font-medium py-2 px-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 flex items-center">
                  Siguiente
                  <i class='bx bx-right-arrow-alt ml-2'></i>
                </button>
              </div>
            </div>
          </div>

          <!-- Finish Section -->
          <div v-if="activeSection === 'finish'" class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-[#193CB8]/10 to-white p-4 border-b">
              <h2 class="text-xl font-bold text-[#193CB8] flex items-center">
                <i class='bx bx-check-circle mr-2'></i>
                Finalizar y Publicar
              </h2>
            </div>

            <div class="p-6 space-y-6">
              <!-- File Upload -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Archivos adjuntos (opcional)
                </label>
                <div
                  class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-[#193CB8]/20 border-dashed rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors cursor-pointer relative">
                  <input id="file-upload" name="file-upload" type="file"
                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" @change="handleFileUpload">
                  <div class="space-y-1 text-center">
                    <i class='bx bx-upload text-5xl text-[#193CB8]'></i>
                    <div class="flex flex-col items-center text-sm text-gray-600">
                      <p class="font-medium text-[#193CB8]">
                        {{ fileName ? fileName : 'Arrastra archivos aquí o haz clic para seleccionar' }}
                      </p>
                      <p class="text-xs text-gray-500 mt-1">
                        PNG, JPG, GIF, PDF hasta 10MB
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Summary -->
              <div class="bg-gray-50 p-5 rounded-lg border border-gray-200">
                <h3 class="font-semibold text-lg text-[#193CB8] mb-3">Resumen de la oferta</h3>
                <div class="space-y-3">
                  <div class="flex items-start">
                    <div class="w-32 text-sm font-medium text-gray-500">Título:</div>
                    <div class="flex-1 font-medium">{{ form.title || 'No especificado' }}</div>
                  </div>
                  <div class="flex items-start">
                    <div class="w-32 text-sm font-medium text-gray-500">Vacantes:</div>
                    <div class="flex-1">{{ form.vacancies || 'No especificado' }}</div>
                  </div>
                  <div class="flex items-start">
                    <div class="w-32 text-sm font-medium text-gray-500">Experiencia:</div>
                    <div class="flex-1">{{ form.min_experience || 'No especificado' }}</div>
                  </div>
                  <div class="flex items-start">
                    <div class="w-32 text-sm font-medium text-gray-500">Estudios:</div>
                    <div class="flex-1">{{ form.min_studies || 'No especificado' }}</div>
                  </div>
                  <div class="flex items-start">
                    <div class="w-32 text-sm font-medium text-gray-500">Fecha límite:</div>
                    <div class="flex-1">{{ form.deadline || 'No especificado' }}</div>
                  </div>
                  <div class="flex items-start">
                    <div class="w-32 text-sm font-medium text-gray-500">Habilidades:</div>
                    <div class="flex-1 flex flex-wrap gap-1">
                      <span v-for="skill in form.skills" :key="skill"
                        class="px-2 py-1 bg-[#193CB8]/10 text-[#193CB8] rounded-full text-xs">
                        {{ skill }}
                      </span>
                      <span v-if="form.skills.length === 0" class="text-gray-500">No especificado</span>
                    </div>
                  </div>
                </div>
              </div>

              <div class="flex justify-between pt-4">
                <button type="button" @click="prevSection"
                  class="bg-gray-100 text-gray-700 font-medium py-2 px-6 rounded-lg hover:bg-gray-200 transition-colors flex items-center">
                  <i class='bx bx-left-arrow-alt mr-2'></i>
                  Anterior
                </button>
                <button type="submit"
                  class="bg-gradient-to-r from-[#193CB8] to-[#2d50c7] text-white font-semibold py-3 px-8 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 flex items-center">
                  <i class='bx bx-check-circle mr-2'></i>
                  Publicar Oferta
                </button>
              </div>
            </div>
          </div>
        </form>
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