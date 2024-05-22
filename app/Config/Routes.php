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
$routes->get('dashboard', 'Dashboard::index');
$routes->get('logout', 'Users::logout');
