<script>
  export default {
    data() {
      return {
        form: {
          title: '',
          description: '',
          minExperience: '',
          vacancies: '',
          workMode: '',
          salary: '',
          tags: '',
          file: null
        }
      };
    },
    methods: {
      submit() {
        this.$inertia.post('/job-offers', this.form);
      }
    }
  };
  </script>
  
  <style scoped>
  /* Estilos adicionales si son necesarios */
  </style>
<template>
    <div class="max-w-2xl mx-auto p-6 bg-white shadow-md rounded-lg">
      <h1 class="text-2xl font-bold mb-6">Crear Nueva Oferta</h1>
      <form @submit.prevent="submit">
        <div class="mb-4">
          <label for="title" class="block text-sm font-medium text-gray-700">Título de la oferta</label>
          <input id="title" v-model="form.title" type="text" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div class="mb-4">
          <label for="description" class="block text-sm font-medium text-gray-700">Descripción de la oferta</label>
          <textarea id="description" v-model="form.description" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
        </div>
        <div class="mb-4">
          <label for="minExperience" class="block text-sm font-medium text-gray-700">Experiencia Mínima</label>
          <select id="minExperience" v-model="form.minExperience" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            <option value="0-1">0-1 años</option>
            <option value="1-3">1-3 años</option>
            <option value="3-5">3-5 años</option>
          </select>
        </div>
        <div class="mb-4">
          <label for="vacancies" class="block text-sm font-medium text-gray-700">Número de Vacantes</label>
          <input id="vacancies" v-model="form.vacancies" type="number" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div class="mb-4">
          <label for="workMode" class="block text-sm font-medium text-gray-700">Modalidad Jornada</label>
          <select id="workMode" v-model="form.workMode" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            <option value="full-time">Tiempo completo</option>
            <option value="part-time">Medio tiempo</option>
          </select>
        </div>
        <div class="mb-4">
          <label for="salary" class="block text-sm font-medium text-gray-700">Salario</label>
          <input id="salary" v-model="form.salary" type="text" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div class="mb-4">
          <label for="tags" class="block text-sm font-medium text-gray-700">Etiquetas</label>
          <input id="tags" v-model="form.tags" type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">Multimedia</label>
          <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
            <div class="space-y-1 text-center">
              <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
              <div class="flex text-sm text-gray-600">
                <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                  <span>Subir archivo</span>
                  <input id="file-upload" name="file-upload" type="file" class="sr-only">
                </label>
                <p class="pl-1">o arrastra y suelta</p>
              </div>
              <p class="text-xs text-gray-500">PNG, JPG, GIF hasta 10MB</p>
            </div>
          </div>
        </div>
        <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Publicar Oferta</button>
      </form>
    </div>
  </template>
  
