<!doctype html>
<html lang="en" class="h-full bg-gray-50">
<head>
  <meta charset="utf-8">
  <title><?= esc($title ?? 'Cebu Scholars Platform') ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Tailwind (CDN for dev; compile for production) -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          container: { center: true, padding: '1rem' }
        }
      }
    }
  </script>
</head>
<body class="h-full text-gray-900">
  <!-- Top Nav -->
  <nav class="bg-indigo-600 text-white">
    <div class="container flex items-center justify-between h-14">
      <a href="<?= site_url('dashboard') ?>" class="font-semibold tracking-wide">
        Scholars Platform
      </a>

      <?php if (auth_user()): ?>
        <div class="flex items-center gap-3">
          <span class="hidden sm:inline text-indigo-100">
            <?= esc(auth_user()['full_name']) ?>
            <span class="opacity-75"> (<?= esc(auth_user()['role']) ?>)</span>
          </span>
          <a href="<?= site_url('logout') ?>"
             class="inline-flex items-center rounded-md border border-white/30 bg-white/10 px-3 py-1.5 text-sm font-medium hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-white/50">
            Logout
          </a>
        </div>
      <?php endif; ?>
    </div>
  </nav>

  <!-- Page -->
  <main class="container my-8">
    <?= $this->renderSection('content') ?>
  </main>

  <!-- Footer -->
  <footer class="mt-12 border-t border-gray-200">
    <div class="container py-6 text-sm text-gray-500">
      © <?= date('Y') ?> Cebu City Scholars Office • All rights reserved.
    </div>
  </footer>
</body>
</html>
