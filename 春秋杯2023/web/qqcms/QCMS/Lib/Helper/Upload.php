<?php
namespace Helper;
defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );

class Upload {
    private static $s_instance;
    private $_type = array ('jpg', 'jpeg', 'gif', 'png', 'webp', 'bmp');
    private $_noType = array('php', 'inc');
    private $_size = 2048; // -- m --
    private $_dir;
    private $_name;
    public $Ret = array('Code' => 0, 'Msg' => '', 'Url' => '');
    function __construct() {        
        $this->_dir = 'Static/upload/' . date ( 'Ymd' ) . '/';
    }
    public static function get_instance() {
        if (! isset ( self::$s_instance )) {
            self::$s_instance = new self ();
        }
        return self::$s_instance;
    }
    
    public function  set($Type, $Size){
        $this->_type = $Type;
        $this->_size = $Size;
    }
    
    public function upload_file($fileRs) {
        $this->_name = uniqid ( rand ( 100, 999 ) ) . rand ( 1, 9 );
        $ext = substr ( strrchr ( $fileRs ['name'], '.' ), 1 );
        if(! in_array ( strtolower($ext), $this->_type )){
            $this->Ret['Code'] = 1001;
            $this->Ret['Msg'] = '不允许上传的文件类型';
            return $this->Ret;
        }
        if(in_array ( strtolower($ext), $this->_noType )){
            $this->Ret['Code'] = 1001;
            $this->Ret['Msg'] = '不允许上传的文件类型';
            return $this->Ret;
        }
        if ($fileRs ['size'] > ($this->_size * 1024)) {
            $this->Ret['Code'] = 1002;
            $this->Ret['Msg'] = '不能超过'.$this->_size.'K';
            return $this->Ret;
        }
        if (! is_uploaded_file ( $fileRs ['tmp_name'] )) {
            $this->Ret['Code'] = 1003;
            $this->Ret['Msg'] = '上传失败';
            return $this->Ret;
        }
        return self::_move_file ( $fileRs ['tmp_name'], $ext );
    }
    private function _move_file($file, $ext) {
        $url = $this->_dir . $this->_name . '.' . $ext;
        if (! is_dir ( $this->_dir )) {
            if(!mkdir ( $this->_dir, 0777 )){
                $this->Ret['Code'] = 1005;
                $this->Ret['Msg'] = '创建上传文件夹失败';
                return $this->Ret;
            }
        }
        if (! move_uploaded_file ( $file, $url )) {
            $this->Ret['Code'] = 1004;
            $this->Ret['Msg'] = '保存上传文件失败';
            return $this->Ret;
        }
        $this->Ret['Url'] = URL_ROOT . $url;
        return $this->Ret;
    }
}
?>