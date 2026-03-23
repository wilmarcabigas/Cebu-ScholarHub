<?php

namespace App\Controllers;

use App\Models\ActivityNotificationModel;
use App\Models\MessageModel;
use Config\Services;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = ['url', 'form', 'auth'];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = service('session');
        $user = auth_user();

        $renderer = Services::renderer();

        $renderer->setVar('navbar_notifications_enabled', false);
        $renderer->setVar('navbar_notification_count', 0);
        $renderer->setVar('navbar_notifications', []);
        $renderer->setVar('navbar_chat_enabled', false);
        $renderer->setVar('navbar_chat_unread_count', 0);

        if ($user && in_array($user['role'] ?? '', ['admin', 'staff', 'school_admin', 'school_staff'], true)) {
            $messageModel = new MessageModel();

            $renderer->setVar('navbar_chat_enabled', true);
            $renderer->setVar('navbar_chat_unread_count', $messageModel->countUnreadForUser((int) $user['id']));
        }

        if ($user && in_array($user['role'] ?? '', ['admin', 'staff'], true)) {
            $notificationModel = new ActivityNotificationModel();

            $renderer->setVar('navbar_notifications_enabled', true);
            $renderer->setVar('navbar_notification_count', $notificationModel->countUnreadForRecipient((int) $user['id']));
            $renderer->setVar('navbar_notifications', $notificationModel->getRecentForRecipient((int) $user['id']));
        }
    }
}
