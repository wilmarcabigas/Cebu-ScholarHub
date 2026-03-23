<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-2xl font-semibold text-gray-800">Billing Records</h1>
    <p class="text-sm text-gray-500">Semester billing submissions to the CCCSP Office</p>
  </div>
  <?php if (in_array(auth_user()['role'], ['school_admin', 'school_staff'])): ?>
    <a href="<?= site_url('school/billing/create') ?>"
       class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
      + Create Billing
    </a>
  <?php endif; ?>
</div>

<?php if (session()->getFlashdata('success')): ?>
  <div class="mb-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
    <?= session()->getFlashdata('success') ?>
  </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
  <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
    <?= session()->getFlashdata('error') ?>
  </div>
<?php endif; ?>

<div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
  <table class="min-w-full divide-y divide-gray-200 text-sm">
    <thead class="bg-gray-50">
      <tr>
        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Semester</th>
        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">School Year</th>
        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Batch Label</th>
        <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">Total Amount</th>
        <th class="px-4 py-3 text-center text-xs font-medium uppercase text-gray-500">Status</th>
        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Submitted</th>
        <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">Action</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-100 bg-white">
      <?php if (empty($batches)): ?>
        <tr>
          <td colspan="7" class="px-4 py-8 text-center text-gray-400">No billing records yet.</td>
        </tr>
      <?php endif; ?>
      <?php foreach ($batches as $b): ?>
        <?php
          $badgeClass = match($b['status']) {
            'draft'     => 'bg-gray-100 text-gray-600',
            'submitted' => 'bg-blue-100 text-blue-700',
            'received'  => 'bg-yellow-100 text-yellow-700',
            'partial'   => 'bg-orange-100 text-orange-700',
            'paid'      => 'bg-green-100 text-green-700',
            default     => 'bg-gray-100 text-gray-500',
          };
        ?>
        <tr class="hover:bg-gray-50">
          <td class="px-4 py-3 font-medium text-gray-800"><?= esc($b['semester']) ?></td>
          <td class="px-4 py-3 text-gray-600"><?= esc($b['school_year']) ?></td>
          <td class="px-4 py-3 text-gray-600"><?= esc($b['batch_label']) ?></td>
          <td class="px-4 py-3 text-right font-semibold text-gray-800">
            ₱<?= number_format($b['total_amount'], 2) ?>
          </td>
          <td class="px-4 py-3 text-center">
            <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold <?= $badgeClass ?>">
              <?= strtoupper($b['status']) ?>
            </span>
          </td>
          <td class="px-4 py-3 text-gray-500 text-xs">
            <?= $b['submitted_at'] ? date('M d, Y', strtotime($b['submitted_at'])) : '—' ?>
          </td>
          <td class="px-4 py-3 text-right space-x-2">
            <a href="<?= site_url('school/billing/view/' . $b['id']) ?>"
               class="text-indigo-600 hover:text-indigo-800 font-medium">View</a>
            <?php if ($b['status'] === 'draft'): ?>
              <a href="<?= site_url('school/billing/print/' . $b['id']) ?>" target="_blank"
                 class="text-gray-500 hover:text-gray-700">Print</a>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?= $this->endSection() ?>