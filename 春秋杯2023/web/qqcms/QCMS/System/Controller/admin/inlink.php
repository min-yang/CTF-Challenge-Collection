<?php
defined ( 'PATH_SYS' ) || exit ( 'No direct script access allowed' );
class Inlink extends ControllersAdmin {
    
    public function index_Action(){
        $Page = intval($_GET['Page']);
        if($Page < 1) $Page = 1;
        $Count = 0;
        $Limit = array(($Page-1)*$this->PageNum, $this->PageNum);
        $CondArr = array();
        if(!empty($_GET['InlinkCateId'])) $CondArr['InlinkCateId'] = $_GET['InlinkCateId'];
        $Arr = $this->InlinkObj->SetCond($CondArr)->SetLimit($Limit)->SetSort(array('Sort' => 'ASC', 'InlinkId' => 'ASC'))->ExecSelectAll($Count);
        
        $inlinkCateArr = $this->Inlink_cateObj->getList();
        $inlinkCateKV = array_column($inlinkCateArr, 'Name', 'InlinkCateId');
        foreach($Arr as $k => $v){
            $GET = $_GET;
            $GET['InlinkCateId'] = $v['InlinkCateId'];
            $Arr[$k]['InlinkCateName'] = '<a class="btn btn-sm btn-primary btn-outline" href="'.$this->CommonObj->Url(array('admin', 'inlink', 'index')).'?'.http_build_query($GET).'">'.$inlinkCateKV[$v['InlinkCateId']].'</a>';
            $Arr[$k]['SortView'] = '<input class="form-control SortInput" type="text" data-type="inlink" data-index="'.$v['InlinkId'].'" value="'.$v['Sort'].'"/>';
        }
        $KeyArr = array(
            'InlinkId' => array('Name' => 'ID', 'Td' => 'th'),
            'Name' => array('Name' => '关键字', 'Td' => 'th'),
            'Url' => array('Name' => '网站地址', 'Td' => 'th'),
            'InlinkCateName' => array('Name' => '分类', 'Td' => 'th'),
            'State' => array('Name' => '状态', 'Td' => 'th', 'Type' => 'Switch'),
            'SortView' => array('Name' => '排序', 'Td' => 'th', 'Style' => 'width:100px;'),            
        );
        $this->BuildObj->PrimaryKey = 'InlinkId';
        $this->BuildObj->TableTopBtnArr = array(
            array('Desc' => '分类管理', 'Class' => 'primary', 'Link' => $this->CommonObj->Url(array('admin', 'inlinkCate', 'index'))),
        );
        $this->BuildObj->NameAdd = '添加内链';
        //$this->BuildObj->IsDel = $this->BuildObj->IsAdd = $this->BuildObj->IsEdit = false;
        $PageBar = $this->CommonObj->PageBar($Count, $this->PageNum);
        
        $tmp['Table'] = $this->BuildObj->Table($Arr, $KeyArr, $PageBar, 'table-sm');
        $this->BuildObj->Arr = array(
            array('Name' =>'InlinkCateId', 'Desc' => '选择分类',  'Type' => 'select', 'Data' => $inlinkCateKV, 'Value' => $_GET['InlinkCateId'], 'Required' => 0, 'Col' => 12),
        );
        $this->BuildObj->Form('get', 'form-inline');
        $this->HeadHtml = $this->BuildObj->Html;
        $this->BuildObj->Js = 'var ChangeStateUrl="'.$this->CommonObj->Url(array('admin', 'api', 'inlinkState')).'";';
        $this->LoadView('admin/common/list', $tmp);
    }
    
    public function add_Action(){
        if(!empty($_POST)){
            if(!$this->VeriObj->VeriPara($_POST, array('Name', 'InlinkCateId', 'IsBlank', 'Url'))) $this->Err(1001);
            $Ret = $this->InlinkObj->SetInsert(array(
                'Name' => $_POST['Name'],
                'InlinkCateId' => $_POST['InlinkCateId'],
                'IsBlank' => $_POST['IsBlank'],
                'Url' => $_POST['Url'],                
                'State' => 1,
                'Sort' => 99,
            ))->ExecInsert();
            if($Ret === false) $this->Err(1002);
            $this->InlinkObj->cleanList();
            $this->Jump(array('admin', 'inlink', 'index'), 1888);
        }
        $CateArr = $this->Inlink_cateObj->getList();
        $CateKV = array_column($CateArr, 'Name', 'InlinkCateId');
        
        $this->BuildObj->Arr = array(
            array('Name' =>'Name', 'Desc' => '关键字',  'Type' => 'input', 'Value' => '', 'Required' => 1, 'Col' => 6),
            array('Name' =>'InlinkCateId', 'Desc' => '分类',  'Type' => 'select', 'Data' => $CateKV, 'Value' => $CateArr[0]['InlinkCateId'], 'Required' => 1, 'Col' => 3),
            array('Name' =>'IsBlank', 'Desc' => '新窗口',  'Type' => 'radio', 'Data' => $this->IsArr, 'Value' => '2', 'Required' => 0, 'Col' => 3),
            array('Name' =>'Url', 'Desc' => '网站地址',  'Type' => 'input', 'Value' => '', 'Required' => 1, 'Col' => 12),            
        );
        
        $this->BuildObj->Form('post', 'form-row');
        $this->LoadView('admin/common/edit');
    }
    
    public function edit_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('InlinkId'))) $this->Err(1001);
        $Rs = $this->InlinkObj->SetCond(array('InlinkId' => $_GET['InlinkId']))->ExecSelectOne();
        if(empty($Rs)) $this->Err(1003);
        if(!empty($_POST)){
            if(!$this->VeriObj->VeriPara($_POST, array('Name', 'InlinkCateId', 'IsBlank', 'Url'))) $this->Err(1001);
            $Ret = $this->InlinkObj->SetCond(array('InlinkId' => $Rs['InlinkId']))->SetUpdate(array(
                'Name' => $_POST['Name'],
                'InlinkCateId' => $_POST['InlinkCateId'],
                'IsBlank' => $_POST['IsBlank'],
                'Url' => $_POST['Url'],
            ))->ExecUpdate();
            if($Ret === false) $this->Err(1002);
            $this->InlinkObj->cleanList();
            $this->Jump(array('admin', 'inlink', 'index'), 1888);
        }
        $CateArr = $this->Inlink_cateObj->getList();
        $CateKV = array_column($CateArr, 'Name', 'InlinkCateId');        
        $this->BuildObj->Arr = array(
            array('Name' =>'Name', 'Desc' => '关键字',  'Type' => 'input', 'Value' => $Rs['Name'], 'Required' => 1, 'Col' => 6),
            array('Name' =>'InlinkCateId', 'Desc' => '分类',  'Type' => 'select', 'Data' => $CateKV, 'Value' => $Rs['InlinkCateId'], 'Required' => 1, 'Col' => 3),
            array('Name' =>'IsBlank', 'Desc' => '新窗口',  'Type' => 'radio', 'Data' => $this->IsArr, 'Value' => $Rs['IsBlank'], 'Required' => 0, 'Col' => 3),
            array('Name' =>'Url', 'Desc' => '网站地址',  'Type' => 'input', 'Value' => $Rs['Url'], 'Required' => 1, 'Col' => 12),
        );
        
        $this->BuildObj->Form('post', 'form-row');
        $this->LoadView('admin/common/edit');
    }
    
    public function del_Action(){
        if(!$this->VeriObj->VeriPara($_GET, array('InlinkId'))) $this->Err(1001);
        $Rs = $this->InlinkObj->SetCond(array('InlinkId' => $_GET['InlinkId']))->ExecSelectOne();
        if(empty($Rs)) $this->Err(1003);
        $Ret = $this->InlinkObj->SetCond(array('InlinkId' => $Rs['InlinkId']))->ExecDelete();
        if($Ret === false) $this->Err(1002); 
        $this->InlinkObj->cleanList();
        $this->Jump(array('admin', 'inlink', 'index'), 1888);
    }
    
}