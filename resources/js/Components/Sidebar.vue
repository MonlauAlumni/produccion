<script>
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue';

const page = usePage();


export default {
  data() {
    return {
      isOpen: false,
      darkMode: false,
      topMenuItems: [
        { text: 'Inicio', icon: 'bx-home', route: '/home' },
        { text: 'Notificaciones', icon: 'bx-bell', badge: '5', route: '/notificaciones' },
        { text: 'Mensajes', icon: 'bx-envelope', badge: '2', route: '/mensajes' },
        { text: 'Calendario', icon: 'bx-calendar', route: '/calendario' },
      ],
      // Definimos los items sin la propiedad visible aquí
      middleMenuItems: [
        { text: 'Alumni connect', icon: 'bx-group', route: '/connect' },
        { text: 'Mis Ofertas', icon: 'bx-briefcase', badge: 'Nuevo', route: '/mis-ofertas', requireRole: 'alumne' },
        { text: 'Gestión de Candidatos', icon: 'bx-user-check', badge: 'Nuevo', route: '/gestion-candidatos', requireRole: 'empresa' },
        { text: 'Empresas', icon: 'bx-buildings', route: '/empresas' },
      ],
      bottomMenuItems: [
        { text: 'Configuración', icon: 'bx-cog', route: '/configuracion' },
        { text: 'Cerrar sesión', icon: 'bx-log-out', route: '/logout' }
      ],
    };
  },
  
  computed: {
    user() {
      return this.$page.props.auth.user || {};
    },
    currentRoute() {
      return window.location.pathname; // Obtiene la ruta actual
    },
    userRole() {

    return this.user?.roles && this.user.roles.length > 0 ? this.user.roles[0].name : null;
  },

    filteredMiddleMenuItems() {
      

      return this.middleMenuItems.filter(item => {
        // Si el item requiere un rol específico, verificamos que el usuario tenga ese rol
        if (item.requireRole) {
          return this.userRole === item.requireRole;
        }
        // Si no requiere un rol específico, siempre se muestra
        return true;
      });
    }
  },
  methods: {
    toggleSidebar() {
      this.isOpen = !this.isOpen;
    },
    navigateTo(route) {
      this.$inertia.visit(route);
    },
    isActive(route) {
      return this.currentRoute === route;
    }
  }
};

</script>

<template>
  <div>
    <!-- Botón menú mobile -->
    <button @click="toggleSidebar"
      class="md:hidden fixed top-4 left-4  bg-[#193CB8] z-100 text-white p-2 rounded-md shadow-md">
      <i class='bx bx-menu text-xl'></i>
    </button>

    <!-- Overlay para móviles -->
    <div v-if="isOpen" @click="toggleSidebar" class="fixed inset-0 bg-black/50 md:hidden z-40 backdrop-blur-sm"></div>

    <!-- Sidebar -->
    <aside :class="[ 
      'h-full w-64 flex flex-col bg-white z-40 shadow-lg border-r border-gray-100 transition-all duration-300', 
      isOpen ? 'fixed top-0 left-0 translate-x-0' : 'fixed top-0 left-0 -translate-x-full', 
      'md:static md:translate-x-0'
    ]">
      <!-- Perfil usuario -->
      <div class="p-4 border-b border-gray-100">
        <div class="flex items-center space-x-3 mb-3">
          <div class="relative">
            <div v-if="user.company">
              <img v-if="user.company.profile_picture" :src="user.company.profile_picture"
                alt="Profile picture" class="w-12 h-12  shadow-sm border-2 border-white">
              <div v-else
                class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center text-[#193CB8] shadow-sm border-2 border-white">
                <i class='bx bxs-building text-xl'></i>
              </div>
            </div>
            <div v-if="user.profile">
              <img v-if="user.profile.profile_picture" :src="user.profile.profile_picture"
                alt="Profile picture" class="w-12 h-12  shadow-sm border-2 border-white">
              <div v-else
                class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center text-[#193CB8] shadow-sm border-2 border-white">
                <i class='bx bxs-user text-xl'></i>
              </div>
            </div>
            <div v-if="user?.company"
              class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-green-500 rounded-full border-2 border-white"></div>
          </div>
          <div>
            <p @click.prevent="navigateTo(`/empresa/${user.company.slang}`)" v-if="user.company" class="font-semibold text-gray-800 hover:underline cursor-pointer">{{ user.company.company_name }}</p>
            <p @click.prevent="navigateTo(`/perfil/${user.profile.slang}`)" v-else class="font-semibold text-gray-800 hover:underline cursor-pointer">{{ user.name }} {{ user.last_name_1 }}</p>
            <p class="text-xs text-gray-500 flex items-center">
              <i class='bx bxs-circle text-green-500 text-[8px] mr-1'></i> Online
            </p>
          </div>
        </div>
      </div>

      <div class="flex-1 overflow-y-auto">
        <!-- Sección principal -->
        <div class="p-4">
          <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-2">Principal</p>
          <nav>
            <ul class="space-y-1">
              <li v-for="item in topMenuItems" :key="item.text">
                <a @click.prevent="navigateTo(item.route)" :class="[ 
                  'flex items-center px-3 py-2.5 rounded-md transition-all relative cursor-pointer',
                  isActive(item.route) ? 'bg-blue-50 text-[#193CB8] font-medium before:absolute before:left-0 before:top-0 before:bottom-0 before:w-1 before:bg-[#193CB8] before:rounded-r-md' : 'text-gray-700 hover:bg-blue-100'
                ]">
                  <i :class="['bx text-xl mr-3', item.icon]"></i>
                  <span>{{ item.text }}</span>
                  <span v-if="item.badge"
                    class="ml-auto bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5 min-w-[20px] text-center">
                    {{ item.badge }}
                  </span>
                </a>
              </li>
            </ul>
          </nav>
        </div>

        <!-- Sección social -->
        <div class="p-4 border-t border-gray-100">
          <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-2">Social</p>
          <nav>
            <ul class="space-y-1">
              <!-- Usamos filteredMiddleMenuItems en lugar de middleMenuItems -->
              <li v-for="item in filteredMiddleMenuItems" :key="item.text">
                <a @click.prevent="navigateTo(item.route)" :class="[ 
                  'flex items-center px-3 py-2.5 rounded-md transition-all relative cursor-pointer',
                  isActive(item.route) ? 'bg-blue-50 text-[#193CB8] font-medium before:absolute before:left-0 before:top-0 before:bottom-0 before:w-1 before:bg-[#193CB8] before:rounded-r-md' : 'text-gray-700 hover:bg-gray-50'
                ]">
                  <i :class="['bx text-xl mr-3', item.icon]"></i>
                  <span>{{ item.text }}</span>
                  <span v-if="item.badge"
                    class="ml-auto bg-[#193CB8] text-white text-xs rounded-full px-1.5 py-0.5 min-w-[20px] text-center">
                    {{ item.badge }}
                  </span>
                </a>
              </li>
            </ul>
          </nav>
        </div>
      </div>

      <!-- Botón Crear oferta -->
      <div v-if="user.company" class="p-4 border-t border-gray-100">
        <button @click.prevent="navigateTo('/ofertas/crear')"
          class="bg-gradient-to-r from-[#193CB8] to-[#2748c6] text-white w-full py-3 rounded-md shadow-md hover:from-[#1535a3] cursor-pointer hover:to-[#193CB8] transition-all flex items-center justify-center">
          <i class='bx bx-plus-circle mr-2'></i>
          <span>Crear oferta</span>
        </button>
      </div>

      <!-- Sección inferior -->
      <div class="p-4 border-t border-gray-100">
        <ul class="space-y-1">
          <li v-for="item in bottomMenuItems" :key="item.text">
            <a @click.prevent="navigateTo(item.route)" :class="[ 
              'flex items-center px-3 py-2.5 text-gray-700 rounded-md transition-all cursor-pointer',
              item.text === 'Cerrar sesión' ? 'hover:bg-red-500 hover:text-white' : 'hover:bg-gray-50'
            ]">
              <i :class="['bx text-xl mr-3', item.icon]"></i>
              <span>{{ item.text }}</span>
            </a>
          </li>
        </ul>
      </div>
    </aside>
  </div>
</template>