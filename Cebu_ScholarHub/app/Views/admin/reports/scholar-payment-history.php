<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div class="max-w-6xl mx-auto space-y-6">
  <!-- Page Header -->
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-semibold text-gray-800">Scholar Payment History</h1>
      <p class="text-sm text-gray-500">Detailed payment records for each scholar across all schools</p>
    </div>
    <a href="<?= site_url('admin/reports/export-scholar') ?>" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
      Export Report
    </a>
  </div>

  <!-- Filters -->
  <div class="rounded-lg border border-gray-200 bg-white p-4 space-y-4">
    <form method="get" action="<?= site_url('admin/reports/scholar-payment-history') ?>" class="space-y-4">
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">School</label>
          <select name="school_id" class="w-full rounded text-sm border border-gray-300 p-2">
            <option value="">-- All Schools --</option>
            <?php foreach ($schools as $school): ?>
              <option value="<?= $school['id'] ?>" <?= isset($_GET['school_id']) && $_GET['school_id'] == $school['id'] ? 'selected' : '' ?>>
                <?= esc($school['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Payment Status</label>
          <select name="payment_status" class="w-full rounded text-sm border border-gray-300 p-2">
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

  <!-- Summary Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="rounded-lg bg-blue-50 border border-blue-200 p-4">
      <p class="text-xs text-blue-600 font-medium mb-1">Total Scholars Billed</p>
      <p class="text-3xl font-bold text-blue-700"><?= $totalScholars ?? 0 ?></p>
    </div>
    <div class="rounded-lg bg-green-50 border border-green-200 p-4">
      <p class="text-xs text-green-600 font-medium mb-1">Fully Paid</p>
      <p class="text-3xl font-bold text-green-700"><?= $fullyPaidCount ?? 0 ?></p>
    </div>
    <div class="rounded-lg bg-red-50 border border-red-200 p-4">
      <p class="text-xs text-red-600 font-medium mb-1">Unpaid / Partial</p>
      <p class="text-3xl font-bold text-red-700"><?= ($totalScholars ?? 0) - ($fullyPaidCount ?? 0) ?></p>
    </div>
  </div>

  <!-- Scholar Payment Records Table -->
  <div class="rounded-lg border border-gray-200 bg-white overflow-hidden">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 border-b border-gray-200">
        <tr>
          <th class="px-4 py-2 text-left text-gray-600 font-medium">Scholar Name</th>
          <th class="px-4 py-2 text-left text-gray-600 font-medium">School</th>
          <th class="px-4 py-2 text-left text-gray-600 font-medium">Course / Year</th>
          <th class="px-4 py-2 text-right text-gray-600 font-medium">Amount Due</th>
          <th class="px-4 py-2 text-right text-gray-600 font-medium">Amount Paid</th>
          <th class="px-4 py-2 text-right text-gray-600 font-medium">Balance</th>
          <th class="px-4 py-2 text-center text-gray-600 font-medium">Payment Status</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        <?php if (empty($scholarPayments)): ?>
          <tr>
            <td colspan="7" class="px-4 py-4 text-center text-gray-500">No scholar payment records found</td>
          </tr>
        <?php else: ?>
          <?php foreach ($scholarPayments as $scholar): 
            $amountDue = 10000; // Fixed amount per scholar
            $amountPaid = $scholar['amount_paid'] ?? 0;
            $balance = $amountDue - $amountPaid;
            
            if ($balance == 0) {
              $statusLabel = 'Fully Paid';
              $statusClass = 'bg-green-100 text-green-800';
            } elseif ($amountPaid > 0) {
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
                <span class="inline-block px-2 py-1 rounded text-xs font-medium <?= $statusClass ?>">
                  <?= $statusLabel ?>
                </span>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Payment Status Distribution -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="rounded-lg border border-gray-200 bg-white p-6">
      <h2 class="text-lg font-semibold text-gray-800 mb-4">Payment Status Distribution</h2>
      <div class="space-y-3">
        <?php 
        $fullyPaid = $fullyPaidCount ?? 0;
        $totalScholarsCount = $totalScholars ?? 0;
        $unpaidPartial = $totalScholarsCount - $fullyPaid;
        
        $statuses = [
          ['label' => 'Fully Paid', 'count' => $fullyPaid, 'color' => 'bg-green-500'],
          ['label' => 'Pending', 'count' => $unpaidPartial, 'color' => 'bg-red-500']
        ];
        
        foreach ($statuses as $status):
          $percentage = $totalScholarsCount > 0 ? round(($status['count'] / $totalScholarsCount) * 100) : 0;
        ?>
          <div>
            <div class="flex justify-between text-sm mb-1">
              <span class="font-medium text-gray-700"><?= $status['label'] ?></span>
              <span class="text-gray-600"><?= $status['count'] ?> scholars (<?= $percentage ?>%)</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div class="<?= $status['color'] ?> h-2 rounded-full" style="width: <?= $percentage ?>%"></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white p-6">
      <h2 class="text-lg font-semibold text-gray-800 mb-4">Collection Summary</h2>
      <div class="space-y-3">
        <?php 
        $totalBilled = ($totalScholars ?? 0) * 10000;
        $totalCollected = 0;
        if (!empty($scholarPayments)):
          $totalCollected = array_sum(array_column($scholarPayments, 'amount_paid'));
        endif;
        $outstanding = $totalBilled - $totalCollected;
        $collectionRate = $totalBilled > 0 ? round(($totalCollected / $totalBilled) * 100) : 0;
        ?>
        <div class="flex justify-between items-center pb-3 border-b">
          <span class="text-gray-700">Total Billed:</span>
          <span class="font-semibold text-gray-800">₱<?= number_format($totalBilled, 0) ?></span>
        </div>
        <div class="flex justify-between items-center pb-3 border-b">
          <span class="text-gray-700">Total Collected:</span>
          <span class="font-semibold text-green-600">₱<?= number_format($totalCollected, 0) ?></span>
        </div>
        <div class="flex justify-between items-center pb-3 border-b">
          <span class="text-gray-700">Outstanding:</span>
          <span class="font-semibold text-red-600">₱<?= number_format($outstanding, 0) ?></span>
        </div>
        <div class="flex justify-between items-center pt-2">
          <span class="font-medium text-gray-800">Collection Rate:</span>
          <span class="font-bold text-lg"><?= $collectionRate ?>%</span>
        </div>
      </div>
    </div>
  </div>

  <div class="flex gap-2 pt-4">
    <a href="<?= site_url('admin/reports') ?>" class="inline-block text-sm text-blue-600 hover:text-blue-700 font-medium">
      ← Back to Reports
    </a>
  </div>
</div>

<?= $this->endSection() ?>
