<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div class="min-h-[60vh] grid place-items-center">
  <div class="w-full max-w-md">
    <div class="bg-white shadow-sm ring-1 ring-gray-200 rounded-xl p-6">
      <h1 class="text-xl font-semibold tracking-tight mb-1">Unlock Account</h1>
      <p class="text-sm text-gray-500 mb-5">Enter the 6-digit code sent to your Gmail to unlock your account.</p>

      <?php if (session()->getFlashdata('error')): ?>
        <div class="mb-4 rounded-md bg-red-50 p-3 ring-1 ring-red-200 text-sm text-red-700">
          <?= esc(session()->getFlashdata('error')) ?>
        </div>
      <?php endif; ?>
      <?php if (session()->getFlashdata('success')): ?>
        <div class="mb-4 rounded-md bg-green-50 p-3 ring-1 ring-green-200 text-sm text-green-700">
          <?= esc(session()->getFlashdata('success')) ?>
        </div>
      <?php endif; ?>

      <form method="post" action="<?= site_url('unlock') ?>" class="space-y-4">
        <?= csrf_field() ?>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Unlock Code</label>
          <input
            type="text"
            name="code"
            maxlength="6"
            pattern="\d{6}"
            placeholder="Enter 6-digit code"
            class="mt-1 p-2 border border-gray-300 rounded-md w-full"
            aria-label="Unlock Code"
            required
          >
        </div>

        <button
          type="submit"
          class="w-full inline-flex justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-white font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
          Unlock Account
        </button>
      </form>

      <p class="text-xs text-gray-500 mt-4">
        Didn't receive the code? Check your spam folder or try logging in again.
      </p>
    </div>
  </div>
</div>

<?= $this->endSection() ?>