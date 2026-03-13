<!doctype html>
<html lang="en" class="h-full bg-slate-100">
<head>
  <meta charset="utf-8">
  <title><?= esc($title ?? 'Cebu Scholars Platform') ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Tailwind CDN (DEV ONLY) -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          container: { center: true, padding: '1rem' },
          boxShadow: {
            softxl: '0 20px 45px rgba(15, 23, 42, 0.10)',
            softmd: '0 10px 25px rgba(15, 23, 42, 0.08)',
            glow: '0 10px 30px rgba(79, 70, 229, 0.18)',
            panel: '0 25px 80px rgba(15, 23, 42, 0.10)',
            float: '0 18px 45px rgba(15, 23, 42, 0.08)'
          },
          backgroundImage: {
            'page-soft': 'linear-gradient(135deg, #f8fafc 0%, #eef2ff 45%, #f1f5f9 100%)',
            'brand-soft': 'linear-gradient(135deg, #34348f 0%, #4f46e5 55%, #2563eb 100%)'
          }
        }
      }
    }
  </script>
</head>

<body class="h-full flex flex-col bg-page-soft text-slate-900 antialiased">

  <!-- Decorative background -->
  <div class="pointer-events-none fixed inset-0 overflow-hidden">
    <div class="absolute -top-24 -left-16 h-72 w-72 rounded-full bg-indigo-200/30 blur-3xl"></div>
    <div class="absolute top-1/3 -right-16 h-80 w-80 rounded-full bg-blue-200/30 blur-3xl"></div>
    <div class="absolute bottom-0 left-1/3 h-72 w-72 rounded-full bg-violet-200/20 blur-3xl"></div>
  </div>

  <!-- ================= TOP NAV ================= -->
  <nav class="sticky top-0 z-50 border-b border-white/40 bg-white/75 backdrop-blur-2xl shadow-sm">
    <div class="container flex items-center justify-between h-18 py-3">
      <a href="<?= site_url('dashboard') ?>"
         class="group flex items-center gap-3 transition duration-300">
        <div class="relative flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-soft text-white shadow-glow transition duration-300 group-hover:scale-105 group-hover:rotate-3">
          <div class="absolute inset-0 rounded-2xl bg-white/10"></div>
          <svg class="relative h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l8 4v5c0 5-3.5 8-8 9-4.5-1-8-4-8-9V7l8-4z"/>
          </svg>
        </div>

        <div class="leading-tight">
          <div class="text-base font-extrabold tracking-wide text-slate-900 transition group-hover:text-indigo-700">
            Cebu ScholarHub
          </div>
          <div class="hidden text-[11px] text-slate-500 sm:block">
            Scholarship Management System
          </div>
        </div>
      </a>

      <?php if (auth_user()): ?>
        <div class="flex items-center gap-3">
          <div class="hidden items-center gap-3 rounded-2xl border border-white/70 bg-white/80 px-3 py-2 shadow-sm backdrop-blur md:flex">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-blue-500 text-sm font-bold text-white shadow-md">
              <?= strtoupper(substr(auth_user()['full_name'] ?? 'U', 0, 1)) ?>
            </div>
            <div class="leading-tight">
              <div class="text-sm font-semibold text-slate-800">
                <?= esc(auth_user()['full_name']) ?>
              </div>
              <div class="text-[11px] uppercase tracking-wide text-slate-500">
                <?= esc(auth_user()['role']) ?>
              </div>
            </div>
          </div>

          <span class="hidden sm:inline rounded-full border border-slate-200 bg-white/90 px-3 py-1.5 text-sm text-slate-700 shadow-sm transition hover:border-indigo-200 hover:text-indigo-700 md:hidden">
            <?= esc(auth_user()['full_name']) ?>
            <span class="opacity-70"> (<?= esc(auth_user()['role']) ?>)</span>
          </span>

          <a href="<?= site_url('logout') ?>"
             class="inline-flex items-center rounded-2xl bg-brand-soft px-4 py-2.5 text-sm font-semibold text-white shadow-md transition duration-300 hover:-translate-y-0.5 hover:shadow-glow focus:outline-none focus:ring-2 focus:ring-indigo-300">
            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H6a2 2 0 01-2-2V7a2 2 0 012-2h5a2 2 0 012 2v1"/>
            </svg>
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
    <div class="container mt-6 relative z-10">
      <?php if ($backUrl): ?>
        <a href="<?= esc($backUrl) ?>"
           class="group inline-flex items-center gap-2 rounded-2xl border border-white/80 bg-white/85 px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm backdrop-blur transition duration-300 hover:-translate-y-0.5 hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700 hover:shadow-softmd">
          <span class="transition group-hover:-translate-x-0.5">←</span>
          <span>Back</span>
        </a>
      <?php else: ?>
        <button type="button"
                onclick="goBack()"
                class="group inline-flex items-center gap-2 rounded-2xl border border-white/80 bg-white/85 px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm backdrop-blur transition duration-300 hover:-translate-y-0.5 hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700 hover:shadow-softmd">
          <span class="transition group-hover:-translate-x-0.5">←</span>
          <span>Back</span>
        </button>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <!-- ================= MAIN CONTENT ================= -->
  <main class="container my-8 flex-1 relative z-10">
    <div class="rounded-[30px] border border-white/60 bg-white/55 p-3 shadow-panel backdrop-blur-xl sm:p-4">
      <div class="rounded-[26px] bg-white/75 p-2 sm:p-3">
        <?= $this->renderSection('content') ?>
      </div>
    </div>
  </main>

  <!-- ================= PROFESSIONAL FOOTER ================= -->
  <footer class="relative z-10 mt-12 border-t border-white/50 bg-white/70 backdrop-blur-2xl">
    <div class="container py-10">

      <div class="grid grid-cols-1 gap-8 md:grid-cols-3 text-sm">

        <!-- About -->
        <div class="rounded-[24px] border border-white/70 bg-white/80 p-6 shadow-sm backdrop-blur transition duration-300 hover:-translate-y-1 hover:shadow-softmd">
          <div class="mb-4 flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-600 to-blue-600 text-white shadow-md">
              <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l8 4v5c0 5-3.5 8-8 9-4.5-1-8-4-8-9V7l8-4z"/>
              </svg>
            </div>
            <h3 class="text-slate-900 font-bold">Cebu ScholarHub</h3>
          </div>
          <p class="text-slate-600 leading-relaxed">
            An integrated scholarship management system of the
            <strong>Cebu City Scholars Office</strong> under the
            <strong>Cebu City Government</strong>.
          </p>
        </div>

        <!-- Quick Links -->
        <div class="rounded-[24px] border border-white/70 bg-white/80 p-6 shadow-sm backdrop-blur transition duration-300 hover:-translate-y-1 hover:shadow-softmd">
          <h3 class="text-slate-900 font-bold mb-4">Quick Links</h3>
          <ul class="space-y-3 text-slate-600">
            <li>
              <a href="<?= site_url('dashboard') ?>" class="inline-flex items-center gap-2 transition duration-200 hover:text-indigo-600 hover:translate-x-1">
                <span>•</span>
                <span>Dashboard</span>
              </a>
            </li>
            <?php if (auth_user()): ?>
              <!--  <li>
                <a href="<?= site_url('profile') ?>" class="hover:text-indigo-600">
                  My Profile
                </a>
              </li> -->
              <li>
                <a href="<?= site_url('logout') ?>" class="inline-flex items-center gap-2 transition duration-200 hover:text-indigo-600 hover:translate-x-1">
                  <span>•</span>
                  <span>Logout</span>
                </a>
              </li>
            <?php endif; ?>
          </ul>
        </div>

        <!-- Contact -->
        <div class="rounded-[24px] border border-white/70 bg-white/80 p-6 shadow-sm backdrop-blur transition duration-300 hover:-translate-y-1 hover:shadow-softmd">
          <h3 class="text-slate-900 font-bold mb-4">Contact Information</h3>
          <ul class="space-y-3 text-slate-600">
            <li class="transition hover:text-slate-900">Cebu City Scholars Office</li>
            <li>
              <a href="https://www.cebucity.gov.ph/" target="_blank" rel="noopener noreferrer" class="transition hover:text-indigo-600">
                Website: www.cebucity.gov.ph
              </a>
            </li>
            <li class="transition hover:text-slate-900">Phone: 503-3024</li>
            <li class="transition hover:text-slate-900">Cebu City, Philippines</li>
          </ul>
        </div>

      </div>

      <!-- Bottom Bar -->
      <div class="mt-10 flex flex-col items-center justify-between border-t border-slate-200 pt-6 text-xs text-slate-500 md:flex-row">
        <p class="transition hover:text-slate-700">
          © <?= date('Y') ?> Cebu City Scholars Office. All rights reserved.
        </p>

        <p class="mt-2 transition hover:text-slate-700 md:mt-0">
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