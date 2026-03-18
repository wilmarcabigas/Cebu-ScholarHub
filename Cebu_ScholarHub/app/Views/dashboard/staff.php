<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div class="space-y-6">
    <!-- Header -->
    <header>
        <h1 class="text-2xl font-bold tracking-tight">Staff Dashboard</h1>
        <p class="mt-1 text-sm text-gray-500">Scholar Office Staff Overview</p>
    </header>

    <!-- Quick Stats -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-5">
            <div class="text-sm font-medium text-gray-500">Pending Reviews</div>
            <div class="mt-1 text-3xl font-semibold text-gray-900"><?= esc($stats['pending_reviews']) ?></div>
            <div class="mt-3 text-xs text-red-600">Needs attention</div>    
        </div>
        
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-5">
            <div class="text-sm font-medium text-gray-500">New Applications</div>
            <div class="mt-1 text-3xl font-semibold text-gray-900"><?= esc($stats['new_applications']) ?></div>
            <div class="mt-3 text-xs text-gray-500">This week</div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-5">
            <div class="text-sm font-medium text-gray-500">School Updates</div>
            <div class="mt-1 text-3xl font-semibold text-gray-900"><?= esc($stats['school_updates']) ?></div>
            <div class="mt-3 text-xs text-indigo-600">From 5 schools</div> 
        </div>
        
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-5">
            <div class="text-sm font-medium text-gray-500">Messages</div>
            <div class="mt-1 text-3xl font-semibold text-gray-900"><?= esc($stats['messages']) ?></div>
            <div class="mt-3 text-xs text-indigo-600">Unread messages</div> 
        </div>
    </div>

    <!-- Charts -->
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-5">
            <div class="mb-4">
                <h3 class="text-base font-semibold text-gray-900">Scholars per School</h3>
                <p class="text-sm text-gray-500">Distribution by partner school</p>
            </div>
            <div class="relative h-72">
                <canvas id="staffSchoolChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-5">
            <div class="mb-4">
                <h3 class="text-base font-semibold text-gray-900">Course Distribution</h3>
                <p class="text-sm text-gray-500">Scholars grouped by course</p>
            </div>
            <div class="relative h-72">
                <canvas id="staffCourseChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-5">
            <div class="mb-4">
                <h3 class="text-base font-semibold text-gray-900">Scholar Status</h3>
                <p class="text-sm text-gray-500">Current scholar status overview</p>
            </div>
            <div class="relative h-72">
                <canvas id="staffStatusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <a href="<?= site_url('messages') ?>"
           class="group rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 hover:shadow-md transition">
            <h3 class="font-semibold text-gray-900">message</h3>
            <p class="mt-1 text-sm text-gray-500">Message partner schools </p>
            <span class="mt-3 inline-flex text-indigo-600 group-hover:translate-x-0.5 transition-transform">View messages →</span>
        </a>

        <a href="<?= site_url('admin/schools') ?>" 
           class="group rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 hover:shadow-md transition">
            <h3 class="font-semibold text-gray-900">Partner Schools</h3>
            <p class="mt-1 text-sm text-gray-500">Manage school partnerships and access.</p>
            <span class="mt-3 inline-flex text-indigo-600 group-hover:translate-x-0.5 transition-transform">View schools →</span>
        </a>

        <a href="<?= site_url('scholars') ?>" 
           class="group rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 hover:shadow-md transition">
            <h3 class="font-semibold text-gray-900">Scholar Management</h3>
            <p class="mt-1 text-sm text-gray-500">Manage all scholar records.</p>
            <span class="mt-3 inline-flex text-indigo-600 group-hover:translate-x-0.5 transition-transform">View scholars →</span>
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined') {
        console.error('Chart.js is not loaded.');
        return;
    }

    const schoolLabels = <?= json_encode($school_chart_labels ?? []) ?>;
    const schoolTotals = <?= json_encode($school_chart_totals ?? []) ?>;

    const courseLabels = <?= json_encode($course_chart_labels ?? []) ?>;
    const courseTotals = <?= json_encode($course_chart_totals ?? []) ?>;

    const statusLabels = <?= json_encode($status_chart_labels ?? []) ?>;
    const statusTotals = <?= json_encode($status_chart_totals ?? []) ?>;

    const chartColors = [
        '#4F46E5', '#06B6D4', '#10B981', '#F59E0B', '#EF4444',
        '#8B5CF6', '#EC4899', '#14B8A6', '#F97316', '#84CC16',
        '#6366F1', '#0EA5E9', '#22C55E', '#EAB308', '#F43F5E'
    ];

    function createPieChart(canvasId, labels, totals) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;

        new Chart(canvas, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: totals,
                    backgroundColor: chartColors,
                    borderColor: '#ffffff',
                    borderWidth: 2,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 10,
                            padding: 16
                        }
                    }
                }
            }
        });
    }

    createPieChart('staffSchoolChart', schoolLabels, schoolTotals);
    createPieChart('staffCourseChart', courseLabels, courseTotals);
    createPieChart('staffStatusChart', statusLabels, statusTotals);
});
</script>

<?= $this->endSection() ?>