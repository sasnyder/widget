<?php

/**
 * Entry point into the application
 * Includes the necessary classes
 * Parses the request and call the controller action
 * Display 404 for unknown routes
 */

error_reporting(E_ALL);

// Cookie for logged in users
define('USER_COOKIE', 'widgetuser');

// Include all PHP class files in 'inc' directory
foreach (glob('./inc/*.php') as $filename) {
	include_once $filename;
}

// Parse the request and set defaults if needed
function parseRequest($request) {
	$controller = (empty($request['controller'])) ? 'Order' : ucfirst($request['controller']);
	$action = (empty($request['action'])) ? 'index' : $request['action'];
	$param1 = (empty($request['param1'])) ? null : $request['param1'];
	return array($controller, $action, $param1);
}

list($controller, $action, $param1) = parseRequest($_REQUEST);

// Instantiate the controller class and call action method with parameter, otherwise 404
if (class_exists($controller)) {
	$controller = new $controller;
	if (method_exists($controller, $action)) {
		$controller->$action($param1);
	} else {
		include './tpl/404.html';
	}
} else {
	include './tpl/404.html';
}