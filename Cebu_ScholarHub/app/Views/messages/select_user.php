<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div class="space-y-6">

    <!-- Header -->
    <header>
        <h1 class="text-2xl font-bold tracking-tight text-gray-900">Messages</h1>
        <p class="mt-1 text-sm text-gray-500">
            Select a user to start a conversation
        </p>
    </header>

    <!-- Users List -->
    <section class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200">
        <header class="px-5 py-4 border-b border-gray-200">
            <h2 class="font-semibold text-gray-800">Available Users</h2>
        </header>

        <?php if (!empty($users)): ?>
            <div class="divide-y divide-gray-200">
                <?php foreach ($users as $user): ?>
                    <a
                        href="<?= site_url('messages/chat/' . $user['id']) ?>"
                        class="flex items-center justify-between px-5 py-4 hover:bg-gray-50 transition group"
                    >
                        <div class="flex items-center gap-4">
                            <!-- Avatar -->
                            <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-sm font-semibold text-indigo-700">
                                <?= strtoupper(substr($user['name'] ?? $user['email'], 0, 1)) ?>
                            </div>

                            <!-- User Info -->
                            <div>
                                <p class="text-sm font-medium text-gray-900">
                                    <?= esc($user['full_name'] ?? 'Unnamed User') ?>
                                </p>
                                <p class="text-xs text-gray-500">
                                    <?= esc($user['email']) ?>
                                </p>
                            </div>
                        </div>

                        <!-- Action -->
                        <span class="text-sm text-indigo-600 group-hover:translate-x-0.5 transition-transform">
                            Open chat →
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="px-5 py-10 text-center">
                <p class="text-sm text-gray-500">No users available for messaging.</p>
            </div>
        <?php endif; ?>
    </section>

</div>

<?= $this->endSection() ?>
