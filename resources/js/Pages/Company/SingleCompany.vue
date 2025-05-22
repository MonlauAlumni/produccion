<script setup>
  import NavBar from "@/Components/Navbar.vue";
  import CompanyProfileHeader from "@/Components/Company/CompanyProfileHeader.vue";
  import AboutSection from "@/Components/Profile/AboutSection.vue";
  import JobListingSection from "@/Components/Company/JobListingSection.vue";
  import EditModalCompany from "@/Pages/Company/EditModalCompany.vue";
  import Layout from "@/Components/Layout.vue";
  import { ref } from "vue";
  import { router } from "@inertiajs/vue3";
  
  const props = defineProps({
    company: Object,
    isAdmin: Boolean,
  });
  
  const editModal = ref(null);
  
  const openEditModal = () => {
    if (editModal.value) {
      editModal.value.openModal();
    }
  };

  const uploadBanner = (e) => {
    const file = e.target.files[0];
    const formData = new FormData();
    formData.append("banner_url", file);
    router.post(`/empresa/${props.company.slang}/update-banner`, formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    }).then((response) => {
      props.company.banner_url = response.data.banner_url;
    });
  };

  
  </script>
  
  <template>
    <Layout>
      <div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex flex-col">
        <!-- Company Banner -->
        <div class="relative w-full h-64 md:h-80 bg-gradient-to-r from-[#193CB8] to-[#2748c6] overflow-hidden">
          
      
            <img 
              src="/public/images/default-company-banner.jpg" 
              alt="Default company banner" 
              class="w-full h-full object-cover opacity-80"
              v-if="!company.banner_url"
            />


      
          <img 
            v-if="company.banner_url"
            :src="company.banner_url || defaultBanner" 
            :alt="company.name + ' banner'"
            class="w-full h-full object-cover opacity-80"
          />
          <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
          
       
          <div class="absolute z-20 right-4 top-4 2xl:top-auto 2xl:bottom-4">
            <label class="cursor-pointer bg-white/90 dark:bg-gray-800 hover:bg-white dark:hover:bg-gray-700 text-[#193CB8] dark:text-blue-200 px-3 py-2 rounded-md shadow-md flex items-center gap-2 transition-all">
              <i class='bx bx-image-add'></i>
              <span class="text-sm font-medium">Cambiar Banner</span>
              <input type="file" @change="uploadBanner" class="hidden" accept="image/*">
            </label>
          </div>
         
        </div>
  
        <main class="flex flex-col items-center justify-center -mt-16 relative z-10 px-4">
          <div class="flex flex-col space-y-6 w-full max-w-5xl">
            <!-- Company Profile Header (positioned to overlap with banner) -->
            <CompanyProfileHeader 
              :company="company" 
              :isAdmin="isAdmin" 
              :profile="company" 
              :openEditModal="openEditModal" 
            />
            
            <!-- Company Information Sections -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <!-- Left Column -->
              <div class="md:col-span-2 space-y-6">
                <AboutSection :profile="company" :isCompany="true" />
                <JobListingSection 
                  title="Ofertas de Empleo" 
                  :isAdmin="isAdmin" 
                  :items="company.jobOffers" 
                  :slang="company.slang" 
                />
              </div>
              
              <!-- Right Column -->
              <div class="space-y-6">
                <!-- Company Stats Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                  <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">Estadísticas</h2>
                  <hr class="border-t border-[#193CB8] dark:border-blue-700 mb-4" />
                  
                  <div class="space-y-4">
                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-2">
                        <i class='bx bx-briefcase text-[#193CB8] dark:text-blue-200 text-xl'></i>
                        <span class="text-gray-600 dark:text-gray-400">Ofertas Activas</span>
                      </div>
                      <span class="font-semibold text-gray-800 dark:text-gray-200">{{ jobListings?.length || 0 }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-2">
                        <i class='bx bx-user-check text-[#193CB8] dark:text-blue-200 text-xl'></i>
                        <span class="text-gray-600 dark:text-gray-400">Contrataciones</span>
                      </div>
                      <span class="font-semibold text-gray-800 dark:text-gray-200">{{ company.hires || 0 }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-2">
                        <i class='bx bx-calendar text-[#193CB8] dark:text-blue-200 text-xl'></i>
                        <span class="text-gray-600 dark:text-gray-400">Miembro desde</span>
                      </div>
                      <span class="font-semibold text-gray-800 dark:text-gray-200">{{ company.joined_date || 'N/A' }}</span>
                    </div>
                  </div>
                </div>
                
                <!-- Contact Information -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                  <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">Contacto</h2>
                  <hr class="border-t border-[#193CB8] dark:border-blue-700 mb-4" />
                  
                  <div class="space-y-4">
                    <div class="flex items-center gap-3">
                      <div class="bg-blue-50 dark:bg-blue-900 p-2 flex rounded-full">
                        <i class='bx bx-envelope text-[#193CB8] dark:text-blue-200 text-xl'></i>
                      </div>
                      <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                        <p class="text-gray-700 dark:text-gray-300">{{ company.email || 'No disponible' }}</p>
                      </div>
                    </div>
                    
                    <div class="flex items-center gap-3">
                      <div class="bg-blue-50 dark:bg-blue-900 p-2 flex rounded-full">
                        <i class='bx bx-phone text-[#193CB8] dark:text-blue-200 text-xl'></i>
                      </div>
                      <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Teléfono</p>
                        <p class="text-gray-700 dark:text-gray-300">{{ company.company_phone || 'No disponible' }}</p>
                      </div>
                    </div>
                    
                    <div class="flex items-center gap-3">
                      <div class="bg-blue-50 dark:bg-blue-900 p-2 flex rounded-full">
                        <i class='bx bx-map text-[#193CB8] dark:text-blue-200 text-xl'></i>
                      </div>
                      <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Ubicación</p>
                        <p class="text-gray-700 dark:text-gray-300">{{ company.location || 'No disponible' }}</p>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Social Media Links -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                  <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">Redes Sociales</h2>
                  <hr class="border-t border-[#193CB8] dark:border-blue-700 mb-4" />
                  
                  <div class="flex justify-center gap-4">
                    <div class="flex items-center justify-center">
                    <a :href="company.linkedin" class="bg-blue-50 dark:bg-blue-900 flex hover:bg-blue-100 dark:hover:bg-blue-800 p-2 rounded-full transition-colors">
                      
                      <i class='bx bxl-linkedin text-[#193CB8] dark:text-blue-200 text-2xl'></i>
                    </a>
                  </div>
                    <div class="flex items-center justify-center">
                    <a :href="company.twitter" class="bg-blue-50 dark:bg-blue-900 flex hover:bg-blue-100 dark:hover:bg-blue-800 p-2 rounded-full transition-colors">
                      <i class='bx bxl-twitter text-[#193CB8] dark:text-blue-200 text-2xl'></i>
                    </a>
                  </div>
                    <div class="items-center flex justify-center">
                    <a :href="company.facebook" class="bg-blue-50 dark:bg-blue-900 hover:bg-blue-100 dark:hover:bg-blue-800 flex p-2 rounded-full transition-colors">
                      <i class='bx bxl-facebook text-[#193CB8] dark:text-blue-200 text-2xl'></i>
                    </a>
                  </div>
                    <div class="flex items-center">
                    <a :href="company.instagram" class="bg-blue-50 dark:bg-blue-900 hover:bg-blue-100 dark:hover:bg-blue-800 flex items-center p-2 rounded-full transition-colors">
                      <i class='bx bxl-instagram text-[#193CB8] dark:text-blue-200 text-2xl'></i>
                    </a>
                  </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </main>
      </div>
      
      <!-- Edit Modal -->

      <EditModalCompany :company="company" :profile="company" :isCompany="true" ref="editModal" />
   </Layout>
  </template>