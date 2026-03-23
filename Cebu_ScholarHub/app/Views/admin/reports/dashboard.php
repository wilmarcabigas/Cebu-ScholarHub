<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<?php $collectionRate = $totalBilled > 0 ? round(($totalPaid / $totalBilled) * 100, 1) : 0; ?>

<div class="space-y-6">
  <div class="border-b border-gray-200 pb-4">
    <h1 class="text-3xl font-bold text-gray-900">Reports</h1>
    <p class="mt-1 text-sm text-gray-500">Office-wide billing, payment, and scholar report summary.</p>
  </div>

  <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
    <div class="rounded-lg border border-gray-200 bg-white p-6">
      <p class="text-sm font-medium text-gray-600">Total Billed</p>
      <p class="mt-2 text-3xl font-bold text-gray-900">₱<?= number_format($totalBilled, 2) ?></p>
    </div>
    <div class="rounded-lg border border-gray-200 bg-white p-6">
      <p class="text-sm font-medium text-gray-600">Total Paid</p>
      <p class="mt-2 text-3xl font-bold text-green-600">₱<?= number_format($totalPaid, 2) ?></p>
    </div>
    <div class="rounded-lg border border-gray-200 bg-white p-6">
      <p class="text-sm font-medium text-gray-600">Outstanding</p>
      <p class="mt-2 text-3xl font-bold text-red-600">₱<?= number_format($totalBalance, 2) ?></p>
    </div>
    <div class="rounded-lg border border-gray-200 bg-white p-6">
      <p class="text-sm font-medium text-gray-600">Collection Rate</p>
      <p class="mt-2 text-3xl font-bold text-indigo-600"><?= $collectionRate ?>%</p>
    </div>
  </div>

  <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    <div class="rounded-lg border border-gray-200 bg-white p-6">
      <h2 class="mb-4 text-lg font-semibold text-gray-900">Financial Reports</h2>
      <div class="space-y-2">
        <a href="<?= site_url('admin/reports/payment-status') ?>" class="block rounded border border-transparent p-3 font-medium text-indigo-600 hover:border-indigo-200 hover:bg-gray-50">
          Payment Status by School
        </a>
        <a href="<?= site_url('admin/reports/financial-report') ?>" class="block rounded border border-transparent p-3 font-medium text-indigo-600 hover:border-indigo-200 hover:bg-gray-50">
          Comprehensive Financial Report
        </a>
        <a href="<?= site_url('admin/reports/export-financial') ?>" class="block rounded border border-transparent p-3 font-medium text-indigo-600 hover:border-indigo-200 hover:bg-gray-50">
          Export Financial Report (CSV)
        </a>
      </div>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white p-6">
      <h2 class="mb-4 text-lg font-semibold text-gray-900">Scholar Reports</h2>
      <div class="space-y-2">
        <a href="<?= site_url('admin/reports/scholar-payment-history') ?>" class="block rounded border border-transparent p-3 font-medium text-indigo-600 hover:border-indigo-200 hover:bg-gray-50">
          Scholar Payment History
        </a>
        <a href="<?= site_url('admin/reports/billing-sheets') ?>" class="block rounded border border-transparent p-3 font-medium text-indigo-600 hover:border-indigo-200 hover:bg-gray-50">
          All Billing Sheets
        </a>
        <a href="<?= site_url('admin/reports/export-scholar') ?>" class="block rounded border border-transparent p-3 font-medium text-indigo-600 hover:border-indigo-200 hover:bg-gray-50">
          Export Scholar Report (CSV)
        </a>
      </div>
    </div>
  </div>

  <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
      <h2 class="text-lg font-semibold text-gray-900">Payment Summary by School</h2>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600">School</th>
            <th class="px-6 py-3 text-right text-xs font-semibold uppercase text-gray-600">Bills</th>
            <th class="px-6 py-3 text-right text-xs font-semibold uppercase text-gray-600">Total Due</th>
            <th class="px-6 py-3 text-right text-xs font-semibold uppercase text-gray-600">Total Paid</th>
            <th class="px-6 py-3 text-right text-xs font-semibold uppercase text-gray-600">Balance</th>
            <th class="px-6 py-3 text-center text-xs font-semibold uppercase text-gray-600">Collection %</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <?php if (empty($schoolSummary)): ?>
            <tr>
              <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">No billing data available.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($schoolSummary as $summary): ?>
              <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= esc($summary['school_name']) ?></td>
                <td class="px-6 py-4 text-right text-sm text-gray-600"><?= $summary['bills_count'] ?></td>
                <td class="px-6 py-4 text-right text-sm font-semibold text-gray-900">₱<?= number_format($summary['amount_due'], 2) ?></td>
                <td class="px-6 py-4 text-right text-sm font-semibold text-green-600">₱<?= number_format($summary['amount_paid'], 2) ?></td>
                <td class="px-6 py-4 text-right text-sm font-semibold text-red-600">₱<?= number_format($summary['balance'], 2) ?></td>
                <td class="px-6 py-4 text-center text-sm font-semibold text-indigo-600"><?= $summary['collection_rate'] ?>%</td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
      <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
        <h2 class="text-lg font-semibold text-gray-900">Recent Scholar Billing Records</h2>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Scholar</th>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">School</th>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Semester</th>
              <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-600">Due</th>
              <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-600">Paid</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <?php if (empty($recentScholarPayments)): ?>
              <tr>
                <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">No scholar billing records found.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($recentScholarPayments as $row): ?>
                <tr class="hover:bg-gray-50">
                  <td class="px-4 py-3 text-sm text-gray-900">
                    <div class="font-medium"><?= esc($row['scholar_name']) ?></div>
                    <div class="text-xs text-gray-500"><?= esc($row['id_num'] ?: 'No ID number') ?></div>
                  </td>
                  <td class="px-4 py-3 text-sm text-gray-600"><?= esc($row['school_name']) ?></td>
                  <td class="px-4 py-3 text-sm text-gray-600"><?= esc($row['semester'] . ' / ' . $row['school_year']) ?></td>
                  <td class="px-4 py-3 text-right text-sm font-medium text-gray-900">₱<?= number_format($row['amount_due'], 2) ?></td>
                  <td class="px-4 py-3 text-right text-sm font-medium <?= $row['amount_paid'] > 0 ? 'text-green-600' : 'text-gray-500' ?>">
                    ₱<?= number_format($row['amount_paid'], 2) ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
      <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
        <h2 class="text-lg font-semibold text-gray-900">Recent Bill Records</h2>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">School</th>
              <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-600">Amount Due</th>
              <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-600">Amount Paid</th>
              <th class="px-4 py-3 text-center text-xs font-semibold uppercase text-gray-600">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <?php if (empty($recentBills)): ?>
              <tr>
                <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">No bills recorded yet.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($recentBills as $bill): ?>
                <?php
                  $statusClass = match ($bill['status'] ?? '') {
                    'paid' => 'bg-green-100 text-green-700',
                    'partial' => 'bg-yellow-100 text-yellow-700',
                    default => 'bg-red-100 text-red-700',
                  };
                ?>
                <tr class="hover:bg-gray-50">
                  <td class="px-4 py-3 text-sm font-medium text-gray-900"><?= esc($bill['school_name']) ?></td>
                  <td class="px-4 py-3 text-right text-sm text-gray-700">₱<?= number_format($bill['amount_due'], 2) ?></td>
                  <td class="px-4 py-3 text-right text-sm text-green-600">₱<?= number_format($bill['amount_paid'], 2) ?></td>
                  <td class="px-4 py-3 text-center text-sm">
                    <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold <?= $statusClass ?>">
                      <?= strtoupper($bill['status']) ?>
                    </span>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
