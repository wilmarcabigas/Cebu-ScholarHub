<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div class="min-h-screen flex items-center justify-center bg-gradient-to-br">

    <div class="bg-white shadow-2xl rounded-2xl w-full max-w-lg p-8">

        <!-- Header -->
        <div class="text-center mb-6">
            <div class="text-5xl mb-3">📂</div>
            <h2 class="text-2xl font-bold text-gray-800">Import Scholars</h2>
            <p class="text-gray-500 text-sm mt-1">
                Upload your Excel file (.xlsx, .xls, .csv)
            </p>
        </div>

        <!-- Form -->
        <form method="post" 
              action="<?= base_url('scholars/import') ?>" 
              enctype="multipart/form-data"
              class="space-y-5">

            <!-- File Input -->
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

    </div>

</div>

<?= $this->endSection() ?>
