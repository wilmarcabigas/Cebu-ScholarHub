<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MessageModel;
use App\Models\UserModel;

class Messages extends BaseController
{
    protected $messageModel;
    protected $userModel;

    public function __construct()
    {
        $this->messageModel = new MessageModel();
        $this->userModel    = new UserModel();
    }

    private function getAuthUser()
    {
        return session()->get('auth_user');
    }

    public function index()
    {
        $authUser = $this->getAuthUser();

        if (!$authUser) {
            return redirect()->to('login');
        }

        $myId   = $authUser['id'];
        $myRole = $authUser['role'];

        // ✅ If Cebu Admin or Cebu Staff
        if (in_array($myRole, ['admin', 'staff'])) {

            $data['users'] = $this->userModel
                ->where('id !=', $myId)
                ->whereIn('role', ['school_admin', 'school_staff'])
                ->where('deleted_at', null) // ignore soft deleted
                ->findAll();
        }

        // ✅ If School Admin or School Staff
        elseif (in_array($myRole, ['school_admin', 'school_staff'])) {

            $data['users'] = $this->userModel
                ->where('id !=', $myId)
                ->whereIn('role', ['admin', 'staff'])
                ->where('deleted_at', null)
                ->findAll();
        }

        // ❌ Scholars cannot message
        else {
            $data['users'] = [];
        }

        return view('messages/select_user', $data);
    }

    public function chat($userId)
    {
        $authUser = $this->getAuthUser();

        if (!$authUser) {
            return redirect()->to('login');
        }

        $myId = $authUser['id'];

        $messages = $this->messageModel->getChat($myId, $userId) ?? [];

        return view('messages/chat', [
            'messages'  => $messages,
            'otherUser' => $this->userModel
                                ->where('deleted_at', null)
                                ->find($userId),
            'myId'      => $myId,
            'other_id'  => $userId,
        ]);
    }

    public function send()
    {
        $authUser = $this->getAuthUser();

        if (!$authUser) {
            return redirect()->to('login');
        }

        $myId = $authUser['id'];

        $this->messageModel->insert([
            'sender_id'   => $myId,
            'receiver_id' => $this->request->getPost('receiver_id'),
            'message'     => $this->request->getPost('message'),
        ]);

        return redirect()->to(
            site_url('messages/chat/' . $this->request->getPost('receiver_id'))
        );
    }
}
