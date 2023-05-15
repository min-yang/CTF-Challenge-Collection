<?php
defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );
class SysField extends ControllersAdmin {
    
    public function index_Action(){
        $Arr = $this->SysObj->getList();
        $FieldArr = array();
        foreach($Arr as $k => $v){
           if($v['IsSys'] == 1 || $v['GroupId'] != 10) continue; 
           $FieldArr[] = $v;
        }
        foreach($FieldArr as $k => $v){
            $FieldArr[$k]['AttrTypeView'] = $this->FieldArr[$v['AttrType']];
            $FieldArr[$k]['SortView'] = '<input class="form-control SortInput" type="text" data-type="sys" data-index="'.$v['Name'].'" value="'.$v['Sort'].'"/>';
            $FieldArr[$k]['callView'] = '<input class="form-control" disabled="disabled" type="text" value="{{qcms:'.$v['Name'].'}}"/>';
        }
        $KeyArr = array(
            'Info' => array('Name' => '变量说明', 'Td' => 'th'),
            'Name' => array('Name' => '网站名称', 'Td' => 'th'),
            'callView' => array('Name' => '调用标签名', 'Td' => 'th'),
            'AttrTypeView' => array('Name' => '类型', 'Td' => 'th'),
            'SortView' => array('Name' => '排序', 'Td' => 'th', 'Style' => 'width:100px;'),
            
        );
        $this->BuildObj->IsEdit = false;
        $this->BuildObj->PrimaryKey = 'Name';
        $tmp['Table'] = $this->BuildObj->Table($FieldArr, $KeyArr, '', 'table-sm');
        $this->LoadView('admin/common/list', $tmp);
    }
    
    public function add_Action(){
        if(!empty($_POST)){
            if(!$this->VeriObj->VeriPara($_POST, array('Info', 'Name', 'AttrType'))) $this->Err(1001);
            if(!$this->VeriObj->IsPassword($_POST['Name'], 2, 20)) $this->Err(1001);
            $SysKv = $this->SysObj->getKv();
            if(isset($SysKv[$_POST['Name']])) $this->Err(1004);
            $Ret = $this->SysObj->SetInsert(array(
                'Name' => $_POST['Name'], 
                'Info' => $_POST['Info'], 
                'AttrType' => $_POST['AttrType'],
                'AttrValue' => $_POST['AttrValue'],
                'GroupId' => 10,
                'Sort' => '99',
                'IsSys' => 2,
            ))->ExecInsert();
            if($Ret === false) $this->Err(1002);
            $this->SysObj->cleanList();
            $this->Jump(array('admin', 'sysField', 'index'));
        }
        $this->BuildObj->Arr = array(
            array('Name' =>'Info', 'Desc' => '变量说明',  'Type' => 'input', 'Value' => '', 'Required' => 1, 'Col' => 6),
            array('Name' =>'Name', 'Desc' => '变量字段名(只能英文和数字)',  'Type' => 'input', 'Value' => '', 'Required' => 1, 'Col' => 6),
            array('Name' =>'AttrType', 'Desc' => '变量类型',  'Type' => 'select', 'Data' => $this->FieldArr, 'Value' => 'input', 'Required' => 1, 'Col' => 6),
            array('Name' =>'AttrValue', 'Desc' => '默认值',  'Type' => 'input', 'Value' => '', 'Required' => 0, 'Col' => 6),            
        );
        
        $this->BuildObj->Form('post', 'form-row');
        $this->LoadView('admin/common/edit');
    }
    
    public function del_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('Name'))) $this->Err(1001);
        $FieldKv = $this->SysObj->getKv();
        if(!isset($FieldKv[$_GET['Name']])) $this->Err(1003);
        $Ret = $this->SysObj->SetCond(array('Name' => $_GET['Name'], 'IsSys' => 2))->ExecDelete();
        if($Ret === false) $this->Err(1002);
        $this->SysObj->cleanList();
        $this->Jump(array('admin', 'sysField', 'index'));
    }
    
}