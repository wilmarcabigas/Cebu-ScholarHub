<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div class="max-w-7xl mx-auto space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-semibold text-gray-800">All Billing Sheets</h1>
      <p class="text-sm text-gray-500">Complete collection of all billing sheets from all schools</p>
    </div>
  </div>

  <div class="rounded-lg border border-gray-200 bg-white p-4 space-y-4">
    <form method="get" action="<?= site_url('admin/reports/billing-sheets') ?>" class="space-y-4">
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
          <label class="mb-1 block text-sm font-medium text-gray-700">Status</label>
          <select name="status" class="w-full rounded border border-gray-300 p-2 text-sm">
            <option value="">-- All Status --</option>
            <option value="draft" <?= isset($_GET['status']) && $_GET['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
            <option value="submitted" <?= isset($_GET['status']) && $_GET['status'] === 'submitted' ? 'selected' : '' ?>>Submitted</option>
            <option value="received" <?= isset($_GET['status']) && $_GET['status'] === 'received' ? 'selected' : '' ?>>Received</option>
            <option value="paid" <?= isset($_GET['status']) && $_GET['status'] === 'paid' ? 'selected' : '' ?>>Paid</option>
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

  <div class="space-y-4">
    <?php if (empty($billingSheets)): ?>
      <div class="rounded-lg border border-gray-200 bg-gray-50 p-6 text-center">
        <p class="text-gray-600">No billing sheets found matching your criteria.</p>
      </div>
    <?php else: ?>
      <?php foreach ($billingSheets as $batch): ?>
        <?php
          $statusColor = match ($batch['status']) {
            'draft' => 'bg-yellow-100 text-yellow-800',
            'submitted' => 'bg-orange-100 text-orange-800',
            'received' => 'bg-purple-100 text-purple-800',
            'paid' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800',
          };
          $itemsCount = (int) ($batch['items_count'] ?? 0);
          $totalAmount = (float) ($batch['total_amount'] ?? 0);
          $amountPaid = (float) ($batch['amount_paid'] ?? 0);
          $balance = $totalAmount - $amountPaid;
        ?>
        <div class="rounded-lg border border-gray-200 bg-white p-6">
          <div class="mb-4 flex items-start justify-between">
            <div>
              <h2 class="text-lg font-semibold text-gray-800"><?= esc($batch['school_name'] ?? 'N/A') ?></h2>
              <p class="text-sm text-gray-500"><?= esc($batch['batch_label']) ?> - <?= esc($batch['semester'] ?? 'N/A') ?></p>
            </div>
            <div class="flex items-center gap-2">
              <span class="inline-block rounded-full px-3 py-1 text-xs font-medium <?= $statusColor ?>">
                <?= ucfirst($batch['status']) ?>
              </span>
              <a href="<?= site_url('admin/billing/print/' . $batch['id']) ?>" target="_blank" class="text-sm font-medium text-blue-600 hover:text-blue-700">
                Print
              </a>
            </div>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full border-collapse text-sm">
              <thead class="border-b border-gray-200 bg-gray-50">
                <tr>
                  <th class="px-3 py-2 text-left font-medium text-gray-600">Scholar</th>
                  <th class="px-3 py-2 text-left font-medium text-gray-600">Course / Year</th>
                  <th class="px-3 py-2 text-right font-medium text-gray-600">Amount</th>
                  <th class="px-3 py-2 text-right font-medium text-gray-600">Paid</th>
                  <th class="px-3 py-2 text-right font-medium text-gray-600">Balance</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <?php if (!empty($batch['items'])): ?>
                  <?php foreach (array_slice($batch['items'], 0, 5) as $item): ?>
                    <?php $itemBalance = (float) ($item['amount'] ?? 0) - (float) ($item['amount_paid'] ?? 0); ?>
                    <tr class="hover:bg-gray-50">
                      <td class="px-3 py-2"><?= esc($item['scholar_name'] ?? 'N/A') ?></td>
                      <td class="px-3 py-2"><?= esc($item['course'] ?? 'N/A') ?> / <?= esc($item['year_level'] ?? 'N/A') ?></td>
                      <td class="px-3 py-2 text-right">₱<?= number_format((float) ($item['amount'] ?? 0), 2) ?></td>
                      <td class="px-3 py-2 text-right">₱<?= number_format((float) ($item['amount_paid'] ?? 0), 2) ?></td>
                      <td class="px-3 py-2 text-right">₱<?= number_format($itemBalance, 2) ?></td>
                    </tr>
                  <?php endforeach; ?>
                  <?php if ($itemsCount > 5): ?>
                    <tr>
                      <td colspan="5" class="px-3 py-2 text-center text-sm text-gray-500">
                        ... and <?= $itemsCount - 5 ?> more items
                      </td>
                    </tr>
                  <?php endif; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="5" class="px-3 py-4 text-center text-sm text-gray-500">No billing items found for this batch.</td>
                  </tr>
                <?php endif; ?>
                <tr class="bg-gray-100 font-semibold">
                  <td colspan="2" class="px-3 py-2">TOTAL (<?= $itemsCount ?> scholars)</td>
                  <td class="px-3 py-2 text-right">₱<?= number_format($totalAmount, 2) ?></td>
                  <td class="px-3 py-2 text-right text-green-600">₱<?= number_format($amountPaid, 2) ?></td>
                  <td class="px-3 py-2 text-right <?= $balance > 0 ? 'text-red-600' : 'text-green-600' ?>">₱<?= number_format($balance, 2) ?></td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="mt-4 grid grid-cols-2 gap-3 text-sm sm:grid-cols-4">
            <div>
              <p class="text-gray-500">Batch ID</p>
              <p class="font-semibold text-gray-800"><?= esc($batch['id']) ?></p>
            </div>
            <div>
              <p class="text-gray-500">Created</p>
              <p class="font-semibold text-gray-800"><?= date('M d, Y', strtotime($batch['created_at'])) ?></p>
            </div>
            <div>
              <p class="text-gray-500">School Year</p>
              <p class="font-semibold text-gray-800"><?= esc($batch['school_year'] ?? 'N/A') ?></p>
            </div>
            <div>
              <p class="text-gray-500">Collection Rate</p>
              <p class="font-semibold text-gray-800"><?= $totalAmount > 0 ? round(($amountPaid / $totalAmount) * 100) : 0 ?>%</p>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <div class="flex gap-2 pt-4">
    <a href="<?= site_url('admin/reports') ?>" class="inline-block text-sm font-medium text-blue-600 hover:text-blue-700">
      Back to Reports
    </a>
  </div>
</div>

<?= $this->endSection() ?>
