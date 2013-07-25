<?php
/**
 * Crawls the current webservice project for all valid mobile API endpoints
 * using .htaccess as the definitive list, since this is our equivalent of
 * a front dispatcher and routing table.
 * 
 */

require_once(__DIR__ . '/constants.php');
require_once(__DIR__ . '/WebserviceRouteConfig.php');


$config_file = WEBSERVICE_INCLUDE_PATH . '/.htaccess';
echo("Parsing routes for config file: $config_file =>" . NEWLINE);

try{
  WebserviceRouteConfig::load($config_file);
  $routes = WebserviceRouteConfig::get_routes();
}
catch(Exception $e)
{
  echo($e->getMessage() . NEWLINE);
  $routes = array();
}

echo("Number of controllers being used: " . count($routes) . NEWLINE);

