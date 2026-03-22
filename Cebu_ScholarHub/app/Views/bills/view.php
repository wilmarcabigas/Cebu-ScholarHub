<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div class="max-w-5xl mx-auto space-y-6">

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
  <div class="flex items-start justify-between">
    <div>
      <h1 class="text-2xl font-semibold text-gray-800">
        <?= esc($batch['semester']) ?> — <?= esc($batch['school_year']) ?>
      </h1>
      <p class="text-sm text-gray-500 mt-1"><?= esc($batch['batch_label']) ?></p>
    </div>
    <div class="flex items-center gap-3">
      <?php if ($batch['status'] === 'draft'): ?>
        <a href="<?= site_url('school/billing/print/' . $batch['id']) ?>" target="_blank"
           class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
          Print Preview
        </a>
        <form method="post" action="<?= site_url('school/billing/submit/' . $batch['id']) ?>">
          <?= csrf_field() ?>
          <button class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
            Submit to CCCSP Office
          </button>
        </form>
      <?php elseif ($batch['status'] === 'submitted'): ?>
        <span class="rounded-full bg-blue-100 text-blue-700 px-3 py-1 text-sm font-semibold">
          Submitted — Awaiting Receipt
        </span>
        <a href="<?= site_url('school/billing/print/' . $batch['id']) ?>" target="_blank"
           class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
          Print
        </a>
      <?php else: ?>
        <?php
          $badgeClass = match($batch['status']) {
            'received' => 'bg-yellow-100 text-yellow-700',
            'partial'  => 'bg-orange-100 text-orange-700',
            'paid'     => 'bg-green-100 text-green-700',
            default    => 'bg-gray-100 text-gray-600',
          };
        ?>
        <span class="rounded-full <?= $badgeClass ?> px-3 py-1 text-sm font-semibold">
          <?= strtoupper($batch['status']) ?>
        </span>
        <a href="<?= site_url('school/billing/print/' . $batch['id']) ?>" target="_blank"
           class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
          Print
        </a>
      <?php endif; ?>
    </div>
  </div>

  <!-- Batch Summary Cards -->
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    <div class="rounded-lg bg-gray-50 p-4">
      <p class="text-xs text-gray-500 mb-1">Total Scholars</p>
      <p class="text-2xl font-semibold text-gray-800"><?= count($items) ?></p>
    </div>
    <div class="rounded-lg bg-gray-50 p-4">
      <p class="text-xs text-gray-500 mb-1">Total Amount</p>
      <p class="text-2xl font-semibold text-gray-800">₱<?= number_format($batch['total_amount'] ?? 0, 2) ?></p>
    </div>
    <div class="rounded-lg bg-gray-50 p-4">
      <p class="text-xs text-gray-500 mb-1">Amount Paid</p>
      <p class="text-2xl font-semibold text-green-700">₱<?= number_format($bill['amount_paid'] ?? 0, 2) ?></p>
    </div>
    <div class="rounded-lg bg-gray-50 p-4">
      <p class="text-xs text-gray-500 mb-1">Balance</p>
      <?php $balance = $batch['total_amount'] - ($bill['amount_paid'] ?? 0); ?>
      <p class="text-2xl font-semibold <?= $balance > 0 ? 'text-red-600' : 'text-green-700' ?>">
        ₱<?= number_format($balance, 2) ?>
      </p>
    </div>
  </div>

  <!-- Scholar List -->
  <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
      <h2 class="text-base font-semibold text-gray-700">Scholar List</h2>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-8">No.</th>
            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">ID Num</th>
            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">MI</th>
            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Course</th>
            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Yr</th>
            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Control No.</th>
            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">School</th>
            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Address / Barangay</th>
            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white">
          <?php foreach ($items as $i => $item): ?>
            <tr class="hover:bg-gray-50">
              <td class="px-3 py-2 text-gray-400"><?= $i + 1 ?></td>
              <td class="px-3 py-2 font-mono text-xs text-gray-600"><?= esc($item['id_num'] ?? '—') ?></td>
              <td class="px-3 py-2 font-medium text-gray-800">
                <?= esc($item['last_name'] . ', ' . $item['first_name']) ?>
              </td>
              <td class="px-3 py-2 text-gray-500"><?= esc($item['middle_name'][0] ?? '—') ?></td>
              <td class="px-3 py-2 text-gray-600"><?= esc($item['course'] ?? '—') ?></td>
              <td class="px-3 py-2 text-gray-600"><?= esc($item['year_level'] ?? '—') ?></td>
              <td class="px-3 py-2 font-mono text-xs text-gray-500"><?= esc($item['control_no'] ?? $item['scholar_control_no'] ?? '—') ?></td>
              <td class="px-3 py-2 text-xs text-gray-500"><?= esc($item['school_name'] ?? '—') ?></td>
              <td class="px-3 py-2 text-xs text-gray-500"><?= esc($item['address'] ?? $item['barangay'] ?? '—') ?></td>
              <td class="px-3 py-2 text-right font-semibold text-gray-700">₱<?= number_format($item['amount'], 2) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot class="bg-gray-50">
          <tr>
            <td colspan="9" class="px-3 py-3 text-right text-sm font-semibold text-gray-700">Total:</td>
            <td class="px-3 py-3 text-right text-sm font-bold text-gray-800">
              ₱<?= number_format($batch['total_amount'] ?? 0, 2) ?>
            </td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

  <div class="flex justify-start">
    <a href="<?= site_url('school/billing') ?>"
       class="text-sm text-gray-500 hover:text-gray-700">&larr; Back to Billing Records</a>
  </div>
</div>

<?= $this->endSection() ?>