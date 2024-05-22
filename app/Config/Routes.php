<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/register', 'Users::register');
$routes->post('/register', 'Users::register');
$routes->get('login', 'Users::login');
$routes->post('login', 'Users::login');
$routes->get('profile/(:num)', 'Users::profile/$1');
$routes->post('update/(:num)', 'Users::update/$1');
// $routes->match(['get','post'],'profile', 'Users::profile');
$routes->get('dashboard', 'Dashboard::index');
$routes->get('logout', 'Users::logout');
