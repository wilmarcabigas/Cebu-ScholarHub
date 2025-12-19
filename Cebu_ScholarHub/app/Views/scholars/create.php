<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div class="space-y-6">
  <header>
    <h1 class="text-2xl font-semibold">Add New Scholar</h1>
  </header>
  <?php if (session()->get('errors')): ?>
    <div class="p-3 bg-red-200 text-red-800 rounded">
      <?php foreach (session('errors') as $error): ?>
        <p>• <?= esc($error) ?></p>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
  <form action="/scholars/store" method="post" class="space-y-4">
    <?= csrf_field() ?>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
    

      <div class="form-group">
        <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
        <input type="text" name="first_name" class="mt-1 p-2 border border-gray-300 rounded-md w-full" required>
      </div>
      <div class="form-group">
        <label for="middle_name" class="block text-sm font-medium text-gray-700">Middle Name</label>
        <input type="text" name="middle_name" class="mt-1 p-2 border rounded-md w-full">
      </div>
      <div class="form-group">
        <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
        <input type="text" name="last_name" class="mt-1 p-2 border border-gray-300 rounded-md w-full" required>
      </div>

      <div class="form-group">
        <label for="gender" class="block text-sm font-medium text-gray-700">Gender</label>
        <select name="gender" class="mt-1 p-2 border border-gray-300 rounded-md w-full" required>
          <option value="male">Male</option>
          <option value="female">Female</option>
          <option value="other">Other</option>
        </select>
      </div>

      <div class="form-group">
        <label for="course" class="block text-sm font-medium text-gray-700">Course</label>
        <input type="text" name="course" class="mt-1 p-2 border border-gray-300 rounded-md w-full" required>
      </div>

      <div class="form-group">
        <label for="year_level" class="block text-sm font-medium text-gray-700">Year Level</label>
        <input type="number" name="year_level" class="mt-1 p-2 border border-gray-300 rounded-md w-full" required>
      </div>

      <div class="form-group">
        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
        <select name="status" class="mt-1 p-2 border border-gray-300 rounded-md w-full" required>
          <option value="active">Active</option>
          <option value="on-hold">On Hold</option>
          <option value="graduated">Graduated</option>
        </select>
      </div>

      <div class="form-group">
        <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
        <input type="date" name="date_of_birth" class="mt-1 p-2 border border-gray-300 rounded-md w-full" required>
      </div>

      <div class="form-group">
        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
        <input type="email" name="email" class="mt-1 p-2 border border-gray-300 rounded-md w-full" required>
      </div>
    </div>
  <?php $auth = session()->get('auth_user'); ?>
      <?php if ($auth['role'] === 'admin' || $auth['role'] === "staff"): ?>
      <div class="form-group" >
          <label class="block text-sm font-medium">School</label>
          <select name="school_id" class="mt-1 p-2 border rounded-md w-full" required>
              <option value="">Select School</option>
              <?php foreach ($schools as $school): ?>
                  <option value="<?= $school['id'] ?>">
                      <?= esc($school['name']) ?>
                  </option>
              <?php endforeach; ?>
          </select>
      </div>
        <?php endif; ?>
    <div class="mt-6">
      <button type="submit" class="px-6 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700">Save Scholar</button>
    </div>
  </form>
</div>

<?= $this->endSection() ?>
