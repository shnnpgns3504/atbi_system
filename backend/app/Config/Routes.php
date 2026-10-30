<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->options('api/(:any)', function() {
    return service('response')->setStatusCode(200);
});

// Auth
$routes->post('api/auth/register', 'Auth::register');
$routes->post('api/auth/login', 'Auth::login');

// ATBIs
$routes->get('api/atbis', 'Atbis::index');
$routes->get('api/atbis/(:segment)', 'Atbis::show/$1');

// Products
$routes->get('api/products', 'Products::index');
$routes->post('api/products/create', 'Products::create');
$routes->post('api/products/update/(:num)', 'Products::update/$1');
$routes->post('api/products/delete/(:num)', 'Products::delete/$1');

// Cart
$routes->get('api/cart', 'Cart::index');
$routes->post('api/cart/add', 'Cart::add');
$routes->post('api/cart/update/(:num)', 'Cart::updateItem/$1');
$routes->post('api/cart/remove/(:num)', 'Cart::remove/$1');
$routes->post('api/cart/clear', 'Cart::clear');

// Calendar
$routes->get('api/calendar', 'Calendar::index');
$routes->post('api/calendar/create', 'Calendar::create');
$routes->post('api/calendar/update/(:num)', 'Calendar::update/$1');
$routes->post('api/calendar/delete/(:num)', 'Calendar::delete/$1');

// Super Admin User Management
$routes->get('api/users', 'Users::index');
$routes->post('api/users/assign-role', 'Users::assignAdmin');
$routes->post('api/users/create', 'Users::createUser');

// Forgot Password
$routes->post('api/forgot/request-otp', 'ForgotPassword::requestOtp');
$routes->post('api/forgot/verify-otp', 'ForgotPassword::verifyOtp');
$routes->post('api/forgot/reset-password', 'ForgotPassword::resetPassword');