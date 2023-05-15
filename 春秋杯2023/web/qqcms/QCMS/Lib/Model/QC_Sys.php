<?php
namespace Model;
use Helper\RedisKey;
use Helper\Redis;

defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );
/*
 * Name 	: QC_Sys
 * Date 	: 2022-03-15
 * Author 	: Qesy
 * QQ 		: 762264
 * Mail 	: 762264@qq.com
 * Company	: Shanghai Rong Yi Technology Co., Ltd.
 * Web		: http://www.sj-web.com.cn/
 * (̅_̅_̅(̲̅(̅_̅_̅_̅_̅_̅_̅_̅()ڪے
 *
 */
class QC_Sys extends \Db_pdo {
	public $TableName = 'sys';
	public $PrimaryKey = 'Name';
	
	
	public function getKv(){
	    $DataArr = self::getList();
	    return array_column($DataArr, 'AttrValue', 'Name');
	}
	
	public function getGroupList($GroupId){
	    $DataArr = self::getList();
	    $Arr = array();
	    foreach($DataArr as $v){
	        if($v['GroupId'] != $GroupId) continue;
	        $Arr[] = $v;
	    }
	    return $Arr;
	}
	
	public function getList(){
	    $key = RedisKey::Sys_Arr_HM();
	    $FieldArr = array();
	    if(Redis::$s_IsOpen == 1 && Redis::exists($key)) {
	        $FieldArr = Redis::hGetAll($key);
	    }else{
	        $Arr = $this->SetField('Name')->SetSort(array('Sort' => 'ASC'))->ExecSelect();
	        $FieldArr = array_column($Arr, 'Name');
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
	    $key = RedisKey::Sys_Arr_HM();
	    $FieldArr = array();
	    if(Redis::exists($key)) {
	        $FieldArr = Redis::hGetAll($key);
	    }else{
	        $Arr = $this->SetField('Name')->ExecSelect();
	        $FieldArr = array_column($Arr, 'Name');
	        //if(!empty($Arr)) Redis::hMset($key, $FieldArr);
	    }
	    foreach($FieldArr as $v){
	        $this->clean($v);
	    }
	    Redis::del($key);
	}
	
	public function GetStat(){
	    $key = RedisKey::Statistics_HM();
	    if(Redis::$s_IsOpen == 1 && Redis::exists($key)) return Redis::hGetAll($key);
	    $UserCount = $this->SetTbName('user')->SetField('COUNT(*) AS c')->ExecSelectOne();
	    $TableCount = $this->SetTbName('table')->SetField('COUNT(*) AS c')->ExecSelectOne();
	    $FileSum = $this->SetTbName('file')->SetField('SUM(Size) AS s')->ExecSelectOne();
	    $CateCount = $this->SetTbName('category')->SetField('COUNT(*) AS c')->ExecSelectOne();
	    $Rs = array('UserCount' => $UserCount['c'], 'TableCount' => $TableCount['c'], 'FileSum' => $FileSum['s'], 'CateCount' => $CateCount['c']);
	    if(Redis::$s_IsOpen == 1){
	        Redis::hMset($key, $Rs);
	        Redis::expire($key, 86400);
	    }	    
	    return $Rs;
	}
}