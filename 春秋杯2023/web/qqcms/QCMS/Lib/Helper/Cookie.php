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
class Cookie {
	private static $s_instance;
	public $expire = 72; // -- hour --
	public static function get_instance() {
		if (! isset ( self::$s_instance )) {
			self::$s_instance = new self ();
		}
		return self::$s_instance;
	}
	public function set($arr, $pre = '', $hour = 0) {
		$time = empty ( $hour ) ? $this->expire * 60 * 60 : $hour * 60 * 60;
		foreach ( $arr as $k => $v ) {
			setcookie ( $pre.'_'.$k, $v, time () + $time, '/', '' );
		}
	}
	public function get($k, $pre = '') {
		return empty ( $_COOKIE [$pre.'_'.$k] ) ? '' : $_COOKIE [$pre.'_'.$k];
	}
	public function del($arr, $pre = '') {
		foreach ( $arr as $k => $v ) {
			setcookie ( $pre.'_'.$k, '', time () - ($this->expire * 60 * 60), '/', '' );
		}
	}
	
	public function delBatch($pre = ''){
	    foreach($_COOKIE as $k => $v){
	        if(strpos($k, $pre) !== false){
	            setcookie ( $k, '', time () - ($this->expire * 60 * 60), '/', $this->Domain );
	        }
	    }
	}
}