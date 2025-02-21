<template>
    <section id="stats-section" class="relative bg-cover bg-center text-white text-center py-16 bg-blue-800">
        <div class="container mx-auto grid grid-cols-4 gap-6">
            <div class="stat-item" v-for="(stat, index) in stats" :key="index">
                <span class="number text-8xl font-bold" :data-value="stat.value">0</span>
                <p class="text-2xl">{{ stat.label }}</p>
            </div>
        </div>
    </section>
</template>

<script setup>
import { onMounted } from 'vue';

const stats = [
    { value: 750, label: "Usuarios registrados" },
    { value: 200, label: "Alumnos en formación profesional" },
    { value: 600, label: "Empresas registradas" },
    { value: 10, label: "Años de experiencia" }
];

onMounted(() => {
    const statsSection = document.getElementById("stats-section");
    const numbers = document.querySelectorAll(".number");
    let hasAnimated = false;

    const animateNumbers = () => {
        numbers.forEach((num) => {
            const target = parseInt(num.getAttribute("data-value"), 10);
            let count = 0;
            const speed = target / 100;

            const updateCount = () => {
                count += speed;
                num.textContent = Math.floor(count);

                if (count < target) {
                    requestAnimationFrame(updateCount);
                } else {
                    num.textContent = target;
                }
            };

            updateCount();
        });
    };

    const observer = new IntersectionObserver(
        (entries) => {
            if (entries[0].isIntersecting && !hasAnimated) {
                animateNumbers();
                hasAnimated = true;
            }
        },
        { threshold: 0.5 }
    );

    observer.observe(statsSection);
});
</script>
