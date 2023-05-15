<?php
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
if(version_compare ( PHP_VERSION, "5.6", "<" ) || version_compare ( PHP_VERSION, "8.0", ">=" )) die ( "Version error : PHP8.0 > QCMS > PHP5.6 !!!" );
require PATH_LIB . 'Config/Base' . EXTEND;
require PATH_LIB . 'Config/Controllers' . EXTEND;
require PATH_LIB . 'Config/Db' . EXTEND;
require PATH_LIB . 'Config/Db_pdo' . EXTEND;
require PATH_LIB . 'Config/Router' . EXTEND;
spl_autoload_register('autoload');
date_default_timezone_set ( 'Asia/Shanghai' );
header("Content-type: text/html; charset=utf-8");
header("Server: QCMS", true);
session_set_cookie_params ( 24 * 3600 );
session_start ();

Router::get_instance ();
?>