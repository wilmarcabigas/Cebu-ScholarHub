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
        'email',
        'semesters_acquired',
        'scholarship_type',
        'upgraded_at',
        'upgraded_by',
        'voucher_no',
        'name_extension',
        'address',
        'contact_no',
        'lrn_no',
        'school_elementary',
        'school_junior',
        'school_senior_high'
    ];    
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    
    // Validation rules
    protected $validationRules = [

'school_id' => 'required|is_natural_no_zero',

'first_name' => 'required|min_length[2]|max_length[100]',
'last_name' => 'required|min_length[2]|max_length[100]',
'middle_name' => 'permit_empty|max_length[100]',

'gender' => 'required|in_list[male,female,other]',
'course' => 'required|min_length[2]|max_length[100]',

'year_level' => 'required|in_list[1,2,3,4]',

'status' => 'required|in_list[active,on-hold,graduated,disqualified]',

'date_of_birth' => 'required|valid_date',

'email' => 'required|valid_email',

'semesters_acquired' => 'required|is_natural_no_zero|less_than_equal_to[10]',
'scholarship_type'   => 'required|in_list[4_semester,8_semester,10_semester]',

'voucher_no' => 'required|min_length[3]|max_length[50]',

'name_extension' => 'permit_empty|in_list[,Jr.,Sr.,II,III,IV,V,VI,VII,VIII]',

'address' => 'required|min_length[10]|max_length[500]',
'contact_no' => 'required|min_length[10]|max_length[20]',

'lrn_no' => 'required|exact_length[12]|numeric',

'school_elementary' => 'required|min_length[3]|max_length[255]',
'school_junior' => 'required|min_length[3]|max_length[255]',
'school_senior_high' => 'required|min_length[3]|max_length[255]',

];

    

    
    // Custom error messages
    protected $validationMessages = [
        'email' => [
            'is_unique' => 'This email is already registered.',
        ],
        'voucher_no' => [
            'is_unique' => 'This voucher number is already in use.',
        ],
        'lrn_no' => [
            'is_unique' => 'This LRN number is already assigned.',
            'exact_length' => 'LRN must be exactly 12 digits.',
            'numeric' => 'LRN must contain only numbers.',
        ],
        'contact_no' => [
            'min_length' => 'Contact number must be at least 10 digits.',
            'max_length' => 'Contact number must not exceed 20 characters.',
        ],
        'name_extension' => [
            'in_list' => 'Please select a valid name extension.',
        ],
    ];
    public function scholarValidationRules($id = null)
{
    $rules = $this->validationRules;

    if ($id) {
        $rules['email'] = "required|valid_email|is_unique[scholars.email,id,{$id}]";
        $rules['voucher_no'] = "required|min_length[3]|max_length[50]|is_unique[scholars.voucher_no,id,{$id}]";
        $rules['lrn_no'] = "required|exact_length[12]|numeric|is_unique[scholars.lrn_no,id,{$id}]";
    } else {
        $rules['email'] = "required|valid_email|is_unique[scholars.email]";
        $rules['voucher_no'] = "required|min_length[3]|max_length[50]|is_unique[scholars.voucher_no]";
        $rules['lrn_no'] = "required|exact_length[12]|numeric|is_unique[scholars.lrn_no]";
    }

    return $rules;
}

    /**
     * Returns the max semesters allowed for a given scholarship_type.
     */
    public static function maxSemesters(string $type): int
    {
        return match($type) {
            '8_semester'  => 8,
            '10_semester' => 10,
            default       => 4,
        };
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