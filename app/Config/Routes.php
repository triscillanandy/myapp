<?php


use App\Controllers\Tools;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// // Default route
$routes->get('/', 'Users::index');

// // Route to display the login form
$routes->get('/login', 'Users::index');

// // Route to handle traditional login form submission
$routes->post('/users/login', 'Users::login');

$routes->get('/register', 'Users::register');
// $routes->get('/register', [Users::class, 'register']);
$routes->post('/register', 'Users::register');
// $routes->get('login', 'Users::login');
// $routes->post('login', 'Users::login');
$routes->get('loginWithGoogle', 'Users::loginWithGoogle');
$routes->get('dashboard', 'Users::dashboard');
$routes->get('profile', 'Users::profile');
$routes->post('profile', 'Users::profile');

// // $routes->match(['get','post'],'index', 'Users::index');
// // $routes->match(['get','post'],'forgotpassword', 'Users::forgotpassword');
$routes->get('dashboard', 'Dashboard::dashboard', ['filter' => 'authFilter']);
$routes->get('logout', 'Users::logout');
$routes->get('users/activate/(:num)/(:any)', 'Users::activate/$1/$2');
$routes->get('contacts/create', 'ContactController::create');

 $routes->post('contacts/create', 'Contactlist::create');
 $routes->get('contacts/edit/(:num)', 'Contactlist::edit/$1');

 
 $routes->post('contacts/update/(:num)', 'Contactlist::update/$1');
 $routes->get('contacts/delete/(:num)', 'Contactlist::delete/$1');
 $routes->delete('contacts/delete/(:num)', 'Contactlist::delete/$1');





$routes->group('email', function ($routes) {
    // Route to display the email form
    $routes->get('form', 'EmailController::showEmailForm');

    $routes->post('upload', 'EmailController::uploadFiles');
    // Route to send email
    $routes->post('send', 'EmailController::sendEmailFromPost');
});

$routes->get('verifyotp', 'Users::verifyOtpPage');
$routes->post('verifyotp', 'Users::verifyOtp');


$routes->get('emails/sent/', 'EmailController::listSentEmails/');

$routes->group('passwordreset', function ($routes) {
    $routes->get('forgot', 'PasswordResetController::forgotPassword');
    $routes->post('request', 'PasswordResetController::requestReset');
    $routes->get('reset/(:
    any)', 'PasswordResetController::resetPassword/$1');
    $routes->post('update', 'PasswordResetController::updatePassword');
});


// app/Config/Routes.php

// $routes->cli('tools/message/(:segment)', 'Tools::message/$1');

// $routes->get('/email/form', 'Email::showEmailForm');
// $routes->post('/email/enqueueEmails', 'Email::enqueueEmails');



// $routes->match(['get','post'],'email', 'Email::index');

// $routes->get('/', 'GoogleCon::index');
// $routes->get('/login', 'GoogleCon::login');
// $routes->get('/profile', 'GoogleCon::profile');
// $routes->get('/loginWithGoogle', 'GoogleCon::loginWithGoogle');
// $routes->get("/logout", "GoogleCon::logout");
// app/Config/Routes.php

// $routes->post('/jwt', 'Cuth::index');
// // $routes->get('/jwt', 'Cuth::index');






// $routes->get('dashboard', 'Dashboard::index',['filter' => 'authFilter']);
//  $routes->get('contacts', 'Contactlist::index');
//  $routes->get('contacts/(:num)', 'Contactlist::show/$1');


//  $routes->get('email/form', 'EmailController::showEmailForm');

//  $routes->post('mail', 'EmailController::sendEmail');

//  $routes->get('mail', 'EmailController::sendEmail');
//  $routes->get('email/send', 'EmailController::sendEmailFromPost');
//  $routes->post('email/send', 'EmailController::sendEmailFromPost');


// File: app/Config/Routes.php
// In app/Config/Routes.php

// $routes->get('login', 'Users::index');
// $routes->post('login', 'Users::login');




// app/Config/Routes.php


//$routes->get('register', 'UsersController::register');

// $routes->group('', ['filter' => 'cors'], function ($routes) {

//     $routes->match(['post', 'get', 'options'],'/register', 'UsersController::register');

   
// // });
//$routes->match(['post', 'get', 'options'],'/register', 'UsersController::register');
// $routes->match(['post', 'get'],'/register', 'UsersController::register');
// // $routes->match(['post', 'get'],'/login', 'UsersController::login');
// $routes->get('login', 'UsersController::login');
// $routes->post('login', 'UsersController::login');
// $routes->post('loginWithGoogle', 'UsersController::loginWithGoogle');
// $routes->get('loginWithGoogle', 'UsersController::loginWithGoogle');



// $routes->get('profile/(:num)', 'UsersController::profile/$1',['filter' => 'authFilter']);
// $routes->put('update/(:num)', 'UsersController::update/$1');

// $routes->match(['get','post'],'index', 'UsersController::index');
// // $routes->match(['get','post'],'forgotpassword', 'UsersController::forgotpassword');
// $routes->match(['get','post'],'forgotpassword/(:num)', 'UsersController::forgotpassword/$1');

// $routes->get('logout', 'UsersController::logout');
// $routes->get('users/activate/(:num)/(:any)', 'UsersController::activate/$1/$2');

// $routes->get("users", "UsersController::cuth", ['filter' => 'authFilter']);

