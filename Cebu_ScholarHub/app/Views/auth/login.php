<?= $this->extend('layouts/loginbase') ?>
<?= $this->section('content') ?>

<?php
  $session = session();

  $verifyMode = $session->getFlashdata('verify_mode') || $session->get('pending_login_user_id');
  $unlockMode = $session->getFlashdata('unlock_mode');
  $resetMode  = $session->get('reset_password_user_id') && $session->get('reset_password_verified');

  $lockedEmail  = $session->getFlashdata('locked_email') ?? old('email') ?? '';
  $pendingEmail = $session->get('pending_login_email') ?? '';
  $resetEmail   = $session->get('reset_password_email') ?? '';
?>

<div class="text-white">

  <!-- Header -->
  <div class="mb-7 text-center">
    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl border <?= $resetMode ? 'border-emerald-300/20 bg-emerald-400/10' : 'border-cyan-300/20 bg-cyan-400/10' ?> shadow-lg backdrop-blur-xl">
      <?php if ($resetMode): ?>
        <svg class="h-8 w-8 text-emerald-300" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 .53-.21 1.04-.59 1.41A2 2 0 0110 13H8v2h2a4 4 0 004-4V9a2 2 0 10-4 0v1m-2 7h8a2 2 0 002-2v-5a2 2 0 00-2-2H8a2 2 0 00-2 2v5a2 2 0 002 2z"/>
        </svg>
      <?php else: ?>
        <svg class="h-8 w-8 text-cyan-300" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 0h12a2 2 0 002-2v-5a2 2 0 00-2-2h-1V7a5 5 0 00-10 0v1H6a2 2 0 00-2 2v5a2 2 0 002 2z"/>
        </svg>
      <?php endif; ?>
    </div>

    <?php if ($resetMode): ?>
      <h1 class="text-2xl font-bold tracking-tight text-white">Reset Password</h1>
      <p class="mt-2 text-sm leading-6 text-slate-300">
        Your unlock code has been verified. Create a new password to unlock your account and login again.
      </p>
    <?php elseif ($unlockMode): ?>
      <h1 class="text-2xl font-bold tracking-tight text-white">Unlock Account</h1>
      <p class="mt-2 text-sm leading-6 text-slate-300">
        Your account was locked after 3 failed attempts. Enter the unlock code sent to your Gmail.
      </p>
    <?php elseif ($verifyMode): ?>
      <h1 class="text-2xl font-bold tracking-tight text-white">OTP Verification</h1>
      <p class="mt-2 text-sm leading-6 text-slate-300">
        Enter the verification code sent to your Gmail to continue login.
      </p>
    <?php else: ?>
      <h1 class="text-2xl font-bold tracking-tight text-white">Sign in</h1>
      <p class="mt-2 text-sm leading-6 text-slate-300">
        Use your issued account to access the platform.
      </p>
    <?php endif; ?>
  </div>

  <!-- Flash messages -->
  <?php if (session()->getFlashdata('message')): ?>
    <div class="mb-4 rounded-2xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-200 backdrop-blur-md">
      <?= esc(session()->getFlashdata('message')) ?>
    </div>
  <?php endif; ?>

  <?php if (session()->getFlashdata('success')): ?>
    <div class="mb-4 rounded-2xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-200 backdrop-blur-md">
      <?= esc(session()->getFlashdata('success')) ?>
    </div>
  <?php endif; ?>

  <?php if (session()->getFlashdata('error')): ?>
    <div class="mb-4 rounded-2xl border border-red-400/20 bg-red-400/10 px-4 py-3 text-sm text-red-200 backdrop-blur-md">
      <?= esc(session()->getFlashdata('error')) ?>
    </div>
  <?php endif; ?>

  <!-- NORMAL LOGIN -->
  <?php if (! $unlockMode && ! $verifyMode && ! $resetMode): ?>
    <form method="post" action="<?= site_url('login') ?>" class="space-y-5">
      <?= csrf_field() ?>

      <div>
        <label for="email" class="mb-2 block text-sm font-medium text-slate-200">Email</label>
        <input
          id="email"
          name="email"
          type="email"
          value="<?= old('email') ?>"
          class="w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-3 text-white placeholder-slate-400 outline-none backdrop-blur-xl transition focus:border-cyan-300 focus:ring-2 focus:ring-cyan-400/30"
          required
        >
      </div>

      <div>
        <label for="password" class="mb-2 block text-sm font-medium text-slate-200">Password</label>
        <input
          type="password"
          name="password"
          id="password"
          required
          class="w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-3 text-white placeholder-slate-400 outline-none backdrop-blur-xl transition focus:border-cyan-300 focus:ring-2 focus:ring-cyan-400/30"
        >
        <p class="mt-2 text-xs leading-5 text-slate-400">
          Enter your password, then the system will send a verification code to your Gmail.
        </p>
      </div>

      <button
        type="submit"
        class="w-full inline-flex justify-center rounded-2xl bg-gradient-to-r from-cyan-400 via-blue-500 to-violet-500 px-4 py-3 text-sm font-semibold text-white shadow-lg transition duration-200 hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-cyan-400/40"
      >
        Verify
      </button>
    </form>
  <?php endif; ?>

  <!-- UNLOCK ACCOUNT -->
  <?php if ($unlockMode && ! $resetMode): ?>
    <form method="post" action="<?= site_url('unlock') ?>" class="space-y-5">
      <?= csrf_field() ?>

      <div>
        <label for="email" class="mb-2 block text-sm font-medium text-slate-200">Email</label>
        <input
          id="email"
          name="email"
          type="email"
          value="<?= esc($lockedEmail) ?>"
          class="w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-3 text-white placeholder-slate-400 outline-none backdrop-blur-xl transition focus:border-cyan-300 focus:ring-2 focus:ring-cyan-400/30"
          required
        >
      </div>

      <div>
        <label for="unlock_code" class="mb-2 block text-sm font-medium text-slate-200">Unlock Code</label>
        <input
          type="text"
          name="unlock_code"
          id="unlock_code"
          maxlength="6"
          pattern="\d{6}"
          placeholder="Enter 6-digit unlock code"
          class="w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-3 text-white placeholder-slate-400 outline-none backdrop-blur-xl transition focus:border-cyan-300 focus:ring-2 focus:ring-cyan-400/30"
          required
        >
        <p class="mt-2 text-xs leading-5 text-slate-400">
          Your account is locked after 3 failed attempts. Enter the Gmail unlock code.
        </p>
      </div>

      <button
        type="submit"
        class="w-full inline-flex justify-center rounded-2xl bg-gradient-to-r from-amber-400 via-orange-500 to-red-500 px-4 py-3 text-sm font-semibold text-white shadow-lg transition duration-200 hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-orange-400/40"
      >
        Unlock Account
      </button>
    </form>
  <?php endif; ?>

  <!-- RESET PASSWORD -->
  <?php if ($resetMode): ?>
    <form method="post" action="<?= site_url('reset-password') ?>" class="space-y-5">
      <?= csrf_field() ?>

      <div>
        <label class="mb-2 block text-sm font-medium text-slate-200">Email</label>
        <input
          type="email"
          value="<?= esc($resetEmail) ?>"
          class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-slate-300 outline-none backdrop-blur-xl"
          readonly
        >
      </div>

      <div>
        <label for="new_password" class="mb-2 block text-sm font-medium text-slate-200">New Password</label>
        <input
          type="password"
          name="password"
          id="new_password"
          minlength="8"
          required
          class="w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-3 text-white placeholder-slate-400 outline-none backdrop-blur-xl transition focus:border-emerald-300 focus:ring-2 focus:ring-emerald-400/30"
        >
        <p class="mt-2 text-xs leading-5 text-slate-400">
          Use at least 8 characters.
        </p>
      </div>

      <div>
        <label for="confirm_password" class="mb-2 block text-sm font-medium text-slate-200">Confirm New Password</label>
        <input
          type="password"
          name="confirm_password"
          id="confirm_password"
          minlength="8"
          required
          class="w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-3 text-white placeholder-slate-400 outline-none backdrop-blur-xl transition focus:border-emerald-300 focus:ring-2 focus:ring-emerald-400/30"
        >
      </div>

      <button
        type="submit"
        class="w-full inline-flex justify-center rounded-2xl bg-gradient-to-r from-emerald-400 via-green-500 to-teal-500 px-4 py-3 text-sm font-semibold text-white shadow-lg transition duration-200 hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-emerald-400/40"
      >
        Save New Password
      </button>
    </form>

    <div class="mt-4">
      <a
        href="<?= site_url('login') ?>"
        class="w-full inline-flex justify-center rounded-2xl border border-white/15 bg-white/5 px-4 py-3 text-sm font-medium text-slate-200 backdrop-blur-xl transition hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white/20"
      >
        Back to Login
      </a>
    </div>
  <?php endif; ?>

  <!-- VERIFY LOGIN CODE -->
  <?php if ($verifyMode && ! $resetMode): ?>
    <form method="post" action="<?= site_url('login/verify-code') ?>" class="space-y-5" id="verifyForm">
      <?= csrf_field() ?>

      <div>
        <label for="verify_email" class="mb-2 block text-sm font-medium text-slate-200">Email</label>
        <input
          id="verify_email"
          type="email"
          value="<?= esc($pendingEmail) ?>"
          class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-slate-300 outline-none backdrop-blur-xl"
          readonly
        >
      </div>

      <div>
        <label for="code" class="mb-2 block text-sm font-medium text-slate-200">Verification Code</label>
        <input
          type="text"
          name="code"
          id="code"
          maxlength="6"
          pattern="\d{6}"
          placeholder="Enter 6-digit verification code"
          class="w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-3 text-white placeholder-slate-400 outline-none backdrop-blur-xl transition focus:border-cyan-300 focus:ring-2 focus:ring-cyan-400/30"
          required
        >
        <p class="mt-2 text-xs leading-5 text-slate-400">
          Enter the verification code sent to your Gmail before accessing the dashboard.
        </p>
      </div>

      <button
        type="submit"
        class="w-full inline-flex justify-center rounded-2xl bg-gradient-to-r from-cyan-400 via-blue-500 to-violet-500 px-4 py-3 text-sm font-semibold text-white shadow-lg transition duration-200 hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-cyan-400/40"
      >
        Login
      </button>
    </form>

    <form method="post" action="<?= site_url('login/resend-code') ?>" class="mt-4">
      <?= csrf_field() ?>
      <button
        type="submit"
        class="w-full inline-flex justify-center rounded-2xl border border-white/15 bg-white/5 px-4 py-3 text-sm font-medium text-slate-200 backdrop-blur-xl transition hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white/20"
      >
        Resend Code
      </button>
    </form>
  <?php endif; ?>

</div>

<?= $this->endSection() ?>