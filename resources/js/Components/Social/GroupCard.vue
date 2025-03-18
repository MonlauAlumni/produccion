<template>
    <div class="mb-4 bg-white rounded-xl shadow overflow-hidden">
        <div class="relative h-36 bg-gray-100">
            <img v-if="group.group_banner" :src="group.group_banner" alt="Banner del grupo"
                class="w-full h-full object-cover" />
            <img v-else src="/public/images/default-group-banner.jpg" alt="Banner del grupo"
                class="w-full h-full object-cover" />
            <img v-if="group.group_logo" :src="group.group_logo" loading="lazy" alt="Foto de perfil del grupo"
                class="z-1 w-16 h-16 rounded-lg object-cover absolute -bottom-8 left-4 border-4 border-white shadow" />
            <div v-else class="z-1 w-16 h-16 rounded-lg object-cover absolute -bottom-8 left-4 border-4 border-white shadow bg-gray-200 flex items-center justify-center">
                <i class='bx bx-group text-3xl'></i>
            </div>
            <div class="absolute bottom-0 left-0 right-0 bg-black/30 text-white p-2 text-center">
                <h3 class="text-lg font-bold">{{ group.name }}</h3>
            </div>
        </div>
        <div class="p-4">
            <p class="text-gray-600 mb-3 line-clamp-2">
                {{ group.description }}
            </p>
            <button class="px-4 py-2 rounded-md bg-[#193CB8] text-white hover:bg-[#142d8c] transition-colors cursor-pointer"
                @click="$inertia.get('/grupos/' + group.slug)">
                Ver grupo
            </button>


        </div>
    </div>
</template>

<script>
import { router } from "@inertiajs/vue3";
export default {
    props: {
        group: {
            type: Object,
            required: true
        }
    }
};
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>