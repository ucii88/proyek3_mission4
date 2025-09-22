<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'AuthController::loginForm');
$routes->get('/login', 'AuthController::loginForm');

$routes->group('auth', function ($routes) {
    $routes->post('login', 'AuthController::login');
    $routes->post('logout', 'AuthController::logout', ['filter' => 'auth']); // Ubah dari GET ke POST dan tambahkan filter auth
});

// Dashboard 
$routes->get('/dashboard', 'DashboardController::index', ['filter' => 'auth']);

$routes->group('courses', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'CourseController::index');
    $routes->get('getAll', 'CourseController::getAll');
    $routes->post('create', 'CourseController::create');
    $routes->post('update', 'CourseController::update');
    $routes->delete('delete/(:num)', 'CourseController::delete/$1');
    $routes->post('enroll/(:num)', 'CourseController::enroll/$1');
    $routes->post('enrollMultiple', 'CourseController::enrollMultiple');
    $routes->post('unenrollMultiple', 'CourseController::unenrollMultiple'); // Tambah unenroll multiple
    $routes->delete('unenroll/(:num)', 'CourseController::unenroll/$1');
    $routes->get('detail/(:num)', 'CourseController::detail/$1');
    $routes->get('search', 'CourseController::search');
});

$routes->group('students', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'StudentController::index');
    $routes->get('getAll', 'StudentController::getAll');
    $routes->post('create', 'StudentController::create');
    $routes->post('update', 'StudentController::update');
    $routes->delete('delete/(:num)', 'StudentController::delete/$1');
    $routes->get('view/(:num)', 'StudentController::view/$1');
    $routes->post('bulk-delete', 'StudentController::bulkDelete');
});

$routes->group('api', ['filter' => 'auth'], function ($routes) {
    $routes->group('courses', function ($routes) {
        $routes->get('search', 'Api\\CourseController::search');
        $routes->get('(:num)/students', 'Api\\CourseController::getStudents/$1');
    });
    
    $routes->group('students', function ($routes) {
        $routes->get('search', 'Api\\StudentController::search');
        $routes->get('(:num)/courses', 'Api\\StudentController::getCourses/$1');
    });
    
    $routes->group('dashboard', function ($routes) {
        $routes->get('stats', 'Api\\DashboardController::getStats');
    });
});

$routes->group('reports', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'ReportController::index');
    $routes->get('enrollment', 'ReportController::enrollment');
    $routes->get('courses', 'ReportController::courses');
    $routes->get('students', 'ReportController::students');
});

if (ENVIRONMENT === 'development') {
    $routes->get('test/db', 'TestController::database');
    $routes->get('test/session', 'TestController::session');
    $routes->get('phpinfo', function() {
        phpinfo();
    });
}

// 404 Override
$routes->set404Override(function() {
    return view('errors/404');
});