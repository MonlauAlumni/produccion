<script setup>
import { Bar } from 'vue-chartjs';
import { computed } from 'vue';

const props = defineProps({
  companiesBySector: Object,
});

const chartData = computed(() => {
  const labels = Object.keys(props.companiesBySector || {});
  const data = Object.values(props.companiesBySector || {});
  return {
    labels,
    datasets: [
      {
        label: 'Empresas por Sector',
        backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#a78bfa', '#f87171', '#f472b6', '#facc15'],
        data,
      },
    ],
  };
});
</script>

<template>
  <div style="height:300px; max-height:300px; min-height:300px;">
    <Bar :data="chartData" :options="{ responsive: true, maintainAspectRatio: false }" style="height: 100%;" />
  </div>
</template>
