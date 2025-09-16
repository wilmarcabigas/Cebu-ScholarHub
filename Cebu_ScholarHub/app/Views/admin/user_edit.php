<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Edit User</h3>
                <p class="mt-1 text-sm text-gray-600">
                    Update user information and permissions.
                </p>
            </div>
        </div>

        <div class="mt-5 md:mt-0 md:col-span-2">
            <form action="<?= site_url('admin/users/edit/' . $user['id']); ?>" method="POST">
                <?= csrf_field(); ?>
                <div class="shadow overflow-hidden sm:rounded-md">
                    <div class="px-4 py-5 bg-white sm:p-6">
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-4">
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" name="email" id="email" value="<?= old('email', $user['email']); ?>"
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>

                            <div class="col-span-6 sm:col-span-4">
                                <label for="full_name" class="block text-sm font-medium text-gray-700">Full Name</label>
                                <input type="text" name="full_name" id="full_name" value="<?= old('full_name', $user['full_name']); ?>"
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                                <select id="role" name="role"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                    <option value="staff" <?= $user['role'] == 'staff' ? 'selected' : ''; ?>>Staff</option>
                                    <option value="school_admin" <?= $user['role'] == 'school_admin' ? 'selected' : ''; ?>>School Admin</option>
                                    <option value="school_staff" <?= $user['role'] == 'school_staff' ? 'selected' : ''; ?>>School Staff</option>
                                    <option value="scholar" <?= $user['role'] == 'scholar' ? 'selected' : ''; ?>>Scholar</option>
                                </select>
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select id="status" name="status"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="active" <?= $user['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?= $user['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                        <button type="submit"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Update User
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>