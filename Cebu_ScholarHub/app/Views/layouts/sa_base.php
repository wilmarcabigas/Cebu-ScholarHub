<!doctype html>
<html lang="en" class="h-full bg-gray-50">
<head>
<meta charset="utf-8">
<title><?= esc($title ?? 'Cebu ScholarHub') ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Tailwind CDN -->
<script src="https://cdn.tailwindcss.com"></script>

<script>
tailwind.config = {
theme: {
extend: {
colors: {
primary: '#15803d'
},
fontFamily: {
sans: ['Inter','system-ui','sans-serif']
},
container: {
center: true,
padding: "1rem"
}
}
}
}
</script>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

</head>

<body class="h-full flex flex-col font-sans text-gray-800 bg-green-50">

<!-- ================= NAVBAR ================= -->
<nav class="bg-green-100 border-b border-gray-200">
<div class="container flex items-center justify-between h-14 bg-green-100">

<a href="<?= site_url('dashboard') ?>" 
class="text-lg font-semibold text-primary bg-gradient-to-r from-green-600 to-green-500 text-white px-3 py-1 rounded-md">
Cebu ScholarHub
</a>

<?php if (auth_user()): ?>
<div class="flex items-center gap-4">

<span class="hidden sm:inline text-sm text-gray-600">
<?= esc(auth_user()['full_name']) ?>
<span class="text-gray-400">(<?= esc(auth_user()['role']) ?>)</span>
</span>

<a href="<?= site_url('logout') ?>"
class="text-sm px-3 py-1.5 rounded-md border border-gray-300 hover:bg-gray-100 transition">
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
<div class="container mt-6">

<?php if ($backUrl): ?>
<a href="<?= esc($backUrl) ?>"
class="text-sm text-primary hover:underline">
← Back
</a>
<?php else: ?>
<button onclick="goBack()"
class="text-sm text-primary hover:underline">
← Back
</button>
<?php endif; ?>

</div>
<?php endif; ?>


<!-- ================= MAIN CONTENT ================= -->
<main class="container flex-1 my-10">
<?= $this->renderSection('content') ?>
</main>


<!-- ================= FOOTER ================= -->
<footer class="bg-white border-t border-gray-200">
<div class="container py-8 text-sm text-gray-600">

<div class="grid md:grid-cols-3 gap-6">

<div>
<h3 class="font-medium text-gray-900 mb-2">Cebu ScholarHub</h3>
<p>
Scholarship management system of the Cebu City Scholars Office.
</p>
</div>

<div>
<h3 class="font-medium text-gray-900 mb-2">Quick Links</h3>

<ul class="space-y-1">
<li>
<a href="<?= site_url('dashboard') ?>" class="hover:text-primary">
Dashboard
</a>
</li>

<?php if (auth_user()): ?>
<li>
<a href="<?= site_url('logout') ?>" class="hover:text-primary">
Logout
</a>
</li>
<?php endif; ?>

</ul>

</div>

<div>
<h3 class="font-medium text-gray-900 mb-2">Contact</h3>

<ul class="space-y-1">
<li>Cebu City Scholars Office</li>
<li>Cebu City, Philippines</li>
<li>503-3024</li>
<li>
<a href="https://www.cebucity.gov.ph/" class="hover:text-primary">
cebucity.gov.ph
</a>
</li>
</ul>

</div>

</div>

<div class="mt-8 pt-4 border-t border-gray-100 text-xs flex flex-col md:flex-row justify-between">

<p>
© <?= date('Y') ?> Cebu City Scholars Office
</p>

<p class="mt-2 md:mt-0">
Cebu ScholarHub • Version 1.0
</p>

</div>

</div>
</footer>


<!-- ================= SCRIPT ================= -->
<script>
function goBack(){
if (window.history.length > 1){
window.history.back();
}else{
window.location.href = "<?= site_url('dashboard') ?>";
}
}
</script>

</body>
</html>