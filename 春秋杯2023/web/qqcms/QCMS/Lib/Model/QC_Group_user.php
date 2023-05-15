<?php
namespace Model;
use Helper\Redis;
use Helper\RedisKey;

defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );
/*
 * Name 	: QC_Group_user
 * Date 	: 2022-03-16
 * Author 	: Qesy
 * QQ 		: 762264
 * Mail 	: 762264@qq.com
 * Company	: Shanghai Rong Yi Technology Co., Ltd.
 * Web		: http://www.sj-web.com.cn/
 * (̅_̅_̅(̲̅(̅_̅_̅_̅_̅_̅_̅_̅()ڪے
 *
 */
class QC_Group_user extends \Db_pdo {
	public $TableName = 'group_user';
	public $PrimaryKey = 'GroupUserId';
	
	public function getList(){	    
	    $key = RedisKey::Group_User_Arr_HM();
	    $FieldArr = array();
	    if(Redis::$s_IsOpen == 1 && Redis::exists($key)) {
	        $FieldArr = Redis::hGetAll($key);
	    }else{
	        $Arr = $this->SetField('GroupUserId')->SetSort(array('GroupUserId' => 'ASC'))->ExecSelect();
	        $FieldArr = array_column($Arr, 'GroupUserId');
	        if(Redis::$s_IsOpen == 1 && !empty($Arr)) Redis::hMset($key, $FieldArr);
	    }
	    $DataArr = array();
	    foreach($FieldArr as $v){
	        $DataArr[$v] = $this->getOne($v);
	    }
	    return $DataArr;
	}
	
	public function cleanList(){
	    if(Redis::$s_IsOpen != 1) return;
	    $key = RedisKey::Group_User_Arr_HM();
	    $FieldArr = array();
	    if(Redis::exists($key)) {
	        $FieldArr = Redis::hGetAll($key);
	    }else{
	        $Arr = $this->SetField('GroupUserId')->ExecSelect();
	        $FieldArr = array_column($Arr, 'GroupUserId');
	        //if(!empty($Arr)) Redis::hMset($key, $FieldArr);
	    }
	    foreach($FieldArr as $v){
	        $this->clean($v);
	    }
	    Redis::del($key);
	}
	
}