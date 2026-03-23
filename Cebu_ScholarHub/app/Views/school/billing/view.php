<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div class="max-w-6xl mx-auto space-y-6">

  <?php if (session()->getFlashdata('success')): ?>
    <div class="rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
      <?= session()->getFlashdata('success') ?>
    </div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('error')): ?>
    <div class="rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
      <?= session()->getFlashdata('error') ?>
    </div>
  <?php endif; ?>

  <!-- Header -->
  <div class="border-b border-gray-200 pb-4">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-gray-900"><?= esc($batch['batch_label']) ?></h1>
        <p class="mt-1 text-sm text-gray-500"><?= esc($batch['semester']) ?> • <?= esc($batch['school_year']) ?></p>
      </div>
      <div class="text-right">
        <?php
          $badgeClass = match($batch['status']) {
            'draft'     => 'bg-gray-100 text-gray-800',
            'submitted' => 'bg-blue-100 text-blue-800',
            'received'  => 'bg-yellow-100 text-yellow-800',
            'partial'   => 'bg-orange-100 text-orange-800',
            'paid'      => 'bg-green-100 text-green-800',
            default     => 'bg-gray-100 text-gray-800',
          };
        ?>
        <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold <?= $badgeClass ?>">
          <?= ucfirst($batch['status']) ?>
        </span>
      </div>
    </div>
  </div>

  <!-- Rejection Banner -->
  <?php if ($batch['status'] === 'draft' && !empty($batch['rejection_remarks'])): ?>
    <div class="rounded-lg border border-red-300 bg-red-50 p-5">
      <div class="flex items-start gap-3">
        <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
        <div>
          <p class="text-sm font-semibold text-red-800 mb-1">This billing was rejected by the office.</p>
          <p class="text-sm text-red-700"><?= esc($batch['rejection_remarks']) ?></p>
          <p class="text-xs text-red-500 mt-2">Please edit the billing to address the issue, then resubmit.</p>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- Receipt Confirmation Banner -->
  <?php if (!empty($batch['receipt_confirmed_at'])): ?>
    <div class="rounded-lg border border-green-300 bg-green-50 px-5 py-4 flex items-center gap-3">
      <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      <p class="text-sm text-green-800 font-medium">
        Your school confirmed receipt of payment on
        <strong><?= date('F d, Y', strtotime($batch['receipt_confirmed_at'])) ?></strong>.
      </p>
    </div>
  <?php endif; ?>

  <!-- Summary Cards -->
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    <div class="rounded-lg border border-gray-200 bg-white p-4">
      <p class="text-xs font-semibold text-gray-500 uppercase">Total Amount</p>
      <p class="text-2xl font-bold text-gray-900">₱<?= number_format($batch['total_amount'], 2) ?></p>
    </div>
    <div class="rounded-lg border border-gray-200 bg-white p-4">
      <p class="text-xs font-semibold text-gray-500 uppercase">Scholars</p>
      <p class="text-2xl font-bold text-gray-900"><?= count($items) ?></p>
    </div>
    <div class="rounded-lg border border-gray-200 bg-white p-4">
      <p class="text-xs font-semibold text-gray-500 uppercase">Amount Received</p>
      <p class="text-2xl font-bold text-green-700">₱<?= number_format($totalAmountPaid, 2) ?></p>
    </div>
    <div class="rounded-lg border border-gray-200 bg-white p-4">
      <p class="text-xs font-semibold text-gray-500 uppercase">Balance</p>
      <?php $balance = $batch['total_amount'] - $totalAmountPaid; ?>
      <p class="text-2xl font-bold <?= $balance > 0 ? 'text-red-600' : 'text-green-700' ?>">
        ₱<?= number_format($balance, 2) ?>
      </p>
    </div>
  </div>

  <!-- Confirm Receipt Section -->
  <?php if (in_array($batch['status'], ['partial', 'paid']) && empty($batch['receipt_confirmed_at'])): ?>
    <div class="rounded-lg border border-indigo-200 bg-indigo-50 p-5 flex items-center justify-between gap-4">
      <div>
        <p class="text-sm font-semibold text-indigo-800">Payment has been recorded by the office.</p>
        <p class="text-xs text-indigo-600 mt-1">
          Please confirm that your school has received the payment of
          <strong>₱<?= number_format($totalAmountPaid, 2) ?></strong>.
        </p>
      </div>
      <form method="post" action="<?= site_url('school/billing/confirm-receipt/' . $batch['id']) ?>"
            onsubmit="return confirm('Confirm that your school has received this payment?')">
        <?= csrf_field() ?>
        <button type="submit"
                class="whitespace-nowrap rounded-md bg-indigo-600 px-5 py-2 text-sm font-medium text-white hover:bg-indigo-700">
          Confirm Receipt
        </button>
      </form>
    </div>
  <?php endif; ?>

  <!-- Payment History -->
  <?php if (!empty($payments)): ?>
    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
      <h3 class="text-base font-semibold text-gray-800 mb-4">
        Payment History
        <span class="ml-2 text-sm font-normal text-gray-500">
          (Total: ₱<?= number_format(array_sum(array_column($payments, 'amount_paid')), 2) ?>)
        </span>
      </h3>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
              <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
              <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Voucher / Check No.</th>
              <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Remarks</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <?php foreach ($payments as $p): ?>
              <tr>
                <td class="px-3 py-2"><?= date('M d, Y', strtotime($p['payment_date'])) ?></td>
                <td class="px-3 py-2 text-right font-medium text-green-700">₱<?= number_format($p['amount_paid'], 2) ?></td>
                <td class="px-3 py-2 font-mono text-xs text-gray-600"><?= esc($p['voucher_no'] ?? '—') ?></td>
                <td class="px-3 py-2 text-gray-500"><?= esc($p['remarks'] ?? '—') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php endif; ?>

  <!-- Billing Items Table -->
  <div class="rounded-lg border border-gray-200 bg-white shadow-sm mb-6 overflow-hidden">
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
      <h2 class="text-lg font-semibold text-gray-900">Scholars Included</h2>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ID Num</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Name</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">MI</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Course</th>
            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Yr</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Control No.</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Address / Barangay</th>
            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Amount</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <?php foreach ($items as $item): ?>
            <tr class="hover:bg-gray-50">
              <td class="px-6 py-4 text-sm font-mono text-gray-600"><?= esc($item['id_num'] ?? '—') ?></td>
              <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= esc($item['last_name'] . ', ' . $item['first_name']) ?></td>
              <td class="px-6 py-4 text-sm text-gray-600"><?= esc(substr($item['middle_name'] ?? '', 0, 1)) ?></td>
              <td class="px-6 py-4 text-sm text-gray-600"><?= esc($item['course'] ?? '—') ?></td>
              <td class="px-6 py-4 text-sm text-center font-semibold text-gray-700"><?= esc($item['year_level'] ?? '—') ?></td>
              <td class="px-6 py-4 text-sm font-mono text-gray-500"><?= esc($item['control_no'] ?? '—') ?></td>
              <td class="px-6 py-4 text-sm text-gray-600"><?= esc(trim(($item['address'] ?? '') . ' ' . ($item['barangay'] ?? ''))) ?></td>
              <td class="px-6 py-4 text-sm font-semibold text-gray-900 text-right">₱<?= number_format($item['amount'], 2) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 text-right">
      <p class="text-sm text-gray-600 mb-1">Total Billing Amount:</p>
      <p class="text-2xl font-bold text-indigo-600">₱<?= number_format($batch['total_amount'], 2) ?></p>
    </div>
  </div>

  <!-- Action Buttons -->
  <div class="flex justify-between items-center">
    <a href="<?= site_url('school/billing') ?>"
       class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
      Back to Billings
    </a>

    <div class="flex gap-3">
      <a href="<?= site_url('school/billing/' . $batch['id'] . '/print') ?>" target="_blank"
         class="px-4 py-2 text-sm font-medium text-white bg-gray-600 rounded-md hover:bg-gray-700">
        Print
      </a>

      <?php if ($batch['status'] === 'draft'): ?>
        <a href="<?= site_url('school/billing/edit/' . $batch['id']) ?>"
           class="px-4 py-2 text-sm font-medium text-white bg-amber-500 rounded-md hover:bg-amber-600">
          Edit
        </a>

        <form method="post" action="<?= site_url('school/billing/submit/' . $batch['id']) ?>"
              class="inline" onsubmit="return confirm('Submit this billing to the office?')">
          <?= csrf_field() ?>
          <button type="submit"
                  class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">
            Submit to Office
          </button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
