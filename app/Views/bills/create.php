<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<div class="max-w-2xl mx-auto">
  <div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-800">Post Billing</h1>
    <p class="text-sm text-gray-500">Create a billing record for a scholar</p>
  </div>

  <form method="post" action="<?= site_url('school/billing/store') ?>"
        class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm space-y-4">
    <?= csrf_field() ?>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Scholar</label>
      <select name="scholar_id" required
              class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <option value="">Select Scholar</option>
        <?php foreach ($scholars as $s): ?>
          <option value="<?= $s['id'] ?>"> <?= esc($s['first_name'] . ' ' . $s['last_name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Billing Period</label>
      <input type="text" name="billing_period" required
             class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Amount Due</label>
        <input type="number" name="amount_due" required
               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
        <input type="date" name="due_date" required
               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
      <textarea name="remarks" rows="3"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
    </div>

    <div class="flex justify-end gap-3 pt-4">
      <a href="<?= site_url('school/billing') ?>"
         class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
        Cancel
      </a>
      <button
        class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
        Save Billing
      </button>
    </div>
  </form>
</div>

<?= $this->endSection() ?>
