<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\ActivityLogger;
use App\Models\MessageModel;
use App\Models\UserModel;

class Messages extends BaseController
{
    protected MessageModel $messageModel;
    protected UserModel $userModel;
    protected ActivityLogger $activityLogger;

    public function __construct()
    {
        $this->messageModel = new MessageModel();
        $this->userModel    = new UserModel();
        $this->activityLogger = new ActivityLogger();
    }

    private function getAuthUser(): ?array
    {
        return session()->get('auth_user');
    }

    private function getAvailableContacts(array $authUser): array
    {
        $myId = (int) $authUser['id'];
        $myRole = $authUser['role'] ?? '';

        if (in_array($myRole, ['admin', 'staff'], true)) {
            $users = $this->userModel
                ->select('users.*, schools.name as school_name')
                ->join('schools', 'schools.id = users.school_id', 'left')
                ->where('users.id !=', $myId)
                ->whereIn('role', ['school_admin', 'school_staff'])
                ->where('users.deleted_at', null)
                ->findAll();
        } elseif (in_array($myRole, ['school_admin', 'school_staff'], true)) {
            $users = $this->userModel
                ->select('users.*, schools.name as school_name')
                ->join('schools', 'schools.id = users.school_id', 'left')
                ->where('users.id !=', $myId)
                ->whereIn('role', ['admin', 'staff'])
                ->where('users.deleted_at', null)
                ->findAll();
        } else {
            $users = [];
        }

        foreach ($users as &$user) {
            $user['display_name'] = $user['school_name'] ?? $user['full_name'] ?? 'Unnamed User';
            $latestMessage = $this->messageModel->getLatestMessageBetween($myId, (int) $user['id']);
            $user['last_message'] = $latestMessage['message_body'] ?? 'No messages yet.';
            $user['last_message_at'] = $latestMessage['sent_at'] ?? null;
            $user['unread_count'] = $this->messageModel->getUnreadCountFromSender($myId, (int) $user['id']);
        }
        unset($user);

        usort($users, static function (array $a, array $b): int {
            $timeA = $a['last_message_at'] ?? '';
            $timeB = $b['last_message_at'] ?? '';

            if ($timeA === $timeB) {
                return strcmp($a['full_name'] ?? '', $b['full_name'] ?? '');
            }

            return strcmp($timeB, $timeA);
        });

        return $users;
    }

    private function canChatWith(array $authUser, array $otherUser): bool
    {
        $myRole = $authUser['role'] ?? '';
        $otherRole = $otherUser['role'] ?? '';

        if (in_array($myRole, ['admin', 'staff'], true)) {
            return in_array($otherRole, ['school_admin', 'school_staff'], true);
        }

        if (in_array($myRole, ['school_admin', 'school_staff'], true)) {
            return in_array($otherRole, ['admin', 'staff'], true);
        }

        return false;
    }

    public function index()
    {
        $authUser = $this->getAuthUser();

        if (! $authUser) {
            return redirect()->to('login');
        }

        $users = $this->getAvailableContacts($authUser);

        if (! empty($users)) {
            return redirect()->to(site_url('messages/chat/' . $users[0]['id']));
        }

        return view('messages/select_user', [
            'title' => 'Messages',
            'user'  => $authUser,
            'users' => $users,
            'show_back' => true,
            'back_url'  => site_url('dashboard'),
        ]);
    }

    public function chat($userId)
    {
        $authUser = $this->getAuthUser();

        if (! $authUser) {
            return redirect()->to('login');
        }

        $myId = (int) $authUser['id'];
            $otherId = (int) $userId;
        $otherUser = $this->userModel->getUserById($otherId);

        if (! $otherUser || ! $this->canChatWith($authUser, $otherUser)) {
            return redirect()->to(site_url('messages'))
                ->with('error', 'You cannot open this conversation.');
        }

        $otherUser['display_name'] = $otherUser['school_name'] ?? $otherUser['full_name'] ?? 'User';

        $this->messageModel->markConversationAsRead($myId, $otherId);

        return view('messages/chat', [
            'title'     => 'Chat',
            'messages'  => $this->messageModel->getChat($myId, $otherId),
            'otherUser' => $otherUser,
            'myId'      => $myId,
            'other_id'  => $otherId,
            'users'     => $this->getAvailableContacts($authUser),
            'show_back' => true,
            'back_url'  => site_url('dashboard'),
        ]);
    }

    public function send()
    {
        $authUser = $this->getAuthUser();

        if (! $authUser) {
            return redirect()->to('login');
        }

        $myId = (int) $authUser['id'];
        $receiverId = (int) $this->request->getPost('receiver_id');
        $message = trim((string) $this->request->getPost('message'));
        $otherUser = $this->userModel->getUserById($receiverId);

        if ($message === '' || ! $otherUser || ! $this->canChatWith($authUser, $otherUser)) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(422)->setJSON([
                    'status' => 'error',
                    'message' => 'Unable to send message.',
                ]);
            }

            return redirect()->back()->with('error', 'Unable to send message.');
        }

        $messageId = $this->messageModel->insert([
            'sender_id'    => $myId,
            'receiver_id'  => $receiverId,
            'message_body' => $message,
            'is_read'      => 0,
            'sent_at'      => date('Y-m-d H:i:s'),
        ]);

        $this->activityLogger->logSchoolAccountAction(
            $authUser,
            'message_sent',
            'Message sent to Cebu admin',
            "{$authUser['full_name']} sent a message to {$otherUser['full_name']}.",
            [
                'action' => 'create',
                'school_id' => $authUser['school_id'] ?? null,
                'subject_type' => 'message',
                'subject_id' => (int) $messageId,
                'new_values' => [
                    'receiver_id' => $receiverId,
                    'message_preview' => mb_substr($message, 0, 120),
                ],
                'metadata' => [
                    'receiver_role' => $otherUser['role'] ?? null,
                ],
            ]
        );

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => $this->messageModel->find($messageId),
            ]);
        }

        return redirect()->to(site_url('messages/chat/' . $receiverId));
    }

    public function fetch($userId)
    {
        $authUser = $this->getAuthUser();

        if (! $authUser) {
            return $this->response->setStatusCode(401)->setJSON(['status' => 'error']);
        }

        $myId = (int) $authUser['id'];
        $otherId = (int) $userId;
        $otherUser = $this->userModel->getUserById($otherId);

        if (! $otherUser || ! $this->canChatWith($authUser, $otherUser)) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error']);
        }

        $this->messageModel->markConversationAsRead($myId, $otherId);

        return $this->response->setJSON([
            'status' => 'success',
            'messages' => $this->messageModel->getChat($myId, $otherId),
            'unread_total' => $this->messageModel->countUnreadForUser($myId),
        ]);
    }

    public function unreadSummary()
    {
        $authUser = $this->getAuthUser();

        if (! $authUser) {
            return $this->response->setStatusCode(401)->setJSON(['status' => 'error']);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'unread_total' => $this->messageModel->countUnreadForUser((int) $authUser['id']),
        ]);
    }
}
