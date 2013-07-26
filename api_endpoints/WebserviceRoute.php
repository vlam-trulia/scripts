<?php
/**
 * Parsing class for a webservice route stored in the .htaccess RewriteRule format.
 * 
 * RewriteRule ^admarker/v1/search   dispatcher.php?_module=AdMarkerV1Search [L,QSA]
 * 
 * @author nroberts
 *
 */
class WebserviceRoute
{
  
  public $rewrite_rule = '';
  
  public $line_number = 0;
  
  public $pattern = '';
  
  public $request_url = '';
  
  public $dispatch_url = '';
  
  public $module = '';
  
  public $controller = '';
  
  public $dispatch_parameters = array();
  
  
  public function __construct($rewrite_rule = '', $line_number = null)
  {
    
    if( !empty($rewrite_rule) )
    {
      $this->parse($rewrite_rule);
    }
    
    if( !empty($line_number) )
    {
      $this->line_number = (int) $line_number;
    }
    
  }
  
  public function get_rewrite_rule()
  {
    return $this->rewrite_rule;
  }
  
  public function get_pattern()
  {
    return $this->pattern;
  }
  
  public function get_request_url()
  {
    return $this->request_url;
  }
  
  public function get_dispatch_url()
  {
    return $this->dispatch_url;
  }
  
  public function get_module()
  {
    return $this->module;
  }
  
  public function get_controller()
  {
    return $this->controller;
  }
  
  public function get_dispath_parameters()
  {
    return $this->dispatch_parameters;
  }
  
  /**
   * 
   * @param string $rewrite_rule
   * @throws Exception
   */
  public function parse($rewrite_rule)
  {   
    
    $pattern = '';
    $request_url = '';
    $dispatch_url = '';
    $dispatch_parameters = array();
    $module = '';
    $controller = '';
    $formatException = new Exception("Format invalid: $rewrite_rule");
    
    /**
     * Skip commented lines and lines
     * that do not define a valid
     * ontroller route.
     */
    if( stristr($rewrite_rule, COMMENT_PATTERN) ||
    !stristr($rewrite_rule, REWRITE_PATTERN) ||
    !stristr($rewrite_rule, DISPATCHER_PATTERN)
    )
    {
      throw $formatException;
    }
    
    $chunks = explode(SPACE, $rewrite_rule);
    if( empty($chunks) )
    {
      throw $formatException;
    }
    
    foreach($chunks as $chunk)
    {
      
      $chunk = trim($chunk);
    
      if( substr($chunk, 0, 1) == CARROT && !stristr($chunk, CONTROLLER_CLASS_SUFFIX) )
      {
        
        $pattern = $chunk;
        $request_url = '/' . trim($chunk, CARROT);
        
      }
    
      if( stristr($chunk, DISPATCHER_PATTERN) )
      {
        
        $dispatch_url = $chunk;
        
        $bits = parse_url($dispatch_url);
        $query_string = $bits['query'];
        
        $dispatch_parameters = $this->parse_parameters($query_string);
    
        if( array_key_exists(PARAM_MODULE, $dispatch_parameters) )
        {
          
          $module = $dispatch_parameters[PARAM_MODULE];
          $controller = self::class_for_module($module);
          
        }
    
      }
    
    }
    
    if( empty($pattern) || empty($request_url) || empty($dispatch_url) || empty($module) || empty($controller) )
    {
      throw $formatException;
    }
    
    $this->rewrite_rule = $rewrite_rule;
    $this->pattern = $pattern;
    $this->request_url = $request_url;
    $this->dispatch_url = $dispatch_url;
    $this->dispatch_parameters = $dispatch_parameters;
    $this->module = $module;
    $this->controller = $controller;
    
  }
  
  /**
   * @param string $query_string
   * @return array
   */
  public function parse_parameters($query_string)
  {
    
    $query_string = trim($query_string);
    
    if( !is_string($query_string) || empty($query_string) ) 
    {
      return;
    }
    
    $parameters = array();
    parse_str($query_string, $parameters);
    
    return $parameters;
    
  }
  
  /**
   * @return array
   */
  public function to_array()
  {
    return get_object_vars($this);
  }  
  
  /**
   * @param string $module
   * @param boolean $with_extension
   * @return string
   */
  public static function class_for_module($module, $with_extension = true)
  {
    $class = CONTROLLER_CLASS_PREFIX . trim($module) . ( $with_extension ? CONTROLLER_CLASS_SUFFIX : '' );
    return $class;
  }
  
  
}