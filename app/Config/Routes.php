<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group('api', function($routes) {
    // Auth
    $routes->post('auth/login', 'Auth::login');

    // Matrimony
    $routes->group('matrimony', ['filter' => 'auth'], function($routes) {
        $routes->get('profiles', 'Profiles::index');
        $routes->get('profiles/(:num)', 'Profiles::show/$1');
        $routes->post('profiles', 'Profiles::create');
        $routes->put('profiles/(:num)', 'Profiles::update/$1');
        $routes->delete('profiles/(:num)', 'Profiles::delete/$1');

        $routes->post('send-interest', 'Interests::send');
        $routes->post('respond-interest', 'Interests::respond');
    });

    // Chat
    $routes->group('chat', ['filter' => 'auth'], function($routes) {
        $routes->post('send', 'Chat::send');
        $routes->get('messages', 'Chat::messages');
    });

    // Premium
    $routes->group('premium', ['filter' => 'auth'], function($routes) {
        $routes->post('subscribe', 'Premium::subscribe');
    });
});