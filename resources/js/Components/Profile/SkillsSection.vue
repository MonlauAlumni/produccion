<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';
import Multiselect from 'vue-multiselect';

const props = defineProps({
  skills: {
    type: Array,
    default: () => []
  },
  isSameUser: Boolean,
  slang: String,
  allSkills: {
    type: Array,
    default: () => []
  }
});

const profileSkills = ref(props.skills);
const isAddingSkill = ref(false);
const selectedSkill = ref(null);

// Observar cambios en las habilidades recibidas por props
watch(() => props.skills, (newSkills) => {
  profileSkills.value = newSkills;
}, { deep: true });

const addSkill = async () => {
  if (!selectedSkill.value) return;
  try {
    const { data } = await axios.post(`/perfil/${props.slang}/skills`, {
      skillId: selectedSkill.value.id
    });
    profileSkills.value.push(data);
    selectedSkill.value = null;
    isAddingSkill.value = false;
  } catch (e) {
    console.error('Error al añadir habilidad', e);
  }
};

const removeSkill = async (skillId) => {
  try {
    await axios.delete(`/perfil/${props.slang}/skills/${skillId}`);
    profileSkills.value = profileSkills.value.filter(s => s.id !== skillId);
  } catch (e) {
    console.error('Error al eliminar habilidad', e);
  }
};

const cancelAddSkill = () => {
  selectedSkill.value = null;
  isAddingSkill.value = false;
};
</script>

<template>
  <section 
    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6" 
    aria-labelledby="skills-heading"
  >
    <!-- Encabezado -->
    <div class="flex items-center justify-between mb-4">
      <h2 id="skills-heading" class="text-xl font-semibold text-gray-800 dark:text-gray-200">Habilidades</h2>
      <button 
        v-if="isSameUser && !isAddingSkill" 
        @click="isAddingSkill = true"
        class="text-[#193CB8] dark:text-blue-200 hover:text-[#2748c6] dark:hover:text-blue-300 transition-colors p-1.5 rounded-full hover:bg-blue-50 dark:hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-[#193CB8] focus:ring-offset-2"
        aria-label="Añadir habilidad"
      >
        <i class='bx bx-plus-circle text-xl' aria-hidden="true"></i>
      </button>
    </div>
    <hr class="border-t border-[#193CB8] dark:border-blue-700 mb-4" />
    
    <!-- Formulario para añadir habilidad -->
    <div v-if="isAddingSkill && isSameUser" class="mb-4">
      <div class="space-y-3">
        <!-- Multiselect con etiqueta visible -->
        <div>
          <label for="skill-select" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Selecciona una habilidad
          </label>
          <Multiselect
            v-model="selectedSkill"
            :options="allSkills"
            label="name"
            track-by="id"
            placeholder="Buscar habilidad"
            class="skill-select"
            :searchable="true"
            :clear-on-select="true"
            :close-on-select="true"
            id="skill-select"
          />
        </div>
        
        <!-- Botones de acción -->
        <div class="flex gap-2">
          <button 
            @click="addSkill" 
            class="bg-[#193CB8] dark:bg-blue-900 hover:bg-[#2748c6] dark:hover:bg-blue-800 text-white px-4 py-2 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-[#193CB8] focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed flex-1"
            aria-label="Confirmar añadir habilidad"
            :disabled="!selectedSkill"
          >
            <span class="flex items-center justify-center gap-1">
              <i class='bx bx-check' aria-hidden="true"></i>
              <span>Añadir</span>
            </span>
          </button>
          <button 
            @click="cancelAddSkill" 
            class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 flex-1"
            aria-label="Cancelar añadir habilidad"
          >
            <span class="flex items-center justify-center gap-1">
              <i class='bx bx-x' aria-hidden="true"></i>
              <span>Cancelar</span>
            </span>
          </button>
        </div>
      </div>
    </div>
    
    <!-- Lista de habilidades -->
    <div 
      v-if="profileSkills && profileSkills.length > 0" 
      class="flex flex-wrap gap-2"
      role="list"
      aria-label="Lista de habilidades"
    >
      <div 
        v-for="skill in profileSkills" 
        :key="skill.id" 
        class="group relative bg-blue-50 dark:bg-blue-900 text-[#193CB8] dark:text-blue-200 px-3 py-1.5 rounded-md text-sm font-medium"
        role="listitem"
      >
        {{ skill.name }}
        <button 
          v-if="isSameUser"
          @click="removeSkill(skill.id)"
          class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-4 h-4 flex items-center justify-center opacity-0 group-hover:opacity-100 focus:opacity-100 transition-opacity"
          :aria-label="`Eliminar habilidad ${skill.name}`"
          tabindex="0"
        >
          <i class='bx bx-x text-xs' aria-hidden="true"></i>
        </button>
      </div>
    </div>
    
    <!-- Estado vacío -->
    <div 
      v-else 
      class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md border border-dashed border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 text-center"
      role="status"
    >
      <i class='bx bx-code-block text-2xl mb-2' aria-hidden="true"></i>
      <p>Aún no se han añadido habilidades.</p>
      <button 
        v-if="isSameUser && !isAddingSkill"
        @click="isAddingSkill = true"
        class="mt-2 text-[#193CB8] dark:text-blue-200 hover:underline font-medium focus:outline-none focus:ring-2 focus:ring-[#193CB8] focus:ring-offset-2"
        aria-label="Añadir habilidades"
      >
        Añadir habilidades
      </button>
    </div>
  </section>
</template>

<style scoped>

:deep(.multiselect__content-wrapper) {
  width: 100% !important;
  max-width: 100% !important;
  min-width: 100% !important;
  box-sizing: border-box;
}

:deep(.multiselect__content) {
  width: 100% !important;
  box-sizing: border-box;
}

:deep(.skill-select) {
  width: 100%;
}

:deep(.multiselect) {
  width: 100%;
}

:deep(.skill-select) {
  width: 100%;
}

:deep(.multiselect__tags) {
  min-height: 40px;
  
 
}

:deep(.multiselect__placeholder) {
  padding-top: 0;
  margin-bottom: 0;
}

:deep(.multiselect__input) {
  margin-bottom: 0;
}

:deep(.multiselect__content-wrapper) {
  width: 100% !important;
  max-width: 100% !important;
  min-width: 100% !important;
}

:deep(.multiselect__option--highlight) {
  background: #193CB8;
  cursor: pointer;
}

:deep(.multiselect__option--highlight:after) {
  background: #193CB8;
}

:deep(.multiselect__single) {
  margin-bottom: 0;
}

/* Mejoras para enfoque */
:deep(.multiselect--active) {
  outline: 2px solid #193CB8;
  outline-offset: 2px;
}
:deep(.multiselect__content) {
  max-height: 200px; /* Limita a aprox. 5 elementos */
  overflow-y: auto;
}

/* Estilo de tarjeta para cada opción del dropdown */
:deep(.multiselect__option) {
  background-color: #ffffff;
  margin: 4px 8px;
  padding: 8px 12px;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
  transition: background-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
  display: flex;
  align-items: center;
}

/* Hover de tarjeta */
:deep(.multiselect__option:hover) {
  background-color: #f0f4ff;
  box-shadow: 0 4px 12px rgba(25, 60, 184, 0.15);
  transform: translateY(-2px);
}

/* Opción activa resaltada */
:deep(.multiselect__option--highlight) {
  background-color: #e0e7ff;
  border-color: #193CB8;
  box-shadow: 0 2px 8px rgba(25, 60, 184, 0.2);
}

/* Ajustes para pantallas pequeñas */
@media (max-width: 640px) {
  :deep(.multiselect__select) {
    height: 38px;
  }
}
</style>