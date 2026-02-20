<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Edit Partner School</h2>
        <p class="text-sm text-gray-500">Update school information</p>
    </div>

    <form method="post" action="/manage/schools/update/<?= $school['id'] ?>"
          class="bg-white rounded-lg shadow p-6 space-y-5">
        <?= csrf_field() ?>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">School Name</label>
            <input type="text" name="name"
                   value="<?= esc($school['name']) ?>"
                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                   required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">School Code</label>
            <input type="text" name="code"
                   value="<?= esc($school['code']) ?>"
                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                   required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
            <textarea name="address" rows="3"
                      class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"><?= esc($school['address']) ?></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contact Person</label>
                <input type="text" name="contact_person"
                       value="<?= esc($school['contact_person']) ?>"
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contact Email</label>
                <input type="email" name="contact_email"
                       value="<?= esc($school['contact_email']) ?>"
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
            <input type="text" name="contact_number"
                   value="<?= esc($school['contact_number']) ?>"
                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <div class="flex justify-end space-x-3 pt-4">
            <a href="/manage/schools"
               class="px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">
                Update School
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
