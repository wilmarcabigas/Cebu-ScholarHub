<?= $this->extend('layouts/school_adminbase') ?>
<?= $this->section('content') ?>

<div class="space-y-6">
    <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">School Dashboard</h1>
            <p class="mt-1 text-sm text-gray-500">
                <?= esc(isset($school['name']) ? $school['name'] : '') ?> Scholar Management
            </p>
        </div>
        <div class="flex gap-3">
            <a href="<?= site_url('school/scholars/create') ?>" 
               class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Scholar
            </a>
        </div>
    </header>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <p class="text-sm text-gray-500">Active Scholars</p>
            <h3 class="mt-2 text-3xl font-bold text-gray-900" id="statActiveScholars">
                <?= esc($stats['active_scholars'] ?? 0) ?>
            </h3>
        </div>

        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <p class="text-sm text-gray-500">Pending Bills</p>
            <h3 class="mt-2 text-3xl font-bold text-gray-900" id="statPendingBills">
                <?= esc($stats['pending_bills'] ?? 0) ?>
            </h3>
        </div>

        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <p class="text-sm text-gray-500">Pending Approval</p>
            <h3 class="mt-2 text-3xl font-bold text-gray-900" id="statPendingApproval">
                <?= esc($stats['pending_approval'] ?? 0) ?>
            </h3>
        </div>

        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <p class="text-sm text-gray-500">Unread Messages</p>
            <h3 class="mt-2 text-3xl font-bold text-gray-900" id="statMessages">
                <?= esc($stats['messages'] ?? 0) ?>
            </h3>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Students by Course</h2>
                    <p class="text-sm text-gray-500">Live course distribution of scholars</p>
                </div>
                <span class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700">
                    Live Data
                </span>
            </div>

            <div class="relative mx-auto h-[320px] w-full max-w-[420px]">
                <canvas id="coursePieChart"></canvas>
            </div>

            <div id="courseChartEmpty" class="hidden mt-4 text-center text-sm text-gray-500">
                No course data available.
            </div>
        </div>

        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Students by Status</h2>
                    <p class="text-sm text-gray-500">Live scholar status distribution</p>
                </div>
                <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">
                    Auto Refresh
                </span>
            </div>

            <div class="relative mx-auto h-[320px] w-full max-w-[420px]">
                <canvas id="statusPieChart"></canvas>
            </div>

            <div id="statusChartEmpty" class="hidden mt-4 text-center text-sm text-gray-500">
                No status data available.
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
        <div class="mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Quick Overview</h2>
            <p class="text-sm text-gray-500">Dashboard shortcuts for school management</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-2">
            <a href="<?= site_url('messages') ?>"
               class="group rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 hover:shadow-md transition">
                <h3 class="font-semibold text-gray-900">Message</h3>
                <p class="mt-1 text-sm text-gray-500">Message partner schools</p>
                <span class="mt-3 inline-flex text-indigo-600 group-hover:translate-x-0.5 transition-transform">View messages →</span>
            </a>

            <a href="<?= site_url('scholars') ?>" 
               class="group rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 hover:shadow-md transition">
                <h3 class="font-semibold text-gray-900">School Scholars</h3>
                <p class="mt-1 text-sm text-gray-500">Manage your school's scholars.</p>
                <span class="mt-3 inline-flex text-indigo-600 group-hover:translate-x-0.5 transition-transform">View scholars →</span>
            </a>    

            <a href="<?= site_url('school/billing') ?>"
               class="group rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 hover:shadow-md transition sm:col-span-2">
                <h3 class="font-semibold text-gray-900">Billing Records</h3>
                <p class="mt-1 text-sm text-gray-500">Manage tuition and other fees.</p>
                <span class="mt-3 inline-flex text-indigo-600 group-hover:translate-x-0.5 transition-transform">Post billing →</span>
            </a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>