<?php

//use App\Controllers\Users;
use App\Controllers\UsersControllers;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Default route
// $routes->get('/', 'Users::index');

// // Route to display the login form
// $routes->get('/login', 'Users::index');

// // // Route to handle traditional login form submission
// $routes->post('/users/login', 'Users::login');

// // $routes->get('/register', 'Users::register');
// // // $routes->get('/register', [Users::class, 'register']);
// // $routes->post('/register', 'Users::register');
// // // $routes->get('login', 'Users::login');
// // // $routes->post('login', 'Users::login');
// // $routes->get('loginWithGoogle', 'Users::loginWithGoogle');
// // $routes->get('dashboard', 'Users::dashboard');
// // $routes->get('profile', 'Users::profile');
// // $routes->post('profile', 'Users::profile');

// // // $routes->match(['get','post'],'index', 'Users::index');
// // // $routes->match(['get','post'],'forgotpassword', 'Users::forgotpassword');
// $routes->get('dashboard', 'Dashboard::dashboard', ['filter' => 'authFilter']);
// // $routes->get('logout', 'Users::logout');
// // $routes->get('users/activate/(:num)/(:any)', 'Users::activate/$1/$2');
// // $routes->match(['get','post'],'email', 'Email::index');

// $routes->get('/', 'GoogleCon::index');
// $routes->get('/login', 'GoogleCon::login');
// $routes->get('/profile', 'GoogleCon::profile');
// $routes->get('/loginWithGoogle', 'GoogleCon::loginWithGoogle');
// $routes->get("/logout", "GoogleCon::logout");
// app/Config/Routes.php

// $routes->post('/jwt', 'Cuth::index');
// // $routes->get('/jwt', 'Cuth::index');











//$routes->get('register', 'UsersController::register');

// $routes->group('', ['filter' => 'cors'], function ($routes) {

//     $routes->match(['post', 'get', 'options'],'/register', 'UsersController::register');

   
// // });
//$routes->match(['post', 'get', 'options'],'/register', 'UsersController::register');
$routes->match(['post', 'get'],'/register', 'UsersController::register');
// $routes->match(['post', 'get'],'/login', 'UsersController::login');
$routes->get('login', 'UsersController::login');
$routes->post('login', 'UsersController::login');
$routes->post('loginWithGoogle', 'UsersController::loginWithGoogle');
$routes->get('loginWithGoogle', 'UsersController::loginWithGoogle');



$routes->get('profile/(:num)', 'UsersController::profile/$1',['filter' => 'authFilter']);
$routes->put('update/(:num)', 'UsersController::update/$1');

$routes->match(['get','post'],'index', 'UsersController::index');
// $routes->match(['get','post'],'forgotpassword', 'UsersController::forgotpassword');
$routes->match(['get','post'],'forgotpassword/(:num)', 'UsersController::forgotpassword/$1');

$routes->get('logout', 'UsersController::logout');
$routes->get('users/activate/(:num)/(:any)', 'UsersController::activate/$1/$2');

$routes->get("users", "UsersController::cuth", ['filter' => 'authFilter']);


// $routes->get('dashboard', 'Dashboard::index',['filter' => 'authFilter']);
 $routes->get('contacts', 'Contactlist::index',['filter' => 'authFilter']);
 $routes->get('contacts/(:num)', 'Contactlist::show/$1');
 $routes->post('contacts/create', 'Contactlist::create');
 $routes->put('contacts/update/(:num)', 'Contactlist::update/$1');
 $routes->delete('contacts/delete/(:num)', 'Contactlist::delete/$1');

 $routes->post('send', 'EmailController::sendEmailFromPost');