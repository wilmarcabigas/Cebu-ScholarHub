<?php
namespace App\Models;
use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
    'email', 
    'password_hash', 
    'full_name', 
    'role', 
    'school_id',
    'status',
    'last_login_at',
    'created_at',
    'updated_at',
    'deleted_at'
];

protected $useTimestamps = true;
protected $createdField = 'created_at';
protected $updatedField = 'updated_at';
protected $deletedField = 'deleted_at';
    
    protected $validationRules = [
        'email' => 'required|valid_email|is_unique[users.email,id,{id}]',
        'full_name' => 'required|min_length[2]',
        'role' => 'required|in_list[admin,staff,school_admin,school_staff,scholar]',
        'status' => 'permit_empty|in_list[active,inactive]'
    ];

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (!isset($data['data']['password'])) {
            return $data;
        }

        $data['data']['password_hash'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        unset($data['data']['password']);

        return $data;
    }

    public function findActiveByEmail(string $email): ?array
    {
        return $this->where('email', $email)
                   ->where('status', 'active')
                   ->first();
    }

    public function getUsersWithSchool()
    {
        return $this->select('users.*, schools.name as school_name')
                   ->join('schools', 'schools.id = users.school_id', 'left')
                   ->findAll();
    }
}