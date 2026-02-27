<!doctype html>
<html lang="en" class="h-full bg-gray-50">
<head>
  <meta charset="utf-8">
  <title><?= esc($title ?? 'Cebu Scholars Platform') ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Tailwind CDN (DEV ONLY) -->
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

<body class="h-full flex flex-col text-gray-900">

  <!-- ================= TOP NAV ================= -->
  <nav class="bg-indigo-600 text-white shadow">
    <div class="container flex items-center justify-between h-14">
      <a href="<?= site_url('dashboard') ?>" class="font-semibold tracking-wide">
        Cebu ScholarHub
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

  <!-- ================= OPTIONAL BACK BUTTON ================= -->
  <?php
    $showBack = $show_back ?? false;
    $backUrl  = $back_url ?? null;
  ?>

  <?php if ($showBack): ?>
    <div class="container mt-4">
      <?php if ($backUrl): ?>
        <a href="<?= esc($backUrl) ?>"
           class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800">
          ← Back
        </a>
      <?php else: ?>
        <button type="button"
                onclick="goBack()"
                class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800">
          ← Back
        </button>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <!-- ================= MAIN CONTENT ================= -->
  <main class="container my-8 flex-1">
    <?= $this->renderSection('content') ?>
  </main>

  <!-- ================= PROFESSIONAL FOOTER ================= -->
  <footer class="bg-white border-t border-gray-200 mt-12">
    <div class="container py-10">

      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-sm">

        <!-- About -->
        <div>
          <h3 class="text-gray-900 font-semibold mb-3">Cebu ScholarHub</h3>
          <p class="text-gray-600 leading-relaxed">
            An integrated scholarship management system of the 
            <strong>Cebu City Scholars Office</strong> under the 
            <strong>Cebu City Government</strong>.
          </p>
        </div>

        <!-- Quick Links -->
        <div>
          <h3 class="text-gray-900 font-semibold mb-3">Quick Links</h3>
          <ul class="space-y-2 text-gray-600">
            <li>
              <a href="<?= site_url('dashboard') ?>" class="hover:text-indigo-600">
                Dashboard
              </a>
            </li>
            <?php if (auth_user()): ?>
            <!--  <li>
                <a href="<?= site_url('profile') ?>" class="hover:text-indigo-600">
                  My Profile
                </a>
              </li> -->
              <li>
                <a href="<?= site_url('logout') ?>" class="hover:text-indigo-600">
                  Logout
                </a>
              </li>
            <?php endif; ?>
          </ul>
        </div>

        <!-- Contact -->
        <div>
          <h3 class="text-gray-900 font-semibold mb-3">Contact Information</h3>
          <ul class="space-y-2 text-gray-600">
            <li>Cebu City Scholars Office</li>
            <li>link: https://www.cebucity.gov.ph/</li>
            <li>Phone: 503-3024</li>
            <li>Cebu City, Philippines</li>
          </ul>
        </div>

      </div>

      <!-- Bottom Bar -->
      <div class="mt-10 pt-6 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center text-xs text-gray-500">
        <p>
          © <?= date('Y') ?> Cebu City Scholars Office. All rights reserved.
        </p>

        <p class="mt-2 md:mt-0">
          System Version 1.0 • Developed for Capstone Project
        </p>
      </div>

    </div>
  </footer>

  <!-- ================= SCRIPTS ================= -->
  <script>
    function goBack() {
      if (window.history.length > 1) {
        window.history.back();
      } else {
        window.location.href = "<?= site_url('dashboard') ?>";
      }
    }
  </script>

</body>
</html>