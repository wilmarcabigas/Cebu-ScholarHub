<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div class="min-h-[60vh] grid place-items-center">
  <div class="w-full max-w-md">
    <div class="bg-white shadow-sm ring-1 ring-gray-200 rounded-xl p-6">
      <h1 class="text-xl font-semibold tracking-tight mb-1">Sign in</h1>
      <p class="text-sm text-gray-500 mb-5">Use your issued account to access the platform.</p>

      <?php if (session()->getFlashdata('error')): ?>
        <div class="mb-4 rounded-md bg-red-50 p-3 ring-1 ring-red-200 text-sm text-red-700">
          <?= esc(session()->getFlashdata('error')) ?>
        </div>
      <?php endif; ?>
      <?php if (session()->getFlashdata('message')): ?>
        <div class="mb-4 rounded-md bg-green-50 p-3 ring-1 ring-green-200 text-sm text-green-700">
          <?= esc(session()->getFlashdata('message')) ?>
        </div>
      <?php endif; ?>

      <form method="post" action="<?= site_url('login') ?>" class="space-y-4">
        <?= csrf_field() ?>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <input
            name="email" type="email" value="<?= old('email') ?>"
            class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
            required
          >
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
          <input
            name="password" type="password"
            class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
            required
          >
        </div>

        <button
          class="w-full inline-flex justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-white font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
          Login
        </button>
      </form>

      <p class="text-xs text-gray-500 mt-4">
        Demo: <span class="font-mono">admin@cebu-scholar.gov</span> / <span class="font-mono">secret123</span>
      </p>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
