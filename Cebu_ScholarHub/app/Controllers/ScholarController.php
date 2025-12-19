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

        $scholars = $this->scholarModel
            ->select('scholars.*, schools.name as school_name')
            ->join('schools', 'schools.id = scholars.school_id', 'left');

        if (in_array($authUser['role'], ['school_admin', 'school_staff'])) {
            $scholars->where('scholars.school_id', $authUser['school_id']);
        }

        $data = [
            'title'    => 'Manage Scholars',
            'scholars' => $scholars->findAll(),
            'user'     => $authUser
        ];

        return view('scholars/index', $data);
    }

    public function create()
    {
        $authUser = session()->get('auth_user');

        return view('scholars/create', [
            'title'   => 'Add New Scholar',
            'schools' => $this->schoolModel->findAll(),
            'user'    => $authUser
        ]);
    }

    public function store()
    {
        $authUser = session()->get('auth_user');

        $schoolId = in_array($authUser['role'], ['school_admin', 'school_staff'])
            ? $authUser['school_id']
            : $this->request->getPost('school_id');

        $data = [
            'school_id'    => $schoolId,
            'first_name'   => $this->request->getPost('first_name'),
            'middle_name'  => $this->request->getPost('middle_name'),
            'last_name'    => $this->request->getPost('last_name'),
            'gender'       => $this->request->getPost('gender'),
            'course'       => $this->request->getPost('course'),
            'year_level'   => $this->request->getPost('year_level'),
            'status'       => $this->request->getPost('status'),
            'date_of_birth'=> $this->request->getPost('date_of_birth'),
            'email'        => $this->request->getPost('email'),
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
            'title'   => 'Edit Scholar',
            'scholar' => $scholar,
            'schools' => $this->schoolModel->findAll(),
            'user'    => $authUser
        ]);
    }

   public function update($id)
{
    $model = new ScholarModel();

    if (! $this->validate([
        'first_name' => 'required',
        'last_name'  => 'required',
        'email'      => 'required|valid_email',
    ])) {
        return redirect()->back()
            ->withInput()
            ->with('errors', $this->validator->getErrors());
    }

    $model->update($id, $this->request->getPost());

    return redirect()->to('/scholars')
        ->with('success', 'Scholar updated successfully');
}

    

    public function delete($id)
    {
        $this->scholarModel->delete($id);

        return redirect()->to(site_url('scholars'))
            ->with('message', 'Scholar deleted successfully');
    }
}