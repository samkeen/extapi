<?php
require 'app/bootstrap.php';
$logger = new Logger(Logger::DEBUG,dirname(__FILE__).'/extapi.log');

$custom_routes = isset($custom_routes)?$custom_routes:null;
$router = new Util_Router($custom_routes);
$controller = Controller_Factory::get_instance($router);
$controller->process();