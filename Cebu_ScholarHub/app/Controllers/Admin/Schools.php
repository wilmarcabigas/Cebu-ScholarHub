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
        $data['schools'] = $this->schoolModel->findAll();
        return view('admin/schools/index', $data);
    }

    public function create()
    {
        return view('admin/schools/create');
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
        $data['school'] = $this->schoolModel->find($id);
        return view('admin/schools/edit', $data);
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
