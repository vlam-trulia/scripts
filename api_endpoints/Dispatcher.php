<?php

class Dispatcher
{
  
  const PARAM_MODULE =  '_module';
  const CONTROLLER_CLASS_PREFIX = 'ApiController';
  const CONTROLLER_CLASS_SUFFIX = '.php';
  const REWRITE_PATTERN = 'RewriteRule';
  const DISPATCHER_PATTERN = 'dispatcher.php?_module=';
  const COMMENT_PATTERN = '#';
  
  /**
   * @param string $module
   * @param boolean $with_extension
   * @return string
   */
  public static function class_for_module($module, $with_extension = true)
  {
    $class = self::CONTROLLER_CLASS_PREFIX . trim($module) . ( $with_extension ? self::CONTROLLER_CLASS_SUFFIX : '' );
    return $class;
  }
  
  
}