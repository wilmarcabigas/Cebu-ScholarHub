<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-2xl font-semibold text-gray-800">Billing Records</h1>
    <p class="text-sm text-gray-500">Invoice-style billing records per scholar</p>
  </div>

  <?php if (in_array(auth_user()['role'], ['school_admin','school_staff'])): ?>
    <a href="<?= site_url('school/billing/create') ?>"
       class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
      + Post Billing
    </a>
  <?php endif; ?>
</div>

<div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
  <table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
      <tr>
        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Scholar</th>
        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Billing Period</th>
        <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">Amount</th>
        <th class="px-4 py-3 text-center text-xs font-medium uppercase text-gray-500">Status</th>
        <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">Action</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-100 bg-white">
      <?php if (empty($bills)): ?>
        <tr>
          <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">
            No billing records found.
          </td>
        </tr>
      <?php endif; ?>

      <?php foreach ($bills as $bill): ?>
        <tr class="hover:bg-gray-50">
          <td class="px-4 py-3 text-sm font-medium text-gray-800">
            <?= esc($bill['scholar_name']) ?>
          </td>
          <td class="px-4 py-3 text-sm text-gray-600">
            <?= esc($bill['billing_period']) ?>
          </td>
          <td class="px-4 py-3 text-right text-sm font-semibold text-gray-800">
            ₱<?= number_format($bill['amount_due'], 2) ?>
          </td>
          <td class="px-4 py-3 text-center">
            <?php
              $badge = match ($bill['status']) {
    'paid'    => 'bg-green-100 text-green-700',
    'partial' => 'bg-yellow-100 text-yellow-700',
    'overdue' => 'bg-red-100 text-red-700',
    default   => 'bg-gray-100 text-gray-500',
};
            ?>
            <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold <?= $badge ?>">
              <?= strtoupper($bill['status']) ?>
            </span>
          </td>
          <td class="px-4 py-3 text-right">
            <a href="<?= site_url('school/billing/view/'.$bill['id']) ?>"
               class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
              View
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?= $this->endSection() ?>
