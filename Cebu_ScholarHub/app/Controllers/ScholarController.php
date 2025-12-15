<?php


namespace App\Controllers;

use App\Models\ScholarModel;
use App\Models\SchoolModel;

class ScholarController extends BaseController
{
    protected $scholarModel;
    protected $schoolModel;

    public function __construct()
    {
        $this->scholarModel = new ScholarModel();
        $this->schoolModel = new SchoolModel();
    }

    public function index()
    {
        $authUser = session()->get('auth_user');
        
        // Get scholars based on school_id for school users
        $scholars = $this->scholarModel
            ->select('scholars.*, schools.name as school_name')
            ->join('schools', 'schools.id = scholars.school_id', 'left');
            
        if (in_array($authUser['role'], ['school_admin', 'school_staff'])) {
            $scholars->where('scholars.school_id', $authUser['school_id']);
        }

        $data = [
    'title' => 'Manage Scholars',
    'scholars' => $scholars->findAll(),
    'user' => $authUser    // ← FIXED
];
    $schoolId = in_array($authUser['role'], ['school_admin', 'school_staff'])
    ? $authUser['school_id']
    : $this->request->getPost('school_id');
        return view('scholars/index', $data);
    }

    public function create()
    {
        $authUser = session()->get('auth_user');

        $data = [
            'title'   => 'Add New Scholar',
            'schools' => $this->schoolModel->findAll(),
            'user'    => $authUser   // ← ADD THIS  
        ];
        
    return view('scholars/create', $data);
    }

    public function store()
    {
        $authUser = session()->get('auth_user');

        // Set school_id for school users
        $schoolId = in_array($authUser['role'], ['school_admin', 'school_staff']) 
            ? $authUser['school_id'] 
            : $this->request->getPost('school_id');

        $data = [
            'school_id' => $schoolId,
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'middle_name' => $this->request->getPost('middle_name'),
            'gender' => $this->request->getPost('gender'),
            'course' => $this->request->getPost('course'),
            'year_level' => $this->request->getPost('year_level'),
            'status' => $this->request->getPost('status'),
            'date_of_birth' => $this->request->getPost('date_of_birth'),
            'email' => $this->request->getPost('email')
        ];

        try {
            if ($this->scholarModel->insert($data)) {
                return redirect()->to('scholars')
                    ->with('message', 'Scholar added successfully');
            }
            
            return redirect()->back()
                ->with('errors', $this->scholarModel->errors())
                ->withInput();

        } catch (\Exception $e) {
            log_message('error', '[Scholar Create] ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error creating scholar')
                ->withInput();
        }
    }

    public function edit($id = null)
{
    $authUser = session()->get('auth_user');

    $scholar = $this->scholarModel->find($id);
    if (!$scholar) {
        return redirect()->to('scholars')
            ->with('error', 'Scholar not found');
    }

    // 🔐 Restrict school users
    if (in_array($authUser['role'], ['school_admin', 'school_staff'])) {
        if ($scholar['school_id'] != $authUser['school_id']) {
            return redirect()->to('scholars')
                ->with('error', 'Unauthorized access');
        }
    }

    $data = [
        'title'   => 'Edit Scholar',
        'scholar' => $scholar,
        'schools' => $this->schoolModel->findAll(),
        'user'    => $authUser
    ];

    return view('scholars/edit', $data);
}

    public function update($id = null)
{
    $authUser = session()->get('auth_user');

    $scholar = $this->scholarModel->find($id);
    if (!$scholar) {
        return redirect()->to('scholars')
            ->with('error', 'Scholar not found');
    }

    // 🔐 Restrict school users to their own scholars only
    if (in_array($authUser['role'], ['school_admin', 'school_staff'])) {
        if ($scholar['school_id'] != $authUser['school_id']) {
            return redirect()->to('scholars')
                ->with('error', 'Unauthorized access');
        }
    }

    // Base data (allowed for all roles)
    $data = [
        'first_name'    => $this->request->getPost('first_name'),
        'last_name'     => $this->request->getPost('last_name'),
        'middle_name'   => $this->request->getPost('middle_name'),
        'gender'        => $this->request->getPost('gender'),
        'course'        => $this->request->getPost('course'),
        'year_level'    => $this->request->getPost('year_level'),
        'status'        => $this->request->getPost('status'),
        'date_of_birth' => $this->request->getPost('date_of_birth'),
        'email'         => $this->request->getPost('email')
    ];

    // ✅ Only Scholars Office Admin can change school
    if ($authUser['role'] === 'admin') {
        $data['school_id'] = $this->request->getPost('school_id');
    }

    try {
    $this->scholarModel->setValidationRules([
        'email' => "required|valid_email|is_unique[scholars.email,id,{$id}]"
    ]);

    if ($this->scholarModel->update($id, $data)) {
        return redirect()->to('scholars')
            ->with('message', 'Scholar updated successfully');
    }

    return redirect()->back()
        ->with('errors', $this->scholarModel->errors())
        ->withInput();

} catch (\Exception $e) {
    log_message('error', '[Scholar Update] ' . $e->getMessage());
    return redirect()->back()
        ->with('error', 'Error updating scholar')
        ->withInput();
}
}

    public function delete($id = null)
    {
        try {
            if ($this->scholarModel->delete($id)) {
                return redirect()->to('scholars')
                    ->with('message', 'Scholar deleted successfully');
            }

            return redirect()->back()
                ->with('error', 'Error deleting scholar');

        } catch (\Exception $e) {
            log_message('error', '[Scholar Delete] ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error deleting scholar');
        }
    }
}