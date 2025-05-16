<script setup>
import { ref, onMounted, watch } from 'vue';
import { Chart, BarController, BarElement, CategoryScale, LinearScale, Tooltip, Legend } from 'chart.js';

Chart.register(BarController, BarElement, CategoryScale, LinearScale, Tooltip, Legend);

const props = defineProps({
  data: {
    type: Object,
    required: true,
  }
});

const chartRef = ref(null);
let chartInstance = null;

const renderChart = () => {
  if (!chartRef.value) return;

  if (chartInstance) {
    chartInstance.destroy();
  }

  const ctx = chartRef.value.getContext('2d');
  chartInstance = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: Object.keys(props.data),
      datasets: [{
        label: 'Users by Training Area',
        data: Object.values(props.data),
        backgroundColor: ['#3b82f6', '#10b981', '#8b5cf6'],
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false, // 🔧 muy importante para evitar scroll infinito
      scales: {
        y: {
          beginAtZero: true,
          precision: 0
        }
      }
    }
  });
};

onMounted(() => {
  if (props.data && Object.keys(props.data).length > 0) {
    renderChart();
  }
});

watch(() => props.data, (newVal) => {
  if (newVal && Object.keys(newVal).length > 0) {
    renderChart();
  }
});
</script>

<template>
  <div class="relative w-full h-64">
    <canvas ref="chartRef" class="absolute inset-0 w-full h-full"></canvas>
  </div>
</template>
