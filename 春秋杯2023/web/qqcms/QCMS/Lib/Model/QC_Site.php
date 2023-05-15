<?php
namespace Model;
use Helper\RedisKey;
use Helper\Redis;

defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );
/*
 * Name 	: QC_Site
 * Date 	: 2022-04-04
 * Author 	: Qesy
 * QQ 		: 762264
 * Mail 	: 762264@qq.com
 * Company	: Shanghai Rong Yi Technology Co., Ltd.
 * Web		: http://www.sj-web.com.cn/
 * (̅_̅_̅(̲̅(̅_̅_̅_̅_̅_̅_̅_̅()ڪے
 *
 */
class QC_Site extends \Db_pdo {
	public $TableName = 'site';
	public $PrimaryKey = 'SiteId';
	
	public function getList(){	    
	    $key = RedisKey::Site_String();
	    if(Redis::$s_IsOpen == 1 && Redis::exists($key)){
	        $Json = Redis::get($key);
	        return json_decode($Json, true);
	    }
	    $Arr = $this->SetSort(array('Sort' => 'ASC', 'SiteId' => 'ASC'))->ExecSelect();
	    if(Redis::$s_IsOpen == 1 && !empty($Arr)) Redis::set($key, json_encode($Arr));
	    return $Arr;
	}
	
	public function cleanList(){
	    if(Redis::$s_IsOpen != 1) return;
	    $key = RedisKey::Site_String();
	    Redis::del($key);
	}
}