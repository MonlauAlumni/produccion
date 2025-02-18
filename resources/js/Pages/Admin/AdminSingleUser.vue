<script setup>
import 'boxicons';
import AdminLayout from './Layouts/AdminLayout.vue';


import { defineProps } from 'vue';

const props = defineProps({
    user: {
        type: Object,
        required: true
    }
});
</script>
<template>
    <AdminLayout title="Editar usuario">
        <div class="h-full">
            
        </div>
    </AdminLayout>
</template>
<style scoped>

</style>