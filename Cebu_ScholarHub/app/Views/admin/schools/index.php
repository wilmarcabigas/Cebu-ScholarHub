<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>


<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Partner Schools</h2>
        <p class="text-sm text-gray-500">Manage school partnerships and access</p>
    </div>

    <a href="/admin/schools/create"
       class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm">
        + Add School
    </a>
</div>
<form method="get" class="flex flex-wrap gap-3 mb-4">

    <!-- Search Box -->
    <input type="text"
           name="search"
           value="<?= esc($search ?? '') ?>"
           placeholder="Search schools..."
           class="border rounded px-3 py-2 text-sm w-64">


<select name="order" class="border rounded px-3 py-2 text-sm">

    <option value="desc" <?= ($order=='desc')?'selected':'' ?>>
        Newest
    </option>

    <option value="asc" <?= ($order=='asc')?'selected':'' ?>>
        Oldest
    </option>

</select>


    <!-- Submit -->
    <button type="submit"
        class="bg-indigo-600 text-white px-4 py-2 rounded text-sm">
        Apply
    </button>

    <!-- Reset -->
    <a href="/admin/schools"
        class="bg-gray-300 px-4 py-2 rounded text-sm">
        Reset
    </a>

</form>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-500">
                <thead class="bg-gray-100">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">School Name</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Person</th>
                <th scope="col" class="px-10 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Created</th>
                <th scope="col" class="px-10 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>

            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-400">
            <?php foreach ($schools as $school): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-800"><?= esc($school['name']) ?></td>
                    <td class="px-6 py-4 text-sm text-gray-600"><?= esc($school['code']) ?></td>
                    <td class="px-6 py-4 text-sm text-gray-600"><?= esc($school['contact_person']) ?></td>
                    <td class="px-6 py-4 text-sm text-gray-600"><?= esc($school['contact_email']) ?></td>
                    <td class="px-6 py-4 text-sm text-gray-600">
    <?= isset($school['created_at']) 
        ? date('M d, Y', strtotime($school['created_at'])) 
        : '-' ?>
</td>

                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
    
    <!-- Edit Icon Button -->
    <a href="/admin/schools/edit/<?= $school['id'] ?>"
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
    <a href="/admin/schools/delete/<?= $school['id'] ?>"
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
