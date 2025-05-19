<template>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-colors duration-300">
        <div class="p-6 pt-20 relative dark:text-gray-200">
            <div class="absolute left-6 -top-12">
                <div class="relative">
                    <div class="w-24 h-24 rounded-xl overflow-hidden border-4 border-white dark:border-gray-800 bg-white dark:bg-gray-700 shadow-md">
                        <img v-if="group.group_logo"
                            :src="group.group_logo || '/images/default-logo.jpg'"
                            :alt="group.group_name + ' logo'" class="w-full h-full object-cover" />
                        <div v-else
                            class="w-full h-full rounded-lg bg-gray-200 dark:bg-gray-600 flex items-center justify-center text-gray-500 dark:text-gray-400">
                            <i class='bx bx-group text-4xl'></i>
                        </div>
                    </div>
                    <label v-if="isAdmin"
                        class="absolute -bottom-2 -right-2 cursor-pointer bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-[#193CB8] p-1.5 rounded-full shadow-md transition-all">
                        <i class='bx bx-camera text-lg'></i>
                        <input type="file" @change="handleLogoUpload" class="hidden" accept="image/*">
                    </label>
                </div>
            </div>

            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ group.name }}</h1>
                    <div class="flex items-center text-gray-500 dark:text-gray-400 text-sm mt-2">
                        <span class="flex items-center mr-4">
                            <i class='bx bx-user-circle mr-1'></i> {{ group.members ? group.members.length : 0 }} miembros
                        </span>
                        <span class="flex items-center">
                            <i class='bx bx-lock-open-alt mr-1' v-if="group.privacy === 'public'"></i>
                            <i class='bx bx-lock-alt mr-1' v-else></i>
                            {{ group.privacy === 'public' ? 'Grupo público' : 'Grupo privado' }}
                        </span>
                    </div>
                </div>
                <div class="mt-4 md:mt-0 flex items-center gap-3">
                    <button v-if="!isMember && group.privacy === 'public'" @click="handleJoinGroup"
                        class="px-4 py-2 bg-[#193CB8] text-white rounded-lg hover:bg-[#142d8c] transition-colors flex items-center cursor-pointer">
                        <i class='bx bx-user-plus mr-1'></i> Unirse al grupo
                    </button>
                    <button v-if="!isMember && group.privacy === 'private'" @click="$emit('request-join')"
                        class="px-4 py-2 bg-[#193CB8] text-white rounded-lg hover:bg-[#142d8c] transition-colors flex items-center cursor-pointer">
                        <i class='bx bx-envelope mr-1'></i> Solicitar unirse
                    </button>
                    <button v-if="isAdmin" @click="$emit('invite-members')"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors flex items-center cursor-pointer">
                        <i class='bx bx-user-plus mr-1'></i> Invitar
                    </button>
                    <button v-if="isAdmin" @click="$emit('edit-group')"
                        class="p-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                        <i class='bx bx-edit-alt'></i>
                    </button>
                </div>
            </div>

            <p class="text-gray-600 dark:text-gray-400 mt-4">{{ group.description }}</p>
            <div v-if="group.tags && group.tags.length > 0" class="flex flex-wrap gap-2 mt-4">
                <span v-for="(tag, index) in group.tags" :key="index" @click="this.$inertia.visit(`/connect/search?q=${tag}`)"
                    class="px-2 py-1 bg-[#A3C1F7]/10 dark:bg-[#A3C1F7]/20 text-[#A3C1F7] rounded-full text-xs cursor-pointer hover:bg-[#A3C1F7]/20 dark:hover:bg-[#A3C1F7]/30 transition-colors">
                    #{{ tag }}
                </span>
            </div>
            <div class="flex border-b border-gray-200 dark:border-gray-700 mt-6">
                <button v-for="tab in tabs" :key="tab.value" @click="handleTabChange(tab.value)"
                    class="px-4 py-2 font-medium text-sm transition-colors"
                    :class="activeTab === tab.value ? 'text-[#A3C1F7] border-b-2 border-[#A3C1F7]' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 cursor-pointer'">
                    {{ tab.label }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import { router } from '@inertiajs/vue3';

export default {
    props: {
        group: Object,
        isAdmin: Boolean,
        isMember: Boolean,
        activeTab: {
            type: String,
            default: 'publicaciones'
        }
    },
    emits: ['update:activeTab', 'request-join', 'invite-members', 'edit-group'],
    data() {
        return {
            tabs: [
                { label: 'Publicaciones', value: 'publicaciones' },
                { label: 'Miembros', value: 'miembros' },
                { label: 'Eventos', value: 'eventos' },
            ]
        };
    },
    methods: {
        handleLogoUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('logo', file);

            router.post(`/grupos/${this.group.slug}/update-logo`, formData, {
                onSuccess: () => {
                },
                onError: () => {
                }
            });
        },
        handleJoinGroup() {
            router.post(`/grupos/${this.group.slug}/join`, {}, {
                onSuccess: () => {
                }
            });
        },
        handleTabChange(tabValue) {
            this.$emit('update:activeTab', tabValue);
        }
    }
};
</script>