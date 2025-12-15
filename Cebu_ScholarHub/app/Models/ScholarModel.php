<?php

namespace App\Models;

use CodeIgniter\Model;

class ScholarModel extends Model
{
    protected $table = 'scholars';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'school_id',
        'first_name',
        'last_name',
        'middle_name',
        'gender',
        'course',
        'year_level',
        'status',
        'date_of_birth',
        'email'
    ];    
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    
    // Validation rules
    protected $validationRules = [
        'first_name'  => 'required|min_length[3]|max_length[100]',
        'last_name'   => 'required|min_length[3]|max_length[100]',
        'gender'      => 'required|in_list[male,female,other]',
        'course'      => 'required|min_length[3]|max_length[100]',
        'year_level'  => 'required|is_natural_no_zero',
        'status'      => 'required|in_list[active,on-hold,graduated]',
        'date_of_birth' => 'required|valid_date',
        'email' => 'required|valid_email',
    ];

    
    // Custom error messages
    protected $validationMessages = [
        'email' => [
            'is_unique' => 'This email is already registered.',
        ],
    ];
    public function scholarValidationRules($id = null)
    {
        $rules = $this->validationRules;

        if ($id) {
            // UPDATE → ignore current record
            $rules['email'] = "required|valid_email|is_unique[scholars.email,id,{$id}]";
        } else {
            // CREATE → must be fully unique
            $rules['email'] = "required|valid_email|is_unique[scholars.email]";
        }

        return $rules;
    }

    // Optional: Search functionality
    public function search($searchTerm)
    {
        return $this->like('first_name', $searchTerm)
                    ->orLike('last_name', $searchTerm)
                    ->orLike('course', $searchTerm)
                    ->orLike('status', $searchTerm)
                    ->findAll();
    }

    // Optional: Get scholars by school ID
    public function getBySchoolId($schoolId)
    {
        return $this->where('school_id', $schoolId)->findAll();
    }

    // Optional: Get scholars by status
    public function getByStatus($status)
    {
        return $this->where('status', $status)->findAll();
    }
}