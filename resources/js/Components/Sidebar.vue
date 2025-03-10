<template>
  <div>
    <!-- Mobile menu button -->
    <button @click="toggleSidebar" class="md:hidden fixed top-4 left-4 z-50 bg-[#193CB8] text-white p-2 rounded-md shadow-md">
      <i class='bx bx-menu text-xl'></i>
    </button>

    <!-- Overlay for mobile -->
    <div v-if="isOpen" @click="toggleSidebar" class="fixed inset-0 bg-black/50 md:hidden z-40 backdrop-blur-sm"></div>

    <!-- Sidebar -->
    <aside :class="[
      'h-full w-64 flex flex-col bg-white z-40 shadow-lg border-r border-gray-100 transition-all duration-300',
      isOpen ? 'fixed top-0 left-0 translate-x-0' : 'fixed top-0 left-0 -translate-x-full',
      'md:static md:translate-x-0'  
    ]">
      <!-- User profile -->
      <div class="p-4 border-b border-gray-100">
        <div class="flex items-center space-x-3 mb-3">
          <div class="relative">
            <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center text-[#193CB8] shadow-sm border-2 border-white">
              <i class='bx bxs-user text-xl'></i>
            </div>
            <div class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-green-500 rounded-full border-2 border-white"></div>
          </div>
          <div>
            <p class="font-semibold text-gray-800">{{ username }}</p>
            <p class="text-xs text-gray-500 flex items-center">
              <i class='bx bxs-circle text-green-500 text-[8px] mr-1'></i> Online
            </p>
          </div>
        </div>
      </div>

      <div class="flex-1 overflow-y-auto">
        <!-- Top navigation section -->
        <div class="p-4">
          <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-2">Principal</p>
          <nav>
            <ul class="space-y-1">
              <li v-for="item in topMenuItems" :key="item.text">
                <a 
                  href="#" 
                  :class="[
                    'flex items-center px-3 py-2.5 rounded-md transition-all relative',
                    item.active 
                      ? 'bg-blue-50 text-[#193CB8] font-medium before:absolute before:left-0 before:top-0 before:bottom-0 before:w-1 before:bg-[#193CB8] before:rounded-r-md' 
                      : 'text-gray-700 hover:bg-gray-50'
                  ]"
                >
                  <i :class="['bx text-xl mr-3', item.icon]"></i>
                  <span>{{ item.text }}</span>
                  <span v-if="item.badge" class="ml-auto bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5 min-w-[20px] text-center">
                    {{ item.badge }}
                  </span>
                </a>
              </li>
            </ul>
          </nav>
        </div>

        <!-- Middle section -->
        <div class="p-4 border-t border-gray-100">
          <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-2">Social</p>
          <nav>
            <ul class="space-y-1">
              <li v-for="item in middleMenuItems" :key="item.text">
                <a 
                  href="#" 
                  :class="[
                    'flex items-center px-3 py-2.5 rounded-md transition-all relative',
                    item.active 
                      ? 'bg-blue-50 text-[#193CB8] font-medium before:absolute before:left-0 before:top-0 before:bottom-0 before:w-1 before:bg-[#193CB8] before:rounded-r-md' 
                      : 'text-gray-700 hover:bg-gray-50'
                  ]"
                >
                  <i :class="['bx text-xl mr-3', item.icon]"></i>
                  <span>{{ item.text }}</span>
                  <span v-if="item.badge" class="ml-auto bg-[#193CB8] text-white text-xs rounded-full px-1.5 py-0.5 min-w-[20px] text-center">
                    {{ item.badge }}
                  </span>
                </a>
              </li>
            </ul>
          </nav>
        </div>
      </div>

      <!-- Post button -->
      <div class="p-4 border-t border-gray-100">
        <button class="bg-gradient-to-r from-[#193CB8] to-[#2748c6] text-white w-full py-3 rounded-md shadow-md hover:from-[#1535a3] hover:to-[#193CB8] transition-all flex items-center justify-center">
          <i class='bx bx-plus-circle mr-2'></i>
          <span>Crear Post</span>
        </button>
      </div>

      <!-- Bottom section -->
      <div class="p-4 border-t border-gray-100">
        <ul class="space-y-1">
          <li v-for="item in bottomMenuItems" :key="item.text">
            <a 
              href="#" 
              class="flex items-center px-3 py-2.5 text-gray-700 rounded-md transition-all hover:bg-gray-50"
            >
              <i :class="['bx text-xl mr-3', item.icon]"></i>
              <span>{{ item.text }}</span>
            </a>
          </li>
        </ul>
      </div>
    </aside>
  </div>
</template>

<script>
export default {
  props: {
    username: { type: String, default: 'Usuario Alumni' },
    usertag: { type: String, default: 'alumni@example.com' }
  },
  data() {
    return {
      isOpen: false,
      darkMode: false,
      topMenuItems: [
        { text: 'Inicio', icon: 'bx-home', active: true },
        { text: 'Notificaciones', icon: 'bx-bell', badge: '5' },
        { text: 'Mensajes', icon: 'bx-envelope', badge: '2' },
        { text: 'Calendario', icon: 'bx-calendar' },
      ],
      middleMenuItems: [
        { text: 'Alumni connect', icon: 'bx-group' },
        { text: 'Mis Ofertas', icon: 'bx-briefcase', badge: 'Nuevo' },
        { text: 'Empresas', icon: 'bx-buildings' },
      ],
      bottomMenuItems: [
      
        { text: 'Configuración', icon: 'bx-cog' },
        { text: 'Cerrar sesión', icon: 'bx-log-out' }
      ]
    };
  },
  methods: {
    toggleSidebar() {
      this.isOpen = !this.isOpen;
    },
<<<<<<< HEAD
  }
=======
    data() {
        return {
            isOpen: false,
            menuItems: [
                { text: 'Inicio', route: '/home' },
                { text: 'Social' },
                { text: 'Notificaciones' },
                { text: 'Mis Ofertas', route: '/ofertas' },
                { text: 'Empresas' },
                { text: 'Alumnos' },
                { text: 'Configuración' }
            ]
        };
    },
    methods: {
        toggleSidebar() {
            this.isOpen = !this.isOpen;
        }
    }
>>>>>>> feature/job-offers
};
</script>