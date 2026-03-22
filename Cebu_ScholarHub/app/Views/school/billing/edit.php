<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div class="max-w-5xl mx-auto">
  <div class="mb-6">
    <a href="<?= site_url('school/billing/view/' . $batch['id']) ?>" class="text-sm text-gray-500 hover:text-gray-700">
      &larr; Back to Billing
    </a>
    <h1 class="text-3xl font-bold text-gray-900 mt-2">Edit Billing Draft</h1>
    <p class="mt-1 text-sm text-gray-500">Update scholars and billing details before submitting to the office.</p>
  </div>

  <?php if (session()->getFlashdata('error')): ?>
    <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
      <?= session()->getFlashdata('error') ?>
    </div>
  <?php endif; ?>

  <form method="post" action="<?= site_url('school/billing/update/' . $batch['id']) ?>" class="space-y-6">
    <?= csrf_field() ?>

    <!-- Billing Header Information -->
    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
      <h2 class="text-lg font-semibold text-gray-900 mb-4">Billing Information</h2>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label for="semester" class="block text-sm font-medium text-gray-700 mb-1">Semester <span class="text-red-500">*</span></label>
          <select id="semester" name="semester" required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Select Semester</option>
            <?php foreach (['1st Semester', '2nd Semester', 'Summer'] as $sem): ?>
              <option value="<?= $sem ?>" <?= $batch['semester'] === $sem ? 'selected' : '' ?>>
                <?= $sem ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label for="school_year" class="block text-sm font-medium text-gray-700 mb-1">School Year <span class="text-red-500">*</span></label>
          <input type="text" id="school_year" name="school_year" placeholder="e.g. 2024-2025" required
                 value="<?= esc($batch['school_year']) ?>"
                 class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <div>
          <label for="batch_label" class="block text-sm font-medium text-gray-700 mb-1">Batch Label <span class="text-red-500">*</span></label>
          <input type="text" id="batch_label" name="batch_label" placeholder="e.g. BATCH 2024 SCHOLARS" required
                 value="<?= esc($batch['batch_label']) ?>"
                 class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
      </div>

      <div class="mt-4">
        <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1">Remarks (Optional)</label>
        <textarea id="remarks" name="remarks" rows="2" placeholder="Add any additional notes..."
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"><?= esc($batch['remarks'] ?? '') ?></textarea>
      </div>
    </div>

    <!-- Scholar Selection -->
    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-900">Select Scholars</h2>
        <div class="flex items-center gap-3">
          <span id="selected-count" class="text-sm font-medium text-indigo-600">0 selected</span>
          <button type="button" onclick="toggleAll()"
                  class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
            Select All
          </button>
        </div>
      </div>

      <!-- Search -->
      <div class="mb-4">
        <input type="text" id="scholar-search" placeholder="Search by name, ID, or course..."
               oninput="filterScholars()"
               class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
      </div>

      <?php if (empty($scholars)): ?>
        <div class="text-center py-12">
          <p class="text-gray-500">No active scholars found in your school.</p>
        </div>
      <?php else: ?>
        <div class="overflow-x-auto border border-gray-200 rounded-md">
          <table class="min-w-full divide-y divide-gray-200 text-sm" id="scholars-table">
            <thead class="bg-gray-50 sticky top-0">
              <tr>
                <th class="px-4 py-3 w-10"><input type="checkbox" id="check-all" onchange="toggleAll(this)"></th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ID Num</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Name</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">MI</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Course</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Yr</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Control No.</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Barangay</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Amount</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white" id="scholars-tbody">
              <?php foreach ($scholars as $s): ?>
                <tr class="hover:bg-gray-50 scholar-row"
                    data-name="<?= strtolower($s['first_name'] . ' ' . $s['last_name']) ?>"
                    data-id="<?= strtolower($s['id_num'] ?? '') ?>"
                    data-course="<?= strtolower($s['course'] ?? '') ?>">
                  <td class="px-4 py-3">
                    <input type="checkbox" name="scholar_ids[]" value="<?= $s['id'] ?>"
                           class="scholar-check" onchange="updateCount()"
                           <?= in_array($s['id'], $existingScholars) ? 'checked' : '' ?>>
                  </td>
                  <td class="px-4 py-3 font-mono text-xs text-gray-600"><?= esc($s['id_num'] ?? '—') ?></td>
                  <td class="px-4 py-3 font-medium text-gray-900"><?= esc($s['last_name'] . ', ' . $s['first_name']) ?></td>
                  <td class="px-4 py-3 text-gray-500"><?= esc(substr($s['middle_name'] ?? '', 0, 1)) ?></td>
                  <td class="px-4 py-3 text-gray-700"><?= esc($s['course'] ?? '—') ?></td>
                  <td class="px-4 py-3 text-center font-semibold text-gray-700"><?= esc($s['year_level'] ?? '—') ?></td>
                  <td class="px-4 py-3 font-mono text-xs text-gray-500"><?= esc($s['control_no'] ?? '—') ?></td>
                  <td class="px-4 py-3 text-xs text-gray-600"><?= esc($s['barangay'] ?? '—') ?></td>
                  <td class="px-4 py-3 text-right font-semibold text-gray-900">₱10,000.00</td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <!-- Total Summary -->
        <div class="flex justify-end mt-4 pt-4 border-t border-gray-200">
          <div class="text-right">
            <p class="text-sm text-gray-600 mb-1">Total Billing Amount:</p>
            <p class="text-3xl font-bold text-indigo-600" id="running-total">₱0.00</p>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <!-- Form Actions -->
    <div class="flex justify-end gap-3">
      <a href="<?= site_url('school/billing/view/' . $batch['id']) ?>"
         class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
        Cancel
      </a>
      <button type="submit"
              class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
        Save Changes
      </button>
    </div>
  </form>
</div>

<script>
// Initialize count on load
document.addEventListener('DOMContentLoaded', updateCount);

function updateCount() {
  const checked = document.querySelectorAll('.scholar-check:checked').length;
  document.getElementById('selected-count').textContent = checked + ' selected';
  const total = checked * 10000;
  document.getElementById('running-total').textContent =
    '₱' + total.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function toggleAll(src) {
  const boxes     = document.querySelectorAll('.scholar-check');
  const allChecked = src ? src.checked : (document.querySelectorAll('.scholar-check:checked').length < boxes.length);
  boxes.forEach(box => {
    if (box.closest('tr').style.display !== 'none') {
      box.checked = allChecked;
    }
  });
  updateCount();
}

function filterScholars() {
  const search = document.getElementById('scholar-search').value.toLowerCase();
  document.querySelectorAll('.scholar-row').forEach(row => {
    const text = (row.dataset.name + ' ' + row.dataset.id + ' ' + row.dataset.course).toLowerCase();
    row.style.display = text.includes(search) ? '' : 'none';
  });
}
</script>

<?= $this->endSection() ?>
