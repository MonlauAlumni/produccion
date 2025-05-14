<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AdminLayout from './Layouts/AdminLayout.vue';
import InputWithIcon from '@/Components/InputWithIcon.vue';
import axios from 'axios';

const props = defineProps({
  company: {
    type: Object,
    required: true
  }
});

const formCompany = ref({
  company_name: props.company.company_name,
  company_phone: props.company.company_phone,
  fiscal_id: props.company.fiscal_id,
  address: props.company.address,
  zip_code: props.company.zip_code,
  population: props.company.population
});

const isSubmitting = ref(false);
const showSuccess = ref(false);
const errorMessage = ref('');

function updateCompany(event) {
  event.preventDefault();
  isSubmitting.value = true;
  errorMessage.value = '';
  
  axios.put(`/admin/company/${props.company.id}`, formCompany.value)
    .then(response => {
      console.log(response.data);
      showSuccess.value = true;
      setTimeout(() => {
        showSuccess.value = false;
      }, 3000);
    })
    .catch(error => {
      console.error(error);
      errorMessage.value = 'Ha ocurrido un error al guardar los cambios.';
    })
    .finally(() => {
      isSubmitting.value = false;
    });
}
</script>

<template>
  <AdminLayout title="Editar empresa">
    <div class="min-h-full bg-gray-50 p-6">
      <!-- Header section -->
      <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
          <button 
            @click="router.get('/admin/companies')" 
            class="flex items-center text-gray-600 hover:text-primary transition-colors"
          >
            <box-icon name='arrow-back' class="h-5 w-5 text-primary" color="#2563EB"></box-icon>
            <span class="ml-1 text-sm font-medium">Volver</span>
          </button>
        </div>
        <h1 class="text-2xl font-bold text-gray-800">Editar información de empresa</h1>
        <p class="text-gray-500 mt-1">Actualice los detalles de la empresa a continuación</p>
      </div>

      <!-- Form card -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <!-- Form header -->
        <div class="border-b border-gray-100 bg-white px-6 py-4">
          <h2 class="font-semibold text-gray-800">Detalles de la empresa</h2>
        </div>

        <!-- Form content -->
        <form @submit="updateCompany" class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700" for="company_name">
                Nombre de la empresa
              </label>
              <InputWithIcon
                v-model="formCompany.company_name"
                type="text"
                icon="business"
                placeholder="Nombre de la empresa"
                class="w-full"
              />
            </div>
            
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700" for="company_phone">
                Teléfono
              </label>
              <InputWithIcon
                v-model="formCompany.company_phone"
                type="text"
                icon="phone"
                placeholder="Teléfono"
                class="w-full"
              />
            </div>
            
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700" for="fiscal_id">
                Número Fiscal
              </label>
              <InputWithIcon
                v-model="formCompany.fiscal_id"
                type="text"
                icon="wallet"
                placeholder="Número Fiscal"
                class="w-full"
              />
            </div>
            
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700" for="address">
                Dirección
              </label>
              <InputWithIcon
                v-model="formCompany.address"
                type="text"
                icon="map"
                placeholder="Dirección"
                class="w-full"
              />
            </div>
            
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700" for="zip_code">
                Código Postal
              </label>
              <InputWithIcon
                v-model="formCompany.zip_code"
                type="text"
                icon="map-pin"
                placeholder="Código Postal"
                class="w-full"
              />
            </div>
            
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700" for="population">
                Población
              </label>
              <InputWithIcon
                v-model="formCompany.population"
                type="text"
                icon="map"
                placeholder="Población"
                class="w-full"
              />
            </div>
          </div>

          <!-- Success message -->
          <div v-if="showSuccess" class="mt-6 p-3 bg-green-50 border border-green-100 rounded-md">
            <p class="text-green-700 text-sm flex items-center">
              <box-icon name='check-circle' type="solid" color="#16a34a" class="h-4 w-4 mr-2"></box-icon>
              Cambios guardados correctamente
            </p>
          </div>

          <!-- Error message -->
          <div v-if="errorMessage" class="mt-6 p-3 bg-red-50 border border-red-100 rounded-md">
            <p class="text-red-700 text-sm flex items-center">
              <box-icon name='error-circle' type="solid" color="#dc2626" class="h-4 w-4 mr-2"></box-icon>
              {{ errorMessage }}
            </p>
          </div>

          <!-- Form actions -->
          <div class="mt-8 flex justify-end">
            <button 
              type="button" 
              @click="router.get('/admin/companies')"
              class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 mr-3"
            >
              Cancelar
            </button>
            <button 
              type="submit" 
              class="px-6 py-2 bg-primary rounded-md text-sm font-medium text-white shadow-sm hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors"
              :disabled="isSubmitting"
            >
              <span v-if="isSubmitting" class="flex items-center">
                <box-icon name='loader-circle' class="animate-spin h-4 w-4 mr-2" color="#ffffff"></box-icon>
                Guardando...
              </span>
              <span v-else>Guardar cambios</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<style scoped>
.bg-primary {
  background-color: #2563EB;
}

.bg-primary-600 {
  background-color: #1D4ED8;
}

.text-primary {
  color: #2563EB;
}

.focus\:ring-primary-500:focus {
  --tw-ring-color: rgba(37, 99, 235, 0.5);
}

.hover\:text-primary:hover {
  color: #2563EB;
}

/* Smooth transitions */
.transition-colors {
  transition-property: background-color, border-color, color, fill, stroke;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}

/* Form focus styles */
input:focus, select:focus, textarea:focus {
  --tw-ring-color: rgba(37, 99, 235, 0.5);
  --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
  --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color);
  box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000);
  border-color: #2563EB;
}

/* Animation for spinner */
.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
</style>