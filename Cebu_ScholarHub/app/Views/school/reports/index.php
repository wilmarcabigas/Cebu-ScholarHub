<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div class="space-y-6">
  <!-- Header -->
  <div class="border-b border-gray-200 pb-4">
    <h1 class="text-3xl font-bold text-gray-900">Reports</h1>
    <p class="mt-1 text-sm text-gray-500">View and export billing and payment reports</p>
  </div>

  <!-- Report Options -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <!-- Overall Financial Summary -->
    <div class="rounded-lg border border-gray-200 bg-white p-6">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">Financial Summary</h3>
      <div class="space-y-4">
        <div>
          <p class="text-sm text-gray-600">Total Billed</p>
          <p class="text-2xl font-bold text-gray-900">₱<?= number_format($totalDue, 2) ?></p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Total Paid</p>
          <p class="text-2xl font-bold text-green-600">₱<?= number_format($totalPaid, 2) ?></p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Outstanding</p>
          <p class="text-2xl font-bold text-red-600">₱<?= number_format($outstanding, 2) ?></p>
        </div>
      </div>
    </div>

    <!-- Key Metrics -->
    <div class="rounded-lg border border-gray-200 bg-white p-6">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">Key Metrics</h3>
      <div class="space-y-3">
        <div class="flex justify-between">
          <span class="text-sm text-gray-600">Active Scholars</span>
          <span class="text-sm font-semibold text-gray-900"><?= $totalScholars ?></span>
        </div>
        <div class="flex justify-between">
          <span class="text-sm text-gray-600">Total Billings</span>
          <span class="text-sm font-semibold text-gray-900"><?= $totalBillings ?></span>
        </div>
        <div class="flex justify-between">
          <span class="text-sm text-gray-600">Collection Rate</span>
          <span class="text-sm font-semibold text-gray-900"><?= $totalDue > 0 ? round(($totalPaid / $totalDue) * 100, 1) : 0 ?>%</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Report Links -->
  <div class="rounded-lg border border-gray-200 bg-white p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Available Reports</h3>
    <div class="space-y-2">
      <a href="<?= site_url('school/reports/payment-history') ?>" class="block p-3 hover:bg-gray-50 rounded border border-transparent hover:border-gray-200 text-indigo-600 font-medium">
        → Payment History Report
      </a>
      <a href="<?= site_url('school/reports/status-summary') ?>" class="block p-3 hover:bg-gray-50 rounded border border-transparent hover:border-gray-200 text-indigo-600 font-medium">
        → Payment Status Summary
      </a>
      <a href="<?= site_url('school/reports/export-payment-history') ?>" class="block p-3 hover:bg-gray-50 rounded border border-transparent hover:border-gray-200 text-indigo-600 font-medium">
        → Download as CSV
      </a>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
