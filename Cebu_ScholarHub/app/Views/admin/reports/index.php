<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div class="space-y-6">
  <!-- Header -->
  <div class="border-b border-gray-200 pb-4">
    <h1 class="text-3xl font-bold text-gray-900">Reports</h1>
    <p class="mt-1 text-sm text-gray-500">View office-wide financial and payment reports</p>
  </div>

  <!-- Overall Summary Cards -->
  <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <div class="rounded-lg border border-gray-200 bg-white p-6">
      <p class="text-sm text-gray-600 font-medium">Total Billed</p>
      <p class="text-3xl font-bold text-gray-900 mt-2">₱<?= number_format($totalBilled, 2) ?></p>
    </div>
    <div class="rounded-lg border border-gray-200 bg-white p-6">
      <p class="text-sm text-gray-600 font-medium">Total Paid</p>
      <p class="text-3xl font-bold text-green-600 mt-2">₱<?= number_format($totalPaid, 2) ?></p>
    </div>
    <div class="rounded-lg border border-gray-200 bg-white p-6">
      <p class="text-sm text-gray-600 font-medium">Outstanding</p>
      <p class="text-3xl font-bold text-red-600 mt-2">₱<?= number_format($totalBalance, 2) ?></p>
    </div>
    <div class="rounded-lg border border-gray-200 bg-white p-6">
      <p class="text-sm text-gray-600 font-medium">Collection Rate</p>
      <p class="text-3xl font-bold text-indigo-600 mt-2"><?= $totalBilled > 0 ? round(($totalPaid / $totalBilled) * 100, 1) : 0 ?>%</p>
    </div>
  </div>

  <!-- Report Links -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="rounded-lg border border-gray-200 bg-white p-6">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">Financial Reports</h3>
      <div class="space-y-2">
        <a href="<?= site_url('admin/reports/payment-status') ?>" class="block p-3 hover:bg-gray-50 rounded text-indigo-600 font-medium border border-transparent hover:border-indigo-200">
          → Payment Status by School
        </a>
        <a href="<?= site_url('admin/reports/financial-report') ?>" class="block p-3 hover:bg-gray-50 rounded text-indigo-600 font-medium border border-transparent hover:border-indigo-200">
          → Comprehensive Financial Report
        </a>
        <a href="<?= site_url('admin/reports/export-financial') ?>" class="block p-3 hover:bg-gray-50 rounded text-indigo-600 font-medium border border-transparent hover:border-indigo-200">
          → Export Financial Report (CSV)
        </a>
      </div>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white p-6">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">Scholar Reports</h3>
      <div class="space-y-2">
        <a href="<?= site_url('admin/reports/scholar-payment-history') ?>" class="block p-3 hover:bg-gray-50 rounded text-indigo-600 font-medium border border-transparent hover:border-indigo-200">
          → Scholar Payment History
        </a>
        <a href="<?= site_url('admin/reports/billing-sheets') ?>" class="block p-3 hover:bg-gray-50 rounded text-indigo-600 font-medium border border-transparent hover:border-indigo-200">
          → All Billing Sheets
        </a>
        <a href="<?= site_url('admin/reports/export-scholar') ?>" class="block p-3 hover:bg-gray-50 rounded text-indigo-600 font-medium border border-transparent hover:border-indigo-200">
          → Export Scholar Report (CSV)
        </a>
      </div>
    </div>
  </div>

  <!-- Payment Summary by School -->
  <div class="rounded-lg border border-gray-200 bg-white overflow-hidden">
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
      <h3 class="text-lg font-semibold text-gray-900">Payment Summary by School</h3>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">School</th>
            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Bills</th>
            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Total Due</th>
            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Total Paid</th>
            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Balance</th>
            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Collection %</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <?php foreach ($schoolSummary as $summary): ?>
            <tr class="hover:bg-gray-50">
              <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= esc($summary['name']) ?></td>
              <td class="px-6 py-4 text-sm text-right text-gray-600"><?= $summary['total_bills'] ?? 0 ?></td>
              <td class="px-6 py-4 text-sm text-right font-semibold text-gray-900">₱<?= number_format($summary['total_due'] ?? 0, 2) ?></td>
              <td class="px-6 py-4 text-sm text-right font-semibold text-green-600">₱<?= number_format($summary['total_paid'] ?? 0, 2) ?></td>
              <td class="px-6 py-4 text-sm text-right font-semibold text-red-600">₱<?= number_format(($summary['total_due'] ?? 0) - ($summary['total_paid'] ?? 0), 2) ?></td>
              <td class="px-6 py-4 text-sm text-center text-indigo-600 font-semibold">
                <?= ($summary['total_due'] ?? 0) > 0 ? round((($summary['total_paid'] ?? 0) / ($summary['total_due'] ?? 1)) * 100, 1) : 0 ?>%
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<div class="mb-6 flex items-center justify-between">
  <div>
    <h1 class="text-2xl font-semibold text-gray-800">Financial Reports</h1>
    <p class="text-sm text-gray-500">Billing and payment overview for the CCCSP Office</p>
  </div>
  <button onclick="window.print()"
          class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
    Print Report
  </button>
</div>

<!-- TAB NAVIGATION -->
<div class="mb-6 border-b border-gray-200">
  <nav class="flex gap-1 -mb-px" id="tab-nav">
    <?php
      $tabs = [
        'overview'  => 'Overall Financial Summary',
        'school'    => 'Per-School Summary',
        'scholar'   => 'Per-Scholar History',
        'bills'     => 'Bill Records',
      ];
    ?>
    <?php foreach ($tabs as $key => $label): ?>
      <button onclick="switchTab('<?= $key ?>')"
              id="tab-<?= $key ?>"
              class="tab-btn px-4 py-2.5 text-sm font-medium border-b-2 whitespace-nowrap
                     <?= $key === 'overview' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?>">
        <?= $label ?>
      </button>
    <?php endforeach; ?>
  </nav>
</div>

<!-- ============================================================ -->
<!-- TAB: OVERVIEW -->
<!-- ============================================================ -->
<div id="panel-overview" class="tab-panel space-y-6">

  <!-- KPI Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="rounded-lg bg-gray-50 p-5">
      <p class="text-xs font-medium text-gray-500 mb-1">Total Billed</p>
      <p class="text-3xl font-semibold text-gray-800">₱<?= number_format($totalBilled, 2) ?></p>
    </div>
    <div class="rounded-lg bg-green-50 p-5">
      <p class="text-xs font-medium text-green-600 mb-1">Total Collected</p>
      <p class="text-3xl font-semibold text-green-700">₱<?= number_format($totalPaid, 2) ?></p>
    </div>
    <div class="rounded-lg bg-red-50 p-5">
      <p class="text-xs font-medium text-red-600 mb-1">Outstanding Balance</p>
      <p class="text-3xl font-semibold text-red-700">₱<?= number_format($totalBalance, 2) ?></p>
    </div>
  </div>

  <!-- Collection Rate Bar -->
  <?php $rate = $totalBilled > 0 ? round(($totalPaid / $totalBilled) * 100, 1) : 0; ?>
  <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
    <div class="flex justify-between mb-2">
      <span class="text-sm font-medium text-gray-700">Collection Rate</span>
      <span class="text-sm font-semibold text-indigo-700"><?= $rate ?>%</span>
    </div>
    <div class="w-full bg-gray-200 rounded-full h-3">
      <div class="bg-indigo-500 h-3 rounded-full" style="width: <?= $rate ?>%;"></div>
    </div>
    <p class="text-xs text-gray-400 mt-2">₱<?= number_format($totalPaid, 2) ?> collected out of ₱<?= number_format($totalBilled, 2) ?> total billed</p>
  </div>

</div>

<!-- ============================================================ -->
<!-- TAB: PER-SCHOOL SUMMARY -->
<!-- ============================================================ -->
<div id="panel-school" class="tab-panel hidden">
  <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">School</th>
          <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Batches</th>
          <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Billed</th>
          <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Paid</th>
          <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Balance</th>
          <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        <?php if (empty($schoolSummary)): ?>
          <tr>
            <td colspan="6" class="px-4 py-8 text-center text-gray-400">No billing data available.</td>
          </tr>
        <?php endif; ?>
        <?php foreach ($schoolSummary as $row): ?>
          <?php
            $badge = match($row['status'] ?? '') {
              'paid'    => 'bg-green-100 text-green-700',
              'partial' => 'bg-yellow-100 text-yellow-700',
              'unpaid'  => 'bg-red-100 text-red-700',
              default   => 'bg-gray-100 text-gray-500',
            };
          ?>
          <tr class="hover:bg-gray-50">
            <td class="px-4 py-3 font-medium text-gray-800"><?= esc($row['school_name']) ?></td>
            <td class="px-4 py-3 text-center text-gray-600"><?= $row['total_batches'] ?></td>
            <td class="px-4 py-3 text-right text-gray-700">₱<?= number_format($row['total_billed'], 2) ?></td>
            <td class="px-4 py-3 text-right text-green-700 font-medium">₱<?= number_format($row['total_paid'], 2) ?></td>
            <td class="px-4 py-3 text-right text-red-600 font-medium">₱<?= number_format($row['balance'], 2) ?></td>
            <td class="px-4 py-3 text-center">
              <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold <?= $badge ?>">
                <?= strtoupper($row['status'] ?? 'UNPAID') ?>
              </span>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- ============================================================ -->
<!-- TAB: PER-SCHOLAR HISTORY -->
<!-- ============================================================ -->
<div id="panel-scholar" class="tab-panel hidden">

  <div class="mb-3">
    <input type="text" id="scholar-filter" placeholder="Search by name, ID, or school..."
           oninput="filterScholars()"
           class="w-full max-w-sm rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
  </div>

  <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID Num</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Scholar Name</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course / Year</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">School</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Semester</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">School Year</th>
          <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
          <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Batch Status</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100" id="scholar-tbody">
        <?php if (empty($scholarPayments)): ?>
          <tr>
            <td colspan="8" class="px-4 py-8 text-center text-gray-400">No scholar billing records found.</td>
          </tr>
        <?php endif; ?>
        <?php foreach ($scholarPayments as $row): ?>
          <?php
            $badge = match($row['batch_status']) {
              'paid'      => 'bg-green-100 text-green-700',
              'partial'   => 'bg-yellow-100 text-yellow-700',
              'received'  => 'bg-blue-100 text-blue-700',
              'submitted' => 'bg-indigo-100 text-indigo-700',
              default     => 'bg-gray-100 text-gray-500',
            };
          ?>
          <tr class="hover:bg-gray-50 scholar-row"
              data-search="<?= strtolower($row['scholar_name'] . ' ' . $row['id_num'] . ' ' . $row['school_name']) ?>">
            <td class="px-4 py-3 font-mono text-xs text-gray-600"><?= esc($row['id_num']) ?></td>
            <td class="px-4 py-3 font-medium text-gray-800"><?= esc($row['scholar_name']) ?></td>
            <td class="px-4 py-3 text-gray-600"><?= esc($row['course'] . ' — ' . $row['year_level']) ?></td>
            <td class="px-4 py-3 text-gray-600"><?= esc($row['school_name']) ?></td>
            <td class="px-4 py-3 text-gray-600"><?= esc($row['semester']) ?></td>
            <td class="px-4 py-3 text-gray-600"><?= esc($row['school_year']) ?></td>
            <td class="px-4 py-3 text-right font-medium text-gray-800">₱<?= number_format($row['billed_amount'], 2) ?></td>
            <td class="px-4 py-3 text-center">
              <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold <?= $badge ?>">
                <?= strtoupper($row['batch_status']) ?>
              </span>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- ============================================================ -->
<!-- TAB: BILL RECORDS -->
<!-- ============================================================ -->
<div id="panel-bills" class="tab-panel hidden">
  <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">School</th>
          <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount Due</th>
          <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount Paid</th>
          <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Balance</th>
          <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Due Date</th>
          <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
          <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        <?php if (empty($bills)): ?>
          <tr>
            <td colspan="7" class="px-4 py-8 text-center text-gray-400">No bills recorded yet.</td>
          </tr>
        <?php endif; ?>
        <?php foreach ($bills as $bill): ?>
          <?php
            $balance = $bill['amount_due'] - $bill['amount_paid'];
            $badge = match($bill['status']) {
              'paid'    => 'bg-green-100 text-green-700',
              'partial' => 'bg-yellow-100 text-yellow-700',
              'unpaid'  => 'bg-red-100 text-red-700',
              default   => 'bg-gray-100 text-gray-500',
            };
          ?>
          <tr class="hover:bg-gray-50">
            <td class="px-4 py-3 font-medium text-gray-800"><?= esc($bill['school_name']) ?></td>
            <td class="px-4 py-3 text-right text-gray-700">₱<?= number_format($bill['amount_due'], 2) ?></td>
            <td class="px-4 py-3 text-right text-green-700">₱<?= number_format($bill['amount_paid'], 2) ?></td>
            <td class="px-4 py-3 text-right text-red-600">₱<?= number_format($balance, 2) ?></td>
            <td class="px-4 py-3 text-center text-gray-500 text-xs">
              <?= $bill['due_date'] ? date('M d, Y', strtotime($bill['due_date'])) : '—' ?>
            </td>
            <td class="px-4 py-3 text-center">
              <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold <?= $badge ?>">
                <?= strtoupper($bill['status']) ?>
              </span>
            </td>
            <td class="px-4 py-3 text-right">
              <a href="<?= site_url('admin/billing/view/' . $bill['batch_id']) ?>"
                 class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
function switchTab(name) {
  document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
  document.querySelectorAll('.tab-btn').forEach(b => {
    b.classList.remove('border-indigo-500','text-indigo-600');
    b.classList.add('border-transparent','text-gray-500');
  });
  document.getElementById('panel-' + name).classList.remove('hidden');
  const btn = document.getElementById('tab-' + name);
  btn.classList.remove('border-transparent','text-gray-500');
  btn.classList.add('border-indigo-500','text-indigo-600');
}

function filterScholars() {
  const q = document.getElementById('scholar-filter').value.toLowerCase();
  document.querySelectorAll('.scholar-row').forEach(r => {
    r.style.display = r.dataset.search.includes(q) ? '' : 'none';
  });
}
</script>

<?= $this->endSection() ?>