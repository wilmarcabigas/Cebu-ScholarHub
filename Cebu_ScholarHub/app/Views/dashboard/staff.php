<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div class="space-y-6">

    <!-- Header -->
    <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Staff Dashboard</h1>
            <p class="mt-1 text-sm text-gray-500">Scholar Office Staff Overview</p>
        </div>
        <div class="flex gap-3">
            <a href="<?= site_url('admin/billing') ?>"
               class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Billing
            </a>
            <a href="<?= site_url('scholars/create') ?>"
               class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Scholar
            </a>
        </div>
    </header>

    <!-- Quick Stats -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-5">
            <div class="flex items-center justify-between mb-1">
                <div class="text-sm font-medium text-gray-500">Pending Reviews</div>
                <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-semibold text-gray-900"><?= esc($stats['pending_reviews']) ?></div>
            <?php if (($stats['pending_reviews'] ?? 0) > 0): ?>
                <div class="mt-2 text-xs text-red-600 font-medium">Needs attention</div>
            <?php else: ?>
                <div class="mt-2 text-xs text-gray-400">All clear</div>
            <?php endif; ?>
        </div>

        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-5">
            <div class="flex items-center justify-between mb-1">
                <div class="text-sm font-medium text-gray-500">New Applications</div>
                <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-semibold text-gray-900"><?= esc($stats['new_applications']) ?></div>
            <div class="mt-2 text-xs text-gray-400">This week</div>
        </div>

        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-5">
            <div class="flex items-center justify-between mb-1">
                <div class="text-sm font-medium text-gray-500">Billing Submissions</div>
                <div class="w-8 h-8 rounded-lg bg-yellow-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-semibold text-gray-900"><?= esc($stats['school_updates'] ?? 0) ?></div>
            <div class="mt-2 text-xs text-indigo-600">
                <a href="<?= site_url('admin/billing') ?>" class="hover:underline">View submissions →</a>
            </div>
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
            <div class="mt-2 text-xs text-indigo-600">
                <a href="<?= site_url('messages') ?>" class="hover:underline">Open inbox →</a>
            </div>
        </div>

    </div>

    <!-- Quick Actions -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

        <a href="<?= site_url('scholars') ?>"
           class="group rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 hover:shadow-md transition-shadow">
            <div class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <h3 class="font-semibold text-gray-900">Scholar Management</h3>
            <p class="mt-1 text-sm text-gray-500">Manage all scholar records.</p>
            <span class="mt-3 inline-flex text-sm text-indigo-600 group-hover:translate-x-0.5 transition-transform">View scholars →</span>
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

        <a href="<?= site_url('messages') ?>"
           class="group rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 hover:shadow-md transition-shadow">
            <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
            </div>
            <h3 class="font-semibold text-gray-900">Messages</h3>
            <p class="mt-1 text-sm text-gray-500">Message partner schools and staff.</p>
            <span class="mt-3 inline-flex text-sm text-indigo-600 group-hover:translate-x-0.5 transition-transform">Open inbox →</span>
        </a>

    </div>

    <!-- Pending Billing Submissions + Recent Messages -->
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
                                ₱<?= number_format($batch['total_amount'] ?? 0, 2) ?>
                            </p>
                        </div>
                        <a href="<?= site_url('admin/billing/view/' . $batch['id']) ?>"
                           class="text-xs font-medium text-indigo-600 hover:text-indigo-800 ml-4 whitespace-nowrap">
                            Review →
                        </a>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <!-- Recent Messages -->
        <section class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
            <header class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
                <h2 class="font-semibold text-gray-800">Recent Messages</h2>
                <a href="<?= site_url('messages') ?>" class="text-xs text-indigo-600 hover:text-indigo-700">View all →</a>
            </header>
            <div class="divide-y divide-gray-100">
                <?php if (empty($recentMessages)): ?>
                    <div class="px-5 py-8 text-center text-sm text-gray-400">No recent messages.</div>
                <?php else: ?>
                    <?php foreach (array_slice($recentMessages, 0, 5) as $msg): ?>
                    <div class="px-5 py-3 flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center text-xs font-semibold text-indigo-700 flex-shrink-0">
                            <?= strtoupper(substr($msg['sender_name'] ?? 'UN', 0, 2)) ?>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-900 truncate"><?= esc($msg['sender_name'] ?? 'Unknown') ?></p>
                            <p class="text-xs text-gray-500 truncate"><?= esc($msg['body'] ?? '') ?></p>
                        </div>
                        <span class="text-xs text-gray-400 whitespace-nowrap"><?= esc($msg['time_ago'] ?? '') ?></span>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

    </div>

</div>

<?= $this->endSection() ?>