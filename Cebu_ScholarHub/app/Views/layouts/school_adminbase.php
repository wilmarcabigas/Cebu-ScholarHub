<!doctype html>
<html lang="en" class="h-full bg-slate-100">
<head>
  <meta charset="utf-8">
  <title><?= esc($title ?? 'Cebu Scholars Platform') ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <script>
    tailwind.config = {
      theme: {
        extend: {
          container: { center: true, padding: '1rem' },
          boxShadow: {
            softxl: '0 20px 45px rgba(15, 23, 42, 0.10)',
            softmd: '0 10px 25px rgba(15, 23, 42, 0.08)',
            glow: '0 10px 30px rgba(79, 70, 229, 0.18)',
            panel: '0 25px 80px rgba(15, 23, 42, 0.10)',
            float: '0 18px 45px rgba(15, 23, 42, 0.08)'
          },
          backgroundImage: {
            'page-soft': 'linear-gradient(135deg, #f8fafc 0%, #eef2ff 45%, #f1f5f9 100%)',
            'brand-soft': 'linear-gradient(135deg, #34348f 0%, #4f46e5 55%, #2563eb 100%)'
          }
        }
      }
    }
  </script>

  <style>
    canvas {
      image-rendering: auto;
    }
  </style>
</head>

<body class="h-full flex flex-col bg-page-soft text-slate-900 antialiased">

  <div class="pointer-events-none fixed inset-0 overflow-hidden">
    <div class="absolute -top-24 -left-16 h-72 w-72 rounded-full bg-indigo-200/30 blur-3xl"></div>
    <div class="absolute top-1/3 -right-16 h-80 w-80 rounded-full bg-blue-200/30 blur-3xl"></div>
    <div class="absolute bottom-0 left-1/3 h-72 w-72 rounded-full bg-violet-200/20 blur-3xl"></div>
  </div>

  <nav class="sticky top-0 z-50 border-b border-white/40 bg-white/75 backdrop-blur-2xl shadow-sm">
    <div class="container flex items-center justify-between h-18 py-3">
      <a href="<?= site_url('dashboard') ?>"
         class="group flex items-center gap-3 transition duration-300">
        <div class="relative flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-soft text-white shadow-glow transition duration-300 group-hover:scale-105 group-hover:rotate-3">
          <div class="absolute inset-0 rounded-2xl bg-white/10"></div>
          <svg class="relative h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l8 4v5c0 5-3.5 8-8 9-4.5-1-8-4-8-9V7l8-4z"/>
          </svg>
        </div>

        <div class="leading-tight">
          <div class="text-base font-extrabold tracking-wide text-slate-900 transition group-hover:text-indigo-700">
            Cebu ScholarHub
          </div>
          <div class="hidden text-[11px] text-slate-500 sm:block">
            Scholarship Management System
          </div>
        </div>
      </a>

      <?php $authUser = session()->get('auth_user'); ?>
      <?php if ($authUser): ?>
        <div class="flex items-center gap-3">
          <div class="hidden items-center gap-3 rounded-2xl border border-white/70 bg-white/80 px-3 py-2 shadow-sm backdrop-blur md:flex">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-blue-500 text-sm font-bold text-white shadow-md">
              <?= strtoupper(substr($authUser['full_name'] ?? 'U', 0, 1)) ?>
            </div>
            <div class="leading-tight">
              <div class="text-sm font-semibold text-slate-800">
                <?= esc($authUser['full_name']) ?>
              </div>
              <div class="text-[11px] uppercase tracking-wide text-slate-500">
                <?= esc($authUser['role']) ?>
              </div>
            </div>
          </div>

          <span class="hidden sm:inline rounded-full border border-slate-200 bg-white/90 px-3 py-1.5 text-sm text-slate-700 shadow-sm transition hover:border-indigo-200 hover:text-indigo-700 md:hidden">
            <?= esc($authUser['full_name']) ?>
            <span class="opacity-70"> (<?= esc($authUser['role']) ?>)</span>
          </span>

          <a href="<?= site_url('logout') ?>"
             class="inline-flex items-center rounded-2xl bg-brand-soft px-4 py-2.5 text-sm font-semibold text-white shadow-md transition duration-300 hover:-translate-y-0.5 hover:shadow-glow focus:outline-none focus:ring-2 focus:ring-indigo-300">
            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H6a2 2 0 01-2-2V7a2 2 0 012-2h5a2 2 0 012 2v1"/>
            </svg>
            Logout
          </a>
        </div>
      <?php endif; ?>
    </div>
  </nav>

  <?php
    $showBack = $show_back ?? false;
    $backUrl  = $back_url ?? null;
  ?>

  <?php if ($showBack): ?>
    <div class="container mt-6 relative z-10">
      <?php if ($backUrl): ?>
        <a href="<?= esc($backUrl) ?>"
           class="group inline-flex items-center gap-2 rounded-2xl border border-white/80 bg-white/85 px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm backdrop-blur transition duration-300 hover:-translate-y-0.5 hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700 hover:shadow-softmd">
          <span class="transition group-hover:-translate-x-0.5">←</span>
          <span>Back</span>
        </a>
      <?php else: ?>
        <button type="button"
                onclick="goBack()"
                class="group inline-flex items-center gap-2 rounded-2xl border border-white/80 bg-white/85 px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm backdrop-blur transition duration-300 hover:-translate-y-0.5 hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700 hover:shadow-softmd">
          <span class="transition group-hover:-translate-x-0.5">←</span>
          <span>Back</span>
        </button>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <main class="container my-8 flex-1 relative z-10">
    <div class="rounded-[30px] border border-white/60 bg-white/55 p-3 shadow-panel backdrop-blur-xl sm:p-4">
      <div class="rounded-[26px] bg-white/75 p-2 sm:p-3">
        <?= $this->renderSection('content') ?>
      </div>
    </div>
  </main>

  <footer class="relative z-10 mt-12 border-t border-white/50 bg-white/70 backdrop-blur-2xl">
    <div class="container py-10">
      <div class="grid grid-cols-1 gap-8 md:grid-cols-3 text-sm">
        <div class="rounded-[24px] border border-white/70 bg-white/80 p-6 shadow-sm backdrop-blur transition duration-300 hover:-translate-y-1 hover:shadow-softmd">
          <div class="mb-4 flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-600 to-blue-600 text-white shadow-md">
              <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l8 4v5c0 5-3.5 8-8 9-4.5-1-8-4-8-9V7l8-4z"/>
              </svg>
            </div>
            <h3 class="text-slate-900 font-bold">Cebu ScholarHub</h3>
          </div>
          <p class="text-slate-600 leading-relaxed">
            An integrated scholarship management system of the
            <strong>Cebu City Scholars Office</strong> under the
            <strong>Cebu City Government</strong>.
          </p>
        </div>

        <div class="rounded-[24px] border border-white/70 bg-white/80 p-6 shadow-sm backdrop-blur transition duration-300 hover:-translate-y-1 hover:shadow-softmd">
          <h3 class="text-slate-900 font-bold mb-4">Quick Links</h3>
          <ul class="space-y-3 text-slate-600">
            <li>
              <a href="<?= site_url('dashboard') ?>" class="inline-flex items-center gap-2 transition duration-200 hover:text-indigo-600 hover:translate-x-1">
                <span>•</span>
                <span>Dashboard</span>
              </a>
            </li>
            <?php if ($authUser): ?>
              <li>
                <a href="<?= site_url('logout') ?>" class="inline-flex items-center gap-2 transition duration-200 hover:text-indigo-600 hover:translate-x-1">
                  <span>•</span>
                  <span>Logout</span>
                </a>
              </li>
            <?php endif; ?>
          </ul>
        </div>

        <div class="rounded-[24px] border border-white/70 bg-white/80 p-6 shadow-sm backdrop-blur transition duration-300 hover:-translate-y-1 hover:shadow-softmd">
          <h3 class="text-slate-900 font-bold mb-4">Contact Information</h3>
          <ul class="space-y-3 text-slate-600">
            <li class="transition hover:text-slate-900">Cebu City Scholars Office</li>
            <li>
              <a href="https://www.cebucity.gov.ph/" target="_blank" rel="noopener noreferrer" class="transition hover:text-indigo-600">
                Website: www.cebucity.gov.ph
              </a>
            </li>
            <li class="transition hover:text-slate-900">Phone: 503-3024</li>
            <li class="transition hover:text-slate-900">Cebu City, Philippines</li>
          </ul>
        </div>
      </div>

      <div class="mt-10 flex flex-col items-center justify-between border-t border-slate-200 pt-6 text-xs text-slate-500 md:flex-row">
        <p class="transition hover:text-slate-700">
          © <?= date('Y') ?> Cebu City Scholars Office. All rights reserved.
        </p>

        <p class="mt-2 transition hover:text-slate-700 md:mt-0">
          System Version 1.0 • Developed for Capstone Project
        </p>
      </div>
    </div>
  </footer>

  <script>
    function goBack() {
      if (window.history.length > 1) {
        window.history.back();
      } else {
        window.location.href = "<?= site_url('dashboard') ?>";
      }
    }

    document.addEventListener('DOMContentLoaded', function () {
      const courseChartCanvas = document.getElementById('coursePieChart');
      const courseEmptyBox = document.getElementById('courseChartEmpty');

      const statusChartCanvas = document.getElementById('statusPieChart');
      const statusEmptyBox = document.getElementById('statusChartEmpty');

      const statActiveScholars = document.getElementById('statActiveScholars');
      const statPendingBills = document.getElementById('statPendingBills');
      const statMessages = document.getElementById('statMessages');
      const statRequirementsDue = document.getElementById('statRequirementsDue');

      let coursePieChart = null;
      let statusPieChart = null;

      function createCenterTextPlugin(labelText) {
        return {
          id: 'centerTextPlugin_' + labelText.replace(/\s+/g, '_'),
          afterDraw(chart) {
            const { ctx } = chart;
            const meta = chart.getDatasetMeta(0);

            if (!meta || !meta.data || !meta.data.length) return;

            const x = meta.data[0].x;
            const y = meta.data[0].y;
            const total = chart.data.datasets[0].data.reduce((sum, value) => sum + Number(value), 0);

            ctx.save();
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';

            ctx.font = '600 14px sans-serif';
            ctx.fillStyle = '#6b7280';
            ctx.fillText(labelText, x, y - 12);

            ctx.font = '700 28px sans-serif';
            ctx.fillStyle = '#111827';
            ctx.fillText(total, x, y + 14);
            ctx.restore();
          }
        };
      }

      function buildOrUpdateDoughnutChart(chartInstance, canvas, labels, totals, colors, centerLabel, emptyBox) {
        if (!canvas) return chartInstance;

        if (!labels.length || !totals.length) {
          if (emptyBox) emptyBox.classList.remove('hidden');
          if (chartInstance) {
            chartInstance.destroy();
            chartInstance = null;
          }
          return chartInstance;
        }

        if (emptyBox) emptyBox.classList.add('hidden');

        const plugin = createCenterTextPlugin(centerLabel);

        if (chartInstance) {
          chartInstance.data.labels = labels;
          chartInstance.data.datasets[0].data = totals;
          chartInstance.update();
          return chartInstance;
        }

        return new Chart(canvas, {
          type: 'doughnut',
          data: {
            labels: labels,
            datasets: [{
              data: totals,
              backgroundColor: colors,
              borderColor: '#ffffff',
              borderWidth: 3,
              hoverOffset: 10
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
              legend: {
                position: 'bottom',
                labels: {
                  usePointStyle: true,
                  padding: 18,
                  boxWidth: 10,
                  color: '#374151',
                  font: {
                    size: 12
                  }
                }
              },
              tooltip: {
                callbacks: {
                  label: function(context) {
                    const label = context.label || '';
                    const value = context.raw || 0;
                    return label + ': ' + value + ' student(s)';
                  }
                }
              }
            }
          },
          plugins: [plugin]
        });
      }

      async function loadDashboardCharts() {
        try {
          const response = await fetch("<?= site_url('dashboard/liveStats') ?>");
          const result = await response.json();

          if (!result || result.status !== 'success') {
            return;
          }

          if (result.stats) {
            if (statActiveScholars && result.stats.active_scholars !== undefined) {
              statActiveScholars.textContent = result.stats.active_scholars;
            }

            if (statPendingBills && result.stats.pending_bills !== undefined) {
              statPendingBills.textContent = result.stats.pending_bills;
            }

            if (statMessages && result.stats.messages !== undefined) {
              statMessages.textContent = result.stats.messages;
            }

            if (statRequirementsDue && result.stats.requirements_due !== undefined) {
              statRequirementsDue.textContent = result.stats.requirements_due;
            }
          }

          const courseLabels = result.course_chart?.labels || [];
          const courseTotals = result.course_chart?.totals || [];

          coursePieChart = buildOrUpdateDoughnutChart(
            coursePieChart,
            courseChartCanvas,
            courseLabels,
            courseTotals,
            [
              '#4F46E5',
              '#7C3AED',
              '#EC4899',
              '#F59E0B',
              '#10B981',
              '#06B6D4',
              '#EF4444',
              '#84CC16',
              '#14B8A6',
              '#8B5CF6'
            ],
            'Total Students',
            courseEmptyBox
          );

          const statusLabels = result.status_chart?.labels || [];
          const statusTotals = result.status_chart?.totals || [];

          statusPieChart = buildOrUpdateDoughnutChart(
            statusPieChart,
            statusChartCanvas,
            statusLabels,
            statusTotals,
            [
              '#10B981',
              '#F59E0B',
              '#EF4444',
              '#3B82F6',
              '#8B5CF6',
              '#EC4899',
              '#14B8A6',
              '#F97316'
            ],
            'Total Status',
            statusEmptyBox
          );

        } catch (error) {
          console.error('Error loading dashboard charts:', error);
          if (courseEmptyBox) courseEmptyBox.classList.remove('hidden');
          if (statusEmptyBox) statusEmptyBox.classList.remove('hidden');
        }
      }

      if (courseChartCanvas || statusChartCanvas) {
        loadDashboardCharts();
        setInterval(loadDashboardCharts, 5000);
      }
    });
  </script>

</body>
</html>