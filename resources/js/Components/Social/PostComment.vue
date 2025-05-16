<script setup>
import { defineProps } from 'vue';

const props = defineProps({
    comment: {
        type: Object,
        required: true
    }
});

const formatDate = (date) => {
    const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    return new Date(date).toLocaleDateString('es-ES', options);
};
</script>

<template>
    <div class="flex items-center gap-2">
        <div class="w-8 h-8 rounded-full overflow-hidden flex-shrink-0">
            <img v-if="comment.user.profile && comment.user.profile.profile_picture"
                :src="comment.user.profile.profile_picture || '/images/default-avatar.jpg'" :alt="comment.user.name"
                class="w-full h-full object-cover" />
            <div v-else
                class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center text-[#193CB8] shadow-sm border-2 border-white">
                <i class='bx bxs-user text-md'></i>
            </div>
        </div>
        <div class="flex-1 bg-gray-50 p-2 rounded-lg">
            <div class="flex items-center justify-between">
                <h4 class="font-medium text-sm text-gray-800">{{ comment.user.name + ' ' + comment.user.last_name_1 + ' ' + comment.user.last_name_2 ?? null }}</h4>
                <p class="text-xs text-gray-500">{{ formatDate(comment.created_at) }}</p>
            </div>
            <p class="text-sm text-gray-700" v-html="comment.content"></p>
        </div>
    </div>
</template>