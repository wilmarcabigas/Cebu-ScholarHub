<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div class="space-y-6">
  <!-- Page Header -->
  <div class="border-b border-gray-200 pb-4">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Billing Management</h1>
        <p class="mt-1 text-sm text-gray-500">Create and manage billing batches for scholars</p>
      </div>
      <a href="<?= site_url('school/billing/create') ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        New Billing
      </a>
    </div>
  </div>

  <!-- Flash Messages -->
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

  <!-- Billings Table -->
  <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
    <?php if (empty($batches)): ?>
      <div class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No billings yet</h3>
        <p class="mt-1 text-sm text-gray-500">Create your first billing batch to get started.</p>
      </div>
    <?php else: ?>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch Label</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Semester</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">School Year</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <?php foreach ($batches as $batch): ?>
              <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= esc($batch['batch_label']) ?></td>
                <td class="px-6 py-4 text-sm text-gray-600"><?= esc($batch['semester']) ?></td>
                <td class="px-6 py-4 text-sm text-gray-600"><?= esc($batch['school_year']) ?></td>
                <td class="px-6 py-4 text-sm font-semibold text-gray-900 text-right">₱<?= number_format($batch['total_amount'], 2) ?></td>
                <td class="px-6 py-4 text-sm">
                  <span class="px-2.5 py-0.5 rounded-full text-xs font-medium <?= match($batch['status']) {
                    'draft'     => 'bg-gray-100 text-gray-800',
                    'submitted' => 'bg-blue-100 text-blue-800',
                    'received'  => 'bg-yellow-100 text-yellow-800',
                    'partial'   => 'bg-orange-100 text-orange-800',
                    'paid'      => 'bg-emerald-100 text-emerald-800',
                    default     => 'bg-gray-100 text-gray-800'
                  } ?>">
                    <?= ucfirst($batch['status']) ?>
                  </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500"><?= date('M d, Y', strtotime($batch['created_at'])) ?></td>
                <td class="px-6 py-4 text-sm space-x-2">
                  <a href="<?= site_url('school/billing/view/' . $batch['id']) ?>" class="text-indigo-600 hover:text-indigo-900">View</a>
                  <?php if ($batch['status'] === 'draft'): ?>
                    <a href="<?= site_url('school/billing/edit/' . $batch['id']) ?>" class="text-amber-600 hover:text-amber-900">Edit</a>
                    <form method="post" action="<?= site_url('school/billing/submit/' . $batch['id']) ?>" class="inline" onsubmit="return confirm('Submit billing to office?');">
                      <?= csrf_field() ?>
                      <button type="submit" class="text-green-600 hover:text-green-900">Submit</button>
                    </form>
                    <a href="<?= site_url('school/billing/delete/' . $batch['id']) ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Delete this billing?');">Delete</a>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<?= $this->endSection() ?>
