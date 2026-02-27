<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div class="space-y-6">
  <header>
    <h1 class="text-2xl font-semibold">Edit Scholar</h1>
  </header>
  <?php if (session()->getFlashdata('error')): ?>
  <div class="p-3 text-red-700 bg-red-100 rounded">
    <?= session()->getFlashdata('error') ?>
  </div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')): ?>
  <div class="p-3 text-red-700 bg-red-100 rounded">
    <ul class="list-disc list-inside">
      <?php foreach (session()->getFlashdata('errors') as $error): ?>
        <li><?= esc($error) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>
  <form action="<?= base_url('school/scholars/update/' . $scholar['id']) ?>" method="post">

    <?= csrf_field() ?>
    
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
      <div class="form-group">
        <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
        <input type="text" name="first_name" class="mt-1 p-2 border border-gray-300 rounded-md w-full" value="<?= esc($scholar['first_name']) ?>" required>
      </div>
      <div class="form-group">
        <label class="block text-sm font-medium text-gray-700">Middle Name</label>
        <input type="text" name="middle_name"
         class="mt-1 p-2 border border-gray-300 rounded-md w-full"
         value="<?= esc($scholar['middle_name']) ?>">
      </div>
      <div class="form-group">
        <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
        <input type="text" name="last_name" class="mt-1 p-2 border border-gray-300 rounded-md w-full" value="<?= esc($scholar['last_name']) ?>" required>
      </div>

      <div class="form-group">
        <label for="gender" class="block text-sm font-medium text-gray-700">Gender</label>
        <select name="gender" class="mt-1 p-2 border border-gray-300 rounded-md w-full" required>
          <option value="male" <?= $scholar['gender'] == 'male' ? 'selected' : '' ?>>Male</option>
          <option value="female" <?= $scholar['gender'] == 'female' ? 'selected' : '' ?>>Female</option>
          <option value="other" <?= $scholar['gender'] == 'other' ? 'selected' : '' ?>>Other</option>
        </select>
      </div>

      <div class="form-group">
        <label for="course" class="block text-sm font-medium text-gray-700">Course</label>
        <input type="text" name="course" class="mt-1 p-2 border border-gray-300 rounded-md w-full" value="<?= esc($scholar['course']) ?>" required>
      </div>

      <div class="form-group">
        <label for="year_level" class="block text-sm font-medium text-gray-700">Year Level</label>
        <input type="number" name="year_level" class="mt-1 p-2 border border-gray-300 rounded-md w-full" value="<?= esc($scholar['year_level']) ?>" required>
      </div>

      <div class="form-group">
        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
        <select name="status" class="mt-1 p-2 border border-gray-300 rounded-md w-full" required>
          <option value="active" <?= $scholar['status'] == 'active' ? 'selected' : '' ?>>Active</option>
          <option value="on-hold" <?= $scholar['status'] == 'on-hold' ? 'selected' : '' ?>>On Hold</option>
          <option value="graduated" <?= $scholar['status'] == 'graduated' ? 'selected' : '' ?>>Graduated</option>
        </select>
      </div>

      <div class="form-group">
        <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
        <input type="date" name="date_of_birth" class="mt-1 p-2 border border-gray-300 rounded-md w-full" value="<?= esc($scholar['date_of_birth']) ?>" required>
      </div>

      <div class="form-group">
        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
        <input type="email" name="email" class="mt-1 p-2 border border-gray-300 rounded-md w-full" value="<?= esc($scholar['email']) ?>" required>
      </div>
    </div>
    <?php if ($user['role'] === 'admin' || $user['role'] === "staff"): ?>
<div class="form-group">
  <label class="block text-sm font-medium text-gray-700">School</label>
  <select name="school_id" class="mt-1 p-2 border border-gray-300 rounded-md w-full" required>
    <?php foreach ($schools as $school): ?>
      <option value="<?= $school['id'] ?>"
        <?= $school['id'] == $scholar['school_id'] ? 'selected' : '' ?>>
        <?= esc($school['name']) ?>
      </option>
    <?php endforeach; ?>
  </select>
</div>
<?php endif; ?>

    <div class="mt-6">
      <button type="submit" class="px-6 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700">Update Scholar</button>
    </div>
  </form>
</div>

<?= $this->endSection() ?>

