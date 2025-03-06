<script setup>
  import NavBar from "@/Components/Navbar.vue";
  import CompanyProfileHeader from "@/Components/Company/CompanyProfileHeader.vue";
  import AboutSection from "@/Components/Profile/AboutSection.vue";
  import JobListingSection from "@/Components/Company/JobListingSection.vue";
  import EditModal from "@/Components/Profile/EditModal.vue";
  import Layout from "@/Components/Layout.vue";
  import { ref } from "vue";
  
  const props = defineProps({
    company: Object,
    jobListings: Array,
    isAdmin: Boolean,
  });
  
  const editModal = ref(null);
  
  const openEditModal = () => {
    if (editModal.value) {
      editModal.value.openModal();
    }
  };
  
  // Default banner if none is provided
  const defaultBanner = "/images/default-company-banner.jpg";
  </script>
  
  <template>
    <Layout>
      <div class="min-h-screen bg-gray-50 flex flex-col">
        <!-- Company Banner -->
        <div class="relative w-full h-64 md:h-80 bg-gradient-to-r from-[#193CB8] to-[#2748c6] overflow-hidden">
          <img 
            :src="company.banner_url || defaultBanner" 
            alt="Company Banner" 
            class="w-full h-full object-cover opacity-80"
          />
          <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
          
          <!-- Banner upload button for admins -->
          <div v-if="isAdmin" class="absolute bottom-4 right-4">
            <label class="cursor-pointer bg-white/90 hover:bg-white text-[#193CB8] px-3 py-2 rounded-md shadow-md flex items-center gap-2 transition-all">
              <i class='bx bx-image-add'></i>
              <span class="text-sm font-medium">Cambiar Banner</span>
              <input type="file" class="hidden" accept="image/*">
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
                  :items="jobListings" 
                  :slang="company.slang" 
                />
              </div>
              
              <!-- Right Column -->
              <div class="space-y-6">
                <!-- Company Stats Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                  <h2 class="text-xl font-semibold mb-4">Estadísticas</h2>
                  <hr class="border-t border-[#193CB8] mb-4" />
                  
                  <div class="space-y-4">
                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-2">
                        <i class='bx bx-briefcase text-[#193CB8] text-xl'></i>
                        <span class="text-gray-600">Ofertas Activas</span>
                      </div>
                      <span class="font-semibold">{{ jobListings?.length || 0 }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-2">
                        <i class='bx bx-user-check text-[#193CB8] text-xl'></i>
                        <span class="text-gray-600">Contrataciones</span>
                      </div>
                      <span class="font-semibold">{{ company.hires || 0 }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-2">
                        <i class='bx bx-calendar text-[#193CB8] text-xl'></i>
                        <span class="text-gray-600">Miembro desde</span>
                      </div>
                      <span class="font-semibold">{{ company.joined_date || 'N/A' }}</span>
                    </div>
                  </div>
                </div>
                
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
                        <p class="text-gray-700">{{ company.email || 'No disponible' }}</p>
                      </div>
                    </div>
                    
                    <div class="flex items-center gap-3">
                      <div class="bg-blue-50 p-2 rounded-full">
                        <i class='bx bx-phone text-[#193CB8] text-xl'></i>
                      </div>
                      <div>
                        <p class="text-sm text-gray-500">Teléfono</p>
                        <p class="text-gray-700">{{ company.phone || 'No disponible' }}</p>
                      </div>
                    </div>
                    
                    <div class="flex items-center gap-3">
                      <div class="bg-blue-50 p-2 rounded-full">
                        <i class='bx bx-map text-[#193CB8] text-xl'></i>
                      </div>
                      <div>
                        <p class="text-sm text-gray-500">Ubicación</p>
                        <p class="text-gray-700">{{ company.location || 'No disponible' }}</p>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Social Media Links -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                  <h2 class="text-xl font-semibold mb-4">Redes Sociales</h2>
                  <hr class="border-t border-[#193CB8] mb-4" />
                  
                  <div class="flex justify-center gap-4">
                    <a href="#" class="bg-blue-50 hover:bg-blue-100 p-3 rounded-full transition-colors">
                      <i class='bx bxl-linkedin text-[#193CB8] text-2xl'></i>
                    </a>
                    <a href="#" class="bg-blue-50 hover:bg-blue-100 p-3 rounded-full transition-colors">
                      <i class='bx bxl-twitter text-[#193CB8] text-2xl'></i>
                    </a>
                    <a href="#" class="bg-blue-50 hover:bg-blue-100 p-3 rounded-full transition-colors">
                      <i class='bx bxl-facebook text-[#193CB8] text-2xl'></i>
                    </a>
                    <a href="#" class="bg-blue-50 hover:bg-blue-100 p-3 rounded-full transition-colors">
                      <i class='bx bx-globe text-[#193CB8] text-2xl'></i>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </main>
      </div>
      
      <!-- Edit Modal -->
      <EditModal :company="company" :profile="company" :isCompany="true" ref="editModal" />
    </Layout>
  </template>