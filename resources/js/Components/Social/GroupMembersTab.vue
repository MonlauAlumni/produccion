<template>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
      <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">Miembros del grupo</h2>
  
      <div class="mb-6">
        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">Administradores</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div
        v-for="member in adminMembers"
        :key="member.id"
        @click="goToProfile(member.user.profile.slang)"
        class="flex items-center gap-3 p-3 cursor-pointer border border-gray-100 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors relative"
          >
        <div class="w-12 h-12 rounded-full flex-shrink-0 relative">
          <img
            v-if="member.user.profile.profile_picture"
            :src="member.user.profile.profile_picture || '/images/default-avatar.jpg'"
            :alt="member.user.name"
            class="w-full h-full object-cover rounded-full"
          />
          <div
            v-else
            class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center text-[#193CB8] dark:text-blue-300 shadow-sm border-2 border-white dark:border-gray-800"
          >
            <i class='bx bxs-user text-xl'></i>
          </div>
          <i
            v-if="member.role === 'admin'"
            class='bx bxs-crown text-yellow-500 dark:text-yellow-400 absolute -top-2 text-xl'
            style="transform: rotate(-30deg);"
          ></i>
        </div>
        <div>
          <h4 class="dark:text-gray-100">
            {{ member.user.name + ' ' + member.user.last_name_1 + (' ' + member.user.last_name_2 ?? '') }}
          </h4>
          <p class="text-xs text-gray-500 dark:text-gray-400">Administrador</p>
        </div>
          </div>
        </div>
      </div>
  
      <div>
        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">Miembros</h3>
      
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div
        v-for="member in regularMembers"
        :key="member.id"
        @click="goToProfile(member.user.profile.slang)"
        class="flex items-center gap-3 p-3 border border-gray-100 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer"
          >
        <div class="w-12 h-12 rounded-full overflow-hidden flex-shrink-0 relative">
          <img
            v-if="member.user.profile_photo_url"
            :src="member.user.profile_photo_url || '/images/default-avatar.jpg'"
            :alt="member.user.name"
            class="w-full h-full object-cover"
          />
          <div
            v-else
            class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center text-[#193CB8] dark:text-blue-300 shadow-sm border-2 border-white dark:border-gray-800"
          >
            <i class='bx bxs-user text-xl'></i>
          </div>
        </div>
        <div class="flex-1">
          <span class="dark:text-gray-100">
            {{ member.user.name + ' ' + member.user.last_name_1 + (' ' + member.user.last_name_2 ?? '') }}
          </span>
          <p class="text-xs text-gray-500 dark:text-gray-400">
            Miembro desde {{ formatDate(member.created_at) }}
          </p>
        </div>
      
        <div v-if="isAdmin" class="flex-shrink-0">
          <button class="p-1 text-gray-400 dark:text-gray-300 hover:text-gray-600 dark:hover:text-gray-100">
            <i class='bx bx-dots-vertical-rounded'></i>
          </button>
        </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      activeTab: {
        type: String,
        required: true
      },
      adminMembers: {
        type: Array,
        required: true
      },
      regularMembers: {
        type: Array,
        required: true
      },
      isAdmin: {
        type: Boolean,
        required: true
      }
    },
    methods: {
      goToProfile(slang) {
        this.$router.push(`/perfil/${slang}`);
      },
      formatDate(date) {
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return new Date(date).toLocaleDateString(undefined, options);
      }
    }
  };
  </script>