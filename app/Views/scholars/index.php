<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<?php
  $role = $user['role'] ?? '';
  $selectedSchool = $_GET['school_id'] ?? '';
  $selectedCourse = $_GET['course'] ?? '';
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

    <!-- SCHOOL FILTER (ONLY staff & admin) -->
    <?php if (in_array($role, ['staff', 'admin'])): ?>
      <form method="get" action="<?= site_url('scholars') ?>" class="relative w-64">
        <label class="block text-sm font-medium text-gray-700 mb-1">
          Filter by School
        </label>

        <button type="button" id="dropdownButton"
          class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2 text-left flex justify-between items-center focus:ring-2 focus:ring-indigo-500">
          
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

        <!-- Preserve course filter -->
        <input type="hidden" name="course"
               value="<?= esc($selectedCourse) ?>">
      </form>
    <?php endif; ?>


    <!-- COURSE FILTER (ONLY school_staff) -->
    <?php if (in_array($role, ['school_staff', 'school_admin'])): ?>

      <?php
        $courses = array_unique(array_column($scholars, 'course'));
      ?>

      <form method="get" action="<?= site_url('scholars') ?>" class="w-64">
        <label class="block text-sm font-medium text-gray-700 mb-1">
          Filter by Course
        </label>

        <select name="course"
                onchange="this.form.submit()"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">

          <option value="">All Courses</option>

          <?php foreach ($courses as $course): ?>
            <option value="<?= esc($course) ?>"
              <?= $selectedCourse === $course ? 'selected' : '' ?>>
              <?= esc($course) ?>
            </option>
          <?php endforeach; ?>

        </select>

        <input type="hidden" name="school_id"
               value="<?= esc($selectedSchool) ?>">
      </form>

    <?php endif; ?>


    <!-- ADD BUTTON (Visible to all roles) -->
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
            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium uppercase">School</th>
            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Actions</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-gray-200">
          <?php foreach ($scholars as $scholar): ?>

            <?php
              if ($selectedCourse && $selectedCourse !== $scholar['course']) continue;
              if ($selectedSchool && $selectedSchool != $scholar['school_id']) continue;
            ?>

            <tr>
              <td class="px-6 py-4"><?= esc($scholar['first_name']) ?></td>
              <td class="px-6 py-4"><?= esc($scholar['last_name']) ?></td>
              <td class="px-6 py-4"><?= esc($scholar['course']) ?></td>
              <td class="px-6 py-4"><?= esc($scholar['status']) ?></td>
              <td class="px-6 py-4"><?= esc($scholar['school_name']) ?></td>

              <td class="px-6 py-4 flex gap-4">
                <a href="<?= site_url('scholars/edit/'.$scholar['id']) ?>"
                   class="text-indigo-600 hover:text-indigo-900">
                  Edit
                </a>

                <a href="<?= site_url('scholars/delete/'.$scholar['id']) ?>"
                   onclick="return confirm('Are you sure you want to delete this scholar?');"
                   class="text-red-600 hover:text-red-800">
                  Delete
                </a>
              </td>
            </tr>

          <?php endforeach; ?>
        </tbody>

      </table>
    </div>
  </section>

</div>


<!-- DROPDOWN SCRIPT (Safe even if hidden) -->
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