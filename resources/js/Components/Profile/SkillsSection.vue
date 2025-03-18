<script setup>
    import { ref } from 'vue';
    import { router } from '@inertiajs/vue3';
    
    const props = defineProps({
      skills: {
        type: Array,
        default: () => []
      },
      isSameUser: Boolean,
      slang: String
    });
    
    const isAddingSkill = ref(false);
    const newSkill = ref('');
    
    // Datos dummy para probar las habilidades
    const dummySkills = ref([
      { id: 1, name: 'Desarrollo Web' },
      { id: 2, name: 'JavaScript' },
      { id: 3, name: 'Vue.js' },
      { id: 4, name: 'Tailwind CSS' }
    ]);
    
    const addSkill = () => {
      if (!newSkill.value.trim()) return;
    
      // Enviar la nueva habilidad al servidor
      router.post(`/perfil/${props.slang}/skills`, {
        skill: newSkill.value.trim()
      }).then(() => {
        // Limpiar el campo después de añadir
        newSkill.value = '';
        isAddingSkill.value = false;
      });
    };
    
    const removeSkill = (skillId) => {
      router.delete(`/perfil/${props.slang}/skills/${skillId}`);
    };
    </script>
    
    <template>
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-xl font-semibold">Habilidades</h2>
          <button 
            v-if="isSameUser && !isAddingSkill" 
            @click="isAddingSkill = true"
            class="text-[#193CB8] hover:text-[#2748c6] transition-colors"
          >
            <i class='bx bx-plus-circle text-xl'></i>
          </button>
        </div>
        <hr class="border-t border-[#193CB8] mb-4" />
        
        <!-- Formulario para añadir habilidad -->
        <div v-if="isAddingSkill && isSameUser" class="mb-4">
          <div class="flex gap-2">
            <input 
              v-model="newSkill" 
              type="text" 
              placeholder="Añadir nueva habilidad" 
              class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#193CB8]"
              @keyup.enter="addSkill"
            />
            <button 
              @click="addSkill"
              class="bg-[#193CB8] hover:bg-[#2748c6] text-white px-3 py-2 rounded-md transition-colors"
            >
              <i class='bx bx-check'></i>
            </button>
            <button 
              @click="isAddingSkill = false"
              class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-2 rounded-md transition-colors"
            >
              <i class='bx bx-x'></i>
            </button>
          </div>
        </div>
        
        <!-- Lista de habilidades -->
        <div v-if="dummySkills && dummySkills.length > 0" class="flex flex-wrap gap-2">
          <div 
            v-for="skill in dummySkills" 
            :key="skill.id" 
            class="group relative bg-blue-50 text-[#193CB8] px-3 py-1.5 rounded-md text-sm font-medium"
          >
            {{ skill.name }}
            <button 
              v-if="isSameUser"
              @click="removeSkill(skill.id)"
              class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full p-0.5 opacity-0 group-hover:opacity-100 transition-opacity"
            >
              <i class='bx bx-x text-xs'></i>
            </button>
          </div>
        </div>
        
        <!-- Estado vacío -->
        <div v-else class="bg-gray-50 p-4 rounded-md border border-dashed border-gray-300 text-gray-500 text-center">
          <i class='bx bx-code-block text-2xl mb-2'></i>
          <p>Aún no se han añadido habilidades.</p>
          <button 
            v-if="isSameUser && !isAddingSkill"
            @click="isAddingSkill = true"
            class="mt-2 text-[#193CB8] hover:underline font-medium"
          >
            Añadir habilidades
          </button>
        </div>
        
       
      </div>
    </template>
    