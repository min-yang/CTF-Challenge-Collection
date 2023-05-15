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

class Config {
    private static $s_ConfRs = array();
    
    public static function DbConfig($GetType = 'DbConfig'){ //接口配置
        if(!empty(self::$s_ConfRs)) return self::$s_ConfRs[$GetType];
        $ConfIniPath = PATH_LIB.'Config/Config.ini';    
        if(!file_exists($ConfIniPath)){
            Header("HTTP/1.1 303 See Other");
            Header("Location: /install/index.html");
            return;
        }
        self::$s_ConfRs = parse_ini_file($ConfIniPath, true);
        return self::$s_ConfRs[$GetType];
    }
    
}

function SiteConfig() {
    return array (
        'UrlType' => '1',
        'Extend' => '.html',
        'DefaultController' => 'index',
        'DefaultFunction' => 'index',
        'Language' => 'en',
        'Url' => '/'
    );
}

function BasicArr(){
    return array(
        'Client' => array('Web' => '网站', 'WcMini' => '微信小程序'),
    );
}

function autoload($classname) { // -- 自动加载类 --
    $filename = PATH_LIB . $classname . '.php';
    $filename = str_replace('\\', '/', $filename);
    if (file_exists ( $filename ))
        require $filename;
}

const WEB_MODE = 'Dev'; //Dev ,Release
const WEB_TITLE = 'QCMS';
const VERSION = '6.0.1';
?>
