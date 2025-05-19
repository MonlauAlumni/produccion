<script>
import { usePage } from '@inertiajs/vue3'
import { computed, onMounted, ref } from 'vue';

export default {
  mounted() {
    const page = usePage()
    const notifications = page.props.auth?.user_notifications ?? 0
    const notificacionesItem = this.topMenuItems.find(item => item.text === 'Notificaciones')
    const userRole = page.props.auth?.user?.roles[0]?.name
    if (notificacionesItem) {
      notificacionesItem.badge = notifications > 0 ? notifications : null
    }

    // Check for dark mode on component mount
    this.darkMode = localStorage.getItem('theme') === 'dark';
    this.applyTheme();
  },

  data() {
    return {
      isOpen: false,
      darkMode: false,
      topMenuItems: [
        { text: 'Inicio', icon: 'bx-home', route: '/home' },
        { text: 'Notificaciones', icon: 'bx-bell', badge: null, route: '/notificaciones' },
        { text: 'Mensajes', icon: 'bx-envelope', badge: '', route: '/mensajes' },
        { text: 'Calendario', icon: 'bx-calendar', route: '/calendario' },
      ],
      // Definimos los items sin la propiedad visible aquí
      middleMenuItems: [
        { text: 'Mis Ofertas', icon: 'bx-briefcase',  route: '/mis-ofertas', requireRole: 'alumne'  },
        { text: 'Alumnos', icon: 'bx-user', route: '/alumnos', requireRole: ['empresa', 'admin'] },
        { text: 'Candidatos', icon: 'bx-user-check', route: '/gestion-candidatos', requireRole: 'empresa' },
        { text: 'Empresas', icon: 'bx-buildings', route: '/empresas', requireRole: ['alumne', 'admin'] },
        { text: 'Alumni connect', icon: 'bx-group', route: '/connect', requireRole: ['alumne', 'admin'] },
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
        if (!item.requireRole) return true;
        if (Array.isArray(item.requireRole)) {
          return item.requireRole.includes(this.userRole);
        }
        return item.requireRole === this.userRole;
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
    },
    toggleDarkMode() {
      this.darkMode = !this.darkMode;
      localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
      this.applyTheme();
    },
    applyTheme() {
      if (this.darkMode) {
        document.documentElement.classList.add('dark');
      } else {
        document.documentElement.classList.remove('dark');
      }
    }
  }
};
</script>

<template>
  <div>
    <!-- Botón menú mobile -->
    <button @click="toggleSidebar"
      class="md:hidden fixed top-2.5 left-4 bg-[#193CB8] z-50 text-white p-2 rounded-md shadow-md">
      <i class='bx bx-menu text-xl'></i>
    </button>

    <!-- Overlay para móviles -->
    <div v-if="isOpen" @click="toggleSidebar" class="fixed inset-0 bg-black/50 md:hidden z-40 backdrop-blur-sm"></div>

    <!-- Sidebar -->
    <aside :class="[ 
      'h-full w-64 flex flex-col z-50 md:z-40 shadow-lg transition-all duration-300', 
      isOpen ? 'fixed top-0 left-0 translate-x-0' : 'fixed top-0 left-0 -translate-x-full', 
      'md:static md:translate-x-0',
      'bg-white dark:bg-gray-900 border-r border-b rounded-br border-gray-100 dark:border-gray-800'
    ]">
      <!-- Perfil usuario -->
      <div class="p-4 border-b border-gray-100 dark:border-gray-800">
        <div class="flex items-center space-x-3 mb-3">
          <div class="relative">
            <div v-if="user.company">
              <img v-if="user.company.profile_picture" :src="user.company.profile_picture"
                alt="Profile picture" class="w-12 h-12 shadow-sm border-2 border-white dark:border-gray-700">
              <div v-else
                class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center text-[#193CB8] dark:text-blue-400 shadow-sm border-2 border-white dark:border-gray-700">
                <i class='bx bxs-building text-xl'></i>
              </div>
            </div>
            <div v-if="user.profile">
              <img v-if="user.profile.profile_picture" :src="user.profile.profile_picture"
                alt="Profile picture" class="w-12 h-12 shadow-sm border-2 border-white dark:border-gray-700">
              <div v-else
                class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center text-[#193CB8] dark:text-blue-400 shadow-sm border-2 border-white dark:border-gray-700">
                <i class='bx bxs-user text-xl'></i>
              </div>
            </div>
            <div v-if="user?.company"
              class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-green-500 rounded-full border-2 border-white dark:border-gray-700"></div>
          </div>
          <div>
            <p @click.prevent="navigateTo(`/empresa/${user.company.slang}`)" v-if="user.company" 
               class="font-semibold text-gray-800 dark:text-gray-200 hover:underline cursor-pointer">
              {{ user.company.company_name }}
            </p>
            <p @click.prevent="navigateTo(`/perfil/${user.profile.slang}`)" v-else 
               class="font-semibold text-gray-800 dark:text-gray-200 hover:underline cursor-pointer">
              {{ user.name }} {{ user.last_name_1 }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center">
              <i class='bx bxs-circle text-green-500 text-[8px] mr-1'></i> Online
            </p>
          </div>
        </div>
      </div>

      <div class="flex-1 overflow-y-auto">
        <!-- Sección principal -->
        <div class="p-4">
          <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2 px-2">Principal</p>
          <nav>
            <ul class="space-y-1">
              <li v-for="item in topMenuItems" :key="item.text">
                <a @click.prevent="navigateTo(item.route)" :class="[ 
                  'flex items-center px-3 py-2.5 rounded-md transition-all relative cursor-pointer',
                  isActive(item.route) 
                    ? 'bg-blue-50 dark:bg-blue-900/30 text-[#193CB8] dark:text-blue-400 font-medium before:absolute before:left-0 before:top-0 before:bottom-0 before:w-1 before:bg-[#193CB8] dark:before:bg-blue-400 before:rounded-r-md' 
                    : 'text-gray-700 dark:text-gray-300 hover:bg-blue-100 dark:hover:bg-blue-900/20'
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
        <div class="p-4 border-t border-gray-100 dark:border-gray-800">
          <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2 px-2">Social</p>
          <nav>
            <ul class="space-y-1">
              <!-- Usamos filteredMiddleMenuItems en lugar de middleMenuItems -->
              <li v-for="item in filteredMiddleMenuItems" :key="item.text">
                <a @click.prevent="navigateTo(item.route)" :class="[ 
                  'flex items-center px-3 py-2.5 rounded-md transition-all relative cursor-pointer',
                  isActive(item.route) 
                    ? 'bg-blue-50 dark:bg-blue-900/30 text-[#193CB8] dark:text-blue-400 font-medium before:absolute before:left-0 before:top-0 before:bottom-0 before:w-1 before:bg-[#193CB8] dark:before:bg-blue-400 before:rounded-r-md' 
                    : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'
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
      <div v-if="userRole === 'admin'" class="p-4 border-t border-gray-100 dark:border-gray-800">
        <button @click.prevent="navigateTo('/admin/dashboard')"
          class="bg-gradient-to-r from-[#193CB8] to-[#2748c6] text-white w-full py-3 rounded-md shadow-md hover:from-[#1535a3] cursor-pointer hover:to-[#193CB8] transition-all flex items-center justify-center">
          <i class='bx bx-shield mr-2'></i>
          <span>Panel de Administración</span>
        </button>
      </div>
      <div v-if="user.company" class="p-4 border-t border-gray-100 dark:border-gray-800">
        <button @click.prevent="navigateTo('/ofertas/crear')"
          class="bg-gradient-to-r from-[#193CB8] to-[#2748c6] text-white w-full py-3 rounded-md shadow-md hover:from-[#1535a3] cursor-pointer hover:to-[#193CB8] transition-all flex items-center justify-center">
          <i class='bx bx-plus-circle mr-2'></i>
          <span>Crear oferta</span>
        </button>
      </div>

      <!-- Sección inferior -->
      <div class="p-4 border-t border-gray-100 dark:border-gray-800">
        <ul class="space-y-1">
          <li v-for="item in bottomMenuItems" :key="item.text">
            <a @click.prevent="navigateTo(item.route)" :class="[ 
              'flex items-center px-3 py-2.5 rounded-md transition-all cursor-pointer',
              item.text === 'Cerrar sesión' 
                ? 'text-gray-700 dark:text-gray-300 hover:bg-red-500 hover:text-white' 
                : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'
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