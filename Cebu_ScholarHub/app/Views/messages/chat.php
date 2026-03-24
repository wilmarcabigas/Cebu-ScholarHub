<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<?php
    $otherName = $otherUser['display_name'] ?? $otherUser['school_name'] ?? $otherUser['full_name'] ?? 'User';
    $otherRole = ucwords(str_replace('_', ' ', $otherUser['role'] ?? 'contact'));
    $otherInitial = strtoupper(substr($otherName, 0, 1));
    $otherPerson = $otherUser['full_name'] ?? 'User';
    $otherEmail = $otherUser['email'] ?? '';
?>

<div class="space-y-6">
    <header class="overflow-hidden rounded-[28px] bg-[linear-gradient(135deg,#0f172a_0%,#312e81_55%,#1d4ed8_100%)] px-6 py-6 text-white shadow-[0_20px_60px_rgba(37,99,235,0.22)] sm:px-7">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex min-w-0 items-center gap-4">
                <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-white/12 text-lg font-bold text-white ring-1 ring-white/15">
                    <?= esc($otherInitial) ?>
                </div>

                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="truncate text-2xl font-bold tracking-tight sm:text-3xl"><?= esc($otherName) ?></h1>
                        <span class="rounded-full bg-white/12 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wide text-blue-100 ring-1 ring-white/15">
                            <?= esc($otherRole) ?>
                        </span>
                    </div>
                    <p class="mt-2 text-sm text-blue-100/85">
                        <?= esc($otherPerson) ?><?= $otherEmail !== '' ? ' | ' . esc($otherEmail) : '' ?>
                    </p>
                    <p class="mt-1 text-sm text-blue-100/75">
                        Live updates refresh automatically while you chat.
                    </p>
                </div>
            </div>

            <div class="rounded-2xl bg-white/10 px-4 py-3 ring-1 ring-white/15">
                <div class="text-xs font-semibold uppercase tracking-[0.18em] text-blue-100/80">Messages</div>
                <div class="mt-2 text-2xl font-bold text-white"><?= count($messages ?? []) ?></div>
                <div class="mt-1 text-xs text-blue-100/80">In this conversation</div>
            </div>
        </div>
    </header>

    <section class="overflow-hidden rounded-[28px] bg-white shadow-sm ring-1 ring-slate-200">
        <div class="grid lg:grid-cols-[320px,minmax(0,1fr)]">
            <aside class="border-b border-slate-200 bg-slate-50/70 lg:border-b-0 lg:border-r">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h2 class="text-base font-semibold text-slate-900">Conversations</h2>
                    <p class="mt-1 text-sm text-slate-500">Unread chats are highlighted here.</p>
                </div>

                <div class="max-h-[42rem] overflow-y-auto">
                    <?php foreach (($users ?? []) as $user): ?>
                        <?php
                            $displayName = $user['display_name'] ?? $user['school_name'] ?? $user['full_name'] ?? 'Unnamed User';
                            $roleLabel = ucwords(str_replace('_', ' ', $user['role'] ?? 'user'));
                            $initial = strtoupper(substr($displayName, 0, 1));
                            $isActive = (int) $user['id'] === (int) $other_id;
                            $hasUnread = !empty($user['unread_count']);
                            $personName = $user['full_name'] ?? 'Unnamed User';
                            $email = $user['email'] ?? '';
                        ?>
                        <a href="<?= site_url('messages/chat/' . $user['id']) ?>"
                           class="block border-b border-slate-200/80 px-5 py-4 transition hover:bg-white <?= $isActive ? 'bg-white shadow-sm' : '' ?>">
                            <div class="flex items-start gap-3">
                                <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-2xl bg-[linear-gradient(135deg,#e0e7ff_0%,#dbeafe_100%)] text-sm font-bold text-indigo-700 ring-1 ring-indigo-100">
                                    <?= esc($initial) ?>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="truncate text-sm font-semibold text-slate-900"><?= esc($displayName) ?></p>
                                        <?php if ($hasUnread): ?>
                                            <span class="inline-flex min-w-[1.35rem] items-center justify-center rounded-full bg-rose-500 px-1.5 py-0.5 text-[10px] font-bold text-white">
                                                <?= esc($user['unread_count']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="mt-1 text-[11px] uppercase tracking-wide text-slate-400"><?= esc($roleLabel) ?></p>
                                    <p class="mt-1 truncate text-xs text-slate-500">
                                        <?= esc($personName) ?><?= $email !== '' ? ' | ' . esc($email) : '' ?>
                                    </p>
                                    <p class="mt-2 line-clamp-2 text-xs <?= $hasUnread ? 'font-semibold text-slate-700' : 'text-slate-500' ?>">
                                        <?= esc($user['last_message'] ?? 'No messages yet.') ?>
                                    </p>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </aside>

            <div class="min-w-0">
                <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h2 class="text-base font-semibold text-slate-900">Conversation</h2>
                            <p class="mt-1 text-sm text-slate-500">Press Enter to send. Press Shift + Enter for a new line.</p>
                        </div>

                        <div class="hidden rounded-full bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-600 ring-1 ring-slate-200 sm:inline-flex">
                            Direct chat
                        </div>
                    </div>
                </div>

                <div id="chatMessages" class="max-h-[32rem] min-h-[26rem] space-y-4 overflow-y-auto bg-[linear-gradient(180deg,#f8fafc_0%,#eef2ff_100%)] px-4 py-5 sm:px-6">
                    <?php if (!empty($messages)): ?>
                        <?php foreach ($messages as $msg): ?>
                            <?php $isMine = (int) $msg['sender_id'] === (int) $myId; ?>
                            <div class="flex <?= $isMine ? 'justify-end' : 'justify-start' ?>">
                                <div class="max-w-[85%] sm:max-w-[70%]">
                                    <div class="mb-1 px-1 text-[11px] font-semibold uppercase tracking-wide <?= $isMine ? 'text-right text-indigo-500' : 'text-slate-400' ?>">
                                        <?= $isMine ? 'You' : esc($otherName) ?>
                                    </div>
                                    <div class="rounded-[22px] px-4 py-3 text-sm leading-6 shadow-sm ring-1 <?= $isMine ? 'bg-indigo-600 text-white ring-indigo-500/40 rounded-br-md' : 'bg-white text-slate-800 ring-slate-200 rounded-bl-md' ?>">
                                        <?= nl2br(esc($msg['message_body'])) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="flex h-full min-h-[20rem] items-center justify-center">
                            <div class="max-w-sm text-center">
                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-white text-slate-500 shadow-sm ring-1 ring-slate-200">
                                    <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-4l-4 4v-4z"/>
                                    </svg>
                                </div>
                                <h3 class="mt-4 text-base font-semibold text-slate-900">No messages yet</h3>
                                <p class="mt-2 text-sm text-slate-500">
                                    Start the conversation with a clear message so the other user knows what you need.
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="border-t border-slate-200 bg-white px-4 py-4 sm:px-6">
                    <form id="chatForm" method="post" action="<?= site_url('messages/send') ?>" class="space-y-3">
                        <?= csrf_field() ?>
                        <input type="hidden" name="receiver_id" value="<?= $other_id ?>">

                        <label for="messageInput" class="text-sm font-medium text-slate-700">Send a message</label>

                        <div class="flex flex-col gap-3 sm:flex-row">
                            <textarea
                                id="messageInput"
                                name="message"
                                required
                                rows="3"
                                placeholder="Type your message here..."
                                class="min-h-[3.5rem] flex-1 resize-y rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-indigo-400 focus:bg-white focus:ring-4 focus:ring-indigo-100"
                            ></textarea>

                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-2xl bg-[linear-gradient(135deg,#34348f_0%,#4f46e5_55%,#2563eb_100%)] px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-200 transition duration-300 hover:-translate-y-0.5 hover:shadow-xl"
                            >
                                Send message
                            </button>
                        </div>

                        <p class="text-xs text-slate-400">
                            Press Enter to send. Press Shift + Enter for a new line.
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const chatMessages = document.getElementById('chatMessages');
    const messageForm = document.getElementById('chatForm');
    const messageInput = document.getElementById('messageInput');
    const receiverId = <?= json_encode((int) $other_id) ?>;
    const myId = <?= json_encode((int) $myId) ?>;
    const otherName = <?= json_encode($otherName) ?>;

    function scrollToBottom() {
        if (chatMessages) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    }

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function renderEmptyState() {
        chatMessages.innerHTML = `
            <div class="flex h-full min-h-[20rem] items-center justify-center">
                <div class="max-w-sm text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-white text-slate-500 shadow-sm ring-1 ring-slate-200">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-4l-4 4v-4z"></path>
                        </svg>
                    </div>
                    <h3 class="mt-4 text-base font-semibold text-slate-900">No messages yet</h3>
                    <p class="mt-2 text-sm text-slate-500">Start the conversation with a clear message so the other user knows what you need.</p>
                </div>
            </div>
        `;
    }

    function renderMessages(messages) {
        if (!chatMessages) {
            return;
        }

        if (!messages.length) {
            renderEmptyState();
            return;
        }

        chatMessages.innerHTML = messages.map((msg) => {
            const isMine = Number(msg.sender_id) === Number(myId);

            return `
                <div class="flex ${isMine ? 'justify-end' : 'justify-start'}">
                    <div class="max-w-[85%] sm:max-w-[70%]">
                        <div class="mb-1 px-1 text-[11px] font-semibold uppercase tracking-wide ${isMine ? 'text-right text-indigo-500' : 'text-slate-400'}">
                            ${isMine ? 'You' : escapeHtml(otherName)}
                        </div>
                        <div class="rounded-[22px] px-4 py-3 text-sm leading-6 shadow-sm ring-1 ${isMine ? 'bg-indigo-600 text-white ring-indigo-500/40 rounded-br-md' : 'bg-white text-slate-800 ring-slate-200 rounded-bl-md'}">
                            ${escapeHtml(msg.message_body).replace(/\n/g, '<br>')}
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        scrollToBottom();
    }

    async function refreshMessages() {
        try {
            const response = await fetch(`<?= site_url('messages/fetch') ?>/${receiverId}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!response.ok) {
                return;
            }

            const result = await response.json();
            renderMessages(result.messages || []);
        } catch (error) {
            console.error(error);
        }
    }

    async function submitMessage() {
        const content = messageInput.value.trim();

        if (!content) {
            return;
        }

        const formData = new FormData(messageForm);

        try {
            const response = await fetch(messageForm.action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });

            if (!response.ok) {
                return;
            }

            messageInput.value = '';
            await refreshMessages();
            messageInput.focus();
        } catch (error) {
            console.error(error);
        }
    }

    if (messageInput && messageForm) {
        messageInput.addEventListener('keydown', async function (event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                await submitMessage();
            }
        });

        messageForm.addEventListener('submit', async function (event) {
            event.preventDefault();
            await submitMessage();
        });
    }

    scrollToBottom();
    refreshMessages();
    setInterval(refreshMessages, 3000);
});
</script>

<?= $this->endSection() ?>
