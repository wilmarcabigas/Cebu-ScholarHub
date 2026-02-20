<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SchoolModel;

class Schools extends BaseController
{
    protected $schoolModel;

    public function __construct()
    {
        $this->schoolModel = new SchoolModel();
    }

    public function index()
    {

        $data = [
        'title' => 'Manage schools',
        'schools' => $this->schoolModel->findAll(),
        'show_back' => true,
        'back_url'  => site_url('dashboard'),
    ];
        return view('admin/schools/index', $data);
    }

    public function create()
    {
        return view('admin/schools/create', [
        'show_back' => true,
        'back_url'  => site_url('admin/schools'), // URL to go back to list page
        'title'     => 'Add New School'          // Optional: for page title
    ]);
    }

    public function store()
    {
        $this->schoolModel->save([
            'name'            => $this->request->getPost('name'),
            'code'            => $this->request->getPost('code'),
            'address'         => $this->request->getPost('address'),
            'contact_person'  => $this->request->getPost('contact_person'),
            'contact_email'   => $this->request->getPost('contact_email'),
            'contact_number'  => $this->request->getPost('contact_number'),
        ]);

        return redirect()->to('/admin/schools')->with('success', 'School added successfully');
    }

    public function edit($id)
{
    $school = $this->schoolModel->find($id);

    if (!$school) {
        return redirect()->to(site_url('admin/schools'))
            ->with('error', 'School not found');
    }

    return view('admin/schools/edit', [
        'title'     => 'Edit School',
        'school'    => $school,
        'show_back' => true,
        'back_url'  => site_url('admin/schools'), // Back to list
    ]);
}

    public function update($id)
    {
        $this->schoolModel->update($id, [
            'name'            => $this->request->getPost('name'),
            'code'            => $this->request->getPost('code'),
            'address'         => $this->request->getPost('address'),
            'contact_person'  => $this->request->getPost('contact_person'),
            'contact_email'   => $this->request->getPost('contact_email'),
            'contact_number'  => $this->request->getPost('contact_number'),
        ]);

        return redirect()->to('/admin/schools')->with('success', 'School updated successfully');
    }

    public function delete($id)
    {
        // Soft delete
        $this->schoolModel->delete($id);

        return redirect()->to('/admin/schools')->with('success', 'School deleted successfully');
    }
}
