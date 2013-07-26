<?php
/**
 * Crawls the current webservice project for all valid mobile API endpoints
 * using .htaccess as the definitive list, since this is our equivalent of
 * a front dispatcher and routing table.
 * 
 */

require_once(__DIR__ . '/constants.php');
require_once(__DIR__ . '/Dispatcher.php');
require_once(__DIR__ . '/WebserviceRouteConfig.php');

$config_file = WEBSERVICE_PATH . '/.htaccess';
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

if( empty($routes) )
{
  throw new Exception('No routes parsed.' . NEWLINE);
}


echo("Number of controllers found in config: " . count($routes) . NEWLINE);

$controllers = array();
$controllers['in_config'] = array();
$controllers['in_config_and_missing'] = array();
$controllers['in_file_system'] = array();
$controllers['in_file_system_and_not_in_config'] = array();

$controllers['safe_to_remove'] = array();
$controllers['extends_obsolete'] = array();
$controllers['extends_token'] = array();
$controllers['requires_v1token'] = array();
$controllers['requires_v2token'] = array();
$controllers['requires_v2_longlivetoken'] = array();


echo("Scanning routes:" . NEWLINE);
foreach($routes as $route)
{
  
  $request_url = $route->get_request_url();
  $module = $route->get_module();
  $controller = $route->get_controller();
  $controller_path = WEBSERVICE_CONTROLLERS_PATH . '/' . $controller;
  
  $controllers['in_config'][] = $controller;
  
  if( !file_exists($controller_path) )
  {
    $controllers['in_config_and_missing'][] = $controller;
    continue;
  }
  
  // checking controller properties
  
}


echo("Scannning controllers directory: " . WEBSERVICE_CONTROLLERS_PATH . NEWLINE);

$files = scandir(WEBSERVICE_CONTROLLERS_PATH);

$blacklist = array('.', '..');
foreach($files as $controller)
{
  
  if( in_array($controller, $blacklist) )
  {
    continue;
  }
  
  $controllers['in_file_system'][] = $controller;
  
  if( !stristr($controller, CONTROLLER_CLASS_SUFFIX) )
  {
    $controllers['safe_to_remove'] = $controller;
    continue;
  }
  
  
  if( !in_array($controller, $controllers['in_config']) )
  {
   $controllers['in_file_system_and_not_in_config'][] = $controller;  
  }
 
}

recursive_sort($controllers);
print_r($controllers);
die;



function recursive_sort($array)
{
  if( empty($array) )
  {
    return;
  }
  
  sort($array);
  foreach($array as $val)
  {
    if( is_array($val) )
    {
      recursive_sort($val);
    }
  }
  
}

function path_for_controller($controller)
{
 return  WEBSERVICE_CONTROLLERS_PATH . '/' . $controller;
}