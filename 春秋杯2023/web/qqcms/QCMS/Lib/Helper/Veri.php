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
class Veri {
	private static $s_instance;
	public static function get_instance() {
		if (! isset ( self::$s_instance )) {
			self::$s_instance = new self ();
		}
		return self::$s_instance;
	}
	
	public function VeriPara(array $Request , array $ParaArr){
	    foreach($ParaArr as $v){
	        if(empty($Request[$v])) return false;
	    }
	    return true;
	}
	
	public function email($email) {
		return filter_var ( $email, FILTER_VALIDATE_EMAIL );
	}
	public function inputMinLength($str, $lenght = 6) {
		if (strlen ( $str ) < $lenght) {
			return false;
		}
		return true;
	}
	public function inputMaxLength($str, $lenght = 6) {
		if (strlen ( $str ) > $lenght) {
			return false;
		}
		return true;
	}
	
	public function IsPassword($Str, $MinLen = 6, $MaxLen = 16) { //只能1-16为数字、字母、下划线
	    if (preg_match('/^[_0-9a-z]{'.$MinLen.','.$MaxLen.'}$/i',$Str)){
	        return true;
	    }else {
	        return false;
	    }
	}
	
	public function VeriMobile($Mobile){ //验证手机号码
	    if(strlen($Mobile) != 11 || is_numeric($Mobile) === false || substr($Mobile,0,1) != '1') return false;
	    return true;
	}
}