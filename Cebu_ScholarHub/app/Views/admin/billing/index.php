<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div class="space-y-6">
  <!-- Page Header -->
  <div class="border-b border-gray-200 pb-4">
    <h1 class="text-3xl font-bold text-gray-900">Submitted Billings</h1>
    <p class="mt-1 text-sm text-gray-500">Review and process billing submissions from schools</p>
  </div>

  <!-- Flash Messages -->
  <?php if (session()->getFlashdata('success')): ?>
    <div class="rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
      <?= session()->getFlashdata('success') ?>
    </div>
  <?php endif; ?>

  <!-- Search & Filter -->
  <div class="flex gap-4 items-end">
    <div class="flex-1">
      <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
      <input type="text" id="search-input" placeholder="Search school or batch label..."
             class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
             onkeyup="filterRows()">
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Status</label>
      <select id="filter-status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              onchange="filterRows()">
        <option value="">All Statuses</option>
        <option value="draft">Draft</option>
        <option value="submitted">Submitted</option>
        <option value="received">Received</option>
        <option value="partial">Partial</option>
        <option value="paid">Paid</option>
      </select>
    </div>
  </div>

  <!-- Billings Table -->
  <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
    <?php if (empty($batches)): ?>
      <div class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No submitted billings</h3>
        <p class="mt-1 text-sm text-gray-500">Billings will appear here once schools submit them.</p>
      </div>
    <?php else: ?>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">School</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Batch Label</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Semester</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">School Year</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Total Amount</th>
              <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Submitted</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 bg-white" id="batches-tbody">
            <?php foreach ($batches as $batch): ?>
              <?php
                $badgeClass = match($batch['status']) {
                  'draft'     => 'bg-gray-100 text-gray-600',
                  'submitted' => 'bg-blue-100 text-blue-700',
                  'received'  => 'bg-yellow-100 text-yellow-700',
                  'partial'   => 'bg-orange-100 text-orange-700',
                  'paid'      => 'bg-green-100 text-green-700',
                  default     => 'bg-gray-100 text-gray-500',
                };
              ?>
              <tr class="hover:bg-gray-50 batch-row"
                  data-search="<?= strtolower($batch['school_name'] . ' ' . $batch['batch_label']) ?>"
                  data-status="<?= $batch['status'] ?>">
                <td class="px-4 py-3 font-medium text-gray-900"><?= esc($batch['school_name']) ?></td>
                <td class="px-4 py-3 text-gray-600"><?= esc($batch['batch_label']) ?></td>
                <td class="px-4 py-3 text-gray-600"><?= esc($batch['semester']) ?></td>
                <td class="px-4 py-3 text-gray-600"><?= esc($batch['school_year']) ?></td>
                <td class="px-4 py-3 text-right font-semibold text-gray-900">₱<?= number_format($batch['total_amount'], 2) ?></td>
                <td class="px-4 py-3 text-center">
                  <span class="inline-flex items-center gap-1 rounded-full px-2 py-1 text-xs font-semibold <?= $badgeClass ?>">
                    <?= strtoupper($batch['status']) ?>
                  </span>
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs">
                  <?= $batch['submitted_at'] ? date('M d, Y', strtotime($batch['submitted_at'])) : '—' ?>
                </td>
                <td class="px-4 py-3 text-right">
                  <a href="<?= site_url('admin/billing/view/' . $batch['id']) ?>"
                     class="text-indigo-600 hover:text-indigo-900 font-medium text-sm">
                    Review
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
function filterRows() {
  const q      = document.getElementById('search-input').value.toLowerCase();
  const status = document.getElementById('filter-status').value.toLowerCase();

  document.querySelectorAll('.batch-row').forEach(row => {
    const matchSearch = !q      || row.dataset.search.includes(q);
    const matchStatus = !status || row.dataset.status === status;
    row.style.display = (matchSearch && matchStatus) ? '' : 'none';
  });
}
</script>

<?= $this->endSection() ?>