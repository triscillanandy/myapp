<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// $routes->get('/', 'Home::index');
// $routes->get('/register', 'Users::register');
// $routes->post('/register', 'Users::register');
// $routes->get('login', 'Users::login');
// $routes->post('login', 'Users::login');
// $routes->get('profile/(:num)', 'Users::profile/$1');
// $routes->put('update/(:num)', 'Users::update/$1');
// $routes->match(['get','post'],'index', 'Users::index');
// $routes->match(['get','post'],'forgotpassword', 'Users::forgotpassword');
// $routes->get('dashboard', 'Dashboard::index');
// $routes->get('logout', 'Users::logout');
// $routes->get('users/activate/(:num)/(:any)', 'Users::activate/$1/$2');
// $routes->match(['get','post'],'email', 'Email::index');

// app/Config/Routes.php

// $routes->post('/jwt', 'Cuth::index');
// $routes->get('/jwt', 'Cuth::index');
$routes->get('/register', 'UsersController::register');
$routes->post('/register', 'UsersController::register');
$routes->get('login', 'UsersController::login');
$routes->post('login', 'UsersController::login');
$routes->get('profile/(:num)', 'UsersController::profile/$1');
$routes->put('update/(:num)', 'UsersController::update/$1');
$routes->match(['get','post'],'index', 'UsersController::index');
$routes->match(['get','post'],'forgotpassword', 'UsersController::forgotpassword');
$routes->get('dashboard', 'Dashboard::index');
$routes->get('logout', 'UsersController::logout');
$routes->get('users/activate/(:num)/(:any)', 'UsersController::activate/$1/$2');
$routes->get("users", "UsersController::cuth");