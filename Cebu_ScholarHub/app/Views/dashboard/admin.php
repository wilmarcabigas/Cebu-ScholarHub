
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
            <div class="text-sm font-medium text-gray-500">Total Scholars</div>
            <!--<div class="mt-1 text-3xl font-semibold text-gray-900">2,451</div>
            <div class="mt-3 text-xs text-green-600">↑ 12% from last month</div>-->
        </div>
        
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-5">
            <div class="text-sm font-medium text-gray-500">Active Schools</div>
        <!--    <div class="mt-1 text-3xl font-semibold text-gray-900">15</div>
            <div class="mt-3 text-xs text-gray-500">No change</div> -->
        </div>
        
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-5">
            <div class="text-sm font-medium text-gray-500">Pending Bills</div>
        <!--    <div class="mt-1 text-3xl font-semibold text-gray-900">₱2.4M</div>
            <div class="mt-3 text-xs text-red-600">↑ 8% this semester</div> -->
        </div>
        
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-5">
            <div class="text-sm font-medium text-gray-500">Messages</div>
        <!--    <div class="mt-1 text-3xl font-semibold text-gray-900">24</div>
            <div class="mt-3 text-xs text-indigo-600">12 unread</div> -->
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <a href="<?= site_url('admin/users') ?>" 
           class="group rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 hover:shadow-md transition">
            <h3 class="font-semibold text-gray-900">User Management</h3>
            <p class="mt-1 text-sm text-gray-500">Add, edit or deactivate system users.</p>
            <span class="mt-3 inline-flex text-indigo-600 group-hover:translate-x-0.5 transition-transform">View users →</span>
        </a>

    <a href="/admin/schools"
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
        <!--  <a href="<?= site_url('admin/reports') ?>" 
           class="group rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 hover:shadow-md transition">
            <h3 class="font-semibold text-gray-900">Reports</h3>
            <p class="mt-1 text-sm text-gray-500">Generate system-wide reports and analytics.</p>
            <span class="mt-3 inline-flex text-indigo-600 group-hover:translate-x-0.5 transition-transform">View reports →</span>
        </a> -->
    </div>

    <!-- Recent Activity -->
    <section class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
        <header class="px-5 py-4 border-b border-gray-200">
            <h2 class="font-semibold text-gray-800">Recent System Activity</h2>
        </header>
        <div class="divide-y divide-gray-200">
            <?php foreach(range(1, 5) as $i): ?>
            <div class="px-5 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-900">New scholar added by <span class="font-medium">USC Admin</span></p>
                        <p class="text-xs text-gray-500 mt-0.5">Juan Dela Cruz (BSCS) enrolled for AY 2023-2024</p>
                    </div>
                    <span class="text-xs text-gray-500">2 hours ago</span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <footer class="px-5 py-4 border-t border-gray-200">
            <a href="<?= site_url('admin/activity') ?>" 
               class="text-sm text-indigo-600 hover:text-indigo-700">View all activity →</a>
        </footer>
    </section>
</div>

<?= $this->endSection() ?>