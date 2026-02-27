<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<?php
  $role = $user['role'] ?? '';
  $selectedSchool = $_GET['school_id'] ?? '';
  $selectedSemester = $_GET['semester'] ?? '';
  $semesters = range(1, 10); // 10 semesters
?>

<div class="space-y-6">

  <!-- HEADER -->
  <header class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
    <div>
      <h1 class="text-2xl font-semibold tracking-tight">
        Welcome, <?= esc($user['full_name']) ?>!
      </h1>
      <p class="text-sm text-gray-500 mt-1">
        You are logged in as 
        <span class="font-medium"><?= esc($role) ?></span>.
      </p>
    </div>
  </header>

  <!-- FILTER SECTION -->
  <div class="mb-6 flex flex-col sm:flex-row sm:items-end gap-6 max-w-4xl">

    <!-- SCHOOL FILTER (staff & admin) -->
    <?php if (in_array($role, ['staff', 'admin'])): ?>
      <form method="get" action="<?= site_url('scholars') ?>" class="relative w-64">
        <label class="block text-sm font-medium text-gray-700 mb-1">
          Filter by School
        </label>

        <button type="button" id="dropdownButton"
          class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2 text-left flex justify-between items-center">
          
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
          ▼
        </button>

        <ul id="dropdownMenu"
          class="absolute hidden w-full bg-white border rounded-lg shadow mt-1 z-50 max-h-60 overflow-auto">
          
          <li class="px-4 py-2 hover:bg-indigo-100 cursor-pointer" data-value="">
            All Schools
          </li>

          <?php foreach ($schools as $school): ?>
            <li class="px-4 py-2 hover:bg-indigo-100 cursor-pointer"
                data-value="<?= $school['id'] ?>">
              <?= esc($school['name']) ?>
            </li>
          <?php endforeach; ?>
        </ul>

        <input type="hidden" name="school_id" id="schoolInput"
               value="<?= esc($selectedSchool) ?>">

        <input type="hidden" name="semester"
               value="<?= esc($selectedSemester) ?>">
      </form>
    <?php endif; ?>


    <!-- SEMESTER FILTER (school staff & admin) -->
    <?php if (in_array($role, ['staff', 'admin', 'school_staff', 'school_admin'])): ?>

      <form method="get" action="<?= site_url('scholars') ?>" class="w-64">
        <label class="block text-sm font-medium text-gray-700 mb-1">
          Filter by Semester
        </label>

        <select name="semester"
                onchange="this.form.submit()"
                class="w-full border border-gray-300 rounded-lg px-3 py-2">

          <option value="">All Semesters</option>

          <?php foreach ($semesters as $sem): ?>
            <option value="<?= $sem ?>"
              <?= $selectedSemester == $sem ? 'selected' : '' ?>>
              Semester <?= $sem ?>
            </option>
          <?php endforeach; ?>

        </select>

        <input type="hidden" name="school_id"
               value="<?= esc($selectedSchool) ?>">
      </form>

    <?php endif; ?>


    <!-- ADD BUTTON -->
    <?php if (in_array($role, ['staff','admin','school_staff'])): ?>
      <a href="<?= site_url('scholars/create') ?>"
         class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
        Add New Scholar
      </a>
    <?php endif; ?>

  </div>


  <!-- SCHOLAR TABLE -->
  <section class="mt-6">
    <h2 class="text-xl font-semibold mb-4">Scholar List</h2>

    <div class="overflow-hidden bg-white shadow rounded-lg">
      <table class="min-w-full divide-y divide-gray-200">
        
        <thead class="bg-gray-100">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium uppercase">First Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Last Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Course</th>
            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Semester</th>
            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium uppercase">School</th>
            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Actions</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-gray-200">
          <?php foreach ($scholars as $scholar): ?>

            <?php
              if ($selectedSchool && $selectedSchool != $scholar['school_id']) continue;
              if ($selectedSemester && $selectedSemester != $scholar['semester']) continue;
            ?>

            <tr>
              <td class="px-6 py-4"><?= esc($scholar['first_name']) ?></td>
              <td class="px-6 py-4"><?= esc($scholar['last_name']) ?></td>
              <td class="px-6 py-4"><?= esc($scholar['course']) ?></td>
              <td class="px-6 py-4">Semester <?= esc($scholar['semester']) ?></td>
              <td class="px-6 py-4"><?= esc($scholar['status']) ?></td>
              <td class="px-6 py-4"><?= esc($scholar['school_name']) ?></td>

              <td class="px-6 py-4 flex gap-4">
                  <!-- Edit Icon -->
  <a href="<?= site_url('scholars/edit/'.$scholar['id']) ?>"
     class="text-indigo-600 hover:text-indigo-900"
     title="Edit">
    <svg xmlns="http://www.w3.org/2000/svg" 
         class="w-5 h-5" 
         fill="none" 
         viewBox="0 0 24 24" 
         stroke="currentColor">
      <path stroke-linecap="round" 
            stroke-linejoin="round" 
            stroke-width="2" 
            d="M11 5h2m-1-1v2m-7.586 9.414l8-8a2 2 0 012.828 0l2.344 2.344a2 2 0 010 2.828l-8 8H7v-2.586z"/>
    </svg>
  </a>

  <!-- Delete Icon -->
  <a href="<?= site_url('scholars/delete/'.$scholar['id']) ?>"
     onclick="return confirm('Are you sure you want to delete this scholar?');"
     class="text-red-600 hover:text-red-800"
     title="Delete">
    <svg xmlns="http://www.w3.org/2000/svg" 
         class="w-5 h-5" 
         fill="none" 
         viewBox="0 0 24 24" 
         stroke="currentColor">
      <path stroke-linecap="round" 
            stroke-linejoin="round" 
            stroke-width="2" 
            d="M6 7h12M9 7V4h6v3m-7 4v6m4-6v6m4-6v6"/>
    </svg>
  </a>

</td> 
            </tr>

          <?php endforeach; ?>
        </tbody>

      </table>
    </div>
  </section>

</div>


<!-- DROPDOWN SCRIPT -->
<script>
const dropdownButton = document.getElementById('dropdownButton');
const dropdownMenu = document.getElementById('dropdownMenu');
const dropdownLabel = document.getElementById('dropdownLabel');
const schoolInput = document.getElementById('schoolInput');

if (dropdownButton && dropdownMenu) {
  dropdownButton.addEventListener('click', () => {
    dropdownMenu.classList.toggle('hidden');
  });

  dropdownMenu.querySelectorAll('li').forEach(item => {
    item.addEventListener('click', () => {
      schoolInput.value = item.dataset.value;
      dropdownLabel.innerText = item.innerText;
      dropdownMenu.classList.add('hidden');
      item.closest('form').submit();
    });
  });

  document.addEventListener('click', (e) => {
    if (!dropdownButton.contains(e.target) &&
        !dropdownMenu.contains(e.target)) {
      dropdownMenu.classList.add('hidden');
    }
  });
}
</script>

<?= $this->endSection() ?>