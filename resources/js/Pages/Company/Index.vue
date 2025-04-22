<template>
    <Layout>
      <div class="bg-gray-50 min-h-screen py-8">
        <div class="container mx-auto px-4">
          <!-- Cabecera -->
          <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Empresas</h1>
            <p class="text-gray-600 max-w-2xl mx-auto">
              Explora las empresas registradas en Alumni y conecta con ellas para descubrir oportunidades profesionales
            </p>
          </div>
          
          <!-- Filtros y búsqueda -->
          <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <form @submit.prevent="search">
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Búsqueda por nombre -->
                <div>
                  <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Buscar por nombre</label>
                  <div class="relative">
                    <input
                      type="text"
                      id="search"
                      v-model="filters.search"
                      placeholder="Nombre de la empresa..."
                      class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-[#193CB8] focus:border-[#193CB8] focus:outline-none"
                    />
                    <i class='bx bx-search absolute right-3 top-2.5 text-gray-400'></i>
                  </div>
                </div>
                
                <!-- Filtro por sector -->
                <div>
                  <label for="sector" class="block text-sm font-medium text-gray-700 mb-1">Sector</label>
                  <select
                    id="sector"
                    v-model="filters.sector"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-[#193CB8] focus:border-[#193CB8] focus:outline-none"
                  >
                    <option value="">Todos los sectores</option>
                    <option v-for="sector in sectors" :key="sector.id" :value="sector.id">
                      {{ sector.name }}
                    </option>
                  </select>
                </div>
                
                <!-- Filtro por ubicación -->
                <div>
                  <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Ubicación</label>
                  <select
                    id="location"
                    v-model="filters.location"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-[#193CB8] focus:border-[#193CB8] focus:outline-none"
                  >
                    <option value="">Todas las ubicaciones</option>
                    <option v-for="location in locations" :key="location" :value="location">
                      {{ location }}
                    </option>
                  </select>
                </div>
              </div>
              
              <div class="flex justify-end mt-4">
                <button
                  type="button"
                  @click="resetFilters"
                  class="px-4 py-2 mr-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                >
                  Limpiar filtros
                </button>
                <button
                  type="submit"
                  class="px-4 py-2 bg-[#193CB8] text-white rounded-lg hover:bg-[#142d8c] transition-colors"
                >
                  <i class='bx bx-search mr-1'></i>
                  Buscar
                </button>
              </div>
            </form>
          </div>
          
          <!-- Resultados -->
          <div v-if="companies.data.length > 0">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <CompanyCard
                v-for="company in companies.data"
                :key="company.id"
                :company="company"
              />
            </div>
            
            <!-- Paginación -->
        
          </div>
          
          <!-- Estado vacío -->
          <div v-else class="bg-white rounded-lg shadow-md p-8 text-center">
            <div class="flex flex-col items-center">
              <i class='bx bx-building-house text-6xl text-gray-300 mb-4'></i>
              <h3 class="text-xl font-semibold text-gray-700 mb-2">No se encontraron empresas</h3>
              <p class="text-gray-500 mb-4">Prueba con otros filtros o términos de búsqueda</p>
              <button
                @click="resetFilters"
                class="px-4 py-2 bg-[#193CB8] text-white rounded-lg hover:bg-[#142d8c] transition-colors"
              >
                Ver todas las empresas
              </button>
            </div>
          </div>
        </div>
      </div>
    </Layout>
  </template>
  
  <script setup>
  import { ref, watch, onMounted } from 'vue';
  import { usePage } from '@inertiajs/vue3';
  import Layout from '../../Components/Layout.vue';
  import CompanyCard from '../../Components/Company/CompanyCard.vue';

  import { router,  } from '@inertiajs/vue3'
  
  const props = defineProps({
    companies: Object,
    locations: Array,
    filters: Object,
  });
  const page = usePage();
  const companies = ref(props.companies);
  const locations = ref(props.locations);
 
  
  const sectors = [
    {
        id: 1,
        name: 'Tecnología',
        },
        {
        id: 2,
        name: 'Salud',
        },
        {
        id: 3,
        name: 'Finanzas',
        },
        {
        id: 4,
        name: 'Educación',
        },
        {
        id: 5,
        name: 'Retail',
    }
  ]
  </script>