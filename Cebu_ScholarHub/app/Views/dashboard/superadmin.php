<?= $this->extend('layouts/sa_base') ?>
<?= $this->section('content') ?>

<div class="space-y-8">

<!-- ================= HEADER ================= -->

<header class="flex flex-col sm:flex-row bg-green sm:items-center sm:justify-between gap-4 ">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Super Administrator Dashboard</h1>
        <p class="text-sm text-gray-500">Cebu ScholarHub System Overview</p>
    </div>

    <a href="<?= site_url('superadmin/users/create') ?>"
       class="flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg shadow hover:bg-green-700 transition">
        +
        Add User
    </a>
</header>


<!-- ================= STATISTICS ================= -->
<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">

    <!-- Total Scholars -->
    <div class="flex items-center justify-between bg-green-600 text-white p-5 rounded-lg shadow">
        <div>
            <p class="text-sm opacity-80">Total Scholars</p>
            <p class="text-3xl font-bold"><?= esc($stats['total_scholars']) ?></p>
        </div>
        <div class="text-4xl opacity-80">🎓</div>
    </div>

    <!-- Active Schools -->
    <div class="flex items-center justify-between bg-blue-600 text-white p-5 rounded-lg shadow">
        <div>
            <p class="text-sm opacity-80">Active Schools</p>
            <p class="text-3xl font-bold"><?= esc($stats['active_schools']) ?></p>
        </div>
        <div class="text-4xl opacity-80">🏫</div>
    </div>

    <!-- Total Users -->
    <div class="flex items-center justify-between bg-yellow-500 text-white p-5 rounded-lg shadow">
        <div>
            <p class="text-sm opacity-80">Total Users</p>
            <p class="text-3xl font-bold"><?= esc($stats['pending_bills']) ?></p>
        </div>
        <div class="text-4xl opacity-80">👤</div>
    </div>

</div>


<!-- ================= CHARTS ================= -->
<div class="grid gap-6 lg:grid-cols-2 mt-6">

    <!-- BAR CHART -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            System Overview (Bar Graph)
        </h3>
        <canvas id="barChart"></canvas>
    </div>

    <!-- PIE CHART -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            System Distribution (Pie Chart)
        </h3>
        <canvas id="pieChart"></canvas>
    </div>

</div>


<!-- ================= MANAGEMENT CARDS ================= -->

<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">

<a href="<?= site_url('superadmin/users') ?>"
   class="bg-white border rounded-lg shadow hover:shadow-md transition p-6">
    <h3 class="text-lg font-semibold text-gray-800">User Management</h3>
    <p class="text-sm text-gray-500 mt-1">
        Add, edit or deactivate system users.
    </p>
    <div class="mt-4 text-green-600 font-medium">
        Manage Users →
    </div>
</a>

<a href="<?= site_url('admin/schools') ?>"
   class="bg-white border rounded-lg shadow hover:shadow-md transition p-6">
    <h3 class="text-lg font-semibold text-gray-800">Partner Schools</h3>
    <p class="text-sm text-gray-500 mt-1">
        Manage school partnerships and access.
    </p>
    <div class="mt-4 text-green-600 font-medium">
        Manage Schools →
    </div>
</a>

</div>


<!-- ================= RECENT ACTIVITY ================= -->

<section class="bg-white rounded-lg shadow">

<header class="border-b px-6 py-4">
<h2 class="font-semibold text-gray-800">Recent System Activity</h2>
</header>

<div class="divide-y">

<?php if (!empty($activities)): ?>
    <?php foreach ($activities as $activity): ?>

    <div class="flex justify-between items-center px-6 py-4">

        <div>
            <p class="text-sm text-gray-800">
                <?= esc($activity['message']) ?>
                <span class="font-semibold text-green-700">
                    <?= esc($activity['name']) ?>
                </span>
            </p>

            <p class="text-xs text-gray-500">
                <?= $activity['type'] === 'user' ? 'User account created' : 'School registered' ?>
            </p>
        </div>

        <span class="text-xs text-gray-400">
            <?= date('M d, Y h:i A', strtotime($activity['time'])) ?>
        </span>

    </div>

    <?php endforeach; ?>
<?php else: ?>

    <div class="px-6 py-4 text-sm text-gray-500">
        No recent activity found.
    </div>

<?php endif; ?>

</div>

</section>

</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

const scholars = <?= esc($stats['total_scholars']) ?>;
const schools = <?= esc($stats['active_schools']) ?>;
const users = <?= esc($stats['pending_bills']) ?>;


/* ================= BAR GRAPH ================= */

new Chart(document.getElementById('barChart'), {
    type: 'bar',
    data: {
        labels: ['Scholars', 'Schools', 'Users'],
        datasets: [{
            label: 'Total Count',
            data: [scholars, schools, users],
            backgroundColor: [
                '#16a34a',
                '#2563eb',
                '#f59e0b'
            ],
            borderRadius: 5
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        }
    }
});


/* ================= PIE CHART ================= */

new Chart(document.getElementById('pieChart'), {
    type: 'pie',
    data: {
        labels: ['Scholars', 'Schools', 'Users'],
        datasets: [{
            data: [scholars, schools, users],
            backgroundColor: [
                '#16a34a',
                '#2563eb',
                '#f59e0b'
            ]
        }]
    },
    options: {
        responsive: true
    }
});

</script>
<?= $this->endSection() ?>