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

### Billing Workflow

The core feature of the system. Flow:

1. **School creates billing batch** (`School\BillingController::create/store`) — selects active scholars (₱10,000 fixed per scholar), creates `billing_batches` record + `billing_items` per scholar + individual `bills` per scholar
2. **School submits batch** (`submit`) — status: `draft` → `submitted`
3. **Admin/staff receives batch** (`Admin\BillingController::receive`) — status: `submitted` → `received`
4. **Admin/staff records payment** (`recordPayment`) — distributes payment amount across unpaid bills sequentially, inserts into `payments` table, status auto-updates to `partial` or `paid` via `BillingBatchModel::updateBatchStatus()`
5. **School confirms receipt** (`School\BillingController::confirmReceipt`) — school acknowledges they received the check/voucher; sets `receipt_confirmed_at` on the batch

Rejection flow: Admin can reject a submitted batch with required `rejection_remarks` (min 10 chars) → status reverts to `draft` → school edits and resubmits.

### Key Models

- **`BillingBatchModel`** — has `updateBatchStatus(int $batchId)` (auto-sets partial/paid) and `duplicateExists(int $schoolId, string $semester, string $schoolYear, int $excludeId)` (prevents duplicates)
- **`BillModel`** — individual bill per scholar in a batch; tracked by `amount_due` / `amount_paid` / `status`
- **`PaymentModel`** — payment records with `voucher_no`, `payment_date`, `remarks`; linked to billing batch via `billing_batch_id`
- **`ScholarModel`** — uses soft deletes; always filter by `deleted_at IS NULL` for active scholars. Has static `maxSemesters(string $type): int` helper returning 4/8/10 based on `scholarship_type`

### Audit Logging

`log_audit(int $userId, string $actionType, string $tableName, int $recordId, string $details)` in `auth_helper.php` writes to `audit_logs` table. Wrapped in try/catch so failures never break main flow. Call this in every significant controller action (create, update, delete, status changes).

### Views & Layout

- All views extend `layouts/base` via `$this->extend('layouts/base')` and use `$this->section('content')`
- Tailwind CSS for all styling (utility classes, no custom CSS build step)
- School billing views: `app/Views/school/billing/` (index, create, edit, view, print)
- Admin billing views: `app/Views/admin/billing/` (index, view, print)

### Important Conventions

- Controller namespaces: `App\Controllers\Admin\*` and `App\Controllers\School\*` for role-specific controllers
- Models use `$allowedFields` — any new DB column must be added to `allowedFields` or inserts/updates will silently ignore it
- Routes use string-based controller references (e.g., `'Admin\BillingController::index'`), so `use` statements for controller classes at the top of `Routes.php` are unused (pre-existing pattern, not a bug)
- CSRF protection is enabled; all POST forms must include `<?= csrf_field() ?>`
- Flash messages use `session()->setFlashdata('success'|'error', 'message')`

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
