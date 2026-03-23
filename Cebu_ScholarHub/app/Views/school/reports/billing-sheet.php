<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div class="max-w-6xl mx-auto space-y-6">
  <!-- Page Header -->
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-semibold text-gray-800">Billing Sheet</h1>
      <p class="text-sm text-gray-500">Detailed billing information for batch <?= esc($batchId) ?></p>
    </div>
    <button onclick="window.print()" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
      Print
    </button>
  </div>

  <?php if (empty($items)): ?>
    <div class="rounded-lg border border-gray-200 bg-gray-50 p-6 text-center">
      <p class="text-gray-600">No billing information found for this batch.</p>
      <a href="<?= site_url('school/reports') ?>" class="text-sm text-blue-600 hover:text-blue-700 font-medium mt-2 inline-block">
        Back to Reports
      </a>
    </div>
  <?php else: ?>
    <!-- Batch Summary -->
    <div class="rounded-lg border border-gray-200 bg-white p-6">
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div>
          <p class="text-xs text-gray-500">Batch ID</p>
          <p class="text-lg font-semibold text-gray-800"><?= esc($batchId) ?></p>
        </div>
        <div>
          <p class="text-xs text-gray-500">Total Scholars</p>
          <p class="text-lg font-semibold text-gray-800"><?= count($items) ?></p>
        </div>
        <div>
          <p class="text-xs text-gray-500">Total Amount</p>
          <p class="text-lg font-semibold text-gray-800">₱<?= number_format(count($items) * 10000, 2) ?></p>
        </div>
        <div>
          <p class="text-xs text-gray-500">Per Scholar</p>
          <p class="text-lg font-semibold text-gray-800">₱<?= number_format(10000, 2) ?></p>
        </div>
      </div>
    </div>

    <!-- Scholars Table -->
    <div class="rounded-lg border border-gray-200 bg-white overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
          <tr>
            <th class="px-4 py-2 text-left text-gray-600 font-medium">Scholar ID</th>
            <th class="px-4 py-2 text-left text-gray-600 font-medium">Name</th>
            <th class="px-4 py-2 text-left text-gray-600 font-medium">Course</th>
            <th class="px-4 py-2 text-left text-gray-600 font-medium">Year</th>
            <th class="px-4 py-2 text-right text-gray-600 font-medium">Amount</th>
            <th class="px-4 py-2 text-left text-gray-600 font-medium">Control No.</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <?php $count = 0; ?>
          <?php foreach ($items as $item): ?>
            <?php $count++; ?>
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-2 font-mono"><?= esc($item['scholar_id'] ?? 'N/A') ?></td>
              <td class="px-4 py-2">
                <div class="font-medium text-gray-800"><?= esc($item['scholar_name'] ?? 'N/A') ?></div>
                <div class="text-xs text-gray-500"><?= esc($item['school_name'] ?? 'N/A') ?></div>
              </td>
              <td class="px-4 py-2"><?= esc($item['course'] ?? 'N/A') ?></td>
              <td class="px-4 py-2"><?= esc($item['year_level'] ?? 'N/A') ?></td>
              <td class="px-4 py-2 text-right font-semibold">₱<?= number_format(10000, 2) ?></td>
              <td class="px-4 py-2 font-mono"><?= esc($item['control_no'] ?? 'N/A') ?></td>
            </tr>
          <?php endforeach; ?>
          <tr class="bg-gray-100 font-semibold">
            <td colspan="4" class="px-4 py-2 text-right">TOTAL:</td>
            <td class="px-4 py-2 text-right">₱<?= number_format($count * 10000, 2) ?></td>
            <td class="px-4 py-2"></td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="flex gap-2 pt-4">
      <a href="<?= site_url('school/reports') ?>" class="inline-block text-sm text-blue-600 hover:text-blue-700 font-medium">
        ← Back to Reports
      </a>
    </div>
  <?php endif; ?>
</div>

<?= $this->endSection() ?>
