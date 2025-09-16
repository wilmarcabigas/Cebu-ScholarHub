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
    $routes->get('debug/check-role', 'UsersController::checkRole');
});

// Example of role-gated areas
$routes->group('admin', ['filter' => 'role:admin'], static function ($routes) {
    $routes->get('users', 'UsersController::index');
    $routes->get('users/create', 'UsersController::create');
    $routes->post('users/create', 'UsersController::create');
    $routes->get('users/edit/(:num)', 'UsersController::edit/$1');
    $routes->post('users/edit/(:num)', 'UsersController::edit/$1');
    $routes->get('users/delete/(:num)', 'UsersController::delete/$1');
});

$routes->group('school', ['filter' => 'role:school_admin,school_staff'], static function ($routes) {
    $routes->get('scholars', 'School\\ScholarsController::index');
});

$routes->group('scholar', ['filter' => 'role:scholar'], static function ($routes) {
    $routes->get('profile', 'Scholar\\ProfileController::index');
});
