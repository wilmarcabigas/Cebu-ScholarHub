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

    private function getMyId()
{
    $authUser = session()->get('auth_user');
    return $authUser['id'] ?? null;
}

    public function index()
    {
        $myId = $this->getMyId();
    
        $data['users'] = $this->userModel
            ->where('id !=', $myId)
            ->findAll();

        return view('messages/select_user', $data);
    }

   public function chat($userId)
{
    $myId = $this->getMyId();

    if (!$myId) {
        return redirect()->to('login');
    }

    $messages = $this->messageModel->getChat($myId, $userId) ?? [];

    return view('messages/chat', [
        'messages'  => $messages,
        'otherUser' => $this->userModel->find($userId),
        'myId'      => $myId,
        'other_id'  => $userId,
    ]);
}


    public function send()
    {
        $myId = $this->getMyId();

        if (!$myId) {
            return redirect()->to('login');
        }

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
