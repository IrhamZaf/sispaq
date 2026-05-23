/**
 * SISPAQ student dashboard — Apex charts + compact FullCalendar (Vuexy analytics/statistics style)
 */
'use strict';

function initSispaqDashboardCharts() {
  if (typeof ApexCharts === 'undefined') {
    return;
  }

  const data = window.sispaqDashboard || {};
  const labelColor = typeof config !== 'undefined' ? config.colors.textMuted : '#6c757d';
  const fontFamily = typeof config !== 'undefined' ? config.fontFamily : 'inherit';

  const barEl = document.querySelector('#horizontalBarChart');
  if (!barEl || !data.bar || barEl.dataset.chartReady === '1') {
    return;
  }

  const labels = data.bar.labels || [];
  const series = data.bar.series || [];
  const maxVal = Math.max(...series, 10);

  const bar = new ApexCharts(barEl, {
    chart: { height: 220, type: 'bar', toolbar: { show: false } },
    plotOptions: {
      bar: {
        horizontal: true,
        barHeight: '55%',
        distributed: true,
        borderRadius: 6
      }
    },
    grid: {
      strokeDashArray: 10,
      borderColor: typeof config !== 'undefined' ? config.colors.borderColor : '#e9ecef',
      padding: { top: -12, bottom: -8, left: 0 }
    },
    colors: [
      config?.colors?.primary,
      config?.colors?.info,
      config?.colors?.success,
      config?.colors?.warning,
      config?.colors?.danger,
      config?.colors?.secondary
    ].filter(Boolean),
    dataLabels: {
      enabled: true,
      formatter: function (val, opts) {
        return labels[opts.dataPointIndex] || '';
      },
      style: { colors: ['#fff'], fontSize: '11px', fontFamily: fontFamily }
    },
    labels: labels,
    series: [{ data: series }],
    xaxis: {
      max: maxVal,
      labels: {
        formatter: function (val) {
          return val + '%';
        },
        style: { colors: labelColor, fontFamily: fontFamily }
      }
    },
    yaxis: { labels: { show: false } },
    legend: { show: false },
    tooltip: {
      custom: function ({ series, seriesIndex, dataPointIndex }) {
        return '<div class="px-3 py-2"><span>' + series[seriesIndex][dataPointIndex] + '%</span></div>';
      }
    }
  });

  bar.render();
  barEl.dataset.chartReady = '1';
}

function initSispaqDashboard() {
  initSispaqDashboardCharts();
}

document.addEventListener('DOMContentLoaded', initSispaqDashboard);
document.addEventListener('livewire:navigated', initSispaqDashboard);
