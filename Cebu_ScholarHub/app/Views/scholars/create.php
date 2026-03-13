<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div class="space-y-6">
  <div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Add New Scholar</h2>
        <p class="text-sm text-gray-500">Manage scholar information and details</p>
    </div>

    <a href="<?= site_url('scholars/import') ?>"
       class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm">
        + Import Scholars
    </a>
</div>
  <?php if (session()->get('errors')): ?>
    <div class="p-3 bg-red-200 text-red-800 rounded">
      <?php foreach (session('errors') as $error): ?>
        <p>• <?= esc($error) ?></p>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
  <form action="/scholars/store" method="post" class="space-y-6">
    <?= csrf_field() ?>

    <!-- SECTION 1: PERSONAL INFORMATION -->
    <div class="bg-white shadow-sm rounded-lg p-6">
      <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">Personal Information</h3>
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div class="form-group">
          <label for="first_name" class="block text-sm font-medium text-gray-700">First Name <span class="text-red-500">*</span></label>
          <input type="text" name="first_name" value="<?= old('first_name') ?>" class="mt-1 p-2 border border-gray-300 rounded-md w-full" required>
        </div>
        <div class="form-group">
          <label for="middle_name" class="block text-sm font-medium text-gray-700">Middle Name</label>
          <input type="text" name="middle_name" value="<?= old('middle_name') ?>" class="mt-1 p-2 border rounded-md w-full">
        </div>
        <div class="form-group">
          <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name <span class="text-red-500">*</span></label>
          <input type="text" name="last_name" value="<?= old('last_name') ?>" class="mt-1 p-2 border border-gray-300 rounded-md w-full" required>
        </div>
        <div class="form-group">
          <label for="name_extension" class="block text-sm font-medium text-gray-700">Name Extension</label>
          <select name="name_extension" class="mt-1 p-2 border border-gray-300 rounded-md w-full">
            <option value="">Select Extension</option>
            <option value="Jr." <?= old('name_extension') === 'Jr.' ? 'selected' : '' ?>>Jr.</option>
            <option value="Sr." <?= old('name_extension') === 'Sr.' ? 'selected' : '' ?>>Sr.</option>
            <option value="II" <?= old('name_extension') === 'II' ? 'selected' : '' ?>>II</option>
            <option value="III" <?= old('name_extension') === 'III' ? 'selected' : '' ?>>III</option>
            <option value="IV" <?= old('name_extension') === 'IV' ? 'selected' : '' ?>>IV</option>
            <option value="V" <?= old('name_extension') === 'V' ? 'selected' : '' ?>>V</option>
            <option value="VI" <?= old('name_extension') === 'VI' ? 'selected' : '' ?>>VI</option>
            <option value="VII" <?= old('name_extension') === 'VII' ? 'selected' : '' ?>>VII</option>
            <option value="VIII" <?= old('name_extension') === 'VIII' ? 'selected' : '' ?>>VIII</option>
          </select>
        </div>
      </div>
    </div>

    <!-- SECTION 2: CONTACT & IDENTIFICATION -->
    <div class="bg-white shadow-sm rounded-lg p-6">
      <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">Contact & Identification</h3>
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="form-group">
          <label for="contact_no" class="block text-sm font-medium text-gray-700">Contact Number <span class="text-red-500">*</span></label>
          <input type="tel" name="contact_no" value="<?= old('contact_no') ?>" class="mt-1 p-2 border border-gray-300 rounded-md w-full" placeholder="e.g., 09XXXXXXXXX" required>
        </div>
        <div class="form-group">
          <label for="lrn_no" class="block text-sm font-medium text-gray-700">LRN No. <span class="text-red-500">*</span></label>
          <input type="text" name="lrn_no" value="<?= old('lrn_no') ?>" class="mt-1 p-2 border border-gray-300 rounded-md w-full" placeholder="12-digit number" pattern="[0-9]{12}" maxlength="12" required>
        </div>
        <div class="form-group">
          <label for="email" class="block text-sm font-medium text-gray-700">Email Address <span class="text-red-500">*</span></label>
          <input type="email" name="email" value="<?= old('email') ?>" class="mt-1 p-2 border border-gray-300 rounded-md w-full" required>
        </div>
        <div class="form-group sm:col-span-2">
          <label for="address" class="block text-sm font-medium text-gray-700">Address <span class="text-red-500">*</span></label>
          <textarea name="address" rows="2" class="mt-1 p-2 border border-gray-300 rounded-md w-full" maxlength="500" required><?= old('address') ?></textarea>
        </div>
      </div>
    </div>

    <!-- SECTION 3: ACADEMIC INFORMATION -->
    <div class="bg-white shadow-sm rounded-lg p-6">
      <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">Academic Information</h3>
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div class="form-group">
          <label for="course" class="block text-sm font-medium text-gray-700">Course <span class="text-red-500">*</span></label>
          <input type="text" name="course" value="<?= old('course') ?>" class="mt-1 p-2 border border-gray-300 rounded-md w-full" required>
        </div>
        <div class="form-group">
          <label for="year_level" class="block text-sm font-medium text-gray-700">Year Level <span class="text-red-500">*</span></label>
          <input type="number" name="year_level" value="<?= old('year_level') ?>" class="mt-1 p-2 border border-gray-300 rounded-md w-full" required>
        </div>
        <div class="form-group">
          <label for="semesters_acquired" class="block text-sm font-medium text-gray-700">Semesters Acquired <span class="text-red-500">*</span></label>
          <input type="number" name="semesters_acquired" value="<?= old('semesters_acquired') ?>" min="1" max="8" class="mt-1 p-2 border border-gray-300 rounded-md w-full" required>
        </div>
        <div class="form-group">
          <label for="status" class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
          <select name="status" class="mt-1 p-2 border border-gray-300 rounded-md w-full" required>
            <option value="">Select Status</option>
            <option value="active" <?= old('status') === 'active' ? 'selected' : '' ?>>Active</option>
            <option value="on-hold" <?= old('status') === 'on-hold' ? 'selected' : '' ?>>On Hold</option>
            <option value="graduated" <?= old('status') === 'graduated' ? 'selected' : '' ?>>Graduated</option>
          </select>
        </div>

        <?php $auth = session()->get('auth_user'); ?>
        <?php if ($auth['role'] === 'admin' || $auth['role'] === "staff"): ?>
        <div class="form-group">
          <label for="school_id" class="block text-sm font-medium text-gray-700">School <span class="text-red-500">*</span></label>
          <select name="school_id" class="mt-1 p-2 border border-gray-300 rounded-md w-full" required>
            <option value="">Select School</option>
            <?php foreach ($schools as $school): ?>
              <option value="<?= $school['id'] ?>" <?= old('school_id') == $school['id'] ? 'selected' : '' ?>>
                <?= esc($school['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- SECTION 4: SCHOOL HISTORY -->
    <div class="bg-white shadow-sm rounded-lg p-6">
      <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">School History</h3>
      <div class="grid grid-cols-1 gap-4">
        <div class="form-group">
          <label for="school_elementary" class="block text-sm font-medium text-gray-700">Elementary School <span class="text-red-500">*</span></label>
          <input type="text" name="school_elementary" value="<?= old('school_elementary') ?>" class="mt-1 p-2 border border-gray-300 rounded-md w-full" required>
        </div>
        <div class="form-group">
          <label for="school_junior" class="block text-sm font-medium text-gray-700">Junior High School <span class="text-red-500">*</span></label>
          <input type="text" name="school_junior" value="<?= old('school_junior') ?>" class="mt-1 p-2 border border-gray-300 rounded-md w-full" required>
        </div>
        <div class="form-group">
          <label for="school_senior_high" class="block text-sm font-medium text-gray-700">Senior High School <span class="text-red-500">*</span></label>
          <input type="text" name="school_senior_high" value="<?= old('school_senior_high') ?>" class="mt-1 p-2 border border-gray-300 rounded-md w-full" required>
        </div>
      </div>
    </div>

    <!-- SECTION 5: BIOGRAPHICAL -->
    <div class="bg-white shadow-sm rounded-lg p-6">
      <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">Biographical Information</h3>
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="form-group">
          <label for="gender" class="block text-sm font-medium text-gray-700">Gender <span class="text-red-500">*</span></label>
          <select name="gender" class="mt-1 p-2 border border-gray-300 rounded-md w-full" required>
            <option value="">Select Gender</option>
            <option value="male" <?= old('gender') === 'male' ? 'selected' : '' ?>>Male</option>
            <option value="female" <?= old('gender') === 'female' ? 'selected' : '' ?>>Female</option>
            <option value="other" <?= old('gender') === 'other' ? 'selected' : '' ?>>Other</option>
          </select>
        </div>
        <div class="form-group">
          <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth <span class="text-red-500">*</span></label>
          <input type="date" name="date_of_birth" value="<?= old('date_of_birth') ?>" class="mt-1 p-2 border border-gray-300 rounded-md w-full" required>
        </div>
      </div>
    </div>

    <!-- SECTION 6: VOUCHER -->
    <div class="bg-white shadow-sm rounded-lg p-6">
      <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">Voucher Information</h3>
      <div class="form-group">
        <label for="voucher_no" class="block text-sm font-medium text-gray-700">Voucher Number <span class="text-red-500">*</span></label>
        <input type="text" name="voucher_no" value="<?= old('voucher_no') ?>" class="mt-1 p-2 border border-gray-300 rounded-md w-full" required>
      </div>
    </div>

    <div class="mt-6 flex gap-2">
      <button type="submit" class="px-6 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700">Save Scholar</button>
      <a href="<?= site_url('scholars') ?>" class="px-6 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">Cancel</a>
    </div>
  </form>
</div>

<?= $this->endSection() ?>
