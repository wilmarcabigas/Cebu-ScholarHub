<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div class="max-w-6xl mx-auto space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-semibold text-gray-800">Scholar Payment History</h1>
      <p class="text-sm text-gray-500">Detailed payment records for each scholar across all schools</p>
    </div>
    <a href="<?= site_url('admin/reports/export-scholar') ?>" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
      Export Report
    </a>
  </div>

  <div class="rounded-lg border border-gray-200 bg-white p-4 space-y-4">
    <form method="get" action="<?= site_url('admin/reports/scholar-payment-history') ?>" class="space-y-4">
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700">School</label>
          <select name="school_id" class="w-full rounded border border-gray-300 p-2 text-sm">
            <option value="">-- All Schools --</option>
            <?php foreach ($schools as $school): ?>
              <option value="<?= $school['id'] ?>" <?= isset($_GET['school_id']) && $_GET['school_id'] == $school['id'] ? 'selected' : '' ?>>
                <?= esc($school['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700">Payment Status</label>
          <select name="payment_status" class="w-full rounded border border-gray-300 p-2 text-sm">
            <option value="">-- All Status --</option>
            <option value="paid" <?= isset($_GET['payment_status']) && $_GET['payment_status'] === 'paid' ? 'selected' : '' ?>>Paid</option>
            <option value="partial" <?= isset($_GET['payment_status']) && $_GET['payment_status'] === 'partial' ? 'selected' : '' ?>>Partially Paid</option>
            <option value="unpaid" <?= isset($_GET['payment_status']) && $_GET['payment_status'] === 'unpaid' ? 'selected' : '' ?>>Unpaid</option>
          </select>
        </div>
        <div class="flex items-end">
          <button type="submit" class="w-full rounded-md bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-900">
            Filter Results
          </button>
        </div>
      </div>
    </form>
  </div>

  <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
    <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
      <p class="mb-1 text-xs font-medium text-blue-600">Total Scholars Billed</p>
      <p class="text-3xl font-bold text-blue-700"><?= $totalScholars ?? 0 ?></p>
    </div>
    <div class="rounded-lg border border-green-200 bg-green-50 p-4">
      <p class="mb-1 text-xs font-medium text-green-600">Fully Paid</p>
      <p class="text-3xl font-bold text-green-700"><?= $fullyPaidCount ?? 0 ?></p>
    </div>
    <div class="rounded-lg border border-red-200 bg-red-50 p-4">
      <p class="mb-1 text-xs font-medium text-red-600">Unpaid / Partial</p>
      <p class="text-3xl font-bold text-red-700"><?= ($totalScholars ?? 0) - ($fullyPaidCount ?? 0) ?></p>
    </div>
  </div>

  <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
    <table class="w-full text-sm">
      <thead class="border-b border-gray-200 bg-gray-50">
        <tr>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Scholar Name</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">School</th>
          <th class="px-4 py-2 text-left font-medium text-gray-600">Course / Year</th>
          <th class="px-4 py-2 text-right font-medium text-gray-600">Amount Due</th>
          <th class="px-4 py-2 text-right font-medium text-gray-600">Amount Paid</th>
          <th class="px-4 py-2 text-right font-medium text-gray-600">Balance</th>
          <th class="px-4 py-2 text-center font-medium text-gray-600">Payment Status</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        <?php if (empty($scholarPayments)): ?>
          <tr>
            <td colspan="7" class="px-4 py-4 text-center text-gray-500">No scholar payment records found</td>
          </tr>
        <?php else: ?>
          <?php foreach ($scholarPayments as $scholar): ?>
            <?php
              $amountDue = (float) ($scholar['amount_due'] ?? 0);
              $amountPaid = (float) ($scholar['amount_paid'] ?? 0);
              $balance = $amountDue - $amountPaid;
              $statusKey = $scholar['payment_status'] ?? 'unpaid';

              if ($statusKey === 'paid') {
                  $statusLabel = 'Fully Paid';
                  $statusClass = 'bg-green-100 text-green-800';
              } elseif ($statusKey === 'partial') {
                  $statusLabel = 'Partially Paid';
                  $statusClass = 'bg-yellow-100 text-yellow-800';
              } else {
                  $statusLabel = 'Unpaid';
                  $statusClass = 'bg-red-100 text-red-800';
              }
            ?>
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-2 font-medium text-gray-800"><?= esc($scholar['scholar_name'] ?? 'N/A') ?></td>
              <td class="px-4 py-2"><?= esc($scholar['school_name'] ?? 'N/A') ?></td>
              <td class="px-4 py-2"><?= esc($scholar['course'] ?? 'N/A') ?> / Year <?= esc($scholar['year_level'] ?? 'N/A') ?></td>
              <td class="px-4 py-2 text-right">₱<?= number_format($amountDue, 2) ?></td>
              <td class="px-4 py-2 text-right font-semibold text-green-600">₱<?= number_format($amountPaid, 2) ?></td>
              <td class="px-4 py-2 text-right font-semibold <?= $balance > 0 ? 'text-red-600' : 'text-green-600' ?>">₱<?= number_format($balance, 2) ?></td>
              <td class="px-4 py-2 text-center">
                <span class="inline-block rounded px-2 py-1 text-xs font-medium <?= $statusClass ?>">
                  <?= $statusLabel ?>
                </span>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    <div class="rounded-lg border border-gray-200 bg-white p-6">
      <h2 class="mb-4 text-lg font-semibold text-gray-800">Payment Status Distribution</h2>
      <div class="space-y-3">
        <?php
          $fullyPaid = $fullyPaidCount ?? 0;
          $totalScholarsCount = $totalScholars ?? 0;
          $unpaidPartial = $totalScholarsCount - $fullyPaid;

          $statuses = [
            ['label' => 'Fully Paid', 'count' => $fullyPaid, 'color' => 'bg-green-500'],
            ['label' => 'Pending', 'count' => $unpaidPartial, 'color' => 'bg-red-500'],
          ];
        ?>
        <?php foreach ($statuses as $status): ?>
          <?php $percentage = $totalScholarsCount > 0 ? round(($status['count'] / $totalScholarsCount) * 100) : 0; ?>
          <div>
            <div class="mb-1 flex justify-between text-sm">
              <span class="font-medium text-gray-700"><?= $status['label'] ?></span>
              <span class="text-gray-600"><?= $status['count'] ?> scholars (<?= $percentage ?>%)</span>
            </div>
            <div class="h-2 w-full rounded-full bg-gray-200">
              <div class="<?= $status['color'] ?> h-2 rounded-full" style="width: <?= $percentage ?>%"></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white p-6">
      <h2 class="mb-4 text-lg font-semibold text-gray-800">Collection Summary</h2>
      <div class="space-y-3">
        <?php
          $totalBilled = !empty($scholarPayments) ? array_sum(array_column($scholarPayments, 'amount_due')) : 0;
          $totalCollected = !empty($scholarPayments) ? array_sum(array_column($scholarPayments, 'amount_paid')) : 0;
          $outstanding = $totalBilled - $totalCollected;
          $collectionRate = $totalBilled > 0 ? round(($totalCollected / $totalBilled) * 100) : 0;
        ?>
        <div class="flex items-center justify-between border-b pb-3">
          <span class="text-gray-700">Total Billed:</span>
          <span class="font-semibold text-gray-800">₱<?= number_format($totalBilled, 0) ?></span>
        </div>
        <div class="flex items-center justify-between border-b pb-3">
          <span class="text-gray-700">Total Collected:</span>
          <span class="font-semibold text-green-600">₱<?= number_format($totalCollected, 0) ?></span>
        </div>
        <div class="flex items-center justify-between border-b pb-3">
          <span class="text-gray-700">Outstanding:</span>
          <span class="font-semibold text-red-600">₱<?= number_format($outstanding, 0) ?></span>
        </div>
        <div class="flex items-center justify-between pt-2">
          <span class="font-medium text-gray-800">Collection Rate:</span>
          <span class="text-lg font-bold"><?= $collectionRate ?>%</span>
        </div>
      </div>
    </div>
  </div>

  <div class="flex gap-2 pt-4">
    <a href="<?= site_url('admin/reports') ?>" class="inline-block text-sm font-medium text-blue-600 hover:text-blue-700">
      Back to Reports
    </a>
  </div>
</div>

<?= $this->endSection() ?>
