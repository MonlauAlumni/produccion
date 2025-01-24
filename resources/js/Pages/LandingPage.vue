<template>
    <Navbar />
    <section >
        <header>
            <h1>Mi Landing Page</h1>
            <p>Un breve mensaje de bienvenida</p>
            <button>Comenzar</button>
        </header>
    </section>
</template>

<script setup>
import Navbar from '../Components/Navbar.vue';
import HomeContainer from '../Components/HomeContainer.vue';
</script>

<style scoped>
.landing-page {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100vh;
}
</style>