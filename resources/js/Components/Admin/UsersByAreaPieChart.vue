<script setup>
import { Pie } from 'vue-chartjs';
import { computed } from 'vue';

const props = defineProps({
  usersByArea: Object,
});

const chartData = computed(() => {
  const labels = Object.keys(props.usersByArea || {});
  const data = Object.values(props.usersByArea || {});
  return {
    labels,
    datasets: [
      {
        label: 'Users by Training Area',
        backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#a78bfa', '#f87171'],
        data,
      },
    ],
  };
});
</script>

<template>
  <Pie :data="chartData" :options="{ responsive: true, maintainAspectRatio: false }" style="height: 300px;" />
</template>
