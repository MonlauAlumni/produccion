<script setup>
    import { ref } from 'vue';
   
    
    const props = defineProps({
      items: Array,  
      title: String,
      slang: String,
      isAdmin: Boolean
    });
    
    const isJobListingModalOpen = ref(false);
    
    const handleSaveJobListing = (jobListingData) => {
      console.log("Nueva oferta de empleo:", jobListingData);
    };
    
    const isExpanded = ref(false);
    
    const toggleExpand = () => {
      isExpanded.value = !isExpanded.value;
    };
    </script>
    
    <template>
      <div class="mb-6">
        <button @click="toggleExpand"
          class="w-full flex justify-between items-center py-3 px-4 bg-white text-[#193CB8] transition-all duration-300 ease-in-out rounded-lg hover:bg-[#193CB8] hover:text-white shadow-sm border border-gray-200 cursor-pointer">
          <h2 class="text-xl font-bold">{{ title }}</h2>
          <div class="space-x-5 flex">
            <button v-if="isAdmin" @click="isJobListingModalOpen = true" class="cursor-pointer z-10">
              <i class="bx bx-plus relative group text-xl">
                <span class="absolute inset-0 w-8 h-8 rounded-full opacity-0 group-hover:opacity-100 transition"></span>
              </i>
            </button>
            <button class="cursor-pointer">
              <i class="bx bx-chevron-down relative group text-xl" :class="{ 'transform rotate-180': isExpanded }">
                <span class="absolute inset-0 w-10 h-10 rounded-full opacity-0 group-hover:opacity-100 transition"></span>
              </i>
            </button>
          </div>
        </button>
        <transition enter-active-class="transition-all duration-300 ease-out"
          leave-active-class="transition-all duration-300 ease-in"
          enter-from-class="transform translate-y-[-10px] opacity-0" enter-to-class="transform translate-y-0 opacity-100"
          leave-from-class="transform translate-y-0 opacity-100" leave-to-class="transform translate-y-[-10px] opacity-0">
          <div v-if="isExpanded" class="mt-4 space-y-4 overflow-hidden">
            <div v-for="(item, index) in items" :key="index"
              class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-[#193CB8] hover:shadow-md transition-shadow duration-300 ease-in-out">
              <h3 class="font-semibold text-lg text-[#193CB8]">
                {{ item.title }}
              </h3>
              <p class="text-sm text-gray-500">
                {{ item.location }} | {{ item.type }}
              </p>
              <p class="mt-2 text-gray-600">{{ item.description }}</p> 
            </div>
          </div>
        </transition>
      </div>
   
    </template>