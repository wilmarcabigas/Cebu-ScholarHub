<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<div class="max-w-4xl mx-auto space-y-6">

  <!-- Billing Summary -->
  <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Billing Details</h2>

    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
      <div>
        <dt class="text-gray-500">Scholar</dt>
        <dd class="font-medium text-gray-800"><?= esc($bill['scholar_name']) ?></dd>
      </div>
      <div>
        <dt class="text-gray-500">Billing Period</dt>
        <dd class="font-medium text-gray-800"><?= esc($bill['billing_period']) ?></dd>
      </div>
      <div>
        <dt class="text-gray-500">Amount Due</dt>
        <dd class="font-semibold text-gray-800">₱<?= number_format($bill['amount_due'],2) ?></dd>
      </div>
      <div>
        <dt class="text-gray-500">Status</dt>
        <dd class="font-medium"><?= strtoupper($bill['status']) ?></dd>
      </div>
    </dl>
  </div>

  <!-- Payment Form -->
  <?php if ($bill['status'] !== 'paid' && in_array(auth_user()['role'], ['school_admin','school_staff'])): ?>
    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
      <h3 class="text-lg font-semibold text-gray-800 mb-4">Post Payment</h3>

      <form method="post" action="<?= site_url('school/payments/store') ?>" class="space-y-4">
        <?= csrf_field() ?>
        <input type="hidden" name="bill_id" value="<?= $bill['id'] ?>">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Amount Paid</label>
            <input type="number" name="amount_paid" required
                   class="w-full rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Payment Date</label>
            <input type="date" name="payment_date" required
                   class="w-full rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
          <textarea name="remarks" rows="3"
                    class="w-full rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
        </div>

        <div class="flex justify-end">
          <button class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
            Save Payment
          </button>
        </div>
      </form>
    </div>
  <?php endif; ?>

  <!-- Payment History -->
  <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Payment History</h3>

    <table class="min-w-full divide-y divide-gray-200 text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
          <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
          <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Remarks</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        <?php if (empty($payments)): ?>
          <tr>
            <td colspan="3" class="px-3 py-4 text-center text-gray-500">
              No payments recorded.
            </td>
          </tr>
        <?php endif; ?>

        <?php foreach ($payments as $p): ?>
          <tr>
            <td class="px-3 py-2"><?= esc($p['payment_date']) ?></td>
            <td class="px-3 py-2 text-right font-medium">
              ₱<?= number_format($p['amount_paid'],2) ?>
            </td>
            <td class="px-3 py-2"><?= esc($p['remarks']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

</div>

<?= $this->endSection() ?>
