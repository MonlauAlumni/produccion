<script setup>
  import NavBar from "@/Components/Navbar.vue";
  import ProfileHeader from "@/Components/Profile/ProfileHeader.vue";
  import AboutSection from "@/Components/Profile/AboutSection.vue";
  import ExperienceSection from "@/Components/Profile/ExperienceSection.vue";
  import SkillsSection from "@/Components/Profile/SkillsSection.vue";
  import EditModalStudent from "@/Pages/Student/EditModalStudent.vue";
  import Layout from "@/Components/Layout.vue";
  import { ref } from "vue";
  import { router } from "@inertiajs/vue3";
  
  const props = defineProps({
    user: Object,
    profile: Object,
    educations: Array,
    workExperiences: Array,
    skills: Array,
    isSameUser: Boolean,
    slang: String,
  });
  
  const editModal = ref(null);
  
  const openEditModal = () => {
    if (editModal.value) {
      editModal.value.openModal();
    }
  };

  const formatDate = (date) => {
    if (!date) return null;

    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    const formattedDate = new Date(date).toLocaleDateString('es-ES', options);
    return formattedDate;
  }

  const uploadBanner = (e) => {
  const file = e.target.files[0];  // Obtienes el archivo desde el input
  const formData = new FormData();  // Creamos un objeto FormData para enviar el archivo
  formData.append("banner_url", file);  // Añadimos el archivo al FormData con la clave 'banner_url'
  
  console.log(props.user.profile);  // Solo para comprobar el perfil del usuario, si es necesario

  // Hacemos el POST a la API para actualizar el banner
  router.post(`/perfil/${props.user.profile.slang}/update-banner`, formData, {
    headers: {
      "Content-Type": "multipart/form-data",  // Indica que se enviará un archivo
    },
    onSuccess: () => {
      // Acción a realizar cuando el banner se haya actualizado correctamente
      props.profile.banner_url = response.data.banner_url;
    },
    onError: (errors) => {
      // Acción a realizar si ocurre un error
      
  
    }
  });
};
  

  // Default banner if none is provided
  const defaultBanner = "/public/images/default-banner.jpg";
  </script>
  
  <template>
    <Layout>
      <div class="min-h-screen bg-gray-50 flex flex-col">
        <!-- Student Banner -->
        <div class="relative w-full h-64 md:h-80 bg-gradient-to-r from-[#193CB8] to-[#2748c6] overflow-hidden">
          <img v-if="profile.banner_url"
            :src="profile.banner_url || defaultBanner" 
            :alt="profile.banner_url" 
            class="w-full h-full object-cover opacity-80"
          />
          <img v-else
     src="/public/images/default-banner.jpg"
     class="w-full h-full object-cover opacity-80"
/>

          <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
          
          <!-- Banner upload button for user's own profile -->
          <div v-if="isSameUser"  class="absolute z-20 bottom-4 right-4">
            <label class="cursor-pointer bg-white/90 hover:bg-white text-[#193CB8] px-3 py-2 rounded-md shadow-md flex items-center gap-2 transition-all">
              <i class='bx bx-image-add'></i>
              <span class="text-sm font-medium">Cambiar Banner</span>
              <input type="file" @change="uploadBanner" class="hidden" accept="image/*">
            </label>
          </div>
        </div>
  
        <main class="flex flex-col items-center justify-center -mt-16 relative z-10 px-4">
          <div class="flex flex-col space-y-6 w-full max-w-5xl">
            <!-- Profile Header (positioned to overlap with banner) -->
            <ProfileHeader 
              :user="user" 
              :isSameUser="isSameUser" 
              :profile="profile" 
              :openEditModal="openEditModal" 
            />
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div class="md:col-span-2 space-y-6">
                <AboutSection :profile="profile" />
                <ExperienceSection 
                  title="Experiencia Laboral" 
                  :isSameUser="isSameUser" 
                  :items="workExperiences" 
                  :slang="slang" 
                  :type="'work'" 
                 
                />
                <ExperienceSection 
                  title="Formación Académica" 
                  :isSameUser="isSameUser" 
                  :items="educations" 
                  :user="user" 
                  :slang="slang"
                  :type="'education'" 
                />
              </div>
              
              <!-- Right Column -->
              <div class="space-y-6">
                <!-- Skills Card -->
                <SkillsSection 
                  :skills="skills || []" 
                  :isSameUser="isSameUser"
                  :slang="slang"
                />
                
                <!-- Contact Information -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                  <h2 class="text-xl font-semibold mb-4">Contacto</h2>
                  <hr class="border-t border-[#193CB8] mb-4" />
                  
                  <div class="space-y-4">
                    <div class="flex items-center gap-3">
                      <div class="bg-blue-50 p-2 rounded-full">
                        <i class='bx bx-envelope text-[#193CB8] text-xl'></i>
                      </div>
                      <div>
                        <p class="text-sm text-gray-500">Email</p>
                        <p class="text-gray-700 text-sm">{{ user.email || 'No disponible' }}</p>
                      </div>
                    </div>
                    
                    <div class="flex items-center gap-3">
                      <div class="bg-blue-50 p-2 rounded-full">
                        <i class='bx bx-phone text-[#193CB8] text-xl'></i>
                      </div>
                      <div>
                        <p class="text-sm text-gray-500">Teléfono</p>
                        <p class="text-gray-700">{{ profile?.phone || 'No disponible' }}</p>
                      </div>
                    </div>
                    
                    <div class="flex items-center gap-3">
                      <div class="bg-blue-50 p-2 rounded-full">
                        <i class='bx bx-map text-[#193CB8] text-xl'></i>
                      </div>
                      <div>
                        <p class="text-sm text-gray-500">Ubicación</p>
                        <p class="text-gray-700">{{ profile?.location || 'No disponible' }}</p>
                      </div>
                    </div>
                  </div>
                </div>
                
                
                <!-- Availability Status -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                  <h2 class="text-xl font-semibold mb-4">Disponibilidad</h2>
                  <hr class="border-t border-[#193CB8] mb-4" />
                  
                  <div class="flex flex-col items-start gap-2">
                    <span class="text-gray-700">Estado actual:</span>
                    
      
                  <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                    {{ profile?.availability || 'Disponible para ofertas' }}
                  </span>
                </div>
                  
                  <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="flex items-center gap-2 text-gray-700">
                      <i class='bx bx-time-five text-[#193CB8]'></i>
                      <span>Última actualización: </span>
                    </div>
                    {{ formatDate(profile?.updated_at) || 'Recientemente' }}
                  </div>
                  
                </div>
              </div>
            </div>
          </div>
        </main>
      </div>
      
      <!-- Edit Modal -->
      
      <EditModalStudent :user="user" :profile="profile" ref="editModal" />
     </Layout>
  </template>