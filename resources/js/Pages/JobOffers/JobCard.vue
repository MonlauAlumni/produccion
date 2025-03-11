<script setup>
    import { defineProps, defineEmits } from 'vue';
    
    const props = defineProps({
        jobOffer: {
            type: Object,
            required: true
        },
        company: {
            type: Object,
            default: () => ({})
        },
        isLiked: {
            type: Boolean,
            default: false
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
        'like',
        'share'
    ]);
    
    // Formatear rango salarial
    const formatSalaryRange = (min, max) => {
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
    
    // Métodos para emitir eventos
    const viewJobOffer = (id) => {
        emit('view', id);
    };
    
    const applyToJob = (id) => {
        emit('apply', id);
    };
    
    const toggleSaveJob = (id) => {
        emit('save', id);
    };
    
    const toggleLikeJob = (id) => {
        emit('like', id);
    };
    
    const shareJob = (id) => {
        emit('share', id);
    };
    </script>
    
    <template>
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <!-- Status Badge (si existe) -->
            <div v-if="jobOffer.status === 'featured'" class="bg-[#193CB8] text-white text-xs font-bold px-3 py-1 flex items-center">
                <i class='bx bx-star mr-1'></i>
                Oferta destacada
            </div>
            
            <div v-if="jobOffer.status === 'urgent'" class="bg-red-500 text-white text-xs font-bold px-3 py-1 flex items-center">
                <i class='bx bx-time mr-1'></i>
                Contratación urgente
            </div>
    
            <div class="p-5">
                <!-- Header -->
                <div class="flex items-start gap-4">
                    <!-- Company Logo -->
                    <div
                        class="w-14 h-14 bg-gray-100 rounded-lg flex items-center justify-center shrink-0 border border-gray-200">
                        <img v-if="jobOffer.company && jobOffer.company.logo" 
                             :src="jobOffer.company.logo" 
                             :alt="jobOffer.company.name"
                             class="w-10 h-10 object-contain" />
                        <i v-else class='bx bx-building text-2xl text-gray-400'></i>
                    </div>
    
                    <!-- Job Info -->
                    <div class="flex-1">
                        <div class="flex items-start justify-between">
                            <div>
                                <h2 class="font-bold text-lg text-[#193CB8] hover:underline cursor-pointer"
                                    @click="viewJobOffer(jobOffer.id)">
                                    {{ jobOffer.title }}
                                </h2>
                                <div class="text-gray-700">
                                    {{ jobOffer.company ? jobOffer.company.name : '' }}
                                </div>
                            </div>
    
                            <!-- Expires Badge -->
                            <div v-if="jobOffer.expires_at"
                                class="bg-yellow-100 text-yellow-800 text-xs font-bold px-2 py-1 rounded-full flex items-center">
                                <i class='bx bx-calendar-exclamation mr-1'></i>
                                Expira: {{ new Date(jobOffer.expires_at).toLocaleDateString() }}
                            </div>
                        </div>
    
                        <!-- Job Details -->
                        <div class="flex flex-wrap gap-x-4 gap-y-2 text-sm mt-3">
                            <div class="flex items-center text-gray-600">
                                <i class='bx bx-map-pin mr-1 text-[#193CB8]'></i>
                                {{ jobOffer.location }}
                            </div>
                            <div class="flex items-center text-gray-600">
                                <i class='bx bx-time-five mr-1 text-[#193CB8]'></i>
                                {{ getJobTypeLabel(jobOffer.job_type) }}
                            </div>
                            <div class="flex items-center text-gray-600">
                                <i class='bx bx-money mr-1 text-[#193CB8]'></i>
                                {{ formatSalaryRange(jobOffer.salary_min, jobOffer.salary_max) }}
                            </div>
                            <div v-if="jobOffer.category" class="flex items-center text-gray-600">
                                <i class='bx bx-category mr-1 text-[#193CB8]'></i>
                                {{ jobOffer.category }}
                            </div>
                        </div>
                    </div>
                </div>
    
                <!-- Description -->
                <div class="mt-4 text-gray-700">
                    <p class="line-clamp-3">{{ jobOffer.description }}</p>
                </div>
    
                <!-- Stats & Engagement -->
                <div class="flex flex-wrap items-center justify-between mt-4 pt-4 border-t border-gray-100">
                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                        <div class="flex items-center">
                            <i class='bx bx-calendar mr-1'></i>
                            {{ getTimeAgo(jobOffer.created_at) }}
                        </div>
                    </div>
    
                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-2 mt-3 sm:mt-0">
                        <button @click="toggleLikeJob(jobOffer.id)" :class="[
                            'p-2 rounded-full transition-colors',
                            isLiked 
                                ? 'bg-red-100 text-red-500' 
                                : 'bg-gray-100 text-gray-500 hover:bg-gray-200'
                        ]">
                            <i :class="[isLiked ? 'bxs-heart' : 'bx-heart']"></i>
                        </button>
                        <button @click="toggleSaveJob(jobOffer.id)" :class="[
                            'p-2 rounded-full transition-colors',
                            isSaved 
                                ? 'bg-[#193CB8]/20 text-[#193CB8]' 
                                : 'bg-gray-100 text-gray-500 hover:bg-gray-200'
                        ]">
                            <i :class="[isSaved ? 'bxs-bookmark' : 'bx-bookmark']"></i>
                        </button>
                        <button @click="shareJob(jobOffer.id)"
                            class="p-2 rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 transition-colors">
                            <i class='bx bx-share-alt'></i>
                        </button>
                    </div>
                </div>
    
                <!-- Apply Button -->
                <div class="mt-4 flex">
                    <button @click="viewJobOffer(jobOffer.id)"
                        class="flex-1 bg-white border border-[#193CB8] text-[#193CB8] font-medium py-2 rounded-l-lg hover:bg-[#193CB8]/5 transition-colors">
                        Ver detalles
                    </button>
                    <button @click="applyToJob(jobOffer.id)"
                        class="flex-1 bg-[#193CB8] text-white font-medium py-2 rounded-r-lg hover:bg-[#142d8c] transition-colors">
                        Aplicar ahora
                    </button>
                </div>
            </div>
        </div>
    </template>