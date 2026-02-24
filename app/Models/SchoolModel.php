<?php

namespace App\Models;

use CodeIgniter\Model;

class SchoolModel extends Model
{
    protected $table = 'schools';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'name',
        'code',
        'address',
        'contact_person',
        'contact_email',
        'contact_number',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Because you have deleted_at
    protected $useSoftDeletes = true;
}