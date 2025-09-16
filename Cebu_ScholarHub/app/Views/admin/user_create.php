<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const roleSelect = document.getElementById('role');
        const schoolField = document.getElementById('schoolField');

        function toggleSchoolField() {
            const role = roleSelect.value;
            if (role === 'school_admin' || role === 'school_staff') {
                schoolField.style.display = 'block';
            } else {
                schoolField.style.display = 'none';
            }
        }

        // Initial check
        toggleSchoolField();

        // Listen for changes
        roleSelect.addEventListener('change', toggleSchoolField);
    });
</script>

<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Create New User</h3>
                <p class="mt-1 text-sm text-gray-600">
                    Add a new user to the system with their basic information and role.
                </p>
            </div>
        </div> 

        <div class="mt-5 md:mt-0 md:col-span-2">
            <form action="<?= site_url('admin/users/create'); ?>" method="POST">
    <?= csrf_field(); ?>
    <div class="shadow overflow-hidden sm:rounded-md">
        <div class="px-4 py-5 bg-white sm:p-6">
            <div class="grid grid-cols-6 gap-6">
                <!-- Email field -->
                <div class="col-span-6 sm:col-span-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" required
                        value="<?= old('email') ?>"
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>

                <!-- Password field -->
                <div class="col-span-6 sm:col-span-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" id="password" required
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>

                <!-- Full Name field -->
                <div class="col-span-6 sm:col-span-4">
                    <label for="full_name" class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input type="text" name="full_name" id="full_name" required
                        value="<?= old('full_name') ?>"
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>

                <!-- Role field -->
                <div class="col-span-6 sm:col-span-3">
                    <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                    <select id="role" name="role" required
                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">Select Role</option>
                        <option value="admin">Admin</option>
                        <option value="staff">Staff</option>
                        <option value="school_admin">School Admin</option>
                        <option value="school_staff">School Staff</option>
                        <option value="scholar">Scholar</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
            <button type="submit"
                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Create User
            </button>
        </div>
    </div>
</form>

            <?php if (session()->has('error')): ?>
    <div class="mb-4 rounded-md bg-red-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800"><?= session('error') ?></p>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (session()->has('errors')): ?>
    <div class="mb-4 rounded-md bg-red-50 p-4">
        <div class="flex">
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Please correct the following errors:</h3>
                <ul class="mt-2 text-sm text-red-700">
                    <?php foreach (session('errors') as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
<?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
