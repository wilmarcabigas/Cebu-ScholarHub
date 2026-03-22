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

  <!-- Page Header -->
  <div class="flex items-start justify-between">
    <div>
      <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-1">
        <?= esc($batch['school_name']) ?>
      </p>
      <h1 class="text-2xl font-semibold text-gray-800">
        <?= esc($batch['semester']) ?> — <?= esc($batch['school_year']) ?>
      </h1>
      <p class="text-sm text-gray-500"><?= esc($batch['batch_label']) ?></p>
    </div>
    <div class="flex items-center gap-3">
      <?php
        $badgeClass = match($batch['status']) {
          'draft'     => 'bg-gray-100 text-gray-700',
          'submitted' => 'bg-blue-100 text-blue-700',
          'received'  => 'bg-yellow-100 text-yellow-700',
          'partial'   => 'bg-orange-100 text-orange-700',
          'paid'      => 'bg-green-100 text-green-700',
          default     => 'bg-gray-100 text-gray-500',
        };
      ?>
      <span class="rounded-full px-3 py-1 text-xs font-semibold <?= $badgeClass ?>">
        <?= strtoupper($batch['status']) ?>
      </span>
      <a href="<?= site_url('admin/billing/print/' . $batch['id']) ?>" target="_blank"
         class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
        Print Billing Sheet
      </a>
    </div>
  </div>

  <!-- Receipt Confirmation Banner -->
  <?php if (!empty($batch['receipt_confirmed_at'])): ?>
    <div class="rounded-lg border border-green-300 bg-green-50 px-5 py-4 flex items-center gap-3">
      <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      <p class="text-sm text-green-800 font-medium">
        School confirmed receipt of payment on
        <strong><?= date('F d, Y \a\t h:i A', strtotime($batch['receipt_confirmed_at'])) ?></strong>.
      </p>
    </div>
  <?php endif; ?>

  <!-- Summary Cards -->
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    <div class="rounded-lg bg-gray-50 p-4">
      <p class="text-xs text-gray-500 mb-1">Scholars Billed</p>
      <p class="text-2xl font-semibold text-gray-800"><?= count($items) ?></p>
    </div>
    <div class="rounded-lg bg-gray-50 p-4">
      <p class="text-xs text-gray-500 mb-1">Total Amount Due</p>
      <?php $amountDue = !empty($bills) ? array_sum(array_column($bills, 'amount_due')) : $batch['total_amount']; ?>
      <p class="text-2xl font-semibold text-gray-800">₱<?= number_format($amountDue, 2) ?></p>
    </div>
    <div class="rounded-lg bg-gray-50 p-4">
      <p class="text-xs text-gray-500 mb-1">Amount Paid</p>
      <p class="text-2xl font-semibold text-green-700">₱<?= number_format($totalAmountPaid, 2) ?></p>
    </div>
    <div class="rounded-lg bg-gray-50 p-4">
      <p class="text-xs text-gray-500 mb-1">Balance</p>
      <?php $balance = $amountDue - $totalAmountPaid; ?>
      <p class="text-2xl font-semibold <?= $balance > 0 ? 'text-red-600' : 'text-green-700' ?>">
        ₱<?= number_format($balance, 2) ?>
      </p>
    </div>
  </div>

  <!-- RECEIVE BILLING (submitted → received) -->
  <?php if ($batch['status'] === 'submitted'): ?>
    <div class="rounded-lg border border-blue-200 bg-blue-50 p-5 space-y-4">
      <h3 class="text-sm font-semibold text-blue-800">
        This billing has been submitted by the school. Set a due date and receive it to post official bills.
      </h3>

      <!-- Receive form -->
      <form method="post" action="<?= site_url('admin/billing/receive/' . $batch['id']) ?>" class="flex items-end gap-4">
        <?= csrf_field() ?>
        <div>
          <label class="block text-sm font-medium text-blue-800 mb-1">Due Date <span class="text-red-500">*</span></label>
          <input type="date" name="due_date" required
                 class="rounded-md border-blue-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
        </div>
        <button type="submit"
                class="rounded-md bg-indigo-600 px-5 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                onclick="return confirm('Receive this billing and post official bills for all scholars?')">
          Receive &amp; Post Bills
        </button>
      </form>

      <!-- Reject form -->
      <div class="border-t border-blue-200 pt-4">
        <p class="text-xs font-semibold text-red-700 mb-2">Reject this billing (requires reason)</p>
        <form method="post" action="<?= site_url('admin/billing/reject/' . $batch['id']) ?>"
              onsubmit="return validateReject(this)">
          <?= csrf_field() ?>
          <div class="flex items-end gap-3">
            <div class="flex-1">
              <textarea name="rejection_remarks" rows="2" minlength="10" required
                        placeholder="State the reason for rejection (minimum 10 characters)..."
                        class="w-full rounded-md border-red-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm px-3 py-2"></textarea>
            </div>
            <button type="submit"
                    class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
              Reject Billing
            </button>
          </div>
        </form>
      </div>
    </div>
  <?php endif; ?>

  <!-- RECORD PAYMENT (received or partial) -->
  <?php if (in_array($batch['status'], ['received', 'partial'])): ?>
    <div class="rounded-lg border border-indigo-200 bg-indigo-50 p-5">
      <h3 class="text-sm font-semibold text-indigo-800 mb-4">Record Payment</h3>
      <?php
        $totalDue       = array_sum(array_column($bills, 'amount_due'));
        $totalPaid      = array_sum(array_column($bills, 'amount_paid'));
        $totalRemaining = $totalDue - $totalPaid;
      ?>
      <p class="text-xs text-indigo-600 mb-3">
        Remaining balance: <strong>₱<?= number_format($totalRemaining, 2) ?></strong>
      </p>
      <form method="post" action="<?= site_url('admin/billing/record-payment/' . $batch['id']) ?>"
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <?= csrf_field() ?>
        <div>
          <label class="block text-xs font-medium text-indigo-800 mb-1">Amount Paid <span class="text-red-500">*</span></label>
          <input type="number" name="amount_paid" step="0.01" min="0.01"
                 max="<?= $totalRemaining ?>" required
                 placeholder="e.g. 210000"
                 class="w-full rounded-md border-indigo-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2">
        </div>
        <div>
          <label class="block text-xs font-medium text-indigo-800 mb-1">Payment Date <span class="text-red-500">*</span></label>
          <input type="date" name="payment_date" required
                 value="<?= date('Y-m-d') ?>"
                 class="w-full rounded-md border-indigo-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2">
        </div>
        <div>
          <label class="block text-xs font-medium text-indigo-800 mb-1">Voucher / Check No.</label>
          <input type="text" name="voucher_no" placeholder="e.g. CHK-2026-001"
                 class="w-full rounded-md border-indigo-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2">
        </div>
        <div>
          <label class="block text-xs font-medium text-indigo-800 mb-1">Remarks</label>
          <input type="text" name="remarks" placeholder="Optional"
                 class="w-full rounded-md border-indigo-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2">
        </div>
        <div class="sm:col-span-2 lg:col-span-4 text-right">
          <button type="submit"
                  class="rounded-md bg-indigo-600 px-6 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                  onclick="return confirm('Record this payment?')">
            Record Payment
          </button>
        </div>
      </form>
    </div>
  <?php endif; ?>

  <!-- Payment History -->
  <?php if (!empty($payments)): ?>
    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
      <h3 class="text-base font-semibold text-gray-800 mb-4">
        Payment History
        <span class="ml-2 text-sm font-normal text-gray-500">
          (Total recorded: ₱<?= number_format(array_sum(array_column($payments, 'amount_paid')), 2) ?>)
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
              <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Recorded By</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <?php foreach ($payments as $p): ?>
              <tr>
                <td class="px-3 py-2"><?= date('M d, Y', strtotime($p['payment_date'])) ?></td>
                <td class="px-3 py-2 text-right font-medium text-green-700">₱<?= number_format($p['amount_paid'], 2) ?></td>
                <td class="px-3 py-2 font-mono text-xs text-gray-600"><?= esc($p['voucher_no'] ?? '—') ?></td>
                <td class="px-3 py-2 text-gray-500"><?= esc($p['remarks'] ?? '—') ?></td>
                <td class="px-3 py-2 text-gray-500"><?= esc($p['updated_by_name'] ?? '—') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php else: ?>
    <?php if (in_array($batch['status'], ['received', 'partial', 'paid'])): ?>
      <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4">
        <p class="text-sm text-yellow-800">No payments recorded yet for this batch.</p>
      </div>
    <?php endif; ?>
  <?php endif; ?>

  <!-- Scholar List -->
  <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
      <h3 class="text-base font-semibold text-gray-700">Scholar Billing Details</h3>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">ID Num</th>
            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">MI</th>
            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Course</th>
            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Yr</th>
            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Control No.</th>
            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Barangay</th>
            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <?php foreach ($items as $i => $item): ?>
            <tr class="hover:bg-gray-50">
              <td class="px-3 py-2 text-gray-400"><?= $i + 1 ?></td>
              <td class="px-3 py-2 font-mono text-xs text-gray-600"><?= esc($item['id_num'] ?? '—') ?></td>
              <td class="px-3 py-2 font-medium text-gray-800"><?= esc($item['last_name'] . ', ' . $item['first_name']) ?></td>
              <td class="px-3 py-2 text-gray-500"><?= esc($item['middle_name'][0] ?? '—') ?></td>
              <td class="px-3 py-2 text-gray-600"><?= esc($item['course'] ?? '—') ?></td>
              <td class="px-3 py-2 text-gray-600"><?= esc($item['year_level'] ?? '—') ?></td>
              <td class="px-3 py-2 font-mono text-xs text-gray-500"><?= esc($item['control_no'] ?? $item['scholar_control_no'] ?? '—') ?></td>
              <td class="px-3 py-2 text-xs text-gray-500"><?= esc($item['barangay'] ?? '—') ?></td>
              <td class="px-3 py-2 text-right font-semibold text-gray-700">₱<?= number_format($item['amount'], 2) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot class="bg-gray-50">
          <tr>
            <td colspan="8" class="px-3 py-3 text-right text-sm font-semibold text-gray-700">Total:</td>
            <td class="px-3 py-3 text-right text-sm font-bold text-gray-800">₱<?= number_format($batch['total_amount'], 2) ?></td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

  <div>
    <a href="<?= site_url('admin/billing') ?>" class="text-sm text-gray-500 hover:text-gray-700">
      &larr; Back to All Billings
    </a>
  </div>
</div>

<script>
function validateReject(form) {
  const remarks = form.querySelector('[name="rejection_remarks"]').value.trim();
  if (remarks.length < 10) {
    alert('Please provide a rejection reason (minimum 10 characters).');
    return false;
  }
  return confirm('Reject this billing? The school will be notified to revise and resubmit.');
}
</script>

<?= $this->endSection() ?>
