<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header section -->
    <div class="md:flex md:items-center md:justify-between mb-6">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl">User Management</h2>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <a href="<?= site_url('admin/users/create'); ?>" 
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Create User
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('message')): ?>
        <div class="rounded-md bg-green-50 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800"><?= session()->getFlashdata('message'); ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

            <form method="GET" class="mb-6 flex flex-wrap gap-3 items-center">

    <!-- Search -->
    <input type="text"
           name="search"
           value="<?= esc($filters['search'] ?? '') ?>"
           placeholder="Search email or name..."
           class="border rounded-md px-3 py-2 text-sm shadow-sm">

    <!-- Role Dropdown -->
    <select name="role"
            class="border rounded-md px-3 py-2 text-sm shadow-sm">
        <option value="">All Roles</option>
        <option value="school_admin"
            <?= ($filters['role'] ?? '')=='school_admin'?'selected':'' ?>>
            SchoolAdmin
        </option>

        <option value="school_staff"
            <?= ($filters['role'] ?? '')=='school_staff'?'selected':'' ?>>
            SchoolStaff
        </option>
    </select>

    <!-- Sort Dropdown -->
    <select name="sort"
            class="border rounded-md px-3 py-2 text-sm shadow-sm">
        <option value="desc"
            <?= ($filters['sort'] ?? '')=='desc'?'selected':'' ?>>
            Newest
        </option>

        <option value="asc"
            <?= ($filters['sort'] ?? '')=='asc'?'selected':'' ?>>
            Oldest
        </option>
    </select>

    <button type="submit"
        class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm">
        Apply
    </button>
        <!-- Reset -->
    <a href="/admin/users"
        class="bg-gray-300 px-4 py-2 rounded text-sm">
        Reset
    </a>

</form>
    <!-- Table -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-500">
                <thead class="bg-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Full Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date Created</th>
                        <th scope="col" class="px-10 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-400">
                    <?php foreach ($users as $user): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= esc($user['email']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= esc($user['full_name']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                <?= esc($user['role']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                <?= $user['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                <?= esc($user['status']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
<td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
    
    <!-- Edit Icon Button -->
    <a href="<?= site_url('admin/users/edit/' . $user['id']); ?>"
       class="inline-flex items-center justify-center p-2 rounded-full text-indigo-600 hover:bg-indigo-100 hover:text-indigo-900 transition"
       title="Edit">
        <svg xmlns="http://www.w3.org/2000/svg" 
             class="h-5 w-5" 
             fill="none" 
             viewBox="0 0 24 24" 
             stroke="currentColor">
            <path stroke-linecap="round" 
                  stroke-linejoin="round" 
                  stroke-width="2" 
                  d="M11 5h2M12 7v10m-7 4h14a2 2 0 002-2V7a2 2 0 00-2-2h-3l-2-2H10L8 5H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
    </a>

    <!-- Delete Icon Button -->
    <a href="<?= site_url('admin/users/delete/' . $user['id']); ?>"
       onclick="return confirm('Are you sure you want to delete?')"
       class="inline-flex items-center justify-center p-2 rounded-full text-red-600 hover:bg-red-100 hover:text-red-900 transition ml-2"
       title="Delete">
        <svg xmlns="http://www.w3.org/2000/svg" 
             class="h-5 w-5" 
             fill="none" 
             viewBox="0 0 24 24" 
             stroke="currentColor">
            <path stroke-linecap="round" 
                  stroke-linejoin="round" 
                  stroke-width="2" 
                  d="M6 7h12M9 7V4h6v3m-7 4v6m4-6v6m5 2H7a2 2 0 01-2-2V7h14v12a2 2 0 01-2 2z" />
        </svg>
    </a>

</td>

                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>