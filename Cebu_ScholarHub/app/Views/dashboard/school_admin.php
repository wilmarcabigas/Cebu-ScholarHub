
<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div class="space-y-6">
    <!-- Header -->
    <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">School Dashboard</h1>
            <p class="mt-1 text-sm text-gray-500">
                <?= esc(data: isset($school['name']) ? $school['name'] : '') ?> Scholar Management
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

    <!-- Quick Stats -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-5">
            <div class="text-sm font-medium text-gray-500">Active Scholars</div>
            <div class="mt-1 text-3xl font-semibold text-gray-900">156</div>
            <div class="mt-3 text-xs text-green-600">↑ 5 new this semester</div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-5">
            <div class="text-sm font-medium text-gray-500">Total Billing</div>
            <div class="mt-1 text-3xl font-semibold text-gray-900">₱780K</div>
            <div class="mt-3 text-xs text-gray-500">For current semester</div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-5">
            <div class="text-sm font-medium text-gray-500">Pending Approval</div>
            <div class="mt-1 text-3xl font-semibold text-gray-900">12</div>
            <div class="mt-3 text-xs text-red-600">Need attention</div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-5">
            <div class="text-sm font-medium text-gray-500">Messages</div>
            <div class="mt-1 text-3xl font-semibold text-gray-900">8</div>
            <div class="mt-3 text-xs text-indigo-600">3 unread</div>
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


        <a href="<?= site_url('scholars') ?>" 
            class="group rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 hover:shadow-md transition">
             <h3 class="font-semibold text-gray-900">School Scholars</h3>
            <p class="mt-1 text-sm text-gray-500">Manage your school's scholars.</p>
            <span class="mt-3 inline-flex text-indigo-600 group-hover:translate-x-0.5 transition-transform">View scholars →</span>
        </a>    

        <a href="<?= site_url('school/billing') ?>"
           class="group rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 hover:shadow-md transition">
            <h3 class="font-semibold text-gray-900">Billing Records</h3>
            <p class="mt-1 text-sm text-gray-500">Manage tuition and other fees.</p>
            <span class="mt-3 inline-flex text-indigo-600 group-hover:translate-x-0.5 transition-transform">Post billing →</span>
        </a>

        <a href="<?= site_url('school/reports') ?>"
           class="group rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 hover:shadow-md transition">
            <h3 class="font-semibold text-gray-900">School Reports</h3>
            <p class="mt-1 text-sm text-gray-500">Generate reports for your school.</p>
            <span class="mt-3 inline-flex text-indigo-600 group-hover:translate-x-0.5 transition-transform">View reports →</span>
        </a>
    </div>

    <!-- Recent Updates -->
    <section class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
        <header class="px-5 py-4 border-b border-gray-200">
            <h2 class="font-semibold text-gray-800">Recent Updates</h2>
        </header>
        <div class="divide-y divide-gray-200">
            <?php foreach(range(1, 5) as $i): ?>
            <div class="px-5 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-900">Billing posted for <span class="font-medium">2nd Semester 2023</span></p>
                        <p class="text-xs text-gray-500 mt-0.5">₱45,000 for 15 scholars</p>
                    </div>
                    <span class="text-xs text-gray-500">1 hour ago</span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<?= $this->endSection() ?>