<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('App');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
//$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
//$routes->get('/', 'Home::index');
$routes->get('/', 'App::index');
$routes->post('/', 'App::index');
//$routes->post('start_game', 'App::start_game');
$routes->post('get_category', 'App::get_category');
$routes->post('get_clue', 'App::get_clue');
$routes->post('initialize_word', 'App::initialize_word');
$routes->post('initialize_clues', 'App::initialize_clues');
//$routes->post('reveal_word', 'App::reveal_word');
$routes->post('reset', 'App::reset');
$routes->post('check_answer', 'App::check_answer');
$routes->get('end_game', 'App::end_game');
//$routes->get('chat', 'App::test');
//$routes->get('test', 'App::test');
//$routes->post('test', 'App::test');


$routes->get('api', 'Api::index');
$routes->post('api', 'Api::index');
$routes->post('api/get_category', 'Api::get_category');
$routes->post('api/get_clue', 'Api::get_clue');
$routes->post('api/initialize_word', 'Api::initialize_word');
$routes->post('api/initialize_clues', 'Api::initialize_clues');
$routes->post('api/reset', 'Api::reset');
$routes->post('api/check_answer', 'Api::check_answer');
$routes->get('api/end_game', 'Api::end_game');

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
