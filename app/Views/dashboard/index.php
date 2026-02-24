<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<?php
  $role = $user['role'] ?? '';
?>

<div class="space-y-6">
  <header class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
    <div>
      <h1 class="text-2xl font-semibold tracking-tight">
        Welcome, <?= esc($user['full_name']) ?>!
      </h1>
      <p class="text-sm text-gray-500 mt-1">
        You are logged in as <span class="font-medium"><?= esc($role) ?></span>.
      </p>
    </div>
  </header>

  <!-- Quick Cards -->
  <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    <?php if (in_array($role, ['admin','staff'])): ?>
      <a href="<?= site_url('admin/users') ?>"
         class="group rounded-2xl bg-white ring-1 ring-gray-200 p-5 hover:shadow-sm transition">
        <div class="flex items-center justify-between">
          <h2 class="text-base font-semibold">Admin Area</h2>
          <span class="text-indigo-600 group-hover:translate-x-0.5 transition">→</span>
        </div>
        <p class="mt-1 text-sm text-gray-500">Manage users, roles, and reports.</p>
      </a>
    <?php endif; ?>

    <?php if (in_array($role, ['school_admin','school_staff'])): ?>
      <a href="<?= site_url('school/scholars') ?>"
         class="group rounded-2xl bg-white ring-1 ring-gray-200 p-5 hover:shadow-sm transition">
        <div class="flex items-center justify-between">
          <h2 class="text-base font-semibold">School Area</h2>
          <span class="text-indigo-600 group-hover:translate-x-0.5 transition">→</span>
        </div>
        <p class="mt-1 text-sm text-gray-500">Manage your scholars and billing.</p>
      </a>
    <?php endif; ?>

    <?php if ($role === 'scholar'): ?>
      <a href="<?= site_url('scholar/profile') ?>"
         class="group rounded-2xl bg-white ring-1 ring-gray-200 p-5 hover:shadow-sm transition">
        <div class="flex items-center justify-between">
          <h2 class="text-base font-semibold">Scholar Area</h2>
          <span class="text-indigo-600 group-hover:translate-x-0.5 transition">→</span>
        </div>
        <p class="mt-1 text-sm text-gray-500">View your profile and scholarship status.</p>
      </a>
    <?php endif; ?>
  </section>

  <!-- Placeholder analytics row (safe to remove later) -->
  <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    <div class="rounded-2xl bg-white ring-1 ring-gray-200 p-5">
      <div class="text-sm text-gray-500">Total Scholars</div>
      <div class="mt-1 text-2xl font-semibold">—</div>
    </div>
    <div class="rounded-2xl bg-white ring-1 ring-gray-200 p-5">
      <div class="text-sm text-gray-500">Bills Pending</div>
      <div class="mt-1 text-2xl font-semibold">—</div>
    </div>
    <div class="rounded-2xl bg-white ring-1 ring-gray-200 p-5">
      <div class="text-sm text-gray-500">Payments Marked</div>
      <div class="mt-1 text-2xl font-semibold">—</div>
    </div>
  </section>
</div>

<?= $this->endSection() ?>
