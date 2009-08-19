<?php
/* SVN FILE: $Id$ */

/**
 * Redis DB storage engine for cache
 *
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework (http://www.cakephp.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @author Jared Armstrong
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * Redis storage engine for cache
 *
 * @package       cake
 * @subpackage    cake.cake.libs.cache
 */
class RedisEngine extends CacheEngine {
	
	var $__Redis = null;
	
/**
 * settings
 * 		PHP_AUTH_USER = xcache.admin.user, default cake
 * 		PHP_AUTH_PW = xcache.admin.password, default cake
 *
 * @var array
 * @access public
 */
	var $settings = array(
	
	function init($settings = array()) {
		App::import('Vendor','Redis');
		if (!class_exists('Redis')) {
			return false;
		}
		
		parent::init(array_merge(array(
			'engine'=> 'Redis', 'prefix' => Inflector::slug(APP_DIR) . '_', 'database'=>'default', 'servers' => array('127.0.0.1'), 'port'=>6379, 'compress'=> false
			), $settings)
		);
		
		return $this->connect($this->settings['server'],$this->settings['port']);
	}
	
	function __destruct(){
		if(isset($this->__Redis)){
			$this->__Redis->disconnect();
		}
	}
	
	function connect($server,$port){
		if($this->__Redis==null){
			try{
				$this->__Redis=new Redis($this->settings['server'],$this->settings['port']);
			}catch(Exception $e){
				return false;
			}
		}
		$this->__Redis->connect();
		return true;
	}
	
	function select_db($db){
		$this->__Redis->select_db($db);
	}
	
	function write($key, &$value, $duration=null) {
		
		$result=$this->__Redis->set($key, $value);
		if($duration != null){
			$this->__Redis->expire($key,$duration);
		}
		
		return true;
	}
	
	function read($key) {
		
		try{
			$result=$this->__Redis->get($key);
		}catch(Exception $e){
			return false;
		}
		
		return $result;
	}
	
	function delete($key){

		try{
			$this->__Redis->delete($key);
		}catch(Exception $e){
			return false;	
		}
		
		return true;
	}
	
}
?>