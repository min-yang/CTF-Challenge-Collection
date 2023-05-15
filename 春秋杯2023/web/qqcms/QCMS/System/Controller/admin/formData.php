<?php
defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );
class FormData extends ControllersAdmin {
    
    public function index_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('FormId'))) $this->Err(1001);
        $FormRs = $this->Sys_formObj->SetCond(array('FormId' => $_GET['FormId']))->ExecSelectOne();
        if(empty($FormRs)) $this->Err(1003);
        $Page = intval($_GET['Page']);
        if($Page < 1) $Page = 1;
        $Count = 0;
        $Limit = array(($Page-1)*$this->PageNum, $this->PageNum);
        $CondArr = array();
        $Arr = $this->Sys_formObj->SetTbName('form_'.$FormRs['KeyName'])->SetCond($CondArr)->SetLimit($Limit)->SetSort(array('FormListId' => 'DESC'))->ExecSelectAll($Count);
        $FieldArr = json_decode($FormRs['FieldJson'], true);
        $KeyArr = array('FormListId' => array('Name' => 'ID', 'Td' => 'th'));
        $KeyArr['NickName'] = array('Name' => '昵称', 'Td' => 'th');
        $UserArr = $this->UserObj->SetCond(array('UserId' => array_column($Arr, 'UserId')))->ExecSelect();
        $UserKv = array_column($UserArr, 'NickName', 'UserId');            

        foreach($Arr as $k => $v){
            $Arr[$k]['NickName'] = ($v['UserId'] != 0) ? $UserKv[$v['UserId']] : '匿名用户';
        }
            
        foreach($FieldArr as $k => $v){
            $KeyArr[$v['Name']] = array('Name' => $v['Comment'], 'Td' => 'th');
        }
        $KeyArr['State'] = array('Name' => '是否审核', 'Type' => 'Switch', 'Td' => 'th');
        $this->BuildObj->PrimaryKey = 'FormListId';

        $this->BuildObj->IsAdd = false;
        //$this->BuildObj->IsDel =  = $this->BuildObj->IsEdit = false;
        $PageBar = $this->CommonObj->PageBar($Count, $this->PageNum);
        $this->BuildObj->Js = 'var ChangeStateUrl="'.$this->CommonObj->Url(array('admin', 'api', 'formDataState', $FormRs['FormId'])).'";';
        $this->BuildObj->TableTopBtnArr = array(
            array('Desc' => '返回', 'Link' => $this->CommonObj->Url(array('admin', 'form', 'index')), 'Class' => 'default'),
        );
        $tmp['Table'] = $this->BuildObj->Table($Arr, $KeyArr, $PageBar);
        $this->LoadView('admin/common/list', $tmp);
    }
    
    public function edit_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('FormId', 'FormListId'))) $this->Err(1001);
        $FormRs = $this->Sys_formObj->SetCond(array('FormId' => $_GET['FormId']))->ExecSelectOne();
        if(empty($FormRs)) $this->Err(1003);
        $Rs = $this->Sys_formObj->SetTbName('form_'.$FormRs['KeyName'])->SetCond(array('FormListId' => $_GET['FormListId']))->ExecSelectOne();
        $FieldArr = json_decode($FormRs['FieldJson'], true);
        
        if(!empty($_POST)){
            $InsertArr = array(); 
            foreach($FieldArr as $v){
                if($v['NotNull'] == 1 && empty($_POST[$v['Name']])) $this->Err(1001);
                $InsertArr[$v['Name']] = $_POST[$v['Name']];
            }
            $Ret = $this->Sys_formObj->SetTbName('form_'.$FormRs['KeyName'])->SetCond(array('FormListId' => $Rs['FormListId']))->SetUpdate($InsertArr)->ExecUpdate();
            if($Ret === false) $this->Err(1002);
            $this->Jump(array('admin', 'formData', 'index'), 1888);
        }
        
        $this->BuildObj->Arr = array();
        foreach($FieldArr as $k => $v){
            $DataTmp = empty($v['Data']) ? array() : explode('|', $v['Data']);
            $Data = array();
            foreach($DataTmp as $dv) $Data[$dv] = $dv;
            $this->BuildObj->Arr[] = array('Name' =>$v['Name'], 'Desc' => $v['Comment'],  'Type' => $v['Type'], 'Data' => $Data, 'Value' => $Rs[$v['Name']], 'Required' => $v['NotNull'], 'Col' => 12);
        }        
        $this->BuildObj->Form('post', 'form-row');
        $this->LoadView('admin/common/edit');
    }
    
    public function del_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('FormId', 'FormListId'))) $this->Err(1001);
        $FormRs = $this->Sys_formObj->SetCond(array('FormId' => $_GET['FormId']))->ExecSelectOne();
        if(empty($FormRs)) $this->Err(1003);
        $Ret = $this->Sys_formObj->SetTbName('form_'.$FormRs['KeyName'])->SetCond(array('FormListId' => $_GET['FormListId']))->ExecDelete();
        if($Ret === false) $this->Err(1002);
        $this->Jump(array('admin', 'formData', 'index'), 1888);
        
    }
    
}