<?php

namespace App\Controllers;

use App\Models\ScholarModel;
use App\Models\SchoolModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ScholarController extends BaseController
{
    protected $scholarModel;
    protected $schoolModel;

    public function __construct()
    {
        $this->scholarModel = new ScholarModel();
        $this->schoolModel  = new SchoolModel();
    }

    public function index()
    {
        $authUser = session()->get('auth_user');

        $scholars = $this->scholarModel
            ->select('scholars.*, schools.name as school_name')
            ->join('schools', 'schools.id = scholars.school_id', 'left');

        if (in_array($authUser['role'], ['school_admin', 'school_staff'])) {
            $scholars->where('scholars.school_id', $authUser['school_id']);
        }

        $data = [
            'title'      => 'Manage Scholars',
            'scholars'   => $scholars->findAll(),
            'user'       => $authUser,
            'show_back'  => true,
            'back_url'   => site_url('dashboard')
        ];

        return view('scholars/index', $data);
    }

    public function create()
    {
        $authUser = session()->get('auth_user');

        return view('scholars/create', [
            'title'      => 'Add New Scholar',
            'schools'    => $this->schoolModel->findAll(),
            'user'       => $authUser,
            'show_back'  => true,
            'back_url'   => site_url('scholars')
        ]);
    }

    public function store()
    {
        $authUser = session()->get('auth_user');

        $schoolId = in_array($authUser['role'], ['school_admin', 'school_staff'])
            ? $authUser['school_id']
            : $this->request->getPost('school_id');

        $data = [
            'school_id'     => $schoolId,
            'first_name'    => $this->request->getPost('first_name'),
            'middle_name'   => $this->request->getPost('middle_name'),
            'last_name'     => $this->request->getPost('last_name'),
            'gender'        => $this->request->getPost('gender'),
            'course'        => $this->request->getPost('course'),
            'year_level'    => $this->request->getPost('year_level'),
            'status'        => $this->request->getPost('status'),
            'date_of_birth' => $this->request->getPost('date_of_birth'),
            'email'         => $this->request->getPost('email'),
        ];

        if (!$this->scholarModel->insert($data)) {
            return redirect()->back()
                ->with('errors', $this->scholarModel->errors())
                ->withInput();
        }

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
        if (! $this->validate([
            'first_name' => 'required',
            'last_name'  => 'required',
            'email'      => 'required|valid_email',
        ])) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->scholarModel->update($id, $this->request->getPost());

        return redirect()->to('/scholars')
            ->with('success', 'Scholar updated successfully');
    }

    public function delete($id)
    {
        $this->scholarModel->delete($id);

        return redirect()->to(site_url('scholars'))
            ->with('message', 'Scholar deleted successfully');
    }

    public function importForm()
    {
        return view('scholars/import', [
            'title' => 'Import Scholars from Excel',
            'user'  => session()->get('auth_user'),
            'show_back' => true,
            'back_url' => site_url('scholars/create')
        ]);
    }

    public function importExcel()
    {
        $file = $this->request->getFile('excel_file');

        if (!$file->isValid()) {
            return "Invalid file.";
        }

        $spreadsheet = IOFactory::load($file->getTempName());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        if (count($rows) < 2) {
            return "Excel file is empty.";
        }

        $header = array_map('trim', $rows[0]);

        $model = new ScholarModel();
        $schoolModel = new SchoolModel();

        $db = \Config\Database::connect();
        $db->transStart();

        for ($i = 1; $i < count($rows); $i++) {

            $rowData = [];
            $row = $rows[$i];

            foreach ($header as $key => $column) {
                if (empty($column)) continue;
                $rowData[$column] = trim($row[$key]);
            }

            // ===== SCHOOL NAME TO ID CONVERSION =====
            if (!empty($rowData['school_name'])) {

                $school = $schoolModel
                    ->where('name', $rowData['school_name'])
                    ->first();

                if (!$school) {
                    $newId = $schoolModel->insert([
                        'name' => $rowData['school_name']
                    ]);
                    $rowData['school_id'] = $newId;
                } else {
                    $rowData['school_id'] = $school['id'];
                }

                $rowData['school_id'] = $school['id'];
                unset($rowData['school_name']);
            } else {
                continue;
            }

            // ===== DATE FORMAT FIX =====
            if (!empty($rowData['date_of_birth'])) {

                if (is_numeric($rowData['date_of_birth'])) {
                    $rowData['date_of_birth'] = date(
                        'Y-m-d',
                        Date::excelToTimestamp($rowData['date_of_birth'])
                    );
                } else {
                    $rowData['date_of_birth'] = date(
                        'Y-m-d',
                        strtotime($rowData['date_of_birth'])
                    );
                }
            }

            // ===== REQUIRED FIELDS CHECK =====
            if (
                empty($rowData['school_id']) ||
                empty($rowData['first_name']) ||
                empty($rowData['last_name']) ||
                empty($rowData['gender']) ||
                empty($rowData['course']) ||
                empty($rowData['year_level']) ||
                empty($rowData['status']) ||
                empty($rowData['date_of_birth'])
            ) {
                continue;
            }

            // ===== DUPLICATE EMAIL CHECK =====
            if (!empty($rowData['email'])) {

                $existing = $model->where('email', $rowData['email'])->first();

                if ($existing) {
                    $model->update($existing['id'], $rowData);
                    continue;
                }
            }

            $model->insert($rowData);
        }

        $db->transComplete();

        
        return redirect()->to(site_url('scholars/import'))
            ->with('message', 'Scholars imported successfully!');
    }
}
