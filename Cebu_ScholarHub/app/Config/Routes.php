<?php

namespace Config;

use App\Controllers\AuthController;
use App\Controllers\Dashboard;
use App\Controllers\Scholars;
use App\Controllers\School;
use App\Controllers\Messages;

$routes->get('/', fn() => redirect()->to('dashboard'));

// Auth
$routes->group('', ['filter' => 'guest'], static function ($routes) {
    $routes->get('login',  'AuthController::login');
    $routes->post('login', 'AuthController::attempt');
});
$routes->get('logout', 'AuthController::logout', ['filter' => 'auth']);

// Protected area (any logged-in user)
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');
});

// Example of role-gated areas
$routes->group('admin', ['filter' => 'role:admin,staff'], static function ($routes) {
    $routes->get('users', 'Admin\\UsersController::index'); // you’ll create these later
});

$routes->group('school', ['filter' => 'role:school_admin,school_staff'], static function ($routes) {
    $routes->get('scholars', 'School\\ScholarsController::index');
});

$routes->group('scholar', ['filter' => 'role:scholar'], static function ($routes) {
    $routes->get('profile', 'Scholar\\ProfileController::index');
});
