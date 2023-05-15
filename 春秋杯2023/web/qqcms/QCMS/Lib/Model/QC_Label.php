<?php
namespace Model;
use Helper\RedisKey;
use Helper\Redis;

defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );
/*
 * Name 	: QC_Label
 * Date 	: 2022-03-22
 * Author 	: Qesy
 * QQ 		: 762264
 * Mail 	: 762264@qq.com
 * Company	: Shanghai Rong Yi Technology Co., Ltd.
 * Web		: http://www.sj-web.com.cn/
 * (̅_̅_̅(̲̅(̅_̅_̅_̅_̅_̅_̅_̅()ڪے
 *
 */
class QC_Label extends \Db_pdo {
	public $TableName = 'label';
	public $PrimaryKey = 'LabelId';
	
	public function getOne($KeyName){	    
	    $key = RedisKey::Label_RS_HM($KeyName);
	    if(Redis::$s_IsOpen == 1 && Redis::exists($key)) return Redis::hGetAll($key);
	    $Rs = $this->SetCond(array('KeyName' => $KeyName))->ExecSelectOne();
	    if(Redis::$s_IsOpen == 1 && !empty($Rs)) Redis::hMset($key, $Rs);
	    return $Rs;
	}
	
	public function clean($KeyName){
	    if(Redis::$s_IsOpen != 1) return;
	    $key = RedisKey::Label_RS_HM($KeyName);
	    Redis::del($key);
	}
}