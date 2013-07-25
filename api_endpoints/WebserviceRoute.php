<?php

require_once(__DIR__ . '/constants.php');
require_once(__DIR__ . '/Dispatcher.php');

class WebserviceRoute
{
  
  public $rewrite_rule = '';
  
  public $pattern = '';
  
  public $request_url = '';
  
  public $dispatch_url = '';
  
  public $module = '';
  
  public $controller = '';
  
  public $dispatch_parameters = array();
  
  
  public function __construct($rewrite_rule = '')
  {
    
    if( !empty($rewrite_rule) )
    {
      $this->parse($rewrite_rule);
    }
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
    if( stristr($rewrite_rule, Dispatcher::COMMENT_PATTERN) ||
    !stristr($rewrite_rule, Dispatcher::REWRITE_PATTERN) ||
    !stristr($rewrite_rule, Dispatcher::DISPATCHER_PATTERN)
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
    
      if( substr($chunk, 0, 1) == CARROT && !stristr($chunk, Dispatcher::CONTROLLER_CLASS_SUFFIX) )
      {
        
        $pattern = $chunk;
        $request_url = '/' . trim($chunk, CARROT);
        
      }
    
      if( stristr($chunk, Dispatcher::DISPATCHER_PATTERN) )
      {
        
        $dispatch_url = $chunk;
        
        $bits = parse_url($dispatch_url);
        $query_string = $bits['query'];
        
        $dispatch_parameters = $this->parse_parameters($query_string);
    
        if( array_key_exists(Dispatcher::PARAM_MODULE, $dispatch_parameters) )
        {
          
          $module = $dispatch_parameters[Dispatcher::PARAM_MODULE];
          $controller = Dispatcher::class_for_module($module);
          
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
  
  
}