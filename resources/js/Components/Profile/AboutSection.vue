<script setup>
  import { ref } from "vue";
  import { defineProps } from "vue";
  
  defineProps({
    profile: Object,
    isCompany: Boolean,
  });
  </script>
  
  <template>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
      <div class="p-6">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-xl font-semibold">{{ isCompany ? 'Sobre nosotros' : 'Sobre mí' }}</h2>
          <i class='bx bx-building-house text-xl text-[#193CB8]' v-if="isCompany"></i>
          <i class='bx bx-user text-xl text-[#193CB8]' v-else></i>
        </div>
        <hr class="border-t border-[#193CB8] mb-4" />
        
        <div v-if="profile && profile.description" class="text-gray-600 leading-relaxed">
          <p>{{ profile.description }}</p>
        </div>
        <div v-else class="bg-gray-50 p-4 rounded-md border border-dashed border-gray-300 text-gray-500 text-center">
          <i class='bx bx-info-circle text-2xl mb-2'></i>
          <p>{{ isCompany ? 'Esta empresa aún no ha añadido una descripción.' : 'Este usuario aún no ha añadido una descripción.' }}</p>
        </div>
        
        <!-- Sección adicional para empresas -->
        <div v-if="isCompany && profile.founded_year" class="mt-6 pt-6 border-t border-gray-200">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex items-center gap-3">
              <div class="bg-blue-50 p-2 rounded-full">
                <i class='bx bx-calendar text-[#193CB8] text-xl'></i>
              </div>
              <div>
                <p class="text-sm text-gray-500">Año de fundación</p>
                <p class="text-gray-700 font-medium">{{ profile.founded_year }}</p>
              </div>
            </div>
            
            <div class="flex items-center gap-3">
              <div class="bg-blue-50 p-2 rounded-full">
                <i class='bx bx-globe text-[#193CB8] text-xl'></i>
              </div>
              <div>
                <p class="text-sm text-gray-500">Sitio web</p>
                <a :href="profile.website" target="_blank" class="text-[#193CB8] hover:underline font-medium">
                  {{ profile.website || 'No disponible' }}
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>