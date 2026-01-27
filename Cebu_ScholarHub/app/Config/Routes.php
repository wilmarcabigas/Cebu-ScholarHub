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


    // Admin routes
    $routes->group('admin', ['filter' => 'role:admin'], static function ($routes) {
        $routes->get('users', 'UsersController::index');
        $routes->get('users/create', 'UsersController::create');
        $routes->post('users/create', 'UsersController::create');
        $routes->get('users/edit/(:num)', 'UsersController::edit/$1');
        $routes->post('users/edit/(:num)', 'UsersController::edit/$1');
        $routes->get('users/delete/(:num)', 'UsersController::delete/$1');
        
        $routes->get('reports', 'Admin\ReportsController::index');

        
        
    });

    // Staff routes
    $routes->group('staff', ['filter' => 'role:staff'], static function ($routes) {
        $routes->get('reviews', 'Staff\ReviewsController::index');
        $routes->get('applications', 'Staff\ApplicationsController::index');
        
    });

     // Staff routes
    $routes->group('manage', ['filter' => 'role:staff, admin'], static function ($routes) {
        $routes->get('reviews', 'Staff\ReviewsController::index');
        $routes->get('applications', 'Staff\ApplicationsController::index');

        $routes->get('schools', 'Admin\Schools::index');
        $routes->get('schools/create', 'Admin\Schools::create');
        $routes->post('schools/store', 'Admin\Schools::store');
        $routes->get('schools/edit/(:num)', 'Admin\Schools::edit/$1');
        $routes->post('schools/update/(:num)', 'Admin\Schools::update/$1');
        $routes->get('schools/delete/(:num)', 'Admin\Schools::delete/$1');
        
    });

    // School Admin routes
    $routes->group('school', ['filter' => 'role:school_admin,school_staff'], function($routes) {
    // Scholar routes
    $routes->get('scholars', 'ScholarController::index');
    $routes->get('scholars/create', 'ScholarController::create');
    $routes->post('scholars/store', 'ScholarController::store');
    $routes->get('scholars/edit/(:num)', 'ScholarController::edit/$1');
    $routes->post('scholars/update/(:num)', 'ScholarController::update/$1');
    $routes->get('scholars/delete/(:num)', 'ScholarController::delete/$1');
    
    

    // Other school routes
    $routes->get('billing', 'School\BillingController::index');
    $routes->get('requirements', 'School\RequirementsController::index');
    $routes->get('reports', 'School\ReportsController::index');
});

    // Scholar routes
    $routes->group('scholars', ['filter' => 'role:admin,staff,school_admin,school_staff'], static function($routes) {
    $routes->get('/', 'ScholarController::index');
    $routes->get('create', 'ScholarController::create');
    $routes->post('store', 'ScholarController::store');
    $routes->get('edit/(:num)', 'ScholarController::edit/$1');
    $routes->put('update/(:num)', 'ScholarController::update/$1');
    $routes->get('delete/(:num)', 'ScholarController::delete/$1');
});
});

// Auth routes
$routes->group('', ['filter' => 'guest'], static function ($routes) {
    $routes->get('login', 'AuthController::login');
    $routes->post('login', 'AuthController::attempt');
});

$routes->get('logout', 'AuthController::logout', ['filter' => 'auth']);