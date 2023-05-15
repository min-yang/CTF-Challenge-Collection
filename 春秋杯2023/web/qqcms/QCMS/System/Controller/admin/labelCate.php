<?php
defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );
class LabelCate extends ControllersAdmin {
    
    public function index_Action(){
        $Arr = $this->Label_cateObj->getList();
        foreach($Arr as $k => $v){
            $Arr[$k]['SortView'] = '<input class="form-control SortInput" type="text" data-type="labelCate" data-index="'.$v['LabelCateId'].'" value="'.$v['Sort'].'"/>';
        }
        $KeyArr = array(
            'LabelCateId' => array('Name' => 'ID', 'Td' => 'th'),
            'Name' => array('Name' => '分类名', 'Td' => 'th'),
            'SortView' => array('Name' => '排序', 'Td' => 'th', 'Style' => 'width:100px;'),
        );
        $this->BuildObj->PrimaryKey = 'LabelCateId';
        $this->BuildObj->TableTopBtnArr = array(
            array('Desc' => '返回', 'Link' => $this->CommonObj->Url(array('admin', 'label', 'index')), 'Class' => 'default'),
        );
        $tmp['Table'] = $this->BuildObj->Table($Arr, $KeyArr, '', 'table-sm');
        $this->LoadView('admin/common/list', $tmp);
    }
    
    public function add_Action(){
        if(!empty($_POST)){
            if(!$this->VeriObj->VeriPara($_POST, array('Name'))) $this->Err(1001);            
            $Ret = $this->Label_cateObj->SetInsert(array(
                'Name' => $_POST['Name'],
                'Sort' => 99,
            ))->ExecInsert();
            if($Ret === false) $this->Err(1002);
            $this->Label_cateObj->cleanList();
            $this->Jump(array('admin', 'labelCate', 'index'), 1888);
        }
        $this->BuildObj->Arr = array(
            array('Name' =>'Name', 'Desc' => '分类名',  'Type' => 'input', 'Value' => '', 'Required' => 1, 'Col' => 12),            
        );        
        $this->BuildObj->Form('post', 'form-row');
        $this->LoadView('admin/common/edit');
    }
    
    public function edit_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('LabelCateId'))) $this->Err(1001);
        $Rs = $this->Label_cateObj->SetCond(array('LabelCateId' => $_GET['LabelCateId']))->ExecSelectOne();
        if(empty($Rs)) $this->Err(1003);
        if(!empty($_POST)){
            if(!$this->VeriObj->VeriPara($_POST, array('Name'))) $this->Err(1001);
            $Ret = $this->Label_cateObj->SetCond(array('LabelCateId' => $Rs['LabelCateId']))->SetUpdate(array(
                'Name' => $_POST['Name'],
            ))->ExecUpdate();
            if($Ret === false) $this->Err(1002);
            $this->Label_cateObj->cleanList();
            $this->Jump(array('admin', 'labelCate', 'index'), 1888);
        }
        $this->BuildObj->Arr = array(
            array('Name' =>'Name', 'Desc' => '分类名',  'Type' => 'input', 'Value' => $Rs['Name'], 'Required' => 1, 'Col' => 12),
        );
        $this->BuildObj->Form('post', 'form-row');
        $this->LoadView('admin/common/edit');
    }
    
    public function del_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('LabelCateId'))) $this->Err(1001);
        $Rs = $this->Label_cateObj->SetCond(array('LabelCateId' => $_GET['LabelCateId']))->ExecSelectOne();
        if(empty($Rs)) $this->Err(1003);
        $Count = $this->LabelObj->SetCond(array('LabelCateId' => $Rs['LabelCateId']))->SetField('COUNT(*) AS c')->ExecSelectOne();
        if($Count['c'] != 0) $this->Err(1045);
        $Ret = $this->Label_cateObj->SetCond(array('LabelCateId' => $Rs['LabelCateId']))->ExecDelete();
        if($Ret === false) $this->Err(1002);
        $this->Label_cateObj->cleanList();
        $this->Jump(array('admin', 'labelCate', 'index'), 1888);
    }
    
}