<template>
    <nav class="bg-blue-600">
        <div class="container mx-auto flex justify-between items-center">
            <a href="/" class="text-white text-2xl font-bold">Alumni</a>
            <ul class="flex">
                <li class="ml-6">
                    <Link href="/" class="text-white">Home</Link>
                </li>
                <li class="ml-6">
                    <Link href="/register" class="text-white">Register</Link>
                </li>
                <li class="ml-6">
                    <Link href="/login" class="text-white">Login</Link>
                </li>
            </ul>
        </div>
    </nav>
</template>

<script setup>
import { Link } from '@inertiajs/inertia-vue3';
</script>