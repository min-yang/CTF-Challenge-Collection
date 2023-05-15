<?php

namespace Helper;

defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );
/*
 * Name : Collection
 * Date : 20120107
 * Author : Qesy
 * QQ : 762264
 * Mail : 762264@qq.com
 *
 * (̅_̅_̅(̲̅(̅_̅_̅_̅_̅_̅_̅_̅()ڪے
 *
 */
class Memcached {
	private $_memcacheObj;
	private static $s_instance;
	public $memcahceTime = 3600;
	public static function get_instance() {
		if (! isset ( self::$s_instance )) {
			self::$s_instance = new self ();
		}
		return self::$s_instance;
	}
	public function connection() {
		$this->_memcacheObj = new Memcache ();
		$this->_memcacheObj->connect ( '192.168.16.63', 11211 ) or die ( "Could not connect" );
	}
	public function set($k, $v) {
		return $this->_memcacheObj->set ( $k, $v, 0, $this->memcahceTime );
	}
	public function get($k) {
		return $this->_memcacheObj->get ( $k );
	}
	public function del($k) {
		return $this->_memcacheObj->delete ( $k, 0 );
	}
}