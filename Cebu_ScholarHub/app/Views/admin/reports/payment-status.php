<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div class="max-w-6xl mx-auto space-y-6">
  <!-- Page Header -->
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-semibold text-gray-800">Payment Status by School</h1>
      <p class="text-sm text-gray-500">Real-time payment collection status across all schools</p>
    </div>
    <a href="<?= site_url('admin/reports') ?>" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
      ← Back to Reports
    </a>
  </div>

  <!-- Summary Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="rounded-lg bg-blue-50 border border-blue-200 p-4">
      <p class="text-xs text-blue-600 font-medium mb-1">Total Schools</p>
      <p class="text-3xl font-bold text-blue-700"><?= count($schools) ?></p>
    </div>
    <div class="rounded-lg bg-green-50 border border-green-200 p-4">
      <p class="text-xs text-green-600 font-medium mb-1">Total Collected</p>
      <p class="text-3xl font-bold text-green-700">₱<?= number_format(array_sum(array_column($schools, 'amount_paid')), 0) ?></p>
    </div>
    <div class="rounded-lg bg-red-50 border border-red-200 p-4">
      <p class="text-xs text-red-600 font-medium mb-1">Outstanding</p>
      <p class="text-3xl font-bold text-red-700">₱<?= number_format(array_sum(array_map(fn($s) => ($s['amount_due'] - $s['amount_paid']), $schools)), 0) ?></p>
    </div>
  </div>

  <!-- Schools Payment Status Table -->
  <div class="rounded-lg border border-gray-200 bg-white overflow-hidden">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 border-b border-gray-200">
        <tr>
          <th class="px-4 py-2 text-left text-gray-600 font-medium">School Name</th>
          <th class="px-4 py-2 text-center text-gray-600 font-medium">Bills Created</th>
          <th class="px-4 py-2 text-right text-gray-600 font-medium">Amount Due</th>
          <th class="px-4 py-2 text-right text-gray-600 font-medium">Amount Paid</th>
          <th class="px-4 py-2 text-right text-gray-600 font-medium">Outstanding</th>
          <th class="px-4 py-2 text-center text-gray-600 font-medium">Collection %</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        <?php if (empty($schools)): ?>
          <tr>
            <td colspan="6" class="px-4 py-4 text-center text-gray-500">No payment data available</td>
          </tr>
        <?php else: ?>
          <?php foreach ($schools as $school): 
            $outstanding = $school['amount_due'] - $school['amount_paid'];
            $collectionRate = $school['amount_due'] > 0 ? round(($school['amount_paid'] / $school['amount_due']) * 100) : 0;
          ?>
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-2 font-medium text-gray-800"><?= esc($school['school_name'] ?? 'N/A') ?></td>
              <td class="px-4 py-2 text-center"><?= $school['bills_count'] ?? 0 ?></td>
              <td class="px-4 py-2 text-right">₱<?= number_format($school['amount_due'], 2) ?></td>
              <td class="px-4 py-2 text-right font-semibold text-green-600">₱<?= number_format($school['amount_paid'], 2) ?></td>
              <td class="px-4 py-2 text-right font-semibold text-red-600">₱<?= number_format($outstanding, 2) ?></td>
              <td class="px-4 py-2 text-center">
                <div class="flex items-center justify-center gap-2">
                  <span class="font-semibold"><?= $collectionRate ?>%</span>
                  <div class="w-16 bg-gray-200 rounded-full h-2">
                    <div class="<?= $collectionRate >= 80 ? 'bg-green-500' : ($collectionRate >= 50 ? 'bg-yellow-500' : 'bg-red-500') ?> h-2 rounded-full" 
                         style="width: <?= min($collectionRate, 100) ?>%"></div>
                  </div>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?= $this->endSection() ?>
