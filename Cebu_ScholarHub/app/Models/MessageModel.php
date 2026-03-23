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
        'is_read',
        'created_at',
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';

    public function getChat(int $me, int $other): array
    {
        return $this->groupStart()
                ->groupStart()
                    ->where('sender_id', $me)
                    ->where('receiver_id', $other)
                ->groupEnd()
                ->orGroupStart()
                    ->where('sender_id', $other)
                    ->where('receiver_id', $me)
                ->groupEnd()
            ->groupEnd()
            ->orderBy('created_at', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    public function getLatestMessageBetween(int $me, int $other): ?array
    {
        return $this->groupStart()
                ->groupStart()
                    ->where('sender_id', $me)
                    ->where('receiver_id', $other)
                ->groupEnd()
                ->orGroupStart()
                    ->where('sender_id', $other)
                    ->where('receiver_id', $me)
                ->groupEnd()
            ->groupEnd()
            ->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->first();
    }

    public function getUnreadCountFromSender(int $receiverId, int $senderId): int
    {
        return $this->where('receiver_id', $receiverId)
            ->where('sender_id', $senderId)
            ->where('is_read', 0)
            ->countAllResults();
    }

    public function countUnreadForUser(int $userId): int
    {
        return $this->where('receiver_id', $userId)
            ->where('is_read', 0)
            ->countAllResults();
    }

    public function markConversationAsRead(int $receiverId, int $senderId): bool
    {
        return (bool) $this->where('receiver_id', $receiverId)
            ->where('sender_id', $senderId)
            ->where('is_read', 0)
            ->set(['is_read' => 1])
            ->update();
    }
}
