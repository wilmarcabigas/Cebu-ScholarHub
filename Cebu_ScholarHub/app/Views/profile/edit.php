<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div class="max-w-2xl mx-auto space-y-6">

    <!-- Header -->
    <header>
        <h1 class="text-2xl font-bold tracking-tight">Edit Profile</h1>
        <p class="mt-1 text-sm text-gray-500">Update your name, email, and password.</p>
    </header>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
            <?= esc(session()->getFlashdata('success')) ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
            <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <form action="<?= site_url('profile/update') ?>" method="post" class="space-y-6">
        <?= csrf_field() ?>

        <!-- Profile Info -->
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-6 space-y-5">
            <h2 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3">Account Information</h2>

            <div>
                <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                <input
                    type="text"
                    id="full_name"
                    name="full_name"
                    value="<?= esc(old('full_name', $user['full_name'])) ?>"
                    required
                    class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                />
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?= esc(old('email', $user['email'])) ?>"
                    required
                    class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <div class="block w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-500">
                    <?= esc(ucfirst(str_replace('_', ' ', $user['role']))) ?>
                </div>
            </div>
        </div>

        <!-- Change Password -->
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-6 space-y-5">
            <div class="border-b border-gray-100 pb-3">
                <h2 class="text-base font-semibold text-gray-900">Change Password</h2>
                <p class="text-xs text-gray-500 mt-0.5">Leave all fields blank to keep your current password.</p>
            </div>

            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                <input
                    type="password"
                    id="current_password"
                    name="current_password"
                    autocomplete="current-password"
                    class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                />
            </div>

            <div>
                <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                <input
                    type="password"
                    id="new_password"
                    name="new_password"
                    autocomplete="new-password"
                    class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                />
                <p class="mt-1 text-xs text-gray-400">Min 8 characters with uppercase, lowercase, number, and special character.</p>
            </div>

            <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    autocomplete="new-password"
                    class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                />
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-3">
            <a href="<?= site_url('dashboard') ?>"
               class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit"
                    class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                Save Changes
            </button>
        </div>

    </form>

</div>

<?= $this->endSection() ?>
