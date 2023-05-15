<?php
namespace Model;
use Helper\RedisKey;
use Helper\Redis;

defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );
/*
 * Name 	: QC_Swiper
 * Date 	: 2022-03-26
 * Author 	: Qesy
 * QQ 		: 762264
 * Mail 	: 762264@qq.com
 * Company	: Shanghai Rong Yi Technology Co., Ltd.
 * Web		: http://www.sj-web.com.cn/
 * (̅_̅_̅(̲̅(̅_̅_̅_̅_̅_̅_̅_̅()ڪے
 *
 */
class QC_Swiper extends \Db_pdo {
	public $TableName = 'swiper';
	public $PrimaryKey = 'SwiperId';
	
	public function getOneByCateId($SwiperId, $SwiperCateId){
	    $Arr = self::getList($SwiperCateId);
	    foreach($Arr as $k => $v){
	        if($v['SwiperId'] == $SwiperId) return $v;
	    }
	    return array();
	}
	
	public function getList($SwiperCateId){	    
	    $key = RedisKey::swiper_String($SwiperCateId);
	    if(Redis::$s_IsOpen == 1 && Redis::exists($key)){
	        $Json = Redis::get($key);
	        return json_decode($Json, true);
	    }
	    $Arr = $this->SetCond(array('SwiperCateId' => $SwiperCateId))->SetSort(array('Sort' => 'ASC', 'SwiperId' => 'ASC'))->ExecSelect();
	    if(Redis::$s_IsOpen == 1 && !empty($Arr)) Redis::set($key, json_encode($Arr));
	    return $Arr;
	}
	
	public function cleanList($SwiperCateId){
	    if(Redis::$s_IsOpen != 1) return;
	    $key = RedisKey::swiper_String($SwiperCateId);
	    Redis::del($key);
	}
}