<?php

namespace App\Models;

use CodeIgniter\Model;

class BillModel extends Model
{
    protected $table = 'bills';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'scholar_id',
        'billing_period',
        'amount_due',
        'due_date',
        'status',
        'remarks',
        'posted_by'
    ];

    protected $useTimestamps = true;
}
