<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<h1 class="text-2xl font-semibold">Scholar Dashboard</h1>
<p class="text-gray-500 mb-4">Welcome, <?= esc($user['full_name']) ?></p>

<section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
  <a href="<?= site_url('scholar/profile') ?>" class="dashboard-card">
    <h2 class="text-base font-semibold">My Profile</h2>
    <p class="text-sm text-gray-500">View your personal scholarship record</p>
  </a>
  <a href="<?= site_url('scholar/billing') ?>" class="dashboard-card">
    <h2 class="text-base font-semibold">Billing Info</h2>
    <p class="text-sm text-gray-500">Check billing and payment updates</p>
  </a>
</section>

<?= $this->endSection() ?>