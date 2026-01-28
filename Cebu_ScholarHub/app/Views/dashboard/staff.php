
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
            <div class="mt-1 text-3xl font-semibold text-gray-900">23</div>
            <div class="mt-3 text-xs text-red-600">Needs attention</div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-5">
            <div class="text-sm font-medium text-gray-500">New Applications</div>
            <div class="mt-1 text-3xl font-semibold text-gray-900">45</div>
            <div class="mt-3 text-xs text-gray-500">This week</div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-5">
            <div class="text-sm font-medium text-gray-500">School Updates</div>
            <div class="mt-1 text-3xl font-semibold text-gray-900">15</div>
            <div class="mt-3 text-xs text-indigo-600">From 5 schools</div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-5">
            <div class="text-sm font-medium text-gray-500">Messages</div>
            <div class="mt-1 text-3xl font-semibold text-gray-900">18</div>
            <div class="mt-3 text-xs text-indigo-600">6 unread</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
       
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
    </div>

    <!-- Tasks & Actions -->
    <div class="grid gap-4 lg:grid-cols-2">
        <!-- Pending Tasks -->
        <section class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200">
            <header class="px-5 py-4 border-b border-gray-200">
                <h2 class="font-semibold text-gray-800">Pending Tasks</h2>
            </header>
            <div class="divide-y divide-gray-200">
                <?php foreach(range(1, 5) as $i): ?>
                <div class="px-5 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-900">Review scholar application</p>
                            <p class="text-xs text-gray-500 mt-0.5">From USC - BS Computer Science</p>
                        </div>
                        <button class="text-sm text-indigo-600 hover:text-indigo-700">Review</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Recent Messages -->
        <section class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200">
            <header class="px-5 py-4 border-b border-gray-200">
                <h2 class="font-semibold text-gray-800">Recent Messages</h2>
            </header>
            <div class="divide-y divide-gray-200">
                <?php foreach(range(1, 5) as $i): ?>
                <div class="px-5 py-4">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-sm font-medium text-gray-600">
                            USC
                        </div>
                        <div>
                            <p class="text-sm text-gray-900">Billing confirmation needed</p>
                            <p class="text-xs text-gray-500 mt-0.5">From USC Admin • 2 hours ago</p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</div>

<?= $this->endSection() ?>