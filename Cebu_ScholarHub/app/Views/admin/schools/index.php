<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Partner Schools</h2>
        <p class="text-sm text-gray-500">Manage school partnerships and access</p>
    </div>

    <a href="/manage/schools/create"
       class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm">
        + Add School
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">School Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact Person</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
            <?php foreach ($schools as $school): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-800"><?= esc($school['name']) ?></td>
                    <td class="px-6 py-4 text-sm text-gray-600"><?= esc($school['code']) ?></td>
                    <td class="px-6 py-4 text-sm text-gray-600"><?= esc($school['contact_person']) ?></td>
                    <td class="px-6 py-4 text-sm text-gray-600"><?= esc($school['contact_email']) ?></td>
                    <td class="px-6 py-4 text-right text-sm">
                        <a href="/manage/schools/edit/<?= $school['id'] ?>"
                           class="text-indigo-600 hover:underline mr-3">
                            Edit
                        </a>
                        <a href="/manage/schools/delete/<?= $school['id'] ?>"
                           onclick="return confirm('Are you sure?')"
                           class="text-red-600 hover:underline">
                            Delete
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php if (empty($schools)): ?>
                <tr>
                    <td colspan="5" class="px-6 py-6 text-center text-sm text-gray-500">
                        No schools found.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>
