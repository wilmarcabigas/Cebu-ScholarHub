# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Development Environment

This is a **CodeIgniter 4** PHP application running on **XAMPP** (Windows).

- **Web server**: Apache via XAMPP (`C:\xampp\htdocs\Cebu ScholarHub\`)
- **Database**: MariaDB 10.4.32 via XAMPP, database name `scholarhub`
- **PHP**: 8.1+ required
- **Base URL**: Configured in `.env` (typically `http://localhost/Cebu%20ScholarHub/Cebu_ScholarHub/public/`)

### Common Commands

```bash
# Run tests
cd "c:\xampp\htdocs\Cebu ScholarHub\Cebu_ScholarHub"
composer test
# or
phpunit

# Run a single test file
phpunit tests/unit/SomeTest.php

# Run CI4 spark commands (requires PHP in PATH or use XAMPP's PHP)
"C:\xampp\php\php.exe" spark migrate
"C:\xampp\php\php.exe" spark db:seed SomeSeeder
"C:\xampp\php\php.exe" spark routes   # List all registered routes

# Direct MySQL access (no password for root by default)
"C:\xampp\mysql\bin\mysql.exe" -u root --host=127.0.0.1 --port=3306 scholarhub
```

### Database Schema Changes

Migrations are in `app/Database/Migrations/`. The project uses CI4's forge-based migrations. When running locally:
- Either run `php spark migrate` via XAMPP's PHP
- Or apply `ALTER TABLE` statements directly via MySQL CLI (preferred for quick fixes)

Verify column existence before adding: `DESCRIBE table_name;`

### Known Database Columns (added outside migrations)

The following columns were added directly via `ALTER TABLE` and are not in a migration file:

```sql
-- users table
ALTER TABLE users
  ADD COLUMN IF NOT EXISTS failed_attempts INT DEFAULT 0,
  ADD COLUMN IF NOT EXISTS unlock_code VARCHAR(10),
  ADD COLUMN IF NOT EXISTS login_code VARCHAR(6),
  ADD COLUMN IF NOT EXISTS login_code_expires_at DATETIME;
```

The `activity_notifications` table exists with schema:
`id, user_id, type, title, message, link, is_read, created_at, actor_id, school_id`

## Architecture Overview

### Role-Based Access Control

Five user roles, each with distinct route groups and views:

| Role | Route Prefix | Access |
|------|-------------|--------|
| `admin` | `/admin/` | Full system access |
| `staff` | `/admin/` + `/staff/` | Billing, reports, scholar management |
| `school_admin` | `/school/` | School's scholars and billing |
| `school_staff` | `/school/` | Same as school_admin |
| `scholar` | `/scholars/` | Own profile only |

Auth is session-based. The session stores `auth_user` (id, email, full_name, role, school_id). Helper functions in `app/Helpers/auth_helper.php` provide `auth_user()`, `auth_id()`, `auth_role()`, `auth_has_role()`, `auth_school_id()`.

Filters in `app/Filters/` enforce authentication (`AuthFilter`) and role checks (`RoleFilter`).

### Login Flow

Two-factor authentication via Gmail OTP:

1. User submits email + password → server validates credentials → sends 6-digit `login_code` via Gmail SMTP → stores code + expiry in `users` table
2. User enters code on OTP screen → server verifies → creates session → redirects to dashboard

Account lockout after 3 failed login attempts: sets `failed_attempts`, sends an unlock code via Gmail, redirects to unlock screen. Unlock verifies the code and then allows a password reset.

**SMTP config** (`app/Config/Email.php`): Gmail SMTP, host `smtp.gmail.com`, port 587, TLS, `$SMTPTimeout = 30`. The login page shows a full-screen loading overlay while the code is being sent to improve perceived UX during the SMTP delay.

**Known issue**: Outbound SMTP ports 587 and 465 may be blocked by Windows Firewall or the network. If Gmail codes are not arriving, check firewall outbound rules.

### Billing Workflow

The core feature of the system. Flow:

1. **School creates billing batch** (`School\BillingController::create/store`) — selects active scholars (₱10,000 fixed per scholar), creates `billing_batches` record + `billing_items` record per scholar
2. **School submits batch** (`submit`) — status: `draft` → `submitted`
3. **Admin/staff receives batch** (`Admin\BillingController::receive`) — status: `submitted` → `received`; creates individual `bills` records per scholar at this step
4. **Admin/staff records payment** (`recordPayment`) — distributes payment amount across unpaid bills sequentially, inserts into `payments` table, status auto-updates to `partial` or `paid` via `BillingBatchModel::updateBatchStatus()`
5. **School confirms receipt** (`School\BillingController::confirmReceipt`) — school acknowledges they received the check/voucher; sets `receipt_confirmed_at` on the batch

Rejection flow: Admin can reject a submitted batch with required `rejection_remarks` (min 10 chars) → status reverts to `draft` → school edits and resubmits.

**Batch statuses:** `draft` → `submitted` → `received` → `partial` / `paid`

### Activity Notifications

`app/Libraries/ActivityNotifier.php` broadcasts in-app notifications to `admin` and `staff` users via the `activity_notifications` table.

- `notifySchoolActivity()` is only called when the actor has role `school_admin` or `school_staff`
- Notification **links must point to admin-accessible routes** (e.g., `admin/billing`) — never `school/billing`, which requires school roles and causes a 403 for admin/staff recipients
- `ActivityNotificationModel` is used by the admin dashboard for the recent activity feed and unread notification badge counts

### Messaging

`app/Controllers/Messages.php` handles direct chat between admin/staff and school users. School roles can only chat with admin/staff and vice versa — enforced by `canChatWith()`.

**Critical column names** in the `messages` table (do not use old names):
- Message content: `message_body` (not `message`)
- Timestamp: `sent_at` (not `created_at`)

These must be consistent across `MessageModel::$allowedFields`, `$createdField = 'sent_at'`, controller insert calls, and view templates.

### Key Models

- **`BillingBatchModel`** — `billing_batches` table. Key methods:
  - `getBatchesForSchool(int $schoolId)` — with `school_name` joined
  - `getAllBatchesForAdmin()` — all batches with `school_name` joined
  - `getBatchWithSchool(int $batchId)` — single batch with `school_name` joined (used by admin)
  - `updateBatchStatus(int $batchId)` — auto-sets `partial`/`paid` based on bills
  - `duplicateExists(int $schoolId, string $semester, string $schoolYear, int $excludeId)` — prevents duplicate batches
- **`BillingItemModel`** — `billing_items` table (file: `BillingitemModel.php`, class: `BillingItemModel`). One row per scholar per batch. Key methods:
  - `getItemsWithScholars(int $batchId)` — joins scholars + schools, returns all columns needed for print views
  - `deleteByBatch(int $batchId)` — used when re-editing a draft
  - `sumByBatch(int $batchId)` — returns total amount for a batch
- **`BillModel`** — `bills` table. Individual bill per scholar in a batch; tracked by `amount_due` / `amount_paid` / `status`. Created only when admin *receives* a batch.
- **`PaymentModel`** — `payments` table. Payment records with `voucher_no`, `payment_date`, `remarks`; linked to a bill via `bill_id`. Has `getPaymentsForBill(int $billId)` and `getPaymentsBySchool(int $schoolId)`.
- **`ScholarModel`** — uses soft deletes; always filter by `deleted_at IS NULL` for active scholars. Has static `maxSemesters(string $type): int` helper returning 4/8/10 based on `scholarship_type`.
- **`MessageModel`** — `messages` table. Uses `message_body` (not `message`) and `sent_at` (not `created_at`). `$createdField = 'sent_at'`. Methods: `getChat()`, `getLatestMessageBetween()`, `markConversationAsRead()`, `countUnreadForUser()`, `getUnreadCountFromSender()`.
- **`ActivityNotificationModel`** — `activity_notifications` table. Used by admin dashboard for recent activity feed and notification badge counts.

### Reports

**Admin reports** (`Admin\ReportsController`, routes under `/admin/reports/`):

| Route | Method | View |
|-------|--------|------|
| `GET /admin/reports` | `index()` | `admin/reports/dashboard` |
| `GET /admin/reports/payment-status` | `paymentStatus()` | `admin/reports/payment-status` |
| `GET /admin/reports/financial-report` | `financialReport()` | `admin/reports/financial-report` |
| `GET /admin/reports/billing-sheets` | `billingSheets()` | `admin/reports/billing-sheets-admin` |
| `GET /admin/reports/scholar-payment-history` | `scholarPaymentHistory()` | `admin/reports/scholar-payment-history-admin` |
| `GET /admin/reports/export-financial` | `exportFinancialReport()` | CSV download |
| `GET /admin/reports/export-scholar` | `exportScholarReport()` | CSV download |

**School reports** (`School\ReportsController`, routes under `/school/reports/`):

| Route | Method | View |
|-------|--------|------|
| `GET /school/reports` | `index()` | `school/reports/index` |
| `GET /school/reports/payment-history` | `paymentHistory()` | `school/reports/payment_history` |
| `GET /school/reports/billing-sheet/{id}` | `billingSheet(int $batchId)` | `school/reports/billing_sheet` |
| `GET /school/reports/status-summary` | `statusSummary()` | `school/reports/status_summary` |
| `GET /school/reports/export-payment-history` | `exportPaymentHistory()` | CSV download |

CSV exports use a UTF-8 BOM and stream directly via `php://output` + `exit`.

### Print Views

Print views are standalone HTML pages (no layout extension) with `window.print()` buttons. They use `@media print { .no-print { display: none; } }` to hide the Print/Close buttons when printing.

| Path | Used by | Notes |
|------|---------|-------|
| `app/Views/bills/print.php` | `School\BillingController::print()` | School-side billing sheet |
| `app/Views/school/billing/print.php` | (duplicate, not directly routed) | Same content as bills/print |
| `app/Views/admin/billing/print.php` | `Admin\BillingController::print()` | Adds school name to batch label |

Print routes:
- `GET /school/billing/print/{id}` → `School\BillingController::print($id)` — enforces school ownership
- `GET /admin/billing/print/{id}` → `Admin\BillingController::print($id)` — uses `getBatchWithSchool()` for `school_name`

### Audit Logging

`log_audit(int $userId, string $actionType, string $tableName, int $recordId, string $details)` in `auth_helper.php` writes to `audit_logs` table. Wrapped in try/catch so failures never break main flow. Call this in every significant controller action (create, update, delete, status changes).

### Admin Dashboard (`app/Views/dashboard/admin.php`)

The admin dashboard has several enhanced sections. When modifying it, keep these in mind:

**Stat Cards** — gradient colored (`from-indigo-500 to-indigo-700`, emerald, amber, violet). Numbers use `data-target="{value}"` for animated JS counters on load. Month-over-month badges (`$mom_scholars`, `$mom_bills`) show `▲/▼ X% vs last month` when data is available.

**Needs Attention panel** — shown between Billing Financial Summary and Quick Actions. Driven by `$attention_batches` (submitted batches pending 7+ days) and `$attention_scholars` (on-hold/disqualified count). Shows a green "All systems normal" message when both are empty. Has an inline JS dismiss button.

**Charts row** (3-column grid):
- Col 1: System Summary doughnut (`systemSummaryChart`) — shows total scholars, active schools, pending bills, unread messages. Center shows `id="chartTotal"`. Legend spans `id="legendScholars/Schools/Bills/Messages"`.
- Col 2: Scholars by School doughnut (`schoolDistributionChart`) — live updated every 5s. Legend dynamically rendered into `id="schoolLegendList"`.
- Col 3: Scholars by Status doughnut (`statusDistributionChart`) — live updated every 5s. Legend into `id="statusLegendList"`.

Required stat card IDs: `totalScholars`, `activeSchools`, `pendingBills`, `unreadMessages`.
Required status badge IDs: `chartStatus`, `schoolChartStatus`, `statusChartStatus`.

**Live stats AJAX** (`/dashboard/live-stats`, every 5s) — updates the 4 stat card numbers and the school/status chart data. Returns `school_chart` and `status_chart` objects with `labels` and `totals` arrays.

**Data passed from `Dashboard::index()` admin case** (in addition to existing stats):

| Variable | Type | Description |
|---|---|---|
| `$enrollment_labels` | `string[]` | Month labels, e.g. `["Mar 2025", ...]` |
| `$enrollment_counts` | `int[]` | Scholar count per month |
| `$mom_scholars` | `float\|null` | % change vs last month (null if no prior data) |
| `$mom_bills` | `float\|null` | % change in submitted batches vs last month |
| `$attention_batches` | `array[]` | Batches with `days_pending`, `school_name`, etc. |
| `$attention_scholars` | `int` | Count of on-hold + disqualified scholars |

### Views & Layout

- All views extend `layouts/base` via `$this->extend('layouts/base')` and use `$this->section('content')`
- **Exception:** print views are standalone HTML (no layout), opened in `target="_blank"` tabs
- Tailwind CSS for all styling (utility classes, no custom CSS build step)
- School billing views: `app/Views/school/billing/` (index, create, edit, view, print)
- Admin billing views: `app/Views/admin/billing/` (index, view, print)
- Admin report views: `app/Views/admin/reports/` (dashboard, payment-status, financial-report, billing-sheets-admin, scholar-payment-history-admin)
- School report views: `app/Views/school/reports/` (index, payment_history, billing_sheet, status_summary)

### Important Conventions

- Controller namespaces: `App\Controllers\Admin\*` and `App\Controllers\School\*` for role-specific controllers
- Models use `$allowedFields` — any new DB column must be added to `allowedFields` or inserts/updates will silently ignore it
- Routes use string-based controller references (e.g., `'Admin\BillingController::index'`), so `use` statements for controller classes at the top of `Routes.php` are unused (pre-existing pattern, not a bug)
- CSRF protection is enabled; all POST forms must include `<?= csrf_field() ?>`
- Flash messages use `session()->setFlashdata('success'|'error', 'message')`
- Notification links must always point to routes accessible by the **recipient's** role, not the sender's (e.g., school actions notify admin/staff, so links go to `admin/billing` not `school/billing`)

### Scholar Scholarship Types

Scholars have three scholarship types stored in `scholars.scholarship_type`:

| Type | Max Semesters | Notes |
|------|--------------|-------|
| `4_semester` | 4 | Default; can be upgraded to `8_semester` |
| `8_semester` | 8 | Upgraded from 4-semester only |
| `10_semester` | 10 | Assigned directly on create/edit; no upgrade path |

**Upgrade rules:**
- Only `4_semester` + `active` scholars can be upgraded — via `POST /scholars/upgrade/{id}`
- Upgrade is one-way: `4_semester` → `8_semester` only
- `upgraded_at` and `upgraded_by` are recorded on upgrade
- `semesters_acquired` is capped per type and enforced in `store()`, `update()`, and `importExcel()`
- Admin/staff can change `scholarship_type` directly via the edit form; school roles see it as read-only

**Key method:** `ScholarModel::maxSemesters(string $type): int` — returns 4, 8, or 10.
