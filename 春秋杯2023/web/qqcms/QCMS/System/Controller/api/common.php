<?php
defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );
class common extends ControllersApi {    
    public $PostData;
    function __construct(){
        parent::__construct();
        $file_in = file_get_contents("php://input");
        $this->PostData = json_decode($file_in, true);
    }
    
    public function sys_Action(){ //获取系统信息
        $Rs = $this->SysRs;
        $AllowField = array(
            'MultistationSecret', 'License');
        foreach($Rs as $k => $v){
            if(in_array($k, $AllowField)) unset($Rs[$k]);
        }
        $this->ApiSuccess($Rs);
    }
    
    public function cateList_Action(){ //分类列表
        $Arr = $this->CategoryObj->getList();
        $this->ApiSuccess($Arr);
    }
    
    public function cateOne_Action(){ //分类详情
        if(!$this->VeriObj->VeriPara($this->PostData, array('CateId'))) $this->ApiErr(1001);
        $Rs = $this->CategoryObj->SetCond(array('CateId' => $this->PostData['CateId']))->ExecSelectOne();
        if(empty($Rs)) $this->ApiErr(1003);
        $this->ApiSuccess($Rs);
    }
    
    public function pageOne_Action(){
        if(!$this->VeriObj->VeriPara($this->PostData, array('PageId'))) $this->ApiErr(1001);
        $Rs = $this->PageObj->getOne($this->PostData['PageId']);
        $this->ApiSuccess($Rs);
    }
    
    public function formOne_Action(){ // 表单
        if(!$this->VeriObj->VeriPara($this->PostData, array('KeyName'))) $this->ApiErr(1001);
        $Rs = $this->Sys_formObj->getOne($this->PostData['KeyName']);
        $Rs['FieldJson'] = json_decode($Rs['FieldJson'], true);
        $this->ApiSuccess($Rs);
    }
    
    public function formSubmit_Action(){
        if(!$this->VeriObj->VeriPara($this->PostData, array('KeyName'))) $this->ApiErr(1001);
        $Rs = $this->Sys_formObj->getOne(trim($this->PostData['KeyName']));
        if(empty($Rs) || $Rs['State'] != 1) $this->ApiErr(1003);
        $InsertArr = array('TsAdd' =>time(), 'State' => $Rs['StateDefault'], 'FormId' => $Rs['FormId']);
        if($Rs['IsLogin'] == 1){
            if(empty($this->PostData['Token'])) $this->ApiErr(1007);
            $TokenRs= $this->TokenObj->getOne(trim($this->PostData['Token']));
            if(empty($TokenRs)) $this->ApiErr(1007);
            $InsertArr['UserId'] = $TokenRs['UserId'];
        }
        $FieldArr = empty($Rs['FieldJson']) ? array() : json_decode($Rs['FieldJson'], true);
        foreach($FieldArr as $v){
            if($v['NotNull'] == 1 && empty($this->PostData[$v['Name']])) $this->ApiErr(1001);
            $InsertArr[$v['Name']] = $this->PostData[$v['Name']];
        }
        $Ret = $this->Sys_formObj->SetTbName('form_'.$Rs['KeyName'])->SetInsert($InsertArr)->ExecInsert();
        if($Ret === false) $this->ApiErr(1002);
        $this->ApiSuccess();
    }
    
    public function labelOne_Action(){
        if(!$this->VeriObj->VeriPara($this->PostData, array('KeyName'))) $this->ApiErr(1001);
        $Rs = $this->LabelObj->getOne($this->PostData['KeyName']);
        $this->ApiSuccess($Rs);
    }
    
    public function contentlist_Action(){
        if(!$this->VeriObj->VeriPara($this->PostData, array('Model'))) $this->ApiErr(1001);
        //$ModelRs = $this->Sys_modelObj->getOne($this->PostData['ModelId']);   
        if(!isset($this->ModelKv[$this->PostData['Model']])) $this->ApiErr(1052);
        $ModelRs = $this->ModelKv[$this->PostData['Model']];        
        $Page = intval($this->PostData['Page']);
        if($Page < 1) $Page = 1;
        $Count = 0;
        if(isset($this->PostData['Row'])) $this->PageNum = intval($this->PostData['Row']);
        $Limit = array(($Page-1)*$this->PageNum, $this->PageNum);
        $CondArr = array('IsDelete' => 2, 'State' => 1);        
        if(!empty($this->PostData['CateId'])){
            $this->CategoryObj->getAllCateId($this->PostData['CateId'], $ModelRs['ModelId']);
            $CondArr['CateId'] = $this->CategoryObj->AllSubCateIdArr;
        }
        if(!empty($this->PostData['State'])) $CondArr['State'] = $this->PostData['State'];
        if(!empty($this->PostData['Keyword'])) $CondArr['Title LIKE'] = $this->PostData['Keyword'];
        if(!empty($this->PostData['Ids'])) $CondArr['Id'] = explode('|', $this->PostData['Ids']);
        $Arr = $this->Sys_modelObj->SetTbName('table_'.$ModelRs['KeyName'])->SetCond($CondArr)->SetLimit($Limit)->SetSort(array('Sort' => 'ASC', 'Id' => 'Desc'))->ExecSelectAll($Count);
        $CateArr = $this->CategoryObj->SetCond(array('CateId' => array_column($Arr, 'CateId')))->SetField('CateId, Name')->ExecSelect();
        $CateKV = array_column($CateArr, 'Name', 'CateId');
        $UserArr = $this->UserObj->SetCond(array('UserId' => array_column($Arr, 'UserId')))->ExecSelect();
        $UserKv = array_column($UserArr, 'NickName', 'UserId');
        foreach($Arr as $k => $v){
            $Arr[$k]['CateName'] = $CateKV[$v['CateId']];
            $Arr[$k]['NickName'] = $UserKv[$v['UserId']];
        }
        
        $Data = array(
            'Page' => $Page,
            'List' => $Arr,
            'Count' => $Count,
        );
        $this->ApiSuccess($Data);
    }
    
    public function contentOne_Action(){
        if(!$this->VeriObj->VeriPara($this->PostData, array('Id'))) $this->ApiErr(1001);
        $TableRs = $this->TableObj->getOne($this->PostData['Id']);
        if(empty($TableRs)) $this->ApiErr(1003);
        $ModelRs = $this->Sys_modelObj->getOne($TableRs['ModelId']);
        if(empty($ModelRs)) $this->ApiErr(1001);
        $Rs = $this->TableObj->SetTbName('table_'.$ModelRs['KeyName'])->SetCond(array('Id' => $TableRs['Id']))->ExecSelectOne();
        if($ModelRs['KeyName'] == 'album'){
            $PhotoRs = $this->PhotosObj->SetCond(array('Id' => $TableRs['Id']))->ExecSelectOne();
            $Photos = empty($PhotoRs['Photos']) ? array() : json_decode($PhotoRs['Photos'], true);
            $Rs['Photo'] = $Photos;
        }
        $this->ApiSuccess($Rs);
    }
    
    
    
    public function link_Action(){
        $Page = intval($this->PostData['Page']);
        if($Page < 1) $Page = 1;
        $Count = 0;
        if(isset($this->PostData['Row'])) $this->PageNum = intval($this->PostData['Row']);
        $Limit = array(($Page-1)*$this->PageNum, $this->PageNum);
        $CondArr = array();
        if(!empty($this->PostData['LinkCateId'])) $CondArr['LinkCateId'] = $this->PostData['LinkCateId'];
        $Arr = $this->LinkObj->SetCond($CondArr)->SetLimit($Limit)->SetSort(array('Sort' => 'ASC', 'LinkId' => 'ASC'))->ExecSelectAll($Count);
        $Data = array(
            'Page' => $Page,
            'List' => $Arr,
            'Count' => $Count,
        );
        $this->ApiSuccess($Data);
    }
    
    public function swiper_Action(){
        if(!$this->VeriObj->VeriPara($this->PostData, array('SwiperCateId'))) $this->ApiErr(1001);        
        $CondArr = array('SwiperCateId' => $this->PostData['SwiperCateId']);
        $Arr = $this->SwiperObj->SetCond($CondArr)->SetSort(array('Sort' => 'ASC', 'SwiperId' => 'ASC'))->ExecSelect();
        $this->ApiSuccess($Arr);
    }
    
    
}