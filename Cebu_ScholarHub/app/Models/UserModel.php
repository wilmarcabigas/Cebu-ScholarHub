<?php
namespace App\Models;
use CodeIgniter\Model;

class UserModel extends Model
{
     protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'email', 'password_hash', 'full_name', 'role', 'school_id',
        'status', 'last_login_at', 'created_at', 'updated_at', 'deleted_at'
    ];
    protected $useTimestamps    = true;
    protected $returnType       = 'array';
    protected $validationRules  = [
        'email'     => 'required|valid_email|max_length[191]',
        'full_name' => 'required|min_length[2]|max_length[191]',
        'role'      => 'required|in_list[admin,staff,school_admin,school_staff,scholar]',
        'status'    => 'permit_empty|in_list[active,disabled]',
    ];

    /**
     * Utility: fetch user by email (active only).
     */
    public function findActiveByEmail(string $email): ?array
    {
        return $this->where('email', $email)
                    ->where('status', 'active')
                    ->first();
    }
}