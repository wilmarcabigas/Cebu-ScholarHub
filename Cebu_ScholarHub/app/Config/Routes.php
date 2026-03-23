<?php

namespace Config;

use App\Controllers\AuthController;
use App\Controllers\Dashboard;
use App\Controllers\Scholars;
use App\Controllers\School;
use App\Controllers\Messages;

$routes->get('/', fn() => redirect()->to('dashboard'));

// Create route groups for each role
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    // Dashboard - will handle role-based redirection
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('dashboard/live-stats', 'Dashboard::liveStats', ['filter' => 'auth']);
    $routes->get('dashboard/liveStats', 'Dashboard::liveStats');
    $routes->get('/dashboard/course-chart-data', 'Dashboard::getCourseChartData');
    // Admin routes
    $routes->group('admin', ['filter' => 'role:admin,staff'], static function ($routes) {
        $routes->get('users', 'UsersController::index');
        $routes->get('users/create', 'UsersController::create');
        $routes->post('users/create', 'UsersController::create');
        $routes->get('users/edit/(:num)', 'UsersController::edit/$1');
        $routes->post('users/edit/(:num)', 'UsersController::edit/$1');
        $routes->get('users/delete/(:num)', 'UsersController::delete/$1');
        
        // Admin billing routes
        $routes->get('billing', 'Admin\BillingController::index');
        $routes->get('billing/view/(:num)', 'Admin\BillingController::view/$1');
        $routes->post('billing/receive/(:num)', 'Admin\BillingController::receive/$1');
        $routes->post('billing/record-payment/(:num)', 'Admin\BillingController::recordPayment/$1');
        $routes->post('billing/reject/(:num)', 'Admin\BillingController::reject/$1');
        $routes->get('billing/print/(:num)', 'Admin\BillingController::print/$1');
        
        // Admin reports routes
        $routes->get('reports', 'Admin\ReportsController::index');
        $routes->get('reports/payment-status', 'Admin\ReportsController::paymentStatus');
        $routes->get('reports/financial-report', 'Admin\ReportsController::financialReport');
        $routes->get('reports/billing-sheets', 'Admin\ReportsController::billingSheets');
        $routes->get('reports/scholar-payment-history', 'Admin\ReportsController::scholarPaymentHistory');
        $routes->get('reports/export-financial', 'Admin\ReportsController::exportFinancialReport');
        $routes->get('reports/export-scholar', 'Admin\ReportsController::exportScholarReport');

        $routes->get('schools', 'Admin\Schools::index');
        $routes->get('schools/create', 'Admin\Schools::create');
        $routes->post('schools/store', 'Admin\Schools::store');
        $routes->get('schools/edit/(:num)', 'Admin\Schools::edit/$1');
        $routes->post('schools/update/(:num)', 'Admin\Schools::update/$1');
        $routes->get('schools/delete/(:num)', 'Admin\Schools::delete/$1');
    });

    // Staff routes
    $routes->group('staff', ['filter' => 'role:staff'], static function ($routes) {
        $routes->get('reviews', 'Staff\ReviewsController::index');
        $routes->get('applications', 'Staff\ApplicationsController::index');
    });

    // School Admin routes
    $routes->group('school', ['filter' => 'role:school_admin,school_staff,staff'], function($routes) {
        // Scholar routes
        $routes->get('scholars', 'ScholarController::index');
        $routes->get('scholars/create', 'ScholarController::create');
        $routes->post('scholars/store', 'ScholarController::store');
        $routes->get('scholars/edit/(:num)', 'ScholarController::edit/$1');
        $routes->post('scholars/update/(:num)', 'ScholarController::update/$1');
        $routes->get('scholars/delete/(:num)', 'ScholarController::delete/$1');
        $routes->post('scholars/upgrade/(:num)', 'ScholarController::upgrade/$1');
        
        // School billing routes
        $routes->get('billing', 'School\BillingController::index');
        $routes->get('billing/create', 'School\BillingController::create');
        $routes->post('billing/store', 'School\BillingController::store');
        $routes->get('billing/view/(:num)', 'School\BillingController::view/$1');
        $routes->get('billing/edit/(:num)', 'School\BillingController::edit/$1');
        $routes->post('billing/update/(:num)', 'School\BillingController::update/$1');
        $routes->post('billing/submit/(:num)', 'School\BillingController::submit/$1');
        $routes->post('billing/confirm-receipt/(:num)', 'School\BillingController::confirmReceipt/$1');
        $routes->get('billing/delete/(:num)', 'School\BillingController::delete/$1');
        $routes->get('billing/print/(:num)', 'School\BillingController::print/$1');
        
        // School reports routes
        $routes->get('reports', 'School\ReportsController::index');
        $routes->get('reports/payment-history', 'School\ReportsController::paymentHistory');
        $routes->get('reports/billing-sheet/(:num)', 'School\ReportsController::billingSheet/$1');
        $routes->get('reports/status-summary', 'School\ReportsController::statusSummary');
        $routes->get('reports/export-payment-history', 'School\ReportsController::exportPaymentHistory');
        
        // Other school routes
        $routes->get('requirements',  'School\RequirementsController::index');
        
        // Payment recording
        $routes->post('payments/store', 'PaymentController::store');
    });

    // Scholar routes
    $routes->group('scholars', ['filter' => 'role:admin,staff,school_admin,school_staff'], static function($routes) {
    $routes->get('/', 'ScholarController::index');
    $routes->get('create', 'ScholarController::create');
    $routes->post('store', 'ScholarController::store');
    $routes->get('edit/(:num)', 'ScholarController::edit/$1');
    $routes->post('update/(:num)', 'ScholarController::update/$1');
    $routes->get('delete/(:num)', 'ScholarController::delete/$1');
    $routes->post('upgrade/(:num)', 'ScholarController::upgrade/$1');
    $routes->get('download-error-report/(:any)', 'ScholarController::downloadErrorReport/$1');

    $routes->get('import', 'ScholarController::importForm');
    $routes->post('import', 'ScholarController::importExcel');
});


       $routes->group('messages', ['filter' => 'role:admin,staff,school_admin,school_staff'], static function($routes) {
    $routes->get('', 'Messages::index');
    $routes->get('chat/(:num)', 'Messages::chat/$1');
    $routes->get('fetch/(:num)', 'Messages::fetch/$1');
    $routes->get('unread-summary', 'Messages::unreadSummary');
    $routes->post('send', 'Messages::send');
});

       $routes->group('notifications', ['filter' => 'role:admin,staff'], static function($routes) {
    $routes->post('mark-all-read', 'NotificationsController::markAllRead');
});
});




// Auth routes

$routes->group('', ['filter' => 'guest'], static function ($routes) {

    // 🔹 Login page
    $routes->get('login', 'AuthController::login');

    // 🔹 Attempt login (process form)
    $routes->post('login', 'AuthController::attempt');

    // 🔹 Unlock account via Gmail code
    $routes->post('unlock', 'AuthController::unlock');
    $routes->post('/login/verify-code', 'AuthController::verifyCode');
    $routes->post('/login/resend-code', 'AuthController::resendCode');
    $routes->post('reset-password', 'AuthController::processResetPassword');
   
    
});


$routes->get('logout', 'AuthController::logout', ['filter' => 'auth']);


