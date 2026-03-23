<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div class="max-w-6xl mx-auto space-y-6">
  <!-- Page Header -->
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-semibold text-gray-800">Financial Report</h1>
      <p class="text-sm text-gray-500">Comprehensive financial overview of all scholarship billings</p>
    </div>
    <a href="<?= site_url('admin/reports/export-financial') ?>" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
      Export Report
    </a>
  </div>

  <!-- Financial Summary Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
    <div class="rounded-lg bg-blue-50 border border-blue-200 p-4">
      <p class="text-xs text-blue-600 font-medium mb-1">Total Billing Amount</p>
      <p class="text-2xl font-bold text-blue-700">₱<?= number_format($totalAmount ?? 0, 0) ?></p>
    </div>
    <div class="rounded-lg bg-green-50 border border-green-200 p-4">
      <p class="text-xs text-green-600 font-medium mb-1">Total Collected</p>
      <p class="text-2xl font-bold text-green-700">₱<?= number_format($totalCollected ?? 0, 0) ?></p>
    </div>
    <div class="rounded-lg bg-red-50 border border-red-200 p-4">
      <p class="text-xs text-red-600 font-medium mb-1">Outstanding Balance</p>
      <p class="text-2xl font-bold text-red-700">₱<?= number_format(($totalAmount ?? 0) - ($totalCollected ?? 0), 0) ?></p>
    </div>
    <div class="rounded-lg bg-purple-50 border border-purple-200 p-4">
      <p class="text-xs text-purple-600 font-medium mb-1">Collection Rate</p>
      <p class="text-2xl font-bold text-purple-700"><?= ($totalAmount ?? 0) > 0 ? round((($totalCollected ?? 0) / ($totalAmount ?? 0)) * 100) : 0 ?>%</p>
    </div>
  </div>

  <!-- Financial Breakdown -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- By Status -->
    <div class="rounded-lg border border-gray-200 bg-white p-6">
      <h2 class="text-lg font-semibold text-gray-800 mb-4">Billing Status Breakdown</h2>
      <div class="space-y-3">
        <div class="flex justify-between items-center pb-2 border-b">
          <span class="text-gray-700">Draft Billings</span>
          <span class="font-semibold">₱<?= number_format($draftAmount ?? 0, 0) ?></span>
        </div>
        <div class="flex justify-between items-center pb-2 border-b">
          <span class="text-gray-700">Submitted Billings</span>
          <span class="font-semibold">₱<?= number_format($submittedAmount ?? 0, 0) ?></span>
        </div>
        <div class="flex justify-between items-center pb-2 border-b">
          <span class="text-gray-700">Received Billings</span>
          <span class="font-semibold">₱<?= number_format($receivedAmount ?? 0, 0) ?></span>
        </div>
        <div class="flex justify-between items-center pb-2 border-b">
          <span class="text-gray-700">Fully Paid Billings</span>
          <span class="font-semibold text-green-600">₱<?= number_format($paidAmount ?? 0, 0) ?></span>
        </div>
      </div>
    </div>

    <!-- Monthly Collection Trend -->
    <div class="rounded-lg border border-gray-200 bg-white p-6">
      <h2 class="text-lg font-semibold text-gray-800 mb-4">Collection Performance</h2>
      <div class="space-y-3">
        <div class="flex justify-between items-center pb-2 border-b">
          <span class="text-gray-700">Bills Pending Payment</span>
          <span class="font-semibold text-orange-600"><?= $pendingBills ?? 0 ?> bills</span>
        </div>
        <div class="flex justify-between items-center pb-2 border-b">
          <span class="text-gray-700">Partially Paid Bills</span>
          <span class="font-semibold text-yellow-600"><?= $partiallyPaidBills ?? 0 ?> bills</span>
        </div>
        <div class="flex justify-between items-center pb-2 border-b">
          <span class="text-gray-700">Fully Paid Bills</span>
          <span class="font-semibold text-green-600"><?= $fullyPaidBills ?? 0 ?> bills</span>
        </div>
        <div class="flex justify-between items-center pt-2">
          <span class="font-medium text-gray-800">Total Bills</span>
          <span class="font-bold"><?= ($pendingBills ?? 0) + ($partiallyPaidBills ?? 0) + ($fullyPaidBills ?? 0) ?> bills</span>
        </div>
      </div>
    </div>
  </div>

  <!-- School-wise Breakdown -->
  <div class="rounded-lg border border-gray-200 bg-white p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Financial Summary by School</h2>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
          <tr>
            <th class="px-4 py-2 text-left text-gray-600 font-medium">School</th>
            <th class="px-4 py-2 text-right text-gray-600 font-medium">Total Due</th>
            <th class="px-4 py-2 text-right text-gray-600 font-medium">Collected</th>
            <th class="px-4 py-2 text-right text-gray-600 font-medium">Outstanding</th>
            <th class="px-4 py-2 text-center text-gray-600 font-medium">Collection %</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <?php if (empty($schoolData)): ?>
            <tr>
              <td colspan="5" class="px-4 py-4 text-center text-gray-500">No financial data available</td>
            </tr>
          <?php else: ?>
            <?php foreach ($schoolData as $school): 
              $outstanding = $school['total_due'] - $school['collected'];
              $collectionRate = $school['total_due'] > 0 ? round(($school['collected'] / $school['total_due']) * 100) : 0;
            ?>
              <tr class="hover:bg-gray-50">
                <td class="px-4 py-2 font-medium text-gray-800"><?= esc($school['school_name'] ?? 'N/A') ?></td>
                <td class="px-4 py-2 text-right">₱<?= number_format($school['total_due'], 0) ?></td>
                <td class="px-4 py-2 text-right font-semibold text-green-600">₱<?= number_format($school['collected'], 0) ?></td>
                <td class="px-4 py-2 text-right font-semibold text-red-600">₱<?= number_format($outstanding, 0) ?></td>
                <td class="px-4 py-2 text-center font-semibold"><?= $collectionRate ?>%</td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="flex gap-2 pt-4">
    <a href="<?= site_url('admin/reports') ?>" class="inline-block text-sm text-blue-600 hover:text-blue-700 font-medium">
      ← Back to Reports
    </a>
  </div>
</div>

<?= $this->endSection() ?>
