<script setup>
  import { ref } from 'vue'
  import { router } from '@inertiajs/vue3'
  import NavBar from '@/Components/Navbar.vue'
  
  const form = ref({
    title: '',
    description: '',
    minExperience: '',
    minStudies: '',
    vacancies: '',
    workMode: '',
    minimum_salary: '',
    maximum_salary: '',
    skills: [],
    file: null
  })
  
  const skills = ref(['Vue.js', 'Laravel', 'PHP', 'MySQL', 'Git'])
  const newSkill = ref('')
  
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
  
  const handleFileUpload = (event) => {
    form.value.file = event.target.files[0]
  }
  
  const submit = () => {
    router.post('/ofertas/crear', form.value, {
      forceFormData: true
    })
  }

  
  </script>
<template>
  <div class="min-h-screen bg-gray-50 flex flex-col">
    <NavBar />
    <div class="  py-8 px-4">
      <div class="bg-white mx-auto  max-w-4xl rounded-lg shadow-sm p-6 mb-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Crear Nueva Oferta</h1>
        <form @submit.prevent="submit" class="space-y-6">
          <!-- Title -->
          <div>
            <label for="title" class="block text-sm font-medium text-gray-700">Título de la oferta</label>
            <input
              id="title"
              v-model="form.title"
              type="text"
              placeholder="Escribe el título para su oferta..."
              required
              class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
            >
          </div>

          <!-- Description -->
          <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Descripción de la oferta</label>
            <textarea
              id="description"
              v-model="form.description"
              placeholder="Escribe la descripción de la oferta"
              required
              rows="4"
              class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
            ></textarea>
          </div>

          <!-- Experience and Studies -->
          <div class="grid md:grid-cols-2 gap-6">
            <div>
              <label for="minExperience" class="block text-sm font-medium text-gray-700">Experiencia Mínima</label>
              <select
                id="minExperience"
                v-model="form.minExperience"
                required
                class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="">Selecciona la experiencia mínima</option>
                <option value="0">Sin Experiencia</option>
                <option value="1-2">1-2 años</option>
                <option value="3-5">3-4 años</option>
                <option value="5">5 o más años</option>
              </select>
            </div>
            <div>
              <label for="minStudies" class="block text-sm font-medium text-gray-700">Estudios Mínimos</label>
              <select
                id="minStudies"
                v-model="form.minStudies"
                required
                class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="">Selecciona los estudios mínimos</option>
                <option value="high-school">Grado Superior</option>
                <option value="bachelor">Grado Medio</option>
                <option value="master">Grado Universitario</option>
              </select>
            </div>
          </div>

          <!-- Vacancies and Work Mode -->
          <div class="grid md:grid-cols-2 gap-6">
            <div>
              <label for="vacancies" class="block text-sm font-medium text-gray-700">Número de Vacantes</label>
              <input
                id="vacancies"
                v-model="form.vacancies"
                type="number"
                placeholder="Escribe el número de vacantes"
                required
                class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              >
            </div>
            <div>
              <label for="workMode" class="block text-sm font-medium text-gray-700">Modalidad Jornada</label>
              <select
                id="workMode"
                v-model="form.workMode"
                required
                class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="">Selecciona la modalidad</option>
                <option value="part-time">Media jornada</option>
                <option value="full-time">Jornada completa</option>
              </select>
            </div>
          </div>

          <!-- Salary -->
          <div class="grid md:grid-cols-2 gap-6">
            <div>
              <label for="salary" class="block text-sm font-medium text-gray-700">Salario</label>
              <select
                id="salary"
                v-model="form.salary"
                class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="">Selecciona el tipo de salario</option>
                <option value="none">Sin especificar</option>
                <option value="fixed">Especificar salario</option>
              </select>
            </div>
            <div class="grid grid-cols-2 gap-x-4" v-if="form.salary === 'fixed'">
              <div>
                <label for="minimum_salary" class="block text-sm font-medium text-gray-700">Salario Mínimo</label>
                <input
                id="minimum_salary"
                v-model="form.minimum_salary"
                type="number"
                placeholder="Salario mínimo..."
                required
                class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              >
              </div>
              <div>
                <label for="maximum_salary" class="block text-sm font-medium text-gray-700">Salario Máximo</label>
                <input
                id="maximum_salary"
                v-model="form.maximum_salary"
                type="number"
                placeholder="Salario máximo..."
                required
                class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              >
              </div>
            </div>
          </div>

          <!-- Skills -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Habilidades requeridas</label>
            <div class="flex flex-wrap gap-2">
              <span
                v-for="skill in skills"
                :key="skill"
                class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm cursor-pointer hover:bg-blue-200"
                @click="toggleSkill(skill)"
              >
                {{ skill }}
              </span>
              <input
                v-model="newSkill"
                @keyup.enter="addSkill"
                type="text"
                placeholder="Añadir habilidad..."
                class="px-3 py-1 border border-gray-300 rounded-full text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"
              >
            </div>
          </div>

          <!-- File Upload -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Archivos adjuntos</label>
            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
              <div class="space-y-1 text-center">
                <i class='bx bx-upload text-4xl text-gray-400'></i>
                <div class="flex text-sm text-gray-600">
                  <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                    <span>Sube un archivo</span>
                    <input id="file-upload" name="file-upload" type="file" class="sr-only" @change="handleFileUpload">
                  </label>
                  <p class="pl-1">o arrastra y suelta</p>
                </div>
                <p class="text-xs text-gray-500">PNG, JPG, GIF hasta 10MB</p>
              </div>
            </div>
          </div>

          <button type="submit" class="w-full bg-blue-600 text-white py-3 px-6 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
            Publicar Oferta
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

