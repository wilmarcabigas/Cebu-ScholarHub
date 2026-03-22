<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div class="max-w-6xl mx-auto space-y-6">
  <!-- Page Header -->
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-semibold text-gray-800">Payment History</h1>
      <p class="text-sm text-gray-500">Detailed record of all payments received</p>
    </div>
    <a href="<?= site_url('school/reports/export-payment-history') ?>" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
      Export Report
    </a>
  </div>

  <!-- Filters -->
  <div class="rounded-lg border border-gray-200 bg-white p-4 space-y-4">
    <form method="get" action="<?= site_url('school/reports/payment-history') ?>" class="space-y-4">
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
          <input type="date" name="start_date" value="<?= esc($_GET['start_date'] ?? '') ?>" class="w-full rounded text-sm border border-gray-300 p-2">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
          <input type="date" name="end_date" value="<?= esc($_GET['end_date'] ?? '') ?>" class="w-full rounded text-sm border border-gray-300 p-2">
        </div>
      </div>
      <button type="submit" class="rounded-md bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-900">
        Filter Results
      </button>
    </form>
  </div>

  <!-- Payments Table -->
  <div class="rounded-lg border border-gray-200 bg-white overflow-hidden">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 border-b border-gray-200">
        <tr>
          <th class="px-4 py-2 text-left text-gray-600 font-medium">Payment Date</th>
          <th class="px-4 py-2 text-left text-gray-600 font-medium">Scholar</th>
          <th class="px-4 py-2 text-left text-gray-600 font-medium">Bill ID</th>
          <th class="px-4 py-2 text-right text-gray-600 font-medium">Amount Paid</th>
          <th class="px-4 py-2 text-left text-gray-600 font-medium">Status</th>
          <th class="px-4 py-2 text-left text-gray-600 font-medium">Received By</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        <?php if (empty($payments)): ?>
          <tr>
            <td colspan="6" class="px-4 py-4 text-center text-gray-500">No payment records found</td>
          </tr>
        <?php else: ?>
          <?php foreach ($payments as $payment): ?>
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-2"><?= date('M d, Y', strtotime($payment['payment_date'])) ?></td>
              <td class="px-4 py-2"><?= isset($payment['scholar_name']) ? esc($payment['scholar_name']) : 'N/A' ?></td>
              <td class="px-4 py-2 font-mono"><?= esc($payment['bill_id']) ?></td>
              <td class="px-4 py-2 text-right font-semibold text-green-600">₱<?= number_format($payment['amount_paid'], 2) ?></td>
              <td class="px-4 py-2">
                <span class="inline-block px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                  Paid
                </span>
              </td>
              <td class="px-4 py-2"><?= esc($payment['received_by'] ?? 'System') ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Summary -->
  <?php if (!empty($payments)): ?>
    <div class="rounded-lg bg-green-50 border border-green-200 p-4">
      <p class="text-sm font-medium text-gray-700 mb-2">
        Total Payments: <span class="font-semibold text-green-700">₱<?= number_format(array_sum(array_column($payments, 'amount_paid')), 2) ?></span>
      </p>
      <p class="text-xs text-gray-600">
        <?= count($payments) ?> payment transaction(s)
      </p>
    </div>
  <?php endif; ?>

  <div class="flex gap-2 pt-4">
    <a href="<?= site_url('school/reports') ?>" class="inline-block text-sm text-blue-600 hover:text-blue-700 font-medium">
      ← Back to Reports
    </a>
  </div>
</div>

<?= $this->endSection() ?>
