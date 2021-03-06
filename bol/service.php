<?php
/**
 * Copyright 2015 SongPhi
 * 
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy
 * of the License at
 * 
 *   http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

/**
* 
*/
class SPVIDEOLITE_BOL_Service
{
  const PLUGIN_NAME = 'spvideolite';
  const PLUGIN_VER = 'v2.0.3';
  protected static $classInstance = null;
  protected static $processors = null;

  public static function getInstance() {
    if ( null === self::$classInstance ) {
      self::$classInstance = new self();
    }
    return self::$classInstance;
  }

  /**
   * ============= UTILITIES FUNCTIONS =============
   */
  public static function getPlugin() {
    return OW::getPluginManager()->getPlugin(self::PLUGIN_NAME);
  }

  public static function getJsUrl($filename) {
    return self::getPlugin()->getStaticJsUrl() . $filename . '.js';
  }

  public static function getCssUrl($filename) {
    return self::getPlugin()->getStaticCssUrl() . $filename . '.css';
  }

  public static function getRoute() {
    $route = OW::getRequestHandler()->getHandlerAttributes();
    if (is_array($route)) {
      return $route;
    }
    return false;
  }

  public static function isPluginInstalled( $key ) {
    try {
      OW::getPluginManager()->getPlugin($key);
      return true;
    } catch (Exception $e) {  }
    return false;
  }

  /**
   *
   */
  public static function isRoute( $controller, $action = null ) {
    $route = self::getRoute();

    if ( !$route )
      return false;

    if ( $route["controller"] == $controller ) {
      if ( $route["action"] == $action || !$action ) {
        return true;
      }
    }

    return false;
  }

  /**
   * ============= PROCESSORS FUNCTIONS =============
   */

  public static function registerProcessor($name) {
    if (null == self::$processors) {
      self::$processors = array();
    }

    self::$processors[$name] = array(
      'className' => ('SPVIDEOLITE_PRO_'.$name),
      'instance' => null
    );

    // require_once SPVIDEOLITE_DIR_PROCESSORS . DS . $name . DS . 'routes.php';
  }

  public static function getProcessorInstance($name) {
    if (is_array(self::$processors[$name])) {
      $className = 'SPVIDEOLITE_PRO_'.$name;
      if (empty(self::$processors[$name]['instance'])) {
        self::$processors[$name]['instance'] = $className::getInstance($className);
      }
      $processorInstance = self::$processors[$name]['instance'];
      return $processorInstance;
    } else {
      throw new Exception("Error Processing Request", 1);
    }
  }

  public static function callProcessorFunction($name,$function,&$controller) {
    $processorInstance = self::getProcessorInstance($name);
    $processorInstance->setController($controller);
    return $processorInstance->$function();
  }

}
