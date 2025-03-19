<template>
    <div
        class="group bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 border border-gray-100 relative">
        <div class="relative h-40 overflow-hidden rounded-t-xl">
            <img v-if="group.group_banner" :src="group.group_banner" alt="Banner del grupo"
                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" />
            <img v-else src="/public/images/default-group-banner.jpg" alt="Banner del grupo"
                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>

            <div class="absolute bottom-0 left-0 right-0 p-4 text-white">
                <div class="flex items-end justify-between">
                    <div class="flex-1 ml-25"> 
                        <h3 class="text-xl font-bold line-clamp-1 mb-1">{{ group.name }}</h3>
                        <div class="flex items-center text-xs text-white/80">
                            <span class="flex items-center mr-3">
                                <i class='bx bx-user-circle mr-1'></i>
                                {{ group.members ? group.members.length : 0 }} miembros
                            </span>
                            <span class="flex items-center">
                                <i class='bx bx-lock-open-alt mr-1' v-if="group.privacy === 'public'"></i>
                                <i class='bx bx-lock-alt mr-1' v-else></i>
                                {{ group.privacy === 'public' ? 'Público' : 'Privado' }}
                            </span>
                        </div>
                    </div>

                    <div v-if="group.category" class="bg-[#193CB8]/80 text-white text-xs px-2 py-1 rounded-full">
                        {{ group.category }}
                    </div>
                </div>
            </div>
        </div>

        <div class="absolute left-4 top-32 z-10">
            <div class="w-20 h-20 rounded-xl overflow-hidden border-4 border-white shadow-md bg-white">
                <img v-if="group.group_logo" :src="group.group_logo" loading="lazy" alt="Logo del grupo"
                    class="w-full h-full object-cover" />
                <div v-else class="w-full h-full bg-gray-100 flex items-center justify-center text-[#193CB8]">
                    <i class='bx bx-group text-4xl'></i>
                </div>
            </div>
        </div>

        <div class="p-5 pt-10">
            <p class="text-gray-600 mb-4 line-clamp-2 text-sm">
                {{ group.description }}
            </p>

            <div v-if="group.tags && group.tags.length > 0" class="flex flex-wrap gap-1 mb-4">
                <span v-for="(tag, index) in group.tags.slice(0, 3)" :key="index"
                    class="px-2 py-0.5 bg-[#193CB8]/10 text-[#193CB8] rounded-full text-xs">
                    #{{ tag }}
                </span>
                <span v-if="group.tags.length > 3" class="text-xs text-gray-500 px-1">+{{ group.tags.length - 3 }}
                    más</span>
            </div>

            <div class="flex items-center justify-between">
                <button
                    class="px-4 py-2 rounded-lg bg-[#193CB8] text-white hover:bg-[#142d8c] transition-colors flex items-center gap-1 text-sm font-medium"
                    @click="navigateToGroup">
                    <i class='bx bx-show'></i> Ver grupo
                </button>

                <button class="p-2 rounded-full text-gray-500 hover:bg-gray-100 transition-colors" title="Más opciones">
                    <i class='bx bx-dots-horizontal-rounded'></i>
                </button>
            </div>
        </div>
    </div>
</template>``
<script setup>
import { router } from "@inertiajs/vue3";

const props = defineProps({
    group: {
        type: Object,
        required: true
    }
});

const navigateToGroup = () => {
    router.get(`/grupos/${props.group.slug}`);
};
</script>
<style scoped>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>