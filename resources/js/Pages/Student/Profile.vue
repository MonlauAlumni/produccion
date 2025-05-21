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
  allSkills: Array,
});

const profile = ref(props.profile);

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
  const file = e.target.files[0];
  const formData = new FormData();
  formData.append("banner_url", file);

  router.post(`/perfil/${props.user.profile.slang}/update-banner`, formData, {
    headers: {
      "Content-Type": "multipart/form-data",
    },
    onSuccess: (response) => {
      console.log('Respuesta subida banner:', response);

      if (response.props.profile && response.props.profile.banner_url) {
        props.profile.banner_url = response.props.profile.banner_url;
      } else {
        console.warn('No se encontró banner_url en response.props.profile');
      }
    },


    onError: (errors) => {
      // Manejo de errores
      console.error("Error al subir banner:", errors);
    }
  });
};



// Default banner if none is provided
const defaultBanner = "/public/images/default-banner.jpg";
</script>

<template>
  <Layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex flex-col">
      <!-- Student Banner -->
      <div class="relative w-full h-64 md:h-80 bg-gradient-to-r from-[#193CB8] to-[#2748c6] overflow-hidden">
        <img v-if="profile.banner_url" :src="profile.banner_url || defaultBanner" :alt="profile.banner_url"
          class="w-full h-full object-cover opacity-80" />
        <img v-else src="/public/images/default-banner.jpg" class="w-full h-full object-cover opacity-80" />

        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>

        <!-- Banner upload button for user's own profile -->
        <div v-if="isSameUser" class="absolute z-20 bottom-4 right-4">
          <label
            class="cursor-pointer bg-white/90 dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-blue-200 hover:bg-white text-[#193CB8] px-3 py-2 rounded-md shadow-md flex items-center gap-2 transition-all">
            <i class='bx bx-image-add'></i>
            <span class="text-sm font-medium">Cambiar Banner</span>
            <input type="file" @change="uploadBanner" class="hidden" accept="image/*">
          </label>
        </div>
<<<<<<< HEAD
      </div>

      <main class="flex flex-col items-center justify-center -mt-16 relative z-10 px-4">
        <div class="flex flex-col space-y-6 w-full max-w-5xl">
          <!-- Profile Header (positioned to overlap with banner) -->
          <ProfileHeader :user="user" :isSameUser="isSameUser" :profile="profile" :openEditModal="openEditModal" />

          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2 space-y-6">
              <AboutSection :profile="profile" />
              <ExperienceSection title="Experiencia Laboral" :isSameUser="isSameUser" :items="workExperiences"
                :slang="slang" :type="'work'" />
              <ExperienceSection title="Formación Académica" :isSameUser="isSameUser" :items="educations" :user="user"
                :slang="slang" :type="'education'" />
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
              <!-- Skills Card -->
              <SkillsSection :skills="skills || []" :isSameUser="isSameUser" :slang="slang"
                :allSkills="allSkills || []" />

              <!-- Contact Information -->
               <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
=======
  
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

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                  <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200 flex items-center justify-between">
                    Publicaciones Recientes
                    <a
                      :href="`/connect/search?q=${encodeURIComponent(user.name + ' ' + (user.last_name_1 || '') + ' ' + (user.last_name_2 || ''))}`"
                      class="text-[#193CB8] dark:text-blue-300 text-sm font-medium hover:underline ml-4"
                    >
                      Ver todas
                    </a>
                  </h2>
                  <hr class="border-t border-[#193CB8] dark:border-blue-700 mb-4" />

                  <div v-if="recentPosts && recentPosts.length > 0" class="space-y-4">
                    <PostCard
                      v-for="post in recentPosts"
                      :key="post.id"
                      :post="post"
                      :formatDate="formatDate"
                      :auth="{ auth_user }"
                      :data-post-id="post.id"
                      :isMember="true"
                    />
                  </div>
                  <div v-else class="text-center text-gray-500 dark:text-gray-400">
                    <p>No hay publicaciones recientes.</p>
                  </div>
                </div>
              </div>
              
              <!-- Right Column -->
              <div class="space-y-6">
                <!-- Skills Card -->
                <SkillsSection 
                  :skills="skills || []" 
                  :isSameUser="isSameUser"
                  :slang="slang"
                  :allSkills="allSkills || []"
                />
                
                <!-- Contact Information -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
>>>>>>> c6bdc4bfe9395379a0e5b27cf7371c3b0efeab4b
                  <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">Contacto</h2>
                  <hr class="border-t border-[#193CB8] dark:border-blue-700 mb-4" />
                  
                  <div class="space-y-4">
                    <div class="flex items-center gap-3">
                      <div class="bg-blue-50 dark:bg-blue-900 p-2 rounded-full">
                        <i class='bx bx-envelope text-[#193CB8] dark:text-blue-200 text-xl'></i>
                      </div>
                      <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                        <p class="text-gray-700 dark:text-gray-300 text-sm">{{ user.email || 'No disponible' }}</p>
                      </div>
                    </div>
                    
                    <div class="flex items-center gap-3">
                      <div class="bg-blue-50 dark:bg-blue-900 p-2 rounded-full">
                        <i class='bx bx-phone text-[#193CB8] dark:text-blue-200 text-xl'></i>
                      </div>
                      <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Teléfono</p>
                        <p class="text-gray-700 dark:text-gray-300">{{ profile?.phone || 'No disponible' }}</p>
                      </div>
                    </div>
                    
                    <div class="flex items-center gap-3">
                      <div class="bg-blue-50 dark:bg-blue-900 p-2 rounded-full">
                        <i class='bx bx-map text-[#193CB8] dark:text-blue-200 text-xl'></i>
                      </div>
                      <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Ubicación</p>
                        <p class="text-gray-700 dark:text-gray-300">{{ profile?.location || 'No disponible' }}</p>
                      </div>
                    </div>
                  </div>
                </div>


              <!-- Availability Status -->
             <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                  <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">Disponibilidad</h2>
                  <hr class="border-t border-[#193CB8] dark:border-blue-700 mb-4" />
                  
                  <div class="flex flex-col items-start gap-2">
                    <span class="text-gray-700 dark:text-gray-300">Estado actual:</span>
                    <span class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300 px-3 py-1 rounded-full text-sm font-medium">
                      {{ profile?.availability || 'Disponible para ofertas' }}
                    </span>
                  </div>
                  
                  <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 dark:text-gray-100">
                    <div class="flex items-center gap-2 text-gray-700 dark:text-gray-300">
                      <i class='bx bx-time-five text-[#193CB8] dark:text-blue-200'></i>
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