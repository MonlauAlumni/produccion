<template>
    <nav class="bg-blue-800 p-4  top-0 left-0 w-full shadow-lg z-50">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex gap-4 items-center">
                <a href="/" class="text-white text-3xl font-bold flex items-center gap-4"> 
                    <img src="/public/images/logo.png" alt="Alumni" class="w-16 h-16">
                    Alumni
                </a>
            </div>
            <ul class="flex items-center gap-4">
                <li v-if="!$page.props.auth.user">
                    <a href="/login" class="text-white text-lg font-semibold">Iniciar Sesión</a>
                </li>
                <li v-if="!$page.props.auth.user">
                    <a href="/register"
                        class="text-white text-lg p-2 border-solid border-2 border-white rounded-3xl font-semibold">Registrarse</a>
                </li>
            </ul>
        </div>    
    </nav>
</template>

<script setup>
</script>