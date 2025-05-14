<script setup>
  import { ref } from 'vue';
  import ExperienceModal from './ExperienceModal.vue';
  import DeleteModal from '@/Components/DeleteModal.vue';
  
  const props = defineProps({
    items: Array,  
    title: String,
    user: Object,
    slang: String,
    type: String,
    isSameUser: Boolean
  });
  
  const isExperienceModalOpen = ref(false);
  const isDeleteModalOpen = ref(false);
  const selectedItem = ref(null);
  const modalMode = ref('create'); // 'create' o 'edit'
  
  const handleSaveExperience = (experienceData) => {
    console.log("Nueva experiencia:", experienceData);
  };
  
  const openEditModal = (item) => {
    selectedItem.value = item;
    modalMode.value = 'edit';
    isExperienceModalOpen.value = true;
  };
  
  const openDeleteModal = (item) => {
    selectedItem.value = item;
    isDeleteModalOpen.value = true;
  };
  
  const openCreateModal = () => {
    selectedItem.value = null;
    modalMode.value = 'create';
    isExperienceModalOpen.value = true;
  };
  
  const isExpanded = ref(true); // Cambiado a true por defecto para mostrar las experiencias
  
  const toggleExpand = () => {
    isExpanded.value = !isExpanded.value;
  };
  
  // Iconos según el tipo de experiencia
  const sectionIcon = props.type === 'work' ? 'bx-briefcase' : 'bxs-graduation';
</script>
  
<template>
  <div class="mb-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
      <button @click="toggleExpand"
        class="w-full flex justify-between items-center py-4 px-6 bg-white text-gray-800 transition-all duration-300 ease-in-out hover:bg-gray-50 cursor-pointer">
        <div class="flex items-center gap-3">
          <i :class="['bx text-xl text-[#193CB8]', sectionIcon]"></i>
          <h2 class="text-xl font-semibold">{{ title }}</h2>
          <span v-if="items && items.length" class="bg-[#193CB8] text-white  text-xs rounded-full px-2 py-1">
            {{ items.length }}
          </span>
        </div>
        <div class="space-x-3 flex items-center">
          <button v-if="isSameUser" @click.stop="openCreateModal()" 
            class="flex cursor-pointer items-center gap-1 text-[#193CB8] hover:text-[#2748c6] bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-md transition-colors">
            <i class="bx bx-plus"></i>
            <span class="text-sm font-medium">Añadir</span>
          </button>
          <i class="bx text-xl" :class="isExpanded ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
        </div>
      </button>
      
      <transition 
        enter-active-class="transition-all duration-300 ease-out"
        leave-active-class="transition-all duration-300 ease-in"
        enter-from-class="max-h-0 opacity-0" 
        enter-to-class="max-h-[1000px] opacity-100"
        leave-from-class="max-h-[1000px] opacity-100" 
        leave-to-class="max-h-0 opacity-0"
      >
        <div v-if="isExpanded" class="overflow-hidden">
          <div v-if="items && items.length" class="divide-y divide-gray-100">
            <div v-for="(item, index) in items" :key="index"
            :class="{'border-b border-gray-300': index !== items.length - 1}"
            class="p-6 hover:bg-gray-50 transition-colors duration-200">
              <div class="flex flex-col md:flex-row md:items-start gap-4">
                <!-- Icono o logo -->
                <div class="hidden md:flex items-center justify-center w-12 h-12 bg-blue-50 rounded-lg text-[#193CB8]">
                  <i :class="['bx text-2xl', type === 'work' ? 'bx-building' : 'bx-book-open']"></i>
                </div>
                
                <div class="flex-1">
                  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                    <h3 class="font-semibold text-lg text-[#193CB8]">
                      {{ item.position || item.degree }}
                    </h3>
                    <div class="flex items-center gap-2 text-sm text-gray-500">
                      <i class='bx bx-calendar'></i>
                      <span>{{ item.start_date }} - {{ item.end_date || 'Actualidad' }}</span>
                    </div>
                  </div>
                  
                  <h4 class="text-md text-gray-700 font-medium mt-1">
                    {{ item.company_name || item.institution || item.school_name }}
                  </h4>
                  
                  <p v-if="item.location" class="text-sm text-gray-500 mt-1">
                    <i class='bx bx-map-pin mr-1'></i>
                    {{ item.location }}
                  </p>
                  
                  <p v-if="item.description" class="mt-3 text-gray-600">
                    {{ item.description }}
                  </p>
                  
                  <!-- Botones de acción (solo para el propio usuario) -->
                  <div v-if="isSameUser" class="mt-4 flex items-center gap-2">
                    <button 
                      @click="openEditModal(item)"
                      class="text-gray-500 cursor-pointer hover:text-[#193CB8] transition-colors">
                      <i class='bx bx-pencil text-blue-500'></i>
                    </button>
                    <button 
                      @click="openDeleteModal(item)"
                      class="text-gray-500 cursor-pointer hover:text-red-500 transition-colors">
                      <i class='bx bx-trash text-red-500'></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Empty state -->
          <div v-else class="p-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-50 mb-4">
              <i :class="['bx text-3xl text-[#193CB8]', sectionIcon]"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-800 mb-2">
              {{ type === 'work' ? 'No hay experiencia laboral' : 'No hay formación académica' }}
            </h3>
            <p class="text-gray-500 max-w-md mx-auto">
              {{ isSameUser 
                ? (type === 'work' 
                  ? 'Añade tu experiencia laboral para mostrar tu trayectoria profesional.' 
                  : 'Añade tu formación académica para mostrar tus estudios y certificaciones.') 
                : (type === 'work' 
                  ? 'Este usuario aún no ha añadido experiencia laboral.' 
                  : 'Este usuario aún no ha añadido formación académica.') 
              }}
            </p>
            <button v-if="isSameUser" @click="openCreateModal()"
              class="mt-4 inline-flex items-center cursor-pointer gap-2 bg-[#193CB8] hover:bg-[#2748c6] text-white px-4 py-2 rounded-md transition-colors">
              <i class='bx bx-plus'></i>
              <span>Añadir {{ type === 'work' ? 'experiencia' : 'formación' }}</span>
            </button>
          </div>
        </div>
      </transition>
    </div>
  </div>
  
  <!-- Modal para añadir/editar experiencia -->
  <ExperienceModal 
    :isOpen="isExperienceModalOpen" 
    :slang="slang"
    :type="type"
    :item="selectedItem"
    :mode="modalMode"
    @close="isExperienceModalOpen = false" 
    @save="handleSaveExperience" 
  />
  
  <!-- Modal para confirmar eliminación -->
  <DeleteModal
    :isOpen="isDeleteModalOpen"
    :slang="slang"
    :type="type"
    :item="selectedItem"
    @close="isDeleteModalOpen = false"
  />
</template>

