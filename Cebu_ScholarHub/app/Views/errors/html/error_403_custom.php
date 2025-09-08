<!doctype html>
<html lang="en" class="h-full bg-gray-50">
<head>
  <meta charset="utf-8">
  <title>403 Forbidden</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full">
  <div class="min-h-full grid place-items-center p-6">
    <div class="w-full max-w-lg bg-white rounded-2xl p-8 ring-1 ring-gray-200">
      <div class="flex items-center gap-3">
        <div class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-rose-100 text-rose-600 font-semibold">403</div>
        <h1 class="text-xl font-semibold">Forbidden</h1>
      </div>
      <p class="mt-3 text-gray-600">
        You don’t have permission to access this resource.
      </p>
      <div class="mt-6">
        <a href="<?= site_url('dashboard') ?>"
           class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2.5 text-white font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
          Back to Dashboard
        </a>
      </div>
    </div>
  </div>
</body>
</html>
