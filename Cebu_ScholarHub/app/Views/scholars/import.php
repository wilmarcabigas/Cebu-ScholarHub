<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<div class="min-h-screen flex items-center justify-center bg-gradient-to-br">

```
<div class="bg-white shadow-2xl rounded-2xl w-full max-w-lg p-8">

    <!-- Header -->
    <div class="text-center mb-6">
        <div class="text-5xl mb-3">📂</div>
        <h2 class="text-2xl font-bold text-gray-800">Import Scholars</h2>
        <p class="text-gray-500 text-sm mt-1">
            Upload your Excel file (.xlsx, .xls, .csv)
        </p>
    </div>

    <!-- Success Message -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-green-800">
                        Import Successful!
                    </h3>
                    <div class="mt-2 text-sm text-green-700 whitespace-pre-line font-mono">
                        <?= session()->getFlashdata('success') ?>
                    </div>

                    <?php if (session()->getFlashdata('error_report_path')): ?>
                        <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded">
                            <h4 class="text-sm font-semibold text-yellow-800 mb-2">⚠️ Rows with Validation Errors</h4>
                            <p class="text-xs text-yellow-700 mb-3">
                                Some rows had validation issues and were not imported. Review the error report below to see which rows failed and why.
                            </p>
                            <a href="<?= session()->getFlashdata('error_report_path') ?>"
                               download
                               class="inline-block px-4 py-2 bg-yellow-600 text-white hover:bg-yellow-700 font-semibold rounded-lg transition shadow">
                               📥 Download Error Report (CSV)
                            </a>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Error Message -->
    <?php if (session()->getFlashdata('error')): ?>
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                        Import Failed!
                    </h3>
                    <div class="mt-2 text-sm text-red-700 whitespace-pre-line">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Form -->
    <form method="post"
          action="<?= site_url('scholars/import') ?>"
          enctype="multipart/form-data"
          class="space-y-5">

        <?= csrf_field() ?>

        <!-- School Dropdown for Staff -->
        <?php if ($user['role'] === 'staff'): ?>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Assign School
                </label>

                <select name="school_id"
                        required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">

                    <option value="">Select School</option>

                    <?php foreach ($schools as $school): ?>
                        <option value="<?= $school['id'] ?>">
                            <?= esc($school['name']) ?>
                        </option>
                    <?php endforeach; ?>

                </select>

                <p class="text-xs text-gray-500 mt-1">
                    All imported scholars will be assigned to this school.
                </p>

            </div>

        <?php endif; ?>

        <!-- File Upload -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Select Excel File
            </label>

            <input type="file"
                   name="excel_file"
                   accept=".xlsx,.xls,.csv"
                   required
                   class="block w-full text-sm text-gray-600
                          file:mr-4 file:py-2 file:px-4
                          file:rounded-full file:border-0
                          file:text-sm file:font-semibold
                          file:bg-blue-600 file:text-white
                          hover:file:bg-blue-700
                          cursor-pointer"/>
        </div>

        <!-- Button -->
        <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700
                       text-white font-semibold py-3
                       rounded-full transition duration-300
                       shadow-md hover:shadow-lg">
            Upload & Import
        </button>

    </form>

    <!-- Reference Info -->
    <div class="mt-8 p-4 bg-blue-50 rounded-lg border border-blue-200">
        <h3 class="text-sm font-semibold text-blue-900 mb-2">
            📋 Excel Column Order (Required)
        </h3>

        <div class="text-xs text-blue-800 space-y-1">
            <p>1. Semesters Acquired</p>
            <p>2. Voucher No.</p>
            <p>3. Last Name</p>
            <p>4. First Name</p>
            <p>5. Middle Name</p>
            <p>6. Extension</p>
            <p>7. Gender (male/female/other)</p>
            <p>8. Course</p>
            <p>9. Year Level (1-4)</p>
            <p>10. Status (active/on-hold/graduated)</p>
            <p>11. Birthdate</p>
            <p>12. Address</p>
            <p>13. Contact No.</p>
            <p>14. Email Address</p>
            <p>15. LRN No. (12 digits)</p>
            <p>16. School (Elementary)</p>
            <p>17. School (Junior)</p>
            <p>18. School (Senior High School)</p>
        </div>
    </div>

</div>
```

</div>

<?= $this->endSection() ?>
