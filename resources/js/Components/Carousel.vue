<template>
    <section class="hero relative bg-cover bg-center text-white text-center overflow-hidden">
        <div class="carousel absolute bottom-0 inset-x-0 w-full h-full">
            <div class="carousel-inner relative w-full h-full overflow-hidden">
                <div 
                    v-for="(image, index) in images" 
                    :key="index"
                    :class="[
                        'carousel-item absolute w-full h-full transition-all duration-1500 ease-in-out',
                        currentIndex === index ? activeClasses : inactiveClasses,
                        transitionDirections[transitionType].enter
                    ]"
                    :style="{
                        zIndex: currentIndex === index ? 10 : 1,
                        backgroundImage: `url(${image})`,
                        backgroundSize: 'cover',
                        backgroundPosition: 'center'
                    }"
                >
                </div>
                <div class="absolute inset-0 bg-black opacity-75 backdrop-blur-sm"></div>
            </div>
            
            <!-- Indicadores (opcional) -->
            <div class="carousel-indicators absolute bottom-5 left-0 right-0 flex justify-center space-x-2 z-20">
                <button 
                    v-for="(_, index) in images" 
                    :key="index"
                    @click="goToSlide(index)"
                    :class="[
                        'w-2.5 h-2.5 rounded-full transition-all duration-300',
                        currentIndex === index ? 'bg-white scale-125' : 'bg-white/50 hover:bg-white/70'
                    ]"
                    aria-label="Ir a la imagen"
                ></button>
            </div>
            
            <!-- Controles (opcional) -->
            <button 
                @click="prev" 
                class="carousel-control absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/30 hover:bg-black/50 flex items-center justify-center z-20 transition-all duration-300"
                aria-label="Imagen anterior"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <button 
                @click="next" 
                class="carousel-control absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/30 hover:bg-black/50 flex items-center justify-center z-20 transition-all duration-300"
                aria-label="Imagen siguiente"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>
        
        <header class="absolute inset-0 flex flex-col items-center justify-center z-30">
            <h1 class="text-4xl font-bold text-shadow-lg">¡Bienvenido a Alumni!</h1>
            <p class="mt-4 text-lg text-shadow-md">Encuentra ofertas de trabajo y conecta con tus compañeros</p>
            <button 
                @click="scrollToShowcase" 
                class="mt-6 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transform hover:scale-105 transition-all duration-300 shadow-lg"
            >
                Descubre más
            </button>
        </header>
    </section>
</template>

<script setup>
import { ref, onMounted, onUnmounted, defineProps, watch } from 'vue';

const props = defineProps({
    images: {
        type: Array,
        required: true
    },
    autoplay: {
        type: Boolean,
        default: true
    },
    interval: {
        type: Number,
        default: 5000
    },
    showControls: {
        type: Boolean,
        default: true
    },
    showIndicators: {
        type: Boolean,
        default: true
    },
    transitionType: {
        type: String,
        default: 'fade', // 'fade', 'slide', 'zoom', 'kenBurns'
        validator: (value) => ['fade', 'slide', 'zoom', 'kenBurns'].includes(value)
    }
});

const currentIndex = ref(0);
const previousIndex = ref(0);
const isTransitioning = ref(false);
let intervalId = null;

// Clases para los diferentes estados de las diapositivas
const activeClasses = 'opacity-100 transform scale-100 translate-x-0';
const inactiveClasses = 'opacity-0 transform scale-105';

// Definir diferentes tipos de transiciones
const transitionDirections = {
    fade: {
        enter: '',
        leave: ''
    },
    slide: {
        enter: 'slide-enter',
        leave: 'slide-leave'
    },
    zoom: {
        enter: 'zoom-enter',
        leave: 'zoom-leave'
    },
    kenBurns: {
        enter: 'ken-burns-enter',
        leave: 'ken-burns-leave'
    }
};

const goToSlide = (index) => {
    if (isTransitioning.value || index === currentIndex.value) return;
    
    isTransitioning.value = true;
    previousIndex.value = currentIndex.value;
    currentIndex.value = index;
    
    // Reiniciar el temporizador si está en autoplay
    if (props.autoplay && intervalId) {
        clearInterval(intervalId);
        intervalId = setInterval(next, props.interval);
    }
    
    // Permitir la siguiente transición después de que termine la actual
    setTimeout(() => {
        isTransitioning.value = false;
    }, 1500); // Debe coincidir con la duración de la transición
};

const next = () => {
    if (isTransitioning.value) return;
    const nextIndex = (currentIndex.value + 1) % props.images.length;
    goToSlide(nextIndex);
};

const prev = () => {
    if (isTransitioning.value) return;
    const prevIndex = (currentIndex.value - 1 + props.images.length) % props.images.length;
    goToSlide(prevIndex);
};

const scrollToShowcase = () => {
    const showcaseSection = document.querySelector('.page-showcase');
    if (showcaseSection) {
        showcaseSection.scrollIntoView({ behavior: 'smooth' });
    }
};

// Gestionar eventos de teclado para accesibilidad
const handleKeyDown = (e) => {
    if (e.key === 'ArrowLeft') {
        prev();
    } else if (e.key === 'ArrowRight') {
        next();
    }
};

onMounted(() => {
    if (props.autoplay) {
        intervalId = setInterval(next, props.interval);
    }
    
    // Añadir eventos de teclado para accesibilidad
    window.addEventListener('keydown', handleKeyDown);
    
    // Pausar autoplay cuando la pestaña no está activa
    document.addEventListener('visibilitychange', () => {
        if (document.hidden && intervalId) {
            clearInterval(intervalId);
        } else if (!document.hidden && props.autoplay && !intervalId) {
            intervalId = setInterval(next, props.interval);
        }
    });
});

onUnmounted(() => {
    if (intervalId) {
        clearInterval(intervalId);
    }
    window.removeEventListener('keydown', handleKeyDown);
});

// Observar cambios en las propiedades
watch(() => props.autoplay, (newValue) => {
    if (newValue && !intervalId) {
        intervalId = setInterval(next, props.interval);
    } else if (!newValue && intervalId) {
        clearInterval(intervalId);
        intervalId = null;
    }
});

watch(() => props.interval, (newValue) => {
    if (props.autoplay && intervalId) {
        clearInterval(intervalId);
        intervalId = setInterval(next, newValue);
    }
});
</script>

<style scoped>
.hero {
    @apply h-screen relative;
}

.carousel {
    @apply absolute w-full h-full;
}

.carousel-inner {
    @apply relative w-full h-full;
}

.carousel-item {
    @apply absolute w-full h-full;
}

/* Efectos de transición */
.slide-enter {
    @apply translate-x-0;
}

.slide-leave {
    @apply -translate-x-full;
}

.zoom-enter {
    @apply scale-110;
}

.zoom-leave {
    @apply scale-100;
}

.ken-burns-enter {
    @apply scale-110 origin-center;
    animation: kenBurns 10s ease-in-out infinite alternate;
}

.ken-burns-leave {
    @apply scale-100;
}

@keyframes kenBurns {
    0% {
        transform: scale(1.1) translate(0, 0);
    }
    25% {
        transform: scale(1.15) translate(-1%, -1%);
    }
    50% {
        transform: scale(1.2) translate(1%, 0);
    }
    75% {
        transform: scale(1.15) translate(0, 1%);
    }
    100% {
        transform: scale(1.1) translate(1%, -1%);
    }
}

/* Sombras de texto para mejor legibilidad */
.text-shadow-lg {
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
}

.text-shadow-md {
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
}

/* Animación para el botón de descubrir más */
@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
}

/* Animación para los controles */
.carousel-control {
    opacity: 0.7;
    transition: opacity 0.3s, transform 0.3s;
}

.carousel-control:hover {
    opacity: 1;
    transform: translateY(-50%) scale(1.1);
}

/* Animación para los indicadores */
.carousel-indicators button {
    transition: transform 0.3s, background-color 0.3s;
}

.carousel-indicators button:hover {
    transform: scale(1.2);
}
</style>