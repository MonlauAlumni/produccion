<script setup>
import 'boxicons';
import { ref, computed, onMounted } from 'vue';
import AdminLayout from './Layouts/AdminLayout.vue';
import UsersByTrainingAreaChart from '@/components/Admin/UserByTrainingAreaChart.vue';
import CompaniesBySectorChart from '@/components/Admin/CompaniesBySectorChart.vue';

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
  { name: 'Empresas', icon: 'bx-building-house', color: 'bg-emerald-600' },
  { name: 'Notificaciones', icon: 'bx-file', color: 'bg-amber-600' },
  { name: 'Configuración', icon: 'bx-cog', color: 'bg-gray-600' },
  { name: 'Connect', icon: 'bx-file-blank', color: 'bg-blue-600' },
  { name: 'Cerrar Sesión', icon: 'bx-log-out', color: 'bg-red-600' }	
];

// Date range options
const dateRanges = ['Last 7 Days', 'Last 30 Days', 'Last Quarter', 'Year to Date', 'Custom Range'];
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

// Total pending items
const totalPending = computed(() => {
  return pendingCounts.reduce((sum, item) => sum + item.count, 0);
});

// Navigate to route
function navigateTo(route) {
  if (typeof window !== 'undefined' && window.Inertia) {
    window.Inertia.visit(route);
  } else if (typeof $router !== 'undefined' && $router.push) {
    $router.push(route);
  } else {
    window.location.href = route;
  }
}
</script>

<template>
  <AdminLayout>
    <div class="p-4 md:p-6 space-y-6 max-w-7xl mx-auto">
      <!-- Header with actions -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
          <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Analytics Dashboard</h1>
          <p class="text-gray-500 mt-1">Welcome back, Admin</p>
        </div>
        

      </div>

      <!-- Tab Navigation -->
      <div class="border-b border-gray-200">
        <nav class="flex space-x-8">
          <button 
            v-for="tab in tabs" 
            :key="tab"
            @click="activeTab = tab"
            class="py-3 px-1 text-sm font-medium border-b-2 transition-colors"
            :class="activeTab === tab ? 'border-violet-600 text-violet-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
          >
            {{ tab }}
          </button>
        </nav>
      </div>

      <!-- Last Updated Info -->
      <div class="flex items-center text-sm text-gray-500">
        <i class="bx bx-time-five mr-1"></i>
        <span>Última actualización: {{ lastUpdated }}</span>
      </div>

      <!-- Metric Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        <div
          v-for="(stat, i) in stats"
          :key="i"
          class="rounded-xl shadow-sm border border-gray-100 bg-white transition-all duration-300 hover:shadow-md overflow-hidden cursor-pointer"
          :class="{'translate-y-0 opacity-100': isLoaded, 'translate-y-4 opacity-0': !isLoaded}"
          :style="{transitionDelay: `${i * 100}ms`}"
          @click="toggleCardExpansion(i)"
        >
          <div class="p-5">
            <div class="flex items-center justify-between">
              <div :class="`p-3 rounded-full ${stat.color}`">
                <i :class="`bx ${stat.icon} text-xl`"></i>
              </div>
              <button class="text-gray-400 hover:text-gray-600">
                <i class="bx bx-dots-vertical-rounded"></i>
              </button>
            </div>
            <div class="mt-4">
              <p class="text-gray-500 text-sm font-medium">{{ stat.title }}</p>
              <p class="text-2xl font-bold text-gray-800 mt-1">{{ formatNumber(stat.value) }}</p>
            </div>
          </div>
          
          <!-- Expandable content -->
          <div 
            v-if="expandedCard === i"
            class="bg-gray-50 p-4 border-t border-gray-100 text-sm text-gray-600 animate-fadeIn"
          >
            <p>{{ stat.description }}</p>
            <div class="mt-2 flex justify-end">
              <button class="text-violet-600 hover:text-violet-700 text-xs font-medium">
                View Details
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Command Center -->
      <div 
        class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden"
        :class="{'translate-y-0 opacity-100': isLoaded, 'translate-y-4 opacity-0': !isLoaded}"
        style="transition: all 0.5s ease; transition-delay: 300ms;"
      >
        <div class="border-b border-gray-100">
          <div class="flex items-center justify-between p-5">
        <div class="flex items-center">
          <div class="bg-violet-100 text-violet-600 p-2 rounded-lg mr-3">
            <i class="bx bx-command text-xl"></i>
          </div>
          <h2 class="text-lg font-semibold text-gray-800">Centro de comandos</h2>
        </div>
        <div class="flex items-center gap-2">
          <span class="text-xs px-2 py-1 bg-green-100 text-green-700 rounded-full flex items-center">
            <span class="w-2 h-2 bg-green-500 rounded-full mr-1"></span>
            All Systems Operational
          </span>
          <button class="text-gray-400 hover:text-gray-600 p-1">
            <i class="bx bx-dots-horizontal-rounded text-xl"></i>
          </button>
        </div>
          </div>
        </div>
        
        <!-- Quick Actions Only -->
        <div class="p-5">
          <h3 class="text-sm font-medium text-gray-500 mb-4">Acciones rápidas</h3>
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <button 
          v-for="(action, i) in quickActions" 
          :key="i"
          class="flex flex-col items-center justify-center p-4 rounded-lg border border-gray-100 hover:bg-gray-50 transition-colors"
          @click="action.route ? navigateTo(action.route) : null"
        >
          <div :class="`p-3 rounded-full ${action.color} text-white mb-2`">
            <i :class="`bx ${action.icon} text-xl`"></i>
          </div>
          <span class="text-xs font-medium text-gray-700">{{ action.name }}</span>
        </button>
          </div>
        </div>
      </div>

      <!-- Charts Section -->
      <div 
        class="grid grid-cols-1 md:grid-cols-2 gap-6"
        :class="{'translate-y-0 opacity-100': isLoaded, 'translate-y-4 opacity-0': !isLoaded}"
        style="transition: all 0.5s ease; transition-delay: 400ms;"
      >
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Usuarios por area de aprendizaje</h2>
            <div class="flex items-center space-x-2">
              <button class="p-1 rounded hover:bg-gray-100">
                <i class="bx bx-refresh text-gray-500"></i>
              </button>
              <button class="p-1 rounded hover:bg-gray-100">
                <i class="bx bx-dots-horizontal-rounded text-gray-500"></i>
              </button>
            </div>
          </div>
          <UsersByTrainingAreaChart :data="props.usersByArea" />
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Empresas por sector</h2>
            <div class="flex items-center space-x-2">
              <button class="p-1 rounded hover:bg-gray-100">
                <i class="bx bx-refresh text-gray-500"></i>
              </button>
              <button class="p-1 rounded hover:bg-gray-100">
                <i class="bx bx-dots-horizontal-rounded text-gray-500"></i>
              </button>
            </div>
          </div>
          <CompaniesBySectorChart :companiesBySector="props.companiesBySector" />
        </div>
      </div>


    </div>
  </AdminLayout>
</template>

<style scoped>
/* Add any component-specific styles here */
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
</style>