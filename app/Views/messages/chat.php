<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div class="space-y-6">

    <!-- Header -->
    <header>
        <h1 class="text-2xl font-bold tracking-tight text-gray-900">
            Chat
        </h1>
        <p class="mt-1 text-sm text-gray-500">
            Conversation with <?= esc($otherUser['name'] ?? $otherUser['email'] ?? 'User') ?>
        </p>
    </header>

    <!-- Chat Box -->
    <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-5 space-y-3 h-96 overflow-y-auto">

        <?php if (!empty($messages)): ?>
            <?php foreach ($messages as $msg): ?>
                <div class="<?= $msg['sender_id'] == $myId ? 'text-right' : 'text-left' ?>">
                    <div class="inline-block px-4 py-2 rounded-lg text-sm
                        <?= $msg['sender_id'] == $myId
                            ? 'bg-indigo-600 text-white'
                            : 'bg-gray-100 text-gray-900'
                        ?>">
                        <?= esc($msg['message']) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-sm text-gray-500 text-center">
                No messages yet. Start the conversation 👋
            </p>
        <?php endif; ?>

    </div>

    <!-- Send Message -->
    <form method="post" action="<?= site_url('messages/send') ?>" class="flex gap-3">
        <?= csrf_field() ?>
        <input type="hidden" name="receiver_id" value="<?= $other_id ?>">

        <input
            type="text"
            name="message"
            required
            placeholder="Type your message…"
            class="flex-1 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
        >

        <button
            type="submit"
            class="px-5 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition"
        >
            Send
        </button>
    </form>

</div>

<?= $this->endSection() ?>
