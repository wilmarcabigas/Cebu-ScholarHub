<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<?php
    $authRole = auth_user()['role'] ?? '';
    $audienceLabel = in_array($authRole, ['admin', 'staff'], true)
        ? 'School Admins and School Staff'
        : 'Cebu Admin and Staff';
?>

<div class="space-y-8">
    <header class="overflow-hidden rounded-[28px] bg-[linear-gradient(135deg,#0f172a_0%,#312e81_45%,#2563eb_100%)] px-6 py-7 text-white shadow-[0_20px_60px_rgba(37,99,235,0.22)] sm:px-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl">
                <div class="inline-flex items-center rounded-full bg-white/12 px-3 py-1 text-xs font-semibold tracking-[0.18em] text-blue-100 ring-1 ring-white/15">
                    Real-Time Messaging
                </div>
                <h1 class="mt-4 text-3xl font-bold tracking-tight sm:text-4xl">Inbox</h1>
                <p class="mt-3 max-w-xl text-sm leading-6 text-blue-100/90 sm:text-base">
                    Manage conversations with a cleaner, faster workspace built for day-to-day coordination.
                </p>
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <div class="rounded-2xl bg-white/10 px-4 py-4 ring-1 ring-white/15 backdrop-blur">
                    <div class="text-xs font-semibold uppercase tracking-[0.18em] text-blue-100/80">Contacts</div>
                    <div class="mt-2 text-2xl font-bold text-white"><?= count($users ?? []) ?></div>
                    <div class="mt-1 text-xs text-blue-100/80">Available to chat</div>
                </div>

                <div class="rounded-2xl bg-white/10 px-4 py-4 ring-1 ring-white/15 backdrop-blur">
                    <div class="text-xs font-semibold uppercase tracking-[0.18em] text-blue-100/80">Audience</div>
                    <div class="mt-2 text-sm font-semibold text-white"><?= esc($audienceLabel) ?></div>
                    <div class="mt-1 text-xs text-blue-100/80">Role-based messaging access</div>
                </div>
            </div>
        </div>
    </header>

    <section class="overflow-hidden rounded-[28px] bg-white shadow-sm ring-1 ring-slate-200">
        <div class="px-6 py-16 text-center sm:px-10">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-[28px] bg-[linear-gradient(135deg,#e0e7ff_0%,#dbeafe_100%)] text-indigo-700 shadow-inner ring-1 ring-indigo-100">
                <svg class="h-9 w-9" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.233-3.084A7.612 7.612 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>

            <h2 class="mt-6 text-2xl font-bold tracking-tight text-slate-900">Choose a conversation from your chat badge</h2>
            <p class="mx-auto mt-3 max-w-2xl text-sm leading-6 text-slate-500 sm:text-base">
                The contact list section has been removed from this page. Open a conversation from your unread message badge in the top navigation or go directly to a specific chat.
            </p>

            <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                <a href="<?= site_url('dashboard') ?>"
                   class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:border-indigo-200 hover:text-indigo-700">
                    Back to dashboard
                </a>

                <?php if (!empty($users)): ?>
                    <a href="<?= site_url('messages/chat/' . $users[0]['id']) ?>"
                       class="inline-flex items-center justify-center rounded-2xl bg-[linear-gradient(135deg,#34348f_0%,#4f46e5_55%,#2563eb_100%)] px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-200 transition hover:-translate-y-0.5 hover:shadow-xl">
                        Open latest chat
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<?= $this->endSection() ?>
