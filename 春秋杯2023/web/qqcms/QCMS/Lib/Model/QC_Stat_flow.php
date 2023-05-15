<?php
namespace Model;
use Helper\Redis;
use Helper\RedisKey;

defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );
/*
 * Name 	: QC_Stat_flow
 * Date 	: 2022-04-01
 * Author 	: Qesy
 * QQ 		: 762264
 * Mail 	: 762264@qq.com
 * Company	: Shanghai Rong Yi Technology Co., Ltd.
 * Web		: http://www.sj-web.com.cn/
 * (̅_̅_̅(̲̅(̅_̅_̅_̅_̅_̅_̅_̅()ڪے
 *
 */
class QC_Stat_flow extends \Db_pdo {
	public $TableName = 'stat_flow';
	public $PrimaryKey = 'Date';
	
	public function GetIndexChart(){	    
	    $Ym = date('Ym');
	    $key = RedisKey::Admin_Index_Chart_String($Ym);
	    if(Redis::$s_IsOpen == 1 && Redis::exists($key)){	        
	        $Json = Redis::get($key);
	        return json_decode($Json, true);
	    }
	    $FlowDataKV = $this->SetCond(array('Date >' => date('Y-m-01'), 'Date <' => date('Y-m-t')))->SetIndex('Date')->ExecSelect();
	    if(Redis::$s_IsOpen == 1 && !empty($FlowDataKV)) {
	        Redis::set($key, json_encode($FlowDataKV));
	        Redis::expire($key, 86400); //默认缓存1天
	    }
	    return $FlowDataKV;
	}
}