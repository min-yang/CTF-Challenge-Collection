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
class Session {
	private static $s_instance;
	public static function get_instance() {
		if (! isset ( self::$s_instance )) {
			self::$s_instance = new self ();
		}
		return self::$s_instance;
	}
	public function set($arr) {
		foreach ( $arr as $k => $v ) {
			$_SESSION [$k] = $v;
		}
	}
	public function get($k) {
		return $_SESSION [$k];
	}
	public function del($arr) {
		foreach ( $arr as $k => $v ) {
			unset ( $_SESSION [$k] );
		}
	}
}