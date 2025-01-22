<template>
    <div class="max-w-4xl mx-auto p-6 bg-white shadow-lg rounded-lg">
      <!-- Sección de Datos del Alumno -->
      <div v-if="currentStep === 1" class="mb-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Datos del Alumno</h2>
        <form @submit.prevent="nextStep">
          <div class="grid grid-cols-1 gap-6 mb-6">
            <div>
              <label for="firstName" class="block text-sm font-medium text-gray-700">Nombre</label>
              <input type="text" id="firstName" v-model="form.name" class="mt-2 p-3 w-full border border-gray-300 rounded-lg" required />
            </div>
  
            <div>
              <label for="lastName" class="block text-sm font-medium text-gray-700">Primer Apellido</label>
              <input type="text" id="lastName" v-model="form.last_name_1" class="mt-2 p-3 w-full border border-gray-300 rounded-lg" required />
            </div>
  
            <div>
              <label for="secondLastName" class="block text-sm font-medium text-gray-700">Segundo Apellido</label>
              <input type="text" id="secondLastName" v-model="form.last_name_2" class="mt-2 p-3 w-full border border-gray-300 rounded-lg" />
            </div>
  
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
              <input type="email" id="email" v-model="form.email" class="mt-2 p-3 w-full border border-gray-300 rounded-lg" required />
            </div>
  
            <div>
              <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
              <input type="password" id="password" v-model="form.password" class="mt-2 p-3 w-full border border-gray-300 rounded-lg" required />
            </div>
  
            <div>
              <label for="confirmPassword" class="block text-sm font-medium text-gray-700">Repetir Contraseña</label>
              <input type="password" id="confirmPassword" v-model="form.confirmPassword" class="mt-2 p-3 w-full border border-gray-300 rounded-lg" required />
            </div>
          </div>
  
          <!-- Botón para pasar a la siguiente sección -->
          <button 
            type="submit" 
            class="w-full py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
            Siguiente: Datos de la Empresa
          </button>
        </form>
      </div>
  
      <!-- Sección de Datos de la Empresa -->
      <div v-if="currentStep === 2" class="company-info">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Datos de la Empresa</h2>
        <form @submit.prevent="submitForm">
          <div class="grid grid-cols-1 gap-6 mb-6">
            <div>
              <label for="companyName" class="block text-sm font-medium text-gray-700">Nombre de la Empresa</label>
              <input type="text" id="companyName" v-model="companyInfo.companyName" class="mt-2 p-3 w-full border border-gray-300 rounded-lg" required />
            </div>
  
            <div>
              <label for="companyPhone" class="block text-sm font-medium text-gray-700">Teléfono de Contacto</label>
              <input type="tel" id="companyPhone" v-model="companyInfo.companyPhone" class="mt-2 p-3 w-full border border-gray-300 rounded-lg" required />
            </div>
  
            <div>
              <label for="fiscalId" class="block text-sm font-medium text-gray-700">Identificación Fiscal</label>
              <input type="text" id="fiscalId" v-model="companyInfo.fiscalId" class="mt-2 p-3 w-full border border-gray-300 rounded-lg" required />
            </div>
  
            <div>
              <label for="description" class="block text-sm font-medium text-gray-700">Descripción de la Empresa</label>
              <textarea id="description" v-model="companyInfo.description" class="mt-2 p-3 w-full border border-gray-300 rounded-lg"></textarea>
            </div>
          </div>
  
          <!-- Botón de registro -->
          <button 
            type="submit" 
            class="w-full py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-200">
            Registrar Empresa
          </button>
        </form>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        currentStep: 1, 
        form: {
          name: '',
          last_name_1: '',
          last_name_2: '',
          email: '',
          password: '',
          confirmPassword: ''
        },
        companyInfo: {
          companyName: '',
          companyPhone: '',
          fiscalId: '',
          description: ''
        }
      };
    },
    methods: {
      nextStep() {
        if (this.validateStudentInfo()) {
          this.currentStep = 2; // Cambiar a la sección de empresa
        } else {
          alert('Por favor, complete todos los campos correctamente.');
        }
      },
      validateStudentInfo() {
        return (
          this.form.firstName &&
          this.form.lastName &&
          this.form.email &&
          this.form.password === this.form.confirmPassword
        );
      },
      submitForm() {
       
      }
      
    }
    
  };
  </script>
  
  <style scoped>
  .company-register {
    max-width: 600px;
    margin: auto;
  }
  </style>
  