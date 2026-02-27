<?php

namespace App\Models;

use CodeIgniter\Model;

class MessageModel extends Model
{
    protected $table = 'messages';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'sender_id',
        'receiver_id',
        'message',
        'created_at'
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';

    public function getChat($me, $other)
    {
        return $this->where(
                "(sender_id = $me AND receiver_id = $other)
                 OR (sender_id = $other AND receiver_id = $me)"
            )
            ->orderBy('created_at', 'ASC')
            ->findAll();
    }
}
