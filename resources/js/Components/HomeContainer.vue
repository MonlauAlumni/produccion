<template>
    <div class="w-[50%] h-[300px] bg-gray-200 rounded-3xl p-4">
        <div class="flex justify-between">
            <div class="flex items-center gap-4">
                <img src="https://placehold.co/80x80" alt="logo" class="rounded-full">
                <a href="" class="">{{ name }}</a>
            </div>
            <div>
                <p class="text-sm text-gray-600">Publicada hace {{ postTime }} </p>
            </div>
        </div>
        <div class="gap-4 flex flex-col">
            <h2 class="text-3xl pt-4">{{ postTitle }}</h2>
            <p class="text-gray-600">{{ postExtract }}</p>
        </div>
    </div>
</template>

<script>
export default {
    name: "HomeContainer",
    props: {
        name: {
            type: String,
            default: "Empresa", 
        },
        postTime: {
            type: String,
            default: "1 día", 
        },
        postTitle: {
            type: String,
            default: "Título de la publicación", 
        },
        postExtract: {
            type: String,
            default: "Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.", 
        },
    },
};
</script>