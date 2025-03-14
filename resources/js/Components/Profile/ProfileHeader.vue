<script setup>
  import { computed, ref } from 'vue';
  import { router } from '@inertiajs/vue3';
  
  const props = defineProps({
    user: Object,
    profile: Object,
    isSameUser: Boolean,
    openEditModal: Function,
  });
  
  const uploadProfileImage = (e) => {
    const file = e.target.files[0];
    const formData = new FormData();
    formData.append('profile_picture', file);
    router.post(`/perfil/${props.user.profile.slang}/update-profile-picture`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
  };
  
  const downloadCV = () => {
    window.location.href = `/perfil/${props.user.profile.slang}/download-cv/`;
  };

  const fullName = computed(() => {
    return `${props.user.name || ''} ${props.user.last_name_1 || ''} ${props.user.last_name_2 || ''}`.trim();
  });
  
  // Computed property for job title/position
  const jobTitle = computed(() => {
    return props.profile?.job_title || 'Estudiante';
  });
  
  // Computed property for graduation year
  const graduationYear = computed(() => {
    return props.profile?.graduation_year || 'Promoción actual';
  });
  
  // Computed property for degree/course
  const degree = computed(() => {
    return props.profile?.degree || 'Desarrollo de Aplicaciones Web';
  });
  </script>
  
  <template>
    <div class="bg-white rounded-lg shadow-md border border-gray-200 w-full">
      <div class="p-6">
        <div class="flex flex-col md:flex-row md:items-center gap-6">
          <!-- Foto de perfil -->
          <div class="relative h-28 w-28 rounded-full bg-white shadow-md overflow-hidden group border-4 border-white mx-auto md:mx-0">
            <img 
              :src="`/storage/${user.profile_picture}` || '/images/default-avatar.png'"  
              :alt="fullName" 
              class="h-full w-full object-cover" 
            />
            
            <!-- Hover para subir nueva imagen (solo para el propio usuario) -->
            <div v-if="isSameUser" class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-60 opacity-0 group-hover:opacity-100 transition-opacity">
              <label class="cursor-pointer text-white bg-[#193CB8] hover:bg-[#2748c6] px-3 py-1.5 rounded-md text-sm font-medium transition-colors">
                <i class="bx bx-upload mr-1"></i> Cambiar
                <input type="file" class="hidden" @change="uploadProfileImage" accept="image/*">
              </label>
            </div>
          </div>
          
          <!-- Información del perfil -->
          <div class="flex-1 text-center md:text-left">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
              <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">{{ fullName }}</h1>
                <p class="text-lg text-[#193CB8] font-medium mt-1">{{ jobTitle }}</p>
                <div class="flex flex-wrap items-center gap-3 mt-2 text-gray-600 justify-center md:justify-start">
                  <div class="flex items-center gap-1">
                    <i class='bx bx-graduation text-[#193CB8]'></i>
                    <span>{{ degree }}</span>
                  </div>
                  <div class="w-1 h-1 bg-gray-300 rounded-full hidden md:block"></div>
                  <div class="flex items-center gap-1">
                    <i class='bx bx-calendar text-[#193CB8]'></i>
                    <span>{{ graduationYear }}</span>
                  </div>
                  <div class="w-1 h-1 bg-gray-300 rounded-full hidden md:block"></div>
                  <div class="flex items-center gap-1">
                    <i class='bx bx-map-pin text-[#193CB8]'></i>
                    <span>{{ profile?.location || 'Ubicación no especificada' }}</span>
                  </div>
                </div>
              </div>
              
              <!-- Botones de acción -->
              <div class="flex items-center gap-3 mt-4 md:mt-0 justify-center md:justify-end">
                <button v-if="isSameUser" @click="props.openEditModal" 
                  class="flex items-center cursor-pointer gap-1.5 bg-[#193CB8] hover:bg-[#2748c6] text-white px-3 py-2 rounded-md transition-colors">
                  <div class="flex justify-center items-center w-30 gap-3">
                  <i class='bx bx-pencil text-x'></i>
                  <span>Editar Perfil</span>
                </div>
                </button>
                
                <button v-else
                  class="flex items-center gap-1.5 bg-[#193CB8] hover:bg-[#2748c6] text-white px-4 py-2 rounded-md transition-colors">
                  <i class='bx bx-envelope'></i>
                  <span>Contactar</span>
                </button>
                
                <button @click="downloadCV" class="flex items-center cursor-pointer gap-1.5 bg-white hover:bg-blue-100 text-gray-700 border border-gray-300 px-4 py-2 rounded-md transition-colors">
                  <i class='bx bx-file text-xl'></i>
                  <span class="hidden sm:inline">Descargar CV</span>
                </button>
              </div>
            </div>
            
            <!-- Redes sociales -->
            <div class="mt-4 flex items-center gap-3 justify-center md:justify-start">
              <a v-if="user.profile.linkedin" :href="user.profile.linkedin" class="bg-blue-50 hover:bg-blue-100 cursor-pointer p-2 flex rounded-full text-[#193CB8] transition-colors">
                <i class='bx bxl-linkedin text-xl'></i>
              </a>
              <a v-if="user.profile.github" :href="user.profile.github" class="bg-blue-50 hover:bg-blue-100 cursor-pointer p-2 flex rounded-full text-[#193CB8] transition-colors">
                <i class='bx bxl-github text-xl'></i>
              </a>
              <a v-if="user.profile.website" :href="user.profile.website" class="bg-blue-50 hover:bg-blue-100 cursor-pointer p-2 flex rounded-full text-[#193CB8] transition-colors">
                <i class='bx bx-globe text-xl'></i>
              </a>
              
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>