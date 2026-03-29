<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div class="space-y-6">
    <section class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <div class="flex flex-col gap-4 border-b border-slate-100 pb-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <span class="inline-flex rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold tracking-wide text-amber-700 ring-1 ring-amber-100">
                    Admin Monitoring
                </span>
                <h1 class="mt-3 text-2xl font-bold text-slate-900">Activity and Audit Logs</h1>
                <p class="mt-2 text-sm text-slate-500">Partner school actions are recorded here with timeline entries and detailed audit data.</p>
            </div>

            <a href="<?= site_url('dashboard') ?>"
               class="inline-flex items-center rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                Back to dashboard
            </a>
        </div>

        <form method="get" class="mt-6 grid gap-4 md:grid-cols-4">
            <label class="block">
                <span class="mb-2 block text-sm font-medium text-slate-700">Search</span>
                <input type="text" name="search" value="<?= esc($filters['search'] ?? '') ?>"
                       class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"
                       placeholder="Actor, school, title">
            </label>

            <label class="block">
                <span class="mb-2 block text-sm font-medium text-slate-700">School</span>
                <select name="school_id"
                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                    <option value="">All schools</option>
                    <?php foreach ($schools as $school): ?>
                        <option value="<?= esc($school['id']) ?>" <?= (string) ($filters['school_id'] ?? '') === (string) $school['id'] ? 'selected' : '' ?>>
                            <?= esc($school['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label class="block">
                <span class="mb-2 block text-sm font-medium text-slate-700">Event Type</span>
                <input type="text" name="event_type" value="<?= esc($filters['event_type'] ?? '') ?>"
                       class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"
                       placeholder="ex. scholar_updated">
            </label>

            <div class="flex items-end gap-3">
                <button type="submit"
                        class="inline-flex flex-1 items-center justify-center rounded-2xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700">
                    Filter Logs
                </button>
                <a href="<?= site_url('admin/logs') ?>"
                   class="inline-flex items-center justify-center rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50">
                    Reset
                </a>
            </div>
        </form>
    </section>

    <section class="grid gap-6 xl:grid-cols-2">
        <div class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="mb-5 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Activity Log</h2>
                    <p class="text-sm text-slate-500">Readable timeline of partner school actions.</p>
                </div>
                <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                    <?= count($activityLogs) ?> entries
                </span>
            </div>

            <div class="space-y-4">
                <?php if ($activityLogs): ?>
                    <?php foreach ($activityLogs as $log): ?>
                        <article class="rounded-3xl border border-slate-200 bg-slate-50/70 p-5">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h3 class="text-base font-semibold text-slate-900"><?= esc($log['title']) ?></h3>
                                    <p class="mt-2 text-sm leading-6 text-slate-600"><?= esc($log['description']) ?></p>
                                </div>
                                <span class="rounded-full bg-white px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500">
                                    <?= esc(str_replace('_', ' ', $log['event_type'])) ?>
                                </span>
                            </div>

                            <div class="mt-4 grid gap-2 text-sm text-slate-500">
                                <div><strong class="text-slate-700">Actor:</strong> <?= esc($log['actor_name'] ?? 'Unknown user') ?><?= !empty($log['actor_role']) ? ' (' . esc($log['actor_role']) . ')' : '' ?></div>
                                <div><strong class="text-slate-700">School:</strong> <?= esc($log['school_name'] ?? 'No school') ?></div>
                                <div><strong class="text-slate-700">Subject:</strong> <?= esc(($log['subject_type'] ?? 'record') . (!empty($log['subject_id']) ? ' #' . $log['subject_id'] : '')) ?></div>
                                <div><strong class="text-slate-700">Request:</strong> <?= esc(($log['request_method'] ?? 'CLI') . ' /' . ($log['request_path'] ?? '')) ?></div>
                                <div><strong class="text-slate-700">When:</strong> <?= esc($log['created_at']) ?></div>
                            </div>

                            <?php if (!empty($log['metadata_array'])): ?>
                                <pre class="mt-4 overflow-x-auto rounded-2xl bg-slate-900 p-4 text-xs text-slate-100"><?= esc(json_encode($log['metadata_array'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) ?></pre>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="rounded-3xl border border-dashed border-slate-300 px-5 py-10 text-center text-sm text-slate-500">
                        No activity log entries found for the current filter.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="mb-5 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Audit Log</h2>
                    <p class="text-sm text-slate-500">Detailed before and after values for sensitive school account changes.</p>
                </div>
                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                    <?= count($auditLogs) ?> entries
                </span>
            </div>

            <div class="space-y-4">
                <?php if ($auditLogs): ?>
                    <?php foreach ($auditLogs as $log): ?>
                        <article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h3 class="text-base font-semibold text-slate-900"><?= esc($log['activity_title'] ?? ucfirst(str_replace('_', ' ', $log['event_type']))) ?></h3>
                                    <p class="mt-1 text-sm text-slate-500">
                                        <?= esc($log['actor_name'] ?? 'Unknown user') ?> • <?= esc($log['school_name'] ?? 'No school') ?> • <?= esc($log['created_at']) ?>
                                    </p>
                                </div>
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-600">
                                    <?= esc($log['action']) ?>
                                </span>
                            </div>

                            <div class="mt-4 grid gap-2 text-sm text-slate-500">
                                <div><strong class="text-slate-700">Event:</strong> <?= esc($log['event_type']) ?></div>
                                <div><strong class="text-slate-700">Record:</strong> <?= esc(($log['auditable_type'] ?? 'record') . (!empty($log['auditable_id']) ? ' #' . $log['auditable_id'] : '')) ?></div>
                                <div><strong class="text-slate-700">Request:</strong> <?= esc(($log['request_method'] ?? 'CLI') . ' /' . ($log['request_path'] ?? '')) ?></div>
                                <div><strong class="text-slate-700">IP:</strong> <?= esc($log['ip_address'] ?? 'Unknown') ?></div>
                            </div>

                            <?php if (!empty($log['old_values_array'])): ?>
                                <div class="mt-4">
                                    <div class="mb-2 text-xs font-semibold uppercase tracking-[0.2em] text-rose-600">Old Values</div>
                                    <pre class="overflow-x-auto rounded-2xl bg-rose-50 p-4 text-xs text-slate-800"><?= esc(json_encode($log['old_values_array'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) ?></pre>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($log['new_values_array'])): ?>
                                <div class="mt-4">
                                    <div class="mb-2 text-xs font-semibold uppercase tracking-[0.2em] text-emerald-600">New Values</div>
                                    <pre class="overflow-x-auto rounded-2xl bg-emerald-50 p-4 text-xs text-slate-800"><?= esc(json_encode($log['new_values_array'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) ?></pre>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($log['metadata_array'])): ?>
                                <div class="mt-4">
                                    <div class="mb-2 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Metadata</div>
                                    <pre class="overflow-x-auto rounded-2xl bg-slate-900 p-4 text-xs text-slate-100"><?= esc(json_encode($log['metadata_array'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) ?></pre>
                                </div>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="rounded-3xl border border-dashed border-slate-300 px-5 py-10 text-center text-sm text-slate-500">
                        No audit log entries found for the current filter.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<?= $this->endSection() ?>
