<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div class="max-w-6xl mx-auto space-y-6">
  <!-- Page Header -->
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-semibold text-gray-800">Billing Status Summary</h1>
      <p class="text-sm text-gray-500">Overview of all billing batches and their current status</p>
    </div>
  </div>

  <!-- Summary Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
    <div class="rounded-lg bg-blue-50 border border-blue-200 p-4">
      <p class="text-xs text-blue-600 font-medium mb-1">Total Batches</p>
      <p class="text-3xl font-bold text-blue-700"><?= count($batches) ?></p>
    </div>
    <div class="rounded-lg bg-yellow-50 border border-yellow-200 p-4">
      <p class="text-xs text-yellow-600 font-medium mb-1">Draft</p>
      <p class="text-3xl font-bold text-yellow-700"><?= count(array_filter($batches, fn($b) => $b['status'] === 'draft')) ?></p>
    </div>
    <div class="rounded-lg bg-orange-50 border border-orange-200 p-4">
      <p class="text-xs text-orange-600 font-medium mb-1">Submitted</p>
      <p class="text-3xl font-bold text-orange-700"><?= count(array_filter($batches, fn($b) => $b['status'] === 'submitted')) ?></p>
    </div>
    <div class="rounded-lg bg-green-50 border border-green-200 p-4">
      <p class="text-xs text-green-600 font-medium mb-1">Received</p>
      <p class="text-3xl font-bold text-green-700"><?= count(array_filter($batches, fn($b) => $b['status'] === 'received')) ?></p>
    </div>
  </div>

  <!-- Status Table -->
  <div class="rounded-lg border border-gray-200 bg-white overflow-hidden">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 border-b border-gray-200">
        <tr>
          <th class="px-4 py-2 text-left text-gray-600 font-medium">Batch Label</th>
          <th class="px-4 py-2 text-left text-gray-600 font-medium">Semester</th>
          <th class="px-4 py-2 text-center text-gray-600 font-medium">Scholars</th>
          <th class="px-4 py-2 text-right text-gray-600 font-medium">Total Amount</th>
          <th class="px-4 py-2 text-right text-gray-600 font-medium">Amount Paid</th>
          <th class="px-4 py-2 text-center text-gray-600 font-medium">Status</th>
          <th class="px-4 py-2 text-left text-gray-600 font-medium">Created</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        <?php if (empty($batches)): ?>
          <tr>
            <td colspan="7" class="px-4 py-4 text-center text-gray-500">No billing batches found</td>
          </tr>
        <?php else: ?>
          <?php foreach ($batches as $batch): 
            $statusColor = match($batch['status']) {
              'draft' => 'bg-yellow-100 text-yellow-800',
              'submitted' => 'bg-orange-100 text-orange-800',
              'received' => 'bg-purple-100 text-purple-800',
              'paid' => 'bg-green-100 text-green-800',
              default => 'bg-gray-100 text-gray-800'
            };
            $statusLabel = ucfirst($batch['status']);
          ?>
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-2 font-medium text-gray-800"><?= esc($batch['batch_label']) ?></td>
              <td class="px-4 py-2"><?= esc($batch['semester']) ?></td>
              <td class="px-4 py-2 text-center"><?= $batch['scholars_count'] ?? 0 ?></td>
              <td class="px-4 py-2 text-right font-semibold">₱<?= number_format(($batch['scholars_count'] ?? 0) * 10000, 2) ?></td>
              <td class="px-4 py-2 text-right font-semibold text-green-600">₱<?= number_format($batch['amount_paid'] ?? 0, 2) ?></td>
              <td class="px-4 py-2 text-center">
                <span class="inline-block px-2 py-1 rounded text-xs font-medium <?= $statusColor ?>">
                  <?= $statusLabel ?>
                </span>
              </td>
              <td class="px-4 py-2 text-sm text-gray-500"><?= date('M d, Y', strtotime($batch['created_at'])) ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Payment Progress by Status -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="rounded-lg border border-gray-200 bg-white p-6">
      <h2 class="text-lg font-semibold text-gray-800 mb-4">Status Distribution</h2>
      <div class="space-y-3">
        <?php 
        $statuses = ['draft', 'submitted', 'received', 'paid'];
        foreach ($statuses as $status):
          $count = count(array_filter($batches, fn($b) => $b['status'] === $status));
          $percentage = !empty($batches) ? round(($count / count($batches)) * 100) : 0;
          $color = match($status) {
            'draft' => 'bg-yellow-500',
            'submitted' => 'bg-orange-500',
            'received' => 'bg-purple-500',
            'paid' => 'bg-green-500',
            default => 'bg-gray-500'
          };
        ?>
          <div>
            <div class="flex justify-between text-sm mb-1">
              <span class="font-medium text-gray-700"><?= ucfirst($status) ?></span>
              <span class="text-gray-600"><?= $count ?> batches (<?= $percentage ?>%)</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div class="<?= $color ?> h-2 rounded-full" style="width: <?= $percentage ?>%"></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white p-6">
      <h2 class="text-lg font-semibold text-gray-800 mb-4">Financial Summary</h2>
      <div class="space-y-3">
        <?php 
        $totalAmount = array_sum(array_map(fn($b) => ($b['scholars_count'] ?? 0) * 10000, $batches));
        $totalPaid = array_sum(array_column($batches, 'amount_paid'));
        $totalDue = $totalAmount - $totalPaid;
        $collectionRate = $totalAmount > 0 ? round(($totalPaid / $totalAmount) * 100) : 0;
        ?>
        <div class="flex justify-between items-center pb-3 border-b">
          <span class="text-gray-700">Total Amount Due:</span>
          <span class="font-semibold text-gray-800">₱<?= number_format($totalAmount, 2) ?></span>
        </div>
        <div class="flex justify-between items-center pb-3 border-b">
          <span class="text-gray-700">Amount Paid:</span>
          <span class="font-semibold text-green-600">₱<?= number_format($totalPaid, 2) ?></span>
        </div>
        <div class="flex justify-between items-center pb-3 border-b">
          <span class="text-gray-700">Outstanding Balance:</span>
          <span class="font-semibold text-red-600">₱<?= number_format($totalDue, 2) ?></span>
        </div>
        <div class="flex justify-between items-center pt-2">
          <span class="font-medium text-gray-800">Collection Rate:</span>
          <span class="font-bold text-lg"><?= $collectionRate ?>%</span>
        </div>
      </div>
    </div>
  </div>

  <div class="flex gap-2 pt-4">
    <a href="<?= site_url('school/reports') ?>" class="inline-block text-sm text-blue-600 hover:text-blue-700 font-medium">
      ← Back to Reports
    </a>
  </div>
</div>

<?= $this->endSection() ?>
