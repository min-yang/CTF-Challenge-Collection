<?php
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

error_reporting ( E_ALL ^ E_NOTICE );
define ( 'PATH_DIRNAME', pathinfo ( __FILE__, PATHINFO_DIRNAME ) );
define ( 'PATH_SYS', PATH_DIRNAME . '/System/' );
define ( 'PATH_TEMPLATE', PATH_DIRNAME . '/Template/' );
define ( 'PATH_LIB', PATH_DIRNAME . '/Lib/' );
define ( 'PATH_STATIC', PATH_DIRNAME . '/Static/' );
define ( 'EXTEND', '.php' );
require PATH_LIB . 'Config/Config' . EXTEND;
define ( 'URL_ROOT', substr($_SERVER['PHP_SELF'], 0, -9) );
define ( 'URL_CURRENT', substr(explode('?', $_SERVER ['REQUEST_URI'])[0], strlen(URL_ROOT))) ; 
define ( 'URL_STATIC', URL_ROOT . 'Static/' );
define ( 'URL_IMG', URL_ROOT . 'Static/images/' );
define ( 'URL_JS', URL_ROOT . 'Static/scripts/' );
define ( 'URL_CSS', URL_ROOT . 'Static/styles/' );
define ( 'URL_BOOT', URL_ROOT.'Static/bootstrap/');
define( 'URL_DOMAIN', $_SERVER['HTTP_HOST']);
require PATH_LIB . 'X' . EXTEND;  
?>