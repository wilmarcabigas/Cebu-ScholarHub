<?php

namespace App\Controllers;

use App\Libraries\ActivityNotifier;
use App\Models\ScholarModel;
use App\Models\SchoolModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use CodeIgniter\Exceptions\PageNotFoundException;

class ScholarController extends BaseController
{
    protected $scholarModel;
    protected $schoolModel;
    protected $activityNotifier;

    public function __construct()
    {
        $this->scholarModel = new ScholarModel();
        $this->schoolModel  = new SchoolModel();
        $this->activityNotifier = new ActivityNotifier();
    }

    public function index()
    {
        $authUser        = session()->get('auth_user');
        $selectedSchool  = $this->request->getGet('school_id');
        $selectedSemester = $this->request->getGet('semester'); // '1st', '2nd', or ''

        $scholars = $this->scholarModel
            ->select('scholars.id, scholars.school_id, scholars.first_name, scholars.last_name, scholars.middle_name, scholars.gender, scholars.course, scholars.year_level, scholars.status, scholars.date_of_birth, scholars.email, scholars.semesters_acquired, scholars.scholarship_type, scholars.upgraded_at, scholars.voucher_no, scholars.name_extension, scholars.address, scholars.contact_no, scholars.lrn_no, scholars.school_elementary, scholars.school_junior, scholars.school_senior_high, scholars.created_at, scholars.updated_at, scholars.deleted_at, schools.name as school_name')
            ->join('schools', 'schools.id = scholars.school_id', 'left');

        // If school staff/admin: only their school
        if (in_array($authUser['role'], ['school_admin', 'school_staff'])) {
            $scholars->where('scholars.school_id', $authUser['school_id']);
        }
        // Admin/staff: can filter by school from dropdown
        elseif (!empty($selectedSchool)) {
            $scholars->where('scholars.school_id', $selectedSchool);
        }

        // Semester filter: odd semesters_acquired = 1st sem, even = 2nd sem
        if ($selectedSemester === '1st') {
            $scholars->whereIn('scholars.semesters_acquired', [1, 3, 5, 7]);
        } elseif ($selectedSemester === '2nd') {
            $scholars->whereIn('scholars.semesters_acquired', [2, 4, 6, 8]);
        }

        $data = [
            'title'             => 'Manage Scholars',
            'scholars'          => $scholars->findAll(),
            'schools'           => $this->schoolModel->findAll(),
            'selectedSchool'    => $selectedSchool,
            'selectedSemester'  => $selectedSemester,
            'user'              => $authUser,
            'show_back'         => true,
            'back_url'          => site_url('dashboard')
        ];

        return view('scholars/index', $data);
    }

    public function create()
    {
        $authUser = session()->get('auth_user');

        return view('scholars/create', [
            'title'   => 'Add New Scholar',
            'schools' => $this->schoolModel->findAll(),
            'user'    => $authUser,
            'show_back' => true,
            'back_url'  => site_url('scholars')
        ]);
    }

    public function store()
    {
        $authUser = session()->get('auth_user');

        $schoolId = in_array($authUser['role'], ['school_admin', 'school_staff'])
            ? $authUser['school_id']
            : $this->request->getPost('school_id');

        $scholarshipType    = $this->request->getPost('scholarship_type') ?: '4_semester';
        $semestersAcquired  = (int) $this->request->getPost('semesters_acquired');
        $maxSemesters       = ScholarModel::maxSemesters($scholarshipType);

        if ($semestersAcquired > $maxSemesters) {
            return redirect()->back()
                ->withInput()
                ->with('errors', ["Semesters acquired cannot exceed {$maxSemesters} for a {$scholarshipType} scholar."]);
        }

        $data = [
            'school_id'          => $schoolId,
            'first_name'         => $this->request->getPost('first_name'),
            'middle_name'        => $this->request->getPost('middle_name'),
            'last_name'          => $this->request->getPost('last_name'),
            'name_extension'     => $this->request->getPost('name_extension'),
            'gender'             => $this->request->getPost('gender'),
            'date_of_birth'      => $this->request->getPost('date_of_birth'),
            'email'              => $this->request->getPost('email'),
            'contact_no'         => $this->request->getPost('contact_no'),
            'address'            => $this->request->getPost('address'),
            'course'             => $this->request->getPost('course'),
            'year_level'         => $this->request->getPost('year_level'),
            'status'             => $this->request->getPost('status'),
            'semesters_acquired' => $semestersAcquired,
            'scholarship_type'   => $scholarshipType,
            'voucher_no'         => $this->request->getPost('voucher_no'),
            'lrn_no'             => $this->request->getPost('lrn_no'),
            'school_elementary'  => $this->request->getPost('school_elementary'),
            'school_junior'      => $this->request->getPost('school_junior'),
            'school_senior_high' => $this->request->getPost('school_senior_high'),
        ];

        $scholarId = $this->scholarModel->insert($data);

        if (!$scholarId) {
            return redirect()->back()
                ->with('errors', $this->scholarModel->errors())
                ->withInput();
        }

        $school = $this->schoolModel->find($schoolId);
        $schoolName = $school['name'] ?? 'Unknown School';
        $scholarName = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));

        $this->activityNotifier->notifySchoolActivity(
            $authUser,
            'scholar_created',
            'New scholar added',
            "{$authUser['full_name']} added {$scholarName} for {$schoolName}.",
            site_url('scholars'),
            (int) $schoolId
        );

        return redirect()->to(site_url('scholars'))
            ->with('message', 'Scholar added successfully');
    }

    public function edit($id)
    {
        $authUser = session()->get('auth_user');
        $scholar  = $this->scholarModel->find($id);

        if (!$scholar) {
            return redirect()->to(site_url('scholars'))
                ->with('error', 'Scholar not found');
        }

        if (in_array($authUser['role'], ['school_admin', 'school_staff']) &&
            $scholar['school_id'] != $authUser['school_id']) {
            return redirect()->to(site_url('scholars'))
                ->with('error', 'Unauthorized access');
        }

        return view('scholars/edit', [
            'title'      => 'Edit Scholar',
            'scholar'    => $scholar,
            'schools'    => $this->schoolModel->findAll(),
            'user'       => $authUser,
            'show_back'  => true,
            'back_url'   => site_url('scholars')
        ]);
    }

    public function update($id)
{
    $model = new ScholarModel();
    $authUser = session()->get('auth_user');

    $validationRules = $model->scholarValidationRules($id);

    if (!$this->validate($validationRules)) {
        return redirect()->back()
            ->withInput()
            ->with('errors', $this->validator->getErrors());
    }

    // FIX SCHOOL ID BASED ON ROLE
    $schoolId = in_array($authUser['role'], ['school_admin', 'school_staff'])
        ? $authUser['school_id']
        : $this->request->getPost('school_id');

    $scholarshipType   = $this->request->getPost('scholarship_type') ?: '4_semester';
    $semestersAcquired = (int) $this->request->getPost('semesters_acquired');
    $maxSemesters      = ScholarModel::maxSemesters($scholarshipType);

    if ($semestersAcquired > $maxSemesters) {
        return redirect()->back()
            ->withInput()
            ->with('errors', ["Semesters acquired cannot exceed {$maxSemesters} for a {$scholarshipType} scholar."]);
    }

    $data = [
        'school_id'          => $schoolId,
        'first_name'         => $this->request->getPost('first_name'),
        'middle_name'        => $this->request->getPost('middle_name'),
        'last_name'          => $this->request->getPost('last_name'),
        'name_extension'     => $this->request->getPost('name_extension'),
        'gender'             => $this->request->getPost('gender'),
        'date_of_birth'      => $this->request->getPost('date_of_birth'),
        'email'              => $this->request->getPost('email'),
        'contact_no'         => $this->request->getPost('contact_no'),
        'address'            => $this->request->getPost('address'),
        'course'             => $this->request->getPost('course'),
        'year_level'         => $this->request->getPost('year_level'),
        'status'             => $this->request->getPost('status'),
        'semesters_acquired' => $semestersAcquired,
        'scholarship_type'   => $scholarshipType,
        'voucher_no'         => $this->request->getPost('voucher_no'),
        'lrn_no'             => $this->request->getPost('lrn_no'),
        'school_elementary'  => $this->request->getPost('school_elementary'),
        'school_junior'      => $this->request->getPost('school_junior'),
        'school_senior_high' => $this->request->getPost('school_senior_high'),
    ];

    if (!$model->update($id, $data)) {
        return redirect()->back()
            ->withInput()
            ->with('errors', $model->errors());
    }

    $school = $this->schoolModel->find($schoolId);
    $schoolName = $school['name'] ?? 'Unknown School';
    $scholarName = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));

    $this->activityNotifier->notifySchoolActivity(
        $authUser,
        'scholar_updated',
        'Scholar record updated',
        "{$authUser['full_name']} updated {$scholarName} from {$schoolName}.",
        site_url('scholars'),
        (int) $schoolId
    );

    return redirect()->to('/scholars')
        ->with('success', 'Scholar updated successfully');
}
       
    /**
     * Upgrade a 4-semester scholar to 8-semester type.
     * Only allowed: 4_semester → 8_semester.
     */
    public function upgrade($id)
    {
        $authUser = session()->get('auth_user');
        $scholar  = $this->scholarModel->find($id);

        if (!$scholar) {
            return redirect()->to(site_url('scholars'))
                ->with('error', 'Scholar not found.');
        }

        // School-scoped roles can only upgrade their own scholars
        if (in_array($authUser['role'], ['school_admin', 'school_staff']) &&
            $scholar['school_id'] != $authUser['school_id']) {
            return redirect()->to(site_url('scholars'))
                ->with('error', 'Unauthorized access.');
        }

        if ($scholar['scholarship_type'] !== '4_semester') {
            return redirect()->back()
                ->with('error', 'Only 4-semester scholars can be upgraded to 8-semester.');
        }

        if ($scholar['status'] !== 'active') {
            return redirect()->back()
                ->with('error', 'Only active scholars can be upgraded.');
        }

        $this->scholarModel->update($id, [
            'scholarship_type' => '8_semester',
            'upgraded_at'      => date('Y-m-d H:i:s'),
            'upgraded_by'      => $authUser['id'],
        ]);

        return redirect()->back()
            ->with('success', 'Scholar successfully upgraded to 8-semester type.');
    }

    public function delete($id)
    {
        $authUser = session()->get('auth_user');
        $scholar = $this->scholarModel->find($id);

        $this->scholarModel->delete($id);

        if ($scholar) {
            $school = $this->schoolModel->find($scholar['school_id']);
            $schoolName = $school['name'] ?? 'Unknown School';
            $scholarName = trim(($scholar['first_name'] ?? '') . ' ' . ($scholar['last_name'] ?? ''));

            $this->activityNotifier->notifySchoolActivity(
                $authUser,
                'scholar_deleted',
                'Scholar record deleted',
                "{$authUser['full_name']} deleted {$scholarName} from {$schoolName}.",
                site_url('scholars'),
                (int) $scholar['school_id']
            );
        }

        return redirect()->to(site_url('scholars'))
            ->with('message', 'Scholar deleted successfully');
    }

    /**
     * Download error report CSV file
     */
    public function downloadErrorReport($filename)
    {
        // Security: Only allow error report files
        if (!preg_match('/^scholar_import_errors_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}\.csv$/', $filename)) {
            throw new PageNotFoundException();
        }

        $filepath = FCPATH . 'uploads/' . $filename;

        // Verify file exists
        if (!file_exists($filepath)) {
            throw new PageNotFoundException();
        }

        // Serve file with download headers
        return $this->response->download($filepath, null);
    }

    /**
     * Download an Excel import template with headers and one sample row.
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Scholars Import');

        $headers = [
            'Semesters Acquired',
            'Scholarship Type',
            'Voucher No.',
            'Last Name',
            'First Name',
            'Middle Name',
            'Extension',
            'Gender',
            'Course',
            'Year Level',
            'Status',
            'Birthdate',
            'Address',
            'Contact No.',
            'Email Address',
            'LRN No.',
            'School (Elementary)',
            'School (Junior)',
            'School (Senior High School)',
        ];

        // Write headers
        foreach ($headers as $col => $header) {
            $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1) . '1';
            $sheet->setCellValue($cell, $header);
        }

        // Style header row
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
        $headerRange = "A1:{$lastCol}1";
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // Sample data row
        $sample = [
            '1',                    // semesters acquired
            '4_semester',           // scholarship type
            'VCH-2024-001',         // voucher no.
            'Dela Cruz',            // last name
            'Juan',                 // first name
            'Santos',               // middle name
            '',                     // extension (Jr., Sr., III, etc.)
            'Male',                 // gender
            'BS Information Technology', // course
            '1',                    // year level (1-4)
            'active',               // status
            '2000-01-15',           // birthdate (YYYY-MM-DD)
            'Brgy. Sample, Cebu City', // address
            '09171234567',          // contact no.
            'juandelacruz@email.com', // email address
            '123456789012',         // lrn no. (12 digits)
            'Sample Elementary School', // school (elementary)
            'Sample Junior High School', // school (junior)
            'Sample Senior High School', // school (senior high school)
        ];

        foreach ($sample as $col => $value) {
            $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1) . '2';
            $sheet->setCellValueExplicit($cell, $value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        }

        // Style sample row
        $sampleRange = "A2:{$lastCol}2";
        $sheet->getStyle($sampleRange)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EEF2FF']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // Auto-size columns
        foreach (range(1, count($headers)) as $col) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }

        // Output as .xlsx download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="scholars_import_template.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    public function importForm()
{
    $authUser = session()->get('auth_user');

    $data = [
        'title' => 'Import Scholars from Excel',
        'user'  => $authUser,
        'show_back' => true,
        'back_url' => site_url('scholars/create')
    ];

    // If staff, load schools for dropdown
    if ($authUser['role'] === 'staff') {
        $data['schools'] = $this->schoolModel->findAll();
    }

    return view('scholars/import', $data);
    }

    /**
     * Import scholars from Excel file with advanced error handling and reporting
     * Optimized for large files (5,000-50,000 rows)
     * Returns to import page with summary and error report link
     */
    public function importExcel()
    {
        $file = $this->request->getFile('excel_file');

        if (!$file->isValid()) {
            return redirect()->back()
                ->with('error', 'Invalid file upload.');
        }

        try {
            // ===== CONFIGURATION =====
            $batchSize = 500;
            $schoolCache = [];
            $existingRecords = [];
            $importSummary = [
                'total_rows' => 0,
                'imported' => 0,
                'updated' => 0,
                'skipped' => 0,
                'errors' => []
            ];

            $requiredColumns = [
                'semesters acquired',
                'scholarship type',
                'voucher no.',
                'last name',
                'first name',
                'middle name',
                'extension',
                'gender',
                'course',
                'year level',
                'status',
                'birthdate',
                'address',
                'contact no.',
                'email address',
                'lrn no.',
                'school (elementary)',
                'school (junior)',
                'school (senior high school)'
            ];

            $columnMap = [
                'semesters acquired' => 'semesters_acquired',
                'scholarship type'   => 'scholarship_type',
                'voucher no.' => 'voucher_no',
                'last name' => 'last_name',
                'first name' => 'first_name',
                'middle name' => 'middle_name',
                'extension' => 'name_extension',
                'gender' => 'gender',
                'course' => 'course',
                'year level' => 'year_level',
                'status' => 'status',
                'birthdate' => 'date_of_birth',
                'address' => 'address',
                'contact no.' => 'contact_no',
                'email address' => 'email',
                'lrn no.' => 'lrn_no',
                'school (elementary)' => 'school_elementary',
                'school (junior)' => 'school_junior',
                'school (senior high school)' => 'school_senior_high'
            ];

            // ===== LOAD SPREADSHEET =====
            $spreadsheet = IOFactory::load($file->getTempName());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            if (count($rows) < 2) {
                return redirect()->back()
                    ->with('error', 'Excel file is empty or contains no data rows.');
            }

            // ===== VALIDATE COLUMN HEADERS =====
            $header = array_map('trim', $rows[0]);
            $headerLower = array_map('trim', array_map('strtolower', $rows[0]));
            $requiredLower = array_map('strtolower', $requiredColumns);

            $missingColumns = array_diff($requiredLower, $headerLower);

            if (!empty($missingColumns)) {  
                return redirect()->back()
                    ->with('error', "Missing columns: " . implode(', ', $missingColumns));
            }

            // DEBUG: Log actual headers
            log_message('debug', 'Excel Headers: ' . json_encode($header));
            log_message('debug', 'Expected Headers: ' . json_encode($requiredColumns));

            if ($headerLower !== $requiredLower) {
                $expectedOrder = implode("\n", array_map(fn($col) => "- $col", $requiredColumns));
                $actualOrder = implode("\n", array_map(fn($col) => "- $col", $header));
                
                log_message('error', 'Column mismatch. Expected: ' . json_encode($requiredLower) . ' Got: ' . json_encode($headerLower));
                
                return redirect()->back()
                    ->with('error', "Excel file has incorrect column format or order.\n\n=== ACTUAL HEADERS ===\n$actualOrder\n\n=== EXPECTED HEADERS ===\n$expectedOrder");
            }

            // ===== CACHE EXISTING RECORDS =====
            $scholarModel = new ScholarModel();
            $existingVouchers = $scholarModel->select('id, voucher_no, lrn_no, email')->findAll();
            foreach ($existingVouchers as $record) {
                if ($record['voucher_no']) {
                    $existingRecords['voucher'][$record['voucher_no']] = $record['id'];
                }
                if ($record['lrn_no']) {
                    $existingRecords['lrn'][$record['lrn_no']] = $record['id'];
                }
                if ($record['email']) {
                    $existingRecords['email'][$record['email']] = $record['id'];
                }
            }

            // ===== LOAD SCHOOLS INTO CACHE =====
            $schoolModel = new SchoolModel();
            $allSchools = $schoolModel->findAll();
            foreach ($allSchools as $school) {
                $schoolCache[strtolower($school['name'])] = $school['id'];
            }

            $db = \Config\Database::connect();
            $rowsToInsert = [];
            $rowsToUpdate = [];
            $authUser = session()->get('auth_user');
            $selectedSchool = $this->request->getPost('school_id');
            // ===== PROCESS ROWS =====
            for ($i = 1; $i < count($rows); $i++) {
                $rowNum = $i + 1;
                $rawRow = $rows[$i];
                $rowData = [];
                $rowErrors = [];

                // Map columns to database fields
                foreach ($header as $key => $excelColumnName) {
                    $excelColumnNameLower = strtolower(trim($excelColumnName));
                    $dbFieldName = null;

                    foreach ($columnMap as $excelCol => $dbCol) {
                        if (strtolower($excelCol) === $excelColumnNameLower) {
                            $dbFieldName = $dbCol;
                            break;
                        }
                    }

                    // Always map the value, even if empty
                    if ($dbFieldName) {
                        $cellValue = $rawRow[$key] ?? '';
                        $rowData[$dbFieldName] = is_string($cellValue) ? trim($cellValue) : $cellValue;
                    }
                }

                // ===== VALIDATE FIRST/LAST NAME =====
                if (empty($rowData['first_name'])) {
                    $rowErrors[] = 'Missing first name';
                }
                if (empty($rowData['last_name'])) {
                    $rowErrors[] = 'Missing last name';
                }

                // ===== VALIDATE GENDER =====
                if (empty($rowData['gender'])) {
                    $rowErrors[] = 'Missing gender';
                } else {
                    $validGenders = ['male', 'female', 'other'];
                    if (!in_array(strtolower($rowData['gender']), $validGenders)) {
                        $rowErrors[] = "Invalid gender: {$rowData['gender']} (must be male, female, or other)";
                    } else {
                        $rowData['gender'] = strtolower($rowData['gender']);
                    }
                }

                // ===== VALIDATE COURSE =====
                if (empty($rowData['course'])) {
                    $rowErrors[] = 'Missing course';
                }

                // ===== VALIDATE YEAR LEVEL =====
                if (empty($rowData['year_level'])) {
                    $rowErrors[] = 'Missing year level';
                } else {
                    $yearLevel = (int)$rowData['year_level'];
                    if ($yearLevel < 1 || $yearLevel > 4) {
                        $rowErrors[] = "Invalid year level: {$rowData['year_level']} (must be 1-4)";
                    } else {
                        $rowData['year_level'] = (string)$yearLevel; // Convert to string for model validation
                    }
                }

                // ===== VALIDATE STATUS =====
                if (empty($rowData['status'])) {
                    $rowErrors[] = 'Missing status';
                } else {
                    $validStatuses = ['active', 'on-hold', 'graduated', 'disqualified'];
                    if (!in_array(strtolower($rowData['status']), $validStatuses)) {
                        $rowErrors[] = "Invalid status: {$rowData['status']} (must be active, on-hold, or graduated)";
                    } else {
                        $rowData['status'] = strtolower($rowData['status']);
                    }
                }

                // ===== VALIDATE LRN NO =====
                if (!empty($rowData['lrn_no'])) {

                
                    $lrnClean = preg_replace('/[^0-9]/', '', $rowData['lrn_no']);
                    if (strlen($lrnClean) !== 12 || !is_numeric($lrnClean)) {
                        $rowErrors[] = "Invalid LRN: must be 12 digits (got: {$rowData['lrn_no']})";
                    } else {
                        $rowData['lrn_no'] = $lrnClean;
                    }
                    
                    if (isset($existingRecords['lrn'][$lrnClean])) {
                        $rowErrors[] = "Duplicate LRN: {$lrnClean} already exists";
                    }
                } else {
                    $rowErrors[] = 'Missing LRN number';
                }

                // ===== VALIDATE VOUCHER NO =====
                if (empty($rowData['voucher_no'])) {
                    $rowErrors[] = 'Missing voucher number';
                } else {
                    if (isset($existingRecords['voucher'][$rowData['voucher_no']])) {
                        $rowErrors[] = "Duplicate voucher: {$rowData['voucher_no']} already exists";
                    }
                }

                // ===== VALIDATE SCHOLARSHIP TYPE =====
                $validTypes = ['4_semester', '8_semester', '10_semester'];
                if (empty($rowData['scholarship_type'])) {
                    $rowData['scholarship_type'] = '4_semester';
                } else {
                    $typeNorm = strtolower(trim($rowData['scholarship_type']));
                    // Accept friendly formats: "4", "4 semester", "4-semester", "4_semester"
                    $typeMap = [
                        '4' => '4_semester', '4 semester' => '4_semester', '4-semester' => '4_semester', '4_semester' => '4_semester',
                        '8' => '8_semester', '8 semester' => '8_semester', '8-semester' => '8_semester', '8_semester' => '8_semester',
                        '10' => '10_semester', '10 semester' => '10_semester', '10-semester' => '10_semester', '10_semester' => '10_semester',
                    ];
                    if (isset($typeMap[$typeNorm])) {
                        $rowData['scholarship_type'] = $typeMap[$typeNorm];
                    } else {
                        $rowErrors[] = "Invalid scholarship type: {$rowData['scholarship_type']} (must be 4_semester, 8_semester, or 10_semester)";
                        $rowData['scholarship_type'] = '4_semester';
                    }
                }

                // ===== VALIDATE SEMESTERS =====
                $semMax = ScholarModel::maxSemesters($rowData['scholarship_type']);
                if (!empty($rowData['semesters_acquired'])) {
                    $semesters = (int)$rowData['semesters_acquired'];
                    if ($semesters < 1 || $semesters > $semMax) {
                        $rowErrors[] = "Invalid semesters: {$rowData['semesters_acquired']} (must be 1-{$semMax} for {$rowData['scholarship_type']})";
                    } else {
                        $rowData['semesters_acquired'] = $semesters;
                    }
                } else {
                    $rowData['semesters_acquired'] = 1;
                }

                // ===== VALIDATE BIRTHDATE =====
                if (!empty($rowData['date_of_birth'])) {
                    try {
                        if (is_numeric($rowData['date_of_birth'])) {
                            $rowData['date_of_birth'] = date(
                                'Y-m-d',
                                Date::excelToTimestamp($rowData['date_of_birth'])
                            );
                        } else {
                            $timestamp = strtotime($rowData['date_of_birth']);
                            if ($timestamp === false) {
                                $rowErrors[] = "Invalid birthdate format: {$rowData['date_of_birth']}";
                            } else {
                                $rowData['date_of_birth'] = date('Y-m-d', $timestamp);
                            }
                        }
                    } catch (\Exception $e) {
                        $rowErrors[] = "Error parsing birthdate: {$rowData['date_of_birth']}";
                    }
                } else {
                    $rowErrors[] = 'Missing birthdate';
                }

                // ===== FORMAT PHONE NUMBER =====
                if (!empty($rowData['contact_no'])) {
                    $rowData['contact_no'] = preg_replace('/[^0-9+]/', '', $rowData['contact_no']);
                } else {
                    $rowErrors[] = 'Missing contact number';
                }

                // ===== VALIDATE ADDRESS =====
                if (empty($rowData['address'])) {
                    $rowErrors[] = 'Missing address';
                }

                // ===== VALIDATE SCHOOLS =====
                if (empty($rowData['school_elementary'])) {
                    $rowErrors[] = 'Missing school (elementary)';
                }
                if (empty($rowData['school_junior'])) {
                    $rowErrors[] = 'Missing school (junior)';
                }
                if (empty($rowData['school_senior_high'])) {
                    $rowErrors[] = 'Missing school (senior high school)';
                }

                // ===== ASSIGN SCHOOL ID BASED ON ROLE =====
                if ($authUser['role'] === 'staff') {

                    if (!$selectedSchool) {
                        $rowErrors[] = 'No school selected for import';
                    } else {
                        $rowData['school_id'] = $selectedSchool;
                    }

                } elseif (in_array($authUser['role'], ['school_admin', 'school_staff'])) {

                    $rowData['school_id'] = $authUser['school_id'];

                } else {

                $rowErrors[] = 'No school assignment possible';

                }

                // ===== IF ERRORS, RECORD AND SKIP ROW =====
                if (!empty($rowErrors)) {
                    $importSummary['errors'][] = [
                        'row' => $rowNum,
                        'data' => $rawRow,
                        'errors' => $rowErrors
                    ];
                    $importSummary['skipped']++;
                    $importSummary['total_rows']++;
                    continue;
                }

                $importSummary['total_rows']++;

                // ===== CHECK FOR DUPLICATES AND UPDATE OR INSERT =====
                $isUpdate = false;
                $updateId = null;

                if (isset($existingRecords['email'][$rowData['email'] ?? ''])) {
                    $updateId = $existingRecords['email'][$rowData['email']];
                    $isUpdate = true;
                }

                if ($isUpdate) {
                    $rowsToUpdate[] = [
                        'id' => $updateId,
                        'data' => $rowData
                    ];
                    $importSummary['updated']++;
                } else {
                    $rowsToInsert[] = $rowData;
                    $importSummary['imported']++;
                }
            }

            // DEBUG: Log summary before DB operations
            log_message('debug', 'Import Summary: ' . json_encode($importSummary));

            // ===== BATCH INSERT RECORDS =====
            $db->transStart();

            if (!empty($rowsToInsert)) {
                log_message('debug', 'Inserting ' . count($rowsToInsert) . ' records');
                log_message('debug', 'Sample row data: ' . json_encode($rowsToInsert[0] ?? []));
                
                // Temporarily disable validation since we already validated above
                $scholarModel->skipValidation(true);
                
                $chunks = array_chunk($rowsToInsert, $batchSize);
                foreach ($chunks as $chunk) {
                    try {
                        $result = $scholarModel->insertBatch($chunk);
                        if (!$result) {
                            $modelErrors = $scholarModel->errors();
                            log_message('error', 'Batch insert failed. Model errors: ' . json_encode($modelErrors));
                            log_message('error', 'DB Error: ' . $db->error()['message'] ?? 'No DB error');
                        } else {
                            log_message('debug', 'Batch inserted ' . count($chunk) . ' records successfully');
                        }
                    } catch (\Exception $e) {
                        log_message('error', 'Exception during batch insert: ' . $e->getMessage());
                    }
                }
            }

            // ===== BATCH UPDATE RECORDS =====
            if (!empty($rowsToUpdate)) {
                log_message('debug', 'Updating ' . count($rowsToUpdate) . ' records');
                foreach ($rowsToUpdate as $updateRecord) {
                    $scholarModel->update($updateRecord['id'], $updateRecord['data']);
                }
            }

            $transResult = $db->transComplete();
            log_message('debug', 'Transaction result: ' . ($transResult ? 'Committed' : 'Failed'));

            if ($transResult && ($importSummary['imported'] > 0 || $importSummary['updated'] > 0)) {
                $schoolIdForNotification = in_array($authUser['role'], ['school_admin', 'school_staff'], true)
                    ? (int) $authUser['school_id']
                    : null;

                $schoolName = 'their school';
                if ($schoolIdForNotification) {
                    $school = $this->schoolModel->find($schoolIdForNotification);
                    $schoolName = $school['name'] ?? $schoolName;
                }

                $summaryText = "Imported {$importSummary['imported']} scholar(s), updated {$importSummary['updated']} scholar(s), and skipped {$importSummary['skipped']} row(s) for {$schoolName}.";

                $this->activityNotifier->notifySchoolActivity(
                    $authUser,
                    'scholar_imported',
                    'Scholar import completed',
                    "{$authUser['full_name']} completed a scholar import. {$summaryText}",
                    site_url('scholars/import'),
                    $schoolIdForNotification
                );
            }

            // ===== GENERATE ERROR REPORT IF THERE ARE ERRORS =====
            $errorReportPath = null;
            if (!empty($importSummary['errors'])) {
                $errorReportPath = $this->generateErrorReport($importSummary['errors']);
            }

            return redirect()->to(site_url('scholars/import'))
                ->with('success', $this->formatImportSummary($importSummary))
                ->with('error_report_path', $errorReportPath);

        } catch (\Exception $e) {
            log_message('error', 'Import Exception: ' . $e->getMessage() . ' - ' . $e->getFile() . ':' . $e->getLine());
            log_message('error', 'Stack Trace: ' . $e->getTraceAsString());
            
            return redirect()->back()
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Generate error report CSV file for failed rows
     * @return string Path to the generated CSV file
     */
    private function generateErrorReport($errors)
    {
        try {
            // Save to public/uploads for web access
            $uploadsDir = FCPATH . 'uploads/';
            
            if (!is_dir($uploadsDir)) {
                mkdir($uploadsDir, 0755, true);
            }

            $filename = 'scholar_import_errors_' . date('Y-m-d_H-i-s') . '.csv';
            $filepath = $uploadsDir . $filename;

            $file = fopen($filepath, 'w');
            
            if (!$file) {
                log_message('error', 'Failed to create error report file: ' . $filepath);
                return null;
            }

            // Write CSV header
            fputcsv($file, ['Row Number', 'Error Messages', 'Original Data']);

            // Write error rows
            foreach ($errors as $error) {
                $errorMessage = is_array($error['errors']) ? implode('; ', $error['errors']) : $error['errors'];
                $originalData = is_array($error['data']) ? implode(' | ', array_filter($error['data'])) : '';
                fputcsv($file, [$error['row'], $errorMessage, $originalData]);
            }

            fclose($file);
            
            log_message('debug', 'Error report generated: ' . $filepath);
            
            // Return download route URL
            return site_url('scholars/download-error-report/' . $filename);
            
        } catch (\Exception $e) {
            log_message('error', 'Error generating report: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Format import summary for display message
     * @return string Formatted summary message
     */
    private function formatImportSummary($summary)
    {
        $message = "✓ Import Complete!\n\n";
        $message .= "Total Rows: {$summary['total_rows']}\n";
        $message .= "Successfully Imported: {$summary['imported']}\n";
        $message .= "Successfully Updated: {$summary['updated']}\n";
        $message .= "Skipped (Errors): {$summary['skipped']}\n";

        if (!empty($summary['errors'])) {
            $message .= "\n⚠ {$summary['skipped']} rows had validation errors.";
            $message .= " Check the error report for details.";
        }

        return $message;
    }
}


