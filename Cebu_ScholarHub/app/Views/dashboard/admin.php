<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div class="space-y-6">

    <!-- Header -->
    <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Admin Dashboard</h1>
            <p class="mt-1 text-sm text-gray-500">System-wide overview and management</p>
        </div>
        <div class="flex gap-3">
            <a href="<?= site_url('admin/billing') ?>"
               class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Billing
            </a>
            <a href="<?= site_url('admin/users/create') ?>"
               class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add User
            </a>
        </div>
    </header>

    <!-- Quick Stats -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-5">
            <div class="flex items-center justify-between mb-1">
                <div class="text-sm font-medium text-gray-500">Total Scholars</div>
                <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-semibold text-gray-900"><?= esc($stats['total_scholars']) ?></div>
            <div class="mt-2 text-xs text-gray-400">Across all partner schools</div>
        </div>

        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-5">
            <div class="flex items-center justify-between mb-1">
                <div class="text-sm font-medium text-gray-500">Active Schools</div>
                <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-semibold text-gray-900"><?= esc($stats['active_schools']) ?></div>
            <div class="mt-2 text-xs text-gray-400">Partner institutions</div>
        </div>

        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-5">
            <div class="flex items-center justify-between mb-1">
                <div class="text-sm font-medium text-gray-500">Pending Bills</div>
                <div class="w-8 h-8 rounded-lg bg-yellow-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-semibold text-gray-900"><?= esc($stats['pending_bills']) ?></div>
            <?php if (($stats['pending_bills'] ?? 0) > 0): ?>
                <div class="mt-2 text-xs text-yellow-600 font-medium">
                    <a href="<?= site_url('admin/billing') ?>" class="hover:underline">Review submissions →</a>
                </div>
            <?php else: ?>
                <div class="mt-2 text-xs text-gray-400">No pending submissions</div>
            <?php endif; ?>
        </div>

        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-5">
            <div class="flex items-center justify-between mb-1">
                <div class="text-sm font-medium text-gray-500">Unread Messages</div>
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-semibold text-gray-900"><?= esc($stats['messages']) ?></div>
            <div class="mt-2 text-xs text-gray-400">
                <a href="<?= site_url('messages') ?>" class="text-indigo-600 hover:underline">Open inbox →</a>
            </div>
        </div>

    </div>

    <!-- Billing Financial Summary -->
    <div class="grid gap-4 sm:grid-cols-3">
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-5">
            <div class="text-sm font-medium text-gray-500 mb-1">Total Billed (Current SY)</div>
            <div class="text-2xl font-semibold text-gray-900">₱<?= number_format($stats['total_billed'] ?? 0, 2) ?></div>
            <div class="mt-2 text-xs text-gray-400">Across all submitted billings</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-5">
            <div class="text-sm font-medium text-gray-500 mb-1">Total Collected</div>
            <div class="text-2xl font-semibold text-green-700">₱<?= number_format($stats['total_collected'] ?? 0, 2) ?></div>
            <?php
                $rate = ($stats['total_billed'] ?? 0) > 0
                    ? round((($stats['total_collected'] ?? 0) / $stats['total_billed']) * 100, 1)
                    : 0;
            ?>
            <div class="mt-2">
                <div class="flex justify-between text-xs text-gray-400 mb-1">
                    <span>Collection rate</span>
                    <span class="font-medium text-gray-600"><?= $rate ?>%</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-1.5">
                    <div class="bg-green-500 h-1.5 rounded-full" style="width: <?= $rate ?>%;"></div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-5">
            <div class="text-sm font-medium text-gray-500 mb-1">Outstanding Balance</div>
            <div class="text-2xl font-semibold text-red-600">₱<?= number_format(($stats['total_billed'] ?? 0) - ($stats['total_collected'] ?? 0), 2) ?></div>
            <div class="mt-2 text-xs text-gray-400">
                <a href="<?= site_url('admin/reports') ?>" class="text-indigo-600 hover:underline">View full report →</a>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

        <a href="<?= site_url('admin/users') ?>"
           class="group rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 hover:shadow-md transition-shadow">
            <div class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <h3 class="font-semibold text-gray-900">User Management</h3>
            <p class="mt-1 text-sm text-gray-500">Add, edit or deactivate system users.</p>
            <span class="mt-3 inline-flex text-sm text-indigo-600 group-hover:translate-x-0.5 transition-transform">View users →</span>
        </a>

        <a href="<?= site_url('admin/schools') ?>"
           class="group rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 hover:shadow-md transition-shadow">
            <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <h3 class="font-semibold text-gray-900">Partner Schools</h3>
            <p class="mt-1 text-sm text-gray-500">Manage school partnerships and access.</p>
            <span class="mt-3 inline-flex text-sm text-indigo-600 group-hover:translate-x-0.5 transition-transform">View schools →</span>
        </a>

        <a href="<?= site_url('admin/billing') ?>"
           class="group rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 hover:shadow-md transition-shadow">
            <div class="w-9 h-9 rounded-lg bg-yellow-50 flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h3 class="font-semibold text-gray-900">Billing</h3>
            <p class="mt-1 text-sm text-gray-500">Review and receive school billing submissions.</p>
            <span class="mt-3 inline-flex text-sm text-indigo-600 group-hover:translate-x-0.5 transition-transform">View billing →</span>
        </a>

        <a href="<?= site_url('admin/reports') ?>"
           class="group rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 hover:shadow-md transition-shadow">
            <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <h3 class="font-semibold text-gray-900">Reports</h3>
            <p class="mt-1 text-sm text-gray-500">Financial summaries and scholar analytics.</p>
            <span class="mt-3 inline-flex text-sm text-indigo-600 group-hover:translate-x-0.5 transition-transform">View reports →</span>
        </a>

    </div>

    <!-- Bottom row: Pending billing submissions + Recent Activity -->
    <div class="grid gap-4 lg:grid-cols-2">

        <!-- Pending Billing Submissions -->
        <section class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
            <header class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
                <h2 class="font-semibold text-gray-800">Pending Billing Submissions</h2>
                <a href="<?= site_url('admin/billing') ?>" class="text-xs text-indigo-600 hover:text-indigo-700">View all →</a>
            </header>
            <div class="divide-y divide-gray-100">
                <?php if (empty($pendingBatches)): ?>
                    <div class="px-5 py-8 text-center text-sm text-gray-400">
                        No pending billing submissions.
                    </div>
                <?php else: ?>
                    <?php foreach (array_slice($pendingBatches, 0, 5) as $batch): ?>
                    <div class="px-5 py-3 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900"><?= esc($batch['school_name']) ?></p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                <?= esc($batch['semester']) ?> · <?= esc($batch['school_year']) ?> ·
                                ₱<?= number_format($batch['total_amount'], 2) ?>
                            </p>
                        </div>
                        <a href="<?= site_url('admin/billing/view/' . $batch['id']) ?>"
                           class="text-xs font-medium text-indigo-600 hover:text-indigo-800 whitespace-nowrap ml-4">
                            Review →
                        </a>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <!-- Recent Activity -->
        <section class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
            <header class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
                <h2 class="font-semibold text-gray-800">Recent System Activity</h2>
            </header>
            <div class="divide-y divide-gray-100">
                <?php if (empty($recentActivity)): ?>
                    <div class="px-5 py-8 text-center text-sm text-gray-400">No recent activity.</div>
                <?php else: ?>
                    <?php foreach (array_slice($recentActivity, 0, 5) as $activity): ?>
                    <div class="px-5 py-3 flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm text-gray-900"><?= esc($activity['description']) ?></p>
                            <p class="text-xs text-gray-500 mt-0.5"><?= esc($activity['detail'] ?? '') ?></p>
                        </div>
                        <span class="text-xs text-gray-400 whitespace-nowrap">
                            <?= esc($activity['time_ago'] ?? '') ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

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