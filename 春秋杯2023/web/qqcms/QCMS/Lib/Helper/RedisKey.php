<?php

namespace Helper;

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
class RedisKey {
	public Static $s_projectKey = 'QCms';

	public static function Table_HM($Table, $PrimaryId){
	    return self::$s_projectKey.'_Table_'.$Table.'_HM_'.$PrimaryId;
	}

	public static function Sys_Arr_HM(){
	    return self::$s_projectKey.'_Sys_Arr_HM';
	}
	
	public static function Group_Admin_Arr_HM(){
	    return self::$s_projectKey.'_Group_Admin_Arr_HM';
	}
	
	public static function Group_User_Arr_HM(){
	    return self::$s_projectKey.'_Group_User_Arr_HM';
	}
	
	public static function Sys_Model_Arr_HM(){
	    return self::$s_projectKey.'Sys_Model_Arr_HM';
	}
	
	public static function Category_String(){
	    return self::$s_projectKey.'_Category_String';
	}
	
	public static function Link_Cate_String(){
	    return self::$s_projectKey.'_Link_Cate_String';
	}
	
	public static function Site_String(){
	    return self::$s_projectKey.'_Site_String';
	}
	
	public static function Inlink_Cate_String(){
	    return self::$s_projectKey.'_Inlink_Cate_String';
	}
	
	public static function Inlink_All_String(){ //所有内链
	    return self::$s_projectKey.'_Inlink_All_String';
	}
	
	public static function Page_Cate_String(){
	    return self::$s_projectKey.'_Page_Cate_String';
	}
	
	public static function Label_Cate_String(){
	    return self::$s_projectKey.'_Label_Cate_String';
	}
	
	public static function Label_RS_HM($KeyName){
	    return self::$s_projectKey.'_Label_RS_HM_'.$KeyName;
	}
	
	public static function Sys_Form_RS_HM($KeyName){
	    return self::$s_projectKey.'_Sys_Form_RS_HM_'.$KeyName;
	}
	
	public static function swiper_String($SwiperCateId){
	    return self::$s_projectKey.'_swiper_String_'.$SwiperCateId;
	}
	
	public static function Statistics_HM(){ // 后台首页简单统计
	    return self::$s_projectKey.'_Statistics_HM';
	}
	
	public static function Admin_Index_Chart_String(){
	    return self::$s_projectKey.'_Admin_Index_Chart_String';
	}

}