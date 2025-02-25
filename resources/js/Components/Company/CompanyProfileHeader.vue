<script setup>
    import SocialButton from '../SocialButton.vue'
    import { computed, ref } from 'vue'
    import { router } from '@inertiajs/vue3';
    
    const props = defineProps({
      company: Object,
      profile: Object,
      isAdmin: Boolean,
      openEditModal: Function,
    });

    const uploadProfileImage = (e) => {
      const file = e.target.files[0];
      const formData = new FormData();
      formData.append('logo', file);
      router.post(`/empresa/${props.company.slang}/update-logo`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      }).then((response) => {
        props.company.logo_url = response.data.logo_url;
      });
    };

    </script>
    
    <template>
      <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6">
          <div class="flex flex-row">
            <div class="flex flex-col items-center md:flex-row md:items-start md:space-x-6">
              <div class="relative h-24 w-26 rounded-sm bg-gray-200 overflow-hidden group">
                <!-- Imagen actual de la empresa -->
                <img :src="company.profile_picture" :alt="company.name + ' logo'" class="h-full w-full object-cover" />
            
                <!-- Hover para subir nueva imagen -->
                <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity">
                  <label class="cursor-pointer text-white bg-blue-500 px-4 py-2 rounded-full">
                    <i class="bx bx-upload"></i>  
                    <input type="file" class="hidden" @change="uploadProfileImage">
                  </label>
                </div>
              </div>
              <div class="mt-4 text-center md:mt-0 md:text-left">
                <h1 class="text-2xl font-bold">{{ company.company_name }}</h1>
             
                
              </div>
            </div>
            <div class="flex w-full justify-end mt-6 md:mt-0">
              <div>
                <button @click="props.openEditModal" v-if="isAdmin"
                  class="cursor-pointer text-blue-500 hover:text-blue-600">
                  <i class='bx bx-pencil'></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>