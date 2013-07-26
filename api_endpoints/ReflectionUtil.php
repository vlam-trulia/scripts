<?php

class ReflectionUtil
{
  
  /**
   * Returns the ReflectionClass instance of the class instance.
   * @param object $class
   * @return ReflectionClass
   */
  public static function reflect($class)
  {
    
    if( !is_object($class) )
    {
      throw new Exception('Invalid class instance.');
    }
    
    return new ReflectionClass($class);
  
  }
  
  /**
   * Returns a list of all interfaces of the class instance.
   * @param object $class
   * @return array
   */
  public static function interfaces_of($class)
  {
    return self::reflect($class)->getInterfaceNames();
  }
  
  /**
   * Returns a list of all parent classes of the class instance.
   * @param object $class
   * @return multitype:NULL
   */
  public static function parents_of($class)
  {
    
    $ref = self::reflect($class);
    
    $parents = array(); 
    while ($parent = $ref->getParentClass()) 
    {
      $parents[] = $parent->getName();
      $ref = $parent;
    }
    
    return $parents;
  }
  
  /**
   * Returns a list of all properties of the class instance.
   * 
   * @param object $class
   * @param int $filter
   * 
   * Filter is one or more of the following bit constants OR'd together: 
   * ReflectionProperty::IS_STATIC || ReflectionProperty::IS_PUBLIC || ReflectionProperty::IS_PROTECTED || ReflectionProperty::IS_PRIVATE
   */
  public static function properties_of($class, $filter = null)
  {
    return self::reflect($class)->getProperties($filter);
  }
  
  /**
   * Returns a list of all properties of the class instance as an associative array.
   * @param object $class
   * @param int $filter
   * @return array
   */
  public static function properties_array_of($class, $filter)
  { 
    
    $fields = array();
    $properties = self::properties_of($class, $filter);
  
    foreach($properties as $property)
    {
      $fields[$property->getName()] = $property->getValue($class);
    }
  
    return $fields;
  }
  
}

