<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div class="min-h-screen bg-[linear-gradient(135deg,#f7f4f2_0%,#f3f6fb_45%,#eef2ff_100%)] -m-4 sm:-m-6 lg:-m-8 p-4 sm:p-6 lg:p-8">
    <div class="mx-auto max-w-7xl">
        <div class="overflow-hidden rounded-[28px] bg-white/90 shadow-[0_25px_80px_rgba(15,23,42,0.10)] ring-1 ring-white/60 backdrop-blur">
            <div class="p-5 sm:p-7 lg:p-8">

                <!-- Header -->
                <header class="mb-8 flex flex-col gap-4 border-b border-slate-100 pb-6 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="mb-2 inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold tracking-wide text-indigo-700 ring-1 ring-indigo-100">
                            Admin Panel
                        </div>
                        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                            Admin Dashboard
                        </h1>
                        <p class="mt-2 text-sm text-slate-500">
                            System-wide overview and management
                        </p>
                    </div>
                        
                    <div class="flex flex-wrap items-center gap-3">
                        <div id="liveSummaryBadge" class="hidden items-center gap-2 rounded-2xl bg-slate-50 px-4 py-2 text-sm text-slate-500 ring-1 ring-slate-200 md:flex">
                            <span class="text-base">📊</span>
                            <span>Live system summary</span>
                        </div>

                        <a href="<?= site_url('admin/users/create') ?>"
                           class="inline-flex items-center rounded-2xl bg-[#34348f] px-5 py-3 text-sm font-medium text-white shadow-lg shadow-indigo-200 transition-all duration-300 hover:-translate-y-0.5 hover:bg-[#2d2d7b] hover:shadow-xl">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add User
                        </a>
                    </div>
                </header>
                
                <!-- Quick Stats -->
<div class="mb-8 grid gap-5 sm:grid-cols-2 xl:grid-cols-4">

    <!-- Total Scholars Clickable -->
    <a href="<?= site_url('scholars') ?>"
       class="group relative block overflow-hidden rounded-[24px] bg-gradient-to-br from-[#eef4ff] via-white to-[#f4f7ff] p-5 shadow-sm ring-1 ring-indigo-100 transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_18px_40px_rgba(79,70,229,0.12)] focus:outline-none focus:ring-2 focus:ring-indigo-400">
        <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-indigo-100/60 blur-2xl"></div>
        <div class="relative">
            <div class="mb-4 flex items-center justify-between">
                <div class="rounded-2xl bg-indigo-600/10 p-3 text-xl">🎓</div>
                <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-[11px] font-semibold text-indigo-700">Overview</span>
            </div>
            <div class="text-sm font-medium text-slate-500">Total Scholars</div>
            <div id="totalScholars" class="mt-2 text-3xl font-bold tracking-tight text-slate-900 group-hover:text-indigo-700 transition">
                <?= esc($stats['total_scholars']) ?>
            </div>
            <p class="mt-2 text-xs text-slate-400">All active scholar records in the system.</p>
            <div class="mt-4 inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 opacity-0 transition duration-300 group-hover:opacity-100">
                View scholars
                <span class="transition-transform duration-300 group-hover:translate-x-1">→</span>
            </div>
        </div>
    </a>


<!-- Active Schools Clickable -->
    <a href="<?= site_url('admin/schools') ?>"
       class="group relative block overflow-hidden rounded-[24px] bg-gradient-to-br from-[#ecfff7] via-white to-[#f3fffb] p-5 shadow-sm ring-1 ring-emerald-100 transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_18px_40px_rgba(16,185,129,0.12)] focus:outline-none focus:ring-2 focus:ring-emerald-400">
        <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-emerald-100/60 blur-2xl"></div>
        <div class="relative">
            <div class="mb-4 flex items-center justify-between">
                <div class="rounded-2xl bg-emerald-600/10 p-3 text-xl">🏫</div>
                <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700">Active</span>
            </div>
            <div class="text-sm font-medium text-slate-500">Active Schools</div>
            <div id="activeSchools" class="mt-2 text-3xl font-bold tracking-tight text-slate-900 group-hover:text-emerald-700 transition">
                <?= esc($stats['active_schools']) ?>
            </div>
            <p class="mt-2 text-xs text-slate-400">Current partner schools in the platform.</p>
            <div class="mt-4 inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 opacity-0 transition duration-300 group-hover:opacity-100">
                View schools
                <span class="transition-transform duration-300 group-hover:translate-x-1">→</span>
            </div>
        </div>
    </a>

                    <div class="group relative overflow-hidden rounded-[24px] bg-gradient-to-br from-[#fff7ed] via-white to-[#fffaf5] p-5 shadow-sm ring-1 ring-amber-100 transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_18px_40px_rgba(245,158,11,0.12)]">
                        <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-amber-100/60 blur-2xl"></div>
                        <div class="relative">
                            <div class="mb-4 flex items-center justify-between">
                                <div class="rounded-2xl bg-amber-500/10 p-3 text-xl">💳</div>
                                <span class="rounded-full bg-amber-50 px-2.5 py-1 text-[11px] font-semibold text-amber-700">Pending</span>
                            </div>
                            <div class="text-sm font-medium text-slate-500">Pending Bills</div>
                            <div id="pendingBills" class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                                <?= esc($stats['pending_bills']) ?>
                            </div>
                            <p class="mt-2 text-xs text-slate-400">Billing items waiting for processing.</p>
                        </div>
                    </div>

                    <div class="group relative overflow-hidden rounded-[24px] bg-gradient-to-br from-[#f5f3ff] via-white to-[#faf7ff] p-5 shadow-sm ring-1 ring-violet-100 transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_18px_40px_rgba(139,92,246,0.12)]">
                        <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-violet-100/60 blur-2xl"></div>
                        <div class="relative">
                            <div class="mb-4 flex items-center justify-between">
                                <div class="rounded-2xl bg-violet-500/10 p-3 text-xl">✉️</div>
                                <span class="rounded-full bg-violet-50 px-2.5 py-1 text-[11px] font-semibold text-violet-700">Inbox</span>
                            </div>
                            <div class="text-sm font-medium text-slate-500">Unread Messages</div>
                            <div id="unreadMessages" class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                                <?= esc($stats['messages']) ?>
                            </div>
                            <p class="mt-2 text-xs text-slate-400">Unread conversations and notices.</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="mb-8">
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">Quick Actions</h2>
                            <p class="text-sm text-slate-500">Fast access to your most used admin tools</p>
                        </div>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                        <a href="<?= site_url('admin/users') ?>"
                           class="group relative overflow-hidden rounded-[24px] bg-white p-6 shadow-sm ring-1 ring-slate-200 transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_18px_45px_rgba(15,23,42,0.08)]">
                            <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-indigo-500 to-blue-500"></div>
                            <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-2xl transition duration-300 group-hover:scale-110">
                                👤
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900">User Management</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-500">Add, edit or deactivate system users.</p>
                            <span class="mt-5 inline-flex items-center font-medium text-indigo-600 transition-transform duration-300 group-hover:translate-x-1">
                                View users
                                <span class="ml-2">→</span>
                            </span>
                        </a>

                        <a href="<?= site_url('messages') ?>"
                           class="group relative overflow-hidden rounded-[24px] bg-white p-6 shadow-sm ring-1 ring-slate-200 transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_18px_45px_rgba(15,23,42,0.08)]">
                            <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-violet-500 to-fuchsia-500"></div>
                            <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-violet-50 text-2xl transition duration-300 group-hover:scale-110">
                                💬
                            </div>
                            <h3 class="text-lg font-semibold capitalize text-slate-900">message</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-500">Message partner schools</p>
                            <span class="mt-5 inline-flex items-center font-medium text-violet-600 transition-transform duration-300 group-hover:translate-x-1">
                                View messages
                                <span class="ml-2">→</span>
                            </span>
                        </a>

                        <a href="/admin/schools"
                           class="group relative overflow-hidden rounded-[24px] bg-white p-6 shadow-sm ring-1 ring-slate-200 transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_18px_45px_rgba(15,23,42,0.08)]">
                            <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-500 to-teal-500"></div>
                            <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-2xl transition duration-300 group-hover:scale-110">
                                🏫
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900">Partner Schools</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-500">Manage school partnerships and access.</p>
                            <span class="mt-5 inline-flex items-center font-medium text-emerald-600 transition-transform duration-300 group-hover:translate-x-1">
                                View schools
                                <span class="ml-2">→</span>
                            </span>
                        </a>
                    </div>
                </div>

                <!-- Unique Pie Chart -->
                <section class="mb-8 overflow-hidden rounded-[28px] bg-white shadow-sm ring-1 ring-slate-200">
                    <header class="flex items-center justify-between border-b border-slate-200 px-5 py-4 sm:px-6">
                        <div>
                            <h2 class="font-semibold text-slate-800">Live System Distribution</h2>
                            <p class="mt-1 text-xs text-slate-500">Modern doughnut chart with live updates</p>
                        </div>
                        <div id="chartStatus" class="rounded-full bg-slate-50 px-3 py-1 text-xs font-medium text-slate-500 ring-1 ring-slate-200">
                            Updating...
                        </div>
                    </header>

                    <div class="grid gap-6 p-5 sm:p-6 lg:grid-cols-[1.2fr,0.8fr] lg:items-center">
                        <div class="relative mx-auto flex h-[340px] w-full max-w-[420px] items-center justify-center">
                            <canvas id="systemSummaryChart"></canvas>
                            <div class="pointer-events-none absolute flex flex-col items-center justify-center text-center">
                                <span class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Total</span>
                                <span id="chartTotal" class="mt-1 text-3xl font-bold text-slate-900">0</span>
                                <span class="mt-1 text-xs text-slate-500">Live Records</span>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="rounded-2xl bg-indigo-50/70 p-4 ring-1 ring-indigo-100">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <span class="h-3.5 w-3.5 rounded-full bg-indigo-500"></span>
                                        <span class="text-sm font-medium text-slate-700">Total Scholars</span>
                                    </div>
                                    <span id="legendScholars" class="text-sm font-bold text-indigo-700"><?= esc($stats['total_scholars']) ?></span>
                                </div>
                            </div>

                            <div class="rounded-2xl bg-emerald-50/70 p-4 ring-1 ring-emerald-100">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <span class="h-3.5 w-3.5 rounded-full bg-emerald-500"></span>
                                        <span class="text-sm font-medium text-slate-700">Active Schools</span>
                                    </div>
                                    <span id="legendSchools" class="text-sm font-bold text-emerald-700"><?= esc($stats['active_schools']) ?></span>
                                </div>
                            </div>

                            <div class="rounded-2xl bg-amber-50/70 p-4 ring-1 ring-amber-100">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <span class="h-3.5 w-3.5 rounded-full bg-amber-500"></span>
                                        <span class="text-sm font-medium text-slate-700">Pending Bills</span>
                                    </div>
                                    <span id="legendBills" class="text-sm font-bold text-amber-700"><?= esc($stats['pending_bills']) ?></span>
                                </div>
                            </div>

                            <div class="rounded-2xl bg-violet-50/70 p-4 ring-1 ring-violet-100">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <span class="h-3.5 w-3.5 rounded-full bg-violet-500"></span>
                                        <span class="text-sm font-medium text-slate-700">Unread Messages</span>
                                    </div>
                                    <span id="legendMessages" class="text-sm font-bold text-violet-700"><?= esc($stats['messages']) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const totalScholarsEl = document.getElementById('totalScholars');
    const activeSchoolsEl = document.getElementById('activeSchools');
    const pendingBillsEl = document.getElementById('pendingBills');
    const unreadMessagesEl = document.getElementById('unreadMessages');

    const legendScholars = document.getElementById('legendScholars');
    const legendSchools = document.getElementById('legendSchools');
    const legendBills = document.getElementById('legendBills');
    const legendMessages = document.getElementById('legendMessages');
    const chartTotalEl = document.getElementById('chartTotal');

    const liveSummaryBadge = document.getElementById('liveSummaryBadge');
    const chartStatus = document.getElementById('chartStatus');
    const canvas = document.getElementById('systemSummaryChart');

    function pulseBadge(text, good = true) {
        if (!liveSummaryBadge) return;
        liveSummaryBadge.className = 'hidden md:flex items-center gap-2 rounded-2xl px-4 py-2 text-sm ring-1';
        if (good) {
            liveSummaryBadge.classList.add('bg-emerald-50', 'text-emerald-700', 'ring-emerald-200');
            liveSummaryBadge.innerHTML = '<span class="text-base">🟢</span><span>' + text + '</span>';
        } else {
            liveSummaryBadge.classList.add('bg-red-50', 'text-red-700', 'ring-red-200');
            liveSummaryBadge.innerHTML = '<span class="text-base">⚠️</span><span>' + text + '</span>';
        }
    }

    function updateChartStatus(text, good = true) {
        if (!chartStatus) return;
        chartStatus.className = 'rounded-full px-3 py-1 text-xs font-medium ring-1';
        if (good) {
            chartStatus.classList.add('bg-emerald-50', 'text-emerald-700', 'ring-emerald-200');
        } else {
            chartStatus.classList.add('bg-red-50', 'text-red-700', 'ring-red-200');
        }
        chartStatus.textContent = text;
    }

    function getChartData() {
        return [
            parseInt(totalScholarsEl?.textContent || 0),
            parseInt(activeSchoolsEl?.textContent || 0),
            parseInt(pendingBillsEl?.textContent || 0),
            parseInt(unreadMessagesEl?.textContent || 0)
        ];
    }

    function updateTotal(data) {
        const total = data.reduce((sum, value) => sum + value, 0);
        if (chartTotalEl) {
            chartTotalEl.textContent = total;
        }
    }

    const centerShadowPlugin = {
        id: 'centerShadowPlugin',
        beforeDraw(chart) {
            const { ctx } = chart;
            const meta = chart.getDatasetMeta(0);

            if (!meta || !meta.data || !meta.data.length) return;

            const x = meta.data[0].x;
            const y = meta.data[0].y;

            ctx.save();
            ctx.beginPath();
            ctx.fillStyle = 'rgba(148, 163, 184, 0.10)';
            ctx.arc(x, y + 8, 52, 0, Math.PI * 2);
            ctx.fill();
            ctx.restore();
        }
    };

    if (!canvas || typeof Chart === 'undefined') {
        console.error('Chart.js is not loaded or canvas is missing.');
        updateChartStatus('Chart failed', false);
        pulseBadge('Chart.js not loaded', false);
        return;
    }

    const initialData = getChartData();
    updateTotal(initialData);

    const systemSummaryChart = new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels: ['Total Scholars', 'Active Schools', 'Pending Bills', 'Unread Messages'],
            datasets: [{
                data: initialData,
                backgroundColor: [
                    '#6366f1',
                    '#10b981',
                    '#f59e0b',
                    '#8b5cf6'
                ],
                hoverBackgroundColor: [
                    '#4f46e5',
                    '#059669',
                    '#d97706',
                    '#7c3aed'
                ],
                borderColor: '#ffffff',
                borderWidth: 6,
                hoverOffset: 14,
                spacing: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            animation: {
                animateRotate: true,
                duration: 900
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleColor: '#ffffff',
                    bodyColor: '#e2e8f0',
                    padding: 12,
                    cornerRadius: 14,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            const value = context.raw || 0;
                            const data = context.dataset.data || [];
                            const total = data.reduce((sum, num) => sum + num, 0);
                            const percent = total ? ((value / total) * 100).toFixed(1) : 0;
                            return ` ${context.label}: ${value} (${percent}%)`;
                        }
                    }
                }
            }
        },
        plugins: [centerShadowPlugin]
    });

    async function fetchLiveStats() {
        try {
            const response = await fetch("<?= site_url('dashboard/live-stats') ?>", {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error('Failed to fetch live stats');
            }

            const data = await response.json();
            const stats = data.stats ? data.stats : data;

            totalScholarsEl.textContent = stats.total_scholars ?? 0;
            activeSchoolsEl.textContent = stats.active_schools ?? 0;
            pendingBillsEl.textContent = stats.pending_bills ?? 0;
            unreadMessagesEl.textContent = stats.messages ?? 0;

            legendScholars.textContent = stats.total_scholars ?? 0;
            legendSchools.textContent = stats.active_schools ?? 0;
            legendBills.textContent = stats.pending_bills ?? 0;
            legendMessages.textContent = stats.messages ?? 0;

            const updatedData = [
                parseInt(stats.total_scholars ?? 0),
                parseInt(stats.active_schools ?? 0),
                parseInt(stats.pending_bills ?? 0),
                parseInt(stats.messages ?? 0)
            ];

            systemSummaryChart.data.datasets[0].data = updatedData;
            systemSummaryChart.update();
            updateTotal(updatedData);

            pulseBadge('Live system summary updated', true);
            updateChartStatus('Chart updated', true);

        } catch (error) {
            console.error(error);
            pulseBadge('Unable to refresh summary', false);
            updateChartStatus('Chart failed', false);
        }
    }

    fetchLiveStats();
    setInterval(fetchLiveStats, 5000);
});
</script>

<?= $this->endSection() ?>