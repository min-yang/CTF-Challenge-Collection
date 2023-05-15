<?php
defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );
class PageCate extends ControllersAdmin {
    
    public function index_Action(){
        $Arr = $this->Page_cateObj->getList();
        foreach($Arr as $k => $v){
            $Arr[$k]['SortView'] = '<input class="form-control SortInput" type="text" data-type="pageCate" data-index="'.$v['PageCateId'].'" value="'.$v['Sort'].'"/>';
        }
        $KeyArr = array(
            'PageCateId' => array('Name' => 'ID', 'Td' => 'th'),
            'Name' => array('Name' => '分类名', 'Td' => 'th'),
            'SortView' => array('Name' => '排序', 'Td' => 'th', 'Style' => 'width:100px;'),
        );
        $this->BuildObj->PrimaryKey = 'PageCateId';
        $this->BuildObj->TableTopBtnArr = array(
            array('Desc' => '返回', 'Link' => $this->CommonObj->Url(array('admin', 'page', 'index')), 'Class' => 'default'),
        );
        $tmp['Table'] = $this->BuildObj->Table($Arr, $KeyArr, '', 'table-sm');
        $this->LoadView('admin/common/list', $tmp);
    }
    
    public function add_Action(){
        if(!empty($_POST)){
            if(!$this->VeriObj->VeriPara($_POST, array('Name'))) $this->Err(1001);
            $Ret = $this->Page_cateObj->SetInsert(array(
                'Name' => $_POST['Name'],
                'Sort' => 99,
            ))->ExecInsert();
            if($Ret === false) $this->Err(1002);
            $this->Page_cateObj->cleanList();
            $this->Jump(array('admin', 'pageCate', 'index'), 1888);
        }
        $this->BuildObj->Arr = array(
            array('Name' =>'Name', 'Desc' => '分类名',  'Type' => 'input', 'Value' => '', 'Required' => 1, 'Col' => 12),
        );
        $this->BuildObj->Form('post', 'form-row');
        $this->LoadView('admin/common/edit');
    }
    
    public function edit_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('PageCateId'))) $this->Err(1001);
        $Rs = $this->Page_cateObj->SetCond(array('PageCateId' => $_GET['PageCateId']))->ExecSelectOne();
        if(empty($Rs)) $this->Err(1003);
        if(!empty($_POST)){
            if(!$this->VeriObj->VeriPara($_POST, array('Name'))) $this->Err(1001);
            $Ret = $this->Page_cateObj->SetCond(array('PageCateId' => $Rs['PageCateId']))->SetUpdate(array(
                'Name' => $_POST['Name'],
            ))->ExecUpdate();
            if($Ret === false) $this->Err(1002);
            $this->Page_cateObj->cleanList();
            $this->Jump(array('admin', 'pageCate', 'index'), 1888);
        }
        $this->BuildObj->Arr = array(
            array('Name' =>'Name', 'Desc' => '分类名',  'Type' => 'input', 'Value' => $Rs['Name'], 'Required' => 1, 'Col' => 12),
        );
        $this->BuildObj->Form('post', 'form-row');
        $this->LoadView('admin/common/edit');
    }
    
    public function del_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('PageCateId'))) $this->Err(1001);
        $Rs = $this->Page_cateObj->SetCond(array('PageCateId' => $_GET['PageCateId']))->ExecSelectOne();
        if(empty($Rs)) $this->Err(1003);
        $Count = $this->PageObj->SetCond(array('PageCateId' => $Rs['PageCateId']))->SetField('COUNT(*) AS c')->ExecSelectOne();
        if($Count['c'] != 0) $this->Err(1045);
        $Ret = $this->Page_cateObj->SetCond(array('PageCateId' => $Rs['PageCateId']))->ExecDelete();
        if($Ret === false) $this->Err(1002);
        $this->Page_cateObj->cleanList();
        $this->Jump(array('admin', 'pageCate', 'index'), 1888);
    }
    
}