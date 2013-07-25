<?php
/**
 * The endpoints in the webservice .htaccess file that we are cataloguing are all in this format:
 *
 * RewriteRule ^admarker/v1/search   dispatcher.php?_module=AdMarkerV1Search [L,QSA]
 * 
 */

require_once(__DIR__ . '/constants.php');
require_once(__DIR__ . '/WebserviceRoute.php');

class WebserviceRouteConfig
{

  public static $config_file = '';

  public static $routes = array();


  /**
   * The endpoints in the webservice .htaccess file that we are cataloguing are all in this format:
   *
   * RewriteRule ^admarker/v1/search   dispatcher.php?_module=AdMarkerV1Search [L,QSA]
   *
   * @param string $config_file path to .htaccess config file for dispatcher routes
   * @return multitype:
  */
  public static function load($config_file)
  {

    self::$config_file = $config_file;

    $config = @file(self::$config_file);

    if( empty($config) )
    {
      throw new Exception('Invalid config file path specified: ' . $config_file);
    }
    
    self::$routes = array();
    
    foreach($config as $config_rule)
    {
      
      $config_rule = trim($config_rule);
      if( empty($config_rule) )
      {
        continue;
      }
      
      try
      {
        self::$routes[] = new WebserviceRoute($config_rule);
      }
      catch(Exception $e)
      {
        echo("Exception: " . $e->getMessage() . NEWLINE);
      }

    }
    
    return self::$routes;
    
  }
  
  /**
   * @return string
   */
  public static function get_config_file()
  {
    return self::$config_file;
  }
  
  /**
   * @return array
   */
  public static function get_routes()
  {
    return self::$routes;
  }

}

