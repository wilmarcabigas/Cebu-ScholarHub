
<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div class="space-y-6">
    <!-- Header -->
    <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">School Staff Dashboard</h1>
            <p class="mt-1 text-sm text-gray-500">
                <?= esc($school['name']) ?> • Staff Portal
            </p>
        </div>
    </header>

    <!-- Quick Stats -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-5">
            <div class="text-sm font-medium text-gray-500">Active Scholars</div>
            <div class="mt-1 text-3xl font-semibold text-gray-900">78</div>
            <div class="mt-3 text-xs text-gray-500">Current semester</div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-5">
            <div class="text-sm font-medium text-gray-500">Pending Bills</div>
            <div class="mt-1 text-3xl font-semibold text-gray-900">12</div>
            <div class="mt-3 text-xs text-yellow-600">Awaiting approval</div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-5">
            <div class="text-sm font-medium text-gray-500">Requirements Due</div>
            <div class="mt-1 text-3xl font-semibold text-gray-900">5</div>
            <div class="mt-3 text-xs text-red-600">Need attention</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <a href="<?= site_url('school/scholars') ?>" 
           class="group rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 hover:shadow-md transition">
            <h3 class="font-semibold text-gray-900">Scholar Records</h3>
            <p class="mt-1 text-sm text-gray-500">View and update scholar information.</p>
            <span class="mt-3 inline-flex text-indigo-600 group-hover:translate-x-0.5 transition-transform">View scholars →</span>
        </a>

        <a href="<?= site_url('school/billing') ?>"
           class="group rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 hover:shadow-md transition">
            <h3 class="font-semibold text-gray-900">Submit Billing</h3>
            <p class="mt-1 text-sm text-gray-500">Create billing requests for review.</p>
            <span class="mt-3 inline-flex text-indigo-600 group-hover:translate-x-0.5 transition-transform">Submit billing →</span>
        </a>

        <a href="<?= site_url('school/requirements') ?>"
           class="group rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 hover:shadow-md transition">
            <h3 class="font-semibold text-gray-900">Requirements</h3>
            <p class="mt-1 text-sm text-gray-500">Track scholar requirements status.</p>
            <span class="mt-3 inline-flex text-indigo-600 group-hover:translate-x-0.5 transition-transform">Check status →</span>
        </a>
    </div>

    <!-- Tasks & Notifications -->
    <div class="grid gap-4 lg:grid-cols-2">
        <!-- Pending Tasks -->
        <section class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200">
            <header class="px-5 py-4 border-b border-gray-200">
                <h2 class="font-semibold text-gray-800">Pending Tasks</h2>
            </header>
            <div class="divide-y divide-gray-200">
                <?php foreach(range(1, 4) as $i): ?>
                <div class="px-5 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-900">Update scholar grades</p>
                            <p class="text-xs text-gray-500 mt-0.5">For Juan Dela Cruz</p>
                        </div>
                        <span class="text-xs text-red-600">Due today</span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Recent Updates -->
        <section class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200">
            <header class="px-5 py-4 border-b border-gray-200">
                <h2 class="font-semibold text-gray-800">Recent Updates</h2>
            </header>
            <div class="divide-y divide-gray-200">
                <?php foreach(range(1, 4) as $i): ?>
                <div class="px-5 py-4">
                    <div class="flex items-center gap-4">
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-900">Billing approved for Maria Santos</p>
                            <p class="text-xs text-gray-500 mt-0.5">2 hours ago</p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <footer class="px-5 py-4 border-t border-gray-200">
                <a href="<?= site_url('school/updates') ?>" 
                   class="text-sm text-indigo-600 hover:text-indigo-700">View all updates →</a>
            </footer>
        </section>
    </div>
</div>

<?= $this->endSection() ?>