<script setup>
    import { defineProps, defineEmits, computed } from 'vue';
    import { usePage } from '@inertiajs/vue3';
    
    const page = usePage();
    const settings = computed(() => page.props.auth.user_settings);
    
    const props = defineProps({
        jobOffer: {
            type: Object,
            required: true
        },
        application: {
            type: Object,
            default: () => ({})
        },
        isSaved: {
            type: Boolean,
            default: false
        }
    });
    
    const emit = defineEmits([
        'view',
        'apply',
        'save',
        'share',
        'cancel'
    ]);
    
    // Formatear rango salarial
    const formatSalaryRange = (min, max) => {
        min = parseInt(min, 10);
        max = parseInt(max, 10);
        if (!min && !max) return 'No especificado';
        if (min && max) return `${min.toLocaleString()}€ - ${max.toLocaleString()}€`;
        return min ? `Desde ${min.toLocaleString()}€` : `Hasta ${max.toLocaleString()}€`;
    };
    
    // Calcular tiempo desde publicación
    const getTimeAgo = (dateString) => {
        if (!dateString) return 'Reciente';
    
        const posted = new Date(dateString);
        const today = new Date();
        const diffTime = Math.abs(today - posted);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
        if (diffDays === 0) return 'Hoy';
        if (diffDays === 1) return 'Ayer';
        if (diffDays < 7) return `Hace ${diffDays} días`;
        if (diffDays < 30) return `Hace ${Math.floor(diffDays / 7)} semanas`;
        return `Hace ${Math.floor(diffDays / 30)} meses`;
    };
    
    // Obtener etiqueta para tipo de trabajo
    const getJobTypeLabel = (jobType) => {
        const types = {
            'remote': 'Remoto',
            'onsite': 'Presencial',
            'hybrid': 'Híbrido'
        };
        return types[jobType] || jobType;
    };
    
    // Obtener información de estado de la aplicación
    const getStatusInfo = (status) => {
        const statusMap = {
            'pending': { 
                text: 'Pendiente de respuesta', 
                color: 'bg-yellow-100 text-yellow-800 border-yellow-200',
                icon: 'bx-time'
            },
            'in_process': { 
                text: 'En proceso de selección', 
                color: 'bg-blue-100 text-blue-800 border-blue-200',
                icon: 'bx-loader'
            },
            'accepted': { 
                text: 'Aplicación aceptada', 
                color: 'bg-green-100 text-green-800 border-green-200',
                icon: 'bx-check-circle'
            },
            'rejected': { 
                text: 'Aplicación rechazada', 
                color: 'bg-red-100 text-red-800 border-red-200',
                icon: 'bx-x-circle'
            },
            'cancelled': { 
                text: 'Aplicación cancelada', 
                color: 'bg-gray-100 text-gray-800 border-gray-200',
                icon: 'bx-block'
            }
        };
        
        return statusMap[status] || { 
            text: 'Estado desconocido', 
            color: 'bg-gray-100 text-gray-800 border-gray-200',
            icon: 'bx-question-mark'
        };
    };
    
    // Métodos para emitir eventos
    const viewJobOffer = () => {
        emit('view', props.jobOffer.id);
    };
    
    const applyToJob = () => {
        emit('apply', props.jobOffer.id);
    };
    
    const toggleSaveJob = () => {
        emit('save', props.jobOffer.id);
    };
    
    const shareJob = () => {
        emit('share', props.jobOffer.id);
    };
    
    const cancelApplication = () => {
        emit('cancel', props.application.id);
    };
    
    const getIconClass = (category) => {
        switch (category) {
            case 'it':
                return 'bx bx-code-alt';
            case 'automotive':
                return 'bx bx-car';
            case 'marketing':
                return 'bx bx-line-chart';
            default:
                return 'bx bx-category'; 
        }
    };
    
    const getCategoryLabel = (category) => {
        switch (category) {
            case 'it':
                return 'Tecnología';
            case 'automotive':
                return 'Automoción';
            case 'marketing':
                return 'Marketing';
            default:
                return 'Categoría desconocida'; 
        }
    };
    
    // Calcular días restantes para respuesta
    const getDaysRemaining = () => {
        if (!props.application.created_at) return null;
        
        const applicationDate = new Date(props.application.created_at);
        const today = new Date();
        const diffTime = Math.abs(today - applicationDate);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        // Asumimos que las empresas suelen responder en 14 días
        const expectedResponseDays = 14;
        const daysRemaining = expectedResponseDays - diffDays;
        
        return daysRemaining > 0 ? daysRemaining : 0;
    };
    
    // Formatear fecha
    const formatDate = (dateString) => {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleDateString('es-ES', { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
    };
    </script>
    
    <template>
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 transition-all duration-300 hover:shadow-xl">
            <!-- Status Banner - Prominente en la parte superior -->
            <div v-if="application && application.status" 
                :class="[
                    'px-4 py-3 flex items-center justify-between',
                    getStatusInfo(application.status).color
                ]">
                <div class="flex items-center gap-2">
                    <i :class="['bx text-xl', getStatusInfo(application.status).icon]"></i>
                    <span class="font-medium mt-0.5">{{ getStatusInfo(application.status).text }}</span>
                </div>
                
                <div v-if="application.status === 'pending'" class="flex items-center gap-5">
                    <span class="text-sm">
                        <i class='bx bx-calendar-check'></i>
                        Aplicada: {{ formatDate(application.created_at) }}
                    </span>
                    <button 
                        @click.stop="cancelApplication"
                        class="bg-red-600/80 hover:bg-red-600 text-white px-2 py-1 cursor-pointer rounded text-sm flex items-center gap-1 transition-colors"
                    >
                        <i class='bx bx-x'></i>
                        <span>Cancelar</span>
                    </button>
                </div>
                
                <div v-else-if="application.status === 'in_process'" class="text-sm gap-x-1 flex items-center">
                    <i class='bx bx-calendar-event'></i>
                    <span>Última actualización: {{ formatDate(application.updated_at) }}</span>
                </div>
                
                <div v-else-if="application.status === 'accepted'" class="text-sm gap-x-1 flex items-center">
                    <i class='bx bx-calendar-check'></i>
                    <span>Aceptada el: {{ formatDate(application.updated_at) }}</span>
                </div>
                
                <div v-else-if="application.status === 'rejected'" class="text-sm gap-x-1 flex items-center">
                    <i class='bx bx-calendar-x'></i>
                    <span>Rechazada el: {{ formatDate(application.updated_at) }}</span>
                </div>
            </div>
    
            <div class="p-5">
                <!-- Header -->
                <div class="flex items-start gap-4">
                    <!-- Company Logo -->
                    <div
                        class="w-14 h-14 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center shrink-0 border border-gray-200 dark:border-gray-600">
                        <img v-if="jobOffer.company && jobOffer.company.profile_picture"
                            :src="`${jobOffer.company.profile_picture}`" :alt="jobOffer.company.name || ''"
                            class="w-13 h-13 rounded-lg" />
                        <i v-else class='bx bx-building text-2xl text-gray-400 dark:text-gray-500'></i>
                    </div>
    
                    <!-- Job Info -->
                    <div class="flex-1">
                        <div class="flex items-start justify-between">
                            <div>
                                <h2 class="font-bold text-lg text-[#4A6EDB] hover:underline cursor-pointer"
                                    @click="viewJobOffer">
                                    {{ jobOffer.title }}
                                </h2>
                                <div class="text-gray-700 dark:text-gray-300">
                                    {{ jobOffer.company ? jobOffer.company.company_name : '' }}
                                </div>
                            </div>
    
                            <!-- Timeline Badge for Pending Applications -->
                            <div v-if="application && application.status === 'pending' && getDaysRemaining() > 0"
                                class="bg-blue-50 text-[#193CB8] text-xs font-bold px-2 py-1 rounded-full flex items-center">
                                <i class='bx bx-time-five mr-1'></i>
                                Respuesta estimada: {{ getDaysRemaining() }} días
                            </div>
                        </div>
    
                        <!-- Job Details -->
                        <div class="flex flex-wrap gap-x-4 gap-y-2 text-sm mt-3">
                            <div class="flex items-center text-gray-600 dark:text-gray-400">
                                <i class='bx bx-map-pin mr-1 text-[#4A6EDB]'></i>
                                {{ jobOffer.location }}
                            </div>
                            <div class="flex items-center text-gray-600 dark:text-gray-400">
                                <i class='bx bx-time-five mr-1 text-[#4A6EDB]'></i>
                                {{ getJobTypeLabel(jobOffer.work_mode) }}
                            </div>
                            <div class="flex items-center text-gray-600 dark:text-gray-400">
                                <i class='bx bx-money mr-1 text-[#4A6EDB]'></i>
                                {{ formatSalaryRange(jobOffer.minimum_salary, jobOffer.maximum_salary) }}
                            </div>
                            <div v-if="jobOffer.category" class="flex items-center text-gray-600 dark:text-gray-400">
                                <i :class="getIconClass(jobOffer.category)" class="mr-1 text-[#4A6EDB]"></i>
                                {{ getCategoryLabel(jobOffer.category) }}
                            </div>
                        </div>
                    </div>
                </div>
    
                <!-- Description -->
                 <div class="mt-4 text-gray-600 dark:text-gray-400 text-sm line-clamp-3">

                    <p class="">{{ jobOffer.description }}</p>
                </div>
    
                <!-- Stats & Engagement -->
                <div class="flex flex-wrap items-center justify-between mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                   
    
                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-2 mt-3 sm:mt-0">
                        <button @click.stop="toggleSaveJob" :class="[
                            'px-2 py-2 rounded-full flex cursor-pointer items-center justify-center transition-colors',
                            isSaved
                                ? 'bg-[#193CB8]/20 text-[#193CB8]'
                                : 'bg-gray-100 text-gray-500 hover:bg-gray-200'
                        ]">
                         
                        </button>
                        
                    </div>
                </div>
    
                <!-- Action Buttons -->
                <div class="mt-4 flex ">
                    <button @click.stop="viewJobOffer"
                        class="flex-1 bg-white dark:bg-gray-800 border cursor-pointer border-[#4A6EDB] text-[#4A6EDB] hover:bg-blue-100 dark:hover:bg-[#4A6EDB]/10 font-medium py-2 rounded-l-lg transition-colors">
                        Ver detalles
                    </button>
                    
                    <!-- Botón contextual según el estado -->
                    <button v-if="!application || application.status === 'rejected'"
                        @click.stop="applyToJob"
                        class="flex-1 bg-red-700 dark:bg-red-800 cursor-pointer text-white font-medium py-2 rounded-r-lg hover:bg-red-800 dark:hover:bg-red-900 transition-colors">
                        Eliminar
                    </button>
                    
                   
                    
                    <button v-else
                        class="flex-1 bg-gray-200 dark:bg-gray-700 cursor-not-allowed text-gray-600 dark:text-gray-400 font-medium py-2 rounded-r-lg">
                        {{ application.status === 'pending' ? 'Pendiente de respuesta' : 'En proceso' }}
                    </button>
                </div>
            </div>
            
            <!-- Cover Letter Preview (si existe) -->
            
        </div>
    </template>
    
    <style scoped>
    /* Animación al pasar el cursor */
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    </style>