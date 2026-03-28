import './bootstrap';

import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

Alpine.start();

const initializeCharts = () => {
    document.querySelectorAll('[data-chart-config]').forEach((canvas) => {
        if (canvas.dataset.chartReady === 'true') {
            return;
        }

        const config = JSON.parse(canvas.dataset.chartConfig);
        // eslint-disable-next-line no-new
        new Chart(canvas, config);
        canvas.dataset.chartReady = 'true';
    });
};
document.addEventListener('DOMContentLoaded', initializeCharts);
window.addEventListener('datavault:refresh-charts', initializeCharts);
