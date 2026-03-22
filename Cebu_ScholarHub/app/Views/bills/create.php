<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div class="max-w-4xl mx-auto">
  <div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-800">Create Billing</h1>
    <p class="text-sm text-gray-500">Select enrolled scholars and generate a semester billing for the CCCSP Office</p>
  </div>

  <?php if (session()->getFlashdata('error')): ?>
    <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
      <?= session()->getFlashdata('error') ?>
    </div>
  <?php endif; ?>

  <form method="post" action="<?= site_url('school/billing/store') ?>">
    <?= csrf_field() ?>

    <!-- Billing Header -->
    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm mb-6 space-y-4">
      <h2 class="text-base font-semibold text-gray-700">Billing Information</h2>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
          <select name="semester" required
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Select Semester</option>
            <option value="1st Semester">1st Semester</option>
            <option value="2nd Semester">2nd Semester</option>
            <option value="Summer">Summer</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">School Year</label>
          <input type="text" name="school_year" placeholder="e.g. 2024-2025" required
                 class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Batch Label</label>
          <input type="text" name="batch_label" placeholder="e.g. BATCH 2024 SCHOLARS" required
                 class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Remarks (optional)</label>
        <textarea name="remarks" rows="2"
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
      </div>
    </div>

    <!-- Scholar Selection -->
    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm mb-6">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-semibold text-gray-700">Select Scholars</h2>
        <div class="flex items-center gap-3">
          <span id="selected-count" class="text-sm text-gray-500">0 selected</span>
          <button type="button" onclick="toggleAll()"
                  class="rounded-md border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50">
            Select All
          </button>
        </div>
      </div>

      <!-- Search -->
      <div class="mb-3">
        <input type="text" id="scholar-search" placeholder="Search by name or ID..."
               oninput="filterScholars()"
               class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
      </div>

      <?php if (empty($scholars)): ?>
        <p class="text-sm text-gray-400 text-center py-8">No enrolled scholars found for your school.</p>
      <?php else: ?>
        <div class="overflow-auto max-h-96 rounded-md border border-gray-200">
          <table class="min-w-full divide-y divide-gray-200 text-sm" id="scholars-table">
            <thead class="bg-gray-50 sticky top-0">
              <tr>
                <th class="px-3 py-2 w-10"><input type="checkbox" id="check-all" onchange="toggleAll(this)"></th>
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">ID Num</th>
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">MI</th>
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Course</th>
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Yr</th>
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Control No.</th>
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Barangay</th>
                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white" id="scholars-tbody">
              <?php foreach ($scholars as $s): ?>
                <tr class="hover:bg-gray-50 scholar-row"
                    data-name="<?= strtolower($s['first_name'] . ' ' . $s['last_name']) ?>"
                    data-id="<?= strtolower($s['id_num'] ?? '') ?>">
                  <td class="px-3 py-2">
                    <input type="checkbox" name="scholar_ids[]" value="<?= $s['id'] ?>"
                           class="scholar-check" onchange="updateCount()">
                  </td>
                  <td class="px-3 py-2 text-gray-600 font-mono text-xs"><?= esc($s['id_num'] ?? '—') ?></td>
                  <td class="px-3 py-2 font-medium text-gray-800">
                    <?= esc($s['last_name'] . ', ' . $s['first_name']) ?>
                  </td>
                  <td class="px-3 py-2 text-gray-500"><?= esc($s['middle_name'][0] ?? '—') ?></td>
                  <td class="px-3 py-2 text-gray-600"><?= esc($s['course'] ?? '—') ?></td>
                  <td class="px-3 py-2 text-gray-600"><?= esc($s['year_level'] ?? '—') ?></td>
                  <td class="px-3 py-2 text-gray-500 font-mono text-xs"><?= esc($s['control_no'] ?? '—') ?></td>
                  <td class="px-3 py-2 text-gray-500 text-xs"><?= esc($s['barangay'] ?? '—') ?></td>
                  <td class="px-3 py-2 text-right font-semibold text-gray-700">₱10,000.00</td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>

      <!-- Running total -->
      <div class="flex justify-end mt-4 pt-4 border-t border-gray-100">
        <div class="text-right">
          <span class="text-sm text-gray-500">Total Billing Amount:</span>
          <span class="ml-2 text-xl font-semibold text-gray-800" id="running-total">₱0.00</span>
        </div>
      </div>
    </div>

    <div class="flex justify-end gap-3">
      <a href="<?= site_url('school/billing') ?>"
         class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
        Cancel
      </a>
      <button type="submit"
              class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
        Save as Draft
      </button>
    </div>
  </form>
</div>

<script>
function updateCount() {
  const checked = document.querySelectorAll('.scholar-check:checked').length;
  document.getElementById('selected-count').textContent = checked + ' selected';
  document.getElementById('running-total').textContent = '₱' + (checked * 10000).toLocaleString('en-PH', {minimumFractionDigits: 2});
}

function toggleAll(src) {
  const boxes = document.querySelectorAll('.scholar-check');
  const allChecked = src ? src.checked : (document.querySelectorAll('.scholar-check:checked').length < boxes.length);
  boxes.forEach(b => b.checked = allChecked);
  if (src) document.getElementById('check-all').checked = allChecked;
  updateCount();
}

function filterScholars() {
  const q = document.getElementById('scholar-search').value.toLowerCase();
  document.querySelectorAll('.scholar-row').forEach(row => {
    const match = row.dataset.name.includes(q) || row.dataset.id.includes(q);
    row.style.display = match ? '' : 'none';
  });
}
</script>

<?= $this->endSection() ?>