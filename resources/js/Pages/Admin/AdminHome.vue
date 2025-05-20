<script setup>
import 'boxicons';
import { ref, computed, onMounted } from 'vue';
import AdminLayout from './Layouts/AdminLayout.vue';
import UsersByTrainingAreaChart from '@/components/Admin/UserByTrainingAreaChart.vue';
import CompaniesBySectorChart from '@/components/Admin/CompaniesBySectorChart.vue';
import { router } from '@inertiajs/vue3';

// Define props with proper types
const props = defineProps({
  totalUsers: { type: Number, default: 0 },
  totalCompanies: { type: Number, default: 0 },
  totalApplications: { type: Number, default: 0 },
  newUsersThisMonth: { type: Number, default: 0 },
  usersByArea: { type: Object, default: () => ({}) },
  companiesBySector: { type: Object, default: () => ({}) }
});

// Animation state
const isLoaded = ref(false);

// Stats with enhanced metadata
const stats = [
  {
    title: 'Empresas Registradas',
    value: props.totalCompanies,
    icon: 'bxs-business',
    color: 'bg-emerald-100 text-emerald-600',
    description: 'Empresas registradas en el sistema'
  },
  {
    title: 'Usuarios registrados',
    value: props.totalUsers,
    icon: 'bxs-user-detail',
    color: 'bg-violet-100 text-violet-600',
    description: 'Usuarios registrados en el sistema'
  },
  {
    title: 'Ofertas de trabajo en curso',
    value: props.totalApplications,
    icon: 'bxs-folder-open',
    color: 'bg-amber-100 text-amber-600',
    description: 'Ofertas de trabajo abiertas y en proceso'
  },
  {
    title: 'Usuarios este mes',
    value: props.newUsersThisMonth,
    icon: 'bxs-user-plus',
    color: 'bg-rose-100 text-rose-600',
    description: 'Nuevos usuarios registrados este mes'
  }
];

// Quick actions
const quickActions = [
  { name: 'Usuarios', icon: 'bx-user-plus', color: 'bg-violet-600', route: '/admin/users' },
  { name: 'Empresas', icon: 'bx-building-house', color: 'bg-emerald-600', route: '/admin/companies' },
  { name: 'Notificaciones', icon: 'bx-bell', color: 'bg-amber-600', route: '/admin/notifications' },
  { name: 'Configuración', icon: 'bx-cog', color: 'bg-gray-600', route: '/configuracion' },
  { name: 'Connect', icon: 'bx-link', color: 'bg-blue-600', route: '/connect' },
  { name: 'Cerrar Sesión', icon: 'bx-log-out', color: 'bg-red-600', route: '/logout' }
];

// Date range options
const selectedRange = ref('Last 30 Days');

// Tab options for different views
const activeTab = ref('Overview');

// Última actualización (timestamp)
const lastUpdated = new Date().toLocaleString('es-ES', {
  weekday: 'short',
  month: 'short',
  day: 'numeric',
  hour: '2-digit',
  minute: '2-digit'
});

// Trigger animations after component mounts
onMounted(() => {
  setTimeout(() => {
    isLoaded.value = true;
  }, 100);
});

// Format large numbers with commas
const formatNumber = (num) => {
  return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
};

// Toggle card expansion
const expandedCard = ref(null);
const toggleCardExpansion = (index) => {
  expandedCard.value = expandedCard.value === index ? null : index;
};

// Navigate to route
function navigateTo(route) {
  if (route === '/logout') {
    window.location.href = route;
  } else {
    router.visit(route);
  }
}

// Total pending items
const totalPending = computed(() => {
  return pendingCounts.reduce((sum, item) => sum + item.count, 0);
});

// Pending counts from props or computed values
const pendingCounts = [
  { type: 'messages', count: 0 },
  { type: 'alerts', count: 0 },
  { type: 'tasks', count: 0 }
];
</script>

<template>
  <AdminLayout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
      <div class="p-6 md:p-8 space-y-8 max-w-7xl mx-auto">
        <!-- Header with actions -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
          <div class="relative">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 dark:text-gray-200 tracking-tight">Analytics Dashboard</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 font-medium">Welcome back, Admin</p>
            <div class="absolute -bottom-2 left-0 w-20 h-1 bg-violet-600 rounded-full"></div>
          </div>
          
          <div class="flex items-center gap-4">
            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 py-2 px-4 rounded-full shadow-sm border border-gray-200 dark:border-gray-700">
              <i class="bx bx-time-five mr-2 text-violet-500"></i>
              <span>{{ lastUpdated }}</span>
            </div>
          </div>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <div
            v-for="(stat, i) in stats"
            :key="i"
            class="rounded-2xl shadow-sm bg-white dark:bg-gray-800 transition-all duration-300 hover:shadow-lg overflow-hidden cursor-pointer group relative"
            :class="{'translate-y-0 opacity-100': isLoaded, 'translate-y-4 opacity-0': !isLoaded}"
            :style="{transitionDelay: `${i * 100}ms`}"
            @click="toggleCardExpansion(i)"
          >
            <div class="absolute top-0 left-0 w-full h-1" :class="stat.color.replace('bg-', 'bg-').replace('text-', '')"></div>
            <div class="p-6">
              <div class="flex items-center justify-between mb-4">
                <div :class="`p-3 rounded-xl ${stat.color} shadow-md`">
                  <i :class="`bx ${stat.icon} text-xl`"></i>
                </div>
                <button class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 opacity-0 group-hover:opacity-100 transition-opacity">
                  <i class="bx bx-dots-vertical-rounded"></i>
                </button>
              </div>
              <div>
                <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">{{ stat.title }}</p>
                <p class="text-3xl font-bold text-gray-800 dark:text-gray-200 mt-2">{{ formatNumber(stat.value) }}</p>
              </div>
            </div>
            
            <!-- Expandable content -->
            <div 
              v-if="expandedCard === i"
              class="bg-gray-50 dark:bg-gray-900 p-5 border-t border-gray-100 dark:border-gray-700 text-sm text-gray-600 dark:text-gray-300 animate-fadeIn"
            >
              <p>{{ stat.description }}</p>
              <div class="mt-4 flex justify-end">
                <button class="text-violet-600 dark:text-violet-400 hover:text-violet-700 dark:hover:text-violet-500 text-xs font-medium flex items-center bg-violet-50 dark:bg-violet-900 hover:bg-violet-100 dark:hover:bg-violet-800 py-2 px-3 rounded-lg transition-colors">
                  View Details
                  <i class="bx bx-right-arrow-alt ml-1"></i>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Tab Navigation -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
          <div class="border-b border-gray-100 dark:border-gray-700">
            <nav class="flex px-6">
              <button 
                v-for="tab in tabs" 
                :key="tab"
                @click="activeTab = tab"
                class="py-5 px-4 text-sm font-medium border-b-2 transition-colors relative mr-6"
                :class="activeTab === tab ? 'border-violet-600 text-violet-600 dark:text-violet-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600'"
              >
                {{ tab }}
              </button>
            </nav>
          </div>
        </div>

        <!-- Command Center -->
        <div 
          class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden"
          :class="{'translate-y-0 opacity-100': isLoaded, 'translate-y-4 opacity-0': !isLoaded}"
          style="transition: all 0.5s ease; transition-delay: 300ms;"
        >
          <div class="border-b border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between p-6">
              <div class="flex items-center">
                <div class="bg-violet-100 dark:bg-violet-900 text-violet-600 dark:text-violet-400 p-3 rounded-xl mr-4 shadow-sm">
                  <i class="bx bx-command text-xl"></i>
                </div>
                <div>
                  <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Centro de comandos</h2>
                  <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Acciones rápidas y estado del sistema</p>
                </div>
              </div>
              <div class="flex items-center gap-3">
                <span class="text-xs px-3 py-1.5 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-400 rounded-full flex items-center shadow-sm">
                  <span class="w-2 h-2 bg-green-500 dark:bg-green-400 rounded-full mr-2 animate-pulse"></span>
                  All Systems Operational
                </span>
                <button class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                  <i class="bx bx-dots-horizontal-rounded text-xl"></i>
                </button>
              </div>
            </div>
          </div>
          
          <!-- Quick Actions -->
          <div class="p-6">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-5">
              <button 
                v-for="(action, i) in quickActions" 
                :key="i"
                class="flex flex-col items-center justify-center p-5 rounded-xl border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all hover:shadow-md hover:-translate-y-1"
                @click="action.route ? navigateTo(action.route) : null"
              >
                <div :class="`p-4 rounded-xl ${action.color} text-white mb-3 shadow-md`">
                  <i :class="`bx ${action.icon} text-xl`"></i>
                </div>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ action.name }}</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Charts Section -->
        <div 
          class="grid grid-cols-1 lg:grid-cols-2 gap-6"
          :class="{'translate-y-0 opacity-100': isLoaded, 'translate-y-4 opacity-0': !isLoaded}"
          style="transition: all 0.5s ease; transition-delay: 400ms;"
        >
          <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
            <div class="border-b border-gray-100 dark:border-gray-700 px-6 py-5">
              <div class="flex justify-between items-center">
                <div>
                  <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Usuarios por área de aprendizaje</h2>
                  <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Distribución de usuarios según su área de formación</p>
                </div>
                <div class="flex items-center space-x-2">
                  <button class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                    <i class="bx bx-refresh"></i>
                  </button>
                  <button class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                    <i class="bx bx-dots-horizontal-rounded"></i>
                  </button>
                </div>
              </div>
            </div>
            <div class="p-6">
              <UsersByTrainingAreaChart :data="props.usersByArea" />
            </div>
          </div>
          
          <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
            <div class="border-b border-gray-100 dark:border-gray-700 px-6 py-5">
              <div class="flex justify-between items-center">
                <div>
                  <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Empresas por sector</h2>
                  <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Distribución de empresas según su sector industrial</p>
                </div>
                <div class="flex items-center space-x-2">
                  <button class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                    <i class="bx bx-refresh"></i>
                  </button>
                  <button class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                    <i class="bx bx-dots-horizontal-rounded"></i>
                  </button>
                </div>
              </div>
            </div>
            <div class="p-6">
              <CompaniesBySectorChart :companiesBySector="props.companiesBySector" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<style scoped>
.translate-y-0 {
  transform: translateY(0);
}
.translate-y-4 {
  transform: translateY(1rem);
}
.opacity-0 {
  opacity: 0;
}
.opacity-100 {
  opacity: 1;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn {
  animation: fadeIn 0.3s ease-out forwards;
}
@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}
.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>