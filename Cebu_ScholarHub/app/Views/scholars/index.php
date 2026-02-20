<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<?php
  $role = $user['role'] ?? '';
?>

<div class="space-y-6">
  <header class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
    <div>
      <h1 class="text-2xl font-semibold tracking-tight">
        Welcome, <?= esc($user['full_name']) ?>!
      </h1>
      <p class="text-sm text-gray-500 mt-1">
        You are logged in as <span class="font-medium"><?= esc($role) ?></span>.
      </p>
    </div>
  </header>

  <?php if (in_array($role, ['staff', 'admin'])): ?>
    <div class="mb-4 flex flex-col sm:flex-row sm:items-end justify-between max-w-[595px] gap-4">
      
      <form method="get" action="<?= site_url('scholars') ?>" class="flex-1 relative">
        <label class="block text-sm font-medium text-gray-700 mb-1">
          Filter by School
        </label>

        <button type="button" id="dropdownButton"
                class="w-full bg-white border border-gray-300 rounded-lg shadow-sm px-4 py-2 text-left flex items-center justify-between focus:outline-none focus:ring-2 focus:ring-indigo-500">
          <span id="dropdownLabel">
            <?php
              $selectedSchoolName = 'All Schools';
              foreach ($schools as $school) {
                if ($school['id'] == $selectedSchool) {
                  $selectedSchoolName = $school['name'];
                  break;
                }
              }
              echo esc($selectedSchoolName);
            ?>
          </span>
          <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </button>

        <ul id="dropdownMenu" class="absolute z-10 hidden mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-auto">
          <li class="px-4 py-2 hover:bg-indigo-100 cursor-pointer" data-value="">
            All Schools
          </li>
          <?php foreach ($schools as $school): ?>
            <li class="px-4 py-2 hover:bg-indigo-100 cursor-pointer" data-value="<?= $school['id'] ?>">
              <?= esc($school['name']) ?>
            </li>
          <?php endforeach; ?>
        </ul>

        <input type="hidden" name="school_id" id="schoolInput" value="<?= esc($selectedSchool) ?>">
      </form>

      <?php if (in_array($role, ['staff','school_admin','school_staff'])): ?>
        <a href="<?= site_url('scholars/create') ?>" 
           class="group rounded-xl bg-blue-600 ring-1 ring-blue-400 px-4 py-2 hover:shadow-lg transition whitespace-nowrap">
          <div class="flex items-center gap-2">
            <span class="text-xs font-semibold text-white">Add New Scholar</span>
            <span class="text-white group-hover:translate-x-0.5 transition">→</span>
          </div>
        </a>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <section class="mt-6">
    <h2 class="text-xl font-semibold">Scholar List</h2>
    <div class="overflow-hidden bg-white shadow rounded-lg">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">First Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 uppercase tracking-wider">School</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <?php foreach ($scholars as $scholar): ?>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= esc($scholar['first_name']) ?></td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= esc($scholar['last_name']) ?></td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= esc($scholar['course']) ?></td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= esc($scholar['status']) ?></td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= esc($scholar['school_name']) ?></td>

              <!-- UPDATED ACTION COLUMN -->
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <div class="flex items-center gap-4">

                  <!-- Edit -->
                  <a href="/scholars/edit/<?= $scholar['id'] ?>" 
                     class="text-indigo-600 hover:text-indigo-900"
                     title="Edit">
                    <svg xmlns="http://www.w3.org/2000/svg" 
                         class="h-8 w-8" 
                         fill="none" 
                         viewBox="0 0 24 24" 
                         stroke="currentColor">
                      <path stroke-linecap="round" 
                            stroke-linejoin="round" 
                            stroke-width="2" 
                            d="M15.232 5.232l3.536 3.536M9 13l6-6 3 3-6 6H9v-3z"/>
                    </svg>
                  </a>

                  <!-- Delete -->
                  <a href="/scholars/delete/<?= $scholar['id'] ?>" 
                     class="text-black-900 hover:text-blue-300"
                     title="Delete"
                     onclick="return confirm('Are you sure you want to delete this scholar?');">
                      <svg xmlns="http://www.w3.org/2000/svg" 
                         class="h-8 w-8" 
                         fill="none" 
                         viewBox="0 0 24 24" 
                         stroke="currentColor">
                      <path stroke-linecap="round" 
                            stroke-linejoin="round" 
                            stroke-width="2" 
                            d="M9 3h6m2 0h-10m1 0l1 2h6l1-2M6 7h12m-1 0l-1 12a2 2 0 01-2 2H10a2 2 0 01-2-2L7 7m3 4v6m4-6v6"/>
                    </svg>
                  </a>

                </div>
              </td>

            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
</div>

<script>
  const dropdownButton = document.getElementById('dropdownButton');
  const dropdownMenu = document.getElementById('dropdownMenu');
  const dropdownLabel = document.getElementById('dropdownLabel');
  const schoolInput = document.getElementById('schoolInput');

  dropdownButton.addEventListener('click', () => {
    dropdownMenu.classList.toggle('hidden');
  });

  dropdownMenu.querySelectorAll('li').forEach(item => {
    item.addEventListener('click', () => {
      const value = item.dataset.value;
      const label = item.innerText.trim();
      dropdownLabel.innerText = label;
      schoolInput.value = value;
      dropdownMenu.classList.add('hidden');
      item.closest('form')?.submit();
    });
  });

  document.addEventListener('click', (e) => {
    if (!dropdownButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
      dropdownMenu.classList.add('hidden');
    }
  });
</script>

<?= $this->endSection() ?>
