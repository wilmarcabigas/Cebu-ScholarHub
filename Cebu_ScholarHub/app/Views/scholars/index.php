<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<?php
  $role = $user['role'] ?? '';
?>

<div class="space-y-6">

  <?php if (session()->getFlashdata('success')): ?>
    <div class="p-3 bg-green-100 text-green-800 rounded"><?= esc(session()->getFlashdata('success')) ?></div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('error')): ?>
    <div class="p-3 bg-red-100 text-red-800 rounded"><?= esc(session()->getFlashdata('error')) ?></div>
  <?php endif; ?>

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

  <?php
    $semesterOptions = [
      ''    => 'All Semesters',
      '1st' => '1st Semester',
      '2nd' => '2nd Semester',
    ];
  ?>

  <?php if (in_array($role, ['staff', 'admin'])): ?>
    <form method="get" action="<?= site_url('scholars') ?>"
          class="mb-4 flex flex-col sm:flex-row sm:items-end gap-4 flex-wrap">

      <!-- Filter by School (custom dropdown) -->
      <div class="relative min-w-[200px] flex-1">
        <label class="block text-sm font-medium text-gray-700 mb-1">Filter by School</label>
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
          <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </button>
        <ul id="dropdownMenu" class="absolute z-10 hidden mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-auto">
          <li class="px-4 py-2 hover:bg-indigo-100 cursor-pointer" data-value="">All Schools</li>
          <?php foreach ($schools as $school): ?>
            <li class="px-4 py-2 hover:bg-indigo-100 cursor-pointer" data-value="<?= $school['id'] ?>">
              <?= esc($school['name']) ?>
            </li>
          <?php endforeach; ?>
        </ul>
        <input type="hidden" name="school_id" id="schoolInput" value="<?= esc($selectedSchool) ?>">
      </div>

      <!-- Filter by Semester -->
      <div class="min-w-[200px]">
        <label for="semesterSelect" class="block text-sm font-medium text-gray-700 mb-1">Filter by Semester</label>
        <select id="semesterSelect" name="semester"
                onchange="this.form.submit()"
                class="w-full bg-white border border-gray-300 rounded-lg shadow-sm px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
          <?php foreach ($semesterOptions as $val => $label): ?>
            <option value="<?= $val ?>" <?= ($selectedSemester ?? '') === $val ? 'selected' : '' ?>>
              <?= $label ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <?php if (in_array($role, ['staff', 'school_admin', 'school_staff'])): ?>
        <a href="<?= site_url('scholars/create') ?>"
           class="group rounded-xl bg-blue-600 ring-1 ring-blue-400 px-4 py-2 hover:shadow-lg transition whitespace-nowrap self-end">
          <div class="flex items-center gap-2">
            <span class="text-xs font-semibold text-white">Add New Scholar</span>
            <span class="text-white group-hover:translate-x-0.5 transition">→</span>
          </div>
        </a>
      <?php endif; ?>

    </form>

  <?php elseif (in_array($role, ['school_admin', 'school_staff'])): ?>
    <!-- Semester filter for school roles -->
    <form method="get" action="<?= site_url('scholars') ?>"
          class="mb-4 flex flex-col sm:flex-row sm:items-end gap-4">
      <div class="min-w-[200px]">
        <label for="semesterSelectSchool" class="block text-sm font-medium text-gray-700 mb-1">Filter by Semester</label>
        <select id="semesterSelectSchool" name="semester"
                onchange="this.form.submit()"
                class="w-full bg-white border border-gray-300 rounded-lg shadow-sm px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
          <?php foreach ($semesterOptions as $val => $label): ?>
            <option value="<?= $val ?>" <?= ($selectedSemester ?? '') === $val ? 'selected' : '' ?>>
              <?= $label ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <a href="<?= site_url('scholars/create') ?>"
         class="group rounded-xl bg-blue-600 ring-1 ring-blue-400 px-4 py-2 hover:shadow-lg transition whitespace-nowrap self-end">
        <div class="flex items-center gap-2">
          <span class="text-xs font-semibold text-white">Add New Scholar</span>
          <span class="text-white group-hover:translate-x-0.5 transition">→</span>
        </div>
      </a>
    </form>
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
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scholarship Type</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Semesters</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 uppercase tracking-wider">School</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <?php foreach ($scholars as $scholar): ?>
            <?php
              $sType    = $scholar['scholarship_type'] ?? '4_semester';
              $maxSem   = $sType === '10_semester' ? 10 : ($sType === '8_semester' ? 8 : 4);
              $acquired = (int)($scholar['semesters_acquired'] ?? 0);
              $badgeColor = match($sType) {
                '8_semester'  => 'bg-blue-100 text-blue-800',
                '10_semester' => 'bg-purple-100 text-purple-800',
                default       => 'bg-gray-100 text-gray-700',
              };
              $badgeLabel = match($sType) {
                '8_semester'  => '8-SEM',
                '10_semester' => '10-SEM',
                default       => '4-SEM',
              };
            ?>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= esc($scholar['first_name']) ?></td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= esc($scholar['last_name']) ?></td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= esc($scholar['course']) ?></td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $badgeColor ?>">
                  <?= $badgeLabel ?>
                </span>
                <?php if (!empty($scholar['upgraded_at'])): ?>
                  <span class="ml-1 text-xs text-green-600" title="Upgraded on <?= esc($scholar['upgraded_at']) ?>">&#8593;</span>
                <?php endif; ?>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <?= $acquired ?>/<?= $maxSem ?>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= esc($scholar['status']) ?></td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= esc($scholar['school_name']) ?></td>

              <!-- UPDATED ACTION COLUMN -->
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <div class="flex items-center gap-2">

                  <!-- View Details -->
                  <button onclick="openDetailsModal(<?= htmlspecialchars(json_encode($scholar)) ?>)"
                          class="text-blue-600 hover:text-blue-900"
                          title="View Details">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-6 w-6"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor">
                      <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                  </button>

                  <!-- Edit -->
                  <a href="/scholars/edit/<?= $scholar['id'] ?>"
                     class="text-indigo-600 hover:text-indigo-900"
                     title="Edit">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-6 w-6"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor">
                      <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15.232 5.232l3.536 3.536M9 13l6-6 3 3-6 6H9v-3z"/>
                    </svg>
                  </a>

                  <!-- Upgrade to 8-semester (only for active 4-semester scholars) -->
                  <?php if ($sType === '4_semester' && $scholar['status'] === 'active'): ?>
                  <form method="post" action="<?= site_url('scholars/upgrade/' . $scholar['id']) ?>" style="display:inline;"
                        onsubmit="return confirm('Upgrade this scholar to 8-semester type?');">
                    <?= csrf_field() ?>
                    <button type="submit" class="text-green-600 hover:text-green-900" title="Upgrade to 8-Semester">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                      </svg>
                    </button>
                  </form>
                  <?php endif; ?>

                  <!-- Delete -->
                  <a href="/scholars/delete/<?= $scholar['id'] ?>"
                     class="text-red-600 hover:text-red-900"
                     title="Delete"
                     onclick="return confirm('Are you sure you want to delete this scholar?');">
                      <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-6 w-6"
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

<!-- SCHOLAR DETAILS MODAL -->
<div id="detailsModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden z-50 overflow-y-auto">
  <div class="flex items-center justify-center min-h-screen px-4">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-screen overflow-y-auto">
      <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
        <h2 class="text-xl font-semibold" id="modalTitle">Scholar Details</h2>
        <button onclick="closeDetailsModal()" class="text-gray-400 hover:text-gray-600">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <div class="p-6 space-y-6">
        <!-- PERSONAL INFO -->
        <div class="border-b pb-4">
          <h3 class="text-lg font-semibold text-gray-900 mb-3">Personal Information</h3>
          <div class="grid grid-cols-2 gap-4">
            <div><label class="text-sm font-medium text-gray-700">First Name</label><p id="modal_first_name" class="text-gray-900"></p></div>
            <div><label class="text-sm font-medium text-gray-700">Middle Name</label><p id="modal_middle_name" class="text-gray-900"></p></div>
            <div><label class="text-sm font-medium text-gray-700">Last Name</label><p id="modal_last_name" class="text-gray-900"></p></div>
            <div><label class="text-sm font-medium text-gray-700">Name Extension</label><p id="modal_name_extension" class="text-gray-900"></p></div>
          </div>
        </div>

        <!-- CONTACT & IDENTIFICATION -->
        <div class="border-b pb-4">
          <h3 class="text-lg font-semibold text-gray-900 mb-3">Contact & Identification</h3>
          <div class="grid grid-cols-2 gap-4">
            <div><label class="text-sm font-medium text-gray-700">Contact Number</label><p id="modal_contact_no" class="text-gray-900"></p></div>
            <div><label class="text-sm font-medium text-gray-700">LRN No.</label><p id="modal_lrn_no" class="text-gray-900"></p></div>
            <div class="col-span-2"><label class="text-sm font-medium text-gray-700">Email Address</label><p id="modal_email" class="text-gray-900"></p></div>
            <div class="col-span-2"><label class="text-sm font-medium text-gray-700">Address</label><p id="modal_address" class="text-gray-900"></p></div>
          </div>
        </div>

        <!-- ACADEMIC INFO -->
        <div class="border-b pb-4">
          <h3 class="text-lg font-semibold text-gray-900 mb-3">Academic Information</h3>
          <div class="grid grid-cols-2 gap-4">
            <div><label class="text-sm font-medium text-gray-700">Course</label><p id="modal_course" class="text-gray-900"></p></div>
            <div><label class="text-sm font-medium text-gray-700">Year Level</label><p id="modal_year_level" class="text-gray-900"></p></div>
            <div><label class="text-sm font-medium text-gray-700">Scholarship Type</label><p id="modal_scholarship_type" class="text-gray-900"></p></div>
            <div><label class="text-sm font-medium text-gray-700">Semesters Acquired</label><p id="modal_semesters_acquired" class="text-gray-900"></p></div>
            <div><label class="text-sm font-medium text-gray-700">Status</label><p id="modal_status" class="text-gray-900"></p></div>
            <div><label class="text-sm font-medium text-gray-700">School</label><p id="modal_school_name" class="text-gray-900"></p></div>
          </div>
        </div>

        <!-- SCHOOL HISTORY -->
        <div class="border-b pb-4">
          <h3 class="text-lg font-semibold text-gray-900 mb-3">School History</h3>
          <div class="space-y-3">
            <div><label class="text-sm font-medium text-gray-700">Elementary School</label><p id="modal_school_elementary" class="text-gray-900"></p></div>
            <div><label class="text-sm font-medium text-gray-700">Junior High School</label><p id="modal_school_junior" class="text-gray-900"></p></div>
            <div><label class="text-sm font-medium text-gray-700">Senior High School</label><p id="modal_school_senior_high" class="text-gray-900"></p></div>
          </div>
        </div>

        <!-- BIOGRAPHICAL -->
        <div class="border-b pb-4">
          <h3 class="text-lg font-semibold text-gray-900 mb-3">Biographical Information</h3>
          <div class="grid grid-cols-2 gap-4">
            <div><label class="text-sm font-medium text-gray-700">Gender</label><p id="modal_gender" class="text-gray-900"></p></div>
            <div><label class="text-sm font-medium text-gray-700">Date of Birth</label><p id="modal_date_of_birth" class="text-gray-900"></p></div>
          </div>
        </div>

        <!-- VOUCHER -->
        <div>
          <h3 class="text-lg font-semibold text-gray-900 mb-3">Voucher Information</h3>
          <div><label class="text-sm font-medium text-gray-700">Voucher Number</label><p id="modal_voucher_no" class="text-gray-900"></p></div>
        </div>
      </div>

      <div class="bg-gray-50 px-6 py-3 border-t border-gray-200 flex justify-end gap-2">
        <button onclick="closeDetailsModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
  // Dropdown functionality
  const dropdownButton = document.getElementById('dropdownButton');
  const dropdownMenu = document.getElementById('dropdownMenu');
  const dropdownLabel = document.getElementById('dropdownLabel');
  const schoolInput = document.getElementById('schoolInput');

  if (dropdownButton) {
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
  }

  // Modal functionality
  function openDetailsModal(scholar) {
    document.getElementById('modalTitle').textContent = scholar.first_name + ' ' + scholar.last_name;
    document.getElementById('modal_first_name').textContent = scholar.first_name || '';
    document.getElementById('modal_middle_name').textContent = scholar.middle_name || '';
    document.getElementById('modal_last_name').textContent = scholar.last_name || '';
    document.getElementById('modal_name_extension').textContent = scholar.name_extension || '';
    document.getElementById('modal_contact_no').textContent = scholar.contact_no || '';
    document.getElementById('modal_lrn_no').textContent = scholar.lrn_no || '';
    document.getElementById('modal_email').textContent = scholar.email || '';
    document.getElementById('modal_address').textContent = scholar.address || '';
    document.getElementById('modal_course').textContent = scholar.course || '';
    document.getElementById('modal_year_level').textContent = scholar.year_level || '';
    const typeLabels = {'4_semester':'4-Semester','8_semester':'8-Semester','10_semester':'10-Semester'};
    document.getElementById('modal_scholarship_type').textContent = typeLabels[scholar.scholarship_type] || scholar.scholarship_type || '';
    document.getElementById('modal_semesters_acquired').textContent = scholar.semesters_acquired || '';
    document.getElementById('modal_status').textContent = scholar.status || '';
    document.getElementById('modal_school_name').textContent = scholar.school_name || '';
    document.getElementById('modal_school_elementary').textContent = scholar.school_elementary || '';
    document.getElementById('modal_school_junior').textContent = scholar.school_junior || '';
    document.getElementById('modal_school_senior_high').textContent = scholar.school_senior_high || '';
    document.getElementById('modal_gender').textContent = scholar.gender || '';
    document.getElementById('modal_date_of_birth').textContent = scholar.date_of_birth || '';
    document.getElementById('modal_voucher_no').textContent = scholar.voucher_no || '';

    document.getElementById('detailsModal').classList.remove('hidden');
  }

  function closeDetailsModal() {
    document.getElementById('detailsModal').classList.add('hidden');
  }

  // Close modal when clicking outside
  document.getElementById('detailsModal')?.addEventListener('click', (e) => {
    if (e.target.id === 'detailsModal') {
      closeDetailsModal();
    }
  });
</script>

<?= $this->endSection() ?>
