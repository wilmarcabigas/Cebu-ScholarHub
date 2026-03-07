<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<?php
  $session = session();

  $verifyMode = $session->getFlashdata('verify_mode') || $session->get('pending_login_user_id');
  $unlockMode = $session->getFlashdata('unlock_mode');

  $lockedEmail = $session->getFlashdata('locked_email') ?? old('email') ?? '';
  $pendingEmail = $session->get('pending_login_email') ?? '';
?>

<div class="min-h-[60vh] grid place-items-center">
  <div class="w-full max-w-md">
    <div class="bg-white shadow-sm ring-1 ring-gray-200 rounded-xl p-6">

      <h1 class="text-xl font-semibold tracking-tight mb-1">
        <?php if ($unlockMode): ?>
          Unlock Account
        <?php elseif ($verifyMode): ?>
          Verify Login
        <?php else: ?>
          Sign in
        <?php endif; ?>
      </h1>

      <p class="text-sm text-gray-500 mb-5">
        <?php if ($unlockMode): ?>
          Enter the unlock code sent to your Gmail to unlock your account.
        <?php elseif ($verifyMode): ?>
          Enter the verification code sent to your Gmail to continue login.
        <?php else: ?>
          Use your issued account to access the platform.
        <?php endif; ?>
      </p>

      <?php if ($session->getFlashdata('error')): ?>
        <div class="mb-4 rounded-md bg-red-50 p-3 ring-1 ring-red-200 text-sm text-red-700">
          <?= esc($session->getFlashdata('error')) ?>
        </div>
      <?php endif; ?>

      <?php if ($session->getFlashdata('message') || $session->getFlashdata('success')): ?>
        <div class="mb-4 rounded-md bg-green-50 p-3 ring-1 ring-green-200 text-sm text-green-700">
          <?= esc($session->getFlashdata('message') ?? $session->getFlashdata('success')) ?>
        </div>
      <?php endif; ?>

      <?php if (! $unlockMode && ! $verifyMode): ?>
        <!-- NORMAL LOGIN -->
        <form method="post" action="<?= site_url('login') ?>" class="space-y-4">
          <?= csrf_field() ?>

          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input
              id="email"
              name="email"
              type="email"
              value="<?= old('email') ?>"
              class="mt-1 p-2 border border-gray-300 rounded-md w-full"
              required
            >
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input
              type="password"
              name="password"
              id="password"
              required
              class="mt-1 p-2 border border-gray-300 rounded-md w-full"
            >
            <p class="text-xs text-gray-500 mt-1">
              Enter your password, then the system will send a verification code to your Gmail.
            </p>
          </div>

          <button
            type="submit"
            class="w-full inline-flex justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-white font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            Verify
          </button>
        </form>
      <?php endif; ?>

      <?php if ($unlockMode): ?>
        <!-- UNLOCK ACCOUNT -->
        <form method="post" action="<?= site_url('unlock') ?>" class="space-y-4">
          <?= csrf_field() ?>

          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input
              id="email"
              name="email"
              type="email"
              value="<?= esc($lockedEmail) ?>"
              class="mt-1 p-2 border border-gray-300 rounded-md w-full"
              required
            >
          </div>

          <div>
            <label for="unlock_code" class="block text-sm font-medium text-gray-700">Unlock Code</label>
            <input
              type="text"
              name="unlock_code"
              id="unlock_code"
              maxlength="6"
              pattern="\d{6}"
              placeholder="Enter 6-digit unlock code"
              class="mt-1 p-2 border border-gray-300 rounded-md w-full"
              required
            >
            <p class="text-xs text-gray-500 mt-1">
              Your account is locked after 3 failed attempts. Enter the Gmail unlock code.
            </p>
          </div>

          <button
            type="submit"
            class="w-full inline-flex justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-white font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            Unlock Account
          </button>
        </form>
      <?php endif; ?>

      <?php if ($verifyMode): ?>
        <!-- VERIFY LOGIN CODE -->
        <form method="post" action="<?= site_url('login/verify-code') ?>" class="space-y-4">
          <?= csrf_field() ?>

          <div>
            <label for="verify_email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input
              id="verify_email"
              type="email"
              value="<?= esc($pendingEmail) ?>"
              class="mt-1 p-2 border border-gray-300 rounded-md w-full bg-gray-100"
              readonly
            >
          </div>

          <div>
            <label for="code" class="block text-sm font-medium text-gray-700">Verification Code</label>
            <input
              type="text"
              name="code"
              id="code"
              maxlength="6"
              pattern="\d{6}"
              placeholder="Enter 6-digit verification code"
              class="mt-1 p-2 border border-gray-300 rounded-md w-full"
              required
            >
            <p class="text-xs text-gray-500 mt-1">
              Enter the verification code sent to your Gmail before accessing the dashboard.
            </p>
          </div>

          <button
            type="submit"
            class="w-full inline-flex justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-white font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            Login
          </button>
        </form>

        <form method="post" action="<?= site_url('login/resend-code') ?>" class="mt-3">
          <?= csrf_field() ?>
          <button
            type="submit"
            class="w-full inline-flex justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-700 font-medium hover:bg-gray-50">
            Resend Code
          </button>
        </form>
      <?php endif; ?>

    </div>
  </div>
</div>

<?= $this->endSection() ?>