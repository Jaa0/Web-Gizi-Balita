<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'APIcontroller::create');
$routes->post('submit', 'APIcontroller::submit');
$routes->get('Result', 'APIcontroller::result');
