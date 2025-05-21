<script setup>
import { ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.min.css'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  jobOffer: {
    type: Object,
    required: true
  },
  skills: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['close'])

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
  job_type: '',
  category: '',
})

// Initialize form with job offer data when component mounts
watch(() => props.isOpen, (isOpen) => {
  if (isOpen && props.jobOffer) {
    form.value = {
      title: props.jobOffer.title || '',
      description: props.jobOffer.description || '',
      location: props.jobOffer.location || '',
      requirements: props.jobOffer.requirements || '',
      experience: props.jobOffer.experience || '',
      responsibilities: Array.isArray(props.jobOffer.responsibilities) ? [...props.jobOffer.responsibilities] : [],
      benefits: Array.isArray(props.jobOffer.benefits) ? [...props.jobOffer.benefits] : [],
      min_experience: props.jobOffer.min_experience || '',
      min_studies: props.jobOffer.min_studies || '',
      vacancies: props.jobOffer.vacancies || '',
      work_mode: props.jobOffer.work_mode || '',
      minimum_salary: props.jobOffer.minimum_salary || '',
      maximum_salary: props.jobOffer.maximum_salary || '',
      skills: props.jobOffer.skills ? [...props.jobOffer.skills] : [],
      deadline: props.jobOffer.deadline ? props.jobOffer.deadline.split('T')[0] : '',
      job_type: props.jobOffer.contract_type || '',
      category: props.jobOffer.category || '',
    }
  }
}, { immediate: true })

const newResponsibility = ref('')
const newBenefit = ref('')
const selectedSkill = ref(null)
const activeTab = ref('basic')
const isSubmitting = ref(false)

const showDeleteConfirmation = ref(false)
const deleteCountdown = ref(5)
const isDeleting = ref(false)
const countdownInterval = ref(null)

// Computed list of skills filtered by selected category
const filteredSkills = computed(() => {
  if (!form.value.category) {
    return props.skills;
  }
  return props.skills.filter(skill => skill.category === form.value.category);
})

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

const addSelectedSkill = () => {
  if (!selectedSkill.value) return
  
  // Si skills es un array de objetos (como en SkillsSection)
  if (typeof selectedSkill.value === 'object' && selectedSkill.value !== null) {
    // Verifica si ya existe la habilidad
    if (!form.value.skills.some(skill => skill.id === selectedSkill.value.id)) {
      form.value.skills.push(selectedSkill.value)
    }
  } else {
    // Si es un string simple
    if (!form.value.skills.includes(selectedSkill.value)) {
      form.value.skills.push(selectedSkill.value)
    }
  }
  
  selectedSkill.value = null
}

const removeSkill = (skillToRemove) => {
  form.value.skills = form.value.skills.filter(skill => {
    if (typeof skill === 'object' && skill !== null) {
      return skill.id !== skillToRemove.id
    }
    return skill !== skillToRemove
  })
}

const openDeleteConfirmation = () => {
  showDeleteConfirmation.value = true
  deleteCountdown.value = 5
  countdownInterval.value = setInterval(() => {
    deleteCountdown.value -= 1
    if (deleteCountdown.value <= 0) {
      clearInterval(countdownInterval.value)
    }
  }, 1000)
}

const closeDeleteConfirmation = () => {
  showDeleteConfirmation.value = false
  clearInterval(countdownInterval.value)
}

const deleteJobOffer = () => {
  if (deleteCountdown.value > 0 || isDeleting.value) return
  
  isDeleting.value = true
  
  router.delete(`/ofertas/${props.jobOffer.id}`, {
    onSuccess: () => {
      closeModal()
      // Redirect to home page or job offers list
      router.visit('/home')
    },
    onError: (errors) => {
      console.error('Error al eliminar la oferta:', errors)
      isDeleting.value = false
    }
  })
}

const closeModal = () => {
  emit('close')
}

const submit = () => {
  if (isSubmitting.value) return
  isSubmitting.value = true
  
  router.post(`/ofertas/${props.jobOffer.id}/editar`, form.value, {
    forceFormData: true,
    onSuccess: () => {
      closeModal()
      // Refresh the page data without a full page reload
      router.reload({ only: ['jobOffer'] })
    },
    onFinish: () => {
      isSubmitting.value = false
    }
  })
}
</script>

<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Background overlay -->
      <div class="fixed inset-0 z-40 bg-gray-500/75 dark:bg-black/75 transition-opacity backdrop-blur-sm" aria-hidden="true" @click="closeModal"></div>


      <!-- Modal panel -->
      <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
        <div class="bg-[#193CB8] dark:bg-[#142d8c] px-6 py-4 flex justify-between items-center">
          <h3 class="text-xl font-bold text-white flex items-center">
            <i class='bx bx-edit mr-2'></i>
            Editar Oferta de Empleo
          </h3>
          <button @click="closeModal" class="text-white hover:text-gray-200">
            <i class='bx bx-x text-2xl'></i>
          </button>
        </div>

        <div class="bg-white dark:bg-gray-800 px-6 py-4 max-h-[80vh] overflow-y-auto">
          <!-- Tabs -->
          <div class="flex border-b border-gray-200 dark:border-gray-700 mb-6">
            <button 
              @click="activeTab = 'basic'" 
              :class="[
                'px-4 py-2 font-medium text-sm',
                activeTab === 'basic' 
                  ? 'border-b-2 border-[#193CB8] text-[#193CB8] dark:border-blue-400 dark:text-blue-400' 
                  : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'
              ]"
            >
              <i class='bx bx-info-circle mr-1'></i>
              Información Básica
            </button>
            <button 
              @click="activeTab = 'details'" 
              :class="[
                'px-4 py-2 font-medium text-sm',
                activeTab === 'details' 
                  ? 'border-b-2 border-[#193CB8] text-[#193CB8] dark:border-blue-400 dark:text-blue-400' 
                  : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'
              ]"
            >
              <i class='bx bx-detail mr-1'></i>
              Detalles
            </button>
            <button 
              @click="activeTab = 'requirements'" 
              :class="[
                'px-4 py-2 font-medium text-sm',
                activeTab === 'requirements' 
                  ? 'border-b-2 border-[#193CB8] text-[#193CB8] dark:border-blue-400 dark:text-blue-400' 
                  : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'
              ]"
            >
              <i class='bx bx-list-check mr-1'></i>
              Requisitos
            </button>
            <button 
              @click="activeTab = 'benefits'" 
              :class="[
                'px-4 py-2 font-medium text-sm',
                activeTab === 'benefits' 
                  ? 'border-b-2 border-[#193CB8] text-[#193CB8] dark:border-blue-400 dark:text-blue-400' 
                  : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'
              ]"
            >
              <i class='bx bx-gift mr-1'></i>
              Beneficios
            </button>
          </div>

          <form @submit.prevent="submit">
            <!-- Basic Information Tab -->
            <div v-if="activeTab === 'basic'" class="space-y-4">
              <div>
                <label for="edit-title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                  Título de la oferta <span class="text-red-500">*</span>
                </label>
                <input 
                  id="edit-title" 
                  v-model="form.title" 
                  type="text" 
                  required
                  class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-[#193CB8] dark:focus:ring-blue-500 focus:border-[#193CB8] dark:focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200"
                >
              </div>

              <div>
                <label for="edit-description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                  Descripción <span class="text-red-500">*</span>
                </label>
                <textarea 
                  id="edit-description" 
                  v-model="form.description" 
                  rows="4" 
                  required
                  class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-[#193CB8] dark:focus:ring-blue-500 focus:border-[#193CB8] dark:focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200"
                ></textarea>
              </div>

              <div>
                <label for="edit-location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                  Localidad <span class="text-red-500">*</span>
                </label>
                <input 
                  id="edit-location" 
                  v-model="form.location" 
                  type="text" 
                  required
                  class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-[#193CB8] dark:focus:ring-blue-500 focus:border-[#193CB8] dark:focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200"
                >
              </div>

              <div>
                <label for="edit-category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                  Categoría <span class="text-red-500">*</span>
                </label>
                <select 
                  id="edit-category" 
                  v-model="form.category" 
                  required
                  class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-[#193CB8] dark:focus:ring-blue-500 focus:border-[#193CB8] dark:focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200"
                >
                  <option value="">Selecciona la categoría</option>
                  <option value="it">Informática</option>
                  <option value="marketing">Marketing</option>
                  <option value="automotive">Automoción</option>
                </select>
              </div>

              <div>
                <label for="edit-deadline" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                  Fecha límite <span class="text-red-500">*</span>
                </label>
                <input 
                  id="edit-deadline" 
                  v-model="form.deadline" 
                  type="date" 
                  required
                  class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-[#193CB8] dark:focus:ring-blue-500 focus:border-[#193CB8] dark:focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200"
                >
              </div>
            </div>

            <!-- Details Tab -->
            <div v-if="activeTab === 'details'" class="space-y-4">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label for="edit-vacancies" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Vacantes <span class="text-red-500">*</span>
                  </label>
                  <input 
                    id="edit-vacancies" 
                    v-model="form.vacancies" 
                    type="number" 
                    required
                    class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-[#193CB8] dark:focus:ring-blue-500 focus:border-[#193CB8] dark:focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200"
                  >
                </div>

                <div>
                  <label for="edit-work-mode" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Modalidad de Trabajo <span class="text-red-500">*</span>
                  </label>
                  <select 
                    id="edit-work-mode" 
                    v-model="form.work_mode" 
                    required
                    class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-[#193CB8] dark:focus:ring-blue-500 focus:border-[#193CB8] dark:focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200"
                  >
                    <option value="">Selecciona la modalidad</option>
                    <option value="onsite">Presencial</option>
                    <option value="remote">Remoto</option>
                    <option value="hybrid">Híbrido</option>
                  </select>
                </div>

                <div>
                  <label for="edit-job-type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Tipo de Contrato <span class="text-red-500">*</span>
                  </label>
                  <select 
                    id="edit-job-type" 
                    v-model="form.job_type" 
                    required
                    class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-[#193CB8] dark:focus:ring-blue-500 focus:border-[#193CB8] dark:focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200"
                  >
                    <option value="">Selecciona el tipo de contrato</option>
                    <option value="part_time">Media Jornada</option>
                    <option value="full_time">Jornada Completa</option>
                    <option value="internship">Prácticas</option>
                  </select>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label for="edit-min-salary" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Salario Mínimo
                  </label>
                  <input 
                    id="edit-min-salary" 
                    v-model="form.minimum_salary" 
                    type="number" 
                    class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-[#193CB8] dark:focus:ring-blue-500 focus:border-[#193CB8] dark:focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200"
                  >
                </div>

                <div>
                  <label for="edit-max-salary" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Salario Máximo
                  </label>
                  <input 
                    id="edit-max-salary" 
                    v-model="form.maximum_salary" 
                    type="number" 
                    class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-[#193CB8] dark:focus:ring-blue-500 focus:border-[#193CB8] dark:focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200"
                  >
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                  Responsabilidades
                </label>
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                  <div class="space-y-2 mb-4">
                    <div v-for="(responsibility, index) in form.responsibilities" :key="index"
                      class="flex items-center bg-white dark:bg-gray-800 p-2 rounded-lg border border-gray-200 dark:border-gray-600">
                      <span class="flex-1 text-gray-800 dark:text-gray-200">{{ responsibility }}</span>
                      <button type="button" @click="removeResponsibility(index)"
                        class="text-red-500 hover:text-red-700 dark:hover:text-red-400">
                        <i class='bx bx-trash'></i>
                      </button>
                    </div>
                    <div v-if="form.responsibilities.length === 0" class="text-gray-500 dark:text-gray-400 text-center py-2">
                      No hay responsabilidades añadidas
                    </div>
                  </div>
                  <div class="flex">
                    <input v-model="newResponsibility" @keyup.enter="addResponsibility" type="text"
                      placeholder="Añadir responsabilidad..."
                      class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-l-lg focus:ring-[#193CB8] dark:focus:ring-blue-500 focus:border-[#193CB8] dark:focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                    <button type="button" @click="addResponsibility"
                      class="bg-[#193CB8] dark:bg-blue-700 text-white px-4 py-2 rounded-r-lg hover:bg-[#142d8c] dark:hover:bg-blue-800">
                      <i class='bx bx-plus'></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Requirements Tab -->
            <div v-if="activeTab === 'requirements'" class="space-y-4">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label for="edit-min-experience" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Experiencia Mínima <span class="text-red-500">*</span>
                  </label>
                  <select 
                    id="edit-min-experience" 
                    v-model="form.min_experience" 
                    required
                    class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-[#193CB8] dark:focus:ring-blue-500 focus:border-[#193CB8] dark:focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200"
                  >
                    <option value="">Selecciona la experiencia mínima</option>
                    <option value="0">Sin Experiencia</option>
                    <option value="1">1-2 años</option>
                    <option value="3">3-4 años</option>
                    <option value="5">5 o más años</option>
                  </select>
                </div>

                <div>
                  <label for="edit-min-studies" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Estudios Mínimos <span class="text-red-500">*</span>
                  </label>
                  <select 
                    id="edit-min-studies" 
                    v-model="form.min_studies" 
                    required
                    class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-[#193CB8] dark:focus:ring-blue-500 focus:border-[#193CB8] dark:focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200"
                  >
                    <option value="">Selecciona los estudios mínimos</option>
                    <option value="high-school">Grado Superior</option>
                    <option value="bachelor">Grado Medio</option>
                    <option value="master">Grado Universitario</option>
                  </select>
                </div>
              </div>

              <div>
                <label for="edit-requirements" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                  Requisitos detallados <span class="text-red-500">*</span>
                </label>
                <textarea 
                  id="edit-requirements" 
                  v-model="form.requirements" 
                  rows="4" 
                  required
                  class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-[#193CB8] dark:focus:ring-blue-500 focus:border-[#193CB8] dark:focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200"
                ></textarea>
              </div>

              <div>
                <label for="edit-experience" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                  Descripción de la experiencia <span class="text-red-500">*</span>
                </label>
                <textarea 
                  id="edit-experience" 
                  v-model="form.experience" 
                  rows="4" 
                  required
                  class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-[#193CB8] dark:focus:ring-blue-500 focus:border-[#193CB8] dark:focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200"
                ></textarea>
              </div>

              <!-- Skills Section -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                  Habilidades requeridas <span class="text-red-500">*</span>
                </label>
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600 mb-4">
                  <!-- Lista de habilidades seleccionadas -->
                  <div class="flex flex-wrap gap-2 mb-4">
                    <span v-for="skill in form.skills" :key="skill.id || skill"
                      class="bg-blue-50 dark:bg-blue-900 text-[#193CB8] dark:text-blue-200 px-3 py-1.5 rounded-md text-sm font-medium group relative border border-[#193CB8]/10 dark:border-blue-700">
                      {{ skill.name }}
                      <button type="button" @click="removeSkill(skill)"
                        class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-4 h-4 flex items-center justify-center opacity-0  focus:opacity-100 transition-opacity"
                        :aria-label="`Eliminar habilidad ${skill.name}`" tabindex="0">
                        <i class='bx bx-x text-xs' aria-hidden="true"></i>
                      </button>
                    </span>
                    <span v-if="form.skills.length === 0" class="text-gray-500 dark:text-gray-400">
                      No hay habilidades seleccionadas
                    </span>
                  </div>

                  <!-- Multiselect para añadir habilidades -->
                  <div>
                    <label for="skill-select" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                      Selecciona una habilidad
                    </label>
                    <Multiselect
                      v-model="selectedSkill"
                      :options="filteredSkills"
                      label="name"
                      track-by="id"
                      placeholder="Buscar habilidad"
                      class="skill-select"
                      :searchable="true"
                      :clear-on-select="true"
                      :close-on-select="true"
                      id="skill-select" />

                    <!-- Botones de acción -->
                    <div class="flex gap-2 mt-3">
                      <button type="button" @click="addSelectedSkill"
                        class="bg-[#193CB8] dark:bg-blue-700 hover:bg-[#2748c6] dark:hover:bg-blue-800 text-white px-4 py-2 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-[#193CB8] dark:focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed flex-1"
                        aria-label="Añadir habilidad seleccionada" :disabled="!selectedSkill">
                        <span class="flex items-center justify-center gap-1">
                          <i class='bx bx-check' aria-hidden="true"></i>
                          <span>Añadir</span>
                        </span>
                      </button>
                      <button type="button" @click="selectedSkill = null"
                        class="bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-500 focus:ring-offset-2 flex-1"
                        aria-label="Cancelar selección">
                        <span class="flex items-center justify-center gap-1">
                          <i class='bx bx-x' aria-hidden="true"></i>
                          <span>Cancelar</span>
                        </span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Benefits Tab -->
            <div v-if="activeTab === 'benefits'" class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                  Beneficios para el empleado
                </label>
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                  <div class="space-y-2 mb-4">
                    <div v-for="(benefit, index) in form.benefits" :key="index"
                      class="flex items-center bg-white dark:bg-gray-800 p-2 rounded-lg border border-gray-200 dark:border-gray-600">
                      <span class="flex-1 text-gray-800 dark:text-gray-200">{{ benefit }}</span>
                      <button type="button" @click="removeBenefit(index)"
                        class="text-red-500 hover:text-red-700 dark:hover:text-red-400">
                        <i class='bx bx-trash'></i>
                      </button>
                    </div>
                    <div v-if="form.benefits.length === 0" class="text-gray-500 dark:text-gray-400 text-center py-2">
                      No hay beneficios añadidos
                    </div>
                  </div>
                  <div class="flex">
                    <input v-model="newBenefit" @keyup.enter="addBenefit" type="text"
                      placeholder="Añadir beneficio..."
                      class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-l-lg focus:ring-[#193CB8] dark:focus:ring-blue-500 focus:border-[#193CB8] dark:focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                    <button type="button" @click="addBenefit"
                      class="bg-[#193CB8] dark:bg-blue-700 text-white px-4 py-2 rounded-r-lg hover:bg-[#142d8c] dark:hover:bg-blue-800">
                      <i class='bx bx-plus'></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-6 flex justify-between gap-3">
              <button 
                type="button" 
                @click="openDeleteConfirmation"
                class="px-4 py-2 bg-red-600 dark:bg-red-700 text-white rounded-lg hover:bg-red-700 dark:hover:bg-red-800 transition-colors flex items-center"
              >
                <i class='bx bx-trash mr-2'></i>
                <span>Eliminar Oferta</span>
              </button>
              
              <div class="flex gap-3">
                <button 
                  type="button" 
                  @click="closeModal"
                  class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors"
                >
                  Cancelar
                </button>
                <button 
                  type="submit"
                  class="px-4 py-2 bg-[#193CB8] dark:bg-blue-700 text-white rounded-lg hover:bg-[#142d8c] dark:hover:bg-blue-800 flex items-center transition-colors"
                  :disabled="isSubmitting"
                >
                  <i v-if="isSubmitting" class='bx bx-loader-alt animate-spin mr-2'></i>
                  <span>{{ isSubmitting ? 'Guardando...' : 'Guardar Cambios' }}</span>
                </button>
              </div>
            </div>
          </form>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteConfirmation" class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="delete-modal-title" role="dialog" aria-modal="true">
          <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-600/75 dark:bg-gray-800/75 transition-opacity" aria-hidden="true"></div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
              <div class="bg-red-600 dark:bg-red-700 px-6 py-4 flex justify-between items-center">
                <h3 class="text-xl font-bold text-white flex items-center">
                  <i class='bx bx-trash mr-2'></i>
                  Eliminar Oferta de Empleo
                </h3>
                <button @click="closeDeleteConfirmation" class="text-white hover:text-gray-200">
                  <i class='bx bx-x text-2xl'></i>
                </button>
              </div>

              <div class="bg-white dark:bg-gray-800 px-6 py-4">
                <div class="mb-4">
                  <div class="flex items-center justify-center w-16 h-16 mx-auto bg-red-100 dark:bg-red-900 rounded-full mb-4">
                    <i class='bx bx-error-circle text-red-600 dark:text-red-400 text-4xl'></i>
                  </div>
                  
                  <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 text-center mb-2">
                    ¿Estás seguro de eliminar esta oferta?
                  </h4>
                  
                  <p class="text-gray-600 dark:text-gray-300 text-center mb-4">
                    Esta acción <span class="font-bold text-red-600 dark:text-red-400">no se puede deshacer</span> y eliminará permanentemente la oferta de empleo <span class="font-semibold">{{ props.jobOffer.title }}</span>.
                  </p>
                  
                  <div class="bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-800 rounded-lg p-3 mb-4">
                    <div class="flex items-start">
                      <i class='bx bx-info-circle text-amber-600 dark:text-amber-400 text-xl mt-0.5 mr-2'></i>
                      <p class="text-sm text-amber-800 dark:text-amber-200">
                        Se eliminarán todos los datos asociados a esta oferta, incluyendo las aplicaciones de candidatos.
                      </p>
                    </div>
                  </div>
                </div>
                
                <div class="flex flex-col sm:flex-row justify-between gap-3">
                  <button 
                    type="button" 
                    @click="closeDeleteConfirmation"
                    class="order-2 sm:order-1 px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors flex items-center justify-center"
                  >
                    <i class='bx bx-x mr-1'></i>
                    Cancelar
                  </button>
                  
                  <button 
                    type="button"
                    @click="deleteJobOffer"
                    :disabled="deleteCountdown > 0 || isDeleting"
                    :class="[
                      'order-1 sm:order-2 px-4 py-2 rounded-lg text-white flex items-center justify-center transition-colors',
                      deleteCountdown > 0 
                        ? 'bg-red-400 dark:bg-red-500 cursor-not-allowed' 
                        : isDeleting 
                          ? 'bg-red-600 dark:bg-red-700 cursor-not-allowed' 
                          : 'bg-red-600 dark:bg-red-700 hover:bg-red-700 dark:hover:bg-red-800'
                    ]"
                  >
                    <i v-if="isDeleting" class='bx bx-loader-alt animate-spin mr-2'></i>
                    <i v-else class='bx bx-trash mr-2'></i>
                    <span v-if="isDeleting">Eliminando...</span>
                    <span v-else-if="deleteCountdown > 0">Espera {{ deleteCountdown }}s...</span>
                    <span v-else>Eliminar Definitivamente</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style>
.multiselect__tags {
  border-radius: 0.5rem;
  border-color: #d1d5db;
  padding: 0.5rem 0.75rem;
}

.dark .multiselect__tags {
  background-color: #374151; /* dark:bg-gray-700 */
  border-color: #4b5563; /* dark:border-gray-600 */
  color: #e5e7eb; /* dark:text-gray-200 */
}

.multiselect__tag {
  background: #193CB8;
  border-radius: 0.375rem;
}

.dark .multiselect__tag {
  background: #3b82f6; /* dark:bg-blue-500 */
}

.multiselect__option--highlight {
  background: #193CB8;
}

.dark .multiselect__option--highlight {
  background: #3b82f6; /* dark:bg-blue-500 */
}

.multiselect__option--selected.multiselect__option--highlight {
  background: #142d8c;
}

.dark .multiselect__option--selected.multiselect__option--highlight {
  background: #2563eb; /* dark:bg-blue-600 */
}

.dark .multiselect__placeholder,
.dark .multiselect__input {
  color: #e5e7eb; /* dark:text-gray-200 */
}

.dark .multiselect__input::placeholder {
  color: #9ca3af; /* dark:text-gray-400 */
}

.dark .multiselect__content-wrapper {
  background-color: #374151; /* dark:bg-gray-700 */
  border-color: #4b5563; /* dark:border-gray-600 */
}

.dark .multiselect__option {
  color: #e5e7eb; /* dark:text-gray-200 */
}

.dark .multiselect__option--selected {
  background: #1f2937; /* dark:bg-gray-800 */
  color: #e5e7eb; /* dark:text-gray-200 */
}

/* Animación para el loader */
@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style>
