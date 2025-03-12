<template>
    <div class="cube">
        <div class="face front"><img src="../../../public/images/monlau-logo.png" alt="Alumni" class="w-full h-full">
        </div>
        <div class="face back"><img src="../../../public/images/monlau-logo.png" alt="Alumni" class="w-full h-full">
        </div>
        <div class="face right"><img src="../../../public/images/monlau-logo.png" alt="Alumni" class="w-full h-full">
        </div>
        <div class="face left"><img src="../../../public/images/monlau-logo.png" alt="Alumni" class="w-full h-full">
        </div>
        <div class="face top"><img src="../../../public/images/monlau-logo.png" alt="Alumni" class="w-full h-full">
        </div>
        <div class="face bottom"><img src="../../../public/images/monlau-logo.png" alt="Alumni" class="w-full h-full">
        </div>
    </div>
</template>
<style scoped>
.cube {
    position: relative;
    width: 150px;
    height: 150px;
    transform-style: preserve-3d;
    animation: rotateCube 10s infinite linear;
}

.cube .face {
    position: absolute;
    width: 150px;
    height: 150px;
    background: #193CB8;
    border: 2px solid #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: #fff;
}

.cube .front {
    transform: translateZ(75px);
}

.cube .back {
    transform: rotateY(180deg) translateZ(75px);
}

.cube .right {
    transform: rotateY(90deg) translateZ(75px);
}

.cube .left {
    transform: rotateY(-90deg) translateZ(75px);
}

.cube .top {
    transform: rotateX(90deg) translateZ(75px);
}

.cube .bottom {
    transform: rotateX(-90deg) translateZ(75px);
}

@keyframes rotateCube {
    from {
        transform: rotateX(0deg) rotateY(0deg);
    }

    to {
        transform: rotateX(360deg) rotateY(360deg);
    }
}
</style>