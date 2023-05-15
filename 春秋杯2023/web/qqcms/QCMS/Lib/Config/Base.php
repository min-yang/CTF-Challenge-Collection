<?php
use Helper\Code;
use Helper\Build;
use Helper\Cookie;
use Helper\CurlQ;
use Helper\Veri;
use Helper\Common;
use Helper\Upload;
use Helper\IdCreate;

use Model\QC_Category;
use Model\QC_File;
use Model\QC_Group_admin;
use Model\QC_Group_user;
use Model\QC_Inlink;
use Model\QC_Inlink_cate;
use Model\QC_Label;
use Model\QC_Label_cate;
use Model\QC_Link;
use Model\QC_Link_cate;
use Model\QC_Log_login;
use Model\QC_Log_operate;
use Model\QC_Page;
use Model\QC_Page_cate;
use Model\QC_Photos;
use Model\QC_Site;
use Model\QC_Stat_flow;
use Model\QC_Swiper;
use Model\QC_Swiper_cate;
use Model\QC_Sys;
use Model\QC_Sys_form;
use Model\QC_Sys_model;
use Model\QC_Table;
use Model\QC_Table_album;
use Model\QC_Table_article;
use Model\QC_Table_down;
use Model\QC_Table_product;
use Model\QC_Tag;
use Model\QC_Tag_map;
use Model\QC_Token;
use Model\QC_User;
use Helper\Pinyin;
use Helper\WaterMask;
use Helper\Redis;

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
abstract class Base {
	public $CommonObj;	
	public $CookieObj; 
	public $CodeObj;
	public $BuildObj;
	public $CurlObj;
	public $VeriObj;
	public $UploadObj;
	public $PinYinObj;
	public $WaterMaskObj;
	
	public $CategoryObj;
	public $FileObj;
	public $Group_adminObj;
	public $Group_userObj;
	public $InlinkObj;
	public $Inlink_cateObj;
	public $LinkObj;
	public $Link_cateObj;
	public $LabelObj;
	public $Label_cateObj;
	public $Log_loginObj;
	public $Log_operateObj;
	public $PageObj;
	public $Page_cateObj;
	public $PhotosObj;
	public $SiteObj;
	public $Stat_flowObj;
	public $SwiperObj;
	public $Swiper_cateObj;
	public $SysObj;
	public $Sys_formObj;
	public $Sys_modelObj;
	public $TableObj;
	public $Table_albumObj;
	public $Table_articleObj;
	public $Table_downObj;
	public $Table_productObj;
	public $TagObj;
	public $Tag_mapObj;
	public $TokenObj;
	public $UserObj;
	
	public $PageNum = 20;
	public $TempArr = array();
	public $LanguageArr = array();
	public $BasicArr = array();
	
	public $SysRs;
	
	public $DefaultField = array(
	    'Index',
	    'Id',
	    'CateId',
	    'Title',
	    'STitle',
	    'Tag',
	    'Pic',
	    'Source',
	    'Author',
	    'Sort',
	    'Keywords',
	    'Description',
	    'TsAdd',
	    'TsUpdate',
	    'ReadNum',
	    'DownNum',
	    'Coins',
	    'Money',
	    'UserLevel',
	    'Color',
	    'UserId',
	    'Good',
	    'Bad',
	    'State',
	    'Content',
	    'IsLink',
	    'LinkUrl',
	    'IsBold',
	    'IsPic',
	    'IsSpuerRec',
	    'IsHeadlines',
	    'IsRec',
	    'IsPost',
	    'IsDelete',
	    'PinYin',
	    'PY',
	    'Summary',
	);
	function __construct() {
	    $this->CodeObj = Code::get_instance();
	    $this->BuildObj = Build::get_instance();
		$this->CookieObj = Cookie::get_instance();		
		$this->CurlObj = CurlQ::get_instance();
		$this->VeriObj = Veri::get_instance();
		$this->CommonObj = Common::get_instance();		
		$this->UploadObj = Upload::get_instance();
		$this->PinYinObj = Pinyin::get_instance();
		$this->WaterMaskObj = WaterMask::get_instance();
		
		$this->LanguageArr = require_once PATH_LIB .'Language/Cn/Error'.EXTEND;
		
		$this->CategoryObj = QC_Category::get_instance();
		$this->FileObj = QC_File::get_instance();
		$this->Group_adminObj = QC_Group_admin::get_instance();
		$this->Group_userObj = QC_Group_user::get_instance();
		$this->InlinkObj = QC_Inlink::get_instance();
		$this->Inlink_cateObj = QC_Inlink_cate::get_instance();
		$this->LinkObj = QC_Link::get_instance();
		$this->Link_cateObj = QC_Link_cate::get_instance();
		$this->LabelObj = QC_Label::get_instance();
		$this->Label_cateObj = QC_Label_cate::get_instance();
		$this->Log_loginObj = QC_Log_login::get_instance();
		$this->Log_operateObj = QC_Log_operate::get_instance();
		$this->PageObj = QC_Page::get_instance();
		$this->Page_cateObj = QC_Page_cate::get_instance();
		$this->PhotosObj = QC_Photos::get_instance();
		$this->SiteObj = QC_Site::get_instance();
		$this->Stat_flowObj = QC_Stat_flow::get_instance();
		$this->SwiperObj = QC_Swiper::get_instance();
		$this->Swiper_cateObj = QC_Swiper_cate::get_instance();
		$this->SysObj = QC_Sys::get_instance();
		$this->Sys_formObj = QC_Sys_form::get_instance();
		$this->Sys_modelObj = QC_Sys_model::get_instance();
		$this->TableObj = QC_Table::get_instance();
		$this->Table_albumObj = QC_Table_album::get_instance();
		$this->Table_articleObj = QC_Table_article::get_instance();
		$this->Table_downObj = QC_Table_down::get_instance();
		$this->Table_productObj = QC_Table_product::get_instance();
		$this->TagObj = QC_Tag::get_instance();
		$this->Tag_mapObj = QC_Tag_map::get_instance();
		$this->TokenObj = QC_Token::get_instance();
		$this->UserObj = QC_User::get_instance();
		
		$this->BasicArr = BasicArr();
		
		$RedisConf = Config::DbConfig('RedisConfig');
		if($RedisConf['IsOpen'] == 1) Redis::exists('test');
	}
	
	public function IdCreate(){ //创建ID	    
	    return IdCreate::createOnlyId();
	}
	
	public function ApiErr($ErrCode, $Desc = ''){
	    $Str = ($ErrCode == 1000) ? $Desc : $this->LanguageArr [$ErrCode];
	    $this->CommonObj->ApiErr($ErrCode, $Str);
	}
	
	public function ApiSuccess($Data = array()){
	    $this->CommonObj->ApiSuccess($Data);
	}
	
	public function Err($ErrCode, $Desc = ''){
	    $Str = ($ErrCode == 1000) ? $Desc : $this->LanguageArr [$ErrCode];
	    $this->CommonObj->Err($Str);
	}
	
	public function DieErr($ErrCode, $Desc = ''){
	    $Str = ($ErrCode == 1000) ? $Desc : $this->LanguageArr [$ErrCode];
	    die($Str);
	}
	
	public function Jump($UrlArr, $ErrCode = 1000, $Desc = ''){
	    $Str = ($ErrCode == 1000) ? $Desc : $this->LanguageArr [$ErrCode];
	    $this->CommonObj->Success($this->CommonObj->Url($UrlArr), $Str);
	}
	
	public function LoadView($Temp, $Data = array()) { // -- Name : 加载模版 --
	    if (! is_file ( PATH_SYS . 'View/' . $Temp . EXTEND )) die ( PATH_SYS . 'View/' . $Temp . EXTEND . ' not found !' );
	    $this->TempArr = empty ( $Data ) ? $this->TempArr : array_merge($this->TempArr, $Data);
	    foreach ( $this->TempArr as $Key => $Val ) $$Key = $Val;
	    require PATH_SYS . 'View/' . $Temp . EXTEND;
	}
	
	public static function InsertFuncArray(array $ControllerArr) { // -- Name : 回调函数 --
		$ParaArr = isset ( $ControllerArr ['ParaArr'] ) ? $ControllerArr ['ParaArr'] : array ();
		$Class = new $ControllerArr ['Name'] ();
		call_user_func_array ( array (& $Class, $ControllerArr ['Method'] . '_Action'), $ParaArr );
	}
}
?>