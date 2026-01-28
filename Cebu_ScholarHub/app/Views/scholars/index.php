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

  <!-- Quick Actions -->
   
  <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    <?php if (in_array($role, ['staff','school_admin','school_staff'])): ?>
      <a href="<?= site_url('scholars/create') ?>" class="group rounded-2xl bg-white ring-1 ring-gray-200 p-5 hover:shadow-lg transition">
        <div class="flex items-center justify-between">
          <h2 class="text-base font-semibold">Add New Scholar</h2>
          <span class="text-indigo-600 group-hover:translate-x-0.5 transition">→</span>
        </div>
        <p class="mt-1 text-sm text-gray-500">Create a new scholar record.</p>
      </a>
    <?php endif; ?>
      <!-- 
    <a href="<?= site_url('scholars') ?>" class="group rounded-2xl bg-white ring-1 ring-gray-200 p-5 hover:shadow-lg transition">
      <div class="flex items-center justify-between">
        <h2 class="text-base font-semibold">Manage Scholars</h2>
        <span class="text-indigo-600 group-hover:translate-x-0.5 transition">→</span>
      </div>
      <p class="mt-1 text-sm text-gray-500">View, edit, and delete scholar records.</p>
    </a> -->
  </section>
      
  <section class="mt-6">
    <h2 class="text-xl font-semibold">Scholar List</h2>
    <div class="overflow-hidden bg-white shadow rounded-lg">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">First Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <?php foreach ($scholars as $scholar): ?>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= esc($scholar['first_name']) ?></td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= esc($scholar['last_name']) ?></td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= esc($scholar['course']) ?></td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= esc($scholar['status']) ?></td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <a href="/scholars/edit/<?= $scholar['id'] ?>" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                <a href="/scholars/delete/<?= $scholar['id'] ?>" class="text-red-600 hover:text-red-900 ml-4">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
</div>

<?= $this->endSection() ?>
