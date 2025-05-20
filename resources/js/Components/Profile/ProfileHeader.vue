<script setup>
  import { computed, ref } from 'vue';
  import { router, usePage } from '@inertiajs/vue3';
  
  const page = usePage()
  const userRole = computed(() => page.props.auth.user?.roles?.[0]?.name ?? null)
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

  console.log("PFP", props.profile.profile_picture);
  
  const downloadCV = () => {
    window.location.href = `/perfil/${props.user.profile.slang}/download-cv/`;
  };

  // Función para contactar al usuario
  const contactUser = () => {
    // Navegar a la página de mensajes con parámetros para abrir el modal
    // y preseleccionar el usuario
    router.get('/mensajes', {
      openModal: true,
      userId: props.user.id,
      jobId: null, // No hay oferta asociada en este caso
      userName: fullName.value // Usar el nombre completo del usuario
    });
  };

  const fullName = computed(() => {
    return `${props.user.name || ''} ${props.user.last_name_1 || ''}`.trim();
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
  
  // Verificar si el usuario es Alumni Dev
  const isAlumniDev = computed(() => {
    const specialIds = [1016, 1030, 1053, 997];
    console.log("ID", props.user.id);
    return specialIds.includes(props.user.id);
  });
</script>
  
<template>
  <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 w-full">
    <div class="p-6">
      <div class="flex flex-col md:flex-row md:items-center gap-6">
        <!-- Foto de perfil -->
        <div class="relative h-28 w-28 rounded-full bg-white dark:bg-gray-700 shadow-md overflow-hidden group border-4 border-white mx-auto md:mx-0">
         <img 
  :src="profile.profile_picture ? profile.profile_picture : '/images/default-avatar.png'"  
  :alt="fullName" 
  class="h-full w-full object-cover" 
/>



          
          <!-- Hover para subir nueva imagen (solo para el propio usuario) -->
          <div v-if="isSameUser" class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-60 opacity-0 group-hover:opacity-100 transition-opacity">
            <label class="cursor-pointer text-white bg-[#193CB8] dark:bg-blue-900 hover:bg-[#2748c6] dark:hover:bg-blue-800 px-3 py-1.5 rounded-md text-sm font-medium transition-colors">
              <i class="bx bx-upload mr-1"></i> Cambiar
              <input type="file" class="hidden" @change="uploadProfileImage" accept="image/*">
            </label>
          </div>
          
          <!-- Insignia Alumni Dev (si corresponde) -->
          <div v-if="isAlumniDev" class="absolute -bottom-1 -right-1 bg-gradient-to-r from-amber-500 to-yellow-300 text-xs font-bold text-white px-2 py-0.5 rounded-full shadow-md border border-amber-600/20">
            <i class='bx bxs-star mr-0.5'></i>
            Alumni Dev
          </div>
        </div>
        
        <!-- Información del perfil -->
        <div class="flex-1 text-center md:text-left">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
              <div class="flex items-center justify-center md:justify-start flex-wrap gap-2">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-gray-200">{{ fullName }}</h1>
                
                <!-- Etiqueta Alumni Dev (si corresponde) -->
                <span v-if="isAlumniDev" class="ml-4 inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-gradient-to-r from-amber-500 to-yellow-300 text-white shadow-sm">
                  <i class='bx bxs-star mr-1'></i>
                  Desarrollador de Alumni
                </span>
              </div>
              
              <p class="text-lg text-[#193CB8] dark:text-blue-200 font-medium mt-1">{{ jobTitle }}</p>
              <div class="flex flex-wrap items-center gap-3 mt-2 text-gray-600 dark:text-gray-400 justify-center md:justify-start">
                <div class="flex items-center gap-1">
                  <i class='bx bx-graduation text-[#193CB8] dark:text-blue-200'></i>
                  <span>{{ degree }}</span>
                </div>
                <div class="w-1 h-1 bg-gray-300 dark:bg-gray-600 rounded-full hidden md:block"></div>
                <div class="flex items-center gap-1">
                  <i class='bx bx-calendar text-[#193CB8] dark:text-blue-200'></i>
                  <span>{{ graduationYear }}</span>
                </div>
                <div class="w-1 h-1 bg-gray-300 dark:bg-gray-600 rounded-full hidden md:block"></div>
                <div class="flex items-center gap-1">
                  <i class='bx bx-map-pin text-[#193CB8] dark:text-blue-200'></i>
                  <span>{{ profile?.location || 'Ubicación no especificada' }}</span>
                </div>
              </div>
            </div>
            
            <!-- Botones de acción -->
            <div class="flex items-center gap-3 mt-4 md:mt-0 justify-center md:justify-end">
              <button v-if="isSameUser" @click="props.openEditModal" 
                class="flex items-center cursor-pointer gap-1.5 bg-[#193CB8] dark:bg-blue-900 hover:bg-[#2748c6] dark:hover:bg-blue-800 text-white px-3 py-2 rounded-md transition-colors">
                <div class="flex justify-center items-center w-30 gap-3">
                <i class='bx bx-pencil text-x'></i>
                <span>Editar Perfil</span>
              </div>
              </button>
              
              <button v-else
                @click="contactUser"
                v-if="userRole === 'empresa'"
                class="flex items-center gap-1.5 bg-[#193CB8] dark:bg-blue-900 hover:bg-[#2748c6] dark:hover:bg-blue-800 text-white px-4 py-2 rounded-md transition-colors">
                <i class='bx bx-envelope'></i>
                <span>Contactar</span>
              </button>
              
              <button v-if="profile.cv_path" @click="downloadCV" 
              class="flex items-center gap-1.5 cursor-pointer bg-white dark:bg-gray-700 hover:bg-blue-100 dark:hover:bg-gray-600 md:w-40 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 px-4 py-2 rounded-md transition-colors">
              <i class='bx bx-file text-xl'></i>
              <span class="sm:inline">Descargar CV</span>
            </button>
            
            </div>
          </div>
          
          <!-- Redes sociales -->
          <div class="mt-4 flex items-center gap-3 justify-center md:justify-start">
            <a v-if="user.profile.linkedin" :href="user.profile.linkedin" class="bg-blue-50 dark:bg-blue-900 hover:bg-blue-100 dark:hover:bg-blue-800 cursor-pointer p-2 flex rounded-full text-[#193CB8] dark:text-blue-200 transition-colors">
              <i class='bx bxl-linkedin text-xl'></i>
            </a>
            <a v-if="user.profile.github" :href="user.profile.github" class="bg-blue-50 dark:bg-blue-900 hover:bg-blue-100 dark:hover:bg-blue-800 cursor-pointer p-2 flex rounded-full text-[#193CB8] dark:text-blue-200 transition-colors">
              <i class='bx bxl-github text-xl'></i>
            </a>
            <a v-if="user.profile.website" :href="user.profile.website" class="bg-blue-50 dark:bg-blue-900 hover:bg-blue-100 dark:hover:bg-blue-800 cursor-pointer p-2 flex rounded-full text-[#193CB8] dark:text-blue-200 transition-colors">
              <i class='bx bx-globe text-xl'></i>
            </a>
            
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Estilos adicionales para la etiqueta Alumni Dev */
.from-amber-500 {
  --tw-gradient-from: #f59e0b;
  --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(245, 158, 11, 0));
}

.to-yellow-300 {
  --tw-gradient-to: #fcd34d;
}

/* Añadir un efecto de brillo sutil a la etiqueta */
@keyframes shine {
  0% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
  100% {
    background-position: 0% 50%;
  }
}

.bg-gradient-to-r.from-amber-500.to-yellow-300 {
  background-size: 200% auto;
  animation: shine 3s ease-in-out infinite;
}
</style>